/* Copyright 2007 by Oliver Steele.  Available under the MIT License. */

/*
 * Parser
 */

Protodoc.Parser = function(options) {
    this.options = options;
}

Protodoc.Parser.prototype.parse = function(text) {
    var id = '[a-zA-Z_$][a-zA-Z_$0=9]*';
    var parser = new StateMachineParser({
        tokens: {
            id: id
        },
        states: {
            initial: [
                    /\/\/\/ ?(.*)/, docLine,
                    /\/\*\* *((?:.|\n)*?)\*\//, docBlock,
                    /function (#{id})\s*\((.*?)\).*/, defun,
                    /var\s+(#{id})\s*=.*/, defvar,
                        /(#{id}(?:\.#{id})*)\.(#{id})\s*=\s*function\s*\((.*?)\).*/, classMethod,
                        // a = a || function(...)
                        /(#{id}(?:\.#{id})*)\.(#{id})\s*=\s*\S+\s*||\s*function\s*\((.*?)\).*/, classMethod,
                        /\n\n/, section,
                        /.*\n/, null,
                        // solitary chars on the last line:
                        /./, null
            ],
            apidocBlock: [
                    / ?\* ?(.*?)\*\/\s*/, [docLine, 'initial'],
                    /(.*?)\*\/\s*/, [docLine, 'initial'],
                    / ?\* ?(.*)/, docLine,
                    /(.*)/, docLine
            ],
            blockComment: [
                    /\*\//, 'initial',
                    /\*/, null,
                    /[^\*]+/, null
            ]
        }});
    var globals = new GlobalContext,
        lastContainer = globals,
        docParser = new CommentParser;
    parser.parse(text);
    return globals;

    function getDocs() {
        var docs = docParser.blocks;
        docParser.reset();
        return docs;
    }
    function docBlock(s) {
        s = s.replace(/^  ?\*(?: |$)/gm, '');
        s.split('\n').each(docLine);
    }
    function docLine(s) {
        docParser.parseLine(s);
    }
    function section() {
        var docs = getDocs();
        if (docs.length)
            globals.addBlock(new SectionBlock(docs));
    }
    function defun(name, params) {
        globals.addDefinition(new FunctionDefinition(name, params, {docs: getDocs()}));
    }
    function defvar(name) {
        globals.addDefinition(new VariableDefinition(name, {docs: getDocs()}));
    }
    function classMethod(path, name, params) {
        var container = lastContainer = globals.findOrMake(path);
        container.addDefinition(new FunctionDefinition(name, params, {docs: getDocs()}));
    }
    function property(path, name) {
        var container = lastContainer = globals.findOrMake(path);
        container.addDefinition(new VariableDefinition(name, {docs: getDocs()}));
    }
}


/*
 * Comment Parser
 */

function CommentParser() {
    this.reset();
}

CommentParser.rules = (function() {
    var rules = [
            /^\s*(\^+)\s*(.*)/, heading,
            /^\s*::\s*(.*)/, CommentBlockTypes.signature,
            /^>>\s*(.*)/, CommentBlockTypes.output,
            /^==\s*(.*)/, CommentBlockTypes.equivalence,
            /^\s+(.*)/, CommentBlockTypes.formatted,
            /^\s*$/, endBlock,
            /(.*)/, paragraphLine
    ];
    return rules;
    function paragraphLine(line) {
        this.createOrAdd(CommentBlockTypes.paragraph).append(line);
    }
    function endBlock() {
        this.endBlock();
    }
    function heading(level, title) {
        this.create(CommentBlockTypes.heading, {level:level.length}).append(title);
    }
})();

CommentParser.prototype = {
    parseLine: function(line) {
        var rules = CommentParser.rules;
        for (var i = 0; i < rules.length; ) {
            var item = rules[i++],
                action = rules[i++],
                match = item.exec(line);
            if (match) {
                if (typeof action == 'function')
                    action.apply(this, match.slice(1));
                else {
                    this.createOrAdd(action).append(match[1]);
                }
                break;
            }
        }
        if (!match)
            throw "no match";
    },

    create: function(type, options) {
        var lines = [],
            block = this.block = OSUtils.merge({type:type, lines:lines, append:lines.push.bind(lines)}, options||{});
        this.blocks.push(block);
        return block;
    },

    createOrAdd: function(type) {
        var block = this.block;
        if ((block||{}).type != type) {
            var lines = [];
            this.block = block = {type:type, lines:lines, append:lines.push.bind(lines)}
            this.blocks.push(block);
        }
        return block;
    },

    endBlock: function() {
        this.block = null;
    },

    reset: function() {
        this.blocks = [];
        this.block = null;
    }
}


/*
 * StateMachineParser
 */

// stateTable :: {String => [Rule]} where
//   [Rule] is an alternating list of (Regex|String, RHS)*
//   RHS is a Function (representing an action) or a String (the name of a state)
function StateMachineParser(options) {
    var tokens = options.tokens;
    var stateTables = options.states;
    this.tables = {};
    for (var key in stateTables) {
        var value = stateTables[key];
        if (typeof value != 'function')
            this.tables[key] = StateMachineParser.makeStateTable(value, tokens);
    }
}

StateMachineParser.prototype.parseLines = function(lines) {
    var state = 'initial';
    lines.each(function(line) {
    });
}

StateMachineParser.prototype.parse = function(string) {
    var start = new Date().getTime();
    var state = 'initial',
        pos = 0;
    while (pos < string.length) {
        //info('state', state, 'pos', string.slice(pos, pos+40));
        var table = this.tables[state];
        if (!table)
            throw "unknown state: " + state;
        var r = table(string, pos);
        state = r.state || state;
        if (pos == r.pos)
            throw "failure to advance";
        pos = r.pos;
    }
    //console.info('time', new Date().getTime()-start);
}

StateMachineParser.makeStateTable = function(ruleList, tokens) {
    var trace = {tries:false, matches:false, actions:false},
        debug = {doublecheck:false},
        testPrefix = true;
    var rules = [];
    if (ruleList.length & 1)
        throw "makeStateTable requires an even number of arguments";
    for (var i = 0; i < ruleList.length; ) {
        var pattern = ruleList[i++],
            rhs = ruleList[i++],
            src = pattern;
        if (src instanceof RegExp) {
            src = String(pattern);//.toSource();
            src = src.slice(1, src.lastIndexOf('/'));
        }
        src = src.replace(/#{(.+?)}/g, function(s, m) {return tokens[m] || s});
        var re = new RegExp('^'+src, 'g'),
            prefixMatch = /^([^\(\[\\\.\*])|^\\([^swdbnt])/.exec(src);
        if (testPrefix && prefixMatch) {
            var prefixChar = prefixMatch[1] || prefixMatch[2];
            re = (function(re, src, prefixChar) {
                return function(string) {
                    var ix = re.lastIndex = this.lastIndex,
                        match = (string.length > ix && string.charAt(ix) == prefixChar
                                 && re.exec(string));
                    if (debug.doublecheck && !match && re(string)) {
                        var msg = "RE didn't match but string did";
                        console.error(msg, src, re, string);
                        throw msg;
                    }
                    if (match)
                        this.lastIndex = re.lastIndex;
                    return match;
                }
            })(re, src, prefixChar);
            re = {exec:re};
        }
        rules.push({
            source: src,
            re: re
        });
        process(rules[rules.length-1], rhs);
    }
    // String -> {state, position}
    return function(string, pos) {
        var base = 0;
        if (true) {
            var base = pos;
            string = string.slice(pos);
            pos = 0;
        }
        for (var i = 0, re, m; rule = rules[i]; i++) {
            var re = rule.re;
            trace.tries && console.info('trying', rule.source, 'at', pos, 'on', string.slice(pos, pos+40));
            re.lastIndex = pos;
            if ((m = re.exec(string)) && m[0].length) {
                if (!(re.lastIndex-m[0].length == pos)) {
                    //info('!=', re.lastIndex, m[0].length, pos);
                    continue;
                }
                trace.matches && console.info('match', rule);
                trace.actions && rule.action && console.info(rule.action, m);
                rule.action && rule.action.apply(m[0], m.slice(1));
                return {pos: base+re.lastIndex, state: rule.target};
            }
            //info('failed', re.toSource(), string.slice(0, 80).toSource(), m);
        }
        // throw the variables into a global, so that we can debug against them
        gTrace = [rules, string, pos, string.slice(pos, pos+80)];
        throw "no match at " + string.slice(pos, pos+80).debugInspect();
    }
    function process(rule, rhs) {
        switch (typeof rhs) {
        case 'function':
            if (rule.action) throw "duplicate targets";
            rule.action = rhs;
            break;
        case 'string':
            if (rule.target) throw "duplicate targets";
            rule.target = rhs;
            break;
        default:
            rhs && rhs.each(process.bind(this, rule));
        }
    }
}

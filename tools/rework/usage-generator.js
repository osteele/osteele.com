/* Copyright 2006 Oliver Steele.  All rights reserved. */

function UsageGenerator(name, fn) {
    UsageGenerator.generators[name] = this;
    this.getUsageTable = fn;
}

UsageGenerator.generators = {};

UsageGenerator.getGenerator = function(name) {
    return UsageGenerator.generators[name];
};

UsageGenerator.addGenerator = function(name, fn) {
    new UsageGenerator(name, fn);
};

UsageGenerator.prototype.getUsageText = function(re, input, replacement) {
	var p = re.source;
	p = p.replace(/\\/, '\\\\');
	p = p.replace('\'', '\\\'');
	p = p.replace('/', '\/');
	
    var rubyPattern = p;
    if (!re.multiline) {
        rubyPattern = rubyPattern.replace(/^\^/, '\\A');
        rubyPattern = rubyPattern.replace(/\$$/, '\\Z');
    }
	p = {flags: re,
		 python: 'r\'' + p + '\'',
		 ruby: '/' + rubyPattern + '/',
		 php: '/' + p + '/',
		 js: re.toString()};
    if (p.js == '//')
        p.js = '(?:)';
	if (re.ignoreCase) {
		p.php += 'i';
		p.ruby += 'i';
	}
	if (re.multiline) {
		p.php += 'm';
	}
	p.php = '\'' + p.php + '\'';
    
    var einput = input.escapeJavascript();
    if (einput.length > 40)
        einput = 'input';

    var flags = re;
    var pythonFlags = [];
    if (flags.ignoreCase)
        pythonFlags.push('re.I');
    if (flags.multiline)
        pythonFlags.push('re.M');
    if (flags.dotall)
        pythonFlags.push('re.S');
    var pythonFlagString = '';
    if (pythonFlags.length)
        pythonFlagString = ', ' + pythonFlags.join(' | ');
    
    var rubyNote = null;
    if (!re.multiline) {
        var scratch = rubyPattern;
        scratch = scratch.replace(/\\./g, '');
        if (rubyPattern.match(/[\^\$]/))
            rubyNote = 'For Ruby in non-multiline mode, replace <tt>^</tt> by <tt>\\A</tt> and <tt>$</tt> by <tt>\\Z</tt>.  I tried to do that, but this pattern was too complicated.';
    }
    
	var table = this.getUsageTable(p, einput, replacement);
	var html = '<div><strong>Usage:</strong></div><table>';
    var lastname = null;
	for (var i = 0; i < table.length; ) {
		var name = table[i++];
		var syntax = table[i++].escapeHTML();
        syntax = replaceCallback(
            syntax, /\b(input|re(?!\.))\b|,\s*options/g,
            function (s) {
                if (s == 'input')
                    return escapeTag(einput, 'span');
                if (s == ', options')
                    return pythonFlagString;
                var selector = name.toLowerCase();
                if (selector == 'javascript') selector = 'js';
                if (!p[selector])
                    error('p['+js+'] == null');
                return escapeTag(p[selector], 'span');
            });
        var label = name == lastname ? '' : name;
        lastname = name;
		html += '<tr><td>'+label+'</td><td><tt>'+syntax+'</tt></td>';
        if (name == 'Ruby' && rubyNote) {
            html += '<td rowspan="3"><span class="info">'+rubyNote+'</class></td>';
            rubyNote = null;
        }
        html += '</tr>';
	}
    html += '</table>';
	return html;
};

UsageGenerator.addGenerator(
    'search', function(re, input) {
        var rubyfn = re.flags.global ? 'scan' : 'match';
        var pythonfn = 'search', pythonre = 're';
        var phpfn = 'preg_match';
        if (re.flags.global) {
            pythonfn = 'findall';
            phpfn += '_all';
        } else if (!re.flags.multiline && re.python.match(/^r['']\^/)) {
            pythonfn = 'match';
            pythonre = re.python.replace(/^r['']\^/, 'r\'');
        }
        
        var table = [
            'JavaScript', 'input.match(re)',
            'JavaScript', 're.exec(input)',
            'PHP', phpfn+'(re, input, $match)',
            'Python', 're.'+pythonfn+'('+pythonre+', input, options)',
            'Ruby', 'input.'+rubyfn+'(re)'
            ];
        if (!re.flags.global)
            table = table.concat(['Ruby', 'input[re]',
                                  'Ruby', 'input =~ re']);
        return table;
    });

UsageGenerator.addGenerator(
    'replace', function(re, input, repl) {
        var repl = repl.escapeJavascript();
        
        var limit = '';
        if (!re.flags.global)
            limit = ', 1';
        
        var rubyfn = re.flags.global ? 'gsub' : 'sub';
        var pyexpr = 're.sub(re, '+repl+', input'+limit+')';
        if (re.flags.ignoreCase || re.flags.multiline)
            pyexpr = 're.compile(re, options).sub('+repl+', input'+limit+')';
        return [
            'JavaScript', 'input.replace(re, ' + repl + ')',
            'PHP', 'preg_replace(re, '+repl+', input' + limit + ')',
            'Python', pyexpr,
            'Ruby', 'input.'+rubyfn+'(re, ' + repl + ')'
            ];
    });

UsageGenerator.addGenerator(
    'scan', function(re) {
        var re2 = re.js;
        if (!re2.match('/[^/]*g[^/]*$'))
            re2 += 'g';
        return [
            'JavaScript', 'input.match(' + re2 + ')',
            'PHP', 'preg_matchall(re, input, $match)',
            'Python', 're.findall(re, input, options)',
            'Ruby', 'input.scan(re)'
            ];
    });

UsageGenerator.addGenerator(
    'split', function(re) {
        var pyexpr = 're.split(re, input)';
        if (re.flags.multiline || re.flags.ignoreCase)
            pyexpr = 're.compile(re, options).split(input)';
        return [
            'JavaScript', 'input.split(' + re.js + ')',
            'PHP', 'preg_split(re, input, &match)',
            'Python', pyexpr,
            'Ruby', 'input.split(re)'
            ];
    });

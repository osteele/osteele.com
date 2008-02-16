/* Copyright 2007 by Oliver Steele.  This work is licensed under a
 * Creative Commons Attribution-Noncommercial-Share Alike 3.0 License.
 * http://creativecommons.org/licenses/by-nc-sa/3.0/
 */

var info = window.console && console.info || function(){};

var gApplet;

var gOptions = {
    run: false,
    interval: 50,
    // model options
    rows: 20,
    columns: 20,
    // drawing options
    sphere: false,
    grid: true,
    dual: false,
    cellWidth: 25,
    cellHeight: 25,
    rounding: 3.25,
    rotation: 0.1,
    cellColors: ['255,0,0', '0,255,0', '0,0,255'],
    edgeColor: '#222',
    colorStrategyIndex: 0
};

var gColorSets = {
    'primary': ['255,0,0', '0,255,0', '0,0,255'],
    'secondary': ['255,255,0', '0,255,255', '255,0,255'],
    'all': ['255,0,0', '0,255,0', '0,0,255',
            '255,255,0', '0,255,255', '255,0,255'],
    'bw': ['255,255,255', '0,0,0']
}


/*
 * Applet
 */

function TilesApplet(elt, options) {
    this.options = options;
    var model = this.model = new TileModel(options.rows, options.columns);
    var viewer = this.viewer = new TileViewer(elt, model, options);
    var labelView = this.labelView = new LabelView(elt);

    var generator = Function.pipe(
        gExpressions,
        Generator.shuffle,
        Generator.cycle,
        Generator.removeNeighboringDuplicates.curry(3),
        Generator.map.curry(
            function(expr){return new CellFunction(expr)}),
        Generator.tap.curry(this.showFunctionName.bind(this)),
        Generator.concatMap.curry(
            function(cfn) {
                return this.generator = new MoveGenerator(model, cfn)
            }.bind(this))
    );
    viewer.generator = generator;

    viewer.draw();
}

TilesApplet.prototype.showFunctionName = function(cfn, offset) {
    var labelView = this.labelView;
    labelView.setHTML(cfn.toHTML());
    labelView.moveToRatio(offset || 0);
    this.state = {cfn: cfn};
}

TilesApplet.prototype.setStaticModel = function(model) {
    var viewer = this.viewer;
    this.savedState = this.savedState || viewer.getState();
    viewer.setState({model: model});
    viewer.redraw();
}

TilesApplet.prototype.setStaticFunction = function(cfn) {
    this.setStaticModel(this.model.clone().fill(cfn));
    this.showFunctionName(cfn, 1);
    this.stop();
}

TilesApplet.prototype.step = function() {
    this.run();
    this.nextFrame();
    this.stop();
}

TilesApplet.prototype.run = function() {
    this.running = true;
    if (this.savedState) {
        this.viewer.setState(this.savedState)
        this.viewer.redraw();
        this.labelView.setHTML(this.state.cfn.toHTML());
        this.labelView.moveToRatio(1 - this.generator.getRatio());
        this.savedState = null;
    }
    if (!this.thread)
        this.thread = window.setInterval(this.nextFrame.bind(this), this.options.interval);
}

TilesApplet.prototype.stop = function() {
    this.running = false;
    if (this.thread) {
        window.clearInterval(this.thread);
        this.thread = null;
    }
}

TilesApplet.prototype.nextFrame = function() {
    this.generator && this.labelView.slideToRatio(1 - this.generator.getRatio());
    this.viewer.nextFrame();
}

TilesApplet.prototype.redraw = function() {
    this.viewer.redraw();
}

function enterExpression() {
    var applet = gApplet;
    var expr = prompt('Enter a JavaScript expression in i and j:',
        window.lastExpression || '');
    if (expr) {
        window.lastExpression = expr;
        var cfn = new CellFunction(expr);
        applet.setStaticFunction(cfn);
    }
}


/*
 * Initialization
 */

function initialize() {
    var options = gOptions;
    var applet = gApplet = new TilesApplet($('applet'), options);
    options.run && applet.run();
    Commands.observeControls();
    Event.observe(window, 'keyup', Commands.handleKey);
}

Event.observe(window, 'load', initialize);


/*
 * Commands
 */

var Commands = {
    toggleRunning: function() {
        this.running ? this.stop() : this.run();
    },
    step: function() {this.step()},
    nextExpression: function() {this.generator.reset()},
    eval: enterExpression,
    nextColorStrategy: function() {
        this.viewer.setColorStrategyIndex(gOptions.colorStrategyIndex + 1);
        this.redraw();
    },
    previousColorStrategy: function() {
        this.viewer.setColorStrategyIndex(gOptions.colorStrategyIndex - 1);
        this.redraw();
    },
    nextColorPalette: function() {
        var nextColorSet = function(colorSet) {
            var colorSetList = $H(gColorSets).values();
            var ix = colorSetList.indexOf(
                colorSetList.find(function(item) {
                    return item.equals1(colorSet);
                }));
            return colorSetList[(ix + 1) % colorSetList.length];
        }
        gOptions.cellColors = nextColorSet(gOptions.cellColors);
        this.redraw();
    },
    changeProjection: function() {
        this.options.sphere = !this.options.sphere;
        this.redraw();
    },
};

Commands.observeControls = function() {
    $$('#controls a').each(function(item) {
        var href = item.attributes['href'].value.slice(1),
            name = href.replace(/-([a-z])/gi, function(_, letter) {
                return letter.toUpperCase();
            });
        var fn = Commands[name];
        Event.observe(item, 'click', function(e) {
            Event.stop(e);
            fn.apply(gApplet);
        });
    });
}

Commands.handleKey = function(e) {
    var applet = gApplet;
    var commandKeyCodes = {
        32: 'toggleRunning',
        83: 'step',                 // s
        67: 'nextColorPalette',     // c
        37: 'nextColorStrategy',    // left arrow
        39: 'previousColorStrategy',    // right arrow
        69: 'eval',                 // e
        40: 'nextExpression',       // down arrow
        78: 'nextExpression',       // n
        80: 'changeProjection'      // p
    };
    var commandName = commandKeyCodes[e.keyCode];
    if (commandName)
        Commands[commandName].apply(applet);
    else
        window.status = 'keyCode: ' + e.keyCode;
}


/**
 * ^Utilities
 */

/// Returns an integer in the range `[0, n)`.
/// :: Integer -> Integer
function random(n) {
    return Math.floor(n * Math.random());
}

/// Returns an object whose `each` method iterates over the range `[start, end)`
function range(start, end) {
    if (arguments.length < 2) {
        end = start;
        start = 0;
    }
    return {
        start: start,
        end: end,
        // valid for integers only:
        length: end - start,
        each: function(fn) {
            for (var i = start; i < end; i++)
                fn(i);
        }
    }
}

/// :: [x] -> x
Array.prototype.choose = function() {
    return this[random(this.length)];
}

// -- only goes one deep
/// :: Eq x -> self = [x] -> [x] -> Bool
Array.prototype.equals1 = function(other) {
    return this.length == other.length &&
        this.every(function(item, ix) {
            return item == other[ix];
        });
}

/// Modifies this array, sorting it by the key returned by `fn`.
/// :: this:[a] (a -> Number) -> this:[a]
Array.prototype.sortUnder = function(fn) {
    var sorted = (this
                  .map(function(item, ix) {return [fn(item, ix), item]})
                  .sort(function(a, b) {return a[0] - b[0]})
                  .pluck(1));
    this.splice.apply(this, [0, this.length].concat(sorted));
}

/// :: [x] -> [x]
Array.prototype.shuffle = function() {
    // this works because |Co(Math.random)| >> |Dom(this)|
    return (this
            .map(function(item) {return [Math.random(), item]})
            .sort(function(a,b) {return a[0]-b[0]})
            .pluck(1));
}

/// Returns a `|dims|`-dimensional array
Array.zeros = function(dims) {
    var init = 0;
    switch (dims.length) {
    case 0:
        return init;
    default:
        var ar = new Array(dims[0]);
        var rdims = dims.slice(1);
        var fn = (rdims.length
                  ? function() {return Array.zeros(rdims)}
                  // optimization:
                  : function() {return init});
        ar.each(function(_, ix) {
            ar[ix] = fn();
        });
        return ar;
    }
}

// adapted from http://www.coryhudson.com/blog/2007/03/10/javascript-currying-redux/
Function.prototype.curry = function(/*args...*/) {
    var fn = this;
    var args = [].slice.call(arguments, 0);
    return function() {
        return fn.apply(this, args.concat([].slice.call(arguments, 0)));
    };
}

// pipe $ fn1 $ fn2 | ...
Function.pipe = function(value /*, functions... */) {
    for (var i = 1; i < arguments.length; i++)
        value = arguments[i](value);
    return value;
}


/*
 * Cell Functions and Expressions
 */

// A function that can be applied to a cell position within a tile
// model to produce a state.
function CellFunction(source) {
    var expr = this.expr = new Expression(source);
    this.fn = expr.toFunction(CellFunction.params);
}

CellFunction.params = 'i, j, r, a';

CellFunction.prototype.applyTo = function(model, i, j) {
    var fn = this.fn,
        states = TileModel.STATES,
        di = i - model.rows / 2,
        dj = j - model.columns / 2,
        r = Math.sqrt(di*di+dj*dj),
        a = Math.atan2(dj, di),
        n = Math.floor(fn(i, j, r, a));
    return (n % states + states) % states;
}

CellFunction.prototype.toHTML = function() {
    var lhs = '<b>S</b><sub><var>i</var>, <var>j</var></sub>';
    var html = lhs + ' = ' + this.expr.toHTML();
    return html;
}

function Expression(source) {
    this.source = String(source);
}

Expression.prototype.toHTML = function() {
    var s = (this.source
             .replace(/([0-9])\*([a-z])/, '$1$2')
             .replace(/Math\./g, '')
             .replace(/\b([ij])\b/g, '<var>$1</var>')
             .replace(/pow\((.*?),\s*(.*?)\)/g, '$1<sup> $2</sup>'));
    ops = [[/\&/g, 'and'],
           [/\|/g, 'or'],
           [/\*/g, 'sdot'],
           [/\^/g, 'otimes'],
           ];
    ops.each(function(item) {
        s = s.replace(item[0], '<span class="op">&' + item[1] + ';</span>');
    });
    return s;
}

Expression.prototype.toJS = function(params) {
    expr = (this.source
            .replace(/(sin|cos|tan|atan|sqrt|pow|exp|log)/g, 'Math.$1')
            .replace(/([0-9])([a-z])/, '$1*$2')
           )
    return expr;
}

Expression.prototype.toFunction = function(params) {
    var stmt = this.toJS();
    if (!stmt.match(/return/))
        stmt = 'return (' + stmt + ')';
    return new Function(params, stmt);
}


/*
 * Generators
 */

var Generator = {}

Generator.concatMap = function(fn, g) {
    var gi = null;
    return {
        next: function() {
            while (true) {
                if (!gi) {
                    var ng = g.next();
                    if (ng == undefined)
                        return undefined;
                    gi = fn(ng);
                }
                var value = gi.next();
                if (value != undefined)
                    return value;
                gi = null;
            }
        },
        reset: function() {
            gi && gi.reset();
            gi = null;
            g.reset();
        }
    }
}

Generator.cycle = function(g) {
    return {
        next: function() {
            var value = g.next();
            if (value == undefined) {
                g.reset();
                value = g.next();
            }
            return value;
        },
        reset: function() {
            g.reset();
        }
    }
}

Generator.fromArray = function(ar) {
    var ix = 0;
    return {
        next: function() {
            if (ix >= ar.length)
                return undefined;
            return ar[ix++];
        },
        reset: function() {
            ix = 0;
        }
    }
}

Generator.map = function(fn, g) {
    return {
        next: function() {
            return fn(g.next());
        },
        reset: g.reset.bind(g)
    }
}

Generator.shuffle = function(ar) {
    return Generator.fromArray(ar.shuffle());
}

Generator.toList = function(g) {
    var ar = [];
    var value;
    while ((value = g.next()) != undefined)
        ar.push(value);
    return ar;
}

Generator.tap = function(fn, g) {
    return {
        next: function() {
            var value = g.next();
            fn(value);
            return value;
        },
        reset: function() {g.reset()}
    }
}

Generator.removeNeighboringDuplicates = function(n, g) {
    var recent = [];
    return {
        next: function() {
            var value;
            while ((value = g.next()) != undefined && recent.indexOf(value) >= 0)
                ;
            recent.push(value);
            if (recent.length > n)
                recent.shift();
            return value;
        },
        reset: function() {
            last = undefined;
            g.reset();
        }
    }
}


/*
 * Model
 */

/// Cell states.  These need to be [0..3).

var SQUARE = 0;
var BEND_DEXTER = 1;
var BEND_SINISTER = 2;

/// Centers of the polygons within a dual cell.  For a given cell state,
/// some of these will be coincident.  (For a square cell, they all
/// coincide.)

var LEFT = 'left';
var RIGHT = 'right';
var TOP = 'top';
var BOTTOM = 'bottom';
var CENTER = 'center';

function TileModel(rows, columns, cfn) {
    this.rows = rows;
    this.columns = columns;
    this.cells = Array.zeros([rows, columns]);
    cfn && this.fill(cfn);
}

TileModel.STATES = 3;

TileModel.prototype.clone = function(fn) {
    var clone = new TileModel(this.rows, this.columns);
    this.eachPosition(function(i, j) {
        clone.cells[i][j] = this.cells[i][j];
    }, this);
    return clone;
}

TileModel.prototype.eachPosition = function(fn, target) {
    var rows = this.rows;
    var columns = this.columns;
    for (var i = 0; i < rows; i++)
        for (var j = 0; j < columns; j++)
            fn.call(target, i, j);
}

TileModel.prototype.fill = function(cfn) {
    var cells = this.cells;
    this.eachPosition(function(i, j) {
        cells[i][j] = cfn.applyTo(this, i, j);
    });
    return this;
}

TileModel.prototype.randomize = function() {
    this.fill(new CellFunction('random(3)'));
}


/*
 * Move Generator
 */

function MoveGenerator(model, expr) {
    this.cells = model.cells;
    this.rows = model.rows;
    this.columns = model.columns;
    this.transitionCompleteCallback = null;
    this.reset();
    if (expr)
        this.queueMovesTo(new TileModel(this.rows, this.columns).fill(expr));
}

MoveGenerator.prototype.reset = function() {
    this.moves = [];
    this.generationFunction = function() {return null};
    this.getRatio = function(){return 0}
}

MoveGenerator.prototype.next = function() {
    var cells = this.cells;
    var move;
    while ((move = this.generationFunction()) &&
           cells[move.i][move.j] == move.state)
        ;
    if (move) {
        move.applyTo = function(model) {
            model.cells[move.i][move.j] = move.state;
        }
    }
    return move;
}

MoveGenerator.prototype.queueMovesTo = function(targetModel) {
    var moves = [];
    targetModel.eachPosition(function(i, j) {
        moves.push({i: i,
                    j: j,
                    state: targetModel.cells[i][j]});
    });
    moves = moves.select(function(move) {
        return this.cells[move.i][move.j] != move.state;
    }.bind(this));
    this.sortMoves(moves);
    this.moves = moves.concat(this.moves);
    this.generationFunction = this.nextQueuedMove;
    var initialMoveLength = moves.length;
    this.getRatio = function() {
        return this.moves.length / initialMoveLength;
    }.bind(this);
}

/// @private

MoveGenerator.prototype.generationFunction = function() {
    return {i: random(this.rows),
            j: random(this.columns),
            state: random(3)};
}

MoveGenerator.prototype.nextQueuedMove = function() {
    return this.moves.pop();
}

MoveGenerator.prototype.sortMoves = function(moves) {
    var biasAngle = this.sweepAngle = 2 * Math.PI*Math.random();
    var biasDiffusion = 2 * Math.random();
    var rowBias = biasDiffusion * Math.cos(biasAngle);
    var columnBias = biasDiffusion * Math.sin(biasAngle);
    moves.sortUnder(function(move) {
        var i = move.i;
        var j = move.j;
        return sortKey = Math.random() + rowBias * i/3 + columnBias * j/3;
    });
}


/*
 * Viewer
 */

var gConsolidateLines = true;

function TileViewer(container, model, options) {
    this.model = model;
    this.options = options;
    this.setModel(model);
    var canvas = this.canvas = document.createElement('canvas');
    canvas.width = options.cellWidth * this.columns;
    canvas.height = options.cellHeight * this.rows;
    container.style.width = canvas.width + 'px';
    container.style.height = canvas.height + 'px';
    container.appendChild(canvas);
    this.ctx = canvas.getContext('2d');
}

TileViewer.prototype.setModel = function(model) {
    this.cells = model.cells;
    this.rows = model.rows;
    this.columns = model.columns;
}

TileViewer.prototype.getState = function() {
    return {
        model: this.model,
        generator: this.generator
    }
}

TileViewer.prototype.setState = function(state) {
    this.setModel(state.model);
    this.generator = state.generator;
}

TileViewer.prototype.draw = function() {
    if (this.options.dual) {
        this.strokeCells();
        this.fillCells();
    }
    this.options.grid && this.drawGrid();
}

TileViewer.prototype.redraw = function() {
    var ctx = this.ctx;
    var canvas = this.canvas;
    ctx.beginPath();
    ctx.rect(0, 0, canvas.width, canvas.height);
    ctx.clip();
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.beginPath();
    this.draw();
}

TileViewer.prototype.setColorStrategyIndex = function(index) {
    var m = gColorStrategyFunctions.length;
    this.options.colorStrategyIndex = index = ((index % m) + m) % m;
    var cfn = new CellFunction(gColorStrategyFunctions[index]);
    this.colorModel = new TileModel(this.rows, this.columns, cfn);
    this.redraw();
}

/// @private

TileViewer.prototype.strokeCells = function() {
    var ctx = this.ctx;
    this.model.eachPosition(function(i, j) {
        this.drawCell(i, j);
    }, this);
    ctx.strokeStyle = this.options.edgeColor;
    ctx.stroke();
}

TileViewer.prototype.fillCells = function() {
    var ctx = this.ctx;
    this.model.eachPosition(function(i, j) {
        this.fillCell(i, j);
    }, this);
}

TileViewer.prototype.drawGrid = function() {
    var ctx = this.ctx;
    //ctx.beginPath();
    this.model.eachPosition(this.drawGridAt, this);
    this.strokeWidth = 2;
    //ctx.stroke();
    this.strokeWidth = 1;
}

TileViewer.prototype.drawGridAt = function(i, j) {
    var ctx = this.ctx;
    ctx.beginPath();
    this.moveTo(i+1, j, CENTER);
    this.lineTo(i,j, CENTER);
    this.lineTo(i,j+1, CENTER);
    switch (this.cells[i][j]) {
    case BEND_SINISTER:
        break;
    case BEND_DEXTER:
        break;
    }
    this.strokeWidth = 2;
    ctx.stroke();
    this.strokeWidth = 1;
}

TileViewer.prototype.fillCell = function(i, j) {
    if (!(0 <= i && i+1 < this.rows && 0 <= j && j+1 < this.columns)) return;
    var ctx = this.ctx;
    var colors = this.options.cellColors;
    //var color = colors[(i^j) % colors.length];
    var color = colors[(i*i+j*j+(Math.floor(i/5+j/10)&1)) % colors.length];
    if (this['colorModel']) {
        var cix = this.colorModel.cells[i][j];
        var color = colors[cix % colors.length];
    }
    var alpha = (i / this.rows + j / this.columns) / 5;
    ctx.fillStyle = 'rgba(' + color + ','+alpha+')';
    ctx.beginPath();
    this.moveTo(i, j, BOTTOM);
    this.lineTo(i, j, RIGHT);
    this.lineTo(i, j+1, LEFT);
    this.lineTo(i, j+1, BOTTOM);
    this.lineTo(i+1, j+1, TOP);
    this.lineTo(i+1, j+1, LEFT);
    this.lineTo(i+1, j, RIGHT);
    this.lineTo(i+1, j, TOP);
    ctx.fill();
}

TileViewer.prototype.nextFrame = function() {
    var move = this.generator && this.generator.next();
    if (!move) return;
    move.applyTo(this.model);

    var ctx = this.ctx;
    var ci = move.i;
    var cj = move.j;

    ctx.save();
    this.moveTo(ci-1, cj-1, CENTER);
    this.lineTo(ci-1, cj+1, CENTER);
    this.lineTo(ci+1, cj+1, CENTER);
    this.lineTo(ci+1, cj-1, CENTER);
    ctx.clip();

    ctx.fillStyle = 'white';
    ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

    var rows = this.rows,
        columns = this.columns;

    if (this.options.dual) {
        for (var i = ci-2; i <= ci+1; i++)
            for (var j = cj-2; j <= cj+1; j++)
                0 <= i && i < rows && 0 <= j && j < columns && this.fillCell(i, j);

        ctx.beginPath();
        for (var i = ci-1; i <= ci+1; i++)
            for (var j = cj-1; j <= cj+1; j++)
                0 <= i && i < rows && 0 <= j && j < columns && this.drawCell(i, j);
        ctx.strokeStyle = this.options.edgeColor;
        ctx.stroke();
    }
    if (this.options.grid)
        for (var i = ci-1; i < ci+1; i++)
            for (var j = cj-1; j < cj+1; j++)
                0 <= i && i < rows && 0 <= j && j < columns && this.drawGridAt(i, j);

    ctx.restore();
}

TileViewer.prototype.drawCell = function(i, j) {
    var ctx = this.ctx;
    var cells = this.cells;
    if (j+1 < this.columns) {
        this.moveTo(i, j, RIGHT);
        this.lineTo(i, j+1, LEFT);
        gConsolidateLines || ctx.stroke();
    }
    if (i+1 < this.rows) {
        this.moveTo(i, j, BOTTOM);
        this.lineTo(i+1, j, TOP);
        gConsolidateLines || ctx.stroke();
    }
    if (cells[i][j]) {
        this.moveTo(i, j, TOP);
        this.lineTo(i, j, BOTTOM);
        gConsolidateLines || ctx.stroke();
    }
}

TileViewer.prototype.moveTo = function(i, j, side) {
    var ctx = this.ctx;
    var pos = this.getPos(i, j, side);
    ctx.moveTo(pos.x, pos.y);
}

TileViewer.prototype.lineTo = function(i, j, side) {
    var ctx = this.ctx;
    var pos = this.getPos(i, j, side);
    ctx.lineTo(pos.x, pos.y);
}

TileViewer.prototype.getPos = function(i, j, side) {
    var cells = this.cells;
    var options = this.options;
    var x = j;
    var y = i;
    var inset = 1/2-1/3;
    var type = cells[i] && cells[i][j];
    switch (type) {
    case BEND_DEXTER:
        switch (side) {
        case LEFT:
        case BOTTOM:
            x -= inset;
            y += inset;
            break;
        case RIGHT:
        case TOP:
            x += inset;
            y -= inset;
        }
        break;
    case BEND_SINISTER:
        switch (side) {
        case LEFT:
        case TOP:
            x -= inset;
            y -= inset;
            break;
        case RIGHT:
        case BOTTOM:
            x += inset;
            y += inset;
        }
    }
    if (options.sphere) {
        // not a true sphere, but it's faster and looks nice
        var x0 = options.cellWidth * this.columns/2,
            y0 = options.cellHeight * this.rows/2,
            dx = 2*(x / (this.columns + inset - 1) - .5),
            dy = 2*(y / (this.rows + inset - 1) - .5),
            a = Math.atan2(dy, dx) + options.rotation,
            pow = options.rounding,
            r = Math.sqrt(dx*dx+dy*dy) / Math.pow(2, 1/pow);
        r = 1 - Math.pow(Math.max(0, 1-r), pow);
        //r = Math.sin(Math.PI*r);
        r *= options.cellWidth * this.columns/2;
        return {x: x0 + r * Math.cos(a), y: y0 + r * Math.sin(a)}
    }
    return {x: options.cellWidth*(x+.5), y: options.cellHeight*(y+.5)};
}


/*
 * Expression Label
 */

function LabelView(container) {
    var view = this.view = document.createElement('div');
    view.className = 'expression-label';
    container.appendChild(view);
    this.containerView = container;
    this.containerHeight = $(container).getDimensions().height;
    view.style.left = $(container).getDimensions().width + 'px';
}

LabelView.prototype.setHTML = function(html) {
    this.view.innerHTML = html;
}

LabelView.prototype.moveToRatio = function(s) {
    this.moveTo(this.ratioToPosition(s));
}

LabelView.prototype.slideToRatio = function(s) {
    this.slideTo(this.ratioToPosition(s));
}

LabelView.prototype.ratioToPosition = function(s) {
    var labelHeight = this.view.getDimensions().height;
    return s * this.containerHeight - labelHeight;
}

LabelView.prototype.moveTo = function(x) {
    this.x0 = x;
    this.dx0 = 0;
    this.view.style.top = Math.floor(x) + 'px';
}

LabelView.prototype.slideTo = function(x1) {
    var view = this.view;
    var dx = x1 - this.x0;
    var ddx = dx - this.dx0;
    var s = 1/10;
    if (Math.abs(ddx) > s)
        ddx = s * ddx / Math.abs(ddx);
    this.dx0 += ddx;
    this.x0 += this.dx0;
    //this.dx0 *= .999;
    view.style.top = Math.floor(this.x0) + 'px';
}


/*
 * Cell Function Expressions
 */

var gExpressions = [
    0,
    1,
    2,
    'i',
    'j',
    'i+j',
    'i-j',
    'i*j',
    'i&j',
    'i|j',
    'i^j',
    'i&1',
    '(i^j)&2',
    '1+(i&1)',
    '1+(j&1)',
    '1+(i&j&1)',
    '1+((i^j)&1)',
    '1+((i^j^1)&1)',
    'i*i*j',
    'i/(j+1)',
    '5*i/(j+1)',
    '10*i/(j+1)',
    'random(3)',
    'sin(i+j)',
    'sin(i-j)',
    'sin(i*j)',
    'sin(i)*sin(j)',
    'exp(i,j)',
    'pow(i,j)'
];

var gColorStrategyFunctions = gExpressions.slice(3);

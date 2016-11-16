/*
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/tools/rematch
  License: MIT License.
*/

var FSA = function (states, transitions, initialState, finalStates) {
  this.states = states;
  this.initialState = initialState;
  this.transitions = transitions;
  this.finalStates = finalStates;
}

FSA.prototype.computeSuccessors = function (states, symbol) {
    var newStates = [];
    for (var i in states) {
        var s = states[i];
        for (var j in this.transitions) {
            var t = this.transitions[j];
            if (t.start == s && t.edge.indexOf(symbol) >= 0)
                if (!Array.includes(newStates, t.end))
                    newStates.push(t.end);
        }
    }
    return newStates;
};

FSA.prototype.includesFinalState = function (states) {
    for (var i in states)
        if (Array.includes(this.finalStates, states[i]))
            return true;
    return false;
};

/*
Copyright 2005-2006 Oliver Steele.  Some rights reserved.
$LastChangedDate: 2006-01-07 15:24:44 -0500 (Sat, 07 Jan 2006) $

This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 2.5 License:
http://creativecommons.org/licenses/by-nc-sa/2.5/.
*/

/*
Implementation notes:
- This code uses the optimization that array[i]=n is faster than array.push(n)
in the AVM. <http://www.openlaszlo.org/docs/guide/performance-tuning.html>
 */

var DataFrame = function () {
  this.rowNames = [];
  this.columnNames = [];
  this.columns = [];
  this.columnSumCache = {}; // {[start,end+1] -> [sum]}
  this.columnNameIndices = {}; // {Date -> row_index}
  this.rowNameIndices = {}; // {tagname -> col_index}
};

DataFrame.prototype = {
  getColumnIndex: function (name) {
    var i = this.columnNameIndices[name];
    // create new columns on demand
    if (!(i >= 0)) {
      i = this.columnNameIndices[name] = this.columnNames.length;
      var column = new Array(this.rowNames.length);
      for (var j = 0; j < column.length; j++)
        column[j] = 0;
      this.columnNames.push(name);
      this.columns.push(column);
    }
    return i;
  },
  
  getRowIndex: function (name) {
    var j = this.rowNameIndices[name];
    // create new rows on demand
    if (!(j >= 0)) {
      j = this.rowNameIndices[name] = this.rowNames.length;
      this.rowNames.push(name);
      // fill up new rows, to keep the matrix rectangular
      for (var i = 0; i < this.columns.length; i++)
        this.columns[i][j] = 0;
    }
    return j;
  },
  
  // helper method for columnRangeSum.  This method iterates over all
  // the rows.
  addColumns_iterate: function (a, b) {
    var sum = new Array(this.columnNames.length);
    for (var j = 0; j < sum.length; j++) sum[j] = 0;
    for (var i = a; i < b; i++) {
      var column = this.columns[i];
      for (var t = 0; t < column.length; t++)
        sum[t] += column[t];
    }
    return sum;
  },
  
  // helper method for columnRangeSum.  This method iterates or
  // subdivides, depending on the range.  It recurses through _memoize
  // to use the cache.  It only subdivides at binary subdivisions of
  // the domain range ([left, right]), rather than the selection range
  // ([a,b]).  This allows the cache to be reused when the selection
  // changes.
  addColumns_choose: function (a, b, left, right) {
    // subdivision cutoff; empirically determined
    if (b - a < 4) return this.addColumns_iterate(a, b);
    var middle = Math.floor((left + right)/2);
    var s0 = a < middle && this.addColumns_memoize(a, Math.min(b, middle-1), left, middle);
    var s1 = middle <= b && this.addColumns_memoize(Math.max(a, middle), b, middle, right);
    if (!s0 || !s1) return s0 || s1;
    var sum = new Array(s0.length);
    for (var i = 0; i < s0.length; i++)
      sum[i] = s0[i] + s1[i];
    return sum;
  },
  
  // helper method for columnRangeSum.  This method memoizes
  // addcolumns_choose.  It only caches ranges in the binary
  // subdivision tree of the domain.  This allows the cache
  // to be reused when the range window changes.
  addColumns_memoize: function (a, b, left, right) {
    if (arguments.length <= 2) {
      left = 0;
      right = this.columns.length;
    }
    var cache = this.columnSumCache;
	// only cache even subdivisions
    var key = a == left && b == right-1 && [a,b];
    if (key && cache[key]) return cache[key];
    if (a==b) return null;
    var sum = this.addColumns_choose(a, b, left, right);
    if (key) cache[key] = sum;
    return sum;
  },
  
  columnRangeSum: function(a, b) {
    //var t0 = (new Date).getTime();
    sum = this.addColumns_memoize(a, b);
    //Debug.write((new Date).getTime()-t0);
    return sum;
  },
  
  getColumnSums: function () {
    var sums = new Array(this.columns.length);
    for (var i = 0; i < this.columns.length; i++) {
      var column = this.columns[i];
      var sum = 0;
      for (var j = 0; j < column.length; j++)
        sum += column[j];
      sums[i] = sum;
    }
    return sums;
  }
}

// Return an array +inversion+ s.t. Ai: source[inversions[i]]=target[i].
// Or maybe I've got that backwards.  It doesn't matter, since you
// won't be able to use it correctly without trying it post ways
// anyway.
function computeArrayinversion(source, target) {
  inversion = [];
  for (var i = 0; i < source.length; i++) {
    var tagname = source[i];
    var j = 0;
    while (target[j] != tagname) j++;
    inversion.push(j);
  }
  return inversion;
}

// posts: [Element name='post']
// returns: DataFrame
function fillTagFrame(dataframe, posts) {
  // This relies on the fact that:
  // 1. Flash iterates backwards, and
  // 2. Delicious returns posts in backwards order
  for (var i in posts) {
    var post = posts[i];
    var date = post.attributes['time'].split('T')[0];
    var tags = post.attributes['tag'].split(' ');
    var di = dataframe.getColumnIndex(date);
    var counts = dataframe.columns[di];
    for (var j in tags) {
      var tag = tags[j];
      var ti = dataframe.getRowIndex(tag);
      counts[ti] += 1;
    }
  }
}

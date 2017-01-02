var jQuery = $ = function(a, c) {
    if (!this || !this.init)
        return new jQuery(a, c);
    return this.init(a,c);
}

jQuery.ajax = ajax;
jQuery.post = ajax.post;
jQuery.get = ajax.get;

jQuery.fn = jQuery.prototype = {
    init: function(a, c) {
    },
    
    jquery: '0.9flash',
    
    size: function() {
        return this.length;
    },
    
    length: 0,
    
    get: function(n) {
        return n == undefined
            ? jQuery.makeArray(this)
            : this[n];
    },
    
    each: function(fn, args) {
        return jQuery.each(this, fn, args);
    },
    
    index: function(item) {
        var pos = -1;
        this.each(function(i) {
            pos >= 0 || this == item && (pos = i);
        });
        return pos;
    },
    
    attr: function(key, value, type) {
        this.setAttribute(key, value);
    },
    
    show : function(speed, callback) {
        return this.speed
            ? this.animate({opacity: 1}, speed, callback)
        : this.filter(':hidden').opacity
    }
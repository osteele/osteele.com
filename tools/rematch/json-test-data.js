/*
Copyright 2006 Oliver Steele.  Some rights reserved.

This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 2.5 License:
http://creativecommons.org/licenses/by-nc-sa/2.5/.
*/

// These are used to test both encoding and decoding.  This is a
// flattened list of [value, string] pairs --- flattened, so that it's
// easier to maintain the test data.
var twoWayTests = [
    false, 'false',
    true, 'true',
    null, 'null',
    // numbers
    123, '123',
    -123, '-123',
    123.4, '123.4',
    -123.4, '-123.4',
    NaN, 'null',
    Infinity, 'null',
    -Infinity, 'null',
    // strings
    "", '""',
    "abc", '"abc"',
    "\nabc", '"\\nabc"',
    "abc\n", '"abc\\n"',
    "a\nb\nc", '"a\\nb\\nc"',
    "a\\b\"c\n\r\f\t\b", '"a\\\\b\\\"c\\n\\r\\f\\t\\b"',
    "\u0123", '"\\u0123"',
    // arrays
    [], '[]',
    [1], '[1]',
    [1,2], '[1,2]',
    [true,false,null,"abc"], '[true,false,null,"abc"]',
    // objects
    {}, '{}',
    {a: 1}, '{"a":1}',
    {a: 1, b: 2}, '{"a":1,"b":2}',
    {a: 1, b: true, c: false, d: null}, '{"a":1,"b":true,"c":false,"d":null}'
    ];

// These can only be used to test decoding, since the stringified
// expressions have simpler normal forms (e.g., 123e1 is stringified
// as 1230).
var decodingTests = [
    '123e1', 1230,
    '123e+1', 1230,
    '123e-1', 12.3,
    '123E1', 1230,
    '123E+1', 1230,
    '123E-1', 12.3,
    '-123e1', -1230,
    '123.4e1', 1234,
    '123.4e+1', 1234,
    '123.4e-1', 12.34,
    '123.4E1', 1234,
    '123.4E+1', 1234,
    '123.4E-1', 12.34,
    '0.000001e12', 1000000
    ];

// Test whitespace in various positions.  Like decodingTests, these 
var whitespaceTests = [
    ' 123', 123,
    ' 123 ', 123,
    ' [ 1 , 2 ] ', [1,2],
    ' { "a" : 1 , "b" : 2 } ', {a: 1, b: 2}
    ];

var invalidEncodingTests = [
    '',
    ' ',
    '-',
    '.1',
    '123.4.5',
    '123e1e1',
    '123a',
    '"abc',
    '"abc\\"',
    '"abc\\u"',
    '"abc\\udefg"',
    '[',
    ']',
    '[,]',
    '[1,]',
    '[,1]',
    '[1 2]',
    '1 2',
    '{,}',
    '{"a":1,}',
    '{,"a":1}',
    '{"a":1 "b":2}'
    ];

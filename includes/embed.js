/*
 * JavaScript library for embedding Laszlo applications
 *
 * Copyright Laszlo Systems, Inc. 2003
 * All Rights Reserved.
 *
 * Usage:
 * In the <html><head> of an HTML document that embeds a Laszlo application,
 * add this line:
 *   <script src="{$lps}/embed.js" language="JavaScript" type="text/javascript"/>
 * At the location within the <html><body> where the application is to be
 * embeded, add this line:
 *   <script language="JavaScript" type="text/javascript">
 *     lzEmbed({url: 'myapp.lzx?lzt=swf', bgcolor: '#000000', width: '800', height: '600'});
 *   </script>
 * where the url matches the URI that the application is served from, and
 * the other properties match the attributes of the application's canvas.
 */

/* Write a tag start.  This code assumes that the attribute values don't
 * require inner quotes; for instance, {x: '100'} works, but
 * {url: 'a>b'} or {url: 'a"b'} won't. */
function lzWriteElement(name, attrs, closep, escapeme) {
    var lt = escapeme ? '&lt;' : '<';
    var o = lt + name;
    for (var p in attrs)
        o += ' ' + p + '="' + attrs[p] + '"';
    if (closep)
        o += '/';
    o += '>';
    return o;
}

function containskey (arr, key) {
    return (arr[key] != null);
}

/* Update each property of a with the value of the same-named property
 * on b. For example, lzUpdate({a:1, b:2}, {b:3, c:4}) mutates the
 * first argument into {a:1, b:3}. */
function lzUpdate(a, b) {
    for (var p in a)
        if (containskey(b,p)) {
            a[p] = b[p];
        }
}


/* Write an <object> and <embed> tag into the document at the location
 * where this function is called.  Properties is an Object whose properties
 * override the attributes and <param> children of the <object> tag, and
 * the attributes of the <embed> tag.
 */
function lzEmbed(properties, escapeme)
{
    var url = properties.url;
    var width = properties.width;
    var height = properties.height;
    var o = '';
    var lt = escapeme ? '&lt;' : '<';
    
    objectAttributes = {
        classid: 'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000',
        type: 'application/x-shockwave-flash',
        width: 0, height: 0
    };

    lzUpdate(objectAttributes, properties);
    objectParams = {
        movie: url,
        scale: 'exactfit',
        salign: 'lt',
        // The properties parameter should override these.
        width: 0, height: 0, bgcolor: 0};
    lzUpdate(objectParams, properties);
    embedAttributes = {
        type: 'application/x-shockwave-flash',
        pluginspage: "http://www.macromedia.com/go/getflashplayer",
        scale: 'exactfit',
        src: url,
        quality: 'high',
        salign: 'lt',
        // The properties parameter should override these.
        width: 0, height: 0, bgcolor: 0};
    lzUpdate(embedAttributes, properties);
    
    var ns = (document.layers)? true:false;
    var ie = (document.all)? true:false;

    // add codebase object to upgrade windows ie players
    // supposedly breaks some mac ie?
    var win = navigator.appVersion.indexOf('Win') != -1;
    if (win)
        objectAttributes.codebase =  "http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0";

    if (navigator.appVersion.indexOf("Macintosh")!=-1) {
        objectAttributes['data'] = url;
    }

    if  (ns) {
        o = lzWriteElement('embed', embedAttributes, true, escapeme);
    } else {
        o = lzWriteElement('object', objectAttributes, false, escapeme);
        for (var p in objectParams)
        o += lt + 'param name="' +
                 p + '" value="' +
                 objectParams[p] + '" />\n';
        o += lzWriteElement('embed', embedAttributes, true, escapeme);
        o += lt + '/object>\n';
    } 
    //alert(o);
    document.write(o);
    return o;
}

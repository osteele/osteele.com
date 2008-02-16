<!DOCTYPE html PUBLIC "-//IETF//DTD HTML 2.0//EN">
<HTML>
<HEAD>
<TITLE>TransformUtils.java</TITLE>
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#1F00FF" ALINK="#FF0000" VLINK="#9900DD">
<A NAME="top">
<A NAME="file1">
<H1>openlaszlo/TransformUtils.java</H1>

<PRE>
<I><FONT COLOR="#B22222">/* *****************************************************************************
 * TransformUtils.java
 * ****************************************************************************/</FONT></I>

<I><FONT COLOR="#B22222">/* J_LZ_COPYRIGHT_BEGIN *******************************************************
* Copyright 2001-2004 Laszlo Systems, Inc.  All Rights Reserved.              *
* Use is subject to license terms.                                            *
* J_LZ_COPYRIGHT_END *********************************************************/</FONT></I>

<B><FONT COLOR="#A020F0">package</FONT></B> org.openlaszlo.utils;
<B><FONT COLOR="#A020F0">import</FONT></B> java.io.*;
<B><FONT COLOR="#A020F0">import</FONT></B> java.util.*;
<B><FONT COLOR="#A020F0">import</FONT></B> javax.xml.transform.*;

<B><FONT COLOR="#A020F0">public</FONT></B> <B><FONT COLOR="#A020F0">abstract</FONT></B> <B><FONT COLOR="#A020F0">class</FONT></B> TransformUtils {
    <I><FONT COLOR="#B22222">/** {Pathname -&gt; Templates} */</FONT></I>
    <B><FONT COLOR="#A020F0">static</FONT></B> <B><FONT COLOR="#A020F0">private</FONT></B> Map sTemplatesMap = <B><FONT COLOR="#A020F0">new</FONT></B> HashMap();
    <I><FONT COLOR="#B22222">/** {Pathname -&gt; lastModified} */</FONT></I>
    <B><FONT COLOR="#A020F0">static</FONT></B> <B><FONT COLOR="#A020F0">private</FONT></B> Map sTemplatesLastModified = <B><FONT COLOR="#A020F0">new</FONT></B> HashMap();
    
    <B><FONT COLOR="#A020F0">static</FONT></B> <B><FONT COLOR="#A020F0">public</FONT></B> Templates getTemplates(String styleSheetPathname)
        <B><FONT COLOR="#A020F0">throws</FONT></B> IOException
    {
        <B><FONT COLOR="#A020F0">synchronized</FONT></B> (sTemplatesMap) {
            Templates templates = (Templates) sTemplatesMap.get(styleSheetPathname);
            Long lastModified = <B><FONT COLOR="#A020F0">new</FONT></B> Long(<B><FONT COLOR="#A020F0">new</FONT></B> File(styleSheetPathname).lastModified());
            <B><FONT COLOR="#A020F0">if</FONT></B> (templates != <B><FONT COLOR="#A020F0">null</FONT></B> &amp;&amp;
                !sTemplatesLastModified.get(styleSheetPathname).equals(lastModified))
                templates = <B><FONT COLOR="#A020F0">null</FONT></B>;
            <B><FONT COLOR="#A020F0">if</FONT></B> (templates == <B><FONT COLOR="#A020F0">null</FONT></B>) {
                CollectingErrorListener errorListener =
                    <B><FONT COLOR="#A020F0">new</FONT></B> CollectingErrorListener();
                <I><FONT COLOR="#B22222">// name the class instead of using
</FONT></I>                <I><FONT COLOR="#B22222">// TransformerFactory.newInstance(), to insure that we
</FONT></I>                <I><FONT COLOR="#B22222">// get saxon and thereby work around bug 3924
</FONT></I>                TransformerFactory factory =
                    <B><FONT COLOR="#A020F0">new</FONT></B> com.icl.saxon.TransformerFactoryImpl();
                factory.setErrorListener(errorListener);
                java.io.InputStream xslInput = 
                    <B><FONT COLOR="#A020F0">new</FONT></B> java.net.URL(<FONT COLOR="#BC8F8F"><B>&quot;file&quot;</FONT></B>, <FONT COLOR="#BC8F8F"><B>&quot;&quot;</FONT></B>, styleSheetPathname).openStream();
                <B><FONT COLOR="#A020F0">try</FONT></B> {
                    Source xslSource = 
                        <B><FONT COLOR="#A020F0">new</FONT></B> javax.xml.transform.stream.StreamSource(xslInput);
                    templates = factory.newTemplates(xslSource);
                } <B><FONT COLOR="#A020F0">catch</FONT></B> (TransformerConfigurationException e) {
                    <B><FONT COLOR="#A020F0">if</FONT></B> (errorListener.isEmpty())
                        <B><FONT COLOR="#A020F0">throw</FONT></B> <B><FONT COLOR="#A020F0">new</FONT></B> ChainedException(e);
                    <B><FONT COLOR="#A020F0">else</FONT></B>
                        <B><FONT COLOR="#A020F0">throw</FONT></B> <B><FONT COLOR="#A020F0">new</FONT></B> ChainedException(errorListener.getMessage(), e);
                } <B><FONT COLOR="#A020F0">finally</FONT></B> {
                    xslInput.close();
                }
                sTemplatesMap.put(styleSheetPathname, templates);
                sTemplatesLastModified.put(styleSheetPathname, lastModified);
            }
            <B><FONT COLOR="#A020F0">return</FONT></B> templates;
        }
    }

    <B><FONT COLOR="#A020F0">public</FONT></B> <B><FONT COLOR="#A020F0">static</FONT></B> <B><FONT COLOR="#A020F0">void</FONT></B> applyTransform(String styleSheetPathname,
                                      String xmlString,
                                      OutputStream out)
        <B><FONT COLOR="#A020F0">throws</FONT></B> IOException
    {
        applyTransform(styleSheetPathname, <B><FONT COLOR="#A020F0">new</FONT></B> Properties(), xmlString, out);
    }
    
    <I><FONT COLOR="#B22222">// http://xml.apache.org/xalan-j/usagepatterns.html#multithreading
</FONT></I>    <I><FONT COLOR="#B22222">// describes this usage pattern.
</FONT></I>    <B><FONT COLOR="#A020F0">static</FONT></B> <B><FONT COLOR="#A020F0">public</FONT></B> <B><FONT COLOR="#A020F0">void</FONT></B> applyTransform(String styleSheetPathname,
                                      Properties properties,
                                      String xmlString,
                                      OutputStream out)
        <B><FONT COLOR="#A020F0">throws</FONT></B> IOException 
    {
        PrintWriter writer = <B><FONT COLOR="#A020F0">new</FONT></B> PrintWriter(out);
        CollectingErrorListener errorListener =
            <B><FONT COLOR="#A020F0">new</FONT></B> CollectingErrorListener();
        <B><FONT COLOR="#A020F0">try</FONT></B> {
            Templates template = getTemplates(styleSheetPathname);
            Transformer transformer = template.newTransformer();
            transformer.setErrorListener(errorListener);
            Source xmlSource =
                <B><FONT COLOR="#A020F0">new</FONT></B> javax.xml.transform.stream.StreamSource(
                    <B><FONT COLOR="#A020F0">new</FONT></B> StringReader(xmlString));
            <B><FONT COLOR="#A020F0">for</FONT></B> (Iterator iter = properties.keySet().iterator();
                 iter.hasNext(); ) {
                String key = (String) iter.next();
                String value = properties.getProperty(key);
                transformer.setParameter(key, value);
            }
            <I><FONT COLOR="#B22222">// Perform the transformation, sending the output to the response.
</FONT></I>            transformer.transform(xmlSource,
                                  <B><FONT COLOR="#A020F0">new</FONT></B> javax.xml.transform.stream.StreamResult(writer));
            writer.close();
        } <B><FONT COLOR="#A020F0">catch</FONT></B> (TransformerConfigurationException e) {
            <B><FONT COLOR="#A020F0">if</FONT></B> (errorListener.isEmpty())
                <B><FONT COLOR="#A020F0">throw</FONT></B> <B><FONT COLOR="#A020F0">new</FONT></B> ChainedException(e);
            <B><FONT COLOR="#A020F0">else</FONT></B>
                <B><FONT COLOR="#A020F0">throw</FONT></B> <B><FONT COLOR="#A020F0">new</FONT></B> ChainedException(errorListener.getMessage(), e);
        } <B><FONT COLOR="#A020F0">catch</FONT></B> (TransformerException e) {
            <B><FONT COLOR="#A020F0">if</FONT></B> (errorListener.isEmpty())
                <B><FONT COLOR="#A020F0">throw</FONT></B> <B><FONT COLOR="#A020F0">new</FONT></B> ChainedException(e);
            <B><FONT COLOR="#A020F0">else</FONT></B>
                <B><FONT COLOR="#A020F0">throw</FONT></B> <B><FONT COLOR="#A020F0">new</FONT></B> ChainedException(errorListener.getMessage(), e);
        }
    }
}

<B><FONT COLOR="#A020F0">class</FONT></B> CollectingErrorListener <B><FONT COLOR="#A020F0">implements</FONT></B> javax.xml.transform.ErrorListener {
    <B><FONT COLOR="#A020F0">protected</FONT></B> StringBuffer messageBuffer = <B><FONT COLOR="#A020F0">new</FONT></B> StringBuffer();
    <B><FONT COLOR="#A020F0">protected</FONT></B> String separator = <FONT COLOR="#BC8F8F"><B>&quot;&quot;</FONT></B>;
    <B><FONT COLOR="#A020F0">protected</FONT></B> <B><FONT COLOR="#A020F0">int</FONT></B> messageCount = 0;
    
    <B><FONT COLOR="#A020F0">private</FONT></B> <B><FONT COLOR="#A020F0">void</FONT></B> appendErrorString(TransformerException exception) {
        messageBuffer.append(separator);
        separator = <FONT COLOR="#BC8F8F"><B>&quot;\n&quot;</FONT></B>;
        messageBuffer.append(exception.getMessageAndLocation());
        messageCount++;
    }

    <B><FONT COLOR="#A020F0">public</FONT></B> <B><FONT COLOR="#A020F0">void</FONT></B> error(TransformerException exception) {
        appendErrorString(exception);
    }
    
    <B><FONT COLOR="#A020F0">public</FONT></B> <B><FONT COLOR="#A020F0">void</FONT></B> warning(TransformerException exception) {
        appendErrorString(exception);
    }
    
    <B><FONT COLOR="#A020F0">public</FONT></B> <B><FONT COLOR="#A020F0">void</FONT></B> fatalError(TransformerException exception) {
        appendErrorString(exception);
    }
    
    <B><FONT COLOR="#A020F0">boolean</FONT></B> isEmpty() {
        <B><FONT COLOR="#A020F0">return</FONT></B> messageCount == 0;
    }

    String getMessage() {
        <B><FONT COLOR="#A020F0">return</FONT></B> messageBuffer.toString();
    }
}
</PRE>
<HR>
<ADDRESS>Generated by <A HREF="http://www.iki.fi/~mtr/genscript/">GNU enscript 1.6.1</A>.</ADDRESS>
</BODY>
</HTML>

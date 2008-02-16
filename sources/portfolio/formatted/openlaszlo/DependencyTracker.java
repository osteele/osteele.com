<!DOCTYPE html PUBLIC "-//IETF//DTD HTML 2.0//EN">
<HTML>
<HEAD>
<TITLE>DependencyTracker.java</TITLE>
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#1F00FF" ALINK="#FF0000" VLINK="#9900DD">
<A NAME="top">
<A NAME="file1">
<H1>openlaszlo/DependencyTracker.java</H1>

<PRE>
<I><FONT COLOR="#B22222">/* *****************************************************************************
 * DependencyTracker.java
* ****************************************************************************/</FONT></I>

<I><FONT COLOR="#B22222">/* J_LZ_COPYRIGHT_BEGIN *******************************************************
* Copyright 2001-2004 Laszlo Systems, Inc.  All Rights Reserved.              *
* Use is subject to license terms.                                            *
* J_LZ_COPYRIGHT_END *********************************************************/</FONT></I>

<B><FONT COLOR="#A020F0">package</FONT></B> org.openlaszlo.cm;

<B><FONT COLOR="#A020F0">import</FONT></B> org.apache.log4j.*;
<B><FONT COLOR="#A020F0">import</FONT></B> java.io.*;
<B><FONT COLOR="#A020F0">import</FONT></B> java.util.*;
<B><FONT COLOR="#A020F0">import</FONT></B> org.openlaszlo.server.LPS;
<B><FONT COLOR="#A020F0">import</FONT></B> org.openlaszlo.utils.ChainedException;

<I><FONT COLOR="#B22222">/** Tracks compilation dependencies, so that it can tell if a file
 * needs to be recompiled.  An instance stores version information
 * about all the source files that an object file depends on (the
 * files such that, if one changed, the object file would be out of
 * date).
 *
 * @author Oliver steele
 */</FONT></I>
<B><FONT COLOR="#A020F0">class</FONT></B> DependencyTracker <B><FONT COLOR="#A020F0">implements</FONT></B> java.io.Serializable {
    <B><FONT COLOR="#A020F0">private</FONT></B> <B><FONT COLOR="#A020F0">static</FONT></B> Logger mLogger  = Logger.getLogger(DependencyTracker.<B><FONT COLOR="#A020F0">class</FONT></B>);

    <I><FONT COLOR="#B22222">/** Records information about the version of a file.
     */</FONT></I>
    <B><FONT COLOR="#A020F0">private</FONT></B> <B><FONT COLOR="#A020F0">static</FONT></B> <B><FONT COLOR="#A020F0">class</FONT></B> FileInfo <B><FONT COLOR="#A020F0">implements</FONT></B> java.io.Serializable {
        <I><FONT COLOR="#B22222">/** pathname */</FONT></I>
        <B><FONT COLOR="#A020F0">private</FONT></B> String mPathname;
        <I><FONT COLOR="#B22222">/** last mod time */</FONT></I>
        <B><FONT COLOR="#A020F0">private</FONT></B> <B><FONT COLOR="#A020F0">long</FONT></B> mLastMod;
        <I><FONT COLOR="#B22222">/** can read? */</FONT></I>
        <B><FONT COLOR="#A020F0">private</FONT></B> <B><FONT COLOR="#A020F0">boolean</FONT></B> mCanRead;
        <I><FONT COLOR="#B22222">/** File length */</FONT></I>
        <B><FONT COLOR="#A020F0">private</FONT></B> <B><FONT COLOR="#A020F0">long</FONT></B> mLength;
        
        <I><FONT COLOR="#B22222">/** Constructs an instance.
         * @param pathname the name of a file
         */</FONT></I>
        FileInfo(String pathname) {
            mPathname = pathname;
            File file = <B><FONT COLOR="#A020F0">new</FONT></B> File(pathname);
            <I><FONT COLOR="#B22222">// Cope with directory indexes for now
</FONT></I>            <I><FONT COLOR="#B22222">// FIXME: [2003-05-09 bloch] is this the right place
</FONT></I>            <I><FONT COLOR="#B22222">// for this?
</FONT></I>            <B><FONT COLOR="#A020F0">if</FONT></B> (file.isDirectory()) {
                file = <B><FONT COLOR="#A020F0">new</FONT></B> File(pathname + File.separator + <FONT COLOR="#BC8F8F"><B>&quot;library.lzx&quot;</FONT></B>);
            }
            <I><FONT COLOR="#B22222">// Truncate to an seconds
</FONT></I>            mLastMod = ((<B><FONT COLOR="#A020F0">long</FONT></B>)(file.lastModified() / 1000L)) * 1000L;
            mCanRead = file.canRead();
            <I><FONT COLOR="#B22222">//mLogger.debug(&quot;lm: &quot; + mLastMod);
</FONT></I>            mLength = file.length();
        }
        
        <I><FONT COLOR="#B22222">/** Returns true iff this FileInfo has up to date information
         * compared to the fileinfo argument.
         * @param info another FileInfo
         * @return see documentation
         */</FONT></I>
        <B><FONT COLOR="#A020F0">boolean</FONT></B> isUpToDate(FileInfo info) {
            <B><FONT COLOR="#A020F0">return</FONT></B> <B><FONT COLOR="#A020F0">this</FONT></B>.mLastMod == info.mLastMod
                &amp;&amp; <B><FONT COLOR="#A020F0">this</FONT></B>.mCanRead == info.mCanRead
                &amp;&amp; <B><FONT COLOR="#A020F0">this</FONT></B>.mLength == info.mLength;
        }
    };

    <I><FONT COLOR="#B22222">/** A list of FileInfo records for files that are depended on. */</FONT></I>
    <B><FONT COLOR="#A020F0">private</FONT></B> <B><FONT COLOR="#A020F0">final</FONT></B> Vector mDependencies = <B><FONT COLOR="#A020F0">new</FONT></B> Vector();
    <B><FONT COLOR="#A020F0">private</FONT></B> Properties mProperties;
    <B><FONT COLOR="#A020F0">private</FONT></B> String mWebappPath;

    DependencyTracker(Properties properties) {
        <B><FONT COLOR="#A020F0">this</FONT></B>.mProperties = properties;
        <B><FONT COLOR="#A020F0">this</FONT></B>.mWebappPath = LPS.HOME(); <I><FONT COLOR="#B22222">// get it from global
</FONT></I>    }
    
    <I><FONT COLOR="#B22222">/** Add the specified file to the list of file dependencies.
     * @param file a file
     */</FONT></I>
    <B><FONT COLOR="#A020F0">void</FONT></B> addFile(File file) {
        mLogger.debug(<FONT COLOR="#BC8F8F"><B>&quot;addFile Path is &quot;</FONT></B> + file.getPath());
        FileInfo fi = <B><FONT COLOR="#A020F0">new</FONT></B> FileInfo(file.getPath());
        <B><FONT COLOR="#A020F0">try</FONT></B> {
            fi.mPathname = file.getCanonicalPath();
        } <B><FONT COLOR="#A020F0">catch</FONT></B> (java.io.IOException e) {
            <B><FONT COLOR="#A020F0">throw</FONT></B> <B><FONT COLOR="#A020F0">new</FONT></B> ChainedException(e);
        }
        mDependencies.add(fi);
    }


    <I><FONT COLOR="#B22222">/**
     * Copy file info from the given tracker to me, omitting
     * omitting then given file.
     */</FONT></I>
    <B><FONT COLOR="#A020F0">void</FONT></B> copyFiles(DependencyTracker t, File omitMe) {
        <B><FONT COLOR="#A020F0">try</FONT></B> {
            <B><FONT COLOR="#A020F0">for</FONT></B> (Iterator e = t.mDependencies.iterator(); e.hasNext(); ) {
                FileInfo f = (FileInfo)e.next();
                <B><FONT COLOR="#A020F0">if</FONT></B> (! f.mPathname.equals(omitMe.getCanonicalPath())) {
                    mDependencies.add(f);
                }
            }
        } <B><FONT COLOR="#A020F0">catch</FONT></B> (java.io.IOException e) {
            <B><FONT COLOR="#A020F0">throw</FONT></B> <B><FONT COLOR="#A020F0">new</FONT></B> ChainedException(e);
        }
    }


    <I><FONT COLOR="#B22222">/** This will update the FileInfo object chain to use the (possibly new)
     * webappPath once the DependencyTracker object has been reconstitutded
     * from ondisk cache.
     */</FONT></I>
    <B><FONT COLOR="#A020F0">void</FONT></B> updateWebappPath() {
        String webappPath = LPS.HOME(); <I><FONT COLOR="#B22222">// get it from global
</FONT></I>        <B><FONT COLOR="#A020F0">if</FONT></B> (webappPath.equals(mWebappPath))
            <B><FONT COLOR="#A020F0">return</FONT></B>;
        mLogger.debug(
<I><FONT COLOR="#B22222">/* (non-Javadoc)
 * @i18n.test
 * @org-mes=&quot;updating webappPath from: &quot; + p[0]
 */</FONT></I>
			org.openlaszlo.i18n.LaszloMessages.getMessage(
				DependencyTracker.<B><FONT COLOR="#A020F0">class</FONT></B>.getName(),<FONT COLOR="#BC8F8F"><B>&quot;051018-128&quot;</FONT></B>, <B><FONT COLOR="#A020F0">new</FONT></B> Object[] {mWebappPath})
);
        mLogger.debug(
<I><FONT COLOR="#B22222">/* (non-Javadoc)
 * @i18n.test
 * @org-mes=&quot;updating webappPath to:   &quot; + p[0]
 */</FONT></I>
			org.openlaszlo.i18n.LaszloMessages.getMessage(
				DependencyTracker.<B><FONT COLOR="#A020F0">class</FONT></B>.getName(),<FONT COLOR="#BC8F8F"><B>&quot;051018-136&quot;</FONT></B>, <B><FONT COLOR="#A020F0">new</FONT></B> Object[] {webappPath})
);
        <B><FONT COLOR="#A020F0">for</FONT></B> (Iterator e = mDependencies.iterator(); e.hasNext(); ) {
            FileInfo saved = (FileInfo) e.next();
            <B><FONT COLOR="#A020F0">if</FONT></B> (saved.mPathname.startsWith(mWebappPath)) {
                mLogger.debug(
<I><FONT COLOR="#B22222">/* (non-Javadoc)
 * @i18n.test
 * @org-mes=&quot;updating dependencies from: &quot; + p[0]
 */</FONT></I>
			org.openlaszlo.i18n.LaszloMessages.getMessage(
				DependencyTracker.<B><FONT COLOR="#A020F0">class</FONT></B>.getName(),<FONT COLOR="#BC8F8F"><B>&quot;051018-147&quot;</FONT></B>, <B><FONT COLOR="#A020F0">new</FONT></B> Object[] {saved.mPathname})
);
                saved.mPathname = webappPath +
                        saved.mPathname.substring(mWebappPath.length());
                mLogger.debug(
<I><FONT COLOR="#B22222">/* (non-Javadoc)
 * @i18n.test
 * @org-mes=&quot;updating dependencies to  : &quot; + p[0]
 */</FONT></I>
			org.openlaszlo.i18n.LaszloMessages.getMessage(
				DependencyTracker.<B><FONT COLOR="#A020F0">class</FONT></B>.getName(),<FONT COLOR="#BC8F8F"><B>&quot;051018-157&quot;</FONT></B>, <B><FONT COLOR="#A020F0">new</FONT></B> Object[] {saved.mPathname})
);
            }
        }
        mWebappPath = webappPath;
    }

    <I><FONT COLOR="#B22222">/** Returns true iff all the files listed in this tracker's
     * dependency list exist and are at the same version as when they
     * were recorded.
     * @return a boolean
     */</FONT></I>
    <B><FONT COLOR="#A020F0">boolean</FONT></B> isUpToDate(Properties properties) {
        Iterator e;
        
        <I><FONT COLOR="#B22222">// fixes bug 962 
</FONT></I>        {
            <B><FONT COLOR="#A020F0">if</FONT></B> (mProperties.size() != properties.size()) {
                mLogger.debug(
<I><FONT COLOR="#B22222">/* (non-Javadoc)
 * @i18n.test
 * @org-mes=&quot;my size &quot; + p[0]
 */</FONT></I>
			org.openlaszlo.i18n.LaszloMessages.getMessage(
				DependencyTracker.<B><FONT COLOR="#A020F0">class</FONT></B>.getName(),<FONT COLOR="#BC8F8F"><B>&quot;051018-181&quot;</FONT></B>, <B><FONT COLOR="#A020F0">new</FONT></B> Object[] {<B><FONT COLOR="#A020F0">new</FONT></B> Integer(mProperties.size())})
);
                mLogger.debug(
<I><FONT COLOR="#B22222">/* (non-Javadoc)
 * @i18n.test
 * @org-mes=&quot;new size &quot; + p[0]
 */</FONT></I>
			org.openlaszlo.i18n.LaszloMessages.getMessage(
				DependencyTracker.<B><FONT COLOR="#A020F0">class</FONT></B>.getName(),<FONT COLOR="#BC8F8F"><B>&quot;051018-189&quot;</FONT></B>, <B><FONT COLOR="#A020F0">new</FONT></B> Object[] {<B><FONT COLOR="#A020F0">new</FONT></B> Integer(properties.size())})
);
                <B><FONT COLOR="#A020F0">return</FONT></B> <B><FONT COLOR="#A020F0">false</FONT></B>;
            }

            <B><FONT COLOR="#A020F0">for</FONT></B> (e = mProperties.keySet().iterator(); e.hasNext(); ) {
                String key = (String) e.next();
                String val0 = mProperties.getProperty(key);
                String val1 = properties.getProperty(key);

                <I><FONT COLOR="#B22222">// val0 can't be null; properties don't allow that
</FONT></I>                <B><FONT COLOR="#A020F0">if</FONT></B> (val1 == <B><FONT COLOR="#A020F0">null</FONT></B> || ! val0.equals(val1)) {
                    mLogger.debug(
<I><FONT COLOR="#B22222">/* (non-Javadoc)
 * @i18n.test
 * @org-mes=&quot;Missing or changed property: &quot; + p[0]
 */</FONT></I>
			org.openlaszlo.i18n.LaszloMessages.getMessage(
				DependencyTracker.<B><FONT COLOR="#A020F0">class</FONT></B>.getName(),<FONT COLOR="#BC8F8F"><B>&quot;051018-207&quot;</FONT></B>, <B><FONT COLOR="#A020F0">new</FONT></B> Object[] {val0})
);
                    <B><FONT COLOR="#A020F0">return</FONT></B> <B><FONT COLOR="#A020F0">false</FONT></B>;
                }
            }
        }

        <B><FONT COLOR="#A020F0">for</FONT></B> (e = mDependencies.iterator(); e.hasNext(); ) {
            FileInfo saved = (FileInfo) e.next();
            FileInfo current = <B><FONT COLOR="#A020F0">new</FONT></B> FileInfo(saved.mPathname);
            <B><FONT COLOR="#A020F0">if</FONT></B> (!saved.isUpToDate(current)) {
                mLogger.debug(saved.mPathname + <FONT COLOR="#BC8F8F"><B>&quot; has changed&quot;</FONT></B>);
                mLogger.debug(<FONT COLOR="#BC8F8F"><B>&quot;was &quot;</FONT></B> + saved.mLastMod);
                mLogger.debug(<FONT COLOR="#BC8F8F"><B>&quot; is &quot;</FONT></B> + current.mLastMod);
                <B><FONT COLOR="#A020F0">return</FONT></B> <B><FONT COLOR="#A020F0">false</FONT></B>;
            }
        }
        <B><FONT COLOR="#A020F0">return</FONT></B> <B><FONT COLOR="#A020F0">true</FONT></B>;
    }
}
</PRE>
<HR>
<ADDRESS>Generated by <A HREF="http://www.iki.fi/~mtr/genscript/">GNU enscript 1.6.1</A>.</ADDRESS>
</BODY>
</HTML>

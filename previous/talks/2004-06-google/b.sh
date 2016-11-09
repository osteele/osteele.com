#! /bin/bash -f
if [ "${OS}" == Windows_NT ];  then
    . ${LPS_HOME}\\WEB-INF\\lps\\server\\bin\\lzenv
else
    . ${LPS_HOME}/WEB-INF/lps/server/bin/lzenv
fi

java -Dpython.home="$JYTHON_HOME" -DLPS_HOME="$LPS_HOME" -classpath "$LZCP" org.python.util.jython xslt.py $*
cp *.sh *.lzx c:/laszlo/lps-doc/google

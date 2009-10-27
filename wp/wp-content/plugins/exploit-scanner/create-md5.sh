#!/bin/sh

# This simple bash script creates a file containing the md5sums of 
# all the files in the current directory and sub directories.
# Copyright Donncha O Caoimh, http://ocaoimh.ie/

rm -f /tmp/md5.txt; 
echo '<?php' > /tmp/md5.txt
echo '$filehashes = array( ' >> /tmp/md5.txt
for i in `find $1 -type f`; 
do 
	export filename=`echo $i|sed "s/$1\///"`
	/bin/echo -n "'$filename' => '" >> /tmp/md5.txt; 
	export m=`cat $i | md5sum|awk '{print $1}'`
	echo "$m'," >> /tmp/md5.txt
	echo $i done; 
done
echo ");\n?>" >> /tmp/md5.txt

#!/bin/bash

LATEXRENDER_URL='http://sixthform.info/steve/wordpress/wp-content/uploads/wp-latexrender.zip'
LATEXRENDER_ZIPFILE='wp-latexrender.zip'
BLOG_URL_BASE=''
PLUGINS_REL_PATH='wp-content/plugins'
USE_OFFSET_BETA=1
LATEXRENDER_DIRNAME="latexrender"

if [[ -x "`which latex`" && -x "`which dvips`" ]]
then
	echo "Commands latex and dvips are present."
else
	echo "Error: Commands latex and dvips not found. You need to install latex."
	exit 1
fi

if [[ -x "`which convert`" && -x "`which identify`" ]]
then
	echo "Commands convert and identify are present."
else
	echo "Error: Commands convert and identify not found. You need to install ImageMagick."
	exit 1
fi

if [[ "$1" != "" ]]
then
	BLOG_URL_BASE=$1
fi

if [[ "${PWD: -${#PLUGINS_REL_PATH}}" == "${PLUGINS_REL_PATH}" ]]
then
	echo "Current directory seems correct."
else
	echo "Error: Current directory should end in ${PLUGINS_REL_PATH}. Please run me in the right place. Exiting."
	exit 1
fi

if [[ -e "${LATEXRENDER_DIRNAME}" ]]
then
	echo "Error: Directory or file ${LATEXRENDER_DIRNAME} already exists. Exiting.";
	exit 1
fi

echo "Installing with BLOG_URL_BASE '${BLOG_URL_BASE}'"

if [[ -e "${LATEXRENDER_ZIPFILE}" ]]
then
	echo "${LATEXRENDER_ZIPFILE} already present.";
else
	wget "${LATEXRENDER_URL}"
fi

echo "Installing into directory ${LATEXRENDER_DIRNAME}"

mkdir "${LATEXRENDER_DIRNAME}"
cd    "${LATEXRENDER_DIRNAME}"
unzip -q "../${LATEXRENDER_ZIPFILE}"

if [[ ${USE_OFFSET_BETA} -eq '1' ]]
then
	echo "Installing with beta vertical-offset-tweaking functionality"
	cp "offset beta"/*.php .
else
	echo "Skipping beta vertical-offset-tweaking functionality"
fi

echo "Setting up correct php file paths and HTTP virtual paths"

sed -i "s#include_once('/home/path_to/wordpress/latexrender#include_once('`pwd`#g" latexrender-plugin.php
sed -i 's#$latexrender_path = "/home/domain_name/public_html/latexrender#$latexrender_path = "'"`pwd`#g" latex.php
sed -i 's#$latexrender_path_http = "/latexrender#$latexrender_path_http = "'"${BLOG_URL_BASE}/${PLUGINS_REL_PATH}/${LATEXRENDER_DIRNAME}#g" latex.php
sed -i 's#var $_latex_path = "/usr/bin/latex#var $_latex_path = "'"`which latex`#g" class.latexrender.php

echo "Setting up correct paths to tex commands and ImageMagick commands"

for cmd in latex dvips convert identify
do
	sed -i 's#var $_'$cmd'_path = "/usr/bin/'$cmd'#var $_'$cmd'_path = "'"`which $cmd`#g" class.latexrender.php
done

echo "Correcting a bug in the offset beta code"

sed -i "s#'.\$style#' \$style#g" latex.php

echo "Making tmp and pictures paths writable"

chmod 777 tmp pictures

echo "Plugin setup complete."

echo "Now go to WP admin, Plugins panel, and activate the plugin LatexRender."


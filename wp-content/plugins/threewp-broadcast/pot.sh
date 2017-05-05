#!/bin/bash

source pot.conf

# Collect a list of all PHP files into this array.
PHPFILES=""

# Find all PHP files
for file in `find ./ -type f -name "*php"` ; do
	# Ignore those we are supposed to ignore.
	match=$( echo $file | grep -E ${IGNORE} )
	if [ "$match" == "" ]; then
		echo Will parse $file
		PHPFILES="$PHPFILES $file"
	fi
done

xgettext -s -c --no-wrap -d ${POT_DOMAIN} -k__ -o "$POT" --omit-header $PHPFILES

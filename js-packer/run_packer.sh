#!/bin/sh

for f in js/*.js
do
	echo Processing $f:
	java -jar packer/compiler.jar --js ../socialnet/js/$f --js_output_file js/$f
done

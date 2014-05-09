@echo off
setlocal enabledelayedexpansion

for %%F in (js/*.js) do call :compress_jar %%F
goto :eof

:compress_jar
	echo Compress %1
	set "output=../root/socialnet/js/%1"
	set name_min=!output:.js=.min.js!
	java -jar closure/compiler.jar --js js/%1 --js_output_file %name_min% --compilation_level SIMPLE_OPTIMIZATIONS
goto :eof

:eof


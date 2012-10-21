@ECHO OFF

for %%F in (js/*.js) do call :compress_jar %%F
goto :eof

:compress_jar
	echo Compress %1
	java -jar packer/compiler.jar --js ../root/socialnet/js/%1 --js_output_file js/%1
goto :eof

:eof
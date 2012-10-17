@echo off

echo This script merges develop branch to
echo master branch and prepares master to
echo be oficially used.
echo.

:: really continue?
:continue
set /p continue=Continue? [y/n]

:: if we do not want to continue, we goto end of file
if %continue% equ n (
	goto eof
)

:: if we specified something else than "n" or "y", we return to :continue
if %continue% neq y (
	goto continue
)

:: checkout to develop
echo.
echo git checkout develop
git checkout develop

:: pull from upstream
echo.
echo git pull https://github.com/phpBB-Social-Network/phpBB-Social-Network.git develop
git pull https://github.com/phpBB-Social-Network/phpBB-Social-Network.git develop

::
:: We just want to copy develop to master and (re)move some files.
::
:: now we are going to create master branch by cloning from develop branch
::

:: delete master
echo.
echo git branch -D master
git branch -D master

:: create new master (clones develop)
echo.
echo git checkout -b master
git checkout -b master


::
:: remove unnecessary files
::

echo.
echo Removing unnecessary files:
echo - /sn-theme/
echo - /git-tools/
echo - /rebuild_gitignore.php
echo.
echo You will need to confirm deletion

rmdir /S ..\sn-theme
rmdir /S ..\git-tools
del ..\rebuild_gitignore.php


::
:: replace content of README.md
::

echo.
echo Replacing content of README.md

type README.md.orig > ..\README.md


::
:: move install_mod.xml from /install_instructions/ to /
::

echo.
echo Moving install_mod.xml to root directory

move ..\install_instructions\install_mod.xml ..\


echo.
echo ........................
echo Merging was successfull!
echo ........................
echo.
echo Everything was proceeded successfully. Congratulations!
echo.
echo Now please delete this file and it's directory,
echo commit changes and push it to master branch.
echo You are free to rock ;)

pause

:eof
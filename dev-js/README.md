# Development JavaScript files
Development files for [phpBB Social Network](http://phpbbsocialnetwork.com/)

All files should be minifyed for shorter load time and load size.

## Folder structure
```
cache/    		<= Cache folder
closure/		<= Contains necessary files for online/offline minify
hooks/			<= Contains phpBB hook
js/		        <= Contains development JavaScript files
```

## How it works
Script is loaded each time when phpBB page is loaded.
Check development files if they been changed and minify changed files.

## Install
This is written as standard [phpBB Hook](https://www.phpbb.com/community/docs/hook_system.html).

Simply copy file ```hooks/hook_dev-js.php``` to ```includes/hooks/hook_dev-js.php``` and [clear phpBB cache](https://www.phpbb.com/kb/article/purging-the-phpbb-cache/).

## Online Compiler
For allowing online Closure compiler should be in file ```closureapi.php``` at lines 10-20
```php
/**
* You can use online Closure compiler
* @problem Count compiled files is limited
*/
require( 'closure/closure_online.php');

/**
* You can use offline Closure compiler
* @require java and PHP exec command
*/
// require( 'closure/closure_offline.php');
```
### Drawbacks
Limited count request in time interval
## Offline Compiler
For allowing offline Closure compiler should be in file ```closureapi.php``` at lines 10-20
```php
/**
* You can use online Closure compiler
* @problem Count compiled files is limited
*/
// require( 'closure/closure_online.php');

/**
* You can use offline Closure compiler
* @require java and PHP exec command
*/
require( 'closure/closure_offline.php');
```
### Require
Offline Closure compiler require to download Java jar file and put it in ```dev-js/closure``` folder

[[Download link for latest](http://closure-compiler.googlecode.com/files/compiler-latest.zip)] [[Download link for all](https://code.google.com/p/closure-compiler/downloads/list)]
### Drawbacks
Require installed [Java](http://www.oracle.com/technetwork/java/javase/downloads/) and allowed [php exec](http://php.net/manual/en/function.exec.php).
### Debugging
For debugging/developing use [unpack JavaScript plugin](http://jsbeautifier.org/) for browsers.
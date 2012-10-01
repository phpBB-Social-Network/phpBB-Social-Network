## phpBB Social Network - Theme Compiler

This is the Theme compiler for any style for phpBB Social Network

#### How To Install

1. Copy file from <code>hooks</code> folder to <code>/root/includes/hooks</code>
2. Add file <code>/root/includes/hooks/hook_sn-theme.php</code> to <code>.gitignore</code>
3. Purge phpBB cache or delete file <cache>/root/cache/data_hooks.php</code>.

#### How To Use 

Folders like <code>prosilver</code> contains less files, that are compiled after you edit these files.

Normaly use your favorite CSS editor to edit <code>*.less</code>.

Than you load any page on SN site where is hook installed.

#### Output file
Output file is <code>/root/styles/prosilver/theme/socialnet.css</code>. Where prosilver is your actuall style on the board.

####  Structure

```
sn-theme/
	hooks/
		/hook_sn_theme.php
	lessphp/
	prosilver/
```

#### `sn-theme/hooks`
contains phpBB related stuff. Normal phpBB hook, that compile the theme for SN.

#### `sn-theme/lessphp`
contains [lessphp](http://leafo.net/lessphp) and [CSSTidy](http://csstidy.sourceforge.net/download.php) optimizer related stuff.
These files are reqiured to compile the style.

#### `sn-theme/prosilver`
contains [lessphp](http://leafo.net/lessphp) files that are parsed to output file.

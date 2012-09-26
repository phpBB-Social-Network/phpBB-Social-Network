## phpBB Social Network - Generating the style

### How To edit
Edit files in <code>socialnet_css</code> folder.

### Structure
```
includes/
	hooks/
styles/
	prosilver/
		theme/
			socialnet_css/
```
Folder <pre>hooks</pre> contains PHP files that compile <code>socialnet.css</code> file from <code>styles/<style folder>/theme/socialnet_css/</code>

### How To Install
Copy ./* to root folder of your installation of phpBB and purge hooks cache.
Style compile is loaded trough hooks.
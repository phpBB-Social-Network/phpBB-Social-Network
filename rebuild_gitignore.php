<?php

$filenames = array();

echo "Starting to collect files...\n";
foreach (glob_recursive('phpBB3/*', 0 , true) as $filename)
{
	$filenames[] = str_replace('phpBB3/', 'root/', $filename);
}

echo "Starting to collect .htaccess files...\n";
foreach (glob_recursive('phpBB3/.*') as $filename)
{
	if (!preg_match('/\.htaccess$/', $filename))
	{
		continue;
	}
	$filenames[] = str_replace('phpBB3/', 'root/', $filename);
}

echo "Adding non-phpBB related files into .gitignore...\n";
$data = "*~

# phpBB #
######################
phpBB3/

# OS generated files #
######################
.DS_Store
.DS_Store?
._*
.Spotlight-V100
.Trashes
Icon?
ehthumbs.db
Thumbs.db

# phpBB files #
######################
root/cache/\n";

echo "Adding phpBB files into .gitignore...\n";
$data .= implode("\n", $filenames);

echo "Writing file...\n";
file_put_contents('.gitignore', $data);

echo "Done!\n";


function glob_recursive($pattern, $flags = 0, $skipdirs = false)
{
	// do not include cache directory
	if (dirname($pattern) != 'phpBB3/cache')
	{
		$files = glob($pattern, $flags);
	}
	else
	{
		$files = array();
	}

	// search for directories
	foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
	{
		if ($skipdirs)
		{
			// now delete directories from the list
			unset( $files[array_search($dir, $files)] );
		}

		$files = array_merge($files, glob_recursive($dir . '/' . basename($pattern), $flags, $skipdirs));
	}

	return $files;
}


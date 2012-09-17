## phpBB Social Network - developer's branch

### How to develop effectivelly
In order to develop phpBB SN, you need working phpBB installation. However we cannot include full phpBB installation in phpBB SN repository. Thus, we have created some scripts to ensure workflow is fast, and does not pollute Github repository.

#### Structure
You will need to set up you local repo in this way:
```
git-tools/
   hooks/
      install
      post-merge
      uninstall
install_instructions/
phpBB3/
   adm/
   cache/
   ...
root/
   adm/
   cache/
   ...
   socialnet/
      ...
      update_database_dev.php
      ...
   ...
   activitypage.php
   mainpage.php
   ...
.gitignore
license.txt
README.md
rebuild_gitignore.php
```
let me explain it...

#### `git-tools/`
contains git-related stuff. In hooks, you can find various hooks used by phpBB SN. For now, it only includes post-merge hook, to automatically update database to latest version. You can install all hooks simply by running `install` in your command line with path set to /git-tools/hooks/.

#### `.gitignore`
As per we want running and working phpBB installation with phpBB SN installed, and, in the same time, availability to push changes to repository, we need to cover all phpBB files in `.gitignore`. This would, hovewer, be loooooong job to write them all manually. So we created script to build it automatically...

#### `rebuild_gitignore.php`
I know, long and stupid filename :). Just do not stop using it just because of name ;). This script, even though it is called "rebuild", can also create `.gitignore` file. Just run it in your command line and your `.gitignore` file will be filled with all files found in... `phpBB3/` directory. Unexpected? Read next.

#### `phpBB3/`
This directory contains only vanilla, not installed latest version of phpBB. Thanks to perfect installation process of phpBB (it does not create any new file), you do not need to do more. `rebuild_gitignore.php` script takes this directory as a source of phpBB files that will be listed in `.gitignore`. So do not forget that phpBB in `phpBB3/` directory must be of the same version as the one installed in `root/` directory.

#### `root/socialnet/update_database_dev.php`
I know you have to say this is another nice, easy-to-remember and short filename. But do not care, you will get use to it ;). Or at least to point we will release git hooks that will cover running it. Until that, you are free to run it always you `pull` from `upstream` repository, becuse this script updates database to newest developer version.

### Celebrate!
Now you should fully understand basic developers filesystem of phpBB Social Network. You are free to post new pull request and enjoy watching your code being used by thousands of phpBB boards ;).
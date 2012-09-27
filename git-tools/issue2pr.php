<?php
/**
 *
 * @package phpBB Social Network
 * @version 0.7.0
 * @copyright (c) phpBB Social Network Team 2010-2012 http://phpbbsocialnetwork.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

/*
 * This file helps you to change issue on github to pull request.
 * It is not possible to merge issue and pull request already created,
 * so you need to push your changes to your fork and specify all data
 * needed to create new pull request. Note that:
 *    - branch name is fetched from local repo and its branch currently
 *      set, so if you want to create pull request from another branch,
 *      you need to specify it.
 *    - pull request will be created always against "develop" branch.
 */

$username = $_GET['username'];
$password = $_GET['password'];
$issue_id = $_GET['issue_id'];
$branch = $_GET['branch'];

if ( isset($_GET['username'], $_GET['password'], $_GET['issue_id'], $_GET['branch']) && $_GET['username'] != '' && $_GET['password'] != '' && $_GET['issue_id'] != '' && $_GET['branch'] != '' )
{
	$result = false;

	$result = do_post_request(
		"https://$username:$password@api.github.com/repos/phpBB-Social-Network/phpBB-Social-Network/pulls",
		'{
				"issue": "' . $issue_id . '",
				"head": "' . $username . ':' . $branch . '",
				"base": "develop"
		}'
	);

	if ($result != false)
	{
		$done_msg = 'Issue <a href="https://github.com/phpBB-Social-Network/phpBB-Social-Network/issues/' . $issue_id . '">#' . $issue_id . '</a> has been converted to pull request successfully!';
	}
}

?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />

		<title>Issue to pull request converter</title>
	</head>

	<body>

		<?php if ($done_msg) echo '<p class="success">' . $done_msg . '</p>'; ?>

		<form action="" method="GET">
			Github username: <input type="text" name="username" placeholder="Github username" value="<?php echo `git config user.name`; ?>" /><br />
			Github password: <input type="password" name="password" placeholder="Github password" /><br />
			Issue ID: <input type="text" name="issue_id" placeholder="Issue ID" /><br />
			Branch: <input type="text" name="branch" placeholder="Branch" value="<?php echo get_branch_name(); ?>" /><br />
			<input type="submit" />
		</form>

	</body>
</html>
<?php

/**
 * sends POST request
 *
 * @param  string $url		url to connect
 * @param  string $data		POST data
 *
 * @return mixed					response from remote url
 */
function do_post_request($url, $data)
{
	$params = array('http' => array(
		'method' => 'POST',
		'content' => $data
	));

	$ctx = stream_context_create($params);
	$fp = @fopen($url, 'rb', false, $ctx);

	if (!$fp)
	{
		throw new Exception("Problem with $url, $php_errormsg");
	}

	$response = @stream_get_contents($fp);

	if ($response === false)
	{
		throw new Exception("Problem reading data from $url, $php_errormsg");
	}

	return $response;
}

/**
 * gets branch user is currently on
 *
 * @return string		name of branch user is currently on
 */
function get_branch_name()
{
	$head = file_get_contents('../.git/HEAD');
	$head = explode('/', $head);

	unset($head[0], $head[1]);

	return implode('/', $head);
}

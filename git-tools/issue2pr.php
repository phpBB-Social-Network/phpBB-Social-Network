<?php

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
			Branch: <input type="text" name="branch" placeholder="Branch" value="<?php echo get_branch_name(); ?>" />
		</form>

	</body>
</html>
<?php

function do_post_request($url, $data, $optional_headers = null)
{
	$params = array('http' => array(
		'method' => 'POST',
		'content' => $data
	));

	if ($optional_headers !== null)
	{
		$params['http']['header'] = $optional_headers;
	}

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

function get_branch_name()
{
	$head = file_get_contents('../.git/HEAD');
	$head = explode('/', $head);
	return $head[2];
}

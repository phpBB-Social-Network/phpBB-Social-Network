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

$username = @$_GET['username'];
$password = @$_GET['password'];
$issue_id = @$_GET['issue_id'];
$branch = @$_GET['branch'];

$result = false;

if ( isset($_GET['username'], $_GET['password'], $_GET['issue_id'], $_GET['branch']) && $_GET['username'] != '' && $_GET['password'] != '' && $_GET['issue_id'] != '' && $_GET['branch'] != '' )
{

	$result = do_post_request(
		"https://api.github.com/repos/phpBB-Social-Network/phpBB-Social-Network/pulls",
		'{
				"issue": "' . $issue_id . '",
				"head": "' . $username . ':' . $branch . '",
				"base": "develop"
		}',
		$username, $password
	);

}
$is_error = false;
if ( $result != false)
{
	$is_error = $result->code > 300;
}

?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />

		<title>Issue to pull request converter</title>
		<script src="http://code.jquery.com/jquery-1.8.2.js"></script>
		<script src="http://code.jquery.com/ui/1.9.0/jquery-ui.js"></script>
		
		<link href="http://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css" rel="stylesheet" type="text/css" />
		<script>
			jQuery(document).ready(function($){
				
				$('#show-create').click(function(){
					$('#postdialog').dialog({width:444,height:185,resizable:false});
				}).trigger('click');
				
				$('.toolbar').buttonset();
	
			});
		</script>
		<style>
			html, body {
				font-size: 0.85em;
				background-color: #ececec;
			}
			.toolbar {
				position:absolute;
				bottom:0;
				line-height: 20px;
				padding: 4px 8px;
				left: 0;
				right: 0;
				background-color: #dcdcfc;
				border-top: 1px solid #1f1f1f;
			}
		</style>
	</head>

	<body>
<? if ( $result ){ ?>
<div id="result" title="GitHub API Result" class="ui-widget"><div class="ui-state-highlight <? if( $is_error) ?>ui-state-error<? ; ?> ui-corner-all" style="padding: 0 .7em"><p><span class="ui-icon ui-icon-info <? if( $is_error) ?>ui-icon-alert<? ; ?>" style="float: left"></span><?
		print "<strong>" .(( $is_error)? "Alert": "Success") . ":</strong><br />";
		print $result->message; 
		?></p></div></div><? }; ?>
		<div id="postdialog" title="Pull request for Issue">
			<form action="#" method="get">
				<table style="width:100%">
					<tr>
						<th style="width:35%;text-align:right">Github username:</th>
						<td><input type="text" name="username" placeholder="Github username" value="<?php echo `git config user.name`; ?>" style="width:100%" /></td>
					</tr>
					<tr>
						<th style="width:35%;text-align:right">Github password:</th>
						<td><input type="password" name="password" placeholder="Github password" style="width:100%" /></td>
					</tr>
					<tr>
						<th style="width:35%;text-align:right">Issue ID:</th>
						<td><input type="text" name="issue_id" placeholder="Issue ID" style="width:100%" /></td>
					</tr>
					<tr>
						<th style="width:35%;text-align:right">Branch:</th>
						<td><input type="text" name="branch" placeholder="Branch" value="<?php echo @get_branch_name(); ?>" style="width:100%" /></td>
					</tr>
					
				</table>
				<input type="submit" style="float:right"/>
			</form>
		</div>
		
		<div class="toolbar">
			<button id="show-create">Create PR</button>
			<button id="reload" onclick="window.location='./issue2pr.php';">Reload</button>
		</div>
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
function do_post_request($url, $data, $username, $password)
{

	$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  

    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,1);
    curl_setopt($ch, CURLOPT_USERPWD, $username.":".$password);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);   
    $content = curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
	
	$return = json_decode($content);
	$return->code = $http_code;
	return $return;
}

/**
 * gets branch user is currently on
 *
 * @return string		name of branch user is currently on
 */
function get_branch_name()
{
	$head = @file_get_contents('../.git/HEAD');
	$head = explode('/', $head);
	return $head[2];
}

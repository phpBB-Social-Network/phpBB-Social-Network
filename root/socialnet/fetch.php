<?php
/**
 *
 * @package phpBB Social Network
 * @version 0.7.0
 * @copyright (c) phpBB Social Network Team 2010-2012 http://phpbbsocialnetwork.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

error_reporting(0);
include_once($phpbb_root_path . 'common.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions.' . $phpEx);
include_once($phpbb_root_path . 'socialnet/includes/video.' . $phpEx);

$url = request_var('url', '');
$url = trim($url);

if ($url == '')
{
	die('');
}

$url = strtr($url, array_flip(get_html_translation_table(HTML_ENTITIES)));

/**
 * Parse URL
 */
$urldata = parse_url($url);
if (!isset($urldata['path']))
{
	$urldata['path'] = '';
}
if (!isset($urldata['query']))
{
	$urldata['query'] = '';
}

/**
 * Fetch page data
 */

if ($urldata['host'] == 'www.youtube.com')
{
	$urlOrig = $url;
	$url = preg_replace('/feature=player_embedded&?/i', '', $url);
	list($string, $status) = get_remote_page($url, $urldata);

	if ($status != "200")
	{
		parse_str($urldata['query'], $query);

		$ytData['url'] = 'http://gdata.youtube.com/feeds/api/videos?max-results=1&q=' . $query['v'];

		list($string, $status) = get_remote_page($ytData['url'], parse_url($ytData['url']));
		$url = $urlOrig;
	}
}
else
{
	list($string, $status) = get_remote_page($url, $urldata);
}

if (empty($string))
{
	$status = 0;
}
/**
 * If fetch error
 */
$ytData = array();
if (preg_match("/^Couldn\'t resolve host/s", $string))
{
	$status = 502;
}

if ($status != "200")
{
	
	if ($status != 0)
	{
		$error = error_fetch($status);
	}
	else
	{
		$error = array('CURL error', $string);
	}
	die("<strong>{$status} {$error[0]}</strong><br />{$error[1]}");
}
/**
 * Parse type of page
 */
$sql = "SELECT g.group_name, e.extension
					FROM " . EXTENSION_GROUPS_TABLE . " AS g
						LEFT OUTER JOIN " . EXTENSIONS_TABLE . " AS e
							ON g.group_id = e.group_id";
$rs = $db->sql_query($sql, 3600);
$rowset = $db->sql_fetchrowset($rs);
$db->sql_freeresult($rs);

$exts = array();
$extensions = '';
for ($i = 0; isset($rowset[$i]); $i++)
{
	$exts[$rowset[$i]['group_name']][] = $rowset[$i]['extension'];
	$extensions .= '|' . $rowset[$i]['extension'];
}

$video_array = array(
	'info'		 => '',
	'provider'	 => '',
	'object'	 => '',
);

$is_file = preg_match('/\.(' . substr($extensions, 1) . ')$/i', $urldata['path']);

$url_desc = $url;
$url_title = $urldata["scheme"] . "://" . $urldata["host"];

$images_ary = array();
$video_array = array(
	'info'		 => '',
	'provider'	 => '',
	'object'	 => '',
);

if ($img = @imagecreatefromstring($string))
{
	/**
	 * Is it an image?
	 */
	$width = imagesx($img);
	$height = imagesy($img);
	imagedestroy($img);
	$images_ary[] = array('img' => $url, 'width' => $width, 'height' => $height, 'num' => 1);
}
else if (!empty($ytData))
{
	/**
	 * Youtube video
	 */
	// Title
	preg_match_all('/<media:title[^>]*>(.*)<\/media:title>/si', $string, $match);
	$url_title = html_entity_decode($match[1][0]);
	// DESC
	preg_match_all('/<media:description[^>]*>(.*)<\/media:description>/si', $string, $match);
	$url_desc = html_entity_decode($match[1][0]);

	preg_match_all('/<media:thumbnail url=\'([^\']*)\' height=\'([0-9]*)\' width=\'([0-9]*)\'[^>]*>/si', $string, $match);

	$images = array();

	for ($i = 0; isset($match[1][$i]); $i++)
	{
		$images[] = array('img' => $match[1][$i], 'width' => $match[3][$i], 'height' => $match[2][$i], 'num' => $i);
	}

	die(json_encode(array(
		'title'		 => trim($url_title),
		'url'		 => trim($url),
		'urlShort'	 => substr($url, 0, 60),
		'desc'		 => trim($url_desc),
		'images'	 => $images,
		'video'		 => $video_array,
	)));
}
else if (!$is_file)
{
	/**
	 * Is it file?
	 */
	preg_match('/<meta http-equiv="Content-Type" content="text\/html; charset=([^"]*)" ?\/?>/si', $string, $match);

	if (isset($match[1]) && $match[1] != 'UTF-8')
	{
		$c_encoding = $match[1];
	}
	else
	{
		$c_encoding = 'UTF-8';
	}

	/**
	 * Fetch page: title
	 */
	$title_regex = '/<title>(.*?)<\/title>/si';
	preg_match_all($title_regex, $string, $title, PREG_PATTERN_ORDER);

	$url_title = @$title[1][0];

	if (trim($url_title) == '')
	{
		$url_title = $urldata["scheme"] . "://" . $urldata["host"];
	}

	/**
	 * Fetch page: description
	 */
	$content = $string;
	$content = preg_replace("'<style[^>]*>.*</style>'siU", '', $content); // strip js
	$content = preg_replace("'<script[^>]*>.*</script>'siU", '', $content); // strip css
	$split = explode("\n", $content);
	$split_content = array();

	$user->add_lang('mods/socialnet');
	$url_desc = '';

	foreach ($split as $k => $v)
	{
		if (strpos(' ' . $v, '<meta'))
		{
			preg_match_all("/<meta[^>]+(http\-equiv|name)=\"([^\"]*)\"[^>]" . "+content=\"([^\"]*)\"[^>]*>/i", $v, $split_content, PREG_PATTERN_ORDER);
			if (isset($split_content[2][0]) && isset($split_content[3][0]) && $split_content[2][0] == 'description')
			{
				if ($split_content[3][0] != '')
				{
					$url_desc = $split_content[3][0];
				}
				break;
			}
		}
	}

	/**
	 * Fetch page: images
	 */
	$image_regex = '/(img|src)=("|\')[^"\'>]+/i';// '/<img[^>]*' . 'src=[\"|\'](.*)[\"|\']/Ui';
	preg_match_all($image_regex, $string, $img, PREG_PATTERN_ORDER);
	
	$images_array = preg_replace('/(img|src)("|\'|="|=\')(.*)/i',"$3",$img[0]);;

	/**
	 * Fetch page: videos
	 */
	$is_video = 0;
	$video_info = '';
	$video_provider = ' ';
	$video = '';
	$embevi = new EmbeVi();
	$embevi->__construct();

	if ($embevi->parseUrl($url))
	{
		$is_video = 1;

		$video_info = $embevi->getEmbeddedInfo();
		$video_provider = $embevi->getEmbeddedProvider();
		$video = $embevi->getCode();

		$video = html_entity_decode($video);

		$video_array = array(
			'info'		 => $video_info,
			'provider'	 => $video_provider[0],
			'object'	 => $video,
		);
	}

	if ($c_encoding != 'UTF-8')
	{
		$url_title = iconv($c_encoding, 'UTF-8', $url_title);
		$url_desc = iconv($c_encoding, 'UTF-8', $url_desc);
	}

	$j = 0;
	$images_ary = array();

	for ($i = 0; isset($images_array[$i]); $i++)
	{
		if (list($width, $height, $type, $attr) = @getimagesize($images_array[$i]))
		{

			if ($width >= 50 && $height >= 50)
			{
				//if ( strpos($images_array)
				$j++;
				$images_ary[] = array('img' => $images_array[$i], 'width' => $width, 'height' => $height, 'num' => $j);
			}
		}
	}
}
else
{
	/**
	 * It is a file
	 */
	$url_title = $url;
	$url_desc = '';
	$fileExt = substr(strrchr($url, '.'), 1);

	$images_ary = array();
	/*
	 * P ircure
	 */
	if (in_array($fileExt, $exts['IMAGES']))
	{
		list($width, $height, $type, $attr) = @getimagesize($url);
		$images_ary[] = array('img' => $url, 'width' => $width, 'height' => $height, 'num' => 1);
	}
}
die(json_encode(array(
	'title'		 => trim(htmlspecialchars_decode($url_title, ENT_QUOTES)),
	'url'		 => trim($url),
	'urlShort'	 => substr($url, 0, 60),
	'desc'		 => trim(htmlspecialchars_decode($url_desc, ENT_QUOTES)),
	'images'	 => $images_ary,
	'video'		 => $video_array,
)));

function get_remote_page($url, &$urldata)
{
	$useragent = $_SERVER['HTTP_USER_AGENT'];
	$headers = array(
		"Host: " . $urldata['host'],
		"User-Agent: " . $useragent,
		"Cache-Control: max-age=0",
		"Connection: keep-alive",
		"Keep-Alive: 300",
		"Pragma: no-cache",
	);

	$ch = curl_init();
	$curl_opts = array(
		CURLOPT_URL => $url,
		CURLOPT_USERAGENT => 'Googlebot/2.1 (+http://www.google.com/bot.html)', //"User-Agent: " . $useragent,
		CURLOPT_AUTOREFERER => true,
		CURLOPT_REFERER => 'http://www.google.com',
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HTTPHEADER => $headers,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_SSL_VERIFYHOST => false,
		CURLOPT_CONNECTTIMEOUT => 5,
		CURLOPT_TIMEOUT => 5,
		CURLOPT_HEADER => false,
		CURLOPT_BUFFERSIZE => 128,
	);

	$error_str = '';
	foreach ($curl_opts as $curl_opt => $curl_val)
	{
		@curl_setopt($ch, $curl_opt, $curl_val);
	}

	$string = curl_exec($ch);
	if (curl_errno($ch))
	{
		return array(curl_error($ch), 0);
	}

	$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	return array($string, $status);
}

function error_fetch($status)
{
	$status_reason = array(
		0 => 'Timeout',
		1 => 'Unsuported protocol',
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',
		226 => 'IM Used',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		306 => 'Reserved',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		422 => 'Unprocessable Entity',
		423 => 'Locked',
		424 => 'Failed Dependency',
		426 => 'Upgrade Required',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		506 => 'Variant Also Negotiates',
		507 => 'Insufficient Storage',
		510 => 'Not Extended'
	);

	$status_msg = array(
		0 => 'Read time out error',
		1 => '"HTTPS" protocol is unsupported<br />SSL is disabled<p>If you get this output when trying to get anything from a https:// server, it means that the instance of curl/libcurl that you\'re using was built without support for this protocol.<br />
					This could\'ve happened if the configure script that was run at build time couldn\'t find all libs and include files curl requires for SSL to work. If the configure script fails to find them, curl is simply built without SSL support.<br />
					To get the https:// support into a curl that was previously built but that reports that https:// is not supported, you should dig through the document and logs and check out why the configure script doesn\'t find the SSL libs and/or include files.<br />
					Also, check out the other paragraph in this FAQ labelled "configure doesn\'t find OpenSSL even when it is installed". </p>',
		400 => "Your browser sent a request that this server could not understand.",
		401 => "This server could not verify that you are authorized to access the document requested.",
		402 => 'The server encountered an internal error or misconfiguration and was unable to complete your request.',
		403 => "You don't have permission to access %U% on this server.",
		404 => "We couldn't find <acronym title='%U%'>that uri</acronym> on our server, though it's most certainly not your fault.",
		405 => "The requested method is not allowed for the URL %U%.",
		406 => "An appropriate representation of the requested resource %U% could not be found on this server.",
		407 => "An appropriate representation of the requested resource %U% could not be found on this server.",
		408 => "Server timeout waiting for the HTTP request from the client.",
		409 => 'The server encountered an internal error or misconfiguration and was unable to complete your request.',
		410 => "The requested resource %U% is no longer available on this server and there is no forwarding address. Please remove all references to this resource.",
		411 => "A request of the requested method GET requires a valid Content-length.",
		412 => "The precondition on the request for the URL %U% evaluated to false.",
		413 => "The requested resource %U% does not allow request data with GET requests, or the amount of data provided in the request exceeds the capacity limit.",
		414 => "The requested URL's length exceeds the capacity limit for this server.",
		415 => "The supplied request data is not in a format acceptable for processing by this resource.",
		416 => 'Requested Range Not Satisfiable',
		417 => "The expectation given in the Expect request-header field could not be met by this server. The client sent <code>Expect:</code>",
		422 => "The server understands the media type of the request entity, but was unable to process the contained instructions.",
		423 => "The requested resource is currently locked. The lock must be released or proper identification given before the method can be applied.",
		424 => "The method could not be performed on the resource because the requested action depended on another action and that other action failed.",
		425 => 'The server encountered an internal error or misconfiguration and was unable to complete your request.',
		426 => "The requested resource can only be retrieved using SSL. Either upgrade your client, or try requesting the page using https://",
		500 => 'The server encountered an internal error or misconfiguration and was unable to complete your request.',
		501 => "This type of request method to %U% is not supported.",
		502 => "The proxy server received an invalid response from an upstream server.",
		503 => "The server is temporarily unable to service your request due to maintenance downtime or capacity problems. Please try again later.",
		504 => "The proxy server did not receive a timely response from the upstream server.",
		505 => 'The server encountered an internal error or misconfiguration and was unable to complete your request.',
		506 => "A variant for the requested resource <code>%U%</code> is itself a negotiable resource. This indicates a configuration error.",
		507 => "The method could not be performed.  There is insufficient free space left in your storage allocation.",
		510 => "A mandatory extension policy in the request is not accepted by the server for this resource.",
	);
	return array(@$status_reason[$status], @$status_msg[$status]);
}

?>
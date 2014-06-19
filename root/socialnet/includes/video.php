<?php
/**
 *
 * @package phpBB Social Network
 * @version 0.7.0
 * @copyright (c) phpBB Social Network Team 2010-2012 http://phpbbsocialnetwork.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

/**
 * EmbeVi
 *
 * An open source tool for PHP 5 or newer
 *
 * @author    Comanici Paul <darkyndy@gmail.com>
 * @copyright Copyright (c) 2009, darkyndy
 * @license   http://www.gnu.org/licenses/
 * @link      http://www.embevi.com
 * @package   EmbeVi
 *
 */

 /**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

class EmbeVi{
  const VERSION = '1.3'; //EmbeVi version
  private $objectAttr = array(); //object attributes
  private $objectParam = array(); //object parameters
  private $embedAttr = array(); //parameters
  //HTML that will appear before the embedded code if you use parseText method
  private $beforeHtml = "<br/>";
  //HTML that will appear after the embedded code if you use parseText method
  private $afterHtml = "<br/>";
  //setting the same width to all embedded codes if $globalWidth is >0
  private $globalWidth = 0;
  //setting the same height to all embedded codes if $globalHeight is >0
  private $globalHeight = 0;
  //setting maximum width to all embedded codes if $globalWidth is >0
  private $globalMaxWidth = 0;
  //setting maximum height to all embedded codes if $globalHeight is >0
  private $globalMaxHeight = 0;
  //set maximum importance
  private $globalMaxImportant = false;
  //setting for keeping ratio
  private $keepRatio = false;
  //set embevi width
  private $embeviWidth = 0;
  //set embevi height
  private $embeviHeight = 0;
  //set default video width
  private $baseWidth = 0;
  //set default video height
  private $baseHeight = 0;
  //default ratio value
  private $ratio = 1;
  //accept shorten URLs (default is false)
  private $acceptShortUrl = false;
  //use function htmlspecialchars_decode for parseHtml method
  private $specialCharDecode = true;
  //site from where you get the embedded code
  private $embeviProvider = '';
  //EmbeVi info about embedded code
  private $embeviInfo = '';
  //list of short URLs services supported
  private $shortUrlServices = array(
    '2su.de/',
    '2.gp/',
    '2.ly/',
    '2ze.us/',
    '3.ly/',
    '301.to/',
    '9mp.com/',
    'a.gd/',
    'a.nf/',
    'abbr.com/',
    'bit.ly/',
    'bloat.me/',
    'buk.me/',
    'chilp.it/',
    'cli.gs/',
    'clk.my/',
    'coge.la/',
    'durl.me/',
    'fly2.ws/',
    'fon.gs/',
    'foxyurl.com/',
    'fwd4.me/',
    'good.ly/',
    'gurl.es/',
    'hao.jp/',
    'hex.io/',
    'hop.im/',
    'hurl.no/',
    'idek.net/',
    'is.gd/',
    'ir.pe/',
    'irt.me/',
    'j.mp/',
    'j2j.de/',
    'kissa.be/',
    'kl.am/',
    'kore.us/',
    'kots.nu/',
    'krz.ch/',
    'ktzr.us/',
    'lin.cr/',
    'l.pr/',
    'linxfix.de/',
    'linkee.com/',
    'lnk.by/',
    'lnk.ly/',
    'lnk.sk/',
    'lt.tl/',
    'lurl.no/',
    'metamark.net/',
    'migre.me/',
    'micurl.com/',
    'min2.me/',
    'minilink.org/',
    'lnk.nu/',
    'minurl.fr/',
    'moourl.com/',
    'myurl.in/',
    'nbx.ch/',
    'pendek.in/',
    'pic.gd/',
    'piko.me/',
    'piurl.com/',
    'pnt.me/',
    'poprl.com/',
    'pt2.me/',
    'puke.it/',
    'qr.cx/',
    'qurl.com/',
    'qux.in/',
    'r.im/',
    'rde.me/',
    'p.ly/',
    'redir.ec/',
    'ri.ms/',
    'rnk.me/',
    'rubyurl.com/',
    'sai.ly/',
    'sl.ly/',
    'sfu.ca/',
    'short.ie/',
    'short.to/',
    'shortn.me/',
    'shrtn.com/',
    'shw.me/',
    'siteo.us/',
    'smallr.net/',
    'smfu.in/',
    'snipie.com/',
    'snipurl.com/',
    'snkr.me/',
    'srnk.net/',
    'tighturl.com/',
    'timesurl.at/',
    'tini.us/',
    'tiny.cc/',
    'tiny.pl/',
    'tinyurl.com/',
    'to.ly/',
    'to.vg/',
    'tr.im/',
    'tsort.us/',
    'tweet.me/',
    'tweetburner.com/',
    'twip.us/',
    'twirl.at/',
    'u.nu/',
    'uiop.me/',
    'ur.ly/',
    'url.ag/',
    'url.ie/',
    'unfaker.it/',
    'urlborg.com/',
    'urlg.info/',
    'ooqx.com/',
    'u.mavrev.com/',
    'urlu.ms/',
    'urlzen.com/',
    'vb.ly/',
    'vl.am/',
    'vtc.es/',
    'xrt.me/',
    'xr.com/',
    'xrl.in/',
    'x.vu/',
    'xxsurl.de/',
    'z.pe/',
    'zi.pe/',
    'zipmyurl.com/',
    'zz.gd/'
  );

  /**
   * EmbeVi Constructor
   * Set default values for the object attributes, object param and embed attributes
   */
  public function __construct(){
    $pluginspage = 'http://get.adobe.com/flashplayer/';
    $allowScriptAccess = 'always';
    $wmode = 'transparent';
    $videoSrc = '';

    /**
     * Embed Attributes
     */
    $this->embedAttr = array(
		'type' => 'application/x-shockwave-flash',
		'src' => $videoSrc,
		'width' => $this->embeviWidth,
		'height' => $this->embeviHeight,
		'wmode' => $wmode,
		'allowScriptAccess' => $allowScriptAccess,
		'pluginspage' => $pluginspage,
		'flashvars' => '',
		'quality' => 'high',
		'allowfullscreen' => 'true',
		'loop' => 'false',
		'autoplay' => 'false',
		'autostart' => 'false',
		'scale' => 'exactfit',
		'align' => 'middle'
	);

    /**
     * Object Param
     */
    $this->objectParam = array(
      'movie' => $videoSrc,
      'wmode' => $wmode,
      'allowScriptAccess' => $allowScriptAccess,
      'pluginspage' => $pluginspage
    );

    /**
     * Object Attributes
     */
    $this->objectAttr = array(
      'classid' => 'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000',
      'codebase' => 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0',
      'type' => 'application/x-shockwave-flash',
      'width' => $this->embeviWidth,
      'height' => $this->embeviHeight
    );
  }

  /**
   * EmbeVi Support List (Array)
   * Example:
   * array(
   *   'provider'     => Direct link to site provider
   *   'info'         => Support information (site and support type)
   *   'width'        => Default width (for object and embed tag)
   *   'height'       => Default height (for object and embed tag)
   *   'src'          => Source of the media to embed. Replace ~to_replace1~, ~to_replace2~, ... with matches from the matchExpr regular expression
   *   'matchExpr'    => Regular expression for matching url
   *   'flashvars'    => (optional) if set, will be passed in the embed tag. Replace ~to_replace1~, ~to_replace2~, ... etc with matches from the matchExpr
   * )
   */
  private $embeviSupport = array(
    array(
      'provider' => array('http://www.youtube.com/'),
      'info' => 'YouTube playlist',
      'width' => 530,
      'height' => 370,
      'src' => 'http://www.youtube.com/p/~to_replace1~',
      'matchExpr' => 'youtube\.com\/watch(?:\?|#!)v=[a-z0-9-_]+&feature=PlayList&p=([a-z0-9-_]+)'
    ),
    array(
      'provider' => array('http://www.youtube.com/'),
      'info' => 'YouTube video',
      'width' => 425,
      'height' => 344,
      'src' => 'http://www.youtube.com/v/~to_replace2~&f=videos&app=youtube_gdata',
      'matchExpr' => 'youtube\.com\/(watch(?:\?|#!)v=|v\/|watch(?:\?|#!)v=[a-z0-9-_]+&feature=PlayList&p=)([a-z0-9-_]+)'
    ),
    array(
      'provider' => array('http://www.metacafe.com/'),
      'info' => 'Metacafe video',
      'width' => 400,
      'height' => 345,
      'src' => 'http://www.metacafe.com/fplayer/~to_replace1~/~to_replace2~.swf',
      'matchExpr' => 'metacafe\.com\/watch\/([a-z0-9-_]+)\/([a-z0-9-_]+)'
    ),
    array(
      'provider' => array('http://www.youtube.com/'),
      'info' => 'YouTube playlist',
      'width' => 530,
      'height' => 370,
      'src' => 'http://www.youtube.com/p/~to_replace1~',
      'matchExpr' => 'youtube\.com\/view_play_list\?p=([a-z0-9-_]+)'
    ),
    array(
      'provider' => array('http://vimeo.com/'),
      'info' => 'Vimeo video',
      'width' => 400,
      'height' => 255,
      'src' => 'http://vimeo.com/moogaloop.swf?clip_id=~to_replace1~&server=vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1',
      'matchExpr' => 'vimeo\.com\/(?:[^#]*#)?([0-9a-z-_]+)'
    )
  );

  /**
   * Set the height of the object and embed
   *
   * @param integer $height - height to set the object and embed
   *
   * @return boolean - true, if the value was set,
   *                 - false, if objectAttr and embedAttr aren't array's
   */
  public function setHeight($height){
    $height = intval($height);
    if($height <= 0){
      $height = $this->baseHeight;
    }
    $this->embeviHeight = $height;
    return $this->setObjectAttr('height', $height) && $this->setEmbedAttr('height', $height);
  }

  /**
   * Get height of the embedded code
   */
  public function getHeight(){
    return $this->embeviHeight;
  }

  /**
   * Set the global height of the object and embed
   *
   * @param integer $height - height to set the object and embed
   * - this will be used for all the embedded codes
   */
  public function setGlobalHeight($height){
    $this->globalHeight = intval($height);
  }

  /**
   * Unset the global height of the object and embed
   */
  public function unsetGlobalHeight(){
    $this->globalHeight = 0;
  }

  /**
   * Set maximum height of the object and embed
   *
   * @param integer $height - height to set the object and embed
   *
   * @return boolean - true, if the value was set,
   *                 - false, if objectAttr and embedAttr aren't array's
   */
  public function setGlobalMaxHeight($maxHeight){
    $this->globalMaxHeight = intval($maxHeight);
  }

  /**
   * Unset maximum global height of the object and embed
   */
  public function unsetGlobalMaxHeight(){
    $this->globalMaxHeight = 0;
  }

  /**
   * Set the width of the object and embed
   *
   * @param integer $width - width to set the object and embed
   * - this will be used for all the embedded codes
   *
   * @return boolean - true, if the value was set,
   *                 - false, if objectAttr and embedAttr aren't array's
   */
  public function setWidth($width){
    $width = intval($width);
    if($width <= 0){
      $width = $this->baseWidth;
    }
    $this->embeviWidth = $width;
    return $this->setObjectAttr('width', $width) && $this->setEmbedAttr('width', $width);
  }

  /**
   * Get width of the embedded code
   */
  public function getWidth(){
    return $this->embeviWidth;
  }

  /**
   * Set the global width of the object and embed
   *
   * @param integer $width - width to set the object and embed
   */
  public function setGlobalWidth($width){
    $this->globalWidth = intval($width);
  }

  /**
   * Unset the global width of the object and embed
   */
  public function unsetGlobalWidth(){
    $this->globalWidth = 0;
  }

  /**
   * Set maximum global width of the object and embed
   *
   * @param integer $width - width to set the object and embed
   */
  public function setGlobalMaxWidth($maxWidth){
    $this->globalMaxWidth = intval($maxWidth);
  }

  /**
   * Unset maximum global width of the object and embed
   */
  public function unsetGlobalMaxWidth(){
    $this->globalMaxWidth = 0;
  }

  /**
   * Set keep ratio calculation
   *
   * @param bool $keepRatio - flag for generating width/height by keeping ratio
   */
  public function setKeepRatio(){
    $this->keepRatio = true;
  }

  /**
   * Unset auto generation width or height using ratio
   */
  public function unsetKeepRatio(){
    $this->keepRatio = false;
  }

  /**
   * Set max dimensions with the highest importance
   */
  public function setMaxImportant(){
    $this->globalMaxImportant = true;
  }

  /**
   * Set defaul dimensions importance
   */
  public function unsetMaxImportant(){
    $this->globalMaxImportant = false;
  }

  /**
   * Use function htmlspecialchars_decode in parseHtml method
   */
  public function setHtmlDecode(){
    $this->specialCharDecode = true;
  }

  /**
   * Don't use function htmlspecialchars_decode in parseHtml method
   */
  public function unsetHtmlDecode(){
    $this->specialCharDecode = false;
  }

  /**
   * Get provider, site from where is used the embedded code
   */
  public function getEmbeddedProvider(){
    return $this->embeviProvider;
  }

  /**
   * Get informations about embedded code
   */
  public function getEmbeddedInfo(){
    return $this->embeviInfo;
  }

  /**
   * Set object attribute value
   *
   * @param mixed $param - the name of the object attribute to be set or an array of multiple object attributes to set
   * @param string $value - (optional) the value to set the object attribute, if only one object attribute is being set
   *
   * @return boolean - true, if the value was set
   *                 - false, if objectAttr isn't array
   */
  public function setObjectAttr($param, $value = ''){
    if (!is_array($this->objectAttr)) return false;

    if ( is_array($param) ) {
      foreach ($param AS $k => $v) {
        $this->objectAttr[$k] = $v;
      }
    }
    else {
      $this->objectAttr[$param] = $value;
    }

    return true;
  }

  /**
   * Set embed attribute value
   *
   * @param mixed $param - the name of the embed attribute to be set or an array of multiple embed attributes to set
   * @param string $value - (optional) the value to set the embed attribute, if only one embed attribute is being set
   *
   * @return boolean - true, if the value was set
   *                 - false, if embedAttr isn't array
   */
  public function setEmbedAttr($param, $value = ''){
    if (!is_array($this->embedAttr)) return false;

    if ( is_array($param) ) {
      foreach ($param AS $k => $v) {
        $this->embedAttr[$k] = $v;
      }
    }
    else {
      $this->embedAttr[$param] = $value;
    }

    return true;
  }

  /**
   * Set object param value
   *
   * @param mixed $param - the name of the param to be set or an array of multiple params to set
   * @param string $value - (optional) the value to set the param, if only one param is being set
   *
   * @return boolean - true, if the value was set
   *                 - false, if objectParam isn't array
   */
  public function setObjectParam($param, $value = ''){
    if (!is_array($this->objectParam)) return false;

    if ( is_array($param) ) {
      foreach ($param AS $k => $v) {
        $this->objectParam[$k] = $v;
      }
    }
    else {
      $this->objectParam[$param] = $value;
    }

    return true;
  }

  /**
   * Parse URL
   *
   * @param string $url - Link to check for embeded video
   *
   * @return boolean - true, if the link is supported for embedding
   *                 - false, if the link isn't supported for embedding
   */
  public function parseUrl($url){
    $goodLink = false;
    $url = trim($url);

    if( $this->acceptShortUrl && preg_match($this->returnShortUrlServicesRegExpr(), $url) ){
      $url = $this->realUrl($url);
    }

    foreach($this->embeviSupport as $k=>$v){
      if(preg_match('/'.$v['matchExpr'].'/i', $url, $result)){
        $this->baseWidth = $v['width'];
        $this->baseHeight = $v['height'];
        $this->embeviProvider = $v['provider'];
        $this->embeviInfo = $v['info'];
        $this->setEmbedAttr('src', $v['src']);
        $this->setWidth(0);
        $this->setHeight(0);

        if(isset($v['flashvars'])){
          $this->setEmbedAttr('flashvars', $v['flashvars']);
          for($regCount = 1; $regCount<count($result); $regCount++){
            $this->embedAttr['src'] = str_ireplace("~to_replace".$regCount."~", $result[$regCount], $this->embedAttr['src']);
            $this->embedAttr['flashvars'] = str_ireplace("~to_replace".$regCount."~", $result[$regCount], $this->embedAttr['flashvars']);
          }
          $this->setObjectParam('flashvars', $this->embedAttr['flashvars']);
        }
        else{
          for($regCount = 1; $regCount<count($result); $regCount++){
            $this->embedAttr['src'] = str_ireplace("~to_replace".$regCount."~", $result[$regCount], $this->embedAttr['src']);
          }
        }
        $this->setObjectParam('movie', $this->embedAttr['src']);
        $goodLink = true;
        break;
      }
    }
    if($goodLink){
      //set width and height that will be used for generated code
      $this->setDimensions();
    }
    return $goodLink;
  }

  /**
   * Get the HTML code for the video
   *
   * @param boolean $addHtmlBefore - false, doesn't do anything
   *                               - true, adds <br/> before the embedded code
   * @param boolean $addHtmlAfter - false, doesn't do anything
   *                              - true, adds <br/> after the embedded code
   *
   * @return string - HTML code for the video
   */
  public function getCode($addHtmlBefore = false, $addHtmlAfter = false){
    $objectAttributes = '';
    $objectParams = '';
    $embedAttributes = '';
    foreach ($this->objectAttr AS $k => $v) {
		$objectAttributes .= ' '.$k.'="'.$v.'"';
    }

    foreach ($this->objectParam AS $k => $v) {
      $objectParams .= '<param name="'.$k.'" value="'.$v.'" />';
    }

    foreach ($this->embedAttr AS $k => $v) {
		$embedAttributes .= ' '.$k.'="'.$v.'"';
    }

    $beforeHtml = "";
    if($addHtmlBefore){
      $beforeHtml = $this->beforeHtml;
    }
    $afterHtml = "";
    if($addHtmlAfter){
      $afterHtml = $this->afterHtml;
    }

    return sprintf("%s\n <object %s>\n %s \n<embed %s />\n</object>%s", $beforeHtml, $objectAttributes, $objectParams, $embedAttributes, $afterHtml);
  }

  /**
   * Parse text
   *
   * @param string $text - Text to check for embeded video
   * @param boolean $keepLink - false, the embedded link will be removed
   *                          - true, the embedded link will be returned
   * @param boolean $addHtmlBefore - false, doesn't do anything
   *                                  - true, adds <br/> before the embedded code
   * @param boolean $addHtmlAfter - false, doesn't do anything
   *                                 - true, adds <br/> after the embedded code
   *
   * @return string - text with embeded code
   *
   */
  public function parseText($text, $keepLink = false, $addHtmlBefore = false, $addHtmlAfter = false){
    if(preg_match_all('/(https?[\S]+)/i', $text, $result)){
      foreach($result[1] AS $link){
        if($this->parseUrl($link)){
          $linkReplacement = $this->getCode($addHtmlBefore, $addHtmlAfter);
          if($keepLink){
            $linkReplacement .= $link;
          }
          $text = preg_replace('/'.preg_quote($link, '/').'/i', $linkReplacement, $text);
        }
      }
    }
    return $text;
  }

  /**
   * Parse HTML
   *
   * @param string $html - HTML to check for embeded video
   * @param boolean $keepLink - false, the embedded link will be removed
   *                          - true, the embedded link will be returned
   * @param boolean $addHtmlBefore - false, doesn't do anything
   *                                  - true, adds <br/> before the embedded code
   * @param boolean $addHtmlAfter - false, doesn't do anything
   *                                 - true, adds <br/> after the embedded code
   *
   * @return string - text with embeded code
   *
   */
  public function parseHtml($html, $keepLink = false, $addHtmlBefore = false, $addHtmlAfter = false){
    if($this->specialCharDecode){
      $html = htmlspecialchars_decode($html);
    }
    if( preg_match_all('@(?<atag><a[^>]*?href ?= ?(?:"|\')(?<href>https?(?:.|\n)*?)(?:"|\').*?>(?<text>.*?)</a(?:[\s]+)?>)@i', $html, $result) ){

      for($i =0; $i<count($result['atag']); $i++){
        $link = $result['href'][$i];
        if($this->parseUrl($link)){
          $linkReplacement = $this->getCode($addHtmlBefore, $addHtmlAfter);
          if($keepLink){
            $linkReplacement .= $result['atag'][$i];
          }
          $html = preg_replace('/'.preg_quote($result['atag'][$i], '/').'/i', $linkReplacement, $html);
        }
      }

    }
    return $html;
  }

  /**
   * Set Before HTML
   *
   * @param string $beforeHtml - string that will appear before the embedded code
   *                           when you use parseText method
   */
  public function setBeforeHtml($beforeHtml){
    $this->beforeHtml = $beforeHtml;
  }

  /**
   * Set After HTML
   *
   * @param string $afterHtml - string that will appear after the embedded code
   *                           when you use parseText method
   */
  public function setAfterHtml($afterHtml){
    $this->afterHtml = $afterHtml;
  }

  /**
   * Set Preference for accepting Short URL
   *
   */
  public function setAcceptShortUrl(){
    $this->acceptShortUrl = true;
  }

  /**
   * Unset Preference for accepting Short URL
   *
   */
  public function unsetAcceptShortUrl(){
    $this->acceptShortUrl = false;
  }

  /**
   * Return real URL
   *
   * @param string $url
   * @return string
   */
  private function realUrl($url){

    if (!function_exists('get_headers')) {
	/**
	* Same functionality as PHP5 get_headers
	*
	* Source code for function: http://php.net/manual/en/function.get-headers.php
	*
	* @author info at marc-gutt dot de
	* @param string $url
	* @param int $format
	* @return array
	*/
      function get_headers($url, $format=0) {
        $headers = array();
        $url = parse_url($url);
        $host = isset($url['host']) ? $url['host'] : '';
        $port = isset($url['port']) ? $url['port'] : 80;
        $path = (isset($url['path']) ? $url['path'] : '/') . (isset($url['query']) ? '?' . $url['query'] : '');
        $fp = fsockopen($host, $port, $errno, $errstr, 3);
        if ($fp){
          $hdr = "GET $path HTTP/1.1\r\n";
          $hdr .= "Host: $host \r\n";
          $hdr .= "Connection: Close\r\n\r\n";
          fwrite($fp, $hdr);
          while (!feof($fp) && $line = trim(fgets($fp, 1024))){
            if ($line == "\r\n") break;
            list($key, $val) = explode(': ', $line, 2);
            if ($format){
              if ($val){
                $headers[$key] = $val;
              }
              else{
                $headers[] = $key;
              }
            }
            else{
              $headers[] = $line;
            }
          }
          fclose($fp);
          return $headers;
        }
        return false;
      }
    }

    $urlHeaders =get_headers($url, 1);
    if(isset($urlHeaders['Location'])){
      if(is_array($urlHeaders['Location'])){
        $url = $urlHeaders['Location'][count($urlHeaders['Location'])-1];
      }
      else{
        $url = $urlHeaders['Location'];
      }
    }

    return $url;
  }

  /**
   * Return shorten url services regular expression
   *
   * @return string
   */
  private function returnShortUrlServicesRegExpr(){
    $shortUrlServices = $this->shortUrlServices;
    foreach($shortUrlServices AS $key => $value){
      $shortUrlServices[$key] = preg_quote($value, '/');
    }
    $shortUrlServices = implode("|", $shortUrlServices);
    return '/https?:\/\/(w{3}\.)?('.$shortUrlServices.')/i';
  }

  /**
   * Set dimensions for embedded code
   *
   */
  private function setDimensions(){
    $useWidth = $this->embeviWidth;
    $useHeight = $this->embeviHeight;

    if($useWidth <= 0 ){
      if($this->globalWidth > 0){
        $useWidth = $this->globalWidth;
      }
	if ( $this->globalMaxImportant && $this->globalMaxWidth > 0 &&
          ( ($useWidth > $this->globalMaxWidth) || ($useWidth == 0 && $this->baseWidth > $this->globalMaxWidth) ) ){
        $useWidth = $this->globalMaxWidth;
      }
    }

    if($useHeight <= 0){
      if($this->globalHeight > 0){
        $useHeight = $this->globalHeight;
      }
	if ( $this->globalMaxImportant && $this->globalMaxHeight > 0 &&
          ( ($useHeight > $this->globalMaxHeight) || ($useHeight == 0 && $this->baseHeight > $this->globalMaxHeight) ) ){
        $useHeight = $this->globalMaxHeight;
      }
    }

	if($this->globalMaxImportant){
      if($this->globalMaxWidth > 0){
        if($useWidth > $this->globalMaxWidth){
          $useWidth = $this->globalMaxWidth;
        }
        elseif($useWidth == 0 && $this->baseWidth > $this->globalMaxWidth){
          $useWidth = $this->globalMaxWidth;
        }
      }
    }

    if($this->keepRatio && $useWidth === 0 && $useHeight !== 0 ){
      $useWidth = round($useHeight*$this->baseWidth/$this->baseHeight, 0);
    }

	if($this->globalMaxImportant){
      if($this->globalMaxHeight > 0){
        if($useHeight > $this->globalMaxHeight){
          $useHeight = $this->globalMaxHeight;
        }
        elseif($useWidth == 0 && $this->baseHeight > $this->globalMaxHeight){
          $useHeight = $this->globalMaxHeight;
        }
      }
    }

    if($this->keepRatio && $useWidth !== 0 && $useHeight === 0 ){
      $useHeight = round($useWidth*$this->baseHeight/$this->baseWidth, 0);
    }

    //set width
    $this->setWidth($useWidth);
    //set height
    $this->setHeight($useHeight);

  }

}

?>
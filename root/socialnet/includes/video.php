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
      'provider' => array('http://www.220.ro/'),
      'info' => '220.ro video',
      'width' => 450,
      'height' => 366,
      'src' => 'http://www.220.ro/emb/~to_replace1~',
      'matchExpr' => '220\.ro\/([a-z0-9-_]+)\/'
    ),
    array(
      'provider' => array('http://video.google.com/'),
      'info' => 'Google video',
      'width' => 400,
      'height' => 326,
      'src' => 'http://video.google.com/googleplayer.swf?docid=~to_replace1~&hl=en&fs=true',
      'matchExpr' => 'video\.google\.com\/videoplay\?docid=([a-z0-9-_]+)'
    ),
    array(
      'provider' => array('http://www.dailymotion.com/'),
      'info' => 'Dailymotion video',
      'width' => 420,
      'height' => 399,
      'src' => 'http://www.dailymotion.com/swf/~to_replace1~',
      'matchExpr' => 'dailymotion\.com.*\/video\/([a-z0-9]+)_'
    ),
    array(
      'provider' => array('http://www.trilulilu.ro/'),
      'info' => 'Trilulilu video',
      'width' => 440,
      'height' => 362,
      'src' => 'http://embed.trilulilu.ro/source/go2player.php?type=video&hash=~to_replace2~&userid=~to_replace1~&src=hi5',
      'matchExpr' => 'trilulilu\.ro\/([a-z0-9-_]+)\/([a-z0-9-_]+)'
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
      'provider' => array('http://www.collegehumor.com/'),
      'info' => 'Collegehumor video',
      'width' => 480,
      'height' => 360,
      'src' => 'http://www.collegehumor.com/moogaloop/moogaloop.swf?clip_id=~to_replace1~&fullscreen=1',
      'matchExpr' => 'collegehumor\.com\/video:([a-z0-9-_]+)'
    ),
    array(
      'provider' => array('http://www.cnet.com/'),
      'info' => 'CnetTV video',
      'width' => 364, 
      'height' => 280,
      'src' => 'http://www.cnet.com/av/video/flv/universalPlayer/universalSmall.swf?playerType=embedded&type=id&value=~to_replace4~',
      'matchExpr' => 'cnettv\.cnet\.com\/([a-z0-9-_]+)\/([0-9]+)-([0-9_]+)-([0-9]+)'
    ),
    array(
      'provider' => array('http://www.glumbert.com/'),
      'info' => 'Glumbert video',
      'width' => 448, 
      'height' => 336,
      'src' => 'http://www.glumbert.com/embed/~to_replace1~',
      'matchExpr' => 'glumbert\.com\/media\/([0-9a-z]+)'
    ),
    array(
      'provider' => array('http://www.myvideo.at', 'http://www.myvideo.de', 'http://www.myvideo.ch', 'http://www.myvideo.be', 'http://www.myvideo.nl'),
      'info' => 'Myvideo video',
      'width' => 470, 
      'height' => 406,
      'src' => 'http://www.myvideo.~to_replace1~/movie/~to_replace2~',
      'matchExpr' => 'myvideo\.(at|be|ch|de|nl)\/(?:watch|movie)\/([a-z0-9-_=]+)'
    ),
    array(
      'provider' => array('http://video.yahoo.com'),
      'info' => 'Yahoo video',
      'flashvars' => 'id=~to_replace2~&vid=~to_replace1~&lang=en-us&intl=us',
      'width' => 512,
      'height' => 322,
      'src' => 'http://d.yimg.com/static.video.yahoo.com/yep/YV_YEP.swf?ver=2.2.46&id=~to_replace2~&vid=~to_replace1~&lang=en-us&intl=us',
      'matchExpr' => 'video\.yahoo\.com\/watch\/([0-9a-z]+)\/([0-9a-z]+)'
    ),
    array(
      'provider' => array('http://vids.myspace.com'),
      'info' => 'Byspace video',
      'width' => 425,
      'height' => 360,
      'src' => 'http://mediaservices.myspace.com/services/media/embed.aspx/m=~to_replace1~,t=1,mt=video',
      'matchExpr' => 'vids\.myspace\.com\/.*VideoID=([0-9a-z]+)'
    ),
    array(
      'provider' => array('http://www.liveleak.com/'),
      'info' => 'Liveleak video',
      'width' => 450,
      'height' => 370,
      'src' => 'http://www.liveleak.com/e/~to_replace1~',
      'matchExpr' => 'liveleak\.com\/view\?i=([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://vimeo.com/'),
      'info' => 'Vimeo video',
      'width' => 400,
      'height' => 255,
      'src' => 'http://vimeo.com/moogaloop.swf?clip_id=~to_replace1~&server=vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1',
      'matchExpr' => 'vimeo\.com\/(?:[^#]*#)?([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.gametrailers.com/'),
      'info' => 'Gametrailers video',
      'width' => 480,
      'height' => 392,
      'src' => 'http://www.gametrailers.com/remote_wrap.php?mid=~to_replace2~',
      'matchExpr' => 'gametrailers\.com\/(player|video.*)\/([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.ustream.tv/'),
      'info' => 'Ustream video',
      'flashvars' => 'viewcount=true&autoplay=false&brand=embed',
      'width' => 400,
      'height' => 320,
      'src' => 'http://www.ustream.tv/flash/video/~to_replace1~',
      'matchExpr' => 'ustream\.tv\/recorded\/([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://clipshack.com/'),
      'info' => 'Clipshack video',
      'width' => 430,
      'height' => 370,
      'src' => 'http://clipshack.com/player.swf?key=~to_replace1~',
      'matchExpr' => 'clipshack\.com\/Clip\.aspx\?key=([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://funnyordie.com/'),
      'info' => 'Funnyordie video',
      'width' => 480,
      'height' => 400,
      'src' => 'http://funnyordie.com/public/flash/fodplayer.swf?key=~to_replace1~',
      'matchExpr' => 'funnyordie\.com\/videos\/([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.filebox.ro/video/'),
      'info' => 'Filebox video',
      'flashvars' => 'source_script=http://videoserver273.filebox.ro/get_video.php&key=~to_replace1~&autostart=0&getLink=http://fbx.ro/v/~to_replace1~&splash=http://imageserver.filebox.ro/get_splash.php?key=~to_replace1~&link=http://fbx.ro/v/~to_replace1~',
      'width' => 420,
      'height' => 315,
      'src' => 'http://www.filebox.ro/video/FileboxPlayer_provider.php',
      'matchExpr' => 'filebox\.ro\/video\/play_video\.php\?key=([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.last.fm/music/videos/'),
      'info' => 'Last.FM video',
      'flashvars' => 'uniqueName=~to_replace1~&autostart=&FSSupport=false&track=false&http://userserve-ak.last.fm/serve/image:320/~to_replace1~.jpg&title=&albumArt=&duration=&creator=',
      'width' => 450,
      'height' => 373,
      'src' => 'http://cdn.last.fm/videoplayer/l/15/VideoPlayer.swf?autostart=false',
      'matchExpr' => 'last\.fm\/music\/.*\/\+videos\/([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.youku.com'),
      'info' => 'youku.com video',
      'width' => 480,
      'height' => 400,
      'src' => 'http://player.youku.com/player.php/sid/~to_replace1~/v.swf',
      'matchExpr' => 'youku\.com\/v_show\/id_([0-9a-z-_=]+)\.html'
    ),
    array(
      'provider' => array('http://ishare.rediff.com/'),
      'info' => 'Rediff.com video',
      'flashvars' => 'videoURL=http://ishare.rediff.com/embedcodeplayer_config_REST.php?content_id=~to_replace1~&x=3',
      'width' => 400,
      'height' => 322,
      'src' => 'http://ishare.rediff.com/images/player_ad_20090416.swf',
      'matchExpr' => 'ishare\.rediff\.com\/video\/.*\/([0-9a-z-_=]+)'
    ),
    array(
      'provider' => array('http://vision.rambler.ru'),
      'info' => 'vision.rambler.ru video',
      'width' => 390,
      'height' => 370,
      'src' => 'http://vision.rambler.ru/i/e.swf?id=~to_replace1~/~to_replace2~/~to_replace3~&logo=1',
      'matchExpr' => 'vision\.rambler\.ru\/users\/([0-9a-z-_=]+)\/([0-9a-z-_=]+)\/([0-9a-z-_=]+)'
    ),
    array(
      'provider' => array('http://www.tudou.com/'),
      'info' => 'Tudou.com video',
      'width' => 400,
      'height' => 340,
      'src' => 'http://www.tudou.com/v/~to_replace2~',
      'matchExpr' => 'tudou\.com\/(programs\/view|v)\/([0-9a-z-_=]+)'
    ),
    array(
      'provider' => array('http://www.ku6.com/'),
      'info' => 'ku6.com video',
      'width' => 414,
      'height' => 305,
      'src' => 'http://player.ku6.com/refer/~to_replace1~/v.swf',
      'matchExpr' => 'ku6\.com\/.*show.*\/([0-9a-z-_=]+)\.html'
    ),
    array(
      'provider' => array('http://www.tinypic.com/'),
      'info' => 'Tinypic.com video',
      'width' => 440,
      'height' => 420,
      'src' => 'http://v5.tinypic.com/player.swf?file=~to_replace1~&s=~to_replace2~',
      'matchExpr' => 'tinypic\.com\/player.php\?v=([0-9a-z-_]+)&s=([0-9]+)'
    ),
    array(
      'provider' => array('http://video.libero.it/'),
      'info' => 'Libero.it video',
      'width' => 440,
      'height' => 420,
      'src' => 'http://video.libero.it/static/swf/eltvplayer.swf?id=~to_replace1~.flv&ap=0',
      'matchExpr' => 'video\.libero\.it\/app\/play\?id=([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://espn.go.com/video/'),
      'info' => 'espn.go.com video',
      'width' => 440,
      'height' => 361,
      'src' => 'http://espn.go.com/broadband/player.swf?mediaId=~to_replace1~',
      'matchExpr' => 'espn\.go\.com\/video\/clip\?id=([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.nfl.com/'),
      'info' => 'nfl.com video',
      'flashvars' => 'autoplay=0&contentId=~to_replace2~&channelId=~to_replace1~',
      'width' => 768,
      'height' => 432,
      'src' => 'http://static.nfl.com/static/site/flash/video/video-detail-player.swf',
      'matchExpr' => 'nfl\.com\/videos\/([0-9a-z-_]+)\/([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://video.web.de'),
      'info' => 'web.de video',
      'width' => 470,
      'height' => 406,
      'src' => 'http://video.web.de/movie/~to_replace1~',
      'matchExpr' => 'video\.web\.de\/watch\/([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://video.eksenim.mynet.com/'),
      'info' => 'eksenim.mynet.com video',
      'flashvars' => 'videolist=http://video.eksenim.mynet.com/batch/video_xml_embed.php?video_id=~to_replace1~&adxml=&autoplay=0',
      'width' => 400,
      'height' => 344,
      'src' => 'http://video.eksenim.mynet.com/flvplayers/vplayer17.swf',
      'matchExpr' => 'video\.eksenim\.mynet\.com\/[0-9a-z-_\.]+\/[0-9a-z-_]+\/([0-9]+)'
    ),
    array(
      'provider' => array('http://www.rutube.ru/'),
      'info' => 'rutube.ru video',
      'width' => 470,
      'height' => 353,
      'src' => 'http://video.rutube.ru/~to_replace1~',
      'matchExpr' => 'rutube\.ru\/tracks\/[0-9+]+\.html\?.*&?v=([0-9a-z-_=]+)'
    ),
    array(
      'provider' => array('http://www.livevideo.com/'),
      'info' => 'livevideo.com video',
      'width' => 445,
      'height' => 369,
      'src' => 'http://www.livevideo.com/flvplayer/embed/~to_replace2~&autoStart=0',
      'matchExpr' => 'livevideo\.com\/video(.*|.{0})\/([0-9a-z]+)\/.*\.aspx'
    ),
    array(
      'provider' => array('http://www.vbox7.com/'),
      'info' => 'vbox7.com video',
      'width' => 450,
      'height' => 403,
      'src' => 'http://i48.vbox7.com/player/ext.swf?vid=~to_replace1~',
      'matchExpr' => 'vbox7\.com\/play:([0-9a-z-_=]+)'
    ),
    array(
      'provider' => array('http://www.revver.com/'),
      'info' => 'revver.com video',
      'width' => 480,
      'height' => 392,
      'src' => 'http://flash.revver.com/player/1.0/player.swf?mediaId=~to_replace1~',
      'matchExpr' => 'revver\.com\/video\/([0-9a-z-_=]+)'
    ),
    array(
      'provider' => array('http://current.com/'),
      'info' => 'current.com video',
      'width' => 400,
      'height' => 286,
      'src' => 'http://current.com/e/~to_replace1~/en_US',
      'matchExpr' => 'current\.com\/items\/([0-9a-z-=]+)_'
    ),
    array(
      'provider' => array('http://www.dalealplay.com/'),
      'info' => 'dalealplay.com video',
      'width' => 464,
      'height' => 380,
      'src' => 'http://www.dalealplay.com/smarty/dap/embedplayer.swf?file=~to_replace1~/busadoraWisinYandel.flv&videoValoracion=0.00&autoStart=false',
      'matchExpr' => 'dalealplay\.com\/informaciondecontenido\.php\?con=([0-9a-z-_=]+)'
    ),
    array(
      'provider' => array('http://www.clipfish.de/'),
      'info' => 'clipfish.de video',
      'width' => 450,
      'height' => 390,
      'src' => 'http://www.clipfish.de/videoplayer.swf?as=0&vid=~to_replace1~&r=1',
      'matchExpr' => 'clipfish\.de.*\/video\/([0-9a-z-_=]+)\/'
    ),
    array(
      'provider' => array('http://clip.vn/'),
      'info' => 'clip.vn video',
      'width' => 450,
      'height' => 390,
      'src' => 'http://clip.vn/w/~to_replace1~',
      'matchExpr' => 'clip\.vn\/watch\/[0-9a-z-_=]+,([0-9a-z-_=]+)'
    ),
    array(
      'provider' => array('http://livestream.com/'),
      'info' => 'livestream.com video',
      'width' => 400,
      'height' => 400,
      'src' => 'http://static.livestream.com/grid/PlayerV2.swf?channel=~to_replace1~&layout=playerEmbedDefault&backgroundColor=0xffffff&backgroundAlpha=1&backgroundGradientStrength=0&chromeColor=0x000000&headerBarGlossEnabled=true&controlBarGlossEnabled=true&chatInputGlossEnabled=false&uiWhite=true&uiAlpha=0.5&uiSelectedAlpha=1&dropShadowEnabled=true&dropShadowHorizontalDistance=10&dropShadowVerticalDistance=10&paddingLeft=10&paddingRight=10&paddingTop=10&paddingBottom=10&cornerRadius=10&backToDirectoryURL=null&showViewers=true&embedEnabled=true&chatEnabled=true&onDemandEnabled=true&programGuideEnabled=false&fullScreenEnabled=true&reportAbuseEnabled=false&gridEnabled=false&initialIsOn=true&initialIsMute=false&initialVolume=10&contentId=null&initThumbUrl=null&playeraspectwidth=4&playeraspectheight=3&mogulusLogoEnabled=true',
      'matchExpr' => 'livestream\.com\/([0-9a-z-_=]+)'
    ),
    array(
      'provider' => array('http://www.tangle.com/'),
      'info' => 'tangle.com video',
      'flashvars' => 'viewkey=~to_replace2~',
      'width' => 330,
      'height' => 270,
      'src' => 'http://www.tangle.com/flash/swf/flvplayer.swf',
      'matchExpr' => 'tangle\.com\/view_video(\.php|.*)\?viewkey=([0-9a-z-_=]+)'
    ),
    array(
      'provider' => array('http://www.vidiac.com/'),
      'info' => 'vidiac.com video',
      'width' => 400,
      'height' => 350,
      'src' => 'http://www.vidiac.com/vidiac.swf?video=~to_replace1~&servicecfg=386',
      'matchExpr' => 'vidiac\.com\/video\/([0-9a-z-_=]+)\.htm'
    ),
    array(
      'provider' => array('http://www.5min.com/'),
      'info' => '5min.com video',
      'width' => 480,
      'height' => 401,
      'src' => 'http://www.5min.com/Embeded/~to_replace1~/',
      'matchExpr' => '5min\.com\/Video\/.*-([0-9]+)'
    ),
    array(
      'provider' => array('http://video.vol.at/'),
      'info' => 'vol.at video',
      'width' => 480,
      'height' => 388,
      'src' => 'http://video.vol.at/media_tp/custom/flowplayer/swf/FlowPlayerDark.swf?config={embedded:true,baseURL:\'http://video.vol.at/media_tp/custom/flowplayer/swf\',loop:false,playList:[{suggestedClipsInfoUrl:\'http://video.vol.at/suggestions.php?id=~to_replace1~\',url:\'http://video.vol.at/media/video_at/~to_replace1~.flv\'}],initialScale:\'scale\',controlBarBackgroundColor:\'0x000000\',autoBuffering:true,autoPlay:false}',
      'matchExpr' => 'video\.vol\.at\/video\/([0-9a-z-_=]+)'
    ),
    array(
      'provider' => array('http://www.wegame.com/'),
      'info' => 'wegame.com video',
      'flashvars' => 'xmlrequest=http://www.wegame.com/player/video/~to_replace1~&embedPlayer=true',
      'width' => 480,
      'height' => 387,
      'src' => 'http://www.wegame.com/static/flash/player.swf?xmlrequest=http://www.wegame.com//player/video/~to_replace0~',
      'matchExpr' => 'wegame\.com\/watch\/([0-9a-z-_=]+)'
    ),
    array(
      'provider' => array('http://ikbis.com/'),
      'info' => 'ikbis.com video',
      'width' => 425,
      'height' => 344,
      'src' => 'http://ikbis.com/swf/embded_flv.swf?video_id=~to_replace1~&fullscreenmode=false&file=http://ikbis.com/playlist_feed/~to_replace1~&image=http://shots.ikbis.com/video_thumbnail/~to_replace1~/screen/video.jpg&autostart=false&overstretch=fit&ply_color=undefined',
      'matchExpr' => 'ikbis\.com\/[0-9a-z-_=]+\/shot\/([0-9a-z-_=]+)'
    ),
    array(
      'provider' => array('http://www.youmaker.com/video/'),
      'info' => 'youmaker.com video',
      'flashvars' => 'file=http://www.youmaker.com/video/v?id=~to_replace1~%26nu%3Dnu&showdigits=true&overstretch=fit&autostart=false&rotatetime=12&linkfromdisplay=false&repeat=list&shuffle=false&&showfsbutton=false&fsreturnpage=&fullscreenpage=',
      'width' => 450,
      'height' => 358,
      'src' => 'http://www.youmaker.com/v.swf',
      'matchExpr' => 'youmaker\.com\/video\/sv\?id=([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.snotr.com/video/'),
      'info' => 'snotr.com video',
      'flashvars' => 'video=~to_replace1~&autoload=false&autoplay=false&startat=0',
      'width' => 520,
      'height' => 390,
      'src' => 'http://www.snotr.com/player.swf?v6',
      'matchExpr' => 'snotr\.com\/video\/([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.onetruemedia.com/'),
      'info' => 'onetruemedia.com video',
      'flashvars' => '&p=~to_replace2~&skin_id=&host=http://www.onetruemedia.com',
      'width' => 408,
      'height' => 382,
      'src' => 'http://www.onetruemedia.com/share_view_player?p=~to_replace2~',
      'matchExpr' => 'onetruemedia\.com\/(shared|otm_site\/view_shared)\?p=([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://clevver.com'),
      'info' => 'clevver.com video',
      'width' => 428,
      'height' => 380,
      'src' => 'http://i.clevver.com/flash/clvembed.swf?vid=~to_replace2~',
      'matchExpr' => 'clevver.com(\/.*|.?)\/videof\/([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.kewego.com/'),
      'info' => 'kewego.com video',
      'flashvars' => 'playerKey=061ca722fea8&skinKey=&language_code=en&stat=internal&autoStart=false&sig=~to_replace1~',
      'width' => 400,
      'height' => 300,
      'src' => 'http://sa.kewego.com/swf/p3/epix.swf',
      'matchExpr' => 'kewego\.com\/video\/([0-9a-z-_]+)\.html'
    ),
    array(
      'provider' => array('http://www.clipser.com/'),
      'info' => 'clipser.com video',
      'width' => 425,
      'height' => 355,
      'src' => 'http://www.clipser.com/Play?vid=~to_replace1~',
      'matchExpr' => 'clipser\.com\/watch_video\/([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.dailyhaha.com/'),
      'info' => 'dailyhaha.com video',
      'width' => 425,
      'height' => 350,
      'src' => 'http://www.dailyhaha.com/_vids/Whohah.swf?Vid=~to_replace1~.flv',
      'matchExpr' => 'dailyhaha\.com\/_vids\/([0-9a-z-_]+)\.htm'
    ),
    array(
      'provider' => array('http://www.howcast.com/'),
      'info' => 'howcast.com video',
      'width' => 432,
      'height' => 276,
      'src' => 'http://www.howcast.com/flash/howcast_player.swf?file=~to_replace1~&theme=black',
      'matchExpr' => 'howcast\.com\/videos\/([0-9]+)'
    ),
    array(
      'provider' => array('http://www.aniboom.com/'),
      'info' => 'aniboom.com video',
      'width' => 594,
      'height' => 334,
      'src' => 'http://api.aniboom.com/e/~to_replace1~',
      'matchExpr' => 'aniboom\.com\/animation-video\/([0-9]+)'
    ),
    array(
      'provider' => array('http://www.bragster.com/'),
      'info' => 'bragster.com video',
      'flashvars' => 'autoPlay=false&brag_id=~to_replace1~',
      'width' => 420,
      'height' => 315,
      'src' => 'http://www.bragster.com/flash/bragster_player_embed.swf',
      'matchExpr' => 'bragster\.com\/brags\/([0-9]+)-'
    ),
    array(
      'provider' => array('http://www.teachertube.com/'),
      'info' => 'teachertube.com video',
      'flashvars' => 'file=http://www.teachertube.com/embedFLV.php?pg=video_~to_replace1~&menu=false&frontcolor=ffffff&lightcolor=FF0000&logo=http://www.teachertube.com/www3/images/greylogo.swf&skin=http://www.teachertube.com/embed/overlay.swf&volume=80&controlbar=over&displayclick=link&viral.link=http://www.teachertube.com/viewVideo.php?video_id=~to_replace1~&stretching=exactfit&plugins=viral-1&viral.callout=none&viral.onpause=false',
      'width' => 470,
      'height' => 260,
      'src' => 'http://www.teachertube.com/embed/player.swf',
      'matchExpr' => 'teachertube\.com\/viewVideo\.php\?video_id=([0-9]+)'
    ),
    array(
      'provider' => array('http://www.shredordie.com/'),
      'info' => 'shredordie.com video',
      'flashvars' => 'key=~to_replace1~&vert=shredordie',
      'width' => 480,
      'height' => 400,
      'src' => 'http://player.ordienetworks.com/flash/fodplayer.swf',
      'matchExpr' => 'shredordie\.com\/videos\/([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.talentrun.com/'),
      'info' => 'talentrun.com video',
      'flashvars' => 'autostart=false&id=~to_replace1~&mode=splay&extUrl=http://www.talentrun.com/',
      'width' => 454,
      'height' => 421,
      'src' => 'http://www.talentrun.com/player/trp/',
      'matchExpr' => 'talentrun\.com\/player\/index\/([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.autsch.de/'),
      'info' => 'autsch.de video',
      'flashvars' => 'pk=~to_replace1~&displayheight=338&autostart=false',
      'width' => 450,
      'height' => 370,
      'src' => 'http://www.autsch.de/playerext/~to_replace1~',
      'matchExpr' => 'autsch\.de\/([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://tvbvideo.de/'),
      'info' => 'tvbvideo.de video',
      'flashvars' => 'playerKey=a67dd9fb6a97&skinKey=&language_code=de&stat=internal&advertise=false&autoStart=false&sig=~to_replace1~',
      'width' => 400,
      'height' => 300,
      'src' => 'http://sa.kewego.com/swf/p3/epix.swf',
      'matchExpr' => 'tvbvideo\.de\/video\/([0-9a-z-_]+)\.html'
    ),
    array(
      'provider' => array('http://www.clipmoon.com/'),
      'info' => 'clipmoon.com video',
      'flashvars' => 'config=http://www.clipmoon.com/flvplayer.php?viewkey=~to_replace1~&external=no',
      'width' => 500,
      'height' => 357,
      'src' => 'http://www.clipmoon.com/flvplayer.swf',
      'matchExpr' => 'clipmoon\.com\/videos\/([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.viddyou.com/'),
      'info' => 'viddyou.com video',
      'width' => 640,
      'height' => 480,
      'src' => 'http://www.viddyou.com/get/v2_full/~to_replace1~.swf',
      'matchExpr' => 'viddyou\.com\/viddstream\?videoid=([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.spymac.com/'),
      'info' => 'spymac.com video',
      'width' => 468,
      'height' => 308,
      'src' => 'http://www.spymac.com/hop?id=~to_replace1~',
      'matchExpr' => 'spymac\.com\/details\/\?([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.youare.tv/'),
      'info' => 'youare.tv video',
      'width' => 350,
      'height' => 300,
      'src' => 'http://www.youare.tv/yatvplayer.swf?videoID=~to_replace1~&serverDomain=youare.tv',
      'matchExpr' => 'youare\.tv\/watch\.php\?id=([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.mindbites.com/'),
      'info' => 'mindbites.com video',
      'width' => 554,
      'height' => 316,
      'src' => 'http://www.mindbites.com/v/~to_replace1~',
      'matchExpr' => 'mindbites\.com\/lesson\/([0-9a-z_]+)-'
    ),
    array(
      'provider' => array('http://www.jujunation.com/'),
      'info' => 'jujunation.com video',
      'flashvars' => 'config=http://www.jujunation.com/videoConfigXmlCode.php?pg=video_~to_replace1~_no_0_extsite&autoPlay=false',
      'width' => 450,
      'height' => 370,
      'src' => 'http://www.jujunation.com/flvplayer_elite.swf',
      'matchExpr' => 'jujunation\.com\/viewVideo\.php\?video_id=([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.rooftopcomedy.com/'),
      'info' => 'rooftopcomedy.com video',
      'flashvars' => 'baseURL=http://www.rooftopcomedy.com&clipCode=~to_replace1~',
      'width' => 448,
      'height' => 292,
      'src' => 'http://www.rooftopcomedy.com/flash/fmpv3/RooftopPlayerEmbedded.swf',
      'matchExpr' => 'rooftopcomedy\.com\/watch\/([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://hamburg1video.de'),
      'info' => 'hamburg1video.de video',
      'flashvars' => 'playerKey=acd17bc8b8f7&skinKey=&language_code=de&stat=internal&advertise=false&autoStart=false&sig=~to_replace1~',
      'width' => 400,
      'height' => 300,
      'src' => 'http://sa.kewego.com/swf/p3/epix.swf',
      'matchExpr' => 'hamburg1video\.de\/video\/([0-9a-z-_]+)\.html'
    ),
    array(
      'provider' => array('http://videos.caught-on-video.com/'),
      'info' => 'caught-on-video.com video',
      'flashvars' => 'video=~to_replace1~',
      'width' => 428,
      'height' => 352,
      'src' => 'http://videos.caught-on-video.com/vidiac.swf',
      'matchExpr' => 'videos\.caught-on-video\.com\/.*\/[0-9]+\/([0-9a-z-_]+)\.htm'
    ),
    array(
      'provider' => array('http://bubblare.se/'),
      'info' => 'bubblare.se video',
      'width' => 425,
      'height' => 350,
      'src' => 'http://bubblare.se/v/~to_replace1~/',
      'matchExpr' => 'bubblare\.se\/movie\/([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://jaycut.com/'),
      'info' => 'jaycut.com video',
      'flashvars' => 'file=http://jaycut.com/videos/send_preview/~to_replace1~&type=flv&returnUrl=http://jaycut.com/&locale=en&author=Toffan&autostart=false&mixerUrl=http://jaycut.com/mixer&inviteFriendsUrl=http://jaycut.com/myjaycut/friends/invite&createGroupUrl=http://jaycut.com/group/create&image=http://jaycut.com/video/~to_replace1~/thumbnail_big.jpeg&profileUrl=',
      'width' => 408,
      'height' => 324,
      'src' => 'http://jaycut.com/flash/preview.swf',
      'matchExpr' => 'jaycut\.com\/video\/([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://jaycut.com/'),
      'info' => 'jaycut.com video',
      'flashvars' => 'file=http://jaycut.com/mixes/send_preview/~to_replace1~&type=flv&returnUrl=http://jaycut.com/&locale=en&author=Toffan&autostart=false&mixerUrl=http://jaycut.com/mixer&inviteFriendsUrl=http://jaycut.com/myjaycut/friends/invite&createGroupUrl=http://jaycut.com/group/create&image=http://jaycut.com/video/~to_replace1~/thumbnail_big.jpeg&profileUrl=',
      'width' => 408,
      'height' => 324,
      'src' => 'http://jaycut.com/flash/preview.swf',
      'matchExpr' => 'jaycut\.com\/mix\/([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.spotn.de/'),
      'info' => 'spotn.de video',
      'flashvars' => 'config=http://www.spotn.de/flvplayer.php?viewkey=~to_replace1~',
      'width' => 450,
      'height' => 370,
      'src' => 'http://www.spotn.de/videoplayer.swf',
      'matchExpr' => 'spotn\.de\/watch\/([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.thexvid.com/'),
      'info' => 'thexvid.com video',
      'width' => 600,
      'height' => 369,
      'src' => 'http://www.thexvid.com/plr/~to_replace1~/video.swf',
      'matchExpr' => 'thexvid\.com\/video\/([0-9a-z-_]+)-'
    ),
    array(
      'provider' => array('http://www.scivee.tv/'),
      'info' => 'scivee.tv video',
      'flashvars' => 'id=~to_replace1~&type=4',
      'width' => 480,
      'height' => 400,
      'src' => 'http://www.scivee.tv/flash/embedCast.swf',
      'matchExpr' => 'scivee\.tv\/node\/([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.tvosz.com/'),
      'info' => 'tvosz.com video',
      'width' => 470,
      'height' => 380,
      'src' => 'http://www.tvosz.com/gtembed.swf?key=~to_replace1~',
      'matchExpr' => 'tvosz\.com\/view_video\.php\?viewkey=([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.dailycomedy.com/'),
      'info' => 'dailycomedy.com video',
      'width' => 320,
      'height' => 240,
      'src' => 'http://www.dailycomedy.com/videos/DCVideoPlayerII_HTTP.swf?videoid=~to_replace1~',
      'matchExpr' => 'dailycomedy\.com\/videos\/([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.deutschlandreporter.de/'),
      'info' => 'deutschlandreporter.de video',
      'width' => 480,
      'height' => 360,
      'src' => 'http://www.deutschlandreporter.de/flvplayer.swf?mediaid=~to_replace1~&hosturl=http://www.deutschlandreporter.de/&themecolor=0x99B3CC&symbolcolor=0x000000&backgroundcolor=0xFFFFFF&autostart=false&loop=false&overlay=http://www.deutschlandreporter.de//media/custom/player_emb.png',
      'matchExpr' => 'deutschlandreporter\.de\/videos\/([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.motorsportmad.com/'),
      'info' => 'motorsportmad.com video',
      'width' => 320,
      'height' => 260,
      'src' => 'http://www.motorsportmad.com/flvplayer.swf?file=http://media.motorsportmad.net.s3.amazonaws.com/~to_replace1~.flv&showfsbutton=true',
      'matchExpr' => 'motorsportmad\.com\/view\/([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.rheinvideo.de/'),
      'info' => 'rheinvideo.de video',
      'flashvars' => 'apiHost=apiwww.rheinvideo.de',
      'width' => 425,
      'height' => 350,
      'src' => 'http://www.rheinvideo.de/pl/~to_replace1~/425x350/swf',
      'matchExpr' => 'rheinvideo\.de\/videos\/([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.selfcasttv.com/'),
      'info' => 'selfcasttv.com video',
      'width' => 340,
      'height' => 283,
      'src' => 'http://www.selfcasttv.com/Selfcast/selfcast.swf?video_1=/~to_replace1~',
      'matchExpr' => 'selfcasttv\.com\/Selfcast\/playVideo\.do\?ref=([0-9a-z-_\/]+)'
    ),
    array(
      'provider' => array('http://myubo.com/', 'http://myubo.sk/'),
      'info' => 'myubo.sk video',
      'width' => 470,
      'height' => 386,
      'src' => 'http://myubo.com/storage/cms/flashPlayer/player.swf?movieURL=http://www.myubo.sk/videa/1/VideoDisk/Media/~to_replace1~/~to_replace2~/flv_~to_replace1~~to_replace2~~to_replace3~~to_replace4~~to_replace5~~to_replace6~~to_replace7~.flv',
      'matchExpr' => 'myubo\.(?:sk|com)\/page\/media_detail\.html\?movieid=([0-9a-z]{2})([0-9a-z]{2})([0-9a-z]+)-([0-9a-z]+)-([0-9a-z]+)-([0-9a-z]+)-([0-9a-z]+)'
    ),
    array(
      'provider' => array('http://www.gettyload.de/'),
      'info' => 'gettyload.de video',
      'width' => 425,
      'height' => 350,
      'src' => 'http://www.gettyload.de/flashplayer/video_embed.swf?xmlFile=~to_replace1~',
      'matchExpr' => 'gettyload\.de\/video\/[a-z0-9-_]+\/([0-9a-z]+)'
    ),
    array(
      'provider' => array('http://www.cliphost24.com/'),
      'info' => 'cliphost24.com video',
      'flashvars' => 'config=http://www.cliphost24.com/share/~to_replace1~/',
      'width' => 400,
      'height' => 320,
      'src' => 'http://www.cliphost24.com/flashplayer',
      'matchExpr' => 'cliphost24\.com\/videoclip-([0-9a-z]+)\.html'
    ),
    array(
      'provider' => array('http://ka.uvuvideo.org'),
      'info' => 'uvuvideo.org video',
      'flashvars' => 'affiliateSiteId=~to_replace2~&widgetId=110617&width=510&height=419&revision=12&kaShare=1&mediaType_mediaID=video_~to_replace1~&autoPlay=0',
      'width' => 510,
      'height' => 419,
      'src' => 'http://serve.a-widget.com/service/getWidgetSwf.kickAction',
      'matchExpr' => 'ka\.uvuvideo\.org\/[0-9a-z-_]+\/video\/([0-9a-z]+)\/([0-9a-z]+)\.html'
    ),
    array(
      'provider' => array('http://www.crovideos.com/'),
      'info' => 'crovideos.com video',
      'flashvars' => '&file=http://www.crovideos.com/flvideo/~to_replace1~.flv&height=260&width=320&frontcolor=0xCCCCCC&backcolor=0x6666FF&lightcolor=0xEEEEEE&logo=http://www.crovideos.com/crovideos-logo-player.png',
      'width' => 320,
      'height' => 260,
      'src' => 'http://www.crovideos.com/player.swf?file=http://www.crovideos.com/flvideo/~to_replace1~.flv&height=260&width=320&frontcolor=0xCCCCCC&backcolor=0x6666FF&lightcolor=0xEEEEEE&logo=http://www.crovideos.com/crovideos-logo-player.png',
      'matchExpr' => 'crovideos\.com\/video\/([0-9a-z]+)'
    ),
    array(
      'provider' => array('http://www.qubetv.tv/'),
      'info' => 'qubetv.tv video',
      'flashvars' => 'file=/videos/~to_replace1~/~to_replace1~.flv&autostart=false',
      'width' => 320,
      'height' => 240,
      'src' => 'http://www.qubetv.tv/swf/flvplayer.swf',
      'matchExpr' => 'qubetv\.tv\/videos\/detail\/([0-9a-z]+)'
    ),
    array(
      'provider' => array('http://citytube.de'),
      'info' => 'citytube.de video',
      'width' => 450,
      'height' => 390,
      'src' => 'http://stream.city-tube.de/player.swf?m=~to_replace2~',
      'matchExpr' => 'citytube\.de\/(\?m=|tube\/movie\/)([0-9a-z]+)'
    ),
    array(
      'provider' => array('http://constantcomedy.com/'),
      'info' => 'constantcomedy.com video',
      'width' => 430,
      'height' => 360,
      'src' => 'http://constantcomedy.com/swfs/embedPlayer.swf?ccVideo=~to_replace1~',
      'matchExpr' => 'constantcomedy\.com\/Video\.aspx\?id=([0-9a-z]+)'
    ),
    array(
      'provider' => array('http://www.luegmol.ch/'),
      'info' => 'luegmol.ch video',
      'flashvars' => 'config=http://www.luegmol.ch/player/luegmol_player_config_ext.php?vkey=~to_replace1~',
      'width' => 500,
      'height' => 395,
      'src' => 'http://www.luegmol.ch/player/luegmol_player.swf',
      'matchExpr' => 'luegmol\.ch\/video\/([0-9a-z]+)'
    ),
    array(
      'provider' => array('http://www.mantoutv.com/'),
      'info' => 'mantoutv.com video',
      'flashvars' => 'config=http://www.mantoutv.com/flvplayer.php?viewkey=~to_replace1~',
      'width' => 450,
      'height' => 370,
      'src' => 'http://www.mantoutv.com/videoplayer.swf',
      'matchExpr' => 'mantoutv\.com\/videoview_([0-9a-z]+)\.html'
    ),
    array(
      'provider' => array('http://www.clonevideos.com/'),
      'info' => 'clonevideos.com video',
      'width' => 450,
      'height' => 375,
      'src' => 'http://www.clonevideos.com/videowatchproplayer.swf?file=http://www.clonevideos.com/vdata/~to_replace1~.flv&vid=~to_replace1~&baseurl=http://www.clonevideos.com/&e=y',
      'matchExpr' => 'clonevideos\.com\/videos\/([0-9a-z]+)'
    ),
    array(
      'provider' => array('http://www.realitatea.net/'),
      'info' => 'realitatea.net video',
      'width' => 480,
      'height' => 380,
      'src' => 'http://www.realitatea.net/images/player/playlist_player.swf?url=1&id=~to_replace1~&autostart=false&showdigits=true&bufferlength=10&allowscriptaccess=always&recommendations=http://www.realitatea.net/feed_recommendations.php?id=~to_replace1~',
      'matchExpr' => 'realitatea\.net\/video_([0-9a-z]+)_'
    ),
    array(
      'provider' => array('http://www.mtv.com/videos/'),
      'info' => 'mtv.com video',
      'flashvars' => 'configParams=id=~to_replace2~&vid=~to_replace0~&uri=mgid:uma:video:mtv.com:~to_replace1~&startUri=(startUri)',
      'width' => 512,
      'height' => 319,
      'src' => 'http://media.mtvnservices.com/mgid:uma:video:mtv.com:~to_replace1~',
      'matchExpr' => 'mtv\.com\/videos\/.*\/([0-9a-z]+)\/.*#id=([0-9a-z]+)'
    ),
    array(
      'provider' => array('http://www.mtv.com/videos/'),
      'info' => 'mtv.com video',
      'flashvars' => 'configParams=vid=~to_replace1~',
      'width' => 512,
      'height' => 319,
      'src' => 'http://media.mtvnservices.com/mgid:uma:video:mtv.com:~to_replace1~',
      'matchExpr' => 'mtv\.com\/videos\/.*\/([0-9]+)\/'
    ),
    array(
      'provider' => array('http://www.rocktube.us/'),
      'info' => 'rocktube.us video',
      'width' => 450,
      'height' => 366,
      'src' => 'http://www.rocktube.us/embedded/~to_replace1~',
      'matchExpr' => 'rocktube\.us\/([0-9a-z]+)'
    ),
    array(
      'provider' => array('http://myplay.com'),
      'info' => 'myplay.com video',
      'flashvars' => 'videoId=~to_replace1~&playerId=271548504&viewerSecureGatewayURL=https://console.brightcove.com/services/amfgateway&servicesURL=http://services.brightcove.com/services&cdnURL=http://admin.brightcove.com&domain=embed&autoStart=false&',
      'width' => 425,
      'height' => 344,
      'src' => 'http://c.brightcove.com/services/viewer/federated_f8/271548504',
      'matchExpr' => 'myplay\.com\/video-player\/[0-9a-z-_]+\/\?bcpid=[0-9a-z-_]+&bclid=[0-9a-z-_]+&bctid=([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.123video.nl/'),
      'info' => '123video.com video',
      'width' => 420,
      'height' => 339,
      'src' => 'http://www.123video.nl/123video_share.swf?mediaSrc=~to_replace2~',
      'matchExpr' => '123video\.nl\/(playvideos\.asp\?MovieID|123video_share\.swf\?mediaSrc)=([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.9you.com/'),
      'info' => '9You.com video',
      'width' => 960,
      'height' => 480,
      'src' => 'http://v.9you.com/fplayer/player_watch.swf?flvID=~to_replace1~',
      'matchExpr' => '9you\.com\/watch\/([0-9a-z-_]+)'
    ),
    array(
      'provider' => array('http://www.blastro.com/'),
      'info' => 'blastro.com video',
      'width' => 512,
      'height' => 408,
      'src' => 'http://images.blastro.com/images/flashplayer/flvPlayer.swf?site=www.blastro.com&filename=~to_replace1~&pageID=&soundLevel=100&embed=&user_ID=-1&playlistMode=ondemand&playlist_id=&adsource=&channel=&keywords=&vid_pos=&artist_name=&quality=700&content_provider=&player_mode=&player_size=&autoplay=false&shuffle=&preroll_provider=&overlay_provider=&endcap_provider=&paidContent=&syndicated_pos=&getVars=detect_mediatype%3Dflv;detect_bitrate%3D_700;big%3D1;&redirect=http://www.blastro.com/player/~to_replace1~.html?detect_mediatype=flv&detect_bitrate=_700&big=1',
      'matchExpr' => 'blastro\.com\/player\/([a-z0-9-_]+)',
    ),
    array(
      'provider' => array('http://www.cellfish.com/'),
      'info' => 'cellfish.com video',
      'width' => 420,
      'height' => 315,
      'src' => 'http://static.cellfish.com/static/swf/player8.swf',
      'matchExpr' => 'cellfish\.com\/(video|ringtone|multimedia)\/([a-z0-9-_]+)',
      'flashvars' => 'Id=~to_replace2~&autoplay=false&widget=true&mediaPage=true&domain=cellfish.com&showProfileName=true'
    ),
    array(
      'provider' => array('http://videos.clarin.com/'),
      'info' => 'clarin.com videos',
      'width' => 533,
      'height' => 438,
      'src' => 'http://www.clarin.com/shared/v9/swf/clarinvideos/player.swf',
      'matchExpr' => 'videos\.clarin\.com\/index\.html\?id=([a-z0-9-_]+)',
      'flashvars' => 'SEARCH=http://www.videos.clarin.com/decoder/buscador_getMtmYRelacionados/~to_replace1~|CLARIN_VIDEOS|VIDEO|EMBEDDED|10|1|10|null.xml&RUTAS=http://www.clarin.com/shared/v9/swf/clarinvideos/rutas.xml&autoplay=false'
    ),
    array(
      'provider' => array('http://www.clipjunkie.com/'),
      'info' => 'ClipJunkie.com video',
      'width' => 495,
      'height' => 370,
      'src' => 'http://www.clipjunkie.com/flvplayer/flvplayer.swf',
      'matchExpr' => 'clipjunkie\.com\/([a-z0-9-_]+)\.htm',
      'flashvars' => 'config=http://www.clipjunkie.com/skin/config.xml&playList=http://www.clipjunkie.com/playlist.php&themes=http://www.clipjunkie.com/flvplayer/themes.xml&flv=http://videos.clipjunkie.com/videos/~to_replace1~.flv&autoplay=false'
    ),
    array(
      'provider' => array('http://www.cliplife.jp/'),
      'info' => 'cliplife.jp video',
      'width' => 320,
      'height' => 264,
      'src' => 'http://player.cliplife.jp/player_external_03.swf?clipinfo=http%3A%2F%2Fstream.cliplife.jp%2Fclipinfo%2Fclipinfo_03.php%3Fcontent_id%3D~to_replace1~',
      'matchExpr' => 'cliplife\.jp\/clip\/\?content_id=([a-z0-9-_]+)',
    ),
    array(
      'provider' => array('http://thedailyshow.com'),
      'info' => 'TheDailyShow video',
      'width' => 480,
      'height' => 383,
      'src' => 'http://media.mtvnservices.com/mgid:cms:video:comedycentral.com:~to_replace1~',
      'matchExpr' => 'thedailyshow\.com\/.*\.jhtml\?videoId=([a-z0-9-_]+)',
      'flashvars' => 'autoPlay=false&endCapAutoPlay=false&nextvideo=off&loop=false'
    ),
    array(
      'provider' => array('http://comedycentral.com'),
      'info' => 'ComedyCentral video',
      'width' => 480,
      'height' => 383,
      'src' => 'http://media.mtvnservices.com/mgid:cms:video:comedycentral.com:~to_replace2~',
      'matchExpr' => 'comedycentral\.com\/.*\.jhtml\?(videoId|episodeId)=([a-z0-9-_]+)',
      'flashvars' => 'autoPlay=false&endCapAutoPlay=false&nextvideo=off&loop=false'
    ),
    array(
      'provider' => array('http://colbertnation.com'),
      'info' => 'colbertnation.com video',
      'width' => 480,
      'height' => 383,
      'src' => 'http://media.mtvnservices.com/mgid:cms:item:comedycentral.com:~to_replace1~',
      'matchExpr' => 'colbertnation\.com\/.*\/([0-9]+)',
      'flashvars' => 'autoPlay=false&endCapAutoPlay=false&nextvideo=off&loop=false'
    ),
    array(
      'provider' => array('http://www.crunchyroll.com/'),
      'info' => 'crunchyroll.com video',
      'width' => 624,
      'height' => 328,
      'src' => 'http://static.crunchyroll.com/flash/20090921112226.d65e2ddb80363cc34004bd6214de692b/StandardVideoPlayer.swf',
      'matchExpr' => 'crunchyroll\.com\/.*(media-|\?mediaid=|\?videoid=)([0-9]+)',
      'flashvars' => 'config_url=http%3A%2F%2Fwww.crunchyroll.com%2Fxml%2F%3Freq%3DRpcApiVideoPlayer_GetStandardConfig%26media_id%3D~to_replace2~%26auto_play%3D0'
    ),
    array(
      'provider' => array('http://dotsub.com/'),
      'info' => 'dotsub.com video',
      'width' => 420,
      'height' => 347,
      'src' => 'http://dotsub.com/static/players/portalplayer.swf',
      'matchExpr' => 'dotsub\.com\/(media|view)\/((?:(?:[0-9a-z]+)-?){5})',
      'flashvars' => 'uuid=~to_replace2~&lang=eng&type=video&plugins=dotsub&embedded=true'
    ),
    array(
      'provider' => array('http://www.divshare.com/'),
      'info' => 'divshare.com video',
      'width' => 425,
      'height' => 319,
      'src' => 'http://www.divshare.com/flash/video2?myId=~to_replace1~',
      'matchExpr' => 'divshare\.com\/download\/([a-z0-9-_]+)'
    ),
    array(
      'provider' => array('http://www.kaltura.com/'),
      'info' => 'fandome.com video',
      'width' => 400,
      'height' => 400,
      'src' => 'http://www.kaltura.com/index.php/kwidget/wid/_35168/uiconf_id/1002330',
      'matchExpr' => 'fandome\.com\/video\/([a-z0-9-_]+)',
      'flashvars' => 'entryId=http://s3.amazonaws.com/lazyjock/~to_replace1~.flv&autoplay=false&volume=100&stretching=exactfit'
    ),
    array(
      'provider' => array('http://www.g4tv.com/'),
      'info' => 'g4tv.com video',
      'width' => 611,
      'height' => 341,
      'src' => 'http://www.g4tv.com/lv3/~to_replace2~',
      'matchExpr' => 'g4tv\.com\/(xplay|videos|lv3|sv3)\/([a-z0-9-_]+)',
      'flashvars' => 'phoenixBase=http%3A//g4tv.com/&colorTheme=0xff9933%2C0x492b0e%2C0xff620e%2C0xffc46f&videokey=~to_replace2~&image=&playerName=videodetail&autoplay=n&leftBarButtons=hidden&rightBarButtons=link%2Ccode%2Cdim&hdContent=false&showSDHD=false&sideBarsOverlap=false&endVideoCallback=VideoPlayer.playNextVideo&showContinuousPlay=false'
    ),
    array(
      'provider' => array('http://gamespot.com'),
      'info' => 'gamespot.com video',
      'width' => 480,
      'height' => 310,
      'src' => 'http://image.com.com/gamespot/images/cne_flash/production/media_player/proteus/gs/proteus2_gs.swf',
      'matchExpr' => 'gamespot\.com\/.*video\/([a-z0-9-_]+)',
      'flashvars' => 'playerMode=in_page&movieAspect=16.9&allowFullScreen=1&showOptions=1&menu_mode=&cs_id=3002244&flavor=480Version&skin=http://image.com.com/gamespot/images/cne_flash/production/media_player/proteus/one/skins/gamespot.png&autoPlay=false&embeddingAllowed=true&paramsURI=http%3A%2F%2Fwww.gamespot.com%2Fpages%2Fvideo_player%2Fxml.php%3Fid%3D~to_replace1~%26pid%3D972793%26ads%3Dnone%26ad_freq%3D0%26ontology%3D36%26ptype%3D6475%26mode%3Din_page%26width%3D480%26height%3D310'
    ),
    array(
      'provider' => array('http://www.gametube.org/'),
      'info' => 'gametube.org video',
      'width' => 451,
      'height' => 372,
      'src' => 'http://www.gametube.org/miniPlayer.swf?vidId=~to_replace2~',
      'matchExpr' => 'gametube\.org\/.*(\#\/video\/|htmlVideo\.jsp\?id=|miniPlayer\.swf\?vidId=)([\/a-z0-9-_=]+)',
    ),
    array(
      'provider' => array('http://www.gloria.tv/'),
      'info' => 'gloria.tv video',
      'width' => 494,
      'height' => 400,
      'src' => 'http://www.gloria.tv/?media=~to_replace1~&amp;embed',
      'matchExpr' => 'gloria\.tv\/\?media=([a-z0-9-_=]+)'
    ),
    array(
      'provider' => array('http://video.gotgame.com/'),
      'info' => 'gotgame.com video',
      'width' => 600,
      'height' => 418,
      'src' => 'http://video.gotgame.com/public/flash/flash_gordon.swf?vid=~to_replace1~',
      'matchExpr' => 'video\.gotgame\.com\/index\.php\/video\/view\/([a-z0-9-_=]+)'
    ),
    array(
      'provider' => array('http://www.guzer.com/'),
      'title' => 'guzer.com video',
      'width' => 486,
      'height' => 382,
      'src' => 'http://www.guzer.com/player/4-4player-licensed.swf',
      'matchExpr' => 'guzer\.com\/videos\/(.*)\.php',
      'flashvars' => '&file=http://www.guzer.com/videos/~to_replace1~.flv&image=http://www.guzer.com/videos/s~to_replace1~.jpg&stretching=exactfit'
    ),
    array(
      'provider' => array('http://www.izlesene.com/'),
      'info' => 'izlesene.com video',
      'width' => 465,
      'height' => 355,
      'src' => 'http://www.izlesene.com/player2.swf?video=~to_replace2~',
      'matchExpr' => 'izlesene\.com\/(player2\.swf\?video=|video\/(?:[a-z0-9-_]+)?\/)([a-z0-9-_=]+)'
    ),
    array(
      'provider' => array('http://www.joost.com/'),
      'info' => 'joost.com video',
      'width' => 640,
      'height' => 360,
      'src' => 'http://www.joost.com/embed/~to_replace1~',
      'matchExpr' => 'joost\.com\/([a-z0-9-_=]+)'
    ),
    array(
      'provider' => array('http://www.justin.tv/'),
      'info' => 'justin.tv video',
      'width' => 400,
      'height' => 300,
      'src' => 'http://www.justin.tv/widgets/live_embed_player.swf',
      'matchExpr' => 'justin\.tv\/([a-z0-9-_=]+)',
      'flashvars' => 'channel=~to_replace1~&auto_play=false&start_volume=50'
    ),
    array(
      'provider' => array('http://www.koreus.com/video/'),
      'info' => 'koreus.com video',
      'width' => 400,
      'height' => 320,
      'src' => 'http://www.koreus.com/video/~to_replace1~',
      'matchExpr' => 'koreus.com\/video\/([a-z0-9-_=]+)\.html'
    ),
    array(
      'provider' => array('http://www.machinima.com/'),
      'info' => 'machinima.com video',
      'width' => 450,
      'height' => 300,
      'src' => 'http://www.machinima.com/flv_player_master/player/waPlayer.swf?VideoID=~to_replace1~&Style=&PlaylistID=&path=http://www.machinima.com/flv_player_master&playerID=0&ra=',
      'matchExpr' => 'machinima\.com(?::80)?\/(?:film\/view(?:&|&amp;)id=|#details_)([a-z0-9-_=]+)'
    ),
    array(
      'provider' => array('http://msnbc.msn.com'),
      'info' => 'msn.com video',
      'width' => 425,
      'height' => 339,
      'matchExpr' => 'msnbc\.msn\.com\/id\/[a-z0-9-_=]+\/vp\/((?:[a-z0-9-_=]+#)?([a-z0-9-_=]+))',
      'src' => 'http://msnbcmedia.msn.com/i/MSNBC/Components/Video/_Player/swfs/embedPlayer/ey073009.swf?domain=www.msnbc.msn.com&settings=22425448&useProxy=true&wbDomain=www.msnbc.msn.com&launch=~to_replace2~&sw=1280&sh=800&EID=oVPEFC&playerid=22425001',
    ),
    array(
      'provider' => array('http://video.mail.ru'),
      'info' => 'mail.ru video',
      'width' => 585,
      'height' => 387,
      'src' => 'http://img.mail.ru/r/video2/player_v2.swf?ver=8&par=http://content.video.mail.ru/mail/~to_replace1~/~to_replace2~/$~to_replace3~$0$',
      'matchExpr' => 'video\.mail\.ru\/mail\/([a-z0-9-_=]+)\/([a-z0-9-_=]+)\/([a-z0-9-_=]+)\.html'
    ),
    array(
      'provider' => array('http://www.madnessvideo.net/'),
      'info' => 'madnessvideo.net video',
      'width' => 400,
      'height' => 320,
      'src' => 'http://www.madnessvideo.net/emb.aspx/~to_replace2~',
      'matchExpr' => 'madnessvideo\.net\/((?:videos.aspx\/)?(video~.*))'
    ),
    array(
      'provider' => array('http://video.milliyet.com.tr/'),
      'info' => 'milliyet.com.tr video',
      'width' => 340,
      'height' => 325,
      'src' => 'http://video.milliyet.com.tr/m.swf?prm=~to_replace1~,~to_replace2~&kanal=~to_replace3~&id=~to_replace4~&tarih=~to_replace5~&get=~to_replace6~',
      'matchExpr' => 'video\.milliyet\.com\.tr\/default\.asp\?prm=([0-9]+),([0-9]+)&kanal=([0-9]+)&id=([0-9]+)&tarih=([0-9\/]+)&get=([0-9\.]+)',
      'flashvars' => '&id=~to_replace4~&tarih=~to_replace5~'
    ),
    array(
      'provider' => array('http://mofile.com/'),
      'info' => 'mofile.com video',
      'width' => 500,
      'height' => 370,
      'src' => 'http://tv.mofile.com/cn/xplayer.swf?v=~to_replace1~',
      'matchExpr' => 'mofile\.com\/(?:show\/)?([a-z0-9-_=]+)',
      'flashvars' => 'v=~to_replace1~&fadshow=0&fadshowtime=8000&fadurl=http://v.mofile.com/v.mofile.com/swf/xbsg_500x358.swf&c=1&b=2&p=&autoplay=0&vTitle=&vtid=6&qDomain=tv.mofile.com&ad=0&ipregion=unknown&ipcity=unknown'
    ),
    array(
      'provider' => array('http://video.mpora.com/'),
      'info' => 'video.mpora.com video',
      'width' => 480,
      'height' => 315,
      'src' => 'http://video.mpora.com/ep/~to_replace1~/',
      'matchExpr' => 'video\.mpora\.com\/watch\/([a-z0-9-_=]+)'
    ),
    array(
      'provider' => array('http://www.seehaha.com/'),
      'info' => 'seehaha.com video',
      'width' => 480,
      'height' => 400,
      'src' => 'http://www.seehaha.com/flash/player.swf?vidFileName=~to_replace1~.flv',
      'matchExpr' => 'seehaha\.com\/flash\/player\.swf\?vidFileName=([a-z0-9-_=]+)\.flv'
    ),
    array(
      'provider' => array('http://video.mthai.com/'),
      'info' => 'video.mthai.com video',
      'width' => 407,
      'height' => 342,
      'src' => 'http://video.mthai.com/Flash_player/player.swf?idMovie=~to_replace1~',
      'matchExpr' => 'video\.mthai\.com\/player\.php\?.*id=([0-9a-z]+)',
    ),
    array(
      'provider' => array('http://videos.onsmash.com/'),
      'info' => 'onsmash.com video',
      'width' => 448,
      'height' => 374,
      'src' => 'http://videos.onsmash.com/e/~to_replace1~',
      'matchExpr' => 'videos\.onsmash\.com\/(?:v|e)\/([a-z0-9-_=]+)',
      'flashvars' => 'autoplay=0'
    ),
    array(
      'provider' => array('http://playlist.com/'),
      'info' => 'playlist.com playlist',
      'width' => 506,
      'height' => 300,
      'src' => 'http://static.pplaylist.com/players/mp3player_new_v103.swf',
      'matchExpr' => 'playlist\.com\/playlist\/([0-9]+)',
      'flashvars' => 'baseurl=http://www.playlist.com&config=site_noautostart&sopath=ppl-103&loginjs=false&autologin=never&getCode=gigya&movie=http://static.pplaylist.com/players/mp3player_new_v103.swf&enablejs=false&javascriptid=playerTop&playlist_id=~to_replace1~&apibaseurl=http://www.playlist.com/api&domain_pre_xspf=http://pl.playlist.com/pl.php?e=1%26playlist=&tracking=true&bgcolor=#ffffff&myheight=300&mywidth=506&wid=si&loc=playlist_audio&getcode=&promo=&meebo=false&userid=&debug=false',
    ),
    array(
      'provider' => array('http://www.rawvegas.tv'),
      'info' => 'rawvegas.tv video',
      'width' => 427,
      'height' => 300,
      'src' => 'http://www.rawvegas.tv/ext.php?uniqueVidID=~to_replace1~',
      'matchExpr' => 'rawvegas\.tv\/watch\/[a-z0-9-_]*\/([a-z0-9-_=]+)',
      'flashvars' => 'uniqueVidID=~to_replace1~'
    ),
    array(
      'provider' => array('http://www.screentoaster.com/'),
      'info' => 'screentoaster.com video',
      'width' => 425,
      'height' => 344,
      'src' => 'http://www.screentoaster.com/swf/STPlayer.swf',
      'matchExpr' => 'screentoaster\.com\/watch\/([a-z0-9]+)',
      'flashvars' => 'video=~to_replace1~',
    ),
    array(
      'provider' => array('http://www.sevenload.com/'),
      'info' => 'sevenload.com video',
      'width' => 500,
      'height' => 408,
      'src' => 'http://static.sevenload.com/swf/player/player.swf?v=143',
      'matchExpr' => 'sevenload\.com\/(?:videos?|videolar|filmy)\/([a-z0-9]{1,7})',
      'flashvars' => 'configPath=http://flash.sevenload.com/player?itemId=~to_replace1~&portalId=&screenlink=0&autoplay=0&environment=sevenload&autoPlayNext=0&locale=en_US'
    ),
    array(
      'provider' => array('http://www.sevenload.com/'),
      'info' => 'sevenload.com video',
      'width' => 500,
      'height' => 408,
      'src' => 'http://sevenload.com/pl/~to_replace1~/500x408/swf',
      'matchExpr' => 'sevenload\.com\/.*(?:episodes|folgen|puntate)\/([a-z0-9]{1,7})',
      'flashvars' => 'configPath=http://flash.sevenload.com/player?itemId=~to_replace1~&portalId=&screenlink=0&autoplay=0&environment=sevenload&autoPlayNext=0&locale=en_US'
    ),
    array(
      'provider' => array('http://www.shareview.us/'),
      'info' => 'shareview.us video',
      'width' => 540,
      'height' => 380,
      'src' => 'http://www.shareview.us/nvembed.swf?key=~to_replace1~',
      'matchExpr' => 'shareview\.us\/(?:video\/|nvembed\.swf\?key=)([a-z0-9-_=]+)\/'
    ),
    array(
      'provider' => array('http://smotri.com/'),
      'info' => 'smotri.com video',
      'width' => 400,
      'height' => 330,
      'src' => 'http://pics.smotri.com/scrubber_custom8.swf?file=~to_replace1~&bufferTime=3&autoStart=false&str_lang=eng&xmlsource=http%3A%2F%2Fpics.smotri.com%2Fcskins%2Fblue%2Fskin_color_lightaqua.xml&xmldatasource=http%3A%2F%2Fpics.smotri.com%2Fskin_ng.xml',
      'matchExpr' => 'smotri\.com\/video\/view\/\?id=([a-z0-9-_=]+)',
    ),
    array(
      'provider' => array('http://southparkstudios.com/'),
      'info' => 'southparkstudios.com video',
      'width' => 480,
      'height' => 400,
      'src' => 'http://media.mtvnservices.com/mgid:cms:item:southparkstudios.com:~to_replace1~',
      'matchExpr' => 'southparkstudios\.com\/clips\/([0-9]+)',
      'flashvars' => 'autoPlay=false&configParams=location%3Dhomepage&soWmode=window&soTargetDivId=video_player_box'
    ),
    array(
      'provider' => array('http://www.spike.com/'),
      'info' => 'spike.com video',
      'width' => 640,
      'height' => 480,
      'src' => 'http://www.spike.com/efp',
      'matchExpr' => 'spike\.com\/(?:video\/(?:[0-9a-z_-]+\/)?|efp\?flvbaseclip=)([0-9]+)',
      'flashvars' => 'flvbaseclip=~to_replace1~'
    ),
    array(
      'provider' => array('http://www.cbssports.com/video/'),
      'info' => 'cbssports.com video',
      'width' => 500,
      'height' => 380,
      'src' => 'http://www.cbs.com/thunder/swf30can10cbssports/rcpHolderCbs-3-4x3.swf?releaseURL=http://release.theplatform.com/content.select?pid=~to_replace1~&amp;Tracking=true&amp;Embedded=True&autoPlayVid=false',
      'matchExpr' => 'cbssports\.com\/video\/player\/(?:play|embed)\/[a-z0-9-_]+\/([0-9a-z_-]+)'
    ),
    array(
      'provider' => array('http://www.tagtele.com/'),
      'info' => 'tagtele.com video',
      'width' => 425,
      'height' => 350,
      'src' => 'http://www.tagtele.com/v/~to_replace1~',
      'matchExpr' => 'tagtele\.com\/(?:v|videos\/voir)\/([0-9]+)'
    ),
    array(
      'provider' => array('http://www.tm-tube.com/'),
      'info' => 'tm-tube.com video',
      'width' => 480,
      'height' => 360,
      'src' => 'http://www.tm-tube.com/vimp.swf?playlistmode=media&amp;mediaid=~to_replace1~&amp;webtv=false&amp;hosturl=http%3A%2F%2Fwww.tm-tube.com%2Fflashcomm.php',
      'matchExpr' => 'tm-tube\.com\/video\/([0-9]+)',
    ),
    array(
      'provider' => array('http://www.trtube.com/'),
      'info' => 'trtube.com video',
      'width' => 425,
      'height' => 350,
      'src' => 'http://www.trtube.com/mediaplayer_3_15.swf?file=http://www.trtube.com/playlist.php?v=~to_replace1~&image=http://www.trtube.com/vi/~to_replace1~.gif&logo=http://www.trimg.com/img/logoembed.gif&linkfromdisplay=false&linktarget=_blank&autostart=false',
      'matchExpr' => 'trtube\.com\/(?:izle\.php\?v=|[a-z0-9-_]+-)([a-z0-9]+)(\.html)?'
    ),
    array(
      'provider' => array('http://videolog.uol.com.br'),
      'info' => 'videolog.uol.com.br video',
      'width' => 424,
      'height' => 318,
      'src' => 'http://www.videolog.tv/ajax/codigoPlayer.php?id_video=~to_replace1~&relacionados=S&default=S&lang=PT_BR&cor_fundo=000000&swf=1&width=424&height=318',
      'matchExpr' => 'videolog\.uol\.com\.br\/video(?:\?|\.php\?id=)([0-9]+)',
    ),
    array(
      'provider' => array('http://www.u-tube.ru/'),
      'info' => 'u-tube.ru video',
      'width' => 400,
      'height' => 300,
      'src' => 'http://www.u-tube.ru/upload/others/flvplayer.swf?file=http://www.u-tube.ru/playlist.php?id=~to_replace1~&width=400&height=300',
      'matchExpr' => 'u-tube\.ru\/(?:playlist\.php\?id=|pages\/video\/)([0-9]+)',
    ),
    array(
      'provider' => array('http://videos.sapo.pt/'),
      'info' => 'videos.sapo.pt video',
      'width' => 410,
      'height' => 281,
      'src' => 'http://rd3.videos.sapo.pt/play?file=http://rd3.videos.sapo.pt/~to_replace1~/mov/1',
      'matchExpr' => 'videos\.sapo\.pt\/([0-9a-z]{20})',
    ),
    array(
      'provider' => array('http://videonuz.ensonhaber.com/'),
      'info' => 'videonuz.com video',
      'width' => 468,
      'height' => 379,
      'src' => 'http://videonuz.ensonhaber.com/mediaplayer2.swf?settings=http://videonuz.ensonhaber.com/player2.config.php?vid=~to_replace1~',
      'matchExpr' => 'videonuz\.ensonhaber\.com\/(?:medyaizle\.php\?haber_id=|haber-|.*?)([0-9]+)'
    ),
    array(
      'provider' => array('http://vidmax.com/'),
      'info' => 'vidmax.com video',
      'width' => 475,
      'height' => 356,
      'src' => 'http://vidmax.com/player.swf',
      'matchExpr' => 'vidmax\.com\/(?:index\.php\/)?videos?\/(?:view\/)?([0-9]+)',
      'flashvars' => '&file=http://www.vidmax.com/media/video/~to_replace1~.mp4&streamer=lighttpd&autostart=false&stretching=fill'
    ),
    array(
      'provider' => array('http://www.vsocial.com/'),
      'info' => 'vsocial.com video V1',
      'width' => 400,
      'height' => 330,
      'src' => 'http://static.vsocial.com/flash/upsl3.0.2/ups3.0.2.swf?d=~to_replace1~&a=0&s=false&url=http://www.vsocial.com/xml/upsl/vsocial/template.php',
      'matchExpr' => 'vsocial\.com\/(?:video\/|flash\/ups\.swf)\?d=([0-9]+)'
    ),
    array(
      'provider' => array('http://www.vsocial.com/'),
      'info' => 'vsocial.com video V2',
      'width' => 410,
      'height' => 400,
      'src' => 'http://www.vsocial.com/ups/~to_replace1~',
      'matchExpr' => 'vsocial\.com\/(?:ups|pdk)\/([0-9a-z]+)'
    ),
    array(
      'provider' => array('http://www.goear.com/'),
      'info' => 'goear.com audio',
      'width' => 353,
      'height' => 132,
      'src' => 'http://www.goear.com/files/external.swf?file=~to_replace2~',
      'matchExpr' => 'goear\.com\/listen(\.php\?v=|\/)([a-z0-9-_=]+)'
    ),
    array(
      'provider' => array('http://www.ijigg.com/'),
      'info' => 'ijigg.com audio',
      'width' => 315,
      'height' => 80,
      'src' => 'http://www.ijigg.com/jiggPlayer.swf?songID=~to_replace2~&Autoplay=0',
      'matchExpr' => 'ijigg\.com\/(jiggPlayer\.swf\?songID=|songs\/|trackback\/)([a-z0-9-_=]+)'
    ),
    array(
      'provider' => array('http://www.jamendo.com/'),
      'info' => 'jamendo.com audio',
      'width' => 200,
      'height' => 300,
      'src' => 'http://widgets.jamendo.com/en/~to_replace1~/?playertype=2008&~to_replace1~_id=~to_replace2~',
      'matchExpr' => 'jamendo\.com\/.*(playlist|track|album)\/([a-z0-9-_=]+)'
    ),
    array(
      'provider' => array('http://www.jujunation.com/'),
      'info' => 'jujunation.com audio',
      'width' => 220,
      'height' => 66,
      'src' => 'http://www.jujunation.com/player.swf?configXmlPath=http://www.jujunation.com/musicConfigXmlCode.php?pg=music_~to_replace1~&playListXmlPath=http://www.jujunation.com/musicPlaylistXmlCode.php?pg=music_~to_replace1~',
      'matchExpr' => 'jujunation.com\/music\.php\?music_id=([a-z0-9-_=]+)'
    ),
    array(
      'provider' => array('http://last.fm/'),
      'info' => 'last.fm audio',
      'width' => 300,
      'height' => 211,
      'matchExpr' => 'last\.fm\/music\/([a-z0-9-_=\+%]+)\/_\/([a-z0-9-_=\+]+)',
      'src' => 'http://cdn.last.fm/webclient/s12n/s/53/lfmPlayer.swf',
      'flashvars' => 'lang=en&amp;lfmMode=playlist&amp;FOD=true&amp;resname=~to_replace2~&amp;restype=track&amp;artist=~to_replace1~',
    ),
    array(
      'provider' => array('http://www.nhaccuatui.com/'),
      'info' => 'nhaccuatui.com audio',
      'width' => 300,
      'height' => 270,
      'src' => 'http://www.nhaccuatui.com/m/~to_replace1~',
      'matchExpr' => 'nhaccuatui\.com\/(?:nghe\?M=|m\/)([a-z0-9-_=]+)',
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
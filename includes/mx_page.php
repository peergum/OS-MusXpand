<?php
/* ---
 * Project: musxpand
 * File:    mx_page.php
 * Author:  phil
 * Date:    28/09/2010
 * ---
 * License:

    This file is part of musxpand.

    musxpand is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    musxpand is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with musxpand.  If not, see <http://www.gnu.org/licenses/>.

    Copyright � 2010 by Philippe Hilger
 */


include_once 'includes/mx_account.php';

function mx_preheader() {
	global $mx_ctxmenu,$mxuser;
	$page=mx_secureword($_GET['p']);
	$option=mx_secureword($_GET['o']);
	$action=mx_secureword($_REQUEST['a']);
	$section=mx_secureword($_REQUEST['k']);
	if ($_REQUEST['signed_request']) {
		//mx_checkfblogin(false);
		$facebook_page = mx_actionurl('main');
		$auth_url = 'https://www.facebook.com/dialog/oauth?client_id='
	            . FACEBOOK_APP_ID . '&redirect_uri=' . urlencode($facebook_page);
		$signed_request = $_REQUEST['signed_request'];
		list($encoded_sig, $payload) = explode('.', $signed_request, 2);

		$data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
		$mxuser->fbdata=$data;
     	if ($mxuser->fbdata['page']) {
	     	 if (!is_pagelike() || (!$mxuser->fbdata['user_id'] && !$mxuser->id)) {
	     	 	$page=$_GET['p']=$_REQUEST['p']='fblikeus';
	     		$option=$_GET['o']=$_REQUEST['o']='';
	     		$action=$_POST['a']=$_REQUEST['a']='';
	     		$section=$_REQUEST['k']='';
	     	 } else if (($page=='' || $page=='main') && $pageuser=mx_getaccountfrompage($mxuser->fbdata['page']['id'])) { // MX registered page
	     		$page=$_GET['p']=$_REQUEST['p']='artists';
	     		$option=$_GET['o']=$_REQUEST['o']='artprof';
	     		$action=$_POST['a']=$_REQUEST['a']=$pageuser;
	     		$section=$_REQUEST['k']='';
	     	}
     	} else {
     	}
	}
	if ($page=='' || $page=='main') {
		$page='main';
		$option='';
		if (!$mxuser->id) {
			//$page='account';
			//$option='signin';
		} else {
			switch($mxuser->status) {
				case MXACCTUNCONFIRMED:
					$page='account';
					$option='confirm';
					break;
				case MXACCTEMAILCONFIRMED:
					$page='account';
					$option='setup';
					break;
				default:
					$page='main';
			}
		}
		$_GET['p']=$_REQUEST['p']=$page;
		$_GET['o']=$_REQUEST['o']=$option;
	}

	switch (mx_checkpage($page,$option)) {
		case MXUNKNOWNPAGE:
		case MXRESTRICTEDPAGE:
		case MXMAINPAGE:
			header('Location: '.mx_pageurl('main'));
			break;
		case MXNOACCESS:
			header('Location: '.mx_pageurl('noaccess'));
			break;
		case MXREDIRECT:
			$other=preg_replace('%[&]?(a|p|o|k|fbp|canvas)=[^&]*%','',$_SERVER['QUERY_STRING']);
			$other=preg_replace('%^[&]+%','',$other);
			header('Location: '.mx_loginredirecturl($page,$option,$action,$section,$other));
			break;
	}
	if ($page!='') {
		$checkfunction='mx_ck'.$page;
		if (function_exists($checkfunction)) $checkfunction($page,$option,$action);
	}
	if ($option!='') {
		$checkfunction='mx_ck'.$option;
		if (function_exists($checkfunction)) $checkfunction($page,$option,$action);
	}
}

function mx_header($opts='') {
	global $mxuser;
	if ($opts) {
		header($opts);
	} else {
		header('Content-type: text/html; charset=utf-8');
	}
	$page=mx_secureword($_GET['p']);
	$option=mx_secureword($_GET['o']);

	?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#">

<head prefix="og:http://ogp.me/ns# fb:http://ogp.me/ns/fb# musxpand:http://ogp.me/ns/fb/musxpand#">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title><?php mx_sitetitle(); ?></title>
<?php mx_metatags(); ?>
<meta name="generator" content="<?php mx_proption('MXVersion'); ?>" />
<meta name="robots" content="index,follow" />

<link rel="stylesheet" href="<?php echo mx_stylesheet(); ?>" type="text/css" media="" />
<link rel="stylesheet" href="<?php mx_proption('templateURL'); ?>/fileuploader.css" type="text/css" media="screen" />
<link href="https://plus.google.com/113506343132201725433" rel="publisher" />
<link rel='index' title='<?php mx_proption('title'); ?>' href='<?php mx_proption('siteurl'); ?>' />
<link rel="shortcut icon" type="image/vnd.microsoft.icon" href="<?php mx_proption('siteurl'); ?>/musxpand.ico"/>
<link rel="icon" type="image/png" sizes="16x16" href="<?php mx_proption('siteurl'); ?>/musxpand-16x16.png"/>
<link type="text/css" href="/templates/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />
<script type="text/javascript" src="/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui-1.8.18.custom.min.js"></script>

<script>

var agreementmsg='<?php echo _('You must agree with the legal terms before submitting'); ?>';
var yousure='<?php echo _('Are you sure you want to do that?'); ?>';
var SentByString='<?php __('Sent by %1 on %2:'); ?>';
var recipientunknown='<?php __('Recipient unknown...'); ?>';
var leavingsitemsg='<?php __('WARNING: '
	.'You\\\'re about to visit a site '
	.'not related to MusXpand!\n'); ?>';
var descmsg='<?php __('Site:'); ?>';
var linkmsg='<?php __('Link:'); ?>';
var okcancelmsg='<?php __('\nClick OK to continue, Cancel to desist'); ?>';
var oopsmessage='<?php __('Oops! Something went wrong...'); ?>';
var available='<?php __('Available'); ?>';
var used='<?php __('Already Used'); ?>';
var houston='<?php __('Error'); ?>';
var needaletter='<?php __('Cannot be a number...'); ?>';
var reserved='<?php __('Reserved'); ?>';

var agreepub='<?php __('WARNING\\nBy publishing these medias, you agree with our terms and conditions, and in particular '
	.'you confirm you own the necessary rights or authorizations to do so. '
	.'You are also aware you won\\\'t be able to remove the access to these media from your current fans, if any, during the term of their subscription, limited to one year.'); ?>';
var agreearch='<?php __('IMPORTANT\\nBy archiving these medias, you are aware that they will still be available to your current subscribers '
	.'until the end of their subscription, limited to one year.'); ?>';

var siteurl='<?php mx_proption('siteurl'); ?>';
var secsiteurl='<?php mx_proption('secure_siteurl'); ?>';
var iconsurl='<?php echo mx_option('templateURL').'/icons/'; ?>';

</script>
<script src="<?php echo mx_option('siteurl').'/js/musxpand.js'; ?>" type="text/javascript"></script>
<script src="<?php echo mx_option('siteurl').'/js/fileuploader.js'; ?>" type="text/javascript"></script>
<script src="<?php echo mx_option('siteurl').'/js/json_sans_eval.js'; ?>" type="text/javascript"></script>
<!--
<link rel="stylesheet" href="<?php mx_proption('siteurl'); ?>/js/video-js/video-js.css" type="text/css" media="screen" />
<script src="<?php echo mx_option('siteurl').'/js/video-js/video.min.js'; ?>" type="text/javascript" charset="utf-8"></script>
<script>
      _V_.options.flash.swf = "<?php echo mx_option('siteurl').'/js/video-js/video-js.swf'; ?>"
</script>
-->
<script src="<?php echo mx_option('siteurl').'/js/flowplayer/flowplayer-3.2.10.min.js'; ?>" type="text/javascript"></script>
<script src="<?php echo mx_option('siteurl').'/js/jquery.zclip.min.js'; ?>" type="text/javascript"></script>
<?php
	// automatic slider for featured/random artists
	if ($option=='featarts' || $page=='main' || $page=='') {
		echo '<script src="'.mx_option('siteurl').'/js/skit/js/external/jquery.easing.1.3.min.js" type="text/javascript"></script>';
		echo '<script src="'.mx_option('siteurl').'/js/skit/js/external/jquery.mousewheel.min.js" type="text/javascript"></script>';
		echo '<script src="'.mx_option('siteurl').'/js/skit/js/sliderkit/jquery.sliderkit.1.9.1.pack.js" type="text/javascript"></script>';
		echo '<script src="'.mx_option('siteurl').'/js/skit/js/sliderkit/addons/sliderkit.timer.1.0.pack.js" type="text/javascript" ></script>';
		echo '<script type="text/javascript">
	    $(window).load(function(){

	        $("#sk1.sliderkit").sliderkit({
	            shownavitems:1,
	            auto:true,
	            circular:true,
	            navclipcenter:true,
	            scrolleasing:"easeInOutCubic",
	            autospeed:8000,
	            timer:{
	            	fadeout:0.7
	            	}
	        });
	        $("#sk2.sliderkit").sliderkit({
	            shownavitems:3,
	            auto:true,
	            circular:true,
	            navclipcenter:true,
	            scrolleasing:"easeInOutCubic",
	            autospeed:7500
	        });

	    });
		</script>';
		echo '<link rel="stylesheet" type="text/css" href="'.mx_option('siteurl').'/js/skit/css/sliderkit-core.css" media="screen, projection" />';
	}
	// graphs
	if ($option=='mystats' || $option=='artprof' || $option=='fanprof' || $option=='myartpage' || $option=='myfanpage') {
		echo '<script src="'.mx_option('siteurl').'/js/jquery.flot.min.js" type="text/javascript"></script>';
		echo '<script src="'.mx_option('siteurl').'/js/jquery.flot.selection.min.js" type="text/javascript"></script>';
	}
	?>
<!--  +1 Button -->
<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>

<!-- BEGIN Analytics stats -->
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-8655750-13']);
  _gaq.push(['_setDomainName', '.example.com']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<!-- END Analytics stats -->

<link rel="stylesheet" href="/js/sshow/css/style.css" />
<script type="text/javascript" src="/js/sshow/jquery.aw-showcase.js"></script>
<script type="text/javascript" src="/js/tooltip/jquery.tooltip.pack.js"></script>

</head>
<body class="bgdrop">
<div class="backpic"></div>
<div id="formhelper"><div></div></div>
<?php
	mx_FBinit();
}

function mx_footer() {
	global $debug,$mxsession,$FBsession,$me,$mxlocale,$mxuser;
	$debug.='mxsession='.$mxsession.' - FBsession='.$FBsession.' - mxlocale='.$mxlocale.
		' - mxuser_id='.$mxuser->id;
	if (DEBUG) {
		echo '<div class="debug">DEBUG INFO: ';
			print_r($_SESSION);
			//echo $debug;
			echo '</div>';
	}
	//--heavy CPU: if (!$mxuser->id) echo '<script type="text/javascript" src="'.mx_option('siteurl').'/js/snow.js"></script>';
	//echo '<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>';
	echo '</body></html>';
}

function mx_stylesheet() {
	return mx_option('templateURL').'/style.css';
}

function mx_body() {
	global $mxuser;
	$templatename=mx_option('template');
	$page=mx_secureword($_GET['p']);
	$option=mx_secureword($_GET['o']);
	if ($page=='fblikeus') {
		$templatefile=mx_option('templatedir').'/likeus.php';
	}
	else if (!$mxuser || !$mxuser->id)
		$templatefile=mx_option('templatedir').'/signin.php';
	else
		$templatefile=mx_option('templatedir').'/index.php';
	if (file_exists($templatefile)) include_once $templatefile;
	if (function_exists('tp_index')) tp_index();
	else echo 'No template index for '.$templatename.' ('.$templatefile.') ...';
}

function mx_content() {
	global $mxuser;
	$locale=$mxuser->locale?$mxuser->locale:'en_US';
	// message and media frame
	echo '<div id="contentframe" class="contentframe form list">';
	echo '<div></div>'; //media or message will be inserted here
	echo '</div>'; // contentframe
	echo '<div id="overlay"></div>';
	if (array_key_exists('q',$_REQUEST)) $query=mx_securestring(urldecode($_REQUEST['q']));
	if ($query) {
		$page='search';
		$option='';
		$action=$query;
	} else {
		$page=mx_secureword($_REQUEST['p']);
		if (!$page) $page='main';
		$option=mx_secureword($_REQUEST['o']);
		$action=mx_secureword($_REQUEST['a']);
	}

	// log it!
	$referer=$_SERVER['HTTP_REFERER'];
	if (preg_match('%^https?://[^.]+.musxpand.[^/]+%',$referer)>0) $referer='';
	$mxuser->logme($page,$option,$action,$referer);

	// define page and option functions
	$pagefunc='mx_mn'.$page;
	$optionfunc='mx_mn'.$option;

	if (MXDEFFEATURES & MXFTDROPMENU) {
		if (function_exists($pagefunc)) $pagefunc($page,$option,$action);
		if (function_exists($optionfunc)) $optionfunc($page,$option,$action);
		return;
	}

	if ($mxuser->id || $page!='main')
		mx_pagetitle($page,mx_pagename($page));

	if (($page=='artists' && $option=='artprof')||($page=='fans' && $option=='fanprof')) {
		$pagefunc($page,$option,$action);
		$optionfunc($page,$option,$action);
		return;
	}
	if (function_exists($pagefunc)) {
		$pagefunc($page,$option,$action);
		$ok=1;
	} else {
		mx_showhtmlpage($page);
	}
	if ($option) {
		mx_optiontitle($option,mx_optionname($page,$option));
		if (function_exists($optionfunc)) {
			$optionfunc($page,$option,$action);
			$ok=1;
		} else {
			mx_showhtmlpage($option);
		}
	}
}

function mx_metatags() {
	global $mxuser;
	$page=mx_secureword($_GET['p']);
	$option=mx_secureword($_GET['o']);
	$action=$_REQUEST['a'];
	$updtime='<meta property="og:updated_time" content="'.time().'" />';
	if ($page!='artists' && $page!='fans' && $page!='media') {
		echo '<meta property="og:title" content="MusXpand - '.mx_option('title').'" />'
		.'<meta name="title" content="MusXpand - '.mx_option('title').'" />'
		.'<meta property="og:type" content="website" />'
		.'<meta property="og:url" content="'.mx_option('basicsiteurl').'" />'
		.'<meta property="og:image" content="'.mx_option('m-logoURL-48x48').'" />'
		.'<meta property="og:site_name" content="MusXpand" />'
		.'<meta property="fb:app_id" content="'.FACEBOOK_APP_ID.'" />'
		.'<meta property="fb:admins" content="100001498041340" />'
		.$updtime
		.'<meta name="description" content="MusXpand is an open social network for artists and fans to share media, news and comments." />'
		.'<meta name="copyright" content="2010-2012 © MusXpand." />'
		.'<meta name="keywords" content="'.MXKEYWORDS.'" />'
		.'<link rel="canonical" href="http://www.example.com">';
		return;
	}
	if ($page=='artists' && $option=='artprof' && $action) {
		$user=$mxuser->getuserinfo($action);
		$artistname=mx_getartistname($user);
		$location='';
		if ($user->city) $location.=$user->city;
		if ($user->state) $location.=($location?', ':'').$user->state;
		if ($user->country) $location.=($location?', ':'').mx_getcountryname($user->country);
		if ($location) $location.='. ';
		$url=($user->username?(mx_option('basicsiteurl').'/a/'.$user->username)
			:mx_actionurl_prod($page,$option,$user->id,'','',true));
		echo '<meta property="og:title" content="'.$artistname.'" />'
		.'<meta name="title" content="'.sprintf(_('%s on MusXpand'),$artistname).'" />'
		.'<meta name="description" content="'.htmlentities($user->shortbio).' - '.$artistname.' is a MusXpand Artist." />'
		.'<meta name="copyright" content="2010-2012 © MusXpand & '.$artistname.'" />'
		.'<meta name="keywords" content="'.MXKEYWORDS.','.$artistname.'" />'
		.'<meta property="og:type" content="musxpand:artist" />' //profile
		.'<meta property="og:url" content="'.$url.'" />'
		.'<meta property="og:audio" content="'.$url.'" />'
		.'<meta property="og:audio:type" content="vnd.facebook.bridge" />'
		.'<meta property="og:image" content="'.mx_fanpic($user->id,'large').'" />'
		.'<meta property="og:site_name" content="MusXpand" />'
		.'<meta property="og:description" content="'.htmlentities($location.$user->shortbio).'" />'
		.$updtime
		//.'<meta property="musxpand:location" content="'.$location.'" />'
		.'<meta property="fb:app_id" content="'.FACEBOOK_APP_ID.'" />'
		.($user->fbid?('<meta property="fb:admins" content="'.$user->fbid.'" />'):'')
		.'<link rel="canonical" href="'.mx_actionurl_prod($page,$option,$user->id,'','',true).'">';
		return;
	} else if ($page=='account' && $option=='myartpage') {
		$artistname=$mxuser->getartistname();
		$location='';
		if ($mxuser->city) $location.=$mxuser->city;
		if ($mxuser->state) $location.=($location?', ':'').$mxuser->state;
		if ($mxuser->country) $location.=($location?', ':'').mx_getcountryname($mxuser->country);
		if ($location) $location.='. ';
		$url=($user->username?(mx_option('basicsiteurl').'/a/'.$mxuser->username)
			:mx_actionurl_prod('artists','artprof',$mxuser->id,'','',true));
		echo '<meta property="og:title" content="'.$artistname.'" />'
		.'<meta name="title" content="'.sprintf(_('%s on MusXpand'),$artistname).'" />'
		.'<meta name="description" content="'.htmlentities($mxuser->shortbio).' - '.$artistname.' is a MusXpand Artist." />'
		.'<meta name="copyright" content="2010-2012 © MusXpand & '.$artistname.'" />'
		.'<meta name="keywords" content="'.MXKEYWORDS.','.$artistname.'" />'
		.'<meta property="og:type" content="musxpand:artist" />' // profile
		.'<meta property="og:url" content="'.$url.'" />'
		.'<meta property="og:audio" content="'.$url.'" />'
		.'<meta property="og:audio:type" content="vnd.facebook.bridge" />'
		.'<meta property="og:image" content="'.mx_fanpic($mxuser->id,'large').'" />'
		.'<meta property="og:site_name" content="MusXpand" />'
		.'<meta property="og:description" content="'.htmlentities($location.$mxuser->shortbio).'" />'
		.$updtime
		//.'<meta property="musxpand:location" content="'.$location.'" />'
		.'<meta property="fb:app_id" content="'.FACEBOOK_APP_ID.'" />'
		.($mxuser->fbid?('<meta property="fb:admins" content="'.$mxuser->fbid.'" />'):'')
		.'<link rel="canonical" href="'.mx_actionurl_prod('artists','artprof',$mxuser->id,'','',true).'">';
		return;
	} else if ($page=='fans' && $option=='fanprof' && $action) {
		$user=$mxuser->getuserinfo($action);
		$fanname=mx_getname($user);
		$location='';
		if ($user->city) $location.=$user->city;
		if ($user->state) $location.=($location?', ':'').$user->state;
		if ($user->country) $location.=($location?', ':'').mx_getcountryname($user->country);
		$url=($user->username?(mx_option('basicsiteurl').'/f/'.$user->username)
			:mx_actionurl_prod($page,$option,$user->id,'','',true));
		echo '<meta property="og:title" content="'.$fanname.'" />'
		.'<meta name="title" content="'.sprintf(_('%s on MusXpand'),$fanname).'" />'
		.'<meta name="description" content="'.htmlentities($user->shortbio).' - '.$fanname.' is a MusXpand Fan." />'
		.'<meta name="copyright" content="2010-2012 © MusXpand & '.$fanname.'" />'
		.'<meta name="keywords" content="'.MXKEYWORDS.','.$fanname.'" />'
		.'<meta property="og:type" content="profile" />' // musxpand:fan
		.'<meta property="og:url" content="'.$url.'" />'
		.'<meta property="og:image" content="'.mx_fanpic($user->id,'large').'" />'
		.'<meta property="og:site_name" content="MusXpand" />'
		.'<meta property="og:description" content="'.htmlentities($user->shortbio).'" />'
		.$updtime
		.'<meta property="musxpand:location" content="'.$location.'" />'
		.'<meta property="fb:app_id" content="'.FACEBOOK_APP_ID.'" />'
		.($user->fbid?('<meta property="fb:admins" content="'.$user->fbid.'" />'):'')
		.'<link rel="canonical" href="'.mx_actionurl_prod($page,$option,$user->id,'','',true).'">';
		return;
	} else if ($page=='account' && $option=='myfanpage') {
		$fanname=$mxuser->getname();
		$location='';
		if ($mxuser->city) $location.=$mxuser->city;
		if ($mxuser->state) $location.=($location?', ':'').$mxuser->state;
		if ($mxuser->country) $location.=($location?', ':'').mx_getcountryname($mxuser->country);
		$url=($user->username?(mx_option('basicsiteurl').'/f/'.$mxuser->username)
			:mx_actionurl_prod('fans','fanprof',$mxuser->id,'','',true));
		echo '<meta property="og:title" content="'.$fanname.'" />'
		.'<meta name="title" content="'.sprintf(_('%s on MusXpand'),$fanname).'" />'
		.'<meta name="description" content="'.htmlentities($mxuser->shortbio).' - '.$fanname.' is a MusXpand Fan." />'
		.'<meta name="copyright" content="2010-2012 © MusXpand & '.$fanname.'" />'
		.'<meta name="keywords" content="'.MXKEYWORDS.','.$fanname.'" />'
		.'<meta property="og:type" content="profile" />' //musxpand:fan
		.'<meta property="og:url" content="'.$url.'" />'
		.'<meta property="og:image" content="'.mx_fanpic($mxuser->id,'large').'" />'
		.'<meta property="og:site_name" content="MusXpand" />'
		.'<meta property="og:description" content="'.htmlentities($mxuser->shortbio).'" />'
		.$updtime
		.'<meta property="musxpand:location" content="'.$location.'" />'
		.'<meta property="fb:app_id" content="'.FACEBOOK_APP_ID.'" />'
		.($mxuser->fbid?('<meta property="fb:admins" content="'.$mxuser->fbid.'" />'):'')
		.'<link rel="canonical" href="'.mx_actionurl_prod('fans','fanprof',$mxuser->id,'','',true).'">';
		return;
	} else if ($page=='media' && $option=='medprof' && $action) {
		$media=$mxuser->getmediainfo($action);
		$artistname=mx_getartistname($media);
		$fanship=$mxuser->getfanship($media->owner_id,$media->id);
		mx_medialist($media,$fanship,true);
		$mediameta='';
		$mediadesc=$media->description;
		$url=mx_option('basicsiteurl').'/m/'.$action;
		switch($media->type) {
			case MXMEDIAINSTR:
			case MXMEDIASONG:
				//$mediatype='music.song';
				$mediatype='musxpand:media';
				$mediaurl=mx_medialink($media->filename,$media->hashcode,$media->hashdir,'-preview');
				$mediameta='<meta property="music:duration" content="'.$media->duration.'" />'
				.'<meta property="og:audio" content="'.$mediaurl.'" />'
				.'<meta property="og:audio:secure_url" content="'.str_replace('http:','https:',$mediaurl).'" />'
				.'<meta property="og:audio:type" content="audio/vnd.facebook.bridge" />' //content="audio/mp3" />'
				.'<meta property="music:musician" content="'.mx_actionurl('artists','artprof',$media->owner_id).'" />'
				.'<meta property="music:album" content="'.mx_actionurl('media','medprof',$media->bundles[0]->id).'" />'
				.'<meta property="og:audio:title" content="'.$media->title.' ('._('Sample').')" />'
				.'<meta property="og:audio:artist" content="'.$artistname.'" />'
				.'<meta property="og:audio:album" content="'.htmlentities($media->bundles[0]->title).'" />'
				.'<meta property="og:video" content="'.mx_option('siteurl').'/flash/xspf_player_slim.swf?'
				.'player_title='.htmlentities('Listen to music on MusXpand')
				.'&song_url='.urlencode($mediaurl)
				.'&song_title='.htmlentities($media->title).'" />'
				.'<meta property="og:video:secure_url" content="'.mx_option('secure_siteurl').'/flash/xspf_player_slim.swf?'
				.'player_title='.htmlentities('Listen to music on MusXpand')
				.'&song_url='.urlencode($mediaurl)
				.'&song_title='.htmlentities($media->title).'" />'
				.'<meta property="og:video:height" content="17" />'
				.'<meta property="og:video:type" content="application/x-shockwave-flash" />';
				//.'<meta property="og:video:title" content="'.$media->title.' ('._('Sample').')" />'
				//.'<meta property="og:video:director" content="'.$artistname.'" />'
				//.'<meta property="og:video:album" content="'.htmlentities($media->bundles[0]->title).'" />'
				break;
			case MXMEDIABG:
			case MXMEDIAPIC:
				$mediatype='musxpand:picture';
				break;
			case MXMEDIABASEBUNDLE:
			case MXMEDIAREGULARBUNDLE:
				$mediatype='musxpand:bundle';
				/*$mediameta='<meta property="og:audio" content="'.$url.'" />'
				.'<meta property="og:audio:type" content="vnd.facebook.bridge" />';*/
				break;
			case MXMEDIAVIDEO:
				//$mediatype='musxpand:videoclip';
				$mediatype='musxpand:media';
				$mediaurl=mx_medialink($media->filename,$media->hashcode,$media->hashdir,'');
				$mediameta='<meta property="og:video" content="'.$mediaurl.'" />'
				.'<meta property="og:video:secure_url" content="'.str_replace('http:','https:',$mediaurl).'" />'
				.'<meta property="og:video:type" content="video/mp4" />';
				break;
			case MXMEDIADOC:
				$mediatype='musxpand:document';
				break;
			default:
				$mediatype='musxpand:media';
		}
		echo '<meta property="og:title" content="'.htmlentities(sprintf(_('%s by %s'),$media->title,$artistname)).'" />'
		.'<meta name="title" content="'.htmlentities(sprintf(_('%s by %s'),$media->title,$artistname)).'" />'
		.'<meta name="description" content="'.htmlentities($media->description).'" />'
		.'<meta name="copyright" content="2010-2012 © MusXpand & '.$artistname.'" />'
		.'<meta name="keywords" content="'.MXKEYWORDS.','.$artistname.','.$media->title.'" />'
		.'<meta property="og:type" content="'.$mediatype.'" />'
		.'<meta property="og:url" content="'.$url.'" />'
		.'<meta property="og:image" content="'.$media->pic.'" />'
		.$mediameta
		.'<meta property="musxpand:artist" content="'.mx_actionurl('artists','artprof',$media->owner_id).'" />'
		.($media->type!=MXMEDIAREGULARBUNDLE && $media->type!=MXMEDIABASEBUNDLE
			?('<meta property="musxpand:bundle" content="'.mx_actionurl('media','medprof',$media->bundles[0]->id).'" />'):'')
		.'<meta property="og:site_name" content="MusXpand" />'
		.'<meta property="og:description" content="'.htmlentities($mediadesc)
		//.' - '._('Full access may require you to subscribe')
		.'" />'
		.$updtime
		.'<meta property="fb:app_id" content="'.FACEBOOK_APP_ID.'" />'
		.($media->fbid?('<meta property="fb:admins" content="'.$media->fbid.'" />'):'')
		.'<link rel="canonical" href="'.mx_actionurl_prod('media','medprof',$media->id,'','',true).'">';
		return;
	}
}

function mx_sitetitle() {
	global $mxuser;
	$page=$_GET['p'];
	$option=$_GET['o'];
	$action=$_GET['a'];
	if (!$page) {
		echo 'MusXpand - '.mx_option('title');
		return;
	}
	if (!$option) {
		echo 'MusXpand - '.mx_pagename($page);
		return;
	}
	if ((($page=='artists' && $option=='artprof')
		|| ($page=='fans' && $option=='fanprof')) && $action) {
		$user=$mxuser->getuserinfo($action);
		if ($option=='fanprof') echo sprintf(_('%s - MusXpand'),mx_getname($user));
		else echo sprintf(_('%s - MusXpand'),mx_getartistname($user));
		return;
	} else if ($page=='media' && $option=='medprof') {
		$media=$mxuser->getmediainfo($action);
		echo sprintf(_('%s by %s - MusXpand'),$media->title,$media->artistname);
		return;
	}
	echo 'MusXpand - '.mx_pagename($page).' - '.mx_optionname($page,$option);
}



mx_preheader();
mx_header();
mx_body();
mx_footer();
?>

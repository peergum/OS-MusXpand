<?php
/* ---
 * Project: musxpand
 * File:    mx_functions.php
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

global $mxdb,$MXVersion,$mxuser,$privlevels,$windowedpages,$picturecache;
$windowedpages=array();
$picturecache=array(); // reuse URLs from S3

//if (!$windowedpages) $windowedpages=array();

$defaultprivacy=array('id','picture',
			'hashdir','fbid','background_id','transparency','status',
			'acctype','lastseen','username','pubcnt','pubsize',
			'gender','country','invitecode','badges',
			'artistname','firstname','shortbio',
			'city','state','country','genres','tastes'
		);

$usernameerrs=array(
	MXUNEMPTYNOCHANGE => _('Username empty or unchanged'),
	MXUNNOTLOGGED => _('User not logged in'),
	MXUNRESTRICTED => _('Restricted username'),
	MXUNONLYNUMBERS	=> _('Username cannot be a number'),
);

$defaultbackgrounds=array(
	'NYC_TotR_night-by-daniel-schwen-low.jpg',
	'Vancouver_City_Night_4_by_ajithrajeswari-low.jpg',
	//'paper-musxpand-multiple-logos.jpg',
	//'musxpand-christmas1.jpg', // boules et guirlandes
	//'musxpand-christmas2.jpg', // guido reni
	//'musxpand-christmas3.jpg', // gerard van honthorst
	//'musxpand-christmas4.jpg', // quebec
);

$sharefilters=array(
	MXSHAREALL => _('All'),
	MXSHAREFRIENDS => _('Friends'),
	MXSHAREFANS => _('Fans'),
	MXSHAREARTISTS => _('Artists'),
	MXSHARELIKERS => _('Likers'),
);

$prodtypes=array(
	MXARTSUB => array(
		_('Fanship'),
		array(
			MXSUBFOY => _('1-Year [FOY]'),
			MXSUBFOFA => _('Unlimited [FOFA]'),
			MXUPGFOFA => _('Unlimited/Upgrade [FOFA]'),
			MXSUBLIKE => _('Like/Fav')
			)
	),
	MXSITESUB => array(
		_('Monthly Subscription'),
		array(
			MXSUBFREE => _('Free/Ad-Sponsored'),
			MXSUBBASIC => _('BASIC'),
			MXSUBPLUS => _('PLUS'),
			MXSUBPREMIUM => _('PREMIUM')
		)
	),
	MXMEDSUB => array(
		_('Media Purchase'),
		array(
			MXBUYBUNDLE => _('Bundle'),
			MXBUYMEDIA => _('Single Media'),
		)
	)

);

$subtypes=array(
	//'freesub' => MXSUBFREE,
	'basicsub' => MXSUBBASIC,
	'plussub' => MXSUBPLUS,
	'premsub' => MXSUBPREMIUM
);

$subprices=array(
	'freesub' => MXFEEFREE,
	'basicsub' => MXFEEBASIC,
	'plussub' => MXFEEPLUS,
	'premsub' => MXFEEPREMIUM
);

$substatuses=array(
	MXPENDINGSUB => _('Pending'),
	MXNEWSUB => _('New'),
	MXCURRENTSUB => _('Current'),
	MXRENEWEDSUB => _('Renewed'),
	MXEXPIREDSUB => _('Expired'),
	MXENDEDSUB => _('Expired'),
	//MXNORENEWSUB => _('Auto-Renewal Cancelled'),
	);

$subrenewals=array(
	MXSUBAUTORENEW => _('Automatic'),
	MXSUBNORENEW => _('Canceled'),
	MXSUBSTOPRENEW => _('Canceling')
	);
$bools=array(_('No'),_('Yes'));

$months=array('&mdash;',_('Jan'),_('Feb'),_('Mar'),_('Apr'),_('May'),_('Jun'),
	_('Jul'),_('Aug'),_('Sep'),_('Oct'),_('Nov'),_('Dec'));

$genders=array(
	MXNOSEX => _('-'),
	MXSEXMALE => _('Male'),
	MXSEXFEMALE => _('Female')
	);

$priorities=array(
	'0' => _('low'),
	'1' => _('Med.'),
	'2' => _('High')
	);

$acctypes=array(
	MXACCOUNTUNDEFINED => _('Not yet chosen'),
	MXACCOUNTFAN => _('I\'m a Fan'),
	MXACCOUNTARTIST => _('I\'m an Artist (and a Fan)'),
	MXACCOUNTBAND => _('We\'re a Band'),
	MXACCOUNTMANAGER => _('I\'m a Manager'),
	MXACCOUNTLABEL => _('We\'re a Label'),
	MXACCOUNTVENUE => _('We\'re a Venue')
	);

$acctypesdef=array(
	MXACCOUNTFAN => array(
		_('You don\'t produce, or intend to share or sell any media on MusXpand <u>for now</u>'),
		_('<b>You won\'t upload any media, except personal pictures</b>'),
		_('You still can upgrade your account to an artist account later on'),
		_('You\'re liable for any copyright infringement'),
		sprintf(_('You agree to the above and to the %s'),mx_windowedpage('terms',_('Terms & Conditions')))
		),
	MXACCOUNTARTIST => array(
		_('<b>If you are under 18, you need to verify that you are <u>legally authorized and able to receive payments</u></b>'),
		_('<b>You will upload media you produced or whose legal rights you own</b>'),
		_('You are <u>exclusively responsible</u> of compensating any possible rights holders'
		.' (band members, covered artists, lyricist, etc...)'),
		_('<b>You will respond directly and personally to any possible lawsuit</b>, if you share any media you\'re not entitled to'),
		_('<b>You won\'t be able to switch back to a FAN account after you get any subscriber</b>'),
		sprintf(_('You agree to the above and to the %s'),mx_windowedpage('terms',_('Terms & Conditions')))
		),
	MXACCOUNTBAND => array(_('This account type is currently unavailable.')),
	MXACCOUNTMANAGER => array(_('This account type is currently unavailable.')),
	MXACCOUNTLABEL => array(_('This account type is currently unavailable.')),
	MXACCOUNTVENUE => array(
		_('This account type is currently unavailable.')
		),
);

$bandroles=array(
	'-1' => _('None'),
	'0' => _('Lead Vocals'),
	'1' => _('Vocals'),
	'2' => _('Lead Guitar'),
	'3' => _('Guitars'),
	'4' => _('Bass'),
	'5' => _('Keyboards'),
	'6' => _('Drums'),
	'7' => _('Percussions'),
	'8' => _('Background Vocals'),
	'9' => _('Programming'),
	'10' => _('Sound Engineer'),
	'11' => _('Lights'),
	'11' => _('Make-up, Hair, Manicure'),
	'12' => _('Clothes'),
	'13' => _('Graphic Designs'),
	'19' => _('Band Management'),
	'20' => _('Label Management'),
	'99' => _('Other'),
	'100' => _('All')
);

$statuses = array(
	MXACCTDISABLED => _('Disabled'),
	MXACCTUNCONFIRMED => _('Unconfirmed'),
	MXACCTEMAILCONFIRMED	=> _('Email Confirmed'),
	MXACCTSETUP	=> _('Account Configured'),
	MXACCTPRIVILEGED => _('Privileged :-)'),
	MXACCTBILLINGCONFIRMED => _('Billing Confirmed'),
	MXACCTIDCONFIRMED => _('ID Confirmed'),
	MXACCTINVESTOR	=> _('Investor'),
	MXACCTTRUSTFUL	=> _('Trustful')
	);

$mediastatuses = array(
	MXMEDIAUPLOADED => _('Uploaded'),
	MXMEDIAVALIDATED	=> _('Pending'),
	MXMEDIAREADY	=> _('Unreleased'),
	MXMEDIANEW	=> _('Bundle Maker'),
	MXMEDIAFANVISIBLE => _('Fans'),
	//MXMEDIAFANSHARED => _('Fans (DL)'),
	MXMEDIAMEMBERVISIBLE => _('Members'),
	//MXMEDIAMEMBERSHARED => _('Members (DL)'),
	MXMEDIAPUBLIC => _('Public'),
	//MXMEDIAPUBLICSHARED => _('Public (DL)'),
	MXMEDIASUSPENDED => _('Suspended'),
	MXMEDIAARCHIVED => _('Archived'),
	MXMEDIAVIRTUAL => _('Work Bundle'),
	);

$pubstatuses = array(
	MXMEDIAUPLOADED => _('No'),
	MXMEDIAVALIDATED	=> _('No'),
	MXMEDIAFANVISIBLE => _('Yes'),
	MXMEDIAFANSHARED => _('Yes'),
	MXMEDIAMEMBERVISIBLE => _('Yes'),
	MXMEDIAMEMBERSHARED => _('Yes'),
	MXMEDIAPUBLIC => _('Yes'),
	MXMEDIASUSPENDED => _('Suspended'),
	MXMEDIAARCHIVED => _('Arquived'),
	);

$privlevels=array(
	'picture' => _('Profile Picture'),
	'identity' => _('Identity'),
	'bio' => _('About/Bio'),
	'website' => _('Website'),
	'age' => _('Age'),
	'birthday' => _('Birthday'),
	'gender' => _('Gender'),
	'city' => _('City'),
	'state' => _('State'),
	'country' => _('Country'),
	'tastes' => _('Musical Tastes'),
	'fanship' => _('Fanships'),
	//'musxpace' => _('MusXpace'),
	'wall' => _('Wall Posts'),
	'stats' => _('Stats'),
	'lastseen' => _('Last Visit')
	);

$filetypes = array(
	MXMEDIASONG => _('Song'),
	MXMEDIAINSTR	=> _('Instrumental'),
	MXMEDIAVIDEO => _('Video'),
	MXMEDIAPIC	=> _('Picture'),
	'4'	=> _('Score'),
	'5'	=> _('Lyrics'),
	'6'	=> _('Letter'),
	MXMEDIABG	=> _('Background'),
	MXMEDIADOC	=> _('Other Text Document'),
	MXMEDIABASEBUNDLE => _('Work Folder'),
	MXMEDIAREGULARBUNDLE => _('Media Bundle'),
	MXMEDIAUNDEFINED => ('Undefined')
	);

$completions = array(
	MXMEDIANOSTATUS => '-',
	MXMEDIADRAFTINCOMP => _('Incomplete Draft'),
	MXMEDIADRAFTCOMP => _('Complete Draft'),
	MXMEDIADEMO => _('Demo Version'),
	MXMEDIAEXTRACT => _('Extract'),
	MXMEDIAFINAL => _('Full Version')
);

$playtypes = array(
	MXPLAYTYPEUNKNOWN => '-',
	MXPLAYTYPEFULL => 'Original',
	MXPLAYTYPEPREVIEW => 'Preview',
	);

$transparencies = array(
	'0' => _('None'),
	'10' => _('10%'),
	'25' => _('25%'),
	'50' => _('50%'),
	);

$languages = array(
	'en_US' => _('English'),
	'fr_FR' => _('French'),
	'pt_BR' => _('Portuguese'),
	);

$notifs = array(
	MXEMAILNOTIF => _('By Email'),
	'1'	=> _('None'),
	);


function mx_option($optname) {
	global $mxdb,$MXVersion,$MXRootPath,$mxuser,$transparencies;
	if (array_key_exists('a',$_REQUEST)) $action=mx_secureword($_REQUEST['a']);
	else $action='';
	switch ($optname) {
		case 'MXVersion':
			return $MXVersion;
		case 'backgroundURL':
			return $mxuser->getbackgroundurl();
		case 'transparencyURL':
			$opt=75;
			//if (!$mxuser || !$mxuser->id) return mx_option('siteurl').'/images/background/whitebg-carre.png';
			if ($mxuser && $mxuser->id && array_key_exists($mxuser->transparency,$transparencies)) $opt=100-($mxuser->transparency);
			if ($opt<50) $opt=50;
			//if ($mxuser->hasfeature(MXFTNEWLOGIN)) return mx_option('siteurl').'/images/background/panther.jpg';
			return ($opt?(mx_option('siteurl').'/images/background/white-dot-'.$opt.'.png'):'');
		case 'logoURL':
			return mx_option('siteurl').'/images/general/musxpand-logo.png';
		case 'm-logoURL':
			return mx_option('siteurl').'/images/general/m-icon-logo.png';
		case 'm-logoURL-48x48':
			return mx_option('siteurl').'/images/general/m-icon-48x48.png';
		case 'mxbannerURL':
			return mx_option('siteurl').'/images/general/musxpand-banner-printout.png';
		case 'versionlogoURL':
			return mx_option('siteurl').'/images/general/cQ-logo.png';
		case 'betalogoURL':
			return mx_option('siteurl').'/images/general/beta-2.png';
		case 'guitarlogo':
			//return mx_option('siteurl').'/images/general/musxpand-logo-with-guitar.png';
			return mx_option('siteurl').'/images/general/mx-black-150x150.png';
		case 'templateURL':
			return mx_option('siteurl').'/templates/'.mx_option('template');
		case 'template':
			if ($action=='printorder') return 'printorder';
			else if ($mxuser && (MXDEFFEATURES & MXFTDROPMENU)) return 'newmx';
			else if ($_REQUEST['signed_request'] || $_GET['canvas']) {
				if ($mxuser->fbdata['page']) return 'fb_page';
				else if ($_GET['canvas']==1) return 'facebook';
				else return 'fblike';
			}
			$opt=$mxdb->option($optname);
			return 'fblike';
			return (!$opt?'default':$opt);
		case 'templatedir':
			return $MXRootPath.'/templates/'.mx_option('template');
		case 'basicsiteurl':
			return MXSITEURL;
		case 'siteurl':
			if (array_key_exists('HTTPS',$_SERVER))
				return MXSECURESITEURL;
			return MXSITEURL;
		case 'secure_siteurl':
			return MXSECURESITEURL;
		case 'usersdir':
			return $MXRootPath.'/users';
		case 'pagesdir':
			return $MXRootPath.'/pages';
		case 'usersURL':
			return mx_option('siteurl').'/users';
		case 'rootdir':
			return $MXRootPath;
		default:
			return $mxdb->option($optname);
	}
}

function mx_proption($optname) {
	echo mx_option($optname);
}

function mx_frdonline() {
	if (!is_logged()) return;
	//echo '<div class="friends module">Online<br/>friends...</div>';
}

function mx_artonline() {
	//echo '<div class="artists module">Online<br/>artists...</div>';
}

function mx_loggedmenu() {
	echo 'Logged in.<br/>[Your options]';
}

function mx_signin() {
	echo 'Sign-in<br/>Register';
}

function is_artist() {
	global $mxuser;
	return (is_logged() && $mxuser->acctype==MXACCOUNTARTIST)?true:false;
}

function is_privileged() {
	global $mxuser;
	return (is_logged() && $mxuser->status>=MXACCTPRIVILEGED)?true:false;
}

function is_pseudoadmin() {
	global $mxuser;
	return (is_logged() && $mxuser->status==MXACCTPSEUDOADMIN)?true:false;
}

function is_pageadmin() {
	global $mxuser;
	return ($mxuser->fbdata['page']['admin']==1)?true:false;
}

function is_pagelike() {
	global $mxuser;
	return ($mxuser->fbdata['page']['liked']==1)?true:false;
}

function is_valid($sub) { // check subscription validity
	if (($sub->subtype==MXSUBFOY && $sub->expiry && strtotime($sub->expiry)>time())
	|| $sub->subtype==MXSUBFOFA || $sub->subtype==MXUPGFOFA) return true;
	return false;
}

function is_admin() {
	global $mxuser;
	return (is_logged() && $mxuser->status==MXACCTTRUSTFUL)?true:false;
}

function is_confirmed() {
	global $mxuser;
	return (is_logged() && $mxuser->status >= MXACCTEMAILCONFIRMED)?true:false;
}

function is_setup() {
	global $mxuser;
	return (is_logged() && $mxuser->status >= MXACCTSETUP)?true:false;
}

function is_logged() {
	global $me,$mxsession,$mxuser;
	return ($mxuser && $mxuser->id>0);
	//return (is_array($me) || $mxsession);

}

function get_FB_cookie($app_id, $application_secret) {
  $args = array();
  parse_str(trim($_COOKIE['fbs_' . $app_id], '\\"'), $args);
  ksort($args);
  $payload = '';
  foreach ($args as $key => $value) {
    if ($key != 'sig') {
      $payload .= $key . '=' . $value;
    }
  }
  if (md5($payload . $application_secret) != $args['sig']) {
    return null;
  }
  return $args;
}

function mx_warning($msg) {
	echo mx_warningstr($msg);
}
function mx_warningstr($msg) {
	return '<div class="warning"><table><tr><td>'.$msg.'</td></tr></table></div>';
}

function mx_important($msg) {
	echo mx_importantstr($msg);
}

function mx_importantstr($msg) {
	return '<div class="important"><table><tr><td>'.$msg.'</td></tr></table></div>';
}

function mx_infomsg($msg) {
	echo mx_infomsgstr($msg);
}

function mx_infomsgstr($msg) {
	return '<div class="infomsg"><table><tr><td>'.$msg.'</td></tr></table></div>';
}

function mx_pagetitle($page,$title) {
	global $mxuser;
	if (MXDEFFEATURES & MXFTDROPMENU) return;
	$option=mx_secureword($_GET['o']);
	echo "<h2><span class='headericon'>".(file_exists(mx_iconfile($page))?mx_icon($page):'').'</span>'.$title;
	$back=_('Back');
	$backtxt=$back.mx_icon('back',$back,'24px');
	if ($page!='' && $page!='main' && $page!='fblikeus') {
		if ($option && $mxuser->id) echo '<div class="backonelevel"><a href="'.mx_pageurl($page).'" alt="'.$back.'">'.$backtxt.'</a></div>';
		else echo '<div class="backonelevel"><a href="'.mx_pageurl('main').'" alt="'.$back.'">'.$backtxt.'</a></div>';
	}
	echo '</h2>';
}

function mx_optiontitle($option,$title) {
	global $mxuser;
	if (MXDEFFEATURES & MXFTDROPMENU) return;
	echo "<h3><span class='headericon'>".(file_exists(mx_iconfile($option))?mx_icon($option,$title,'16px'):'')."</span>$title</h3>";
}

function mx_optionsubtitle($title) {
	global $mxuser;
	if (MXDEFFEATURES & MXFTDROPMENU) return;
	echo "<h4>$title</h4>";
}

function mx_iconfile($name) {
	return mx_option('templatedir').'/icons/'.$name.'.png';
}

function mx_iconurl($name,$id='') {
	if (!preg_match('%\.[^.]+$%',$name)) $name.='.png';
	return (($id && false)?(mx_option('siteurl').'/images/icons/'):(mx_option('templateURL').'/icons/'))
		.$name;
}

function mx_likeicon($id,$type,$mylikes,$uid) {
	switch($type) {
		case MXLIKEIT:
			$icon='likes';
			$txt=_('Like');
			$prf='l';
			break;
		case MXDISLIKEIT:
			$icon='dislikes';
			$txt=_('Dislike');
			$prf='d';
			break;
	}
	if ($type!=$mylikes) { // includes when mylikes is NULL
		$norm='no';
		$hover='';
	} else {
		$norm='';
		$hover='no';
	}
	$str='<img name="'.$prf.'i_'.$id.'" src="'.mx_iconurl($norm.$icon).'"'
		.' alt="'.$txt.'"';
	$str.= ' onmouseover="this.src=\''.mx_iconurl($hover.$icon).'\';"';
	$str.= ' onmouseout="this.src=\''.mx_iconurl($norm.$icon).'\';"';
	$str.= ' onclick="javascript:likeclick(\''.$id.'\',\''.$uid.'\',\''.$prf.'\','
		.($mylikes?$mylikes:0).');"';
	$str.= ' />';
	return $str;
}

function mx_icon($name,$alt='',$height='',$id='',$hover='',$mode='id') {
	$idstr=($id)?(' '.$mode.'="'.$id.'"'):'';
	$str='<img'.$idstr.' src="'.mx_iconurl($name,$id).'"'.
			($height?(' height="'.$height.'"'):'') .
			($alt?(' title="'.$alt.'"'):'');
	if ($hover) {
		$str.= ' onmouseover="if (!this.name) this.src=\''.mx_iconurl($hover,$id).'\';"';
		$str.= ' onmouseout="if (!this.name) this.src=\''.mx_iconurl($name,$id).'\';"';
		$str.= ' onclick="javascript:iconclick(\''.$id.'\',\''.$name.'\',\''.$hover.'\');"';
	}
	$str.= '/>';
	return $str;
}

function mx_sharebuttons($buttid,$url,$pic,$desc) {
	return '';
	$sharebutton='<g:plusone size="medium" href="'.$url.'"></g:plusone>';
	$sharebutton.='<div class="fb-like" data-href="'
	.$url
	.'" data-send="true" data-layout="box_count" data-width="" data-show-faces="false" data-font="verdana"></div>';
	/*
	$sharebutton.='<a href="http://pinterest.com/pin/create/button/?url='.urlencode($url)
	.'&media='.urlencode($pic).'&description='.htmlspecialchars($description)
	.'" class="pin-it-button" count-layout="horizontal" target=_blank><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>';
	*/
	$sharebutton=mx_icon('share',_('Share'),19,'sh_'.$buttid,'sharehover')
	.'<div class="mxshare share_'.$buttid.'"><div class="mxsharebox"><div class="mxsharetitle">'._('Share')
	.'<div class="mxshareclose" onclick="javascript:iconclick(\'ush_'.$buttid.'\',\'\',\'\');"> X </div></div>'
	.'<div class="mxsharebuttons">'.$sharebutton.'</div></div></div>';
	return $sharebutton;
}

function mx_sharefilter($value) {
	global $sharefilters;
	$str='';
	if (!$value) {
		$str=$sharefilters[MXSHAREALL];
	} else {
		foreach($sharefilters as $k => $desc) {
			if ($k & $value) {
				if ($str) $str.=', ';
				$str.=$desc;
			}
		}
	}
	return $str;
}

function mx_difftime($adate) {
	global $mxuser;
	if (!$adate) return _('Never?!');
	date_default_timezone_set($mxuser->timezone);
	$utc=timezone_open('UTC');
	$tz=timezone_open($mxuser->timezone);
	if (preg_match('%^[0-9]+-[0-9]+-[0-9]+$%',$adate)) {
		$dtthen=date_create($adate,$tz);
	} else {
		$dtthen=date_create($adate,$utc);
	}
	if (!preg_match('%[0-9]+:[0-9]+%',$adate)) return date_format($dtthen->setTimezone($tz),'d M Y');
	$dtnow=date_create('now',$utc);
	$dtnow->setTimezone($tz);
	$dft=date_diff($dtthen,$dtnow);
	$dtthen->setTimezone($tz);
	//error_log('difftime: dtthen='.$dtthen->format('d M Y, H:i.' / dtnow=')
	if ($dft->days>=7) return str_replace(', 00:00','',date_format($dtthen,'d-M-Y, H:i'));
	else if ($dft->days>=1) return date_format($dtthen,'D, H:i');
	//else if ($dft->days==1) return sprintf(_('Yesterday, %s'),date_format($dtthen,'H:i'));
	else if ($dft->h>0) return sprintf(_('%sh ago'),$dft->h);
	else if ($dft->i>0) return sprintf(_('%smin ago'),$dft->i);
	else if ($dft->s>0) return sprintf(_('%ss ago'),$dft->s);
	else return _('right now');
}

function mx_size($value) {
	if ($value>=109951162777) {
		$size=$value/1099511627776;
		$unit=_('TB');
	} else if ($value>=107374182) {
		$size=$value/1073741824;
		$unit=_('GB');
	} else if ($value>=104857){
		$size=$value/1048576;
		$unit=_('MB');
	} else if ($value>=102) {
		$size=$value/1024;
		$unit=_('KB');
	} else {
		$size=$value;
		$unit=_('B');
	}
	return sprintf('%.1f%s',$size,$unit);
}

function mx_infofield($field,$value,$fldinfo) {
	global $mxdb,$mxuser,$defaultprivacy;
	if (is_array($fldinfo)) $fldtype=$fldinfo[2];
	else $fldtype=$fldinfo;
	//error_log('infofield: '.$fldtype.' '.print_r($value,true));
	switch ($fldtype) {
		case 'percent':
			return ($value>100?100:floor($value)).'%';
		case 'playtime':
			$hr=floor($value/3600);
			$mn=floor(($value-3600*$hr)/60);
			$sc=$value-3600*hr-60*$mn;
			return ($hr?($hr.':'):'').sprintf('%02d:%02d',$mn,$sc);
		case 'mediaplay':
			$media=$mxuser->getmediainfo($value);
			$fanship=$mxuser->getfanship($media->owner_id,$media->id);
			mx_medialist($media,$fanship);
			$str=$media->mediadatalight;
			$str=str_replace('{PRICE}','<table class="buymedia"><tr><td>',$str);
			$str=str_replace('{PRICE2}','</td><td>',$str);
			$str=str_replace('{PRICE3}','</td></tr></table>',$str);
			return $str;
			break;
		case 'playtype':
			global $playtypes;
			return $playtypes[$value+0];
			break;
		case 'genre':
			$genres=$mxdb->listgenres();
			$cats=array();
			$subgenres=array();
			foreach ($genres as $genre){
				if (!$genre->cat) {
					$cats[$genre->hash]=$genre->id;
					$subgenres[$genre->id]=array();
				}
			}
			foreach ($genres as $genre){
				if ($genre->cat) {
					$subgenres[$cats[$genre->cat]][]=$genre->id;
					$catgenre[$genre->id]=$cats[$genre->cat];
				}
			}
			$str='<table class="genres">';
			$ok=0;
			for ($i=0; $i<5; $i++) {
				$genre=$value[$i];
				if ($genre) {
					$cat=$catgenre[$genre];
					$str.='<tr><th>'.(!$i?_('Primary'):($i==1?_('Secondary'):_('Other'))).':</th><td>'.$genres[$genre]->genre
					.($cat?(' ('.$genres[$cat]->genre.')'):'').'</td></tr>';
					$ok=1;
				}
			}
			$str.='</table>';
			if (!$ok) return _('Not informed');
			return $str;
			break;
		case 'pagepic':
			if (!$value) return '';
			return '<img class="fanpic" src="'.$value.'">';
			break;
		case 'size':
			return mx_size($value);
			break;
		case 'invites':
			return sprintf(_('So far, %d of your referrees registered'),$value);
			break;
		case 'agreement':
			if ($value=="0000-00-00 00:00:00")
				return sprintf(_('%s not yet accepted.'),mx_windowedpage('terms',_('Terms & Conditions')));
			return sprintf(_('%s Accepted: %s.'),mx_windowedpage('terms',_('Terms & Conditions')),mx_difftime($value));
			break;
		case 'proid':
			if ($value==MXNOPROYET) return _('Not Affiliated Yet');
			if (!$value) return _('Undefined');
			$pro=$mxdb->listpros($value);
			if ($pro[0]) return $pro[0]->name.' ('.$pro[0]->website.')';
			else return _('Undefined');
			break;
		case 'editbutton':
			return '<input type="'.($field=='submit'?'submit':($field=='clear'?'reset':'button')).'"'
				.' name="'.$field.'" value="'.$value.'"'
				.' onclick="if (buttonclick(\''.$field.'\')) submit();"'
				.(strpos($field,'-')===0?' disabled':'').'>';
			break;
		case 'mediabtns':
			//$str='<div class="media">';
			//$str.='<div class="icons">'.
			$str=mx_icon('playmedia',_('Play'),24,'pm_'.$value,'pausemedia')
			.'&nbsp;'
			.mx_icon('downmedia',_('Download'),24,'dm_'.$value,'downmedia_active')
			.mx_icon('addmedia',_('Add'),24,'am_'.$value,'addedmedia');
			return $str;
			break;
		case 'mediainfo':
			//$str='<div class="media">';
			//$str.='<div class="icons">'.
			$str=mx_icon('infomedia',_('Info'),16,'im_'.$value,'infomedia_down');
			return $str;
			break;
		case 'wall':
			$userinfo=$mxuser->getuserinfo($value->authid);
			$str = '<div class="wall wid_'.$value->msgid.'">';
			$str.= '<div class="wauth">';
			/*$str.= '<img class="wallpic" src="'.mx_fanpic($value->authid,'square').'" />'
			.' '.mx_getartistname($userinfo); */
			$str.= '<table><tr><td class="subline"><img class="wallpic" src="'.mx_fanpic($value->authid,'square').'" /></td>'
				.'<td>';
			if ($value->type=='artist') {
				$name=mx_getartistname($userinfo);
				$str.=	'<a href="'.mx_actionurl('artists','artprof',$userinfo->id).'" alt="'.$name.'">'
				.$name.'</a>';
			} else {
				$name=mx_getname($userinfo);
				$str.= '<a href="'.mx_actionurl('fans','fanprof',$userinfo->id).'" alt="'.$name.'">'
					.$name.'</a>';
			}
			$str.= '</td></tr></table>';
			$str.= '</div>';
			//$filters=str_replace(', ','<br/>',mx_sharefilter($value->filter));
			$filters=mx_sharefilter($value->filter);
			if ($value->flags & MXWALLDELETED) $str.='<div class="canwall">'._('DELETED').'</div>';
			else $str.= '<div class="wfilter">'.$filters.'</div>';
			$str.= '<div class="wdate">'.mx_difftime($value->date).'</div>';
			$str.= '<div class="wbody">';
			if ($value->refid) {
				$str.='<div class="refwall">Ref. Wall#'.$value->refid._(' (will be shown later)').'</div><br/>';
			}
			$body= mx_urls2anchors(htmlspecialchars($value->body));
			$str.=preg_replace('%\n%','<br/>',$body);
			$str.= '</div>'; // class body
			if ($mxuser->id) {
				if ($value->authid==$mxuser->id) {
					$str.= '<div class="wdelete" onclick="javascript:deletewall(\''.$value->msgid.'\');">';
					$str.= _('Delete');
					$str.= '</div>'; // wdelete
				} else {
					$str.= '<div class="wreport">';
					$str.= _('Flag This');
					$str.= '</div>'; // wreport
				}
				$str.= '<div class="wcomments" onclick="javascript:switchcomments(\''.$value->type.'\',\''.$value->msgid.'\');">';
				if (!$value->comments) $str.= _('Any comment?');
				else $str.=sprintf(($value->comments==1)?_('%d comment.'):_('%d comments.'),
					$value->comments);
				$str.= '</div>'; // wcomments
				$str.= '<div class="wlikes">';
				$str.= mx_likeicon($value->msgid,MXLIKEIT,$value->mylikes,$mxuser->id)
				.'<div name="ln_'.$value->msgid.'">'
				.sprintf('%d',($value->likes?$value->likes:0)).'</div>'
				.mx_likeicon($value->msgid,MXDISLIKEIT,$value->mylikes,$mxuser->id)
				.'<div name="dn_'.$value->msgid.'">'
				.sprintf('%d',($value->dislikes?$value->dislikes:0)).'</div>';
				$str.='</div>'; // wlikes
				$str.='<div class="wcommentspanel" id="'.$value->type.'cm_'.$value->msgid.'">';
				$str.='</div>';
			}
			$str.= '</div>'; // class wall
			return $str;
			break;
		case 'fan':
			$userinfo=$mxuser->getuserinfo($value);
			$name=mx_getname($userinfo);
			/*return '<div class="subline"><img class="subpic" src="'.mx_fanpic($userinfo->id).'" />'
				.'<a href="'.mx_actionurl('artists','artprof',$userinfo->id).'" alt="'.$name.'">'
				.$name
				.'</a>'
				.'</div>';*/
			return '<table class="name"><tr><td class="subline"><img tag="'.$userinfo->id.'" class="subpic" src="'.mx_fanpic($userinfo->id,'square',$userinfo->gender)
				.'" itemprop="image" /></td>'
				.'<td><a href="'.mx_actionurl('fans','fanprof',$userinfo->id).'" alt="'.$name.'" itemprop="url">'
				.'<span itemprop="name">'.$name.'</span>'
				.'</a>'
				.'</td></tr></table>';
			break;
		case 'artist':
			$userinfo=$mxuser->getuserinfo($value);
			$name=mx_getartistname($userinfo);
			/*return '<div class="subline"><img class="subpic" src="'.mx_fanpic($userinfo->id).'" />'
				.'<a href="'.mx_actionurl('artists','artprof',$userinfo->id).'" alt="'.$name.'">'
				.$name
				.'</a>'
				.'</div>';*/
			return '<table class="name"><tr><td class="subline"><img tag="'.$userinfo->id.'" class="subpic" src="'.mx_artpic($userinfo->id,'square',$userinfo->gender)
				.'" itemprop="image" /></td>'
				.'<td><a href="'.mx_actionurl('artists','artprof',$userinfo->id).'" alt="'.$name.'" itemprop="url">'
				.'<span itemprop="name">'.$name.'</span>'
				.'</a></td></tr></table>';
			break;
		case 'subcat': // subscriptions types only
			global $prodtypes;
			return $prodtypes[$value][0];
			break;
		case 'subtype': // subscriptions types only
			global $prodtypes;
			return $prodtypes[MXARTSUB][1][$value];
			break;
		case 'newsubtype': // subscriptions types only
			global $prodtypes;
			return $prodtypes[$value['subcat']][1][$value['subtype']];
			break;
		case 'substatus': // subscriptions statuses
			global $substatuses;
			$status=$substatuses[$value];
			if ($value==MXNEWSUB || $value==MXRENEWEDSUB)
				$status='<span class="newsub">'.$status.'</span>';
			return $status;
			break;
		case 'subrenewal': // subscriptions statuses
			global $subrenewals;
			if (!$value) {
				return MXNONAPPLICABLE;
			}
			$status=$subrenewals[$value];
			//if ($value==MXNEWSUB || $value==MXRENEWEDSUB)
			//	$status='<span class="newsub">'.$status.'</span>';
			return $status;
			break;
		case 'hidden':
			return '[invisible:'.$field.']';
			break;
		case 'price':
			return str_replace(' ','&nbsp;',sprintf('US$ %6.2f',$value));
		/*
		case 'prodtype':
			global $prodtypes;
			return $prodtypes[$value][0];
		case 'prodvar':
			global $prodtypes;
			return $prodtypes[$value[0]][0];
		case 'artistid':
			$user=$mxuser->getuserinfo($value);
			return mx_getartistname($user);
		*/
		case 'hiddenmemo':
			return '<textarea id="'.$field.'" rows="'.$fldinfo[3].'" cols="50" name="'.$field.'"' .
			' disabled>'.($value?$value:_('No history available')).
			'</textarea>';
			break;
		case 'html':
			return $value;
		case 'text':
			return $value?$value:'<i>'._('Undefined').'</i>';
			break;
		case 'friends':
			return $value?$value:'<i>'._('Undefined').'</i>';
			break;
		case 'quote':
			return '<span class="codinoma">'.$value.'</span>';
			break;
		case 'password':
			$str=($value!='')?('['.$value.'] ('._('generated').')'):str_repeat('&#149;',12);
			return $str;
			break;
		case 'integer':
			return $value;
			break;
		case 'boolean':
			global $bools;
			return $bools[$value];
			break;
		case 'memo':
			$value=htmlspecialchars($value);
			$value=mx_urls2anchors($value);
			/*$value=preg_replace('%(https?://([^ \n,.]|(\.[a-zA-Z0-9]))+)([ .,\n]|$)%i',
			'<a href="\1" target="_blank">\1</a>\4',$value);*/
			$value=preg_replace('%\n%','<br/>',$value);
			return $value;
			break;
		case 'simplememo':
			$value=htmlspecialchars($value);
			$value=preg_replace('%\n%','<br/>',$value);
			return $value;
			break;
		case 'update':
			$str=_('Updated:<br/>');
			if (!$value || $value=='0000-00-00') return $str._('Unspecified');
			return $str.mx_difftime($value);
			break;
		case 'date':
		case 'timestamp':
			if (!$value || $value=='0000-00-00') return _('Unspecified');
			return mx_difftime($value);
			break;
		case 'expdate':
			if ($value=='0000-00-00') return _('Unspecified');
			if ($value=='9999-01-01') return _('Never');
			if (!$value) return _('None');
			preg_match_all('%([0-9]+)%',$value,$datevalues);
			$datefields=mx_infofield($field.'_d',$datevalues[1][2],array(1,$datevalues[1][2],'integer',2)).
			'-'.mx_infofield($field.'_m',$datevalues[1][1],array(1,$datevalues[1][1],'month')).
			'-'.mx_infofield($field.'_y',$datevalues[1][0],array(1,$datevalues[1][0],'integer',4));
			if ($fldtype=='expdate' && $value!='9999-01-01') {
				$vtime=strtotime($value);
				if ($vtime>time() && $vtime<time()+86400*7)
					return '<span class="expiring">'.$datefields.'</span>';
				if ($vtime<time())
					return '<span class="expired">'.$datefields.'</span>';
			}
			return $datefields;
			break;
		case 'month':
			global $months;
			return $months[$value+0];
			break;
		case 'locale':
			global $languages;
			return $languages[$value];
			break;
		case 'gender':
			global $genders;
			return $genders[$value];
			break;
		case 'url':
			$value=mx_urls($value);
			return $value;
			break;
		case 'submit':
			return '['.$value.']';
			break;
		case 'reset':
			return '['.$value.']';
			break;
		case 'acctype':
			global $acctypes;
			return $acctypes[$value];
			break;
		case 'status':
			global $statuses;
			return $statuses[$value];
			break;
		case 'island':
			$islcnt=$mxdb->islcnt($value);
			if ($value==0) return sprintf(_('Nowhere Island [%d souls]'),$islcnt);
			break;
		case 'archipelago':
			$archicnt=$mxdb->archicnt($value);
			if ($value==0) return sprintf(_('Middle Of The Sea [%d souls]'),$archicnt);
			break;
		case 'privacy':
			global $privlevels;
			$col=0;
			$privfield='<table class="privacy"><tr>';
			foreach ($privlevels as $lname => $ldesc) {
				$privfield.='<td>';
				if (array_search($lname,explode(',',$value))!==false
				|| array_search($lname, $defaultprivacy))
					$privfield.='<span class="priv">'.$ldesc
					.'</span>'
					.(array_search($lname, $defaultprivacy)?'<span class="mandatory"><sup>*</sup></span>':'');
				else $privfield.='<span class="nopriv">'.$ldesc.'</span>';
				$privfield.='</td>';
				$col=(++$col % 4);
				if (!$col) $privfield.='</tr><tr>';
			}
			$privfield.='</tr></table>';
			$privfield.='<span class="mandatoryhelp"><sup>*</sup>'._('This information is always visible').'</span>';
			return $privfield;
			break;
		case 'filetype':
			global $filetypes;
			$ft=$filetypes[$value];
			$ftfile='/icons/mediatype_'.$value.'.png';
			if (file_exists(mx_option('templatedir').$ftfile))
				return '<img class="mediatype" src="'.mx_option('templateURL').$ftfile.'"' .
						' alt="'.$ft.'"/>';
			return $ft;
			break;
		case 'mediatype':
			global $filetypes;
			$ft=$filetypes[$value];
			return $ft;
			break;
		case 'completion':
			global $completions;
			if (!$value) $value=MXMEDIANOSTATUS;
			return $completions[$value];
			break;
		case 'mediastatus':
			global $mediastatuses;
			return $mediastatuses[$value];
			break;
		case 'pubstatus':
			global $pubstatuses;
			return $pubstatuses[$value];
			break;
		case 'media':
			global $filetypes;
			switch($fldinfo[3]) {
				case MXMEDIAPIC:
				case MXMEDIABG:
					$w=$fldinfo[4]['video']['resolution_x'];
					$h=$fldinfo[4]['video']['resolution_y'];
					if (!$w || $w>320) { $w='320'; $h=''; }
					return '<img src="'.$value.'"'.($w?(' width="'.$w.'"'):'').
						($h?(' height="'.$h.'"'):'').'>';
				case MXMEDIAINSTR:
				case MXMEDIASONG:
					return mx_soundplayerbutton($value,$fldinfo[6]).mx_soundplayertrack($value,$fldinfo[6]);
				case MXMEDIAVIDEO:
					return mx_videoplayerbutton($value,$fldinfo[6])
						.mx_videoplayertrack($value,$fldinfo[6],$fldinfo[4],$fldinfo[5]);
				default:
					return '(Media type not yet handled)';
			}
			break;
		case 'array':
			return mx_arraytotable($value);
		case 'background':
			$bg=$mxuser->getbackground($value);
			if (!$bg) $bgpic=_('Standard');
			else $bgpic=$bg->title;
			return $bgpic.'<div class="bgtest"><img id="bgpic" src="'.
				$mxuser->getbackgroundurl($value).'" /></div>';
		case 'transparency':
			global $transparencies;
			return $transparencies[$value];
			break;
		case 'bandrole':
			global $bandroles;
			if ($value==null || !array_key_exists($value,$bandroles)) $value=MXBANDROLEOTHER;
			return $bandroles[$value];
			break;
		case 'priority':
			global $priorities;
			if ($value==null || !array_key_exists($value,$priorities)) $value=MXLOW;
			return $priorities[$value];
			break;
		/*case 'timestamp':
			$today=date("%Y-m-d%");
			$yesterday=date("%Y-m-d%",time()-86400);
			$value=preg_replace($today,_('Today'),$value);
			$value=preg_replace($yesterday,_('Yesterday'),$value);
			return $value;
			break;*/
		case 'picture':
			//return '<img src="'.mx_fanpic($mxuser->id,'large',$mxuser->gender,is_artist()).'"/>';
			if (!$value) return '';
			if (!is_object($value)) return '<img class="dirpic" src="'.$value.'"/>';
			else return '<img class="'.($value->type=='person'?'dirpic':'bundlepic').'" tag="'.$value->id
			.'" src="'.$value->pic.'"/>';
			break;
		case 'dragdroppic':
			//return '<img src="'.mx_fanpic($mxuser->id,'large',$mxuser->gender,is_artist()).'"/>';
			if (!$value) return '';
			return '<img class="dirpic'
			.(($value->type==MXMEDIAPIC || $value->type==MXMEDIABG)?(' dragpic'):(' droppic'))
			.'" tag="'.$value->id.'" src="'.$value->pic.'"/>';
			break;
		case 'actions':
			$str='<div class="actionmenu"><ul>';
			if ($value) {
				foreach ($value as $vaction) {
					$str.='<li><a href="'.$vaction[1].'" alt="'.$vaction[0].'">'.$vaction[0].'</a></li>';
				}
			} else {
				$str.='<li class="inactive"><span class="inactive">'
				._('No action').'</span></li>';
			}
			$str.='</ul></div>';
			return $str;
			break;
		case 'msgfld':
			return $value.'<div class="hidden" id="'.$field.'">'.$value.'</div>';
			break;
		case 'msgflags':
			$str='';
			if ($value & MXFRIENDREQUEST) $str.=_('Friendship');
			return ($str?$str:_('Message'));
			break;
		case 'notif':
			global $notifs;
			return $notifs[$value?$value:MXEMAILNOTIF];
			break;
		default:
			return mx_infofield($field,$value,'text');
			break;
	}
}

function mx_arraytotable($value) {
	$arrstr='<table class="subform">';
			foreach ($value as $vkey => $vval) {
				$arrstr.='<tr><td>'.$vkey.'</td><td>';
				if (!is_array($vval)) $arrstr.=$vval;
				else $arrstr.=mx_arraytotable($vval);
				$arrstr.='</td></tr>';
			}
			$arrstr.='</table>';
			return $arrstr;
}


function mx_formfield($field,$value,$fldinfo) {
	$itemhelp='';
	if (is_array($fldinfo) && $fldinfo[4]) $itemhelp=mx_fieldhelp($fldinfo[1],$fldinfo[4]);
	return mx_formfieldfld($field,$value,$fldinfo).$itemhelp;
}

function mx_formfieldfld($field,$value,$fldinfo) {
	global $mxdb,$mxuser,$facebook,$me,$defaultprivacy,$genresdefined;
	if (is_array($fldinfo)) $fldtype=$fldinfo[2];
	else $fldtype=$fldinfo;
	switch ($fldtype) {
		case 'captcha':
			require_once('ext_includes/recaptchalib.php');
			$str=recaptcha_get_html(MX_RECAPTCHA_PUBLIC).'<input type="hidden" name="'.$field.'" value="1">';
			return $str;
			break;
		case 'g-button':
			$str='<input type="image" name="'.$field.'" value="'.$value.'" src="'.mx_iconurl($fldinfo[3])
			.'" onmouseover="this.src=\''.mx_iconurl($fldinfo[3].'hover').'\';"'
			.' onmouseout="this.src=\''.mx_iconurl($fldinfo[3]).'\';"'
			.' onclick="return buttonclick(\''.$field.'\');">';
			return $str;
			break;
		case 'genre':
			$genres=$mxdb->listgenres();
			//die(print_r($genres,true));
			$cats=array();
			$subgenres=array();
			foreach ($genres as $genre){
				if (!$genre->cat) {
					$cats[$genre->hash]=$genre->id;
					$subgenres[$genre->id]=array();
				}
			}
			foreach ($genres as $genre){
				if ($genre->cat) {
					$subgenres[$cats[$genre->cat]][]=$genre->id;
					$catgenre[$genre->id]=$cats[$genre->cat];
				}
			}
			if (!$genresdefined) {
				$str='<script language="javascript">'.CRLF;
				$str.='var cats=new Array();'.CRLF;
				$str.='var subcats=new Array();'.CRLF;
				$str.='var subcatsndx=new Array();'.CRLF;
				foreach($cats as $cat) {
					$n=1;
					$str.='cats['.$cat.']=\''.$genres[$cat]->genre.'\';'.CRLF;
					$str.='subcats['.$cat.']=new Array();'.CRLF;
					$str.='subcatsndx['.$cat.']=new Array();'.CRLF;
					$str.='subcats['.$cat.'][0]=\''._('Any').'\';'.CRLF;
					$str.='subcatsndx['.$cat.'][0]='.$cat.';'.CRLF;
					foreach($subgenres[$cat] as $subcat) {
						$str.='subcats['.$cat.']['.$n.']=\''.$genres[$subcat]->genre.'\';'.CRLF;
						$str.='subcatsndx['.$cat.']['.$n++.']=\''.$subcat.'\';'.CRLF;
					}
				}
				$str.='</script>'.CRLF;
				$genresdefined=true;
			}
			$str.='<table class="genres">';
			$str.='<tr><th></th><th>'._('Category').'</th><th>'._('Subcategory').'</th></tr>';
			for($i=0;$i<5;$i++) {
				if ($value[$i] && $genres[$value[$i]]->cat) {
					$cat=$catgenre[$value[$i]];
				} else {
					$cat=$value[$i];
				}
				$subcat=$value[$i];
				$str.='<tr><th>'.($i==0?_('Primary'):($i==1?_('Secondary'):_('Other'))).':</th>';
				$str.='<td>';
				$str.='<select name="'.$field.'_cat['.$i.']" onchange="javascript:showcats(\''.$field.'_'.$i.'\',this.value);">';
				$str.='<option value="0"'.(!$value[$i]?' selected':'').'>'._('None').'</option>';
				foreach ($cats as $acat) {
					$str.='<option value="'.$acat.'"'.($acat==$cat?' selected':'').'>'.$genres[$acat]->genre.'</option>'; // ('.$genres[$cat]->wiki.')</option>';
				}
				$str.='</select></td>';
				$str.='<td><select id="'.$field.'_'.$i.'" name="'.$field.'['.$i.']">'; //onchange="javascript:showpro(this.value);">';
				if (!$value[$i]) $str.='<option value="0" selected>'._('None').'</option>';
				else {
					$str.='<option value="'.$cat.'"'.($value[$i]==$cat?' selected':'').'>'._('Any').'</option>';
					foreach ($subgenres[$cat] as $sub) {
						$str.='<option value="'.$sub.'"'.($value[$i]==$sub?' selected':'').'>'.$genres[$sub]->genre.'</option>'; // ('.$genres[$cat]->wiki.')</option>';
					}
				}
				$str.='</select>';
				$str.='</td></tr>';
			}
			$str.='</table>';
			return $str;
			break;
		case 'bundle':
			$str='<select name="'.$field.'">';
			foreach($value as $bid => $title) {
				$str.='<option value="'.$bid.'">'.$title.'</option>';
			}
			$str.='</select>';
			return $str;
			break;
		case 'transactionid': // for the cart
			$str=$value;
			$str.='<input type="hidden" name="'.$field.'" value="'.$value.'">';
			return $str;
			break;
		case 'proid':
			if (!$value) $value=-1;
			$pros=$mxdb->listpros();
			$str='<select name="'.$field.'" onchange="javascript:showpro(this.value);">';
			$str.='<option value="-1"'.($value==-1?' selected':'').'>'._('* Not Affiliated Yet').'</option>';
			$def=' selected';
			foreach ($pros as $i => $pro){
				if ($value==$pro->id) $def='';
				$str.='<option value="'.$pro->id.'"'.($value==$pro->id?' selected':'').'>'.$pro->name.' ('.$pro->website.')</option>';
			}
			if ($value==-1) $def='';
			$str.='<option value="0"'.$def.'>'._('* Not Listed, add below').'</option>'; // default if no other selected
			$str.='</select>';
			$str.='<div class="newpro" id="newpro"'.($def?' style="display:block;"':'').'><table class="protable">';
			$str.='<tr><th>'._('PRO Name:').'</th><td><input name="proname" type="text" value="'.mx_securestring($_REQUEST['proname']).'" size="40"></td></tr>';
			$str.='<tr><th>'._('PRO Website:').'</th><td><input name="prosite" type="text" value="'.mx_securestring($_REQUEST['prosite']).'" size="60"></td></tr>';
			$str.='</table>';
			$str.='</div>';
			return $str;
			break;
		case 'acctype':
			global $acctypes,$acctypesdef;
			if ($value!=MXACCOUNTUNDEFINED && $mxuser->status!=MXACCTEMAILCONFIRMED && $value!=MXACCOUNTFAN) {
				return $acctypes[$value];
			}
			$accfield='';
			$accfield.='<dl>';
			foreach ($acctypes as $i => $acctype) {
				if ($i == MXACCOUNTUNDEFINED || ($value==MXACCOUNTUNDEFINED && $i>MXACCOUNTARTIST)) continue; // pull option 0: undefined
				//if ($i == MXACCOUNTFAN && $value==MXACCOUNTFAN) continue; // pull option fan if already a fan
				$accfield.='<dt><input name="'.$field.'" type="radio" value="'.$i.'"'.($value==$i?' checked':'')
				.(($i>2)?' disabled':'').'> '
				.$acctypes[$i].'</dt>';
				$accfield.='<dd><ul>';
				foreach($acctypesdef[$i] as $def) $accfield.='<li>'.$def.'</li>';
				$accfield.='</ul></dd>';
			}
			$accfield.='</dl>';
			/*
			$accfield='<select name="'.$field.'">';
			foreach($acctypes as $i => $acctype) { // pulling option 0:unknown
				if ($i == MXACCOUNTUNDEFINED) continue;
				$accfield.='<option value="'.$i.'"'.($value==$i?' selected':'').'>'.$acctypes[$i].'</option>';
			}
			$accfield.='</select>';
			*/
			return $accfield;
			break;
		case 'timezone':
			$tzs=$mxdb->tzlist();
			$str='<select name="'.$field.'">';
			foreach($tzs as $tz){
				$str.='<option value="'.$tz.'"'.($value==$tz?' selected':'').'>'.$tz.'</option>';
			}
			$str.='</select>';
			return $str;
			break;
		case 'username':
			return '<input type="text" name="'.$field.'" value="'.$value.'"'
			.($fldinfo[3]>0?(' size="'.min($fldinfo[3],40).'"'):'').($fldinfo[5]?(' placeholder="'.$fldinfo[5].'"'):'')
			.' onchange="checkusername(this);" onkeyup="checkusername(this);">'
			.'<div id="usernamevalidation" class="usernamevalidation"></div>';
			break;
		case 'hidden':
			return '<input type="hidden" id="'.$field.'" name="'.$field.'" value="'.$value.'">';
			break;
		case 'agreement':
			return '<input type="checkbox" id="'.$field.'" name="'.$field.'" value="1" '.(($value && $value!="0000-00-00 00:00:00") || $fldinfo[5]?'checked':'').'> '.$fldinfo[3];
			break;
			case 'checkbox':
			return '<input type="checkbox" id="'.$field.'" name="'.$field.'" value="1" '.($value || $fldinfo[5]?'checked':'').'> '.$fldinfo[3];
			break;
		case 'legalname':
				// if defined, can't be changed by user
				if ($value && $mxuser->status!=MXACCTEMAILCONFIRMED) { return $value.'<input type="hidden" name="'.$field.'" value="'.$value.'">'; break; }
				// otherwise handle as normal text
		case 'mediatitle':
			$value=htmlspecialchars($value);
			/*$value=preg_replace('%(https?://([^ \n,.]|(\.[a-zA-Z0-9]))+)([ .,\n]|$)%i',
			 '<a href="\1" target="_blank">\1</a>\4',$value);*/
			$value=preg_replace('%\n%','<br/>',$value);
			return '<div id="'.$field.'" class="titlefld" onclick="javascript:clickedit(\''
			.$field.'\',1);">'.($value?$value:_('Insert a title here')).'</div>';
			break;
		case 'mediadesc':
			//$value=htmlspecialchars($value);
			/*$value=preg_replace('%(https?://([^ \n,.]|(\.[a-zA-Z0-9]))+)([ .,\n]|$)%i',
			'<a href="\1" target="_blank">\1</a>\4',$value);*/
			$value=preg_replace('/\n/','<br/>',$value);
			return '<div id="'.$field.'" class="descfld" onclick="javascript:clickedit(\''
			.$field.'\',1);">'.($value?$value:_('Insert a description here')).'</div>';
			break;
		case 'fullname':
		case 'text':
			return '<input type="text" name="'.$field.'" value="'.$value.'"'
			.($fldinfo[3]>0?(' size="'.min($fldinfo[3],80).'"'):'').($fldinfo[5]?(' placeholder="'.$fldinfo[5].'"'):'').'>';
			break;
		case 'newpassword':
			$str='<input type="password" name="new_'.$field.'" value="" placeholder="New Password"'
				.($fldinfo[3]>0?(' size="'.min($fldinfo[3],40).'"'):'').'>';
			$str.='<br/><input type="password" name="conf_'.$field.'" value="" placeholder="Confirm New Password"'
			.($fldinfo[3]>0?(' size="'.min($fldinfo[3],40).'"'):'').'>';
			return $str;
			break;
		case 'password':
			$str='<input type="password" name="'.$field.'" value="" placeholder="Current Password"'
			.($fldinfo[3]>0?(' size="'.min($fldinfo[3],40).'"'):'').'>';
			if ($fldinfo[5]==true) {
				$str.=' <input type="button" value="'.mx_optionname('account','pwdreset').'" onclick="window.location=\''.mx_optionurl('account','pwdreset').'\';"/>';
				$str.='<br/><input type="password" name="new_'.$field.'" value="" placeholder="New Password"'
				.($fldinfo[3]>0?(' size="'.min($fldinfo[3],40).'"'):'').'>';
				$str.='<br/><input type="password" name="conf_'.$field.'" value="" placeholder="Confirm New Password"'
				.($fldinfo[3]>0?(' size="'.min($fldinfo[3],40).'"'):'').'>';
			}
			return $str;
			break;
		case 'integer':
			return '<input type="text" name="'.$field.'" value="'.$value.'"'
			.($fldinfo[3]>0?(' size="'.min($fldinfo[3],40).'"'):'').($fldinfo[5]?(' placeholder="'.$fldinfo[5].'"'):'').'>';
			break;
		case 'boolean':
			global $bools;
			$boolfield='<select id="'.$field.'" name="'.$field.'">';
			for ($i=0; $i<2; $i++) {
				$boolfield.='<option value="'.($i+1).'"'.($value==$i+1?' selected':'').'>'.$bools[$i].'</option>';
			}
			$boolfield.='</select>';
			return $boolfield;
		case 'memo':
		case 'simplememo':
			$str='<textarea id="'.$field .'"'.' rows="'.$fldinfo[3]
			.'" cols="'.($fldinfo[6]?$fldinfo[6]:'67').'"'
			.' name="'.$field.'"'
			.($fldinfo[5]?' placeholder="'.$fldinfo[5].'"':'')
			.' onkeypress="var lnfeed=/\n/g;var lf=this.value.match(lnfeed);if(lf){lfn=lf.length;}else{lfn=0;}this.rows=(1+Math.floor(this.value.length/this.cols)+lfn);"'
			.'>'.$value.
			'</textarea>';
			return $str;
			break;
		case 'date':
			preg_match_all('%([0-9]+)%',$value,$datevalues);
			$datefields=mx_formfield($field.'_d',$datevalues[1][2],array(1,$datevalues[1][2],'integer',2,'','DD')).
			'&nbsp;'.mx_formfield($field.'_m',$datevalues[1][1],array(1,$datevalues[1][0],'month')).
			'&nbsp;'.mx_formfield($field.'_y',$datevalues[1][0],array(1,$datevalues[1][0],'integer',4,'','YYYY'));
			return $datefields;
			break;
		case 'month':
			global $months;
			$monthfield='<select name="'.$field.'">';
			for ($i=0; $i<13; $i++) {
				$monthfield.='<option value="'.($i).'"'.($value==$i?' selected':'').'>'.$months[$i].'</option>';
			}
			$monthfield.='</select>';
			return $monthfield;
			break;
		case 'mediastatus':
			global $mediastatuses;
			if ($value==MXMEDIAVIRTUAL) return _('This is a virtual bundle. Status cannot be changed.')
			.'<input type="hidden" name="'.$field.'" value="'.$value.'">';
			$statusfield='<select name="'.$field.'">';
			foreach ($mediastatuses as $i => $mediastatus) {
				if (($i > MXMEDIANEW && $i < MXMEDIASUSPENDED && $i!=MXMEDIAMEMBERVISIBLE)) {
					$statusfield.='<!-- '.$i.' --><option value="'.$i.'"'.($value==$i?' selected':'').'>'.$mediastatus.'</option>';
				}
			}
			$statusfield.='</select>';
			return $statusfield;
			break;
		case 'bundlestatus':
			global $mediastatuses;
			if ($value==MXMEDIAVIRTUAL) return _('This is a virtual bundle. Status cannot be changed.')
			.'<input type="hidden" name="'.$field.'" value="'.$value.'">';
			$statusfield='<fieldset class="bundlestatus">';
			foreach ($mediastatuses as $i => $mediastatus) {
				if (($i >= MXMEDIAFANVISIBLE && $i < MXMEDIASUSPENDED && $i!=MXMEDIAMEMBERVISIBLE)
					|| ($i == MXMEDIAARCHIVED &&
						(($value >= MXMEDIAFANVISIBLE && $value < MXMEDIASUSPENDED) || $value==MXMEDIAARCHIVED))
					|| ($i == MXMEDIAREADY && $value==MXMEDIAREADY)
					|| ($i == MXMEDIANEW && $value==MXMEDIANEW)) {
					$statusfield.='<input type="radio" name="'.$field.'" id="st_'.$i.'" value="'.$i.'"'.($value==$i?' checked':'')
					.' onclick="javascript:return changestatus(\''.$field.'\','.$value.','.$i.');">'
					.'<label for="st_'.$i.'">'.sprintf(($i==MXMEDIAPUBLIC?'<b>%s</b>':'%s'),$mediastatus).'</label><br/>';
				}
			}
			$statusfield.='</fieldset>';
			return $statusfield;
			break;
		case 'locale':
			global $languages;
			$langfield='<select name="'.$field.'">';
			foreach ($languages as $lcode => $lname) {
				$langfield.='<option value="'.$lcode.'"'.($value==$lcode?' selected':'').'>'.$lname.'</option>';
			}
			$langfield.='</select>';
			return $langfield;
			break;
		case 'gender':
			global $genders;
			$genfield='<select name="'.$field.'">';
			foreach ($genders as $i => $gender) {
				$genfield.='<option value="'.$i.'"'.($value==$i?' selected':'').'>'.$gender.'</option>';
			}
			$genfield.='</select>';
			return $genfield;
			break;
		case 'privacy':
			global $privlevels;
			$col=0;
			$privfield='<table class="privacy"><tr>';
			foreach ($privlevels as $lname => $ldesc) {
				$privfield.='<td><input type="checkbox" name="'.$field.'[]" value="'.$lname.'"';
				if (array_search($lname, $defaultprivacy)) $privfield.=' disabled';
				if (array_search($lname,explode(',',$value))!==false
				|| array_search($lname, $defaultprivacy)) $privfield.=' checked';
				$privfield.='>'.$ldesc
				.(array_search($lname, $defaultprivacy)?'<span class="mandatory"><sup>*</sup></span>':'')
				.'</td>';
				$col=(++$col % 3);
				if (!$col) $privfield.='</tr><tr>';
			}
			$privfield.='</tr></table>';
			$privfield.='<span class="mandatoryhelp"><sup>*</sup>'._('This information is always visible').'</span>';
			return $privfield;
			break;
		case 'sharefilter':
			global $sharefilters;
			$col=0;
			$filter='<table class="sharefilter"><tr>';
			foreach ($sharefilters as $bit => $desc) {
				$filter.='<td><input type="checkbox" name="'.$field.'[]" value="'.$bit.'"';
				if ((!$value && $bit==MXSHAREALL)
					|| ($value & $bit)) $filter.=' checked';
				if ($bit==MXSHAREALL) $filter.=' onclick="javascript:shareall(this.form);"';
				else $filter.=' onclick="javascript:unshareall(this.form);"';
				$filter.='>'.$desc.'</td>';
				$col=(++$col % 5);
				if (!$col) $filter.='</tr><tr>';
			}
			$filter.='</tr></table>';
			return $filter;
			break;
		case 'url':
			return '<input type="text" name="'.$field.'" value="'.$value.'"'
			.($fldinfo[3]>0?(' size="'.min($fldinfo[3],40).'"'):'').'>';
			break;
		case 'script':
			return '<script>'.$value.'</script>';
			break;
		case 'submit':
			return '<input type="submit" name="'.$field.'" value="'.$value.'">';
			break;
		/* case 'paypal-ck':
			return '<input type="image" ' .
				'src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" ' .
				'onclick="if (buttonclick(\'pp-'.$field.'\')) submit();">';
			break; */
		case 'button':
			if (!$value) return '&nbsp;&mdash;&nbsp;';
			/*if ($field=='ppckout')
				return '<div class="checkout">-- OR --<br/><input type="image" ' .
				'src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" ' .
				'onclick="if (buttonclick(\''.$field.'\')) submit();"></div>';*/
			if ($field=="checkout" && (MXPAYPALSANDBOX==false || is_admin() || MXBETA ))
				return '<div class="checkout">' /*
				.'<input type="button"'
				.' name="'.$field.'" value="'.$value.'"'
				.' onclick="if (buttonclick(\''.$field.'\')) submit();"'
				.(strpos($field,'-')===0?' disabled':'').'>'
				.'<br/>--- OR ---<br/>' */
				.'<input type="image" '
				.'src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" '
				.'onclick="if (buttonclick(\'pp-'.$field.'\')) submit();">'
				.'</div>';
			if (($field=='confckout' || $field=='canckout') && (MXPAYPALSANDBOX==false || is_admin() || MXBETA ) )
				return '<div class="'.$field.'">'
				.'<input type="button"'
				.' name="'.$field.'" value="'.$value.'"'
				.' onclick="if (buttonclick(\''.$field.'\')) submit();"'
				.(strpos($field,'-')===0?' disabled':'').'>'
				.'</div>';
			if ((MXCHECKOUTOK==false && !is_admin()) && ($field=='checkout' || $field=='confckout' || $field=='canckout')) {
				return '<br/><b>[ Sorry, CHECKOUT is temporarily disabled. ]</b>';
			}
			if (strpos($field,'*')===0) {
				if (!is_admin()) return '';
				else $field=substr($field, 1);
			}
			if (strpos($field,'+')===0) { $btntype='submit'; $field=substr($field, 1); }
			else if ($field=='clear') { $btntype='reset'; }
			else $btntype='button';
			return '<input type="'.$btntype.'"'
				.' name="'.$field.'" value="'.$value.'"'
				.' onclick="if (buttonclick(\''.$field.'\')) submit();"'
				.(strpos($field,'-')===0?' disabled':'').'>';
			break;
		case 'reset':
			return '<input type="reset" name="'.$field.'" value="'.$value.'">';
			break;
		case 'file':
			return '<input type="file" name="'.$field.'" value="'.$value.'">';
			break;
		case 'fileuploader':
			return '<div id="fileuploader"></div>';
			break;
		case 'filetype':
			global $filetypes;
			 // bundle types cannot be changed
			if ($value==MXMEDIABASEBUNDLE || $value==MXMEDIAREGULARBUNDLE) return $filetypes[$value]
				.'<input type="hidden" name="'.$field.'" value="'.$value.'">';
			$ftypefield='<select name="'.$field.'">';
			foreach ($filetypes as $ftvalue => $filetype) {
				if ($ftvalue==MXMEDIABASEBUNDLE || $ftvalue==MXMEDIAREGULARBUNDLE) continue;
				$ftypefield.='<option value="'.$ftvalue.'"'.($value==$ftvalue?' selected':'').'>'.$filetype.'</option>';
			}
			$ftypefield.='</select>';
			return $ftypefield;
			break;
		case 'completion':
			global $completions;
			$fcomp='<select name="'.$field.'">';
			foreach ($completions as $fcvalue => $completion) {
				$fcomp.='<option value="'.$fcvalue.'"'.($value==$fcvalue?' selected':'').'>'.$completion.'</option>';
			}
			$fcomp.='</select>';
			return $fcomp;
			break;
		case 'background':
			$bgs=$mxuser->getbackgrounds();
			$bgfld='<select name="'.$field.'" onchange="javascript:bgtest(this.value);">';
			$pics="";
			while ($bgs && $bg=$mxuser->getbackgrounds($bgs)) {
				$pics.='<img class="hidden" id="bg_'.$bg->id.
						'" src="'.$mxuser->getbackgroundurl($bg).'"/>';
				//$backgrounds[$bg->id]=$bg->title;
				$bgfld.='<option value="'.$bg->id.'"';
				if ($bg->id==$mxuser->background_id) $bgfld.=' selected';
				$bgfld.='>'.$bg->title.'</option>';
			}
			$bgfld.='</select><div class="bgtest"><img id="bgpic" src="'.
			$mxuser->getbackgroundurl($mxuser->background_id).'"/>'.$pics.'</div>';
			return $bgfld;
			break;
		case 'transparency':
			global $transparencies;
			/*$scr='<script language="javascript">
			function settransp(trsp) {
				var mysheets=document.styleSheets;
				var i,j;
				var targetrule;
				for (i=0; i<mysheets.length; i++) {
					var mysheet=mysheets[i];
					var myrules=mysheet.cssRules?mysheet.cssRules:mysheet.rules;
					for (j=0; i<myrules.length; i++){
						if(myrules[i].selectorText.toLowerCase()=="div.whitebg"){
							targetrule=myrules[i]
							break;
						}
					}
				}
				opt=100-trsp;' .
						'targetrule.style.background="'.mx_option('siteurl').'/images/background/white-dot-"+opt+".png";' .
						'alert(targetrule);
			}
			</script>';*/
			$trspfield='<select name="'.$field.'">';
			foreach ($transparencies as $trvalue => $trdesc) {
				$trspfield.='<option value="'.$trvalue.'"'.($value==$trvalue?' selected':'').
				'>'.$trdesc.'</option>';
			}
			$trspfield.='</select>';
			return $trspfield;
		case 'bandrole':
			global $bandroles;
			if ($value==null) $value=MXBANDROLENONE;
			$rolefield='<select name="'.$field.'">';
			foreach ($bandroles as $rolecode => $rolename) {
				$rolefield.='<option value="'.$rolecode.'"'.($value==$rolecode?' selected':'').'>'.$rolename.'</option>';
			}
			$rolefield.='</select>';
			return $rolefield;
			break;
		case 'user':
			if (preg_match('%^([0-9]+)$%',$value)>0) {
				$userinfo=$mxuser->getuserinfo($value);
				return '<input type="text" id="'.$field.'" name="'.$field.'" value="'.mx_getname($userinfo).'"'
				.($fldinfo[3]>0?(' size="'.min($fldinfo[3],40).'"'):'').' placeholder="'._('Recipient Name').'"'
				.' onkeyup="return quicksearch(event,this,\''.$field.'\');"'
				.' onblur="return quicksearch(event,this,\''.$field.'\');">'
				.' <input type="hidden" id="h_'.$field.'" name="h_'.$field.'" value="'.$value.'">' .
					'<div id="to_icon">'.mx_icon('ok').'</div>'
				.'<div class="usersearch" id="'.$field.'_search"></div>';
			}
			return '<input type="text" id="'.$field.'" name="'.$field.'" value="'.$value.'"'
			.($fldinfo[3]>0?(' size="'.min($fldinfo[3],40).'"'):'').' placeholder="'._('Recipient Name').'"'
			.' onkeyup="return quicksearch(event,this,\''.$field.'\');"'
			.' onblur="return quicksearch(event,this,\''.$field.'\');">'
			.' <input type="hidden" id="h_'.$field.'" name="h_'.$field.'" value="">'
			.'<div id="to_icon" style="display:none;">'.mx_icon('ok').'</div>'
			.'<div class="usersearch" id="'.$field.'_search"></div>';
			break;
		case 'subject':
			$disab=''; //($value?' disabled':'');
			return '<input id="'.$field.'" type="text" name="'.$field.'" value="'.$value.'"'
			.' placeholder="'._('Optional').'"'
			.($fldinfo[3]>0?(' size="'.min($fldinfo[3],40).'"'):'').$disab.'>';
			break;
		case 'picture':
			$str='<div class="newpic"><img id="newpic" src="'
			.mx_fanpic($mxuser->id,'large',$mxuser->gender,is_artist())
			.'"/></div><ul id="piclist">';
			$str.='<li><input type="radio" name="'.$field.'" value="'.$value.'" checked '
				.' onclick="var newpic=new Image(); newpic.src=\''
				.mx_fanpic($mxuser->id,'large',$mxuser->gender,is_artist()).'\'; document.getElementById(\'newpic\').src=newpic.src;">'
				._('Do not change').'</li>';
			if ($mxuser->fbid && !$me) {
				$str.='<li><input type="radio" name="'.$field.'" value="fb"'
				.' onclick="var newpic=new Image(); newpic.src=\'http://graph.facebook.com/'.$mxuser->fbid.'/picture?type=large\'; document.getElementById(\'newpic\').src=newpic.src;">'
				._('Update from Facebook').'</li>';
			}
			if ($mxuser->fbid && $me) $str.='<li><input type="radio" name="'.$field.'" value="fb"'
				.' onclick="var newpic=new Image(); newpic.src=\'http://graph.facebook.com/'.$mxuser->fbid.'/picture?type=large\'; document.getElementById(\'newpic\').src=newpic.src;">'
				._('Update from Facebook').'</li>';
			$mypics=$mxuser->getpics();
			if ($mypics) {
				$str.='<li><input type="radio" id="mediapic" name="'.$field.'" value=""'
				.' onclick="var mp=document.getElementById(\'mediasel\');pictest(mp.value,\''.$field.'\');">'._('From My Media').'<br/>';
				$picfld='<select id="mediasel" name="'.$field.'media" onchange="pictest(this.value,\''.$field.'\');">';
				$pics="";
				while ($pic=$mxuser->getpics($mypics)) {
					$pics.='<img class="hidden" id="pic_'.$pic->id.
							'" src="'.$mxuser->getpicurl($pic).'"/>';
					//$pictures[$pic->id]=$pic->title;
					$picfld.='<option value="'.$pic->id.'"';
					//if ($pic->id==$mxuser->background_id) $picfld.=' selected';
					$picfld.='>'.$pic->title.'</option>';
				}
				$picfld.='</select></li>'.$pics;
				$str.=$picfld;
			}
			$str.='<li><div id="fileuploader"></div></li></ul>';
			return $str;
			break;
		case 'msgflags':
			$str='';
			if ($value & MXFRIENDREQUEST) $str.=_('Friendship');
			return $str;
			break;
		case 'notif':
			global $notifs;
			$notiffield='<select name="'.$field.'">';
			foreach ($notifs as $nvalue => $ntype) {
				$notiffield.='<option value="'.$nvalue.'"'.($value==$nvalue?' selected':'').'>'.$ntype.'</option>';
			}
			$notiffield.='</select>';
			return $notiffield;
			break;
		default:
			return '[Unknown field/type: "'.$field.'" ('.$fldtype.')] '.$value;
			break;
	}
}

function mx_showdir($table,$clickable=false) {
	echo mx_showdirstr($table,$clickable);
}
function mx_showdirstr($table,$clickable=false) {
	global $mxuser;
	/*
	$str='<table class="directory"><tr>';
	$numpeople=count($table);
	$maxcol=max(6,floor(sqrt($numpeople)));
	$col=0;
	$row=0;
	foreach($table as $person) {
		if (!$col) {
			$str.='<tr>';
			$row++;
		}
		$str.='<td'.($clickable?(' class="friend"'
		.' onmouseover="showbutton('.$person.');"'
		.' onmouseout="hidebutton('.$person.');"'):' class="friend"')
		.'>';
		$str.='<div class="addbutton" id="'.$person.'"' .
			' onclick="window.location=\''.mx_actionurl('account','messages','af:'.$person,'writemsg').'\';">Add as a Friend</div>';
		if (is_array($person)) {
			$str.='<img class="dirpic" src="https://graph.facebook.com/'.$person['id'].'/picture?type=square"/> '
			.'<span class="dirname">'.$person['name'].'</span>';
		} else {
			$dbuser=$mxuser->getuserinfo($person);
			$str.='<img class="dirpic" src="'.mx_fanpic($person,'square',$dbuser->gender).'"/>'.
			'<span class="dirname">'.mx_getname($dbuser).'</span>';
		}
		$str.='</td>';
		$col=(++$col % $maxcol);
		if (!$col) $str.='</tr>';
	}
	if ($col && $row>1) $str.='<td class="friend" colspan="'.($maxcol-$col).'">&nbsp;</td>';
	$str.='</table>';
	*/
	$numpeople=count($table);
	$maxcol=max(6,floor(sqrt($numpeople)));

	$str= '<table class="directory"><tr>';
	$c=0;
	//if ($table['count']==30) $str.= '<td>'._('30 fans chosen at random...').'</td></tr><tr>';
	foreach ($table as $person) {
		$user=is_array($person)?$person:$mxuser->getuserinfo($person);
		$str.= '<td class="friend">';
		/*
		.' onmouseover="showbutton('.$user->id.',event);"'
		.' onmouseout="hidebutton('.$user->id.',event);">';
		$str.= '<div class="addbutton" id="'.$user->id.'"'
			.' onclick="window.location=\''.mx_actionurl('fans','fanprof',$user->id).'\';">'; //See Page</div>';*/
		$str.= '<a href="'.mx_actionurl('fans','fanprof',$user->id).'" title="'
		.mx_getname($user).' - '._('See Page')
		.'" class="pictooltip">';
		$str.= '<img tag="'.$user->id.'" class="dirpic" src="'.mx_fanpic($user->id,'square',$user->gender).'">';
		$str.= '</a>';
		$str.= '<br/>'.mx_getname($user);
		$str.= '</td>';
		$c=(++$c % $maxcol);
		if (!$c) $str.= '</tr><tr>';
	}
	$str.= '</tr></table>';
	return $str;
}

function mx_showtable($table,$values,$listtype='',$buttons,$section) {
	echo mx_showtablestr($table,$values,$listtype,$buttons,$section);
}

function mx_showtablestr($table,$values,$listtype='',$buttons,$section,$onerow=false) {
	global $mxuser;
	$str='';
	$secinfo=$table[$section];
	if (!$onerow) $str.='<div class="scrolltable"><table id="t_'.$section.'" class="'
	./*($listtype=='wall'?'wall':'subform')*/$listtype.'"'
	.($listtype=='cart'?' tag="act_cartdrop"':'')
	.'>';
	$cols=0;
	foreach ($secinfo as $field => $fldinfo) {
		if (is_array($fldinfo) && $fldinfo[0]>=0 && $fldinfo[2]!="hidden") $cols++;
	}
	if (!$onerow && $listtype!='wall' && $listtype!='pubmed') {
		$str.='<tr class="'.$section.'_hdr">';
		foreach ($secinfo as $field => $fldinfo) {
			if (!is_array($fldinfo)) continue;
			if ($fldinfo[0]==-1 && $fldinfo[2]) {
				$str.='<td colspan="'.$cols.'" class="tabledesc">';
				if (function_exists('mx_mnhelpme')) {
					$str.='<div class="helpme">'
						.'<a href="'.mx_actionurl('help','helpme',$listtype,$section).'">'.mx_icon('helpme','Help',24).'</a>'
						.'</div>';
				}
				$str.=$fldinfo[2].'</td></tr><tr class="'.$section.'_hdr">';
				continue;
			}
			if ($fldinfo[2]=='hidden') {
				$str.=mx_formfield($field,$fldinfo[1],$fldinfo);
				continue;
			}
			$str.='<td class="columnname '.$listtype.'_'.$field.'"'.($fldinfo[3]?(' width="'.$fldinfo[3].'%"'):'').'>'.$fldinfo[1].'</td>';
		}
		$str.='</tr>';
	}
	$i=1;
	if (array_key_exists($section,$values)) {
		$headrow='';
		$vsection=$values[$section];
		if ($listtype=='pubmed' /* && $section=='media' */) {
			$str.='<tr id="bundlelist"><td colspan="'.$cols.'" class="bundlelist">';
			$str.='<h5>'._('Bundles').'</h5>';
			$str.='<div class="bundlelist">';
			$featbun=0;
			foreach ($vsection as $listelem) {
				$ttl=$listelem->title;
				if (strlen($ttl)>13) $ttl=substr($ttl,0,10).'...';
				$name=$listelem->artistname;
				if (strlen($name)>13) $name=substr($name,0,10).'...';
				$str.='<a href="'./*mx_actionurl('artist','artprof',$listelem->owner_id).*/'#!ob='.$listelem->id.'" onclick="openbundle('.$listelem->id.');">';
				//$str.='<a href="'.mx_actionurl('media','medprof',$listelem->id).'">';
				$str.='<div class="bundlelistpic"><div class="bundlelistartist">'.$name.'</div>'
					.$listelem->mediapicnoprop.'<div class="bundlelisttitle">'
					.$ttl.'</div></div>';
				$str.='</a>';
				if ($listelem->featured && !$featbun) $featbun=$listelem->id; // will show featured bundle
			}
			$str.='</div>';
			if ($listtype=='pubmed') {
				$str.='<div class="detailstip">'._('Click on a bundle in the list above to open it...').'</div>';
				/*
				if ($section=='media') $str.='<tr><td colspan="'.$cols.'" class="detailstip">'
				.'<div id="player">'
				.mx_mediabutton('openbundle', _('Open All Bundles'),24,'oa_',0,'hover')
				.mx_mediabutton('notready.gif', _('Please wait...'),24,'wa_',0)
				.mx_mediabutton('closebundle', _('Close All Bundles'),24,'ca_',0,'hover')
				.'</div>'
				.'</td></tr>';
				*/
			}
			$str.='</td></tr>';
			$str.='<tr id="bundledetails"'.($featbun?' class="featured"':'').'><td colspan="'.$cols.'"><h5>'._('Bundle Details').'</h5></td></tr>';
		}
		$total=0;
		foreach ($vsection as $listelem) {
			if ($listtype=='pubmed' && $section!='media') {
				$artistname=mx_infofield('artistname',$listelem->artistname,$secinfo['artistname']);
				if ($headrow!=$artistname && $listelem->id!='{id}') {
					$headrow=$listelem->artistname;
					$str.='<tr class="headrow"><td colspan="'.$cols.'">';
					$str.='<img tag="'.$listelem->owner_id.'" class="artistpic" src="'.mx_fanpic($listelem->owner_id,'square',MXNOSEX,true).'" />'
					.' <a href="'.mx_actionurl('artists','artprof',$listelem->owner_id).'" alt="'.$listelem->artistname.'">'
					.$listelem->artistname.'</a>';
					$str.='</td></tr>';
				}
			} else if ($listtype=='media') {
				if ($listelem->type==MXMEDIABASEBUNDLE || $listelem->type==MXMEDIAREGULARBUNDLE) {
					$bundles[$listelem->id]=$listelem->title;
				}
			}
			if ($listtype=='messages') $lclass='class="msgline m_'.$listelem->msgid.'"';
			else if ($listtype=='media') $lclass='class="pubmed"';
			else if ($listtype=='subscriptions') $lclass='class="subline"';
			else if ($listtype=='wall') $lclass='class="wall"';
			else if ($listtype=='pubmed') {
				$lclass='class="pubmed bundle brow_'.$listelem->id.($listelem->featured?' featured':'').'"';
			} else $lclass='';
			if ($listtype=='media' && ($section!='media' && $section!='pubmed')) $lclass='class="pubmed dragmedia"';
			//$str.='<tr '.$lclass.' id="'.($listelem->id=='{id}'?'model_row':('row_'.$listelem->id)).'">';
			$str.='<tr tag="'.$listelem->id.'" '.$lclass
				.($listelem->id=='{id}'?' id="model_row"':'');
			if ($listtype=='subscriptions') $str.=' xitemprop="'.($listelem->subtype==MXSUBLIKE?'likes':'fanOf').'" itemscope itemtype="http://schema.org/MusicGroup"';
			if ($listtype=='fanships') $str.=' xitemprop="'.($listelem->subtype==MXSUBLIKE?'likers':'fans').'" itemscope itemtype="http://schema.org/People"';
			$str.='>';
			//if ($listtype=='messages' && $listelem->read) $read=$listelem->read;
			foreach ($secinfo as $field => $fldinfo) {
				if ($fldinfo[0]==-1 || !is_array($fldinfo)) continue;
				if ($fldinfo[2]=='hidden') continue;
				$str.='<td class="msgcell input_'.($onerow?0:$i).' '.$listtype.'_'.$field
					// additional classes here
					//.(($listtype=='media' && $field=='select')?(' ms_'.$listelem->status):'')
					.(($listtype=='messages' && !$listelem->read)?' newmsg':'')
					.(($listtype=='messages' && ($listelem->status & MXREQIGNORED))?' newmsg':'')
					//.(($listtype=='messages' && ($listelem->status & MXREQRECUSED))?' recreq':'')
					//.(($listtype=='messages' && ($listelem->status & MXREQACCEPTED))?' accreq':'')
					.(($listtype=='messages' && $listelem->cancelled)?' canmsg':'')
					.(($listtype=='subscriptions' && ($listelem->status == MXNEWSUB
					 || $listelem->status == MXRENEWEDSUB) )?' newsub':'')
					//.(($listtype=='wall' && ($listelem->flags & MXWALLDELETED))?' canmsg':'')
					.'"' // onclick procs below
					.(($listtype=='messages' && $field!='select')?(' onclick="javascript:readcontent('.$listelem->msgid.')"'):'')
					//.(($listtype=='media' && $field!='select')?(' onclick="javascript:readcontent('.$listelem->id.')"'):'')
					.'>'
					.'<a name="bdetails_'.$listelem->id.'"></a>'
					.(!$fldinfo[0]?mx_infofield($field,$listelem->$field,$fldinfo):mx_formfield($field,$listelem->$field,$fldinfo))
					.(($listtype=='messages' && ($listelem->status & MXREQACCEPTED) && $field=='flags')?
						('<br/><span class="accreq">'._('ACCEPTED').'</span>'):'')
					.(($listtype=='messages' && ($listelem->status & MXREQRECUSED) && $field=='flags')?
						('<br/><span class="recreq">'._('RECUSED').'</span>'):'')
					.'</td>';
			}
			if ($listtype=='cart') $total+=$listelem->price;
			$str.='</tr>';
			$i=3-$i; // switch between 1 and 2
		}
	} else if ($section!='newbun') {
		if ($listtype=='pubmed' || $listtype=='media')
			$lclass='class="pubmed emptybundle"';
		else
			$lclass='';
		$str.='<tr '.$lclass.'><td class="input_1" colspan="'.$cols.'">';
		switch($listtype) {
			case 'cart':
				$str.=($section=='cart'?_('Your cart is empty.'):_('Your wish list is empty.'));
				break;
			case 'subscriptions':
				$str.=_('No fanships found');
				break;
			case 'wall':
				$str.=_('No updates posted yet');
				break;
			case 'pubmed':
			case 'media':
				$str.=_('Sorry, no media here at this time...');
				break;
			default:
				$str.=_('No results found');
				break;
		}
		$str.='</td></tr>';
	}
	if ($onerow) return $str;
	if ($listtype=='cart' && $section=='cart' && $total>0) {
		// ACTIVATE WHEN HST
		/*if ($mxuser->cart->taxcountrycode=='CA')
			$taxes=$total*MXTAXHST;
		else */ $taxes=0;
		$price=array(1,_('Price'),'price',10);
		$str.='<tr><td colspan="'.(count($secinfo)).'"><hr/></td></tr>';
		$str.='<tr><td colspan=3 class="cartinfo" rowspan=3>'
		.MXCARTINFORMATION;
		// if not yet purchased, show term and conditions link
		if ($mxuser->cart->progress<4) $str.='<hr/>'
		.sprintf(_('Read the %s before checking out'),
			mx_windowedpage('salesterms',_('terms & conditions')));
		$str.='</td>'
		.'<td class="msgcell input_2">'
		._('Total').'</td>'
		.'<td class="msgcell input_2">'
		.mx_infofield('total',$total,$price)
		.'</td></tr>';
		if ($taxes) {
			$str.='<tr><td class="msgcell input_1">'
			._('Canadian Sales Tax<br/>(H.S.T. 12%)').'</td>'
			.'<td class="msgcell input_1">'
			.mx_infofield('total',$taxes,$price)
			.'</td></tr>';
		} else if (!$mxuser->cart->taxcountrycode) {
			$str.='<tr><td class="msgcell input_1">'
			._('Our sales are TAX FREE<br/>at this time,<br/>in agreement with<br/>Canadian Tax Laws.').'</td>'
			.'<td class="msgcell input_1">'
			._('US$ 0.00')
			.'</td></tr>';
		} else if (!$mxuser->cart->taxcountrycode) {
			$str.='<tr><td class="msgcell input_1">'
			._('Taxes may apply<br/>for Canadian Residents').'</td>'
			.'<td class="msgcell input_1">'
			.'(H.S.T.&nbsp;12%)'
			.'</td></tr>';
		} else {
			$str.='<tr><td class="msgcell input_1">'
			._('Sales Tax').'</td>'
			.'<td class="msgcell input_1">'
			._('US$ 0.00')
			.'</td></tr>';
		}
		$str.='<tr><td class="msgcell carttotal input_2">'
		._('Order Total').'</td>'
		.'<td class="msgcell carttotal input_2">'
		.mx_infofield('total',$total+$taxes,$price)
		.'</td></tr>';
	}
	if (array_key_exists('select',$table[$section])
	&& array_key_exists($section,$values)) { // show select column message
		$str.='<tr><td class="helprow">'.mx_icon('bentarrow').'</td><td class="helprow" colspan="'.($cols-1).'">'
		._('Click to select/unselect one particular row<br/>or click the top box to select/unselect them all')
		.'</td></tr>';
	}
	/*
	if ($listtype=='pubmed' && array_key_exists($section,$values)) { // show inactive buttons explanation
		$str.='<tr><td class="helprow arrow">'.mx_icon('bentarrow').'</td><td class="helprow" colspan="'.($cols-1).'">'
		._('Gray buttons correspond to restricted access media.'
		.'<br/>To get full access, you need to be a member and/or fan')
		.'</td></tr>';
	}
	*/
	if ($buttons) {
		$str.='<tr id="t_'.$section.'_buttons"><td class="buttons" colspan="'.$cols.'">';
		$spacer='';
		$str.='<br/>';
		/*if ($listtype=='media' && $section=='new') {
			$str.='<input type="text" name="bundlename" placeholder="'._('Bundle Name').'" />'.$spacer
			.mx_formfield('newbundle','&larr; '._('Create a New Bundle'),'button')
			.'<br/>';
		}*/
		/*if ($listtype=='media' && ($section=='new' || $section=='published')) {
			$str.=mx_formfield('bundleid',$bundles,'bundle').$spacer
			.mx_formfield('m_move'.($section=='new'?'new':'pub'),'&larr; '._('Move to Bundle'),'button')
			.'<br/>';
		}*/
		foreach ($buttons as $btnname => $btnlabel) {
			if (!is_array($btnlabel) && ($total>0 || $btnname=='shopmore')) {
				$str.=$spacer.mx_formfield($btnname,$btnlabel,'button');
				$spacer='&nbsp;';
			} else if ($btnname==$section) {
				foreach ($btnlabel as $btnname2 => $btnlabel2) {
					if  ($listtype != 'cart' || $total>0 || $btnname2=='shopmore') {
						$str.=$spacer.mx_formfield($btnname2,$btnlabel2,'button');
						$spacer='&nbsp;';
					}
				}
			}
		}
		$str.='</td></tr>';
	}
	$str.='</table></div>';
	return $str;
}

function mx_showlist($list,$values,$listtype='',$submit=false,$counts=false) {
	echo mx_showliststr($list,$values,$listtype,$submit,$counts);
}

function mx_showliststr($list,$values,$listtype='',
		$submit=false,$counts=false,$shorttable=false) {
	$str='<div class="'.($listtype=='cart'?'cart':'form').' list">';
	$str.='<table><tr><th>'.$list[2].'</th></tr>';
	$str.='<tr><td class="title">'.$list[3].'</td></tr>';
	$str.='<tr><td>';
	$group=mx_secureword($_REQUEST['k']);
	$form=0;
	$style='edit';
	//if ($listtype=='messages' || $listtype=='search') {
	$nonempty='';
	foreach($list[5] as $section => $secinfo) {
		$cntval[$section]=0;
		if ($secinfo[0]==0 && $counts && count($values[$section])>0) { // counts for lists
			foreach($values[$section] as $vptr => $vsec) {
				if (!$vsec->read) {
					$cntval[$section]++;
					if (!$nonempty) $nonempty=$section;
				}
			}
		}
	}
	//}
	foreach ($list[5] as $section => $secinfo) {
		if ($group=='') {
			if ($nonempty) $group=$nonempty;
			else $group=$section;
		}
		$str.='<div id="'.($form?'f_':'').$section.'" class="'.$style.((($section==$group) && !$form)?'':' hidden').'">';
		$str.='<form name="'.$section.'" method="POST" enctype="multipart/form-data"'
			.' onsubmit="return checkform(\''.$section.'\');">';
		$str.='<table><tr><td>';
		$str.='<fieldset>';
		if (!$counts) $cntval[$section]=0;
		else if ($listtype=='cart') $cntval[$section]=count($values[$section]);
		foreach ($list[5] as $grp => $det) {
			if ($grp==$section) $str.='<legend class="seltab">'.$secinfo[$section][1]
				.($cntval[$section]?('<div class="itemno"><div>'.$cntval[$section].'</div></div>'):'').'</legend>';
			else {
				$str.='<legend class="tab"><a href="javascript:tabswitch(\''.$section.'\',\''.$grp.'\');"' .
				' alt="'.$det[$grp][1].'">'.$det[$grp][1]
				.($cntval[$grp]?('<div class="itemno"><div>'.$cntval[$grp].'</div></div>'):'').'</a></legend>';
			}
		}
		if (!$secinfo[0]) { // if tab is list
			$str.=mx_showtablestr($list[5],$values,$listtype,$list[4],$section);
		} else { // then tab is form
			$tabform=array();
			$tabform[5]=$secinfo;
			$tabform[1]=$list[1];
			$str.='<table class="subform'.($list[1]?' nofielddesc':'').'"><tr><td>';
			if (array_key_exists($section,$values)) $vsection=$values[$section];
			//if ($listtype=='fbpages' || $listtype=='messages') $nofieldset=true;
			//else $nofieldset=false;
			$str.=mx_fieldsetstr($tabform,$vsection,true,null,true,($secinfo[0]==1));
			$str.='</td></tr>';
			//if ($submit || true) {
				$cols=0;
				foreach ($secinfo as $field => $fldinfo) {
					if (is_array($fldinfo) && $fldinfo[0]>=0 && $fldinfo[2]!="hidden") $cols++;
				}
				$str.='<tr><td class="buttons" colspan="'.$cols.'">';
				$spacer='';
				foreach ($list[4] as $btnname => $btnlabel) {
					if (!is_array($btnlabel)) {
						$str.=$spacer.mx_formfield($btnname,$btnlabel,'button');
						$spacer='&nbsp;';
					} else if ($btnname==$section) {
						foreach ($btnlabel as $btnname2 => $btnlabel2) {
							$str.=$spacer.mx_formfield($btnname2,$btnlabel2,'button');
							$spacer='&nbsp;';
						}
					}
				}
				$str.='</td></tr>';
			//}
			$str.='</table>';
		}
		$str.='</fieldset></td></tr>';
		$str.='</table>';
		$str.='</form>';
		$str.='</div>';
	}
	$str.='</td></tr>';
	$str.='</table>';
	$str.='</div>';
	return $str;
}

function mx_form2list($form,$values,$cansubmit=true,$secure=false,$errors=null,$subsubmit=false,$nofieldset=false) {

	// not working !!

	$list=array();
	$list[0]=$form[0];
	$list[1]=0;
	$list[2]=$form[2];
	$list[3]=$form[3];
	$list[4]=array();
	$list[5]=array();
	$listvalues=array();
	$grpcnt=0;
	foreach($form[5] as $fld => $det) {
		if ($det[0]==-1) {
			$grpcnt++;
			$grp='group_'.$grpcnt;
			$list[4][$grp]=$form[4];
			$list[5][$grp]=array();
			$list[5][$grp][$grp]=$det;
			continue;
		}
		$list[5][$grp][$fld]=$det;
		$listvalues[$grp][$fld]=$values[$fld];
	}
	//die(print_r($list,true));

	mx_showlist($list,$listvalues,$listtype='',$submit=$cansubmit,$counts=false);
}

function mx_showname() {
	global $mxuser;
	if ($mxuser->id) {
		echo '<div class="showname">';
		if ($mxuser->fullname) echo $mxuser->fullname;
		else if ($mxuser->artistname) echo $mxuser->artistname;
		else echo _('New User');
		echo '</div>';
	}
}


function mx_showform($form,$values,$cansubmit=true,$secure=false,$errors=null,$subsubmit=false,$nofieldset=false) {
	echo mx_showformstr($form,$values,$cansubmit,$secure,$errors,$subsubmit,$nofieldset);
}

function mx_showformstr($form,$values,$cansubmit=true,$secure=false,$errors=null,$subsubmit=false,$nofieldset=false) {
	$page=mx_secureword($_GET['p']);
	$option=mx_secureword($_GET['o']);
	$str='<div class="form">';
	$str.='<form name="'.$form[0].'" method="POST" enctype="multipart/form-data"' .
			' onsubmit="return checkform(\''.$form[0].'\');"';
	if ($secure) $str.=' action="'.mx_optionurl_secure($page,$option).'"';
	else $str.=' action="'.mx_optionurl_normal($page,$option).'"';
	$str.='>';
	$str.='<table><tr><th>'.$form[2].'</th></tr>';
	$str.='<tr><td class="title">'.$form[3].'</td></tr>';
	$str.='<tr><td>';
	$str.=mx_fieldsetstr($form,$values,$cansubmit,$errors,$nofieldset);
	$str.='</td></tr>';
	if ($cansubmit && is_array($form[4])) {
		$spacer='';
		$str.='<tr><td class="buttons">';
		$spacer='';
		foreach ($form[4] as $btnname => $btnlabel) {
			if (!is_array($btnlabel)) $str.=$spacer.mx_formfield($btnname,$btnlabel,'button',null,true);
			else $str.=$spacer.$btnlabel[0];
			$spacer='&nbsp;';
		}
		//$str.=mx_formfield('submit',$form[4],'submit').'&nbsp;';
		//$str.=mx_formfield('clear',$form[6],'reset');
		$str.='</td></tr>';
	}
	$str.='</table>';
	$str.='</form>';
	$str.='</div>';
	return $str;
}


function mx_letterstr($form,$values) {
	$str.='<div class="f_sender"><u>'._('From').'</u>: '
	.mx_infofield('from',$values['from'],$form[5]['from']).'<div id="from_'.$values['msgid'].'" class="hidden">'.$values['from'].'</div>'
	.'</div>';
	$str.='<div class="f_date"><u>'._('Date').'</u>: '
	.mx_infofield('date',$values['date'],$form[5]['date']).'<div id="date_'.$values['msgid'].'" class="hidden">'.$values['date'].'</div>'
	.'</div>';
	$str.='<div class="f_receiver"><u>'._('To').'</u>: '
	.mx_infofield('to',$values['to'],$form[5]['to']).'<div id="to_'.$values['msgid'].'" class="hidden">'.$values['to'].'</div>'
	.'</div>';
	$str.='<div id="h_to_'.$values['msgid'].'" class="hidden">'.$values['h_to'].'</div>';
	$str.='<div id="h_from_'.$values['msgid'].'" class="hidden">'.$values['h_from'].'</div>';
	if ($values['subject']) {
		$str.='<div class="f_subject"><u>'._('Subject').'</u>: '
		.mx_infofield('subject',$values['subject'],$form[5]['subject']).'</div>';
	}
	$str.='<div id="subject_'.$values['msgid'].'" class="hidden">'.$values['subject'].'</div>';
	if ($values['flags']) {
		$str.='<div class="f_flags"><u>'._('Type').'</u>: '
		.mx_infofield('flags',$values['flags'],$form[5]['flags']).'</div>';
	}
	$str.='<div id="flags_'.$values['msgid'].'" class="hidden">'.$values['flags'].'</div>';
	$str.='<div class="f_body">'
	.mx_infofield('body',$values['body'],$form[5]['body']).'<div id="body_'.$values['msgid'].'" class="hidden">'.$values['body'].'</div>'
	.'</div>';
	$str.='<div class="f_buttons">';
	$spacer='';
	foreach ($form[4] as $btnname => $btnlabel) {
		$str.=$spacer.mx_formfield($btnname,$btnlabel,'button',null,true);
		$spacer='&nbsp;';
	}
	$str.='</div>';
	return $str;
}

function mx_fieldsetstr($form,$values,$cansubmit=true,$errors=null,$nofieldset=false,
		$fielddesc=true) {
	//if ($form[1]==1) $nofieldset=true;
	$str=($nofieldset?'':'<fieldset>');
	$fieldset=0;
	//die(print_r($form));
	$cols=0;
	foreach ($form[5] as $field => $fldinfo) {
		if (is_array($fldinfo) && $fldinfo[0]>=0 && $fldinfo[2]!="hidden") $cols++;
	}
	$mandatory=0;
	foreach ($form[5] as $field => $fldinfo) {
		if (!is_array($fldinfo)) continue;
		if ($fldinfo[0]==-1) {
			if (!$form[1]) {
				if ($fieldset) {
					if ($mandatory) {
						$str.='<tr><td class="mandatoryhelp" colspan="'.$cols.'"><sup>*</sup>'.
						_('Information in <b>bold</b> is mandatory').'</td></tr>';
						$mandatory=0;
					}
					$str.='</table></fieldset><br/></td></tr>';
					$str.='<tr><td><fieldset>';
				}
				$str.='<a name="'.$field.'"></a>';
				if (!$nofieldset) $str.='<legend>'.$fldinfo[1].'</legend>';
				$fieldset=1;
				$str.='<table class="subform">';
				if ($fldinfo[2]) $str.='<tr><td colspan=2 class="tabledesc">'.$fldinfo[2].'</td></tr>';
			}
			continue;
		}
		if ($fldinfo[0]==-2) {
			$str.='<tr><td colspan="2">'
			.mx_showtablestr($fldinfo[1],$fldinfo[2],$fldinfo[3],$fldinfo[4],$fldinfo[5])
			.'</td></tr>';
			continue;
		}
		if ($fldinfo[0]==-3) { //inside buttons
			$spacer='';
			$str.='<tr><td class="buttons" colspan="'.$cols.'">';
			$spacer='';
			foreach ($fldinfo[1] as $btnname => $btnlabel) {
				if (!is_array($btnlabel)) $str.=$spacer.mx_formfield($btnname,$btnlabel,'button',null,true);
				else $str.=$spacer.$btnlabel[0];
				$spacer='&nbsp;';
			}
			//$str.=mx_formfield('submit',$form[4],'submit').'&nbsp;';
			//$str.=mx_formfield('clear',$form[6],'reset');
			$str.='</td></tr>';
			continue;
		}
		if ($fieldset==0) {
			$str.='<table class="subform">';
			$fieldset=1;
		}
		if ($fldinfo[2]=='hidden') {
			$str.='<tr><td colspan=2>'.mx_formfield($field,
				($values[$field]?$values[$field]:$fldinfo[1]),$fldinfo).'</td></tr>';
			continue;
		}
		$str.='<tr class="row_'.$field.'">';
		if ($fldinfo[0]>=3) $mandatory=1;
		if ($fielddesc) $str.='<td class="fieldname'.($fldinfo[0]>=3?' mandatory':'')
			.(($errors && array_key_exists($field,$errors))?' flderror':'')
			.'">'.$fldinfo[1]
		.($fldinfo[0]>=3?'<sup>*</sup>':'').'</td>';
		if (!$fldinfo[0] || !$cansubmit) {
			$str.='<td class="data"'.($fielddesc?'':' colspan=2').'>'.
				mx_infofield($field,$values[$field],$fldinfo);
				$str.='</td>';
		} else {
				$str.='<td class="input"'.($fielddesc?'':' colspan=2').'>';
				if ($errors && array_key_exists($field,$errors)) {
					$str.='<div class="formerror"><table><tr><td class="errorarrow">&darr;</td><td>'.$errors[$field].'</td></tr></table></div>';
				}
				$str.=mx_formfield($field,$values[$field],$fldinfo);
				$str.='</td>';
		}
		$str.='</tr>';
	}
	if ($mandatory) {
		$str.='<tr><td class="mandatoryhelp" colspan="'.$cols.'"><sup>*</sup>'.
		_('Information in <b>bold</b> is mandatory').'</td></tr>';
	}
	if ($cansubmit && is_array($form[4]) && false) { // inside buttons (disabled)
		$spacer='';
		$str.='<tr><td style="border:red 1px solid;" class="buttons" colspan="'.$cols.'">';
		$spacer='';
		foreach ($form[4] as $btnname => $btnlabel) {
			$str.=$spacer.mx_formfield($btnname,$btnlabel,'button',null,true);
			$spacer='&nbsp;';
		}
		//$str.=mx_formfield('submit',$form[4],'submit').'&nbsp;';
		//$str.=mx_formfield('clear',$form[6],'reset');
		$str.='</td></tr>';

	}
	$str.='</table>';
	$str.=($nofieldset?'':'</fieldset>');
	return $str;
}

function mx_fieldhelp($fname,$desc) {
	$hid=md5($fname);
	return '<img class="fieldhelp" src="'.mx_iconurl('infobutton').'"'
		.' title="'.str_replace(':','',$fname).' - '.$desc.'" />';
	return ' <img class="fieldhelp" src="'.mx_iconurl('infobutton').'"'
			.' onmouseover="javascript:showhelp(\''.$hid.'\',event);"'
			.' onmouseout="javascript:hidehelp(event);"'
			/*.' onmouseout="javascript:hidehelp(\''.$hid.'\',event);"'*/
			.'  />'
			.'<div id="'.$hid.'" class="fieldhelp"'
			.' onmouseover="javascript:showhelp(\''.$hid.'\',event);"'
			.' onmouseout="javascript:hidehelp(event);"><div>'
			.'<span class="title">'.str_replace(':','',$fname).'</span>'
			.'<span class="desc">'.$desc.'</span></div></div>';
}

function mx_genpassword() {
	$pass='';
	$chartab='ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%?*';
	for ($i=0; $i<8; $i++) {
		$pass.=substr($chartab,rand(0,strlen($chartab)-1),1);
	}
	return $pass;
}

function mx_getidfromusername($id) {
	global $mxdb;
	return $mxdb->getidfromusername($id);
}

function mx_sendconfirmationcode($user) {
	if ($user->email) {
		$to=$user->email;
		$subj=_('Welcome to MusXpand');
		$html=mx_showhtmlpagestr('confirmemail');
		$html=str_replace('{CONFIRMCODE}',$user->confirmationcode,$html);
		$confirmurl=mx_actionurl('account','register','confirmation').'&c='.$user->confirmationcode;
		$confirmlink='<a href="'.$confirmurl.'">MusXpand</a>'
		.sprintf(_(' (link: %s)'),$confirmurl);
		$html=str_replace('{CONFIRMURL}',$confirmlink,$html);
		mx_sendmail($to,$subj,mx_html2text($html),$html);
	}
}


function mx_sendnewpassword($user) {
	if ($user->email) {
		$to=$user->fullname.' <'.$user->email.'>';
		$subj=_('Important Message from MusXpand');
		$html=mx_showhtmlpagestr('newpassword');
		$html=str_replace('{PASSWORD}',$user->password,$html);
		$siteurl='<a href="'.mx_option('basicsiteurl').'">MusXpand</a>';
		$html=str_replace('{SITEURL}',$siteurl,$html);
		$html=str_replace('{SUPPORTEMAIL}',MXSUPPORTEMAIL,$html);
		mx_sendmail($to,$subj,mx_html2text($html),$html);
	}
}

function mx_lostpassword($user) {
	if ($user->email) {
		$to=$user->fullname.' <'.$user->email.'>';
		$subj=_('Important Message from MusXpand');
		$html=mx_showhtmlpagestr('lostpassword');
		$html=str_replace('{CONFIRMCODE}',$user->confirmationcode,$html);
		$confirmurl=mx_actionurl('account','signin','confirmation').'&c='.$user->confirmationcode;
		$confirmlink='<a href="'.$confirmurl.'">MusXpand</a>'.sprintf(_(' (link: %s)'),$confirmurl);
		$html=str_replace('{CONFIRMURL}',$confirmlink,$html);
		$siteurl='<a href="'.mx_option('basicsiteurl').'">MusXpand</a>';
		$html=str_replace('{SITEURL}',$siteurl,$html);
		$html=str_replace('{SUPPORTEMAIL}',MXSUPPORTEMAIL,$html);
		mx_sendmail($to,$subj,mx_html2text($html),$html);
	}
}

function mx_artpic($id,$size='square',$sex=MXNOSEX) {
	return mx_fanpic($id,$size,$sex,true);
}

function gets3url($keyname,$timeout=null) {
	global $picturecache,$s3;
	if (array_key_exists($keyname, $picturecache)) return $picturecache[$keyname];
	if ($timeout) $url=$s3->get_object_url(MXS3BUCKET,$keyname,$timeout);
	else $url=$s3->get_object_url(MXS3BUCKET,$keyname);
	$picturecache[$keyname]=$url;
	return $url;
}

function mx_fanpic($id,$size='square',$sex=MXNOSEX,$artist=false) {
	global $mxdb,$mxuser;
	if (!$id) return mx_option('guitarlogo');
	$pic=$mxdb->fanpic($mxuser->id, $id);
	//error_log('fanpic id='.$id.' pic='.print_r($pic,true));
	if ($pic->picture=='local') {
		$keyname='users/'.$pic->hashdir.'/pics/me_'.$size.'.jpg';
		$picurl=mx_secureurl(gets3url($keyname)); //.'?'.round(time()/60);
	} else if (!$pic || !$pic->picture) {
		//error_log('nopic');
		if ($sex==MXNOSEX) $sx='';
		else if ($sex==MXSEXMALE) $sx='_m';
		else $sx='_f';
		if ($artist) $picname='artnopic';
		else $picname='nopic';
		$picurl=mx_option('templateURL').'/icons/'.$picname.$sx.'.png';
	} else if ($pic->fbid) {
			$picurl='http://graph.facebook.com/'.$pic->fbid.'/picture?type='.$size;
	} else $picurl=$pic->picture;
	return str_replace('http://','https://',$picurl);
}

function mx_sendnotice($level,$msg,$page='',$option='',$action='',$section='') {
	global $notices;
	$notices[$level][]=array(
		'text' => $msg,
		'link' => mx_actionlink($page,$option,$action,$section)
	);
}

function mx_formatnotice($notice) {
	return $notice['text'].
		($notice['link']!=''?('<br/>'.$notice['link']):'');
}

function mx_notice() {
	global $notices;
	$noticetypes=array('red','yellow','green');
	foreach ($noticetypes as $noticelevel) {
		if (!array_key_exists($noticelevel,$notices)) continue;
		foreach ($notices[$noticelevel] as $notice) {
			echo '<div class="notice '.$noticelevel.'">'.mx_formatnotice($notice).'</div>';
		}
	}
}


function mx_onlynumbers($str) {
	return preg_replace('%[^0-9]%','',$str);
}


function mx_domain($link) {
	return 'Link';
}

function mxerror($msg,$file,$line,$opt='') {
	$str='Error: '.$msg.'<br/>File:  '.basename($file).' (line '.$line.')<br/>Other: '.$opt;
	error_log($str);
	return $str;
}

function mx_secureword($str) {
	$str=preg_replace('%[^a-zA-Z0-9-_:.]%','',$str);
	return $str;
	}

function mx_secureredir($str) {
	$str=preg_replace('%[^a-zA-Z0-9-_,=:]%','',$str);
	return $str;
	}

function mx_securestring($str) {
	// don't do anything for the moment
	$str=preg_replace('%<[^>]*>%','',$str);
	//error_log('str='.print_r($str,true));
	if ($str) $str=stripcslashes($str);
	return $str;
}

function mx_msgformat($str) {
	$newstr=preg_replace('%\n%','<br/>',$str);
	return $newstr;
}

function mx_gender($gender) {
	global $genders;
	return $genders[$gender];
}

function mx_cleanhtml($html) {
	$html=preg_replace('%[\[][^\]]+[\]]%','',$html);
	$html=preg_replace('%<(/?(b|i|br)/?)>%','[\1]',$html);
	$html=preg_replace('%<[^>]+>%','',$html);
	$html=preg_replace('%\[(/?(b|i|br)/?)\]%','<\1>',$html);
	return $html;
}

function mx_urls2anchors($urls) {
	$urls=preg_replace('%([^.]\.\.+)([^. ])%','\1 \2',$urls); // avoid suspension points to be considered part of an url
	$urls=preg_replace('%(https?://)?((www\.)?([-0-9a-z.]+)(\.([a-z][a-z][a-z](\.[a-z][a-z])?|[a-z][a-z]))([/?]([-0-9a-zA-Z=\%\+_/;.&?#!]*[0-9a-zA-Z#!/])?)?)%',
	'<a href="http://\2" target="_blank" onclick="return warnurl(\'\4\',\'\2\');">\4[\5]</a>',$urls);
	return $urls;
}

function mx_userpic() {
	global $mxuser;
	echo $mxuser->picture();
	//echo '<a href="'.mx_pageurl('account').'">'.$mxuser->picture().'</a>';
}

function mx_urls($urls) {
	$urls=htmlspecialchars($urls);
	if ($urls=='-') return $urls;
	$urls=preg_replace('%[\r\n, \t]+%',',',$urls);
	$urls=preg_replace('%^,|,$%','',$urls);
	$urls=preg_replace('%(http(s?)://)?([^, ]+)%i',' http\2://\3',$urls);
	$urls=mx_urls2anchors($urls);
	$urls=str_replace(',',', ',$urls);
	return $urls;
}
function mx_deleteuser($fbid) {
	global $mxdb;
	$mxdb->deleteuser($fbid);
}

function mx_counton() { // how many users connected ?
	global $mxdb;
	$users=$mxdb->counton();
	echo '<div class="quicknums">';
	echo '<h5>'._('Some Numbers').'</h5>';
	if (is_privileged()) {
		echo '<a href="'.mx_pageurl('whoswhere').'" alt="'.mx_pagename('whoswhere').'">';
		if (!$users['on']) __('Nobody\'s online...');
		else if ($users['on']==1) __('1 user online');
		else echo sprintf(_('%s users online'),$users['on']);
		echo '</a>';
		echo '<br/>';
		echo '<a href="'.mx_optionurl('fans','fandir').'" alt="'.mx_optionname('fans','fandir').'">';
		if (!$users['fans']) __('No fan registered...');
		else if ($users['fans']==1) __('1 fan registered');
		else echo sprintf(_('%s fans registered'),$users['fans']);
		echo '</a>';
		echo '<br/>';
		echo '<a href="'.mx_optionurl('artists','artsdir').'" alt="'.mx_optionname('artists','artsdir').'">';
		if (!$users['artists']) __('No artist registered...');
		else if ($users['artists']==1) __('1 artist registered');
		else echo sprintf(_('%s artists registered'),$users['artists']);
		echo '</a>';
		echo '<hr/>';
	}
	echo sprintf(_('%s visitors'),'<span id="mxvisits">'.$users['visitors'].'</span>');
	echo '<br/>';
	//echo sprintf(_('%d connections'),$users['connections']);
	//echo '<br/>';
	echo sprintf(_('%s hits'),'<span id="mxhits">'.$users['hits'].'</span>');
	echo '<br/>';
	echo '<script>cnttmr=setInterval(\'checkvisits()\',10000);</script>';
	echo '</div>';
}

function mx_showhtmlpage($page) {
	global $windowedpages;
	$html=mx_showhtmlpagestr($page);
	if ($html) {
		echo $html;
	} else {
		__('(Content not yet ready)');
	}
}

function mx_showhtmlpagestr($page,$windowedpages=array()) {
	global $mxuser,$acctypes,$browser,$prodtypes;
	if (array_key_exists($page,$windowedpages) /* $page$windowedpages[$page]==1 */) return '';
	$windowedpages[$page]=1;
	$locale=$mxuser->locale?$mxuser->locale:'en_US';
	$locpage=mx_option('pagesdir').'/'.$locale.'/'.$page.'.html';
	if (file_exists($locpage))
		$html=file_get_contents(mx_option('pagesdir').'/'.$locale.'/'.$page.'.html');
	else {
		$locpage=mx_option('pagesdir').'/en_US/'.$page.'.html';
		if (file_exists($locpage)) {
			$html=file_get_contents(mx_option('pagesdir').'/en_US/'.$page.'.html');
			$html='<div class="nottranslated">'.
				_('(This page will be translated to your language soon)').'</div>'.$html;
		}
	}
	//$html=preg_replace('%\n\n%','<br/><br/>',$html);
	$html=preg_replace('%(\n\n|$)%','</p>\1',$html);
	$html=preg_replace('%[\n]\* ([^\n]+)%','<li>\1</li>',$html);
	$html=preg_replace('%(^|\n\n)([\([a-zA-Z0-9].*)%','<p>\2',$html);
	$html=preg_replace('%{playicon:([^}]+)}%',
		'<a class="playbutton" href="javascript:play(\'$1\');">'.
		mx_icon('playsound','listen',16,'i_$1').'</a>',$html);
	$html=preg_replace('%{mail:([^},]+),([^},]+),([^}]+)}%',
		'<a href="mailto:$1?Subject=$3" alt="$2">$2</a>',$html);
	if (preg_match_all('%{menu:([^},]+),?([^},]+)?,?([^}]+)?}%',$html,$menus)) {
		for ($i=0; $i<count($menus[0]);$i++) {
			$html=str_replace($menus[0][$i],mx_optionlink($menus[1][$i],$menus[2][$i],$menus[3][$i]),$html);
		}
	}
	if (preg_match_all('%{icon:([^},]+),?([^},]+)?}%',$html,$icon)) {
		for ($i=0; $i<count($icon[0]);$i++) {
			$html=str_replace($icon[0][$i],mx_icon($icon[1][$i],$icon[2][$i],'48','act_'.$icon[1][$i],$icon[1][$i].'hover'),$html);
		}
	}
	$html=str_replace('{PRIVACY}',_('Privacy Policy'),$html);
	if ($browser) {
		$mybrowser=sprintf('<b>%s, version %s, on %s %s</b>',$browser->getBrowser(),$browser->getVersion(),
		$browser->getPlatform(),$browser->isAol()?(' [AOL Version'.$browser->getAolVersion().']'):''.$browser->isMobile()?_(' (Mobile)'):'');
	} else $mybrowser='Undefined';
	$html=str_replace('{GOBACK}','<a href="javascript:history.back();">'._('Back').'</a>',$html);
	$html=str_replace('{FACEBOOK_APP_ID}',FACEBOOK_APP_ID,$html);
	$html=str_replace('{BROWSER}',$mybrowser,$html);
	$html=str_replace('{TERMS}',_('Terms & Conditions'),$html);
	$html=str_replace('{SALESTERMS}',_('Sales Terms & Conditions'),$html);
	if (preg_match_all('%{window:([^},]+),?([^},]+)?}%',$html,$wins)) {
		for ($i=0; $i<count($wins[0]);$i++) {
			$wpage=mx_windowedpage($wins[1][$i],$wins[2][$i],true,$windowedpages);
			$html=str_replace($wins[0][$i],$wpage['str'],$html);
			$html.=$wpage['div'];
		}
	}

	if (strpos($html,'{ADDBASIC}')>0) {
		$html=str_replace('{ADDBASIC}',
		'<table class="buymedia"><tr><td>'.mx_icon('cartmedia',_('Add to Cart'),'24px','bs_basicsub','cartmediahover')
		.'</td><td>'.sprintf('$%.2f',MXFEEBASIC).'</td></tr></table>',$html);
	}
	if (strpos($html,'{ADDPLUS}')>0) {
		$html=str_replace('{ADDPLUS}',
		'<table class="buymedia"><tr><td>'.mx_icon('cartmedia',_('Add to Cart'),'24px','bs_plussub','cartmediahover')
		.'</td><td>'.sprintf('$%.2f',MXFEEPLUS).'</td></tr></table>',$html);
	}
	if (strpos($html,'{ADDPREMIUM}')>0) {
		$html=str_replace('{ADDPREMIUM}',
		'<table class="buymedia"><tr><td>'.mx_icon('cartmedia',_('Add to Cart'),'24px','bs_premsub','cartmediahover')
		.'</td><td>'.sprintf('$%.2f',MXFEEPREMIUM).'</td></tr></table>',$html);
	}
	if (strpos($html,'{ADDFREE}')>0) {
		$html=str_replace('{ADDFREE}',
		'<table class="buymedia"><tr><td>'.mx_icon('cartmedia',_('Add to Cart'),'24px','bs_freesub','cartmediahover')
		.'</td><td>'.sprintf('$%.2f',MXFEEFREE).'</td></tr></table>',$html);
	}

	$html=str_replace('{SIGNIN}',mx_icon('enterdrop',_('Enter - Sign in or sign up'),48,'act_enterdrop','enterdrophover'),$html);
	$html=str_replace('{ACCTYPE}',$acctypes[$mxuser->acctype],$html);
	$html=str_replace('{FOY}',MXFEEFOY,$html);
	$html=str_replace('{FOFA}',MXFEEFOFA,$html);
	$html=str_replace('{FOFAUPG}',sprintf('%.2d',MXFEEFOFA-MXFEEFOY),$html);
	$html=str_replace('{FOFAYRS}',MXFOFAYRS,$html);
	$html=str_replace('{FREE}',MXFEEFREE,$html);
	$html=str_replace('{BASIC}',MXFEEBASIC,$html);
	$html=str_replace('{PLUS}',MXFEEPLUS,$html);
	$html=str_replace('{PREMIUM}',MXFEEPREMIUM,$html);
	$html=str_replace('{1SONG}',MXFEE1SONG,$html);
	$html=str_replace('{SONGS}',MXFEESONGS,$html);
	$html=str_replace('{SETUPFEE}',MXFEESETUP,$html);
	$html=str_replace('{MAXINVITES}',MXMAXINVITES,$html);
	$html=str_replace('{siteurl}',mx_option('siteurl'),$html);
	$html=str_replace('{googlemap}','<div class="googlemap"><iframe width="300" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.ca/maps/ms?msa=0&amp;msid=217503076480382472301.0004a98d653fcbd092ac2&amp;hl=en&amp;ie=UTF8&amp;ll=49.205402,-123.134594&amp;spn=0.008412,0.012875&amp;z=15&amp;output=embed"></iframe><br /><small>View <a href="http://maps.google.ca/maps/ms?msa=0&amp;msid=217503076480382472301.0004a98d653fcbd092ac2&amp;hl=en&amp;ie=UTF8&amp;ll=49.205402,-123.134594&amp;spn=0.008412,0.012875&amp;z=15&amp;source=embed" style="color:#0000FF;text-align:left">MusXpand</a> in a larger map</small></div>',$html);
	$html=preg_replace('%{aboutimg:([^,}]+)(,([^}]+))?}%',
		'<img class="about" src="'.mx_option('siteurl').'/images/about/$1" alt="$3" />',$html);
	$html=preg_replace('%{aboutpic:([^,}]+)(,([^}]+))?}%',
		'<div class="about"><img src="'.mx_option('siteurl').'/images/about/$1-comic.jpg" alt="$3" ' .
				'onmouseover="this.src=\''.mx_option('siteurl').'/images/about/$1-normal.jpg'.'\';" ' .
				' onmouseout="this.src=\''.mx_option('siteurl').'/images/about/$1-comic.jpg'.'\';"/>' .
				'<br/>$3</div>',$html);
	/*$html=preg_replace('%{officepic:([^,}]+)(,([^}]+))?}%',
		'<img class="officepic" src="http://www.example.com/webcam/$1?'.time().'" alt="$3" />',$html);*/
	$html=preg_replace('%{seismograph:([^,}]+)(,([^}]+))?}%',
		'<a href="'.MXPRODSITE.'/$1" target=_blank><img class="seismograph" src="'.MXPRODSITE.'/images/about/$1?'.time().'" alt="$3" /></a>',$html);
	$html=str_replace('{firstname}',($mxuser->firstname?$mxuser->firstname:$mxuser->fullname),$html);
	$registerurl=mx_optionurl('account','register').'?i='.$mxuser->invitecode;
	$registerlink='<a href="'.$registerurl.'" alt="'._('Register').'">MusXpand</a>'.sprintf(_(' (link: %s)'),$registerurl);
	$html=str_replace('{REGISTERURL}',$registerlink,$html);
	$html=str_replace('{HIS_HER}',$mxuser->gender==MXSEXMALE?'his':($mxuser->gender==MXSEXFEMALE?'her':'their'),$html);
	$html=str_replace('{FRIENDNAME}',$mxuser->fullname.($mxuser->acctype==MXACCOUNTARTIST?(' ('.$mxuser->artistname.')'):''),$html);
	$html=str_replace('{MUSXPANDLOGO}','<img src="'.mx_option('logoURL').'" alt="'._('Logo').'" height="48"/>',$html);
	$html=str_replace('{M-LOGO}','<img src="'.mx_option('m-logoURL').'" alt="'._('Logo').'" height="48"/>',$html);
	$html=str_replace('{GUITARLOGO}','<img src="'.mx_option('guitarlogo').'" alt="'._('Logo').'" />',$html);
	$html=str_replace('{EMAIL}',$mxuser->email,$html);
	$html=str_replace('{SENDER}',($mxuser->fullname?$mxuser->fullname:$mxuser->artistname),$html);
	$html=str_replace('{localtime}',date('r'),$html);
	$html=str_replace('{RULE}','<hr>',$html);
	$html=str_replace('{TOP}','<a class="backtotop" href="#pagetop">'._('Back').'</a>',$html);
	$html=str_replace('[','<',$html);
	$html=str_replace(']','>',$html);
	return $html;
}

function mx_windowedpage($wpage,$title,$sep=false,$windowedpages=array()) {
	$pg=mx_showhtmlpagestr($wpage,$windowedpages);
	if ($pg!='') {
		$div='<div class="newhelp"><div id="wp_'.$wpage.'" title="'.$title.'">';
		/*$div='<div class="helppage" id="wp_'.$wpage.'"><div><div class="menubar">'.$title
		.'<div class="closebutton" onclick="hidewindow(\'wp_'.$wpage.'\');">'.'X'
		.'</div></div><div class="helpcontent">';*/
		$div.=$pg;
		$div.='</div></div>';
		//$div.='</div></div></div>';
		//$str='<a href="javascript:showwindow(\'wp_'.$wpage.'\');" alt="'.$title.'">'.$title.'</a>';
		$str='<a onclick="$(\'#wp_'.$wpage.'\').dialog({position:\'center\',width:800,height:600,zIndex:4000,closeOnEscape:true});" href="javascript:;">'.$title.'</a>';
	} else { $div=''; $str=$title; }
	return ($sep)?array('div' => $div, 'str' => $str):($str.$div);
}

function mx_checkvalues(&$user) {
	if ($user->age <= 0 || $user->age > 130) $user->age=MXNOTINFORMED;
	if (!$user->birthday) $user->birthday=MXNOTINFORMED;
	if (!$user->website) $user->website=MXNOTINFORMED;
	if (!$user->email) $user->email=MXNOTINFORMED;
	if (!$user->shortbio) $user->shortbio='...';
	if (!$user->longbio) $user->longbio=MXNOTINFORMED;
	if ($user->transparency>50) $user->transparency=50;
	if ($user->gender=='') $user->gender=-1;
	if ($user->mediacnt=='') $user->mediacnt=MXNOTINFORMED;
	if ($user->mediasize=='') $user->mediasize=MXNOTINFORMED;
	if ($user->pubcnt=='') $user->pubcnt=MXNOTINFORMED;
	if ($user->pubsize=='') $user->pubsize=MXNOTINFORMED;
	if ($user->subcnt=='') $user->subcnt=MXNOTINFORMED;
	if ($user->subfoy=='') $user->subfoy=MXNOTINFORMED;
	if ($user->subfofa=='') $user->subfofa=MXNOTINFORMED;
	if ($user->sublike=='') $user->sublike=MXNOTINFORMED;
}

function mx_showcustompage($page,$user,$media=null,$simul='') {
	global $mxuser,$transparencies,$windows,$mxdb;
	if ($simul=='public') {
		$saveuser=$mxuser;
		$mxuser=new MXUser(-1);
		$user=$mxuser->getuserinfo($user->id,true); // get public info instead...
	}
	mx_checkvalues($user);
	//error_log(print_r($user,true));
	$locale=$mxuser->locale?$mxuser->locale:'en_US';
	$locpage=mx_option('pagesdir').'/'.$locale.'/'.$page.'.html';
	if (file_exists($locpage))
		$html=file_get_contents(mx_option('pagesdir').'/'.$locale.'/'.$page.'.html');
	else {
		$locpage=mx_option('pagesdir').'/en_US/'.$page.'.html';
		if (file_exists($locpage)) {
			$html=file_get_contents(mx_option('pagesdir').'/en_US/'.$page.'.html');
			//$html='<div class="nottranslated">'.
				//_('(This page will be translated to your language soon)').'</div>'.$html;
		}
	}
	$html=preg_replace('%{playicon:([^}]+)}%',
		'<a class="playbutton" href="javascript:play(\'$1\');">'.
		mx_icon('playsound','listen',16,'i_$1').'</a>',$html);
	$html=preg_replace('%{mail:([^},]+),([^},]+),?([^}]+)}%',
		'<a href="mailto:$1?Subject=$3" alt="$2">$2</a>',$html);
	if (preg_match_all('%{menu:([^},]+),?([^}]+)?}%',$html,$menus)) {
		for ($i=0; $i<count($menus[0]);$i++) {
			$html=str_replace($menus[0][$i],mx_optionlink($menus[1][$i],$menus[2][$i]),$html);
		}
	}
	$html=preg_replace('%{aboutimg:([^,}]+)(,([^}]+))?}%',
		'<img class="about" src="'.mx_option('siteurl').'/images/about/$1" alt="$3" />',$html);
	$html=str_replace('{firstname}',($mxuser->firstname?$mxuser->firstname:$mxuser->fullname),$html);
	$html=str_replace('[','<',$html);
	$html=str_replace(']','>',$html);
	// user stuff
	$user->subs=$mxuser->getsub($user->id);
	$stotal=$sfoy=$sfofa=$slike=0;
	foreach ($user->subs as $k => $sub) {
		++$stotal;
		if ($sub->subtype==MXSUBFOY) ++$sfoy;
		else if ($sub->subtype==MXSUBFOFA || $sub->subtype==MXUPGFOFA) ++$sfofa;
		else if ($sub->subtype==MXSUBLIKE) ++$slike;
	}
	$html=str_replace('{PIC}','<img tag="'.$user->id.'" src="'.mx_fanpic($user->id,'large').'" />',$html);
	$html=str_replace('{ARTISTPIC}','<img tag="'.$user->id.'" class="artistpic" src="'.mx_artpic($user->id,'large',$user->gender)
		.'" itemprop="image"/>'
		//.'<div class="picstamp"><img class="picstamp" src="'.mx_iconurl('artistlogo').'"/></div>'
		,$html);
	//if ($user->acctype==MXACCOUNTFAN) {
	if ($sfofa+$sfoy>=10) $fanrank='n1fan';
	else if ($sfofa+$sfoy>=1) $fanrank='truefan';
	else $fanrank='fanlogo';
	//}
	$html=str_replace('{FANPIC}','<img tag="'.$user->id.'" class="fanpic" src="'.mx_fanpic($user->id,'large',$user->gender)
		.'" itemprop="image"/>'
		.'<div class="picstamp"><img class="picstamp" src="'.mx_iconurl($fanrank).'"/></div>',$html);
	$html=str_replace('{ARTISTNAME}',mx_getartistname($user),$html);
	$html=str_replace('{LASTSEEN}',mx_difftime($user->lastseen),$html);
	$html=str_replace('{NAME}',mx_getname($user),$html);
	$html=str_replace('{FULLNAME}',mx_getname($user),$html);
	if (strpos($html,'{BACKGROUND}')>0) {
		$bg=$mxuser->getbackgroundurl($user->background_id);
		$html=str_replace('{BACKGROUND}',$bg,$html);
		if (!strpos($bg,'tiled')) $html=str_replace('{BACKGROUNDSIZE}','background-size:100%;',$html);
	}
	// find transparency background
	$opt=90;
	if (array_key_exists($user->transparency,$transparencies)) $opt=100-($user->transparency);
	if ($opt<50) $opt=50;
	$whitebg=$opt?(mx_option('siteurl').'/images/background/white-dot-'.$opt.'.png'):'';
	$yellowbg=$opt?(mx_option('siteurl').'/images/background/yellow-dot-'.$opt.'.png'):'';
	$html=str_replace('{WHITEBG}',$whitebg,$html);
	$html=str_replace('{YELLOWBG}',$yellowbg,$html);
	$html=str_replace('{OPACITY}',1-$user->transparency/100,$html);
	$html=str_replace('{OPACITY100}',100-$user->transparency,$html);
	$html=str_replace('{BIO}',mx_urls2anchors(mx_cleanhtml($user->longbio)),$html);
	$html=str_replace('{AGE}',$user->age,$html);
	$html=str_replace('{BDAY}',$user->birthday,$html);
	$html=str_replace('{ABOUT}',mx_cleanhtml($user->shortbio),$html);
	$html=str_replace('{GENDER}',mx_gender($user->gender),$html);
	$html=str_replace('{WEBSITE}',mx_urls($user->website),$html);
	$html=str_replace('{EMAIL}',$user->email,$html);
	$artlinks='<ul>';
	if ($user->username) {
		foreach(array('artist','artists','art','arts','at') as $aurl) {
			$link='http://'.$user->username.'.'.$aurl.'.example.com';
			$artlinks.='<li><a href="'.$link.'">'.$link.'</a></li>';
		}
		$link='http://www.example.com/a/'.$user->username;
		$artlinks.='<li><a href="'.$link.'">'.$link.'</a></li>';
	}
	$link='http://www.example.com/artists/artprof?a='.$user->id;
	$artlinks.='<li><a href="'.$link.'">'.$link.'</a></li>';
	$artlinks.='</ul>';
	$html=str_replace('{ARTLINKS}',$artlinks,$html);
	$fanlinks='<ul>';
	if ($user->username) {
		foreach(array('fan','fans') as $furl) {
			$link='http://'.$user->username.'.'.$furl.'.example.com';
			$fanlinks.='<li><a href="'.$link.'">'.$link.'</a></li>';
		}
		$link='http://www.example.com/f/'.$user->username;
		$fanlinks.='<li><a href="'.$link.'">'.$link.'</a></li>';
	}
	$link='http://www.example.com/fans/fanprof?a='.$user->id;
	$fanlinks.='<li><a href="'.$link.'">'.$link.'</a></li>';
	$fanlinks.='</ul>';
	$html=str_replace('{FANLINKS}',$fanlinks,$html);
	if (strpos($html,'{STYLES}')>0 || strpos($html,'{TASTES}')>0) {
		$genres=$mxdb->listgenres();
		$cats=array();
		$subgenres=array();
		foreach ($genres as $genre){
			if (!$genre->cat) {
				$cats[$genre->hash]=$genre->id;
				$subgenres[$genre->id]=array();
			}
		}
		foreach ($genres as $genre){
			if ($genre->cat) {
				$subgenres[$cats[$genre->cat]][]=$genre->id;
				$catgenre[$genre->id]=$cats[$genre->cat];
			}
		}
		if (strpos($html,'{STYLES}')>0) $value=$user->genres;
		else $value=$user->tastes;
		$str='<table class="genres">';
		$ok=0;
		for ($i=0; $i<5; $i++) {
			$genre=$value[$i];
			if ($genre) {
				$cat=$catgenre[$genre];
				$str.='<tr><th>'.(!$i?_('Primary'):($i==1?_('Secondary'):_('Other'))).':</th><td>'.$genres[$genre]->genre
				.($cat?(' ('.$genres[$cat]->genre.')'):'').'</td></tr>';
				$ok=1;
			}
		}
		$str.='</table>';
		if (!$ok) $str=_('Not informed');
		$html=str_replace('{TASTES}',$str,$html);
		$html=str_replace('{STYLES}',$str,$html);
	}
	if (strpos($html,'{MEDIA}')>0) {
		//$mediatable=$mxuser->listartistmedia($user->id);
		//$html=str_replace('{MEDIA}',mx_showmediastr($mediatable),$html);
		//$mediatable=$mxuser->listartistmedia($user->id);
		$googlecrawler=mx_securestring($_GET['_escaped_fragment_']);
		$ob=preg_replace('%[^0-9]%','',$googlecrawler);
		//error_log('ob='.$ob.' / media->id='.$media->id);
		$mediatable=mx_showmediastr($user->id,'media',($ob?$ob:$media->id));
		$html=str_replace('{MEDIA}',$mediatable,$html);
	}
	$location='';
	if ($user->city) $location.='<span itemprop="addressLocality">'.$user->city.'</span>';
	if ($user->state) $location.=($location?', ':'').'<span itemprop="addressRegion">'.$user->state.'</span>';
	if ($user->country) $location.=($location?', ':'').'<span itemprop="addressCountry">'.mx_getcountryname($user->country).'</span>';
	$html=str_replace('{LOCATION}',$location,$html);
	$html=str_replace('{FACELIKE}',
		'<fb:like href="'.mx_actionurl_prod('artists','artprof',$user->id).'" send="false" show_faces="false" width="60" font=""></fb:like>'
		,$html);
	$html=str_replace('{PLUSONE}',
		'<g:plusone size="medium" href="'
		.mx_actionurl_prod('artists','artprof',$user->id).'" callback="mxpluslike"></g:plusone>',$html);
	$html=str_replace('{FANFACELIKE}',
		'<fb:like href="'.mx_actionurl_prod('fans','fanprof',$user->id).'" send="false" show_faces="true" width="60" font=""></fb:like>'
		,$html);
	$html=str_replace('{FANPLUSONE}',
		'<g:plusone size="medium" href="'
		.mx_actionurl_prod('fans','fanprof',$user->id).'"></g:plusone>',$html);
	$hassub=0;
	if (strpos($html,'{SUBSCRIBERS}')>0) {
		$html=str_replace('{SUBSCRIBERS}',mx_subscribers($user->id,false),$html);
	}
	if (strpos($html,'{LIKERS}')>0) {
		$html=str_replace('{LIKERS}',mx_subscribers($user->id,true),$html);
	}
	if (strpos($html,'{SUBSCRIPTIONS}')>0 || strpos($html,'{LIKES}')>0 || strpos($html,'{SUBSCRIBE}')>0) {
		if (strpos($html,'{SUBSCRIPTIONS}')>0) {
			$html=str_replace('{SUBSCRIPTIONS}',mx_subscriptions($user->subs,false),$html);
		}
		if (strpos($html,'{LIKES}')>0) {
			$html=str_replace('{LIKES}',mx_subscriptions($user->subs,true),$html);
		}
	}
	$mysubs=$mxuser->getsub();
	foreach ($mysubs as $sub) {
		if ($sub->subcat==MXARTSUB && $sub->objectid==$user->id && $sub->status!=MXEXPIREDSUB) {
			$hassub=$sub->subtype;
			$autorenew=$sub->renewal;
			break;
		}
	}
	if (strpos($html,'{ILOVE}')>0) {
		if (!$mxuser->id) $str='';
		else if ($hassub==MXSUBLIKE) { // likers
			$str=mx_icon('ilove',_('Like!'),'','nl_'.$user->id,'ilovehover','class');
		} else if ($hassub) { // fan love
			$str=mx_icon('fanlove',_('Fan Love'),'');
		} else if ($mxuser->id==$user->id) {
			$str=mx_icon('melove',_('Love Me'),'');
		} else {
			$str=mx_icon('nolove',_('I LOVE THIS!'),'','il_'.$user->id,'nolovehover','class');
		}
		$html=str_replace('{ILOVE}',$str,$html);
	}
	if (strpos($html,'{SUBSCRIBE}')>0 && $mxuser->id && $mxuser->id!=$user->id && $user->status!=MXACCTDISABLED
		&& ($user->pubcnt>=MXMINIMUMMEDIA || is_admin())) {
		$subscribefoy=_('1-Year: ${FOY}');
		//$subscribefofa=_('{FOFAYRS}: ${FOFA}');
		//$upgradefofa=_('Upgrade: ${FOFAUPG}');
		$subfoybtn=array(1,$subscribefoy,'g-button','subs1year',
			'<b>F</b>an <b>O</b>ne <b>Y</b>ear (FOY)<br/>This fanship entitles you to <b>listen to and download all media</b>, <u>from this artist'
			.' exclusively</u>, <b>during 1 year</b>. After the first renewal in 1 year, '
			.'you will get access to this artist\'s media forever.');
		$subfofabtn=array(1,$subscribefofa,'g-button','subsforever',
			'<b>F</b>an <b>O</b>nce <b>F</b>an <b>A</b>lways (FOFA)<br/>This fanship entitles you to <b>download all media</b>, <u>from this artist' .
			' exclusively</u>, <b>forever</b>');
		$subfofaupgbtn=array(1,$upgradefofa,'g-button','subsforeverupg',
			'This upgrade entitles you to <b>download all media</b>, <u>from this artist' .
			' exclusively</u>, <b>forever</b>');
		if ($hassub==MXSUBFOFA || $hassub==MXUPGFOFA) {
			$subscribetxt=_('You\'re a Fan, FOREVER!');
			$cansubfoy=false;
			$cansubfofa=false;
			$canupgfofa=false;
		} else if ($hassub==MXSUBFOY) {
			if ($autorenew) {
				$subscribetxt=_('Auto-Renewal Enabled');
			} else {
				$subscribetxt=_('Auto-Renewal Disabled');
			}
			$cansubfoy=false;
			$cansubfofa=false;
			$canupgfofa=false;
		} else {
			$subscribetxt=sprintf(_('Subscribe to this %s'),
				($user->acctype==MXACCOUNTARTIST?_("Artist"):_("Band")));
			$cansubfoy=true;
			$cansubfofa=false;
			$canupgfofa=false;

		}
		$addcart='<form style="display:inline" name="addtocart" action="'.mx_pageurl('cart').'" method="POST">'
			.'<div class="title"><img src="'.mx_option('templateURL').'/icons/cart.png" />'
			.' '.$subscribetxt.'</div>'
			.'<input type="hidden" name="id" value="'.$user->id.'">'
			.'<input type="hidden" name="a" value="">'
			.($cansubfoy?(mx_formfield('addfoy',$subscribefoy,$subfoybtn)):'')
			.($cansubfofa?(mx_formfield('addfofa',$subscribefofa,$subfofabtn)):'')
			.($canupgfofa?(mx_formfield('upgfofa',$upgradefofa,$subfofaupgbtn)):'')
			.'</form>';
		$html=str_replace('{SUBSCRIBE}',$addcart,$html);
	} /* else if ($user->pubcnt<MXMINIMUMMEDIA && !is_admin()) {
		$html=str_replace('{SUBSCRIBE}','(This artist has not uploaded enough media to allow fanships)',$html);
	} */ else if ($user->status==MXACCTDISABLED) {
		$html=str_replace('{SUBSCRIBE}',_('Sorry, this account is no more active.'),$html);
	} else if ($mxuser->id==$user->id) {
		$html=str_replace('{SUBSCRIBE}',_('Hey! You\'re an artist!!'),$html);
	} else {
		$logfirst='<div class="title"><img src="'.mx_option('templateURL').'/icons/cart.png" /> '
		._('to become a FAN...').'</div>'
		.sprintf(_('%s or %s'),
			'<a href="'.mx_actionurl('account', 'signin','','','artists,artprof,'.$user->id)
			.'" alt="'._('Sign-in').'">'.mx_icon('signinbtn',_('Sign-in'),'','xx','signinbtnhover').'</a>',
			'<a href="'.mx_actionurl('account', 'register','','','artists,artprof,'.$user->id,'','i='.$user->invitecode)
			.'" alt="'._('Register').'">'.mx_icon('registerbtn',_('Register'),'','xx','registerbtnhover').'</a>');
		$html=str_replace('{SUBSCRIBE}',$logfirst,$html);
	}
	// fans stats
	$fanstats='<table><tr><th>'._('Stats').'</th></tr>'
	.'<tr><td><a href="javascript:tabswitch(\'GENERAL\',\'SUBSCRIPTIONS\');">'._('Fanships').'</a>'
	.'<br/>'._('FOFA:').' '.$sfofa
	.'<br/>'._('FOY:').' '.$sfoy
	.'<br/>'._('Likes:').' '.$slike
	.'<br/><span class="stattotal">'._('Total:').' '.$stotal.'</span>'
	.'</td></tr>'
	.'</table>';
	$html=str_replace('{FANSTATS}',$fanstats,$html);
	// artists stats
	$stats='<table><tr><th>'._('Stats').'</th></tr>'
	.'<tr><td class="first"><a href="javascript:tabswitch(\'GENERAL\',\'MEDIA\');">'._('Media').'</a>'
	.'<br/><span class="stathdr">'._('Uploaded').'</span><br/>'.$user->mediacnt
		.($user->mediacnt!=MXNOTINFORMED?(' ('.mx_size($user->mediasize).')'):'')
	.'<br/><span class="stathdr">'._('Published').'</span><br/>'.$user->pubcnt.' ('.mx_size($user->pubsize).')</td></tr>'
	.'<tr><td><a href="javascript:tabswitch(\'GENERAL\',\'SUBSCRIBERS\');">'._('Fans').'</a>'
	.'<br/>'._('FOFA:').' '.$user->subfofa
	.'<br/>'._('FOY:').' '.$user->subfoy
	.'<br/>'._('Likes:').' '.$user->sublike
	.'<br/><span class="stattotal">'._('Total:').' '.$user->subcnt.'</span>'
	.'</td></tr>'
	.'</table>';
	$html=str_replace('{STATS}',$stats,$html);
	$html=str_replace('{FOY}',MXFEEFOY,$html);
	$html=str_replace('{FOFA}',MXFEEFOFA,$html);
	$html=str_replace('{FOFAUPG}',sprintf('%.2d',MXFEEFOFA-MXFEEFOY),$html);
	$html=str_replace('{FOFAYRS}',MXFOFAYRS,$html);
	$html=str_replace('{FREE}',MXFEEFREE,$html);
	$html=str_replace('{BASIC}',MXFEEBASIC,$html);
	$html=str_replace('{PLUS}',MXFEEPLUS,$html);
	$html=str_replace('{PREMIUM}',MXFEEPREMIUM,$html);
	$html=str_replace('{1SONG}',MXFEE1SONG,$html);
	$html=str_replace('{SONGS}',MXFEESONGS,$html);
	$html=str_replace('{SETUPFEE}',MXFEESETUP,$html);
	$html=str_replace('{siteurl}',mx_option('siteurl'),$html);
	$html=str_replace('{WALL}',mx_showuserwallstr($user),$html);
	$html=str_replace('{MYREVIEWS}',_('Available Soon'),$html);
	$chats='<iframe class="chat" src="../ext_includes/chat/index.php"></iframe>';
	$html=str_replace('{SHOWS}',_('Available Soon'),$html);
	$html=str_replace('{CHATS}',_('Available Soon'),$html);
	$html=str_replace('{MENTIONS}',_('Available Soon'),$html);
	$fbreviews='<div class="fb-comments" data-href="'.mx_actionurl('artists','artprof',$user->id).'" data-num-posts="10" data-width="540"></div>';
	$html=str_replace('{REVIEWS}',$fbreviews,$html);
	if (strpos($html,'{SHARE}')>0) $tools=mx_sharetools($user->id,true); // artists tools
	if (strpos($html,'{SHAREFAN}')>0) $tools=mx_sharetools($user->id,false); // fans tools
	$html=str_replace('{SHARE}',$tools,$html); // for artists
	$html=str_replace('{SHAREFAN}',$tools,$html); // for fans
	$html=str_replace('{QRCODE}',mx_qrcode($user,true),$html); // for artists
	$html=str_replace('{QRCODEFAN}',mx_qrcode($user,false),$html); // for fans

	// build tabs if needed
	$realnames=array(
		'MEDIA'	=> _('Media'),
		'GENERAL'	=>	_('Info'),
		'WALL'	=> _('Wall'),
		'SHOWS'	=> _('Shows'),
		'MENTIONS'	=> _('Mentions'),
		'REVIEWS'	=> _('Reviews'),
		'SUBSCRIBERS'	=> _('Fans'),
		'LIKERS'	=> _('Likers'),
		'MYREVIEWS'	=> _('My Reviews'),
		'SUBSCRIPTIONS'	=> _('Fanships'),
		'LIKES'	=> _('Likes'),
		'FRIENDS'	=> _('Friends'),
		'CHATS'	=> _('Chats'),
		'SHARE'	=> _('Share!'),
		'RESTRICTED'	=> _('RESTRICTED'),
	);
	if (strpos($html,'{TABS}')>0) {
		$html=str_replace('{TABS}','<div class="form"><table><tr><td>',$html);
		$html=str_replace('{/TABS}','</td></tr></table></div>',$html);
		$tabs=preg_match_all('%{TAB:([*+]?)([^}]+)}%',$html,$tabnames);
		$group=mx_secureword($_REQUEST['k']);
		if (!$group || !array_search($group, $tabnames[2])) {
			$group=$tabnames[2][0];
		}
		for($i=0;$i<$tabs;$i++) {
			//$newtab='<div id="artist_'.($form?'f_':'').$i.'" class="'.$style.((($i==$group) && !$form)?'':' hidden').'">';
			$newtab='<div id="'.$tabnames[2][$i].($form?'_f':'').'" class="'.$style.((($tabnames[2][$i]==$group) && !$form)?'':' hidden').'">';
			//$newtab.='<form name="artist_'.$i.'" method="POST" enctype="multipart/form-data"' .
			//		' onsubmit="return checkform(\'artist_'.$i.'\');">';
			$newtab.='<table><tr><td>';
			$newtab.='<fieldset>';
			for ($j=0;$j<$tabs;$j++) {
				$tabclass='';
				if ($tabnames[1][$j]=='*') {
					if (!is_admin()) continue;
					$tabclass=' admin';
				} else if ($tabnames[1][$j]=='+') {
					if ($mxuser->id!=$user->id && !is_admin()) continue;
					if ($mxuser->id==$user->id) $tabclass=' owner';
					else $tabclass=' admin';
				}
				if ($i==$j) $newtab.='<legend class="seltab'.$tabclass.'">'.$realnames[$tabnames[2][$i]].'</legend>';
				else {
					$newtab.='<legend class="tab'.$tabclass.'"><a href="javascript:tabswitch(\''.$tabnames[2][$i].'\',\''.$tabnames[2][$j].'\');"'
					.' alt="'.$realnames[$tabnames[2][$j]].'">'.$realnames[$tabnames[2][$j]]
					.'</a></legend>';
				}
			}
			$html=str_replace($tabnames[0][$i],$newtab,$html);
		}
		//$html=str_replace('{/TAB}','</td></tr></table></fieldset></td></tr>',$html);
		$str='</fieldset></td></tr></table>';
		//$str.='</form>';
		$str.='</div>';
		$html=str_replace('{/TAB}',$str,$html);
	}
	// link to add as a friend
	if ($user->id==$mxuser->id) {
		$befriend=_('Hey, this is you!!');
	} else if ($mxuser->isfriend($user->id)) {
		$befriend=sprintf('%s<br/>%s',
		_('This is one of your friends'),
		'<a href="'.mx_actionurl('account','messages','sm:'.$user->id,'writemsg').'">'._('Send a Message').'</a>');
		if ($user->acctype==MXACCOUNTARTIST)
		$befriend.='<br/><a href="'.mx_actionurl('artists','artprof',$user->id).'">'._('See Artist Page').'</a>';
	} else {
		$befriend='<a href="'
		.mx_actionurl('account','messages','af:'.$user->id,'writemsg')
		.'">'._('Request Friendship').'</a>';
		/*
		if (is_admin()) {
			$befriend.='<br/><a href="'.mx_actionurl('artists','artprof',$user->id).'">'._('See Artist Page [ADM]').'</a>';
		}
		*/
	}
	$html=str_replace('{BEFRIEND}',$befriend,$html);
	$friends=$mxuser->getfriends($user->id);
	if (!$friends) $friendslist=_('This person\'s friends list is hidden.');
	else if (!count($friends['confirmed'])) $friendslist=_('This person\'s friends list is empty.');
	else $friendslist=mx_showdirstr($friends['confirmed']);
	$html=str_replace('{FRIENDS}',$friendslist,$html);
	$html=preg_replace('%\r?\n\r?\n%','<br/><br/>',$html);

	// graphic stats
	if (strpos($html,'{MYSTATS}')>0 && ($user->id==$mxuser->id || is_admin())) {
		$mystats=mx_statsstr($user);
		$html=str_replace('{MYSTATS}',$mystats,$html);
	}

	$html=str_replace('{PRICE}','<table class="buymedia"><tr><td>',$html);
	$html=str_replace('{PRICE2}','</td><td>',$html);
	$html=str_replace('{PRICE3}','</td></tr></table>',$html);
	$media->purchase=str_replace('{PRICE}','<table class="buymedia"><tr><td>',$media->purchase);
	$media->purchase=str_replace('{PRICE2}','</td><td>',$media->purchase);
	$media->purchase=str_replace('{PRICE3}','</td></tr></table>',$media->purchase);

	// media pages

	$html=str_replace('{MEDIASCHEMA}',$media->schema,$html);
	$html=str_replace('{BUYTAG}',$media->pricetag,$html);
	$html=str_replace('{BUYMEDIA}',$media->purchase,$html);
	$html=str_replace('{MEDIAMETA}',$media->meta,$html);
	$html=str_replace('{MEDIA_TITLE}',$media->title,$html);
	$html=str_replace('{MEDIA_DESC}',$media->description.$media->content,$html);
	$html=str_replace('{MEDIA_PIC}',$media->mediapic,$html);
	$html=str_replace('{MEDIA_BUTTONS}',($media->buttons?('<div class="mediabuttons"><div id="player">'.$media->buttons.'</div></div>'):''),$html);
	if (strpos($html,'{MEDIA_CONTENT}')>0) {
		if ($media->type==MXMEDIABASEBUNDLE || $media->type==MXMEDIAREGULARBUNDLE) {
			$mediatable=mx_xmlbundle($media->id,'media','',false);
			$mediatable=str_replace('pubmed','pubmed bundled bun_'.$media->id,$mediatable);
			$mediatable=preg_replace('%input_.%','bundled',$mediatable);
			$submedia='<table class="mediadetails">';
			$submedia.='<tr><td><h5>'._('Medias').'</h5></td></tr>';
			$submedia.=$mediatable.'</table>';
		}
		else {
			$submedia='<table class="mediadetails">';
			$submedia.='<tr><td><h5>'.(count($media->bundles)>1?_('Bundles'):_('Bundle')).'</h5></td></tr>';
			foreach($media->bundles as $bundle) {
				$submedia.='<tr class="pubmed bundled"><td class="msgcell bundled media_mediadata">'
				.'<div class="bundledetails" itemprop="inAlbum" itemscope itemtype="http://schema.org/MusicAlbum">'
				.'<div class="bundleminipic">'/*
				.' onmouseover="showbutton(\'m_'.$bundle->id.'\');"'
				.' onmouseout="hidebutton(\'m_'.$bundle->id.'\');">'
				.'<div class="gobunbutton" id="m_'.$bundle->id.'"'*/
				.'<a href="'.mx_actionurl('media','medprof',$bundle->id).'" title="'
				.$bundle->title.' - '._('See Media Page')
				.'" class="pictooltip" itemprop="url">'
				//._('See Bundle').'</div>'
				.$bundle->mediapic
				.'</a>'
				.'</div>'
				//.($bundle->buttons?('<div class="mediabuttons"><div id="player">'.$bundle->buttons.'</div></div>'):'')
				.'<div class="bundletitle" itemprop="name">'.$bundle->title.'</div>'
				.'<div class="bundledesc" itemprop="description">'.$bundle->description.'</div>'
				.'</div></td>'
				.'</tr>';
			}
			$submedia.='</table>';
		}
		$html=str_replace('{MEDIA_CONTENT}',$submedia,$html);
	}
	$html=str_replace('{ARTISTLINK}',mx_actionurl('artist','artprof',$user->id),$html);
	if (strpos($html,'{ARTISTBUTTON}')>0) {
		$artistbutton='<div class="artistpic" itemprop="byArtist" itemscope itemtype="http://www.schema.org/MusicGroup">'/*
			.' onmouseover="showbutton('.$user->id.');"'
			.' onmouseout="hidebutton('.$user->id.');">'
			.'<div class="goartbutton" id="'.$user->id.'"'*/
			.'<a href="'.mx_actionurl('artists','artprof',$user->id).'" title="'
			.mx_getartistname($user).' - '._('See Page')
			.'" class="pictooltip" itemprop="url">'
			//._('See Profile').'</div>'
			.'<img tag="'.$user->id.'" class="artistpic" src="'.mx_artpic($user->id,'large',$user->gender).'" itemprop="image">'
			.'</a>'
			.'<br/><span itemprop="name">'.mx_getartistname($user).'</span>'
			.'</div>';
		$html=str_replace('{ARTISTBUTTON}',$artistbutton,$html);
	}
	if (strpos($html,'{USERSHARE}')>0) {
		$url=($user->acctype==MXACCOUNTFAN?mx_actionurl('fans','fanprof',$user->id):mx_actionurl('artists','artprof',$user->id));
		if (!is_logged()) $sharebutton=mx_sharebuttons('u_'.$user->id,$url,mx_artpic($user->id,'large',$user->gender),$user->shortbio);
		else $sharebutton='';
		$html=str_replace('{USERSHARE}',$sharebutton,$html);
	}
	if (strpos($html,'{MEDIASHARE}')>0) {
		$url=mx_option('basicsiteurl').'/m/'.$media->id;
		if (!is_logged()) $sharebutton=mx_sharebuttons('m'.$media->id,$url,$media->pic,$media->description);
		else $sharebutton='';
		$html=str_replace('{MEDIASHARE}',$sharebutton,$html);
	}

	// -end user stuff
	if ($html) {
		echo $html;
	} else {
		__('(Content not yet ready)');
	}
	if ($simul=='public') {
		?>
		<script>
		simul=$('<div class="simulation"><?php __('Public View'); ?></div>');
		simul.appendTo($('.artisttmpl1'));
		</script>
		<?php
		$mxuser=new MXUser();
	}
}


function mx_sociallikes() {
	?>
<p style="text-align:center;">
<div class="facebookbox">
<div class="fb-like-box" data-href="http://www.facebook.com/musxpand" data-width="520" data-height="260" data-colorscheme="light" data-show-faces="true" data-border-color="white" data-stream="false" data-header="false"></div>
	<!--
<a href="https://plus.google.com/113506343132201725433?prsrc=3" style="text-decoration:none;"><img src="https://ssl.gstatic.com/images/icons/gplus-16.png" alt="" style="border:0;width:16px;height:16px;"/></a>
<g:plusone size="medium" href="http://www.example.com"></g:plusone><br/>
<fb:like class="fb-like" href="http://www.example.com" data-send="false" data-layout="button_count" data-show-faces="false" data-width="60" data-font="trebuchet ms"></fb:like>
<br/><a href="http://pinterest.com/pin/create/button/?url=www.example.com&media=<?php echo urlencode(mx_option('guitarlogo')); ?>&description=<?php echo htmlspecialchars('MusXpand is a brand new community bringing artists and fans together.'); ?>" class="pin-it-button" count-layout="horizontal"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>
-->
</div>
</p>
<!-- <p style="text-align:center">
<div class="fb-facepile" data-href="http://www.example.com" data-max-rows="2" data-width="170"></div>
</p> -->
	<?php
}


function mx_checkbrowser() {
	global $browser,$mxuser;
	$mybrowser=$browser->getBrowser();
	$myversion=$browser->getVersion();
	if (($mybrowser==Browser::BROWSER_SAFARI && $myversion>=5)
	|| $mybrowser==Browser::BROWSER_IPHONE
	|| $mybrowser==Browser::BROWSER_IPOD
	|| $mybrowser==Browser::BROWSER_IPAD
	|| ($mybrowser==Browser::BROWSER_IE && $myversion>=9)
	|| ($mybrowser==Browser::BROWSER_OPERA && $myversion>=11)
	|| ($mybrowser==Browser::BROWSER_CHROME && $myversion>=15)
	|| ($mybrowser==Browser::BROWSER_FIREFOX && $myversion>=7)
	|| ($mybrowser==Browser::BROWSER_ANDROID)
	|| $browser->isRobot()
		) {
		//echo '<p style="text-align:center;">'.sprintf(_('Cool, your browser (%s %s) is 100%% compatible with MusXpand!'),$mybrowser,$myversion).'</p>';
	} else {
		echo '<div class="mx-message">'.sprintf(_('Your browser version (%s) is outdated.'),$mybrowser.' '.$myversion).'<br/>'
		.mx_windowedpage('browsers',_('Please upgrade.'))
		.($browser->isAol()?('<br/><hr>'._('Also note that AOL browser has been reported as somehow incompatible with the site because AOL is using obsolete '
		.'technology, so we highly recommend to AOL users to switch to Firefox or Chrome')):'')
		.'</div>';
		if (!$mxuser || !$mxuser->id) echo '<script>showwindow(\'wp_browsers\');</script>';
	}
}

function mx_mnerrpage() {
	mx_pagetitle('errpage',mx_pagename('errpage'));
	__('<p>You tried to access a page that does not exist on MusXpand,' .
			' that was moved away or that you are simply not allowed access to.</p>');
	echo sprintf(_('<p>If necessary, please contact our support at %s.</p>'),MXSUPPORTEMAIL);
	__('Thank you.');
}

function mx_monthname($month) {
	global $months;
	return $months[$month];
}

function mx_customtext($str) {
	$str=str_replace('{NEW}',mx_icon('new',_('NEW'),'24px'),$str);
	return $str;
}

function mx_html2text($str) {
	$str=str_replace('<br/>',"\r\n",$str);
	$str=str_replace('<hr>',str_repeat('-',60),$str);
	$str=preg_replace('%<a href="([^"]+)">[^<]+</a>%','$1',$str);
	$str=preg_replace('%<style>[^<]*</style>%','',$str);
	$str=preg_replace('%<[^<]+>%','',$str);
	return $str;
}

function mx_peoplepage($id) {
	global $mxuser;
	$user=$mxuser->getuserinfo($id);
	$str='<div class="people">';
	if ($user->acctype==MXACCOUNTFAN || $user->acctype==MXACCOUNTUNDEFINED) {
		$name=mx_getname($user);
		$str.='<img class="listpic" src="'.mx_fanpic($user->id,'square',$user->gender)
				.'"/>'
				.' <a href="'.mx_actionurl('fans','fanprof',$user->id).'" alt="'.$name.'">'
				.$name
				.'</a>';
		// return '<a href="'.mx_actionurl('fans','fanprof',$id).'" alt="'.$name.'">'.$name.'</a>';
	} else if ($user->acctype==MXACCOUNTARTIST) {
		$name=mx_getartistname($user);
		$str.='<img class="listpic" src="'.mx_artpic($user->id,'square',$user->gender)
				.'"/>'
				.' <a href="'.mx_actionurl('artists','artprof',$user->id).'" alt="'.$name.'">'
				.$name
				.'</a>';
		//return '<a href="'.mx_actionurl('artists','artprof',$id).'" alt="'.$name.'">'.$name.'</a>';
	} else {
		$str.=_('Anonymous');
	}
	$str.='</div>';
	return $str;
}

function mx_getcountryname($countrycode) {
	global $mxdb;
	return $mxdb->getcountryname($countrycode);
}

function mx_getartistidfrombundle($bundleid) {
	global $mxdb;
	return $mxdb->getartistidfrombundle($bundleid);
}

function mx_infosecure() {
	echo '<div class="gandilocker"><a href="https://www.gandi.net/ssl/secured/example.com/1586077/320684ced7/"'
	.' target=_blank><img src="'.mx_option('siteurl').'/images/general/GandiSSL_A_standard_en.png"></a></div>';
}

function mx_getaccountfrompage($pageid) {
	global $mxdb;
	return $mxdb->getaccountfrompage($pageid);
}

function mx_getrefid($typeid,$refid) {
	global $mxdb;
	//error_log('get refid type '.$typeid.' for '.$refid);
	return $mxdb->getrefid($typeid,$refid);
}

function mx_qrcode($user,$artist=true) {
	global $s3;
	$keyname='users/'.$user->hashdir.'/pics/qr_'.($artist?'a':'f').'.png';
	$str='<img src="'.mx_secureurl(gets3url($keyname)).'" id="qrcode"'
	.' onmouseover="qrzoom(true);" alt="QR-Code" />';
	$keyname='users/'.$user->hashdir.'/pics/vCard_'.($artist?'a':'f').'.vcf';
	$str.='<br/><a target="_blank" href="'.mx_secureurl(gets3url($keyname)).'" alt="vCard">'
	.mx_icon('downmedia','Download','16px').' '._('vCard').'</a>';
	return $str;
}

function mx_getuserpublicinfo($userid) {
	global $mxuser;
	return $mxuser->getuserinfo($userid,true); // public info only!
}


function mx_secureurl($url) {
	if ($_SERVER['HTTPS']) return str_replace('http:','https:',$url);
	else return $url;
}

function mx_sharetools($userid,$artist=true) {
	global $s3,$mxdb;
	$user=mx_getuserpublicinfo($userid);
	mx_checkvalues($user);
	//die(print_r($user,true));
	if ($artist) {
		$name=$user->artistname;
		$page='artists';
		$opt='artprof';
	} else {
		$name=mx_getname($user);
		$page='fans';
		$opt='fanprof';
	}
	if (!$name) return _('Available Soon');
	//die(print_r($user,true));
	//if (!is_admin()) return _('Available Soon');

	// big badges
	$fmt=array(
		'large' => '150px wide',
		'small' => '100px wide',
		'square' => '80px x 96px'
	);
	// smaller on-musxpand badges
	$badgesize=array(
		_('Small') => '-small',
		_('Large') => '',
	);
	$badgecolor=array(
		_('White') => '',
		_('Black') => '-black',
	);
	if (!$user->badges) {
		// QR codes generation
		/*
		$xcard=new XMLWriter();
		$xcard->openMemory();
		$xcard->startDocument('1.0','UTF-8');
		$xcard->startElementNs(null,'vcards','urn:ietf:params:xml:ns:vcard-4.0');
		$xcard->startElement('vcard');

		$xcard->startElement('n'); // name
		$xcard->startElement('surname'); // family name
		$xcard->text($user->lastname);
		$xcard->endElement(); // surname
		$xcard->startElement('given'); // first name
		$xcard->text($user->firstname);
		$xcard->endElement(); // given
		$xcard->endElement(); // n

		$xcard->startElement('fn'); // fullname
		$xcard->startElement('text'); // text
		$xcard->text($name);
		$xcard->endElement(); // text
		$xcard->endElement(); // fn

		$xcard->startElement('org'); // org
		$xcard->startElement('text'); // text
		$xcard->text('MusXpand');
		$xcard->endElement(); // text
		$xcard->endElement(); // org

		$xcard->startElement('title'); // shortbio
		$xcard->startElement('text'); // text
		$xcard->text($user->about);
		$xcard->endElement(); // text
		$xcard->endElement(); // shortbio

		$xcard->startElement('photo'); // photo
		$xcard->startElement('uri'); // URI
		$xcard->text(mx_fanpic($user->id,'small',$user->gender,$isartist));
		$xcard->endElement(); // URI
		$xcard->endElement(); // photo

		$xcard->startElement('adr'); // address
		$xcard->startElement('parameters'); // parameters
		$xcard->startElement('type'); // type
		$xcard->text($isartist?'work':'home');
		$xcard->endElement(); // type
		$xcard->startElement('label'); // label
		$location='';
		if ($user->city) $location.=$user->city;
		if ($user->state) $location.=($location?', ':'').$user->state;
		if ($user->country) $location.=($location?'\n':'').mx_getcountryname($user->country);
		$xcard->text($location);
		$xcard->endElement(); // label
		$xcard->startElement('locality'); // locality
		$xcard->text($user->city);
		$xcard->endElement(); // locality
		$xcard->startElement('region'); // region
		$xcard->text($user->state);
		$xcard->endElement(); // region
		$xcard->startElement('country'); // country
		$xcard->text(mx_getcountryname($user->country));
		$xcard->endElement(); // country
		$xcard->endElement(); // parameters
		$xcard->endElement(); // address

		$xcard->startElement('email'); // country
		$xcard->startElement('text'); // text
		$xcard->text($user->email);
		$xcard->endElement(); // text
		$xcard->endElement(); // email

		$xcard->startElement('rev'); // revision
		$xcard->startElement('timestamp'); // date
		$xcard->text(gmdate('Ymd\THis\Z'));
		$xcard->endElement(); // date
		$xcard->endElement(); // rev

		$xcard->endElement(); // vcard
		$xcard->endElement(); // vcards

		$xvcard=$xcard->flush();
		*/
		$saveart=$artist;
		foreach (array(true,false) as $artist) {
			$vcard='BEGIN:VCARD'.CRLF
			.'VERSION:4.0'.CRLF;
			//$vcard.='SOURCE:'.mx_option('siteurl').'/vcf.php?a='.$user->id.CRLF;
			$vcard.='KIND:individual'.CRLF;
			//$vcard.='XML:'.$xvcard.CRLF;
			if ($artist) {
				$vcard.='FN:'.$name.CRLF;
				$vcard.='N:'.$user->lastname.';'.$user->firstname.CRLF;
			}
			else {
				$vcard.='FN:'.$user->fullname.CRLF;
				$vcard.='N:'.$user->lastname.';'.$user->firstname.CRLF;
			}
			$vcard.='PHOTO:'.mx_fanpic($user->id,'small',$user->gender,$artist).CRLF;
			if ($user->gender!=MXNOSEX) $vcard.='GENDER:'.($user->gender==MXSEXMALE?'M':'F').CRLF;
			if ($user->birthdate) $vcard.='BDAY:'.str_replace('-','',$user->birthdate).CRLF;
			$vcard.='ADR;TYPE='.($artist?'work':'home').':;;;'
				.$user->city.';'.$user->state.';;'.mx_getcountryname($user->country).CRLF;
			$vcard.='EMAIL:'.$user->email.CRLF;
			if ($user->timezone) $vcard.='TZ:'.$user->timezone.CRLF;
			if ($user->about) $vcard.='TITLE:"'.$user->shortbio.'"'.CRLF;
			$vcard.='LOGO:'.mx_option('logoURL').CRLF;
			//$vcard.='ORG:'.CRLF;
			$vcard.='UID:urn:uuid:'.$user->hashdir.CRLF;
			$vcard.='URL:'.mx_actionurl($artist?'artists':'fans',$artist?'artprof':'fanprof',$user->id).CRLF;
			$vcard.='REV:'.gmdate('Ymd\THis\Z').CRLF;
			$vcard.='END:VCARD'.CRLF;

			QRcode::png($vcard, mx_option('usersdir').'/tmp/'.$user->hashdir.'_QR'.($artist?'a':'f').'.png', 'L', 2, 4);
			$keyname='users/'.$user->hashdir.'/pics/qr_'.($artist?'a':'f').'.png';
			$res=$s3->create_object(MXS3BUCKET,$keyname,array(
				'fileUpload' => mx_option('usersdir').'/tmp/'.$user->hashdir.'_QR'.($artist?'a':'f').'.png',
				'acl' => AmazonS3::ACL_PUBLIC
			));
			//@unlink(mx_option('usersdir').'/tmp/'.$user->hashdir.'_'.$value.'.jpg');
			file_put_contents(mx_option('usersdir').'/tmp/'.$user->hashdir.'_vCard'.($artist?'a':'f').'.vcf', $vcard);
			$keyname='users/'.$user->hashdir.'/pics/vCard_'.($artist?'a':'f').'.vcf';
			$res=$s3->create_object(MXS3BUCKET,$keyname,array(
				'fileUpload' => mx_option('usersdir').'/tmp/'.$user->hashdir.'_vCard'.($artist?'a':'f').'.vcf',
				'acl' => AmazonS3::ACL_PUBLIC
			));
			@unlink(mx_option('usersdir').'/tmp/'.$user->hashdir.'_vCard'.($artist?'a':'f').'.vcf');
		}
		$artist=$saveart;
	}

	if (!$user->badges) {
	// badges generation
		ini_set('allow_url_fopen',1);
		$keyname='users/'.$user->hashdir.'/pics/me_large.jpg';
		$picurl=mx_secureurl(gets3url($keyname,'2 minutes'));
		// load pic
		$pic=imagecreatefromjpeg($picurl);
		if (!$pic) $pic=imagecreatefrompng(mx_fanpic($user->id,'large',$user->gender,false));
		$w=imagesx($pic);
		$h=imagesy($pic);
		$nhl=round($h*150/$w);
		$nhs=round($h*100/$w);
		$mwh=min($w,$h);
		$imlarge=imagecreatetruecolor(150,$nhl+16);
		$imsmall=imagecreatetruecolor(100,$nhs+16);
		$imsquare=imagecreatetruecolor(80,80+16);
		$fgl = imagecolorallocate($imlarge, 255,255,255);
		$fgs = imagecolorallocate($imsmall, 255,255,255);
		$fgq = imagecolorallocate($imsquare, 255,255,255);
		$bgl=imagecolorallocate($imlarge,0xff,0xff,0x99);
		$bgs=imagecolorallocate($imsmall,0xff,0xff,0x99);
		$bgq=imagecolorallocate($imsquare,0xff,0xff,0x99);
		//imagefill($imlarge,0,0,$fcl);
		//imagefill($imsmall,0,0,$fcs);
		//imagefill($imsquare,0,0,$fcq);
		//imagefilledrectangle($imlarge,1,$nhl,148,$nhl+14,$bgl);
		//imagefilledrectangle($imsmall,1,$nhs,98,$nhs+14,$bgs);
		//imagefilledrectangle($imsquare,1,80,78,80+14,$bgq);
		imageline($imlarge,0,$nhl-1,150-1,$nhl-1,$fgl);
		imageline($imsmall,0,$nhs-1,100-1,$nhs-1,$fgs);
		imageline($imsquare,0,80-1,80-1,80-1,$fgq);
		imageantialias($imlarge,true);
		imageantialias($imsmall,true);
		imageantialias($imsquare,true);
		imagecopyresampled($imlarge,$pic,1,1,0,0,150-2,$nhl-2,$w,$h);
		imagecopyresampled($imsmall,$pic,1,1,0,0,100-2,$nhs-2,$w,$h);
		imagecopyresampled($imsquare,$pic,1,1,
			($mwh==$h?($w-$mwh)/2:0),($mwh==$w?($h-$mwh)/2:0),80-2,80-2,$mwh,$mwh);
		// add artistname
		$font=mx_option('rootdir').'/fonts/HoboStd.otf';
		$textl=$texts=$textq=strtoupper($name);

		while (($factl=16/max(array(16,strlen($textl))))<0.6) {
			$textl=substr($textl,0,strrpos($textl, ' ')).'...';
		}
		while (($facts=12/max(array(12,strlen($texts))))<0.6) {
			$texts=substr($texts,0,strrpos($texts, ' ')).'...';
		}

		while (($factq=9/max(array(9,strlen($textq))))<0.6) {
			$textq=substr($textq,0,strrpos($textq, ' ')).'...';
		}
		$szl=9.4*$factl;
		$szs=9.4*$facts;
		$szq=9.4*$factq;
		$txtl=imagettfbbox($szl,0,$font,$textl);
		$txts=imagettfbbox($szs,0,$font,$texts);
		$txtq=imagettfbbox($szq,0,$font,$textq);
		imagettftext($imlarge,$szl,0,(150-$txtl[2]-$txtl[0])/2-1,$nhl+(16-$txtl[1]-$txtl[7])/2, $fgl, $font, $textl);
		imagettftext($imsmall,$szs,0,(100-$txts[2]-$txts[0])/2-1,$nhs+(16-$txts[1]-$txts[7])/2, $fgs, $font, $texts);
		imagettftext($imsquare,$szq,0,(80-$txtq[2]-$txtq[0])/2-1,80+(16-$txtq[1]-$txtq[7])/2, $fgq, $font, $textq);
		/*
		$logo=imagecreatefrompng(mx_option('rootdir').'/images/general/musxpand-logo.png');
		$imlogo=imagerotate($logo,90,0);
		$lw=imagesx($imlogo);
		$lh=imagesy($imlogo);
		imagecopyresampled($imlarge,$imlogo,129,0,0,0,$lw/2,$lh/2,$lw,$lh);
		*/
		imagejpeg($imlarge,mx_option('usersdir').'/tmp/'.$user->hashdir.'_large.jpg');
		imagejpeg($imsquare,mx_option('usersdir').'/tmp/'.$user->hashdir.'_square.jpg');
		imagejpeg($imsmall,mx_option('usersdir').'/tmp/'.$user->hashdir.'_small.jpg');

		foreach ($fmt as $value => $desc) {
			$keyname='users/'.$user->hashdir.'/pics/badge_'.$value.'.jpg';
			$res=$s3->create_object(MXS3BUCKET,$keyname,array(
				'fileUpload' => mx_option('usersdir').'/tmp/'.$user->hashdir.'_'.$value.'.jpg',
				'acl' => AmazonS3::ACL_PUBLIC
			));
			@unlink(mx_option('usersdir').'/tmp/'.$user->hashdir.'_'.$value.'.jpg');
		}

		// mini-badges
		$keyname='users/'.$user->hashdir.'/pics/me_square.jpg';
		$picurl=mx_secureurl(gets3url($keyname,'2 minutes'));
		// load pic
		$pic=imagecreatefromjpeg($picurl);
		if (!$pic) $pic=imagecreatefrompng(mx_fanpic($user->id,'large',$user->gender,false));
		$pw=imagesx($pic);
		$ph=imagesy($pic);
		$badgepref='badge-on-musxpand';
		foreach ($badgesize as $sname => $ssuf) {
			switch($ssuf) {
				case '':
					$offx=9;
					$offy=1;
					break;
				case '-small':
				default:
					$offx=-2;
					$offy=1;
					break;
			}
			foreach ($badgecolor as $cname => $csuf) {
				$badgemask=imagecreatefrompng(mx_option('rootdir').'/images/badges/'.$badgepref.$ssuf.$csuf.'.png');
				$bw=imagesx($badgemask);
				$bh=imagesy($badgemask);
				$badge=imagecreatetruecolor($bw,$bh);
				//imagepalettecopy($badge, $badgemask);
				$transp=imagecolorallocatealpha($badge,0,0,0,127);
				imagefill($badge,0,0,$transp);
				imageantialias($badge,true);
				//imagealphablending($badge,false);
				//imagealphablending($badgemask,false);
				imagecopyresampled($badge,$pic,floor(($bh-72)/2)+$offx,floor(($bh-72)/2)+$offy,0,0,72,72,$pw,$ph);
				imagecopyresampled($badge,$badgemask,0,0,0,0,$bw,$bh,$bw,$bh);
				imagesavealpha($badge,true);
				$tmpfile=mx_option('usersdir').'/tmp/'.$user->hashdir.'_mini'.$ssuf.$csuf.'.png';
				imagepng($badge,$tmpfile);
				$keyname='users/'.$user->hashdir.'/pics/minibadge'.$ssuf.$csuf.'.png';
				$res=$s3->create_object(MXS3BUCKET,$keyname,array(
					'fileUpload' => $tmpfile,
					'acl' => AmazonS3::ACL_PUBLIC
				));
				@unlink($tmpfile);
			}
		}
		ini_set('allow_url_fopen',0);
		$user->badges=1;
		$mxdb->updateuser($user, 'badges');
	}
	$str='<div class="badges"><table><tr>';
	foreach ($fmt as $value => $desc) {
		$str.='<th>'.sprintf(_('Badge %s'),$desc).'</th>';
	}
	$str.='</tr><tr>';
	foreach ($fmt as $value => $desc) {
		$keyname='users/'.$user->hashdir.'/pics/badge_'.$value.'.jpg';
		$str.='<td><img src="'.mx_secureurl(gets3url($keyname)).'" /></td>';
	}
	$str.='</tr><tr>';
	foreach ($fmt as $value => $desc) {
		$str.='<th>'._('Code').'</th>';
	}
	$str.='</tr><tr>';
	foreach ($fmt as $value => $desc) {
		$keyname='users/'.$user->hashdir.'/pics/badge_'.$value.'.jpg';
		$str.='<td>';
		$str.='<textarea id="b_'.$value.'" onmouseover="this.select(); setcopybtn(\'bt_'.$value.'\',\'b_'.$value.'\');">';
		$str.='<a href="'.mx_actionurl($page,$opt,$user->id).'" alt="'.$name.'">'
		.'<img src="'.mx_secureurl(gets3url($keyname)).'" />'
		.'</a>';
		$str.='</textarea><br/>';
		$str.='<a style="display:none;" href="#" class="toclipbtn" id="bt_'.$value.'">'._('Copy').'</a>';
		$str.='</td>';
	}
	$str.='</tr></table>';
	//$str.='<center><a href="'.mx_actionurl('artists','artprof',$user->id).'">'.$user->artistname.'</a></center>';
	$str.='</div>';

	// smaller on-musxpand badges
	$str.='<div class="badges"><table>';
	$i=0;
	foreach ($badgesize as $sname => $ssuf) {
		$i++;
		$j=0;
		foreach ($badgecolor as $cname => $csuf) {
			$j++;
			$value=$i.'_'.$j;
			$str.='<tr><th>'.sprintf(_('MX Badge, %s, %s'),$sname,$cname).'</th>'
			.'<th>'._('Code').'</th></tr>';
			$keyname='users/'.$user->hashdir.'/pics/minibadge'.$ssuf.$csuf.'.png';
			$str.='<tr><td><img src="'.mx_secureurl(gets3url($keyname)).'" /></td>';
			$str.='<td>';
			$str.='<textarea id="b_'.$value.'" onmouseover="this.select(); setcopybtn(\'bt_'.$value.'\',\'b_'.$value.'\');">';
			$str.='<a href="'.mx_actionurl($page,$opt,$user->id).'" alt="'.$name.'">'
			.'<img src="'.mx_secureurl(gets3url($keyname)).'" />'
			.'</a>';
			$str.='</textarea><br/>';
			$str.='<a style="display:none;" href="#" class="toclipbtn" id="bt_'.$value.'">'._('Copy').'</a>';
			$str.='</td>';
			$str.='</tr>';
		}
	}
	$str.='</table>';
	$str.='</div>';

	return $str;
}

function mx_setsession($user,$time) {
	global $mxsession;
	$thisaddr=$_SERVER['REMOTE_ADDR'];
	$mxsession=$user->id.','.$time.','.md5($user->pwdhash.$user->id.$thisaddr.$time.'12031968');
	$_SESSION['mxsession']=$mxsession;
}


function mx_mxforfans() {
	echo '<div class="dummies"><a href="'.mx_pageurl('bestdeal').'" alt="'._('MusXpand for Fans').'">'.mx_icon('musxpand-dummies').'</a></div>';
}

function mx_mxforartists() {
	echo '<div class="dummies"><a href="'.mx_pageurl('bestdealarts').'" alt="'._('MusXpand for Artists').'">'.mx_icon('musxpand-artists').'</a></div>';
}


function mx_prices() {
	global $windowedpages;
	$prices=mx_windowedpage('mxprices', _('MusXpand prices explained.'),true,$windowedpages);
	echo '<div class="dummies"><a href="javascript:showwindow(\'wp_mxprices\');">'.mx_icon('musxpand-prices-new').'</a></div>';
	echo $prices['div'];
}

function mx_onefav($fav) {
	global $mxuser;
	switch($fav->favtype) {
		case MXFAVUSER:
			$user=$mxuser->getuserinfo($fav->favid);
			$link=mx_actionurl('artists','artprof',$fav->favid);
			$pic=mx_artpic($fav->favid,'square',MXSEXMALE);
			$class='artistpic';
			$alt=mx_getartistname($user);
			break;
		case MXFAVMEDIA:
			$media=$mxuser->getmediainfo($fav->favid);
			$fanship=$mxuser->getfanship($media->owner_id,$media->id);
			mx_medialist($media,$fanship);
			$link=mx_actionurl('media','medprof',$fav->favid);
			$pic=$media->pic;
			$alt=$media->title;
			$class='bundlepic';
			break;
		default:
			$pic='xx';
			$link='';
			$alt='';
			$class='';
	}
	return '<div class="favorite" tag="'.$fav->id.'"'
	.' onmouseover="$(this).find(\'.favdel\').show();"'
	.' onmouseout="$(this).find(\'.favdel\').hide();">'
	.'<a href="'.$link.'" title="'.$alt.' - '._('See Page').'" class="pictooltip">'
	.'<img tag="'.$fav->favid.'" class="'.$class.'" src="'.$pic.'" alt="'.$alt.'"/>'
	//.' onclick="window.location=\''.$link.'\';" />'
	.'</a>'
	.'<div class="favdel"><div'
	.' onclick="return delfav('.$fav->id.');"'
	.' onmouseover="$(this).css(\'background\',\'black\');"'
	.' onmouseout="$(this).css(\'background\',\'white\');"'
	.'>X</div></div>'
	.'</div>';
}

function mx_favorites() {
	global $mxuser;
	echo '<div class="favbar">';
	$favs=$mxuser->getfavorites();
	//if (count($favs)) {
		echo '<h4>'._('Favorites').'</h4>';
	//}
	foreach ($favs as $fav) {
		echo mx_onefav($fav);
	}
	echo '</div>';
}


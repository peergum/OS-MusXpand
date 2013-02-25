<?php
/* ---
 * Project: musxpand
 * File:    mx_menus.php
 * Author:  phil
 * Date:    29/09/2010
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

/*
$mx_usermenu=array(
	'signin' => array(2,_('Sign In')),
	'register' => array(2,_('Register')),
	'profile' => array(1,_('Profile')),
	'signoff' => array(1,_('Logout'))
);
*/
/*
 * arrays:
 * - accestype:
 * 	0: everyone
 * 	1: not logged in
 * 	2: logged in
 * 	3: confirmed
 * 	10: artist
 * 	-1: everyone (not visible in menus)
 * 	-2: logged in (not visible)
 * 	-3: confirmed (not visible)
 * 	127: admin only
 * - menu item name
 * - submenu array OR page redir OR # (for javascript)
 * - option redir OR javascript
 */
$mx_ctxmenu=array(
	'main' => array(0,_('Home'),
		array(
		'musxpand' => array(0,_('What is MusXpand?')),
		//'fans' => array(3,_('Fans'),'fans',''),
		//'artists' => array(0,_('Artists'),'artists',''),
		'facebook' => array(0,_('Our FB Page')),
		'news' => array(-3,_('News')),
		//'paperli' => array(0,_('Weekly News')),
		'feedback' => array(2,_('Feedback!'),'#','UserVoice.showPopupWidget(); return false;'),
		'whoswhere' => array(-3,_('Who\'s Where?'),'whoswhere',''),
		)
	),
	'noaccess' => array(-2,_('Beta Phase'),
		array()
	),
	'bestdeal' => array(-1,_('MusXpand for the FAN'),
		array(
		//'musxpand' => array(0,_('More Details')),
			)
		),
	'bestdealarts' => array(-1,_('MusXpand for the ARTIST'),
		array(
		//'musxpand' => array(0,_('More Details')),
			)
		),
	'artists' => array(3,_('Artists'),
		array(
		'artsdir' => array(0,_('Directory'),'artists','artsdir'),
		//'artsnews' => array(3,_('News')),
		'featarts' => array(0,_('Featured')),
		//'artscharts' => array(3,_('Charts')),
		//'music' => array(3,_('Music')),
		//'media' => array(3,_('Media'),'media'),
		'artprof' => array(-1,_('Artist Profile')),
		)
	),
	'media' => array(-3,_('Media'),
		array(
		'pubmed' => array(-1,_('Public')),
		'membmed' => array(-3,_('Member Only')),
		'fanmed' => array(-3,_('Fan Only')),
		'mysubs' => array(-3,_('My Fanships'),'account','mysubs'),
		'mystuff' => array(-10,_('My Stuff'),'account','mystuff'),
		'medprof' => array(-1,_('Media Details')),
		)
	),
	'fans' => array(3,_('Fans'),
		array(
		'fandir' => array(3,_('Directory'),'fans','fandir'),
		//'featfans' => array(3,_('Featured')),
		//'fancharts' => array(3,_('Charts')),
		'fanprof' => array(-1,_('Fan Profile')),
		)
	),
	'bundle' => array(10,_('Bundle Maker'),'account','bundle'),
	//'mystuff' => array(10,_('My Stuff'),'account','mystuff'),
	'account' => array(3,_('My Account'),
		array(
		//'noaccess' => array(0,_('Beta Phase')),
		'signin' => array(1,_('Sign In')),
		'register' => array(MXINVITEONLY?(-1):1,_('Register')),
		'confirm' => array(2.4,_('Confirm Email')),
		'setup' => array(2.5,_('Quick Setup')),
		'fbsetup' => array(-11,_('Facebook Setup')),
		'bundle' => array(10,_('Bundle Maker')),
		'mystuff' => array(10,_('My Stuff')),
		'mystats' => array(3,_('My Stats')),
		'myartpage' => array(10,_('My Artist Page')),
		'wall' => array(3,_('My Walls')),
		'friends' => array(3,_('My Friends')),
		'messages' => array(3,_('My Messages')),
		//'cart' => array(-3,_('My Cart')),
		'myshows' => array(127,_('My Shows')),
		'mymusxp' => array(-3,_('My MusXpace'),'musxpace','mymusxp'),
		'acctype' => array(-2,_('Account Type')),
		'pwdreset' => array(-2,_('I Forgot My Password')),
		//'cart' => array(3,_('My Cart')),
		'mysubs' => array(3,_('My Fanships')),
		'myfanpage' => array(3,_('My Fan Page')),
		'mybands' => array(-10,_('My Bands')),
		'invites' => array(3,_('My Invites'),'account','invites'),
		'profile' => array(3,_('My Profile')),
		'playstats' => array(-3,_('My Play Stats')),
		'signoff' => array(-2,_('Logout')),
		'delacct' => array(-3,_('Delete Account')),
		)//,null,true // new menu!
	),
	'cart' => array(3,_('My Cart'),array()),
	'musxpace' => array(-3,_('MusXpace'),
		array(
		'mymusxp' => array(3,_('My MusXpace')),
		'directory' => array(3,_('Directory')),
		'3dvision' => array(3,_('3D Vision'))
		)
	),
	'about' => array(-1,_('About'),
		array(
		'cQ' => array(0,'Christopher Quinn [cQ]'),
		'phil' => array(0,'Philippe Hilger'),
		)
	),
	'careers' => array(-1,_('Careers'),array()),
	'fbgoapp' => array(-1,_('Use FB App'),array()),
	'fbapp' => array(10,_('Add to FB Page'),array()),
	'fbpage' => array(-1,_('MusXpand\'s FB Page'),array()),
	'fblikeus' => array(-1,_(''),array()),
	'whoswhere' => array(-20,_('Who\'s Where?'),array()),
	'search' => array(-3,_('Search Results'),null),
	'errpage' => array(-1,_('Error'),null),
	'admin' => array(126,_('Admin'),
		array(
		'upgrade' => array(127,_('DB Upgrade')),
		'dbgenres' => array(127,_('Update Genres')),
		'totest' => array(127,_('Switch to Tester')),
		'toadmin' => array(126,_('Switch back to Admin')),
		'fbaction' => array(127,_('Send FB Action')),
		'locate' => array(-1,_('Locate'))
		)
	),
	'tester' => array(126,_('Tester'),
		array(
		'toadmin' => array(126,_('Switch back to Admin')),
		)
	),
	'privacy' => array(-1,_('Privacy Policy'),null),
	'terms' => array(-1,_('Terms &amp; Conditions'),null),
	'dev' => array(-1,_('Developments'),
		array(
			'plugins' => array(-1,_('Skloogs Plug-Ins'),'main','')
		)
	),
	'signoff' => array(2,_('Logout'),array()),
	'help' => array(-1,_('Help'),
		array(
		'musxpand' => array(3,_('MusXpand'),'help','musxhelp'),
		//'sitehelp' => array(0,_('Site')),
		'accthelp' => array(0,_('Accounts')),
		'account' => array(-1,_('Accounts'),'help','accthelp'),
		//'subshelp' => array(3,_('Fanships')),
		//'fanshelp' => array(3,_('Fans')),
		//'artshelp' => array(3,_('Artists')),
		'mainhelp' => array(-1,_('Main Page')),
		'mediahelp' => array(-1,_('Media')),
		'musxhelp' => array(-1,_('MusXpand')),
		//'media' => array(3,_('Media'),'help','mediahelp'),
		'signin' => array(-1,_('Sign-in')),
		'register' => array(-1,_('Register')),
		'setup' => array(-2,_('Quick Setup')),
		'cart' => array(-3,_('Cart'),'help','carthelp'),
		//'carthelp' => array(3,_('Cart')),
		'faq' => array(3,_('FAQ')),
		'morehelp' => array(-3,_('Other')),
		'fans' => array(-3,_('Fans')),//,'help','fanshelp'),
		'artists' => array(-10,_('Artists'),'help','artshelp'),
		'help' => array(-2,_('Other'),'help','morehelp'),
		'abouthelp' => array(-1,_('MusXpand')),
		'termshelp' => array(-1,_('Terms &amp; Conditions')),
		'privhelp' => array(-1,_('Privacy Policy')),
		'investhelp' => array(-1,_('Investors')),
		//'careerhelp' => array(-1,_('Careers')),
		'about' => array(-1,_('MusXpand'),'help','mainhelp'),
		'terms' => array(-1,_('Terms &amp; Conditions'),'help','termshelp'),
		'privacy' => array(-1,_('Privacy Policy'),'help','privhelp'),
		'investors' => array(-1,_('Investors'),'help','investhelp'),
		//'careers' => array(-1,_('Careers')),
		'helptech' => array(-1,_('Technical Info')),
		'mxversion' => array(-3,_('MusXpand Version')),
		'bugs' => array(-3,_('Bugs Corrections')),
		'todo' => array(-3,_('To Do List')),
		'main' => array(-1,_('Main Page'),'help',''),
		'mystuff' => array(-10,_('My Stuff'),'help','mystuffhelp'),
		'mystuffhelp' => array(-10,_('My Stuff')),
		'helpme' => array(-1,_('Contextual Help')),
		'newartist' => array(-1,_('New Artists')),
		)
	),
);

$mx_musxmenu=array(
	'about' => array(0,_('About')),
	'careers' => array(0,_('Careers')),
	'terms' => array(0,_('Terms')),
	'privacy' => array(0,_('Privacy'),'help','privhelp'),
	'investors' => array(-1,_('Investors')),
	'help' => array(2,_('Help'))
);

$mx_iconmenu=array(
	//'main' => array(0,_('Main'),'main',''),
	'signin' => array(1,_('Sign In'),'account','signin'),
	'friends' => array(3,_('Friends'),'account','friends'),
	//'media' => array(3,_('Media'),'media'),
	'messages' => array(3,_('Messages'),'account','messages'),
	'cart' => array(3,_('Cart'),'cart'),
	'signoff' => array(2,_('Sign Off'),'account','signoff'),
);


$mx_helpmenu=array(
	'help' => array(2,_('Help'))
);

$mx_fbmenu=array(
	'main' => array(0,_('Home'),array(
			'main' => array(0,_('Home'),''),
			'signin' => array(1,_('Sign-in'),'account','signin'),
			'register' => array(1,_('Register'),'account','register'),
			'signoff' => array(2,_('Logout'),'account','signoff'),
			'fbgoapp' => array(0,_('Full App'),'#','javascript:top.location=\'http://apps.facebook.com/musxpand\';'),
			'fbsetup' => array(11,_('App Setup'),'account','fbsetup'),
			'fbapp' => array(0,_('Get this App on Your Page'),'fbapp'),
			'fbpage' => array(0,_('MusXpand\'s FB Page'),'#','javascript:top.location=\'http://www.facebook.com/musxpand\';'),
		)
	),
);

$mx_dropmenu=array(
	'playdrop' => array(0,_('Play - Play some medias')),
	'infodrop' => array(0,_('Info - Get information about the page or object')),
	'blogdrop' => array(3,_('News - To the artist\'s wall')),
	'writedrop' => array(3,_('Review - Rate and review')),
	'lovedrop' => array(3,_('Like - Show your love!')),
	'plusdrop' => array(3,_('Add - Add to your playlists')),
	'maildrop' => array(3,_('Message - Contact the artist or send messages')),
	'frienddrop' => array(3,_('Friend - Time to make friends...')),
	'sharedrop' => array(0,_('Share - Share to Facebook and others')),
	'cartdrop' => array(3,_('Buy - What would that be...?')),
	'mediadrop' => array(10,_('Media - Upload and manage your media')),
	'setupdrop' => array(3,_('Setup - Adjust your information here')),
);

$mx_minimenu=array(
	'exitdrop' => array(1,_('Exit - Time to leave...?')),
	'enterdrop' => array(0,_('Enter - Sign in/up')),
	'sep' => '',
	'artsdrop' => array(2,_('Artists - Artists Directory')),
	'fansdrop' => array(3,_('Fans - Fans Directory')),
);


function mx_pagename($page) {
	global $mx_ctxmenu, $mx_musxmenu;
	if (array_key_exists($page,$mx_ctxmenu)) return $mx_ctxmenu[$page][1];
	else if (array_key_exists($page,$mx_musxmenu)) return $mx_musxmenu[$page][1];
	else return 'Visiting...';
}

function mx_optionname($page,$option) {
	global $mx_ctxmenu;
	if (array_key_exists($page,$mx_ctxmenu)
		&& array_key_exists($option,$mx_ctxmenu[$page][2])) return $mx_ctxmenu[$page][2][$option][1];
	else return '...nowhere';
}

function mx_cleanurl($params) {
	return preg_replace('%[?&]?[po]=%','/',$params);
}

// check user can access this page
function mx_checkpage($page,$option) {
	global $mx_ctxmenu,$mxuser;
	$ret=MXPAGEOK;
	if (!array_key_exists($page,$mx_ctxmenu)
 	|| ($option && !array_key_exists($option,$mx_ctxmenu[$page][2]))) $ret=MXUNKNOWNPAGE;
 	else {
		$pagelevel=$mx_ctxmenu[$page][0];
		if ($option!='') {
			$optlevel=$mx_ctxmenu[$page][2][$option][0];
			if ($optlevel==1 && is_logged() && $option!='signin' && $option!='register') $ret=MXMAINPAGE;
			else if (($optlevel==1.5 || $optlevel==-1.5) && is_confirmed()) $ret=MXREDIRECT;
			else if (($optlevel>=2 || $optlevel<=-2) && !is_logged()) $ret=MXREDIRECT;
			else if (($optlevel==2.4 || $optlevel==-2.4) && (!is_logged() || is_setup())) $ret=MXRESTRICTEDPAGE;
			else if (($optlevel==2.5 || $optlevel==-2.5) && (!is_confirmed()/* || is_setup()*/)) $ret=MXRESTRICTEDPAGE;
			else if (($optlevel>=3 || $optlevel<=-3) && !is_setup()) $ret=MXRESTRICTEDPAGE;
			else if (($optlevel==10 || $optlevel==-10) && !is_artist()) $ret=MXRESTRICTEDPAGE;
			else if (($optlevel==20 || $optlevel==-20) && !is_privileged()) $ret=MXRESTRICTEDPAGE;
			else if ($optlevel==126 && !is_pseudoadmin() && !is_admin()) $ret=MXRESTRICTEDPAGE;
			else if ($optlevel==127 && !is_admin()) $ret=MXRESTRICTEDPAGE;
		} else {
			if ($pagelevel==1 && is_logged()) $ret=MXMAINPAGE;
			else if (($pagelevel==1.5 || $pagelevel==-1.5) && is_confirmed()) $ret=MXREDIRECT;
			else if (($pagelevel>=2 || $pagelevel<=-2) && !is_logged()) $ret=MXREDIRECT;
			else if (($pagelevel>=2.5 || $pagelevel<=-2.5) && !is_confirmed()) $ret=MXRESTRICTEDPAGE;
			else if (($pagelevel>=3 || $pagelevel<=-3) && !is_setup()) $ret=MXRESTRICTEDPAGE;
			else if (($pagelevel==10 || $pagelevel==-10) && !is_artist()) $ret=MXRESTRICTEDPAGE;
			else if (($pagelevel==20 || $pagelevel==-20) && !is_privileged()) $ret=MXRESTRICTEDPAGE;
			else if ($pagelevel==126 && !is_pseudoadmin() && !is_admin()) $ret=MXRESTRICTEDPAGE;
			else if ($pagelevel==127 && !is_admin()) $ret=MXRESTRICTEDPAGE;
			//if ($pagelevel>2 && !($mxuser->function & $pagelevel)) $ret=false;
		}
		//error_log('page='.$page.' opt='.$option.' ret='.$ret);
		return $ret;
 	}
}
function mx_showbigmenu($page) {
	global $mx_ctxmenu;
	mx_showmenu($mx_ctxmenu[$page][2],7,'account');
}

function mx_showmenu($menu,$menutype,$curpage='',$curopt='') {
	global $mx_ctxmenu, $mx_fbmenu, $mxuser;
	define('MXMENUVERTUSER',0);
	define('MXMENUHORIZTOP',1);
	define('MXMENUHORIZBOT',2);
	define('MXMENUHORIZICON',3);
	define('MXMENUHORIZHELP',4);
	define('MXMENUVERTMAIN',5);
	define('MXMENUHORIZFB',6);
	define('MXMENUVERTICON',7);
	if ($menutype!=0 && $menutype!=4 && $menutype!=6 && $menu!=$mx_ctxmenu && array_key_exists($curpage,$mx_ctxmenu)
		&& (($mx_ctxmenu[$curpage][0]==1 && is_logged())
		//|| ($mx_ctxmenu[$curpage][0]!=0 && !is_confirmed())
		|| ($mx_ctxmenu[$curpage][0]==1.5 && !is_confirmed()) // register
		|| ($mx_ctxmenu[$curpage][0]==2 && !is_logged())
		|| ($mx_ctxmenu[$curpage][0]==2.4 && (!is_logged() || is_confirmed()))
		|| ($mx_ctxmenu[$curpage][0]==2.5 && (!is_confirmed() || is_setup()))
		|| ($mx_ctxmenu[$curpage][0]>=3 && !is_setup())
		|| ($mx_ctxmenu[$curpage][0]==10 && !(is_setup() && is_artist()))
		|| ($mx_ctxmenu[$curpage][0]==11 && !(is_setup() && is_artist() && is_pageadmin()))
		|| ($mx_ctxmenu[$curpage][0]==20 && !is_privileged())
		|| $mx_ctxmenu[$curpage][0]<0
		//|| ($mx_ctxmenu[$curpage][0]==-2 && !is_logged())
		|| ($mx_ctxmenu[$curpage][0]==127 && !is_admin())
		|| ($mx_ctxmenu[$curpage][0]==126 && !is_pseudoadmin()))) {
		return;
	}
	if ($menutype==6
		&& (($mx_fbmenu[$curpage][0]==1 && is_logged())
		|| ($mx_fbmenu[$curpage][0]==1.5 && !is_confirmed()) // register
		|| ($mx_fbmenu[$curpage][0]==2 && !is_logged())
		|| ($mx_fbmenu[$curpage][0]==2.4 && (!is_logged() || is_confirmed()))
		|| ($mx_fbmenu[$curpage][0]==2.5 && (!is_confirmed() || is_setup()))
		|| ($mx_fbmenu[$curpage][0]>=3 && !is_setup())
		|| ($mx_fbmenu[$curpage][0]==10 && !(is_setup() && is_artist()))
		|| ($mx_fbmenu[$curpage][0]==11 && !(is_setup() && is_artist() && is_pageadmin()))
		|| ($mx_fbmenu[$curpage][0]<0)
		|| ($mx_fbmenu[$curpage][0]==127 && !is_admin())
		|| ($mx_fbmenu[$curpage][0]==126 && !is_pseudoadmin())
		)) {
		//error_log(print_r($mx_fbmenu[$curpage][0],true));
		return;
	}
	switch($menutype) {
		case MXMENUVERTUSER: // user vertical menu
			$style='usermenu menusep';
			break;
		case MXMENUHORIZTOP: // topbar horizontal menu
			$style='ctxmenu';
			break;
		case MXMENUHORIZBOT: // bottom screen menu
			$style='musxmenu';
			break;
		case MXMENUHORIZICON: // topbar icon menu
			$style='iconmenu';
			break;
		case MXMENUHORIZHELP: // help menu
			$style='helpmenu';
			break;
		case MXMENUVERTMAIN: // main vertical menu
			$style='mainmenu menusep';
			break;
		case MXMENUHORIZFB: // facebook menu
			$style='fbmenu';
			break;
		case MXMENUVERTICON: // big icons menu
			$style='bigmenu';
			break;
	}
	echo '<div class=\''.$style.'\'>';
	echo '<ul>';
	if ($mxuser->id && ($menutype==MXMENUVERTUSER || $menutype==MXMENUVERTMAIN)) echo '<li class="menutitle">'.($menutype==MXMENUVERTMAIN?'':strtoupper(mx_pagename($curpage))).'</li>';
	foreach ($menu as $menuopt => $menutable) {
		/*if (abs($menutable[0])>1 && !is_confirmed()
		&& $menuopt!='signoff'
		//&& $menuopt!='messages'
		&& $menuopt!='help'
		//&& $menuopt!='friends'
		&& $menuopt!='setup') continue;*/
		if (array_key_exists(2,$menutable) && !is_array($menutable[2]) && $menutable[2]!='#') {
			// redirection
			$page=$menutable[2];
			$option=($menutable[3]!=''?$menutable[3]:'');
		} else if (array_key_exists(2,$menutable) && $menutable[2]=='#') {
			// javascript (feedback)
			$page='#';
			$option='';
		} else if ($menutype==1 || $menutype==0 || $menutype==7) {
			// top or user menu
			$page=$curpage;
			$option=$menuopt;
		} else if ($menutype==4) {
			// help menu
			if ($mx_ctxmenu['help'][2][$curpage][3]=='') {
				$page='help';
				$option=$curpage;
			} else {
				$page='help';
				$option=$mx_ctxmenu['help'][2][$curpage][3];
			}
		} else {
			$page=$menuopt;
			$option='';
		}
		if (($menutype!=4 || $curpage!=$menuopt) &&
			(!$menutable[0] // accessible to everyone
				|| ($menutable[0]==1 && !is_logged()) // only NOT logged
				|| ($menutable[0]==1.5 && !is_logged()) // register only appears when not logged
				|| ($menutable[0]==2 && is_logged()) // only logged
				|| ($menutable[0]==2.4 && is_logged() && !is_confirmed())
				|| ($menutable[0]==2.5 && is_confirmed() && !is_setup()) // only logged and not confirmed
				|| ($menutable[0]==3 && is_setup()) // only confirmed
				|| ($menutable[0]==10 && is_setup() && is_artist()) // only artists
				|| ($menutable[0]==11 && is_setup() && is_artist() && is_pageadmin()) // artist and fb page admin
				|| ($menutable[0]==20 && is_privileged()) // only privileged
				|| ($menutable[0]==127 && is_admin()) // only admins
				|| ($menutable[0]==126 && is_pseudoadmin()) // only pseudoadmins (testers)
			)) {
			if ($menutype==3) { // icon menu
				$label=mx_icon($menuopt,$menutable[1],'20px');
				if ($menuopt=='messages' && $mxuser->newmsgs>0)
					$label.='<div class="newmsgs"><div>'.$mxuser->newmsgs.'</div></div>';
				if ($menuopt=='cart' && $mxuser->cart && $mxuser->cart->items>0)
					$label.='<div class="cartitems"><div>'.$mxuser->cart->items.'</div></div>';
			} else if ($menutype==7) { // big menu
				$label=mx_icon($menuopt.'-big',$menutable[1],'112px');
				if ($menuopt=='messages' && $mxuser->newmsgs>0)
					$label.='<div class="newmsgs"><div>'.$mxuser->newmsgs.'</div></div>';
				if ($menuopt=='cart' && $mxuser->cart && $mxuser->cart->items>0)
					$label.='<div class="cartitems"><div>'.$mxuser->cart->items.'</div></div>';
			} else if ($menutype==0 || $menutype==5 || $menutype==6) { // vertical menus
				if ($menutype!=6 && file_exists(mx_iconfile($menuopt.'btn'))) {
					$label='<span class="menubutton"><div>'.mx_icon($menuopt.'btn',$menutable[1],'','xx',$menuopt.'btnhover').'</div></span>';
				} else {
					if (file_exists(mx_iconfile($menuopt)))
						$label='<span class="menuicon">'.mx_icon($menuopt,$menutable[1],'16px').'</span>';
					else
						$label='<span class="menuicon">'.mx_icon('blank','','16px').'</span>';
					if ($menuopt=='messages' && $mxuser->newmsgs>0)
						$label.='<div class="newmsgs2"><div>'.$mxuser->newmsgs.'</div></div>';
					if ($menuopt=='cart' && $mxuser->cart && $mxuser->cart->items>0)
						$label.='<div class="cartitems2"><div>'.$mxuser->cart->items.'</div></div>';
					$label.='<span class="menulabel">'.$menutable[1].'</span>';
				}
				//$label.=$menutable[1];
			} else $label=$menutable[1];
			echo '<li';
			if ($menutype!=6 && file_exists(mx_iconfile($menuopt.'btn'))) echo ' class="button"';
			else if ($curpage==$menuopt||$curopt==$menuopt) echo ' class="selected"';
			else if (!function_exists('mx_mn'.$menuopt)) echo ' class="inactive"';
			echo '>';
			if ($page=="#") {
				echo '<a href="#" onclick="'.$menutable[3].'"' .
					' alt="'.$menutable[1].'">'.$label.'</a>';
			} else {
				if (function_exists('mx_mn'.$menuopt)) echo '<a href="'.mx_optionurl($page,$option)
					.'">'.$label.'</a>'
					.($menutable[4]?('<div class="newmenu">'.mx_icon('new',_('New'),'16px').'</div>'):'');
				else
					echo '<span class="inactive">'.$label.' '._('(soon)').'</span>';
			}
			echo '</li>';
		}
	}
	echo '</ul></div>';
}

function mx_usermenu($page='') {
	global $mx_ctxmenu,$mxuser;
	if ($page) {
		$curpag=$page;
		$curopt='';
	}
	else {
		$curpag=mx_secureword($_GET['p']);
		$curopt=mx_secureword($_GET['o']);
	}
	if (is_logged()) {
		switch($mxuser->status) {
			case MXACCTSETUP:
				break;
			case MXACCTDISABLED:
				echo '<div class="accountdisabled">'._('This account doesn\'t exist').'</div>';
				$curpag='main';
				break;
			case MXACCTEMAILCONFIRMED:
				if ($curpag!='account' && $curopt!='setup')
					echo '<div class="accountsetup">'._('Please setup<br/>your account to<br/>get full access').'<br/>&darr;</div>';
					$curpag='account';
				break;
			case MXACCTUNCONFIRMED:
				if ($curpag!='account' && $curopt!='confirm')
					echo '<div class="accountsetup">'._('Please confirm<br/>your email to<br/>get full access').'<br/>&darr;</div>';
					$curpag='account';
				break;
		}
	}
	//mx_showmenu($mx_ctxmenu['account'][2],0,'account',$curopt);
	if ($curpag) mx_showmenu($mx_ctxmenu[$curpag][2],0,$curpag,$curopt);
}


function mx_mainmenu($menutype=5) {
	global $mx_ctxmenu;
	$curpage=$_GET['p'];
	if (!$curpage) $curpage='main';
	if (array_key_exists($curpage,$mx_ctxmenu)) mx_showmenu($mx_ctxmenu,$menutype,$curpage);
}

function mx_fbmenu() {
	global $mx_fbmenu;
	$curpage=mx_secureword($_GET['p']);
	$curopt=mx_secureword($_GET['o']);
	if (!$curpage || !array_key_exists($curpage,$mx_fbmenu)) $curpage='main';
	mx_showmenu($mx_fbmenu[$curpage][2],6,$curpage,$curopt);
}


function mx_ctxmenu() {
	global $mx_ctxmenu;
	$curpage=$_GET['p'];
	$curopt=$_GET['o'];
	if (!$curpage) $curpage='main';
	if (array_key_exists($curpage,$mx_ctxmenu))
		mx_showmenu($mx_ctxmenu[$curpage][2],1,$curpage,$curopt);
}

function mx_musxmenu() {
	global $mx_musxmenu,$MXVersion,$MXRelease;
	echo '<div class="copyright">&copy; 2010-2012, MusXpand' .
			' <span class="version"><a href="'.
			mx_optionurl('help','mxversion').
			'">('.$MXVersion.'|'.$MXRelease.')</a></span></div>';
	mx_showmenu($mx_musxmenu,2);
}

function mx_iconmenu() {
	global $mx_iconmenu;
	mx_showmenu($mx_iconmenu,3);
}

function mx_helpmenu() {
	global $mx_helpmenu,$mx_ctxmenu;
	$curpage=$_GET['p'];
	$curopt=$_GET['o'];
	if ($curopt && array_key_exists($curopt,$mx_ctxmenu['help'][2]))
		return mx_showmenu($mx_helpmenu,4,$curopt);
	if (!$curpage) $curpage='main';
	//if (array_key_exists($curpage,$mx_ctxmenu))
	mx_showmenu($mx_helpmenu,4,$curpage);
}

function mx_pageurl($page) {
	return mx_actionurl($page);
	//return mx_option('siteurl').'/index.php?p='.$page;
}

function mx_pagelink($page) {
	global $mx_ctxmenu;
	if (array_key_exists($page,$mx_ctxmenu)) {
		return '<a href="'.mx_pageurl($page).'" alt="'.$mx_ctxmenu[$page][1].'">'.$mx_ctxmenu[$page][1].'</a>';
	} else {
		return '<a href="'.mx_pageurl($page).'" alt="'.$page.'">'.$page.'</a>';
	}
}

function mx_optionurl_normal($page,$option,$section='') {
	return mx_actionurl_normal($page,$option,'',$section);
}

function mx_optionurl($page,$option,$section='') {
	return mx_actionurl($page,$option,'',$section='');
}

function mx_optionurl_secure($page,$option,$section='') {
	return mx_actionurl_secure($page,$option,'',$section='');
}

function mx_loginredirecturl($page,$option,$action='',$section='',$other='') {
	$redir=$page.','.$option.','.$action.','.$section.','.urlencode($other);
	return mx_actionurl_normal('account', 'signin', 'redirect', '', $redir);
}

function mx_loginfbredirecturl($page,$option,$action='',$section='',$other='') {
	$redir=$page.','.$option.','.$action.','.$section.','.urlencode($other);
	return mx_actionurl('account', 'signin', 'fb', '', $redir, ($_REQUEST['signed_request']?'secure':'normal'));
}

function mx_optionlink($page,$option,$section='') {
	global $mx_ctxmenu;
	return mx_actionlink($page,$option,'',$section='');
}

function mx_actionurl_prod($page,$option,$action,$section='',$redir='',$nofacebook=false) {
	return mx_actionurl($page,$option,$action,$section='',$redir='','prod','',$nofacebook);
}

function mx_actionurl_normal($page,$option,$action,$section='',$redir='',$other='') {
	return mx_actionurl($page,$option,$action,$section,$redir,'normal',$other);
}

function mx_actionurl($page='main',$option='',$action='',$section='',$redir='',$site=null,$other='',$nofacebook=false) {
	switch($site) {
		case 'prod':
			$host='http://www.example.com';
			break;
		case 'normal':
			$host=mx_option('basicsiteurl');
			break;
		case 'secure':
			$host=mx_option('secure_siteurl');
			break;
		default:
			$host=mx_option('siteurl');
	}
	// --- stay in canvas or facebook page mode
	if (!$nofacebook) {
		if ($_GET['canvas']) $other.=($other?'&':'').'canvas=1';
		if ($_GET['fbp']) $other.=($other?'&':'').'fbp=1';
		if ($_REQUEST['signed_request']) $other.=($other?'&':'').'signed_request='.$_REQUEST['signed_request'];
	}
	$r=mx_secureredir(urldecode($_GET['r']));
	//error_log('actionurl: redir='.$redir.' r='.$r.' other='.$other);
	$i=mx_secureword($_GET['i']);
	if ($r && !$redir && ($option=='signin' || $option=='register')) $redir=$r;
	if ($i && strpos($other,'i=')===false && ($option=='signin' || $option=='register')) $other.=($other?'&':'').'i='.$i;
	// ---
	$str=$host.'/'.$page;
	if ($option) $str.='/'.$option;
	$qr='';
	if ($action) $qr.='a='.$action;
	if ($section) $qr.=($qr?'&':'').'k='.$section;
	if ($redir) $qr.=($qr?'&':'').'r='.urlencode($redir);
	//return mx_option('siteurl').'/index.php?p='.$page.'&o='.$option.'&a='.$action.'&k='.$section;
	if ($other) $qr.=($qr?'&':'').$other;
	if ($qr) $str.='?'.$qr;
	return $str;
}

function mx_actionurl_secure($page,$option,$action,$section='',$redir='',$other='') {
	return mx_actionurl($page,$option,$action,$section='',$redir='','secure',$other);
}

function mx_actionlink($page,$option,$action='',$section='',$redir='',$other='') {
	global $mx_ctxmenu;
	//if ($action) $act='&a='.$action;
	if (array_key_exists($page,$mx_ctxmenu)) {
		if (array_key_exists($option,$mx_ctxmenu[$page][2]))
			return '<a href="'.mx_actionurl($page,$option,$action,$section,$redir,null,$other).'" alt="'.$mx_ctxmenu[$page][2][$option][1].'">'.$mx_ctxmenu[$page][2][$option][1].'</a>';
		else
			return '<a href="'.mx_pageurl($page).'" alt="'.$mx_ctxmenu[$page][1].'">'.$mx_ctxmenu[$page][1].'</a>';
	} else {
		return '<a href="'.mx_pageurl($page).'" alt="'.$page.'">'.$page.'</a>';
	}
}


function mx_dropmenu() {
	global $mx_dropmenu,$mxuser;
	$page=mx_secureword($_GET['p']);
	$option=mx_secureword($_GET['o']);
	$action=mx_securestring($_REQUEST['a']);
	if ($option=='signin' || $option=='register') $tag='l';
	else if ($page=='artists' && $option=='artprof') $tag='a:'.$action;
	else if ($page=='fans' && $option=='fanprof') $tag='f:'.$action;
	else if ($page=='account') $tag='p:'.$mxuser->id;
	else if ($page=='media' && $option=='medprof') $tag='m:'.$action;
	else if ($page=='' || $page=='main') $tag='l';
	else $tag='';
	echo '<div class="dropmenu" tag="'.$tag.'"><ul>';
	foreach($mx_dropmenu as $action => $actiondetails) {
		if (!$actiondetails[0]
			|| ($actiondetails[0]==3 && is_logged())
			|| ($actiondetails[0]==10 && is_artist())) {
				echo '<li>';
				if ($action=='cartdrop' && $mxuser->cart->items) {
					echo '<div class="number"><div id="cartitems">'.$mxuser->cart->items.'</div></div>';
				} else if ($action=='maildrop' && $mxuser->newmsgs) {
					echo '<div class="number"><div id="newmsgs">'.$mxuser->newmsgs.'</div></div>';
				} else if ($action=='lovedrop' && $mxuser->subs['changed']>0) {
					echo '<div class="number"><div id="newmsgs">'.$mxuser->subs['changed'].'</div></div>';
				}
				echo mx_icon($action,$actiondetails[1],48,'act_'.$action,$action.'hover');
				echo '</li>';
		}
	}
	echo '</ul></div>';
}

function mx_minimenu() {
	global $mx_minimenu,$mxuser;
	$page=mx_secureword($_GET['p']);
	echo '<div class="minimenu"><ul>';
	foreach($mx_minimenu as $action => $actiondetails) {
		if (!is_array($actiondetails)) echo '<li>&nbsp;&nbsp;&nbsp;</li>';
		else {
			if ((!$actiondetails[0] && !is_logged() && $page!='account')
				|| ($actiondetails[0]==2)
				|| ($actiondetails[0]>0 && $actiondetails[0]<3 && is_logged())
				|| ($actiondetails[0]>=3 && $mxuser->status>=MXACCTSETUP))
				echo '<li>'.mx_icon($action,$actiondetails[1],48,'act_'.$action,$action.'hover').'</li>';
		}
	}
	echo '</ul></div>';
}



<?php
/* ---
 * Project: musxpand
 * File:    mx_help.php
 * Author:  phil
 * Date:    09/10/2010
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

function mx_mnhelp($page,$option,$action) {
	global $mxuser;
	echo '<a name="pagetop"></a>';
	if (!$option) {
		echo '<div class="mx-message">';
		mx_showhtmlpage('helpmain');
		echo '</div>';
	} else if ($option=='register') {
		switch ($mxuser->status) {
			case MXACCTUNCONFIRMED:
				echo '<div class="mx-message">';
				mx_showhtmlpage('unconfirmed');
				echo '</div>';
				break;
			case MXACCTEMAILCONFIRMED:
				echo '<div class="mx-message">';
				mx_showhtmlpage('unsetup');
				echo '</div>';
				break;
			default:
				echo '<div class="mx-message">';
				mx_showhtmlpage('helpmain');
				echo '</div>';
				break;
		}
	}
}

function mx_mnmusxpand($page,$option,$action) {
	mx_mnmusxhelp($page,$option,$action);
}

function mx_mnmusxhelp($page,$option,$action) {
	echo '<div class="mx-message">';
	mx_showhtmlpage('musxpand');
	echo '</div>';
}

function mx_mnmxversion($page,$option,$action) {
	global $MXVersion,$MXRelease,$MXRelDate,$MXlines,$MXCodinoma;

	$versform=array(
		'mxversion',0,'MusXpand Version',
		_('Some information about MusXpand\'s development...'),
		array(),
		array(
			'coding' => array(-1,_('Version Information'),_('About current version of MusXpand Software')),
			'version' => array(0,_('Version:'),'text'),
			'codename' => array(0,_('Codename:'),'quote'),
			'release' => array(0,_('Release:'),'text'),
			'reldate' => array(0,_('Release Date:'),'text'),
			'lines' => array(0,_('Code Lines:'),'text'),
			'hosting' => array(-1,_('Hosting'),_('What\'s behind?')),
			'location' => array(1,_('Server Location:'),'text'),
			'system' => array(1,_('System:'),'text'),
			//'processor' => array(1,_('Processor(s):'),'text'),
			'server' => array(1,_('Server:'),'text'),
			'php' => array(1,_('PHP Version:'),'text'),
			'team' => array(-1,_('Project Information'),_('The MusXpand Development Facts')),
			'startdev' => array(0,_('Started:'),'text'),
			'developer' => array(0,_('Developer:'),'text'),
			'testers' => array(0,_('Local/Remote<br/>Beta-Testers:'),'text'),
			'equipments' => array(0,_('Development<br/>platforms:'),'text'),
			'software' => array(0,_('Software:'),'text'),
			'tests' => array(0,_('Tests:'),'text'),
			//'more' => array(-1,_('Want to know more...?'),_('A few links you may love to click :-)')),
			//'bugs' => array(0,_('Bugs and Updates Reports'),'text'),
			//'todo' => array(0,_('The ever growing TO DO List'),'text'),
		'a' => array(1,'submit','hidden')
		)
	);

	$fp=popen('uname -sr','r');
	$system=fgets($fp);
	pclose($fp);
	$fp=popen('uname -p','r');
	$machine=fgets($fp);
	pclose($fp);

	preg_match('%(Apache/(?P<apache>[^ ]+))%i',$_SERVER['SERVER_SOFTWARE'],$sver);
	$location=mx_locate(gethostbyname("www.example.com"));
	if ($location) $region=mx_region($location);
	else $region='';
	$versvalues=array(
		'startdev' => '28-Sep-2010',
		'developer' => mx_peoplepage('peergum').', IT Engineer and somehow musician (FR)', // Phil
		'testers' => mx_peoplepage(45).', Music Fan & Wife :-) (BR)' // Anya
				.'<br/>'.mx_peoplepage(47).', Artist (US)' // Johnny Nowhere
				.'<br/>'.mx_peoplepage('limey59').', Music Fan (CA)' // David Slater
				.'<br/><a href="'.mx_optionurl('about','cQ').'">Christopher Quinn, Artist (US) - R.I.P.</a>'
				.'<br/>'.mx_peoplepage('anubisspire').', Artist (US)' // Bill MacKechnie
				.'<br/>'.mx_peoplepage('carosta').', Artist (DE)' // Carola Rost-Maskawy
				.'<br/>'.mx_peoplepage(58).', Artist (US)' // Bob Pope
				.'<br/>'.mx_peoplepage('francisvoignier').', Artist (US)' // Francis Voignier
				.'<br/>'.mx_peoplepage('iMickeyD').', Artist (US)' // Michael David Sherwood
				.'<br/>'.mx_peoplepage('skyfire').', Music Fan (AU)', // Anthony James Widdowson
		'equipments' => 'MacBook Pro (500GB/8GB)'
				.'<br/>Fedora Linux Server (Dell)'
				.'<br/>Windows 7 PC (Dell)',
		'software' => 'Mac OS X 10.7'
				.'<br/>Windows 7'
				.'<br/>Eclipse'
				.'<br/>Subversion (SVN)'
				.'<br/>XAMP & LAMP (Apache/MySQL/PHP on Mac OS X and Linux)'
				.'<br/>unix shell scripts (bash,awk)',
		'tests' => 'Mac: Safari + Firefox'
				.'<br/>Windows: Internet Explorer + Firefox',
		'version' => $MXVersion,
		'codename' =>  $MXCodinoma, //'<a href="'.mx_optionurl('about','cQ').'">'.$MXCodinoma.'</a>',
		'release' => $MXRelease,
		'reldate' => $MXRelDate,
		'lines' => $MXlines,
		'location' => ($location->city?$location->city:'').($region?(' ('.$region.'),'):($location->region?('/'.$location->region.','):'')).' '.$location->countryName,
		'server' => 'Apache '.$sver['apache'],
		'php' => phpversion(),
		'system' => $system,
		'processor' => $machine,
		//'bugs' => mx_optionlink('help','bugs'),
		//'todo' => mx_optionlink('help','todo'),
	);
	echo '<div class="mx-message">';
	mx_showform($versform,$versvalues,false);
	echo '</div>';
	//print_r($location);

	//phpinfo();
}

function mx_mnaccthelp($page,$option,$action) {
	echo '<div class="mx-message">';
	mx_showhtmlpage('accthelp');
	echo '</div>';
}

function mx_mnterms($page,$option,$action) {
	switch($action) {
		case 'sales':
			mx_showhtmlpage('salesterms');
			break;
		default:
			mx_showhtmlpage('terms');
			break;
	}
}

function mx_mntermshelp($page,$option,$action) {
	mx_mnterms($page,$option,$action);
}

function mx_mnprivhelp($page,$option,$action) {
	mx_showhtmlpage('privacy');
}

function mx_mnprivacy($page,$option,$action) {
	mx_mnprivhelp($page,$option,$action);
}

function mx_mnfaq($page,$option,$action) {
	global $mxuser;
	echo '<div class="mx-message">';
	mx_showhtmlpage('faq');
	if ($mxuser->acctype==MXACCOUNTFAN) mx_showhtmlpage('faqfans');
	if ($mxuser->acctype==MXACCOUNTARTIST) mx_showhtmlpage('faqarts');
	echo '</div>';
}

function mx_mnbestdeal($page,$option,$action) {
	if (!$option) mx_showhtmlpage('bestdeal');
}

function mx_mnbestdealarts($page,$option,$action) {
	if (!$option) mx_showhtmlpage('bestdealarts');
}

function mx_mnhelpme($page,$option,$action) {
	echo '<div class="mx-message">';
	switch ($action) {
		case 'media':
			mx_showhtmlpage('mystuffhelp');
			break;
		case 'invites':
			mx_showhtmlpage('inviteshelp');
			break;
		default:
			mx_showhtmlpage('nohelpyet');
			break;
	}
	echo '</div>';
}

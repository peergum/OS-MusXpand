<?php
/* ---
 * Project: musxpand
 * File:    mx_definitions.php
 * Author:  phil
 * Date:    Apr 9, 2011
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

global $MXDEFAULTMODULES,$MXUNSIGNEDMODULES;

define('CRLF',"\r\n");

// default background
//define ('MXDEFAULTBACKGROUND','logowall.jpg');
//define ('MXDEFAULTBACKGROUND','musxpand-halloween.jpg');

define('MXSUPPORTPHONE',_('Support: +1 (604) 267-3004 / <a href="skype:musxpand">Skype: MusXpand</a> (English, French and Portuguese)'));
define('MXKEYWORDS','indie,independent,art,music,fans,artists,subscription,subscriptions,media,social'
	.',network,networking,friends,share,news,bands,business,download,rock,pop,jazz,electronica,dance');

// Max file size
define('MXMAXFILESIZE','314572800'); // 300MB=300*1024*1024 (was 100)
// invites
define('MXMAXINVITES',50);

//=== general
define('MXTAXHST',0.12);
define('MXCARTINFORMATION',
	_('All fanships start from the date of purchase.<br/>' .
	'[FOY] fanships are not refundable and are automatically renewed,'
	.' until you cancel them.<br/>'
	.'[FOFA] fanships and upgrades can be cancelled, suspended and/or partially refunded,'
	.' <u>under particular conditions</u>.'));

define ('MXNONAPPLICABLE',_('-'));
define ('MXPPNOTEONRECURRINGPAYMENTS',_('1-Year subscriptions'
	.' will be renewed after 1 year, unless you choose not to.'
	.' With a FOFA upgrade, the corresponding renewal is immediately cancelled.').' ');
//define ('MXPPNOTEONUPGRADES',_('The automatic renewals of the fanship you upgraded'
//	.' will be canceled accordingly').' ');

//=== mx_db
define('MXALREADYLINKED',-1);
define('MXNOWLINKED',0);
define('MXNOTCREATED',-2);
define('MXWRONGPWD',-3);
define('MXNOPWDMATCH',-4);
define('MXBANDCREATED',1);

define('MXCARTPENDING',1);
define('MXCARTCANCELLEDFROMPAYPAL',2);
define('MXCARTCANCELLEDFROMCONFIRM',3);
define('MXCARTCHECKOUTADDRESS',4);
define('MXCARTCHECKOUTPAYPAL',5);
define('MXCARTCONFIRM',6);

define('MXCARTCONFIRMED',99);
//define('MXCARTORDERED',99);

define('MXBILLINGADDRESS',1);
define('MXSHIPPINGADDRESS',2);

//=== mx_account
define('MXNOTINFORMED','-');
define('MXNOTAVAIL','N/A');

// sex
define('MXNOSEX',-1);
define('MXSEXMALE',0);
define('MXSEXFEMALE',1);

// account statuses
define('MXACCTDISABLED',-1);
define('MXACCTUNCONFIRMED',0);
define('MXACCTEMAILCONFIRMED',1);
define('MXACCTSETUP',2);
define('MXACCTPRIVILEGED',5);
define('MXACCTBILLINGCONFIRMED',10);
define('MXACCTIDCONFIRMED',30);
define('MXACCTINVESTOR',100);
define('MXACCTPSEUDOADMIN',126);
define('MXACCTTRUSTFUL',127);


//=== mx_messages
// message statuses
define('MXMSGREAD',0x01);
define('MXMSGREPLIED',0x02);
define('MXMSGARCHIVED',0x04);
define('MXMSGDELETED',0x08);
define('MXMSGDRAFT',0x10);
define('MXREQCANCELLED',0x20);
define('MXREQACCEPTED',0x40);
define('MXREQRECUSED',0x80);
define('MXREQIGNORED',0x100);

// message flags
define('MXFRIENDREQUEST',0x01);
define('MXREQUEST',MXFRIENDREQUEST); // all possible request OR'ed


//=== mx_functions

// media
define('MXMEDIABG','7');
define('MXMEDIASONG',0);
define('MXMEDIAINSTR',1);
define('MXMEDIAVIDEO',2);
define('MXMEDIAPIC',3);
define('MXMEDIADOC',10);
define('MXMEDIABASEBUNDLE',20);
define('MXMEDIAREGULARBUNDLE',21);
define('MXMEDIAUNDEFINED',99);


// media status
define('MXMEDIANOSTATUS',0);
define('MXMEDIADRAFTINCOMP',1);
define('MXMEDIADRAFTCOMP',2);
define('MXMEDIADEMO',3);
define('MXMEDIAEXTRACT',4);
define('MXMEDIAFINAL',10);

// media availability
define('MXMEDIAUPLOADED',0);
define('MXMEDIAVALIDATED',1);
define('MXMEDIAREADY',2);
define('MXMEDIANEW',3);
define('MXMEDIAFANVISIBLE',10);
define('MXMEDIAFANSHARED',11);
define('MXMEDIAMEMBERVISIBLE',20);
define('MXMEDIAMEMBERSHARED',21);
define('MXMEDIAPUBLIC',30);
define('MXMEDIAPUBLICSHARED',31);
define('MXMEDIASUSPENDED',98);
define('MXMEDIAARCHIVED',99);
define('MXMEDIAVIRTUAL',100);

define('MXBANDROLEALL','100');
define('MXBANDROLEOTHER','99');
define('MXBANDROLENONE','-1');
define('MXLOW','0');

// notifications
define('MXEMAILNOTIF',0);


define('MXPAGEOK','0');
define('MXUNKNOWNPAGE','1');
define('MXMAINPAGE','2');
define('MXREDIRECT','3');
define('MXRESTRICTEDPAGE','4');
define('MXNOACCESS','-1');

// product types
define('MXARTSUB',1);
define('MXSITESUB',2);
define('MXMEDSUB',3);

// Artist subscription types
define('MXSUBFOY',1); // foy subscription
define('MXSUBFOFA',2); // fofa subscription
define('MXUPGFOFA',3); // upgrade fofa
define('MXSUBLIKE',4); // just a liker

// site subscriptions
define('MXSUBFREE',1); // ad-sponsored sub
define('MXSUBBASIC',2); // Basic sub
define('MXSUBPLUS',3); // Plus sub
define('MXSUBPREMIUM',4); // Premium sub

//media subscriptions
define('MXBUYBUNDLE',1); // Bundle purchase
define('MXBUYMEDIA',2); // Bundle purchase

define('MXSUBAUTORENEW',1);
define('MXSUBNORENEW',2);
define('MXSUBSTOPRENEW',3);

// account types
define('MXACCOUNTUNDEFINED','0');
define('MXACCOUNTFAN','1');
define('MXACCOUNTARTIST','2');
define('MXACCOUNTBAND','3');
define('MXACCOUNTMANAGER','4');
define('MXACCOUNTLABEL','5');
define('MXACCOUNTVENUE','6');

// subscriptions
define('MXPENDINGSUB',0);
define('MXNEWSUB',1);
define('MXCURRENTSUB',2);
define('MXRENEWEDSUB',3);
define('MXEXPIREDSUB',4);
define('MXENDEDSUB',5);
//define('MXNORENEWSUB',6);
define('MXSUBNOEXPIRY','9999-01-01');

// fanships
define('MXNONMEMBER',0);
define('MXMEMBER',1);
define('MXLIKER',2);
define('MXFAN',3);
define('MXME',4);
define('MXSUBSCRIBER',5);
define('MXBUYER',6);

//=== mx_page
define('MXDEFARTISTPAGE','basicartisttemplate');
define('MXDEFFANPAGE','basicfantemplate');
define('MXPRODSITE','http://www.example.com');

// Walls
define('MXSHAREALL',0);
define('MXSHAREFRIENDS',1);
define('MXSHAREFANS',2);
define('MXSHAREARTISTS',4);
define('MXSHARELIKERS',8);

// walls flags
define('MXWALLDELETED',0x01);
define('MXWALLFLAGGED',0x02);

// likes
define('MXLIKEIT',1);
define('MXDISLIKEIT',2);

// friends
define('MXPENDINGFRIEND',0);
define('MXFRIEND',1);
define('MXRECUSEDFRIEND',2);
define('MXIGNOREDFRIEND',3);

//PROs
define('MXNOPROYET',-1);

// bundlees
define('MXDEFAULTBUNDLENAME',_('Miscellaneous'));
define('MXDEFAULTBUNDLEDESC',_('Work In Progress'));
define('MXDEFAULTNEWBUNDLENAME',_('New Bundle\'s Name'));
define('MXDEFAULTNEWBUNDLEDESC',_('Please describe your new bundle'));

// DB errors
define('MXDBERROR',-1);
define('MXOK',0);
define('MXNOCHANGE',1);
define('MXNOLINK',2);

// username errors
define('MXUNOK',0);
define('MXUNEMPTYNOCHANGE','-1');
define('MXUNNOTLOGGED','-2');
define('MXUNRESTRICTED','-3');
define('MXUNONLYNUMBERS','-4');

// playstats

define('MXPLAYTYPEUNKNOWN',0);
define('MXPLAYTYPEFULL',1);
define('MXPLAYTYPEPREVIEW',2);

// modules
$MXDEFAULTMODULES=array(
		'mxbarleft' => array('logo'),
		'mxbarcenter' => array('dropmenu'),
		'mxbarright' => array('minimenu'),
		'mxtopleft' => array(),
		'mxtop' => array(),
		'mxtopright' => array(),
		'mxleft' => array('favbar','randminipics'), // 'mainmenu','usermenu'
		'mxhigh' => array('content'),
		'mxlow' => array(),
		'mxright' => array('search','mxpromo','yahoogrp'),
		'mxlowleft' => array(),
		'mxlowcenter' => array(),
		'mxlowright' => array(),
		'mxbotleft' => array(),
		'mxbot' => array('musxmenu'),
		'mxbotright' => array(),
		'mxbotbar' => array('player'),
		);

$MXUNSIGNEDMODULES=array(
	'mxbarleft' => array('logo'),
	'mxbarcenter' => array('dropmenu'),
	'mxbarright' => array('minimenu'),
	'mxleftunsigned' => array('randminipics','yahoogrp'),
	'mxhigh' => array('content'),
	'mxlow' => array(),
	'mxrightunsigned' => array('mxpromo','mxquickfacts'),
	'mxbot' => array('musxmenu'),
	'mxbotright' => array(),
	'mxbotbar' => array('player'),
);

$MXREMOVEDMODULES=array('mxpromoon');

define('MXFTNEWLOGIN',0x00000001);
define('MXFTDROPMENU',0x00000001);

define('MXDEFFEATURES',MXFTDROPMENU);

// favorites
define('MXFAVUSER',1);
define('MXFAVMEDIA',2);

<?php
/* ---
 * Project: musxpand
 * File:    mx_init.php
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

global $mxdb,$MXVersion,$MXDomain,$FBcookie,$mxsession,$MXlines,$mxlocale,$MXRelease,$MXRelDate;

$MXAppName='MusXpand';
$MXVersion='1.0';
$MXCodinoma='Spoti\'kill ;-)'; //'Christopher Quinn [cQ]';
$MXDomain='musxpand';

//date_default_timezone_set('America/Vancouver');
date_default_timezone_set('UTC');

$MXRootPath=$_SERVER['DOCUMENT_ROOT'];

$relfile=$MXRootPath."/release.txt";
if (file_exists($relfile)) {
	$fp=fopen($relfile,'r');
	$release=fgets($fp);
	$rstat=fstat($fp);
	fclose($fp);
	$MXRelease=sprintf("%d",$release);
	$MXRelDate=strftime('%D-%H:%M',$rstat['mtime']);
} else $release='';
$MXlines=trim(file_get_contents($MXRootPath."/lines.txt")).' lines';

session_set_cookie_params(86400,'/',null,false);

if (session_start()) {
	$mxsession=$_SESSION['mxsession'];
	$mxlocale=$_SESSION['mxlocale'];
}

include_once 'includes/mx_definitions.php';
include_once 'includes/mx_language.php';
include_once 'includes/mx_config.php';
include_once 'includes/mx_prices.php';
include_once 'includes/mx_dirinit.php';
include_once 'includes/mx_db.php';
include_once 'includes/mx_menus.php';
include_once 'includes/mx_browser.php';
include_once 'includes/mx_functions.php';
include_once 'includes/mx_facebook.php';
include_once 'includes/mx_account.php';
include_once 'includes/mx_friends.php';
include_once 'includes/mx_main.php';
include_once 'includes/mx_fans.php';
include_once 'includes/mx_musxpace.php';
include_once 'includes/mx_help.php';
include_once 'includes/mx_ads.php';
include_once 'includes/mx_media.php';
include_once 'includes/mx_search.php';
include_once 'includes/mx_admin.php';
include_once 'includes/mx_bands.php';
include_once 'includes/mx_getid3.php';
include_once 'includes/mx_about.php';
include_once 'includes/mx_messages.php';
include_once 'includes/mx_geoip.php';
include_once 'includes/mx_artists.php';
include_once 'includes/mx_walls.php';
include_once 'includes/mx_shows.php';
include_once 'includes/mx_careers.php';
include_once 'includes/mx_cart.php';
include_once 'includes/mx_paypal.php';
include_once 'includes/mx_check.php';
include_once 'includes/mx_emails.php';
include_once 'includes/mx_amazon.php';
include_once 'includes/mx_otherpages.php';
include_once 'ext_includes/waveform-generator.php';
include_once 'includes/mx_blogs.php';
include_once 'ext_includes/phpqrcode/qrlib.php'; // QR Codes generation
include_once 'includes/mx_modules.php';
//include_once 'includes/mx_chats.php';

function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    if ($errno!=8) error_log('ERROR #'.$errno.': ['.$errstr.'] in '.$errfile.' line #'.$errline);
    //die();
    //throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

error_reporting(E_ALL & ~E_NOTICE);
set_error_handler("exception_error_handler");

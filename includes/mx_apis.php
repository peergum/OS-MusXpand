<?php
/* ---
 * Project: musxpand
 * File:    mx_api.php
 * Author:  phil
 * Date:    Sep 18, 2012
 * ---
    This file is part of MusXpand.
    Copyright � 2010-2012 by Philippe Hilger
 */

$mxapiversion='1.0';

require_once 'includes/mx_init.php';

define('MXAPIOK',0);
define('MXAPIINVALID',-1);
define('MXAPIFUNCTIONINVALID',-2);
define('MXAPIUNSECURE',-3);
define('MXAPIAPPKEYFAIL',-4);
define('MXAPIAPPHASHFAIL',-5);
define('MXAPIAPPUSERFAIL',-6);

define('MXAPPPRIVKEY','lalala');

function mx_apicall($name,$func,$jsonpar) {
	global $mxapiversion;
	if (!$_SERVER['HTTPS']) return mx_apiout(MXAPIUNSECURE,'Unsecure Call');
	$par=json_decode($jsonpar);
	//error_log(print_r($par,true));
	if (function_exists('mx_api_'.$name)) {
		$afname='mx_api_'.$name;
		return $afname($func,$par);
	}
	return mx_apiout(MXAPIOK,array(
		'Api' => $name,
		'Function' => $func,
		'Parameters' => json_decode($par)
		));
	return mx_apiout(MXAPIINVALID,'Crap.');
}

function mx_apiout($code,$msg) {
	global $mxapiversion;
	return array(
		'version' => $mxapiversion,
		'code' => $code,
		'msg' => $msg
		);
}

function mx_apinotvalid($name,$func) {
	return mx_apiout(MXAPIFUNCTIONINVALID,'More Crap.');
}

function mx_api_version($func,$par) {
	global $mxapiversion;
	return mx_apiout(0,'MusXpand API v.'.$mxapiversion);
}

function mx_api_auth($func,$par) {
	global $mxdb;
	switch($func) {
		case 'user_auth':
			$user=$par->user;
			$mxappkey=$par->mxappkey;
			$pwhash=$par->pwhash;
			$apphash=$par->apphash;
			if ($mxappkey!='musxpand') {
				return mx_apiout(MXAPIAPPKEYFAIL,'Denied[A].');
			}
			if ($apphash!=md5($user.$mxappkey.MXAPPPRIVKEY.'20121221')) {
				return mx_apiout(MXAPIAPPHASHFAIL,'Denied[H].');
			}
			$dbuser=$mxdb->checkapplogin($user,$pwhash);
			if (!$dbuser) {
				return mx_apiout(MXAPIAPPUSERFAIL,'Denied[U].');
			}
			return mx_apiout(MXAPIOK,'Authentification OK.');
			break;
		default:
			return mx_apinotvalid('auth',$func);
	}
}

function mx_oauth2() {

}
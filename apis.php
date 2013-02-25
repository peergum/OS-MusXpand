<?php
/* ---
 * Project: musxpand
 * File:    apis.php
 * Author:  phil
 * Date:    Sep 18, 2012
 * ---
    This file is part of MusXpand.
    Copyright � 2010-2012 by Philippe Hilger
 */


require 'includes/mx_apis.php';

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
	$apiname=trim(preg_replace('[^a-zA-Z]','',mx_secureword($_REQUEST['a']))); // api name
	$apifunc=trim(preg_replace('[^a-zA-Z0-9]','',mx_secureword($_REQUEST['f']))); // api subcommand
	$apipar=json_encode(array(
		'mxappkey' => 'myapp',
		'user' => 'email@example.com',
		'pwhash' => 'mypwhash',
		'apphash' => 'myapphash')); //trim(preg_replace('[^a-zA-Z0-9]','',mx_secureword($_POST['p']))); // api parameters (JSON)
	//if (!$apiname || !$apipar) die('Crap.');
	die(json_encode(mx_apicall($apiname,$apifunc,$apipar)));
}

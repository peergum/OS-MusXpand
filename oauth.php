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


require 'includes/mx_oauth.php';

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
	$oauthfunc=trim(preg_replace('[^a-z_]','',mx_secureword($_REQUEST['a']))); // oauth function
	mx_oauth($oauthfunc);
}

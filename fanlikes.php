<?php
/* ---
 * Project: musxpand
 * File:    fanlikes.php
 * Author:  phil
 * Date:    11/10/2011
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

require 'includes/mx_artists.php';

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
	if (!$mxuser->id) return;
	$aid=trim(strtolower(preg_replace('![^0-9]!','',mx_secureword($_REQUEST['i']))));
	$like=mx_secureword($_REQUEST['l']);
	$dislike=mx_secureword($_REQUEST['d']);
	$r=mx_securestring($_REQUEST['r']);
	if (!$aid) { return; }
	if ($r) { //TODO review
		/*
		$msg=new StdClass();
		$msg->body=$b;
		$msg->filter=MXSHAREALL;
		$msg->refid=$msgid;
		$mxuser->saveupdate($msg);
		$like=0;
		$dislike=0;
		*/
	}
	mx_xmlfanlike($aid,$like,$dislike=!$like);
}

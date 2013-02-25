<?php
/* ---
 * Project: musxpand
 * File:    paypal.php
 * Author:  phil
 * Date:    09/09/2011
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

require 'includes/mx_walls.php';

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
	if (!$mxuser->id) return;
	$msgid=trim(strtolower(preg_replace('![^0-9]!','',mx_secureword($_REQUEST['m']))));
	$like=mx_secureword($_REQUEST['l']);
	$dislike=mx_secureword($_REQUEST['d']);
	$b=mx_securestring($_REQUEST['b']);
	$a=mx_securestring($_REQUEST['a']);
	$k=mx_securestring($_REQUEST['k']);

	if (!$msgid) return;
	if ($a=='d') { // delete wall
		die($mxuser->markwalldeleted($msgid));
	}
	//$fld=mx_securestring($_REQUEST['f']);
	if ($b) {
		$msg=new StdClass();
		$msg->body=$b;
		$msg->filter=MXSHAREALL;
		$msg->refid=mx_getrefid('wallid',$msgid);
		$mxuser->saveupdate($msg);
		$like=0;
		$dislike=0;
	}
	mx_xmlwalls($msgid,$like,$dislike,$k);
}

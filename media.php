<?php
/* ---
 * Project: musxpand
 * File:    media.php
 * Author:  phil
 * Date:    03/29/2012
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

require 'includes/mx_media.php';

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
	if (!$mxuser->id) return;
	$pid=trim(strtolower(preg_replace('![^0-9]!','',mx_secureword($_REQUEST['p'])))); // pic id or pos
	$mid=trim(strtolower(preg_replace('![^0-9]!','',mx_secureword($_REQUEST['m'])))); // media id
	$st=trim(strtolower(preg_replace('![^0-9]!','',mx_secureword($_REQUEST['s'])))); // new status
	$bid=trim(strtolower(preg_replace('![^0-9]!','',mx_secureword($_REQUEST['b'])))); // bundle id
	$did=trim(strtolower(preg_replace('![^0-9]!','',mx_secureword($_REQUEST['d'])))); // destination bundle id
	$fld=trim(strtolower(preg_replace('![^a-zA-Z]!','',mx_secureword($_REQUEST['f'])))); // fld (title or desc)
	$txt=trim(urldecode($_REQUEST['t'])); // fld text
	if (!$mid && !$st) return;
	if ($st && $mid) {
		die(json_encode($mxuser->setmediastatus($mid, $st)));
	}
	if ($fld) {
		die(json_encode($mxuser->updatemediainfo($mid, $fld, $txt)));
	}
	if ($did) { // p=pos
		die(json_encode($mxuser->movetobundle($mid,$did,$pid)));
	}
	if ($pid) { // p=pic id
		die(json_encode($mxuser->linkmedia($pid,$mid)));
	}
	die(json_encode(array('mediaid' => $mid, 'bundleid' => $bid, 'status' => mx_xmlmedia($mid))));
}

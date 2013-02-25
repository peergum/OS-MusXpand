<?php
/* ---
 * Project: musxpand
 * File:    favs.php
 * Author:  phil
 * Date:    06/11/2012
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

require 'includes/mx_account.php';

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
	if (!$mxuser->id) return;
	$rid=trim(strtolower(preg_replace('![^0-9]!','',mx_secureword($_REQUEST['r'])))); // favid to remove
	$fid=trim(strtolower(preg_replace('![^0-9]!','',mx_secureword($_REQUEST['f'])))); // obj id to add
	$tid=trim(strtolower(preg_replace('![^0-9]!','',mx_secureword($_REQUEST['t'])))); // obj type
	$bg=trim(strtolower(preg_replace('![^0-9]!','',mx_secureword($_REQUEST['bg'])))); // obj id to set as bg
	$ty=trim(strtolower(preg_replace('![^a-z]!','',mx_secureword($_REQUEST['ty'])))); // obj type
	if (!$rid && (!$fid || !$tid) && (!$bg || !$ty)) return;
	if ($rid) {
		die(json_encode($mxuser->delfav($rid)));
	}
	if ($fid && $tid) {
		die(json_encode($mxuser->addfav($fid,$tid)));
	}
	if ($bg && $ty) {
		$bgok=0;
		if ($ty=='m') {
			$media=$mxuser->getmediainfo($bg);
			if ($media->type==MXMEDIABG || $media->type==MXMEDIAPIC) $bgok=$bg;
			else {
				$media->linked=$mxuser->getlinkedmedia($media->id);
				if (is_array($media->linked)) $bgok=$media->linked[0]->id;
			}
		}
		if ($bgok) {
			$mxuser->setoption('background_id',$bgok);
			die(json_encode(array('success' => true,'url' => $mxuser->getbackgroundurl($bgok))));
		}
		die(json_encode(array('success' => false)));
	}
}

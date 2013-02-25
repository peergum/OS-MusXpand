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

require 'includes/mx_account.php';

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
	$mid=trim(strtolower(preg_replace('![^0-9]!','',mx_secureword($_REQUEST['m'])))); // media id
	$mpt=trim(strtolower(preg_replace('![^0-9]!','',mx_secureword($_REQUEST['mt'])))); // media playtype
	$act=trim(strtolower(mx_secureword($_REQUEST['a']))); // action
	$per=trim(strtolower(preg_replace('![^0-9.]!','',mx_secureword($_REQUEST['p'])))); // percent played
	$tim=trim(strtolower(preg_replace('![^0-9]!','',mx_secureword($_REQUEST['t'])))); // time played (sec)
	$rat=trim(strtolower(preg_replace('![^0-9]!','',mx_secureword($_REQUEST['r'])))); // rating
	$st=trim(strtolower(preg_replace('![^0-9]!','',mx_secureword($_REQUEST['s'])))); // status
	$id=trim(strtolower(preg_replace('![^0-9]!','',mx_secureword($_REQUEST['id'])))); // playid
	if (!$mid || !$act) return;
	if ($act!='update') error_log('play: [uid='.$mxuser->id.',mid='.$mid.',mpt='.$mpt.',act='.$act.',id='.$id.',per='.$per.',tim='.$tim.',rat='.$rat.',st='.$st.']');
	die(json_encode($mxuser->setplaytime($mid,$mpt,$act,$id,$per,$tim,$rat,$st)));
}

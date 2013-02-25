<?php
/* ---
 * Project: musxpand
 * File:    mx_check.php
 * Author:  phil
 * Date:    23/08/2011
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

include_once 'mx_init.php';

function mx_checkusername($myname,$name) {
	global $mxdb,$mxuser;
	if ($name) { $name=trim(strtolower(preg_replace('![^0-9a-zA-Z-_.]!','',$name))); }
	if (!$name || $name=='' || $name==$myname) $rep=MXUNEMPTYNOCHANGE;
	else if (!$mxuser || !$mxuser->id) $rep=MXUNNOTLOGGED;
	else if ($name=='fb' || $name=='musxpand' || $name=='admin') $rep=MXUNRESTRICTED;
	else if (preg_replace('%[0-9]%','',$name)=='') $rep=MXUNONLYNUMBERS;
	else $rep=$mxdb->checkusername($name);
	//error_log('rep='.$rep.' myname='.$myname.' reqname='.$name);
	return $rep;
}


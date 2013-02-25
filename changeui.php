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

require 'includes/mx_init.php';

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
	if (!$mxuser->id) return;
	$bloc=trim(strtolower(preg_replace('![^a-z]!','',mx_secureword($_REQUEST['b'])))); // bloc name
	$mods=trim(strtolower(preg_replace('![^a-z,_]!','',mx_securestring($_REQUEST['m'])))); // modules
	error_log('id:'.$mxuser->id.' b:'.$bloc.' m:'.$mods);
	if (!$mods || !$bloc) return;
	die(json_encode($mxuser->setmodules($bloc,$mods)));
}

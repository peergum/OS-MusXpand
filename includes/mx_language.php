<?php
/* ---
 * Project: musxpand
 * File:    mx_language.php
 * Author:  phil
 * Date:    29/09/2010
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

global $mxlocale;

function __($string) {
	echo _($string);
}

function mx_setlocale() {
	global $mxlocale;
	putenv('LC_ALL='.$mxlocale);
	setlocale(LC_ALL,$mxlocale.'.utf-8',
		$mxlocale.'.utf8',
		$mxlocale.'.UTF-8',
		$mxlocale.'.UTF8',
		$mxlocale);
}

bindtextdomain($MXDomain,$MXRootPath.'/lang');
textdomain($MXDomain);

// check change in locale and apply

$page=($_GET['p']);
$option=($_GET['o']);
$action=($_REQUEST['a']);

if ($page=='account' && $option=='profile' && $action=='done') {
	$locale=$_REQUEST['locale'];
	if ($locale!='' && $mxlocale!=$locale) {
		$mxlocale=$locale;
		$_SESSION['mxlocale']=$mxlocale;
	}
	header("Location: ".$_SERVER['PHP_SELF']."?p=$page&o=$option&a=ok");
}

mx_setlocale();


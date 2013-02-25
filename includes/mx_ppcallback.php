<?php
/* ---
 * Project: musxpand
 * File:    mx_ppcallback.php
 * Author:  phil
 * Date:    May 13, 2011
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

    Copyright © 2010 by Philippe Hilger
 */

if ($_POST['METHOD']=='CallbackRequest') {
	
	foreach ($_POST as $key => $value) {
		$value = urlencode(stripslashes($value));
		$req .= "&$key=$value";
	}

	$fp=fopen('/tmp/ppcb.log','a+');
	fputs($fp,$req);
	if ($_POST['SHIPTOCOUNTRYCODE']=='CA') {
	}
	fclose($fp);
}
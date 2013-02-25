<?php
/* ---
 * Project: musxpand
 * File:    mx_picupdate.php
 * Author:  phil
 * Date:    17/12/2010
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

include_once 'includes/mx_init.php';
require_once 'ext_includes/fileuploader.php';

// list of valid extensions, ex. array("jpeg", "xml", "bmp")
$allowedExtensions = array('jpg','jpeg','png','gif');

// max file size in bytes
$sizeLimit = 10 * 1024 * 1024;

$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
//$userdir = mx_option('usersdir').'/'.$mxuser->hashdir;
//$mxuser->checkuserdir();
$result = $uploader->handleUpload(mx_option('usersdir').'/tmp/',true);
error_log(print_r($result,true));
if (!array_key_exists('error',$result)) {
	$result=$mxuser->addmedia($uploader->getName(),$uploader->getSize(),MXMEDIAREADY,
		'Profile Pic',MXMEDIAPIC,$uploader->getName());
		//$result['error']=$result['link'];
}
//error_log(print_r($result,true));

// to pass data through iframe you will need to encode all html tags
print_r(htmlspecialchars(json_encode($result), ENT_NOQUOTES));
//error_log('picupload return: '.htmlspecialchars(json_encode($result), ENT_NOQUOTES));

?>

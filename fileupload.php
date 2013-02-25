<?php
/* ---
 * Project: musxpand
 * File:    mx_fileupload.php
 * Author:  phil
 * Date:    12/10/2010
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

if (!$mxuser->id) {
	$result=array('error' => 'You have been idle for too long. Please reload the page.');
	print_r(htmlspecialchars(json_encode($result), ENT_NOQUOTES));
	die();
}

// increase timeout
mx_setsession($mxuser,time()+1200);

// list of valid extensions, ex. array("jpeg", "xml", "bmp")
$allowedExtensions = array('mp3','jpg','jpeg','png','gif','doc','pdf','m4v','mp4','mov');

// max file size in bytes
$sizeLimit = MXMAXFILESIZE;

$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
// --- removed to save to a global /users/tmp dir on the web server, then addmedia moves to S3
//$userdir = mx_option('usersdir').'/'.$mxuser->hashdir;
//$mxuser->checkuserdir();
//$result = $uploader->handleUpload($userdir.'/tmp/',true);
// --- end
//error_log("before upload\n");
$result = $uploader->handleUpload(mx_option('usersdir').'/tmp/',true);
//error_log("after upload\n");

if (!array_key_exists('error',$result)) {
	switch(strtolower(pathinfo($uploader->getName(),PATHINFO_EXTENSION))) {
		case 'mp3':
		case 'mp4':
			$ftype=MXMEDIASONG;
			break;
		case 'jpg':
		case 'jpeg':
		case 'png':
		case 'gif':
			$ftype=MXMEDIAPIC;
			break;
		case 'm4v':
		case 'mov':
		case 'avi':
		case 'mpg':
		case 'mpeg':
			$ftype=MXMEDIAVIDEO;
			break;
		default:
			$ftype=MXMEDIAUNDEFINED;
			break;
	}
	//$result=$mxuser->addmedia($uploader->getName(),$uploader->getSize(),0);
	//error_log('calling addmedia');
	$result=$mxuser->addmedia($uploader->getName(),$uploader->getSize(),MXMEDIAVALIDATED,
		$uploader->getName(),$ftype,$uploader->getName());
	//error_log('back from addmedia');
}

if ($result['error'] && strlen($result['error'])>160) $result['error']=substr($result['error'],0,160).'[...]';
//error_log('fileupload.php: '.print_r($result,true));
// to pass data through iframe you will need to encode all html tags
print_r(htmlspecialchars(json_encode($result), ENT_NOQUOTES));
//error_log('fileupload.php return: '.htmlspecialchars(json_encode($result), ENT_NOQUOTES));
?>

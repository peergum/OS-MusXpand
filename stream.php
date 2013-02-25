<?php
/* ---
 * Project: musxpand
 * File:    mx_stream.php
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

    Copyright © 2010 by Philippe Hilger
 */

include_once 'mx_init.php';

//header('content-type: text/plain');
//die(mx_option('usersdir').'/'.$mxuser->hashdir.'/media/4c5fc57c429c299a8d03e65d6de08735.mp3');

$hash=$_GET['hc'];
$id=$_GET['id'];
$stream=$mxdb->getstream($id,$hash);
if (!$stream) die();

preg_match('%([^.]+)$%',$stream->filename,$ext);
$mimetypes=array(
	'mp3' => 'audio/mpeg',
	'wav' => 'audio/x-wav',
	'aif' => 'audio/x-aiff',
	'jpg' => 'image/jpeg',
	'gif' => 'image/gif',
	'png' => 'image/png',
	'pdf' => 'application/pdf',
	'mpg' => 'video/mpeg',
	'mpeg' => 'video/mpeg',
	'mp4' => 'video/mpeg',
	'm4v' => 'video/mpeg',
	'qt' => 'video/quicktime',
	'mov' => 'video/quicktime',
	'doc' => 'application/msword'
);

/*switch($filetypes[$stream->type]) {
	case 'Song':
	case 'Instrumental':
	case 'Demo/Draft (incomplete)':
	case 'Demo/Draft (complete)':
		header('content-type: audio/binary');
		break;
	case 'Video':
		header('content-type: video/binary');
		break;
	case 'Picture':
		header('content-type: image/binary');
		break;
	default:
		header('content-type: text/plain');
		echo 'file: '.$stream->filename.' type: '.$filetypes[$stream->type];
		die();
}*/
header('content-type: '.$mimetypes[$ext[0]]);
//header('content-length: '.$stream->filesize);
$song=file_get_contents(mx_option('usersdir').'/'.$mxuser->hashdir.'/media/'.$hash.'.'.strtolower($ext[0]));
echo $song;

?>

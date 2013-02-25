<?php
/* ---
 * Project: musxpand
 * File:    mx_media.php
 * Author:  phil
 * Date:    19/10/2010
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

require_once 'includes/mx_init.php';

$mediacache=array();

function mx_ckmedia($page,$option,$action) {
	if (!$option && !is_logged()) $_REQUEST['o']='pubmed';
}

function mx_mnmedia($page,$option,$action) {
	if (!$option) mx_showhtmlpage('media');
}

function mx_ckbundle($page,$option,$action) {
	global $mxuser;
	$fid=mx_secureword($_POST['bundleid']);
	$ftitle=mx_securestring($_POST['buntitle']);
	$ftype=MXMEDIAREGULARBUNDLE;
	$fdesc=mx_securestring($_POST['bundesc']);
	$fcomp=MXMEDIANOSTATUS; //mx_secureword($_POST['buncomp']);
	$status=mx_secureword($_POST['bunshare']);
	switch($action) {
		case 'bundok':
			//$mxuser->updatemediadesc($fid,$ftitle,$ftype,$fdesc,$fcomp);
			header('location: '.mx_actionurl('account','mystuff','','new'));
			die();
			break;
		case 'bundone':
			$mxuser->publishmedia($fid,$status,$ftitle,$ftype,$fdesc,$fcomp);
			$bundledmedia=$mxuser->listmediafrombundle($fid,'',$mxuser->id);
			foreach ($bundledmedia as $media) {
				$mxuser->publishmedia($media->id,MXMEDIAFANVISIBLE,$media->title,
					$media->type,$media->description,$media->completion);
			}
			header('location: '.mx_actionurl('account','mystuff','','published'));
			die();
			break;
	}
}

function mx_mnbundle($page,$option,$action) {
	global $mxuser;

	// work media table
	//-----$nbundleid=$mxuser->getnewbundle();
	$wbundleid=$mxuser->getbasebundle();
	$wbundle=$mxuser->getmediainfo($wbundleid);
	if (!$wbundle) {
		__('This media could not be found.');
		return;
	}
	$fanship=null;
	mx_medialist($wbundle,$fanship,true,true);
	$allmedia=array();
	$mediaarray=array();
	$mediaarray['newbun']=array(-1,_('Media'),_('The following media are currently available in your Work Bundle.'
		.'<br/>Select the ones you want in your new bundle'));
	$mediaarray['mediadata']=array(0,_('Uploaded Media'),'html');

	$medialist=array(
		'medialist',0,_('Media List'),'',
		array(
			'newbun' => array(
				//'download' => _('Download'),
			)
		),
		array(
			'newbun' => $mediaarray
		)
	);
	$allmedia['newbun'][]=$wbundle;

	$newarray=array();
	$newarray['newbun']=array(-1,_('Media'),_('Preparing your new bundle is as easy as using drap and drop'
			.' from your Work Bundle.<br/>You can also drag and drop thumbnails to the bundle icon and track icons.'
			.'<br/>And finally you can also reorder the media within the bundle.'));
	$newarray['mediadata']=array(0,_('New Bundle\'s Contents'),'html');

	$newlist=array(
		'medialist',0,_('Media List'),'',
		array(
			'newbun' => array(
				//'download' => _('Download'),
			)
		),
		array(
			'newbun' => $newarray
		)
	);
	$newbundid=$mxuser->getnewbundle();
	$newbund=$mxuser->getmediainfo($newbundid);
	mx_medialist($newbund,$fanship,true,true);
	$newmedia['newbun'][]=$newbund;
	// bundle form
	$bunflds=array(
		/*'bunname' => array(1,_('New Bundle Name'),'text','80'),
		'bunshare' => array(1,_('Shared With'),'mediastatus','',
			_('<dl><dt>Public</dt><dd>(Recommended) Everyone can see the bundle and preview its content</dd>'
				.'<dt>Members</dt><dd>All MusXpand members can see the bundle and preview its content</dd>'
				.'<dt>Fans</dt><dd>Only fans can see the bundle and fully access its content</dd></dl>'
				)
			),*/
		'sep1' => array(-1,_('New Bundle'),' '),
		'newb' => array(-2,$newlist[5],$newmedia,'media',$newlist[4],'newbun'),
		'uploadhere' => array(-3,array(
			'upload' => array('<div id="fileuploader"></div>'),
			'mystuff' => _('Back to My Stuff'),
			//'bundone' => _('I\'m done, publish now!'),
			//'bundok' => _('I\'m done but publish later...'),
			)),
		'sep2' => array(-1,_('Uploaded Media Pool'),' '),
		'medias' => array(-2,$medialist[5],$allmedia,'media',$medialist[4],'newbun'),
		'bundleid' => array(0,$newbundid,'hidden'),
		'a' => array(1,'none','hidden')
 	);
	$buttons=array(
		//'upload' => array('<div id="fileuploader"></div>'),
		);
	$bunform=array(
		'bunform',0,_('Bundle Builder'),
			_('Prepare your new bundle below...')
			.'<div class="helpnotice"><span class="helpnotice">'._('On quality:').'</span> '
			._('for music, we now <u>require</u> artists to upload 320Kbps/44KHz media files (HiFi) for the fans to enjoy a'
			.' satisfactory listening experience.').'</div>',
			$buttons,
			$bunflds
	);

	$bunvals=array('bunshare' => MXMEDIAPUBLIC);

	//echo '<a name="mediaplayer"></a><div id="mediaplayer"><div id="playerwindow"></div></div>';
	mx_showform($bunform,$bunvals);
	echo '<script language="javascript">
		openbundle('.$wbundleid.',0,true);
		openbundle('.$newbundid.',0,true);
		</script>';
	echo '<script language="javascript">
		setdroppable('.$newbundid.');
		setsortable('.$newbundid.');
		setworksortable('.$wbundleid.');
		</script>';

	?>
	<script language="javascript">
	    function createUploader(){
	        var uploader = new qq.FileUploader({
	            element: document.getElementById('fileuploader'),
	            template: '<div class="qq-uploader">' +
	                '<div class="qq-upload-drop-area"><span>Drop new media here</span></div>' +
	                '<div class="qq-upload-button">Upload or drop new media here</div>' +
	                '<ul class="qq-upload-list"></ul>' +
	             	'</div>',
	            action: '<?php echo mx_option('siteurl').'/fileupload.php'; ?>',
	            params: {
	            	id: '<?php echo $mxuser->id; ?>'
	            },
	            maxConnections: 3,
	            allowedExtensions: ['jpg','jpeg','png','mp3','m4v','pdf','mp4','mov'],
				sizeLimit: <?php echo MXMAXFILESIZE; ?>,
				onComplete: function(id,filename,result) { if ('success' in result) showupload(result); }
	        });
	    }

	    // in your app create uploader as soon as the DOM is ready
	    // don't wait for the window to load
	    if (window.onload) {
	    	var oldloadfunction=window.onload;
	    	window.onload = (typeof window.onload != 'function') ? createUploader : function() { oldloadfunction(); createUploader(); };
	    } else window.onload = createUploader;
	</script>
	<?php
}


function mx_ckmystuff($page,$option,$action) {
	global $mxuser,$errors;
	switch ($action) {
	case 'm_bundle':
		header('location:'.mx_optionurl('account','bundle'));
		die();
	case 'mediapublish':
		$pid=0;
		//die(phpinfo());
		while ($fid=$_POST['id_'.$pid]) {
			//$fname=$_POST['name_'.$pid];
			$ftitle=$_POST['title_'.$pid];
			$ftype=$_POST['type_'.$pid];
			$fdesc=$_POST['desc_'.$pid];
			$fcomp=$_POST['comp_'.$pid];
			$status=$_POST['status_'.$pid];
			if ($ftype==MXMEDIABASEBUNDLE) $status=MXMEDIAVIRTUAL;
			if ($status>=MXMEDIAREADY) {
				$mxuser->publishmedia($fid,$status,$ftitle,$ftype,$fdesc,$fcomp);
				if ($ftype==MXMEDIAREGULARBUNDLE) {
					//error_log('bundle publishing!');
					$bundledmedia=$mxuser->listmediafrombundle($fid,'',$mxuser->id);
					foreach ($bundledmedia as $media) {
						//error_log('publishing '.$media->title);
						$mxuser->publishmedia($media->id,$status,$media->title,
							$media->type,$media->description,$media->completion);
					}
				}
			} else {
				if ($errors) $errors.='<br/>';
				$errors.=sprintf(_('[%s] is not ready and cannot be changed at this time'),$ftitle);
			}
			$pid++;
		}
		$_REQUEST['k']='published';
		break;
	case 'mediaupdate':
		$pid=0;
		//die(phpinfo());
		while ($fid=$_POST['id_'.$pid]) {
			//$fname=$_POST['name_'.$pid];
			$ftitle=$_POST['title_'.$pid];
			$ftype=$_POST['type_'.$pid];
			$fdesc=$_POST['desc_'.$pid];
			$status=$_POST['status_'.$pid];
			$fcomp=$_POST['comp_'.$pid];
			if ($ftype==MXMEDIABASEBUNDLE) $status=MXMEDIAVIRTUAL;
			if ($status>=MXMEDIAREADY) {
				$mxuser->publishmedia($fid,$status,$ftitle,$ftype,$fdesc,$fcomp);
				if ($ftype==MXMEDIAREGULARBUNDLE) {
					//error_log('bundle publishing!');
					$bundledmedia=$mxuser->listmediafrombundle($fid,'',$mxuser->id);
					foreach ($bundledmedia as $media) {
						//error_log('publishing '.$media->title);
						$mxuser->publishmedia($media->id,$status,$media->title,
							$media->type,$media->description,$media->completion);
					}
				}
			} else {
				if ($errors) $errors.='<br/>';
				$errors.=sprintf(_('[%s] is not ready and cannot be changed at this time'),$ftitle);
			}
			$pid++;
		}
		break;
	case 'mediadescupdate':
		$pid=0;
		//die(phpinfo());
		while ($fid=$_POST['id_'.$pid]) {
			$fname=$_POST['name_'.$pid];
			$ftitle=$_POST['title_'.$pid];
			$ftype=$_POST['type_'.$pid];
			$fdesc=$_POST['desc_'.$pid];
			$fcomp=$_POST['comp_'.$pid];
			$fdelete=$_POST['delete_'.$pid];
			//if ($status>=MXMEDIAREADY) {
				if ($fdelete=='1') {
					$mxuser->deletemedia($fid,$fname);
				} else {
					$mxuser->updatemediadesc($fid,$ftitle,$ftype,$fdesc,$fcomp);
				}
			/*} else {
				if ($errors) $errors.='<br/>';
				$errors.=sprintf(_('[%s] is not ready and cannot be changed at this time'),$ftitle);
			}*/
			$pid++;
		}
		break;
	case 'newbundle':
		$bundle=mx_securestring($_REQUEST['bundlename']);
		if ($bundle) {
			if (!$mxuser->createbundle($bundle))
				$errors=_('Error: Bundle was not created.');
		} else {
			$errors=_('You did not inform a name for the new bundle.');
		}
		break;
	case 'm_movenew':
	case 'm_movepub':
		$bid=mx_secureword($_REQUEST['bundleid']);
		if ($bid) {
			$selmedia=$_REQUEST['selmedia'];
			if (!$selmedia) {
				$errors=_('No media selected.');
				break;
			}
			$medialist=$mxuser->listselectedmedia($selmedia);
			if (!$medialist) {
				$errors=_('No media selected.');
				break;
			}
			$errors='';
			foreach($medialist as $id => $media) {
				if ($res=$mxuser->movetobundle($id,$bid)) {
					if ($errors) $errors.='<br/>';
					switch ($res) {
						case MXDBERROR:
							$errors.=sprintf(_('Failed moving [%s] to bundle'),$media->title);
							break;
						case MXNOCHANGE:
							$errors.=sprintf(_('[%s] is shared: cannot be moved to a non-shared bundle.'),$media->title);
							break;
					}
				}
			}
		} else {
			$errors=_('You did not inform the bundle to move your media to.');
		}
		$_REQUEST['k']=$action=='m_movenew'?'new':'published';
		break;
	case 'm_linknew':
	case 'm_linkpub':
		$selmedia=$_REQUEST['selmedia'];
		if (!$selmedia) {
			$errors=_('No media selected.');
			break;
		}
		$medialist=$mxuser->listselectedmedia($selmedia);
		if (!$medialist) {
			$errors=_('No media selected.');
			break;
		}
		$errors='';
		// see what we need to link
		$pic=$trk=$doc=0;
		$trkmedia=array();
		$picmedia=array();
		$docmedia=array();
		foreach($medialist as $id => $media) {
			switch($media->type) {
				case MXMEDIAPIC:
				case MXMEDIABG:
					$picmedia[$id]=$media;
					$pic++;
					break;
				case MXMEDIASONG:
				case MXMEDIAINSTR:
				case MXMEDIAVIDEO:
				case MXMEDIAREGULARBUNDLE:
				case MXMEDIABASEBUNDLE:
					$trkmedia[$id]=$media;
					$trk++;
					break;
				case MXMEDIADOC:
					$docmedia[$id]=$media;
					$doc++;
					break;
			}
		}
		if ($pic+$doc+$trk<2) {
			$errors=_('We need at least two medias for a link, e.g. a picture and a track/bundle');
			break;
		}
		if (!$pic && !$doc) {
			$errors=_('You need to select one picture OR one document to link to the tracks/bundles');
			break;
		}
		if (!$trk && $pic>1 && $doc>1) {
			$errors=_('I\'m a bit confused about what to link to what...?!');
			break;
		}
		if (!$trk && !$doc) {
			$errors=_('You cannot link pictures together...');
			break;
		}
		if (!$trk && !$pic) {
			$errors=_('You cannot link documents together...');
			break;
		}
		if ($trk) { // link pics and/or docs to tracks or bundles
			$linkmedia=array_replace($picmedia,$docmedia);
			$destmedia=$trkmedia;
		} else if ($pic==1) { // link pic to docs
			$linkmedia=$picmedia;
			$destmedia=$docmedia;
		} else if ($doc==1) { // link doc to pics
			$linkmedia=$docmedia;
			$destmedia=$picmedia;
		}
		foreach ($destmedia as $id => $media) {
			foreach ($linkmedia as $lid => $lmedia) {
				if ($lmedia->owner_id != $mxuser->id) { // something wrong: linking media from someone else...
					if ($errors) $errors.='<br/>';
					$errors.=sprintf(_('Media [%s] is not yours!'),$lmedia->title);
				} else if ($media->owner_id != $mxuser->id) { // idem: media linked to is not ours!!
					if ($errors) $errors.='<br/>';
					$errors.=sprintf(_('Media [%s] is not yours!'),$media->title);
				} else if ($res=$mxuser->linkmedia($lid,$id)) {
					if ($errors) $errors.='<br/>';
					switch ($res) {
						case MXDBERROR:
						case MXNOLINK:
							$errors.=sprintf(_('Failed linking [%s] to [%s]'),$lmedia->title,$media->title);
							break;
					}
				}
			}
		}
		if ($action=='m_linknew') $_REQUEST['k']='new';
		else $_REQUEST['k']='published';
		break;
	case 'm_scannew':
	case 'm_scanpub':
	case 'm_scanarch':
	case 'm_scanmed':
		$selmedia=$_REQUEST['selmedia'];
		if (!$selmedia) {
			$errors=_('No media selected.');
			break;
		}
		$medialist=$mxuser->listselectedmedia($selmedia);
		if (!$medialist) {
			$errors=_('No media selected.');
			break;
		}
		$errors='';
		foreach($medialist as $id => $media) {
			if ($media->type==MXMEDIABASEBUNDLE || $media->type==MXMEDIAREGULARBUNDLE)
				continue;
			$mxuser->rescanmedia($media);
			//$mxuser->setmediastatus($id,MXMEDIAVALIDATED);
		}
		break;
	case 'm_deletenew':
	case 'm_deletepub':
	case 'm_deletearch':
		$selmedia=$_REQUEST['selmedia'];
		if (!$selmedia) {
			$errors=_('No media selected.');
			break;
		}
		$medialist=$mxuser->listselectedmedia($selmedia);
		if (!$medialist) {
			$errors=_('No media selected.');
			break;
		}
		foreach($medialist as $id => $media) {
			$res = $mxuser->deletemedia($id,$media->filename);
			if (array_key_exists('error',$res)) {
				if ($errors) $errors.='<br/>';
				$errors.=$res['error'];
			}
		}
		break;
	case 'm_archivepub':
		$selmedia=$_REQUEST['selmedia'];
		if (!$selmedia) {
			$errors=_('No media selected.');
			break;
		}
		$medialist=$mxuser->listselectedmedia($selmedia);
		if (!$medialist) {
			$errors=_('No media selected.');
			break;
		}
		foreach($medialist as $id => $media) {
			$mxuser->archivemedia($id,$media->filename);
		}
		$_REQUEST['k']='archived';
		break;
	case 'm_publishnew':
	case 'm_publisharch':
	case 'm_editnew':
	case 'm_editpub':
	case 'm_editarch':
	case 'm_editmed':
		$selmedia=$_REQUEST['selmedia'];
		if (!$selmedia) {
			$errors=_('No media selected.');
			$_REQUEST['a']='';
		}
		break;
	default:
		break;
	}
}
function mx_mnmystuff($page,$option,$action) {
	global $mxuser,$errors;
	if ($errors) mx_warning($errors);
	switch ($action) {
	case 'submit':
		print_r($_FILES);
		break;
	case 'fileupload':
		$tmpfiles=$mxuser->gettmpmedia();
		if (!$tmpfiles) {
			__('No files seem to have been uploaded to your account.');
			break;
		}
		$allmedia=array();
		$mediaval=array();
		$pid=0;
		$onlynew=$_REQUEST['onlynew'];
		//echo 'onlynew:'.$onlynew.'<br/>time()='.time().' - date():'.date('U').' - gmdate():'.gmdate('U').'<br/>';
		while ($tmpfiles && $media=$mxuser->gettmpmedia($tmpfiles)) {
			$flds= array(
				'filename_'.$pid => array(-1,$media->filename),
				'id_'.$pid => array(1,$media->id,'hidden'),
				'name_'.$pid => array(0,$media->filename,'hidden'),
				'title_'.$pid => array(1,_('Title:'),'text',100),
				'type_'.$pid => array(1,_('Type:'),'filetype'),
				'desc_'.$pid => array(1,_('Description:'),'simplememo'),
				'comp_'.$pid => array(1,_('State:'),'completion'),
				'delete_'.$pid => array(1,_('Delete:'),'checkbox',
					_('Permanently remove this file from server'),
					null,($onlynew && $onlynew>strtotime($media->timestamp)))
			);
			//echo 'id #'.$pid.' date:'.$media->timestamp.' ('.strtotime($media->timestamp).')<br/>';
			switch(pathinfo($media->filename,PATHINFO_EXTENSION)) {
				case 'jpg':
				case 'png':
				case 'gif':
					$ftype=MXMEDIAPIC;
					break;
				case 'mp4':
				case 'm4v':
				case 'avi':
				case 'mov':
					$ftype=MXMEDIAVIDEO;
					break;
				case 'mp3':
				case 'wav':
				case 'aif':
					$ftype=MXMEDIASONG;
					break;
				default:
					$ftype=MXMEDIADOC;
					break;
			}
			$fvalues=array(
				'title_'.$pid => $media->filename,
				'type_'.$pid => $ftype,
				'desc_'.$pid => _('You should describe your media here...'),
				'comp_'.$pid => MXMEDIANOSTATUS,
				'delete_'.$pid => 1
			);
			$allmedia=array_merge($allmedia,$flds);
			$mediaval=array_merge($mediaval,$fvalues);
			$pid++;
		}
		$allmedia['a']=array(1,'mediaupdate','hidden');
		$buttons=array(
			'mediaupdate' => _('Submit'),
			'clear' => _('Clear')
		);
		$mediaform=array(
			'mediaform',0,_('Media Information'),_('Please fill in the information' .
					' about the uploaded files'),
			$buttons,
			$allmedia
		);
		mx_showform($mediaform,$mediaval);
		break;
	case 'm_publishnew':
	case 'm_publisharch':
	case 'm_editpub':
	case 'm_editarch':
	case 'm_editmed':
		$selmedia=$_REQUEST['selmedia'];
		if (!$selmedia) {
			__('No media selected.');
			break;
		}
		$medialist=$mxuser->listselectedmedia($selmedia);
		if (!$medialist) {
			__('No media selected.');
			break;
		}
		$allmedia=array();
		$mediaval=array();
		$pid=0;
		foreach($medialist as $id => $media) {
			$flds= array(
				'filename_'.$pid => array(-1,$media->filename),
				'id_'.$pid => array(1,$id,'hidden'),
				'name_'.$pid => array(0,$media->filename,'hidden'),
				'title_'.$pid => array(1,_('Title:'),'text',60),
				'desc_'.$pid => array(1,_('Description:'),'simplememo','','',_('You should describe your media here...')),
				'type_'.$pid => array(1,_('Type:'),'filetype'),
				'comp_'.$pid => array(1,_('State:'),'completion'),
				'size_'.$pid => array(0,_('Size:'),'size'),
				'status_'.$pid => array(1,_('Status:'),'mediastatus'),
			//'delete_'.$pid => array(1,_('Delete:'),'checkbox',
				//	_('Permanently remove this file from server'),
				//	null,($onlynew && $onlynew>strtotime($media->timestamp)))
			);
			//echo 'id #'.$pid.' date:'.$media->timestamp.' ('.strtotime($media->timestamp).')<br/>';
			switch(pathinfo($media->filename,PATHINFO_EXTENSION)) {
				case 'jpg':
				case 'png':
				case 'gif':
					$ftype=MXMEDIAPIC;
					break;
				case 'mp4':
				case 'm4v':
				case 'avi':
				case 'mov':
					$ftype=MXMEDIAVIDEO;
					break;
				case 'mp3':
				case 'wav':
				case 'aif':
					$ftype=MXMEDIASONG;
					break;
				default:
					$ftype=MXMEDIADOC;
					break;
			}
			$fvalues=array(
				'title_'.$pid => ($media->title?$media->title:$media->filename),
				'size_'.$pid => $media->size,
				'type_'.$pid => ($media->type?$media->type:$ftype),
				'desc_'.$pid => $media->desc,
				'comp_'.$pid => $media->comp,
				'status_'.$pid => $media->status,
					//'delete_'.$pid => 1
			);
			$allmedia=array_merge($allmedia,$flds);
			$mediaval=array_merge($mediaval,$fvalues);
			$pid++;
		}
		$agreement=array(
			'warning' => array(-1,_('Legal Terms'),_('By checking the box below,'
				.' you acknowledge that any media made available to your current fans'
				.' will be available to them for at least one full year from the date of'
				.' its publication. Whoever is not a fan will lose access to any media as soon as you'
				.' archive them.')),
			//'agreement' => array(1,_('I Agree'),'boolean',2,_('You have to agree to continue...')),
			'agreement' => array(1,_('Agreement'),'checkbox',_('I fully agree with the above statement.'),_('You have to agree to continue...')),
		);
		$allmedia=array_merge($allmedia,$agreement);
		$allmedia['a']=array(1,'none','hidden');
		//$mediaval['agreement']=0;
		$buttons=array(
			'mediapublish' => _('Publish'),
			'mymedia' => _('Cancel'),
			'clear' => _('Reset form')
		);
		$mediaform=array(
			'mediaform',0,_('Media Information'),_('Please inform the scope of publication' .
					' for each media'),
			$buttons,
			$allmedia
		);
		mx_showform($mediaform,$mediaval,true);
		break;
	case 'm_editnew':
	//case 'm_editpub':
	//case 'm_editarch':
	//case 'm_editmed':
		$selmedia=$_REQUEST['selmedia'];
		if (!$selmedia) {
			__('No media selected.');
			break;
		}
		$medialist=$mxuser->listselectedmedia($selmedia);
		if (!$medialist) {
			__('No media selected.');
			break;
		}
		$allmedia=array();
		$mediaval=array();
		$pid=0;
		foreach($medialist as $id => $media) {
			$flds= array(
				'filename_'.$pid => array(-1,$media->filename),
				'id_'.$pid => array(1,$id,'hidden'),
				'name_'.$pid => array(0,$media->filename,'hidden'),
				'title_'.$pid => array(1,_('Title:'),'text',100),
				'type_'.$pid => array(1,_('Type:'),'filetype'),
				'size_'.$pid => array(0,_('Size:'),'integer'),
				'desc_'.$pid => array(1,_('Description:'),'simplememo','','',_('You should describe your media here...')),
				'comp_'.$pid => array(1,_('State:'),'completion'),
			//'delete_'.$pid => array(1,_('Delete:'),'checkbox',
				//	_('Permanently remove this file from server'),
				//	null,($onlynew && $onlynew>strtotime($media->timestamp)))
			);
			if ($action=='m_editpub') {
				$flds['status_'.$pid]=array(1,_('Access'),'mediastatus');
			}
			//echo 'id #'.$pid.' date:'.$media->timestamp.' ('.strtotime($media->timestamp).')<br/>';
			switch(pathinfo($media->filename,PATHINFO_EXTENSION)) {
				case 'jpg':
				case 'jpeg':
				case 'png':
				case 'gif':
					$ftype=MXMEDIAPIC;
					break;
				case 'mp4':
				case 'm4v':
				case 'avi':
				case 'mov':
					$ftype=MXMEDIAVIDEO;
					break;
				case 'mp3':
				case 'wav':
				case 'aif':
					$ftype=MXMEDIASONG;
					break;
				default:
					$ftype=MXMEDIADOC;
					break;
			}
			$fvalues=array(
				'title_'.$pid => ($media->title?$media->title:$media->filename),
				'size_'.$pid => $media->size,
				'type_'.$pid => ($media->type?$media->type:$ftype),
				'desc_'.$pid => $media->desc,
				'comp_'.$pid => $media->comp,
				'status_'.$pid => $media->status,
					//'delete_'.$pid => 1
			);
			$allmedia=array_merge($allmedia,$flds);
			$mediaval=array_merge($mediaval,$fvalues);
			$pid++;
		}
		$allmedia['a']=array(1,'mediadescupdate','hidden');
		$buttons=array(
			'mediadescupdate' => _('Submit'),
			'mymedia' => _('Cancel'),
			'clear' => _('Clear')
		);
		$mediaform=array(
			'mediaform',0,_('Media Information'),_('Please fill in the information' .
					' about the uploaded files'),
			$buttons,
			$allmedia
		);
		mx_showform($mediaform,$mediaval);
		break;
	case 'mymedia':
	default:
		$mxuser->checkbundles();
		$listorder=mx_secureword($_GET['s']);
		if ($listorder!='') $listorder.=' asc';
		$listorder.=($listorder?',':'').'timestamp desc';
		$allmedia=array();
		$medialist=array(
			'medialist',0,_('Media List'),'',
			array(
				'new' => array(
					//'upload' => _('Upload'),
					//'m_editnew' => _('Edit'),
					'm_bundle' => _('Bundle Maker'),
					'm_publishnew' => _('Publish'),
					//'m_linknew' => _('Link'),
					'm_deletenew' => _('Delete'),
					//'m_scannew' => _('Rescan'),
					'clear' => _('Clear')
				),
				'published' => array(
					//'m_editpub' => _('Edit'),
					//'m_linkpub' => _('Link'),
					//'m_deletepub' => _('Delete'),
					//'m_archivepub' => _('Archive'),
					//'m_scanpub' => _('Rescan'),
					'clear' => _('Clear')
				),
				'archived' => array(
					//'m_editarch' => _('Edit'),
					'*m_deletearch' => _('Delete [ADMIN]'), // admin only (*)
					//'m_publisharch' => _('Re-Publish'),
					//'m_scanarch' => _('Rescan'),
					'clear' => _('Clear')
				),
				'allmedia' => array(
					//'m_editmed' => _('Edit'),
					//'m_scanmed' => _('Rescan'),
					'clear' => _('Clear')
				),
			),
			array(
				'new' => array(
					'new' => array(-1,_('New Media & Uploads'),_('Below is the list of all media you uploaded recently'
						.' and still haven\'t published for fans, members and/or everyone to discover.')
						.'<div class="helpnotice"><span class="helpnotice">'._('On quality:').'</span> '
						._('for music, we now <u>require</u> artists to upload 320Kbps/44KHz media files (HiFi) for the fans to enjoy a'
						.' satisfactory listening experience.')
						.'<br/>'._('Click or the Help button on the right for some help about the publishing process').'</div>'
						.'<div id="fileuploader"></div>'),
					'select' => array(0,'<input id="checkallbox" type="checkbox" onclick="javascript:checkall(\'new\');">','html',3),
					'mediadata' => array(0,'<div id="player">'
						.mx_mediabutton('openbundle', _('Open All Bundles'),24,'oa_',0,'hover')
						.mx_mediabutton('notready.gif', _('Please wait...'),24,'wa_',0)
						.mx_mediabutton('closebundle', _('Close All Bundles'),24,'ca_',0,'hover')
						.'</div>','html'),
					/*
					'buttons' => array(0,'<div id="player">'
						.mx_mediabutton('openbundle', _('Open All Bundles'),24,'oa_',0,'hover')
						.mx_mediabutton('notready.gif', _('Please wait...'),24,'wa_',0)
						.mx_mediabutton('closebundle', _('Close All Bundles'),24,'ca_',0,'hover')
						.'</div>','text'),
					//'infobtn' => array(0,_('Actions'),'mediainfo'),
					'meddesc' => array(0,_('Title').mx_orderlink('title'),'text'),
					//'status' => array(0,_('Access').mx_orderlink('status'),'mediastatus'),
					'type'  => array(0,_('Type').mx_orderlink('type'),'mediatype'),
					'info' => array(0,_('Info'),'text'),
					'filesize' => array(0,_('Size'),'size'),
					//'activation' => array(1,_('Info'),'date'),
					//'completion'  => array(1,_('State').mx_orderlink('completion'),'completion'),
					//'status'  => array(1,_('Target').mx_orderlink('status'),'mediastatus'),
					//'list' => array(1,_('Media'),'pubmedia')
					*/
					'a' => array(1,'newbundle','hidden'),

				),
				'published' => array(
					'published' => array(-1,_('Published'),_('The media you published so far.')),
					'select' => array(0,'<input id="checkallbox" type="checkbox" onclick="javascript:checkall(\'published\');">','html',3),
					'mediadata' => array(0,'<div id="player">'
						.mx_mediabutton('openbundle', _('Open All Bundles'),24,'oa_',0,'hover')
						.mx_mediabutton('notready.gif', _('Please wait...'),24,'wa_',0)
						.mx_mediabutton('closebundle', _('Close All Bundles'),24,'ca_',0,'hover')
						.'</div>','html'),
					/*
					'buttons' => array(0,'<div id="player">'
						.mx_mediabutton('openbundle', _('Open All Bundles'),24,'oa_',0,'hover')
						.mx_mediabutton('notready.gif', _('Please wait...'),24,'wa_',0)
						.mx_mediabutton('closebundle', _('Close All Bundles'),24,'ca_',0,'hover')
						.'</div>','text'),
					//'infobtn' => array(0,_('Actions'),'mediainfo'),
					'meddesc' => array(0,_('Title').mx_orderlink('title'),'text'),
					'status' => array(0,_('Access').mx_orderlink('status'),'mediastatus'),
					'type'  => array(0,_('Type').mx_orderlink('type'),'mediatype'),
					'info' => array(0,_('Info'),'text'),
					'filesize' => array(0,_('Size'),'size'),
					//'activation' => array(1,_('Info'),'date'),
					//'completion'  => array(1,_('State').mx_orderlink('completion'),'completion'),
					//'status'  => array(1,_('Target').mx_orderlink('status'),'mediastatus'),
					//'list' => array(1,_('Media'),'pubmedia')
					*/
					'a' => array(1,'none','hidden'),
				),
				'archived' => array(
					'archived' => array(-1,_('Archived'),_('The published media you decided to archive<br/>' .
							' (still available to fans during one year after archived).')),
					'select' => array(0,'<input id="checkallbox" type="checkbox" onclick="javascript:checkall(\'archived\');">','html',3),
					'mediadata' => array(0,'<div id="player">'
						.mx_mediabutton('openbundle', _('Open All Bundles'),24,'oa_',0,'hover')
						.mx_mediabutton('notready.gif', _('Please wait...'),24,'wa_',0)
						.mx_mediabutton('closebundle', _('Close All Bundles'),24,'ca_',0,'hover')
						.'</div>','html'),
					/*
					'buttons' => array(0,'<div id="player">'
						.mx_mediabutton('openbundle', _('Open All Bundles'),24,'oa_',0,'hover')
						.mx_mediabutton('notready.gif', _('Please wait...'),24,'wa_',0)
						.mx_mediabutton('closebundle', _('Close All Bundles'),24,'ca_',0,'hover')
						.'</div>','text'),
					//'infobtn' => array(0,_('Actions'),'mediainfo'),
					'meddesc' => array(0,_('Title').mx_orderlink('title'),'text'),
					'status' => array(0,_('Access').mx_orderlink('status'),'mediastatus'),
					'type'  => array(0,_('Type').mx_orderlink('type'),'mediatype'),
					'info' => array(0,_('Info'),'text'),
					'filesize' => array(0,_('Size'),'size'),
					//'activation' => array(1,_('Info'),'date'),
					//'completion'  => array(1,_('State').mx_orderlink('completion'),'completion'),
					//'status'  => array(1,_('Target').mx_orderlink('status'),'mediastatus'),
					//'list' => array(1,_('Media'),'pubmedia')
					*/
					'a' => array(1,'none','hidden'),
				),
				'allmedia' => array(
					'allmedia' => array(-1,_('All Media'),_('Below is the list of all media you uploaded' .
							' into your account.')),
					'select' => array(0,'<input id="checkallbox" type="checkbox" onclick="javascript:checkall(\'allmedia\');">','html',3),
					'mediadata' => array(0,'<div id="player">'
						.mx_mediabutton('openbundle', _('Open All Bundles'),24,'oa_',0,'hover')
						.mx_mediabutton('notready.gif', _('Please wait...'),24,'wa_',0)
						.mx_mediabutton('closebundle', _('Close All Bundles'),24,'ca_',0,'hover')
						.'</div>','html'),
					/*
					'buttons' => array(0,'<div id="player">'
						.mx_mediabutton('openbundle', _('Open All Bundles'),24,'oa_',0,'hover')
						.mx_mediabutton('notready.gif', _('Please wait...'),24,'wa_',0)
						.mx_mediabutton('closebundle', _('Close All Bundles'),24,'ca_',0,'hover')
						.'</div>','text'),
					//'infobtn' => array(0,_('Actions'),'mediainfo'),
					'meddesc' => array(0,_('Title').mx_orderlink('title'),'text'),
					'status' => array(0,_('Access').mx_orderlink('status'),'mediastatus'),
					'type'  => array(0,_('Type').mx_orderlink('type'),'mediatype'),
					'info' => array(0,_('Info'),'text'),
					'filesize' => array(0,_('Size'),'size'),
					//'activation' => array(1,_('Info'),'date'),
					//'completion'  => array(1,_('State').mx_orderlink('completion'),'completion'),
					//'status'  => array(1,_('Target').mx_orderlink('status'),'mediastatus'),
					//'list' => array(1,_('Media'),'pubmedia')
					*/
					'a' => array(1,'none','hidden'),
				),
			),
		);
		$mediabundles=$mxuser->listbundles($mxuser->id,null,$listorder,true);
		while ($mediabundles && $bundle=$mxuser->listbundles($mxuser->id,$mediabundles,$listorder,true)) {
			foreach($mxuser->getmediainfo($bundle->id) as $key => $value) {
				$bundle->$key=$value;
			}
			$bundle->filesize=$bundle->size;
			//$bundle->info=sprintf(_('%d media<br/>(%s)'),$bundle->cnt,mx_size($bundle->size));
			mx_medialist($bundle,null,false,true);
			//error_log(print_r($bundle,true));
			if ($bundle->type==MXMEDIABASEBUNDLE) {
				$allmedia['new'][]=$bundle;
				$allmedia['published'][]=$bundle;
				$allmedia['archived'][]=$bundle;
			} else {
				if ($bundle->status<MXMEDIAFANVISIBLE) $allmedia['new'][]=$bundle;
				if ($bundle->status>=MXMEDIAFANVISIBLE && $bundle->status<MXMEDIAARCHIVED) $allmedia['published'][]=$bundle;
				if ($bundle->status==MXMEDIAARCHIVED) $allmedia['archived'][]=$bundle;
			} /* else if ($bundle->status==MXMEDIAVALIDATED || $bundle->status==MXMEDIAREADY) $allmedia['new'][]=$bundle;
			else if ($bundle->status==MXMEDIAARCHIVED) $allmedia['archived'][]=$bundle;
			else if ($bundle->status>=MXMEDIAFANVISIBLE && $bundle->status<=MXMEDIAPUBLICSHARED) {
				$allmedia['published'][]=$bundle;
				$allmedia['archived'][]=$bundle;
			}*/
			$allmedia['allmedia'][]=$bundle;
		}
		//$str='<div id="mediaplayer"><div id="playerwindow"></div></div>';
		//$str.='<div class="form">';
		/*$str.='<form name="media">'
		.mx_showtablestr($medialist[5],$allmedia,'pubmed',$medialist[4],'media')
		.'</form></div>';*/
		$str.=mx_showliststr($medialist,$allmedia,'media',true);
		/*$str.='<script type="text/javascript" charset="utf-8">
	    // Add VideoJS to all video tags on the page when the DOM is ready
	    VideoJS.setupAllWhenReady();
	  	</script>';*/
		echo $str;
		echo '<script language="javascript">';
		foreach($allmedia['allmedia'] as $bundle) {
			echo 'setdroppable('.$bundle->id.');';
			if ($bundle->type==MXMEDIABASEBUNDLE) echo 'setworksortable('.$bundle->id.');';
			else echo 'setsortable('.$bundle->id.');';
		}
		$wbundleid=$mxuser->getbasebundle();
		echo 'openbundle('.$wbundleid.');';
		echo '</script>';


		?>
<script language="javascript">
    function createUploader(){
        var uploader = new qq.FileUploader({
            element: document.getElementById('fileuploader'),
            action: '<?php echo mx_option('siteurl').'/fileupload.php'; ?>',
            params: {
            	id: '<?php echo $mxuser->id; ?>'
            },
            allowedExtensions: ['jpg','jpeg','png','mp3','m4v','pdf','mp4','mov'],
			sizeLimit: <?php echo MXMAXFILESIZE; ?>,
			onComplete: function(id,filename,result) { if ('success' in result) showupload(result); }
        });
    }

    // in your app create uploader as soon as the DOM is ready
    // don't wait for the window to load
    if (window.onload) {
    	var oldloadfunction=window.onload;
    	window.onload = (typeof window.onload != 'function') ? createUploader : function() { oldloadfunction(); createUploader(); };
    } else window.onload = createUploader;
</script>
		<?php

		break;
	case 'upload':
		?>
<div id="basicuploader">
<noscript>
		<?php
		__('Javascript is not currently enabled in your browser. You can enjoy a easier and' .
				' simpler file uploader if you enable that feature. If you prefer, consult' .
				' your manual, activate Javascript and reload this page...');
		?>
</noscript>
		<?
		$buttons=array(
			'submit' => _('Submit'),
			'clear' => _('Clear')
		);
		$uploadform=array(
			'basicupload',0,_('Media Upload [Basic Uploader]'),_('Please fill the form below to upload' .
					' new media to your account'),
			$buttons,
			array(
				'fileinfo' => array(-1,_('File Information'),_('Please describe the file you' .
						' will upload')),
				'name' => array(1,_('Media name:'),'text',100),
				'filename' => array(1,_('File:'),'file'),
				'type' => array(1,_('Type:'),'filetype'),
				'description' => array(1,_('Description:'),'simplememo'),
				'authorship' => array(-1,_('Legal Bindings'),_('By clicking the box below,' .
						' you hereby certify to the full extends of copyright laws that you' .
						' fully own the rights' .
						' on the material you are going to upload, or are legally' .
						' authorized to do so. You also confirm, that you are aware that' .
						' any false claim is subject to prosecution by the legal copyrights' .
						' owner to whom you will directly and exclusively respond to.')),
				'agreement'  => array(1,_('I Agree'),'boolean',2),
				'a' => array(1,'submit','hidden')
			)
		);
		mx_showform($uploadform,'',true);
		?>
</div>
<div id='jsuploader' style="display:none;">
		<?php
		$oldmedia="";
		$tmpfiles=$mxuser->gettmpmedia();
		while ($tmpfiles && $media=$mxuser->gettmpmedia($tmpfiles)) {
			$oldmedia.=$media->filename.'<br/>';
		}
		$buttons=array(
			'submit' => _('Submit'),
			'clear' => _('Clear')
		);
		$uploadform=array(
			'jsupload',0,_('Media Upload [Javascript Uploader]'),_('Please upload one or more files' .
					' below by clicking on the Upload button or using drag-and-drop, then submit the' .
					' form to fill the necessary information about the files'),
			$buttons,
			array(
				'upload' => array(-1,_('File Upload'),_('Please click the Upload button or' .
						' drag-and-drop your files on it<br/>' .
						'If you already uploaded files and want to validate them,' .
						' just agree with the legal terms and submit the form.')),
				'oldfiles' => array(0,_('Previously Uploaded:'),'text'),
				'onlynew' => array(1,_('Only New'),'checkbox',_('Discard previously uploaded files')),
				'files' => array(1,_('Files:'),'fileuploader'),
				'authorship' => array(-1,_('Legal Bindings'),_('By clicking the box below,' .
						' you hereby certify to the full extends of copyright laws that you' .
						' fully own the rights' .
						' on the material you are going to upload, or are legally' .
						' authorized to do so. You also confirm, that you are aware that' .
						' any false claim is subject to prosecution by the legal copyrights' .
						' owner to whom you will directly and exclusively respond to.')),
				'agreement'  => array(1,_('I Agree'),'boolean',2),
				'a' => array(1,'fileupload','hidden')
			)
		);
		mx_showform($uploadform, array(
			'oldfiles' => ($oldmedia?$oldmedia:_('None')),
			'onlynew' => time()
			),true);
		?>
</div>

<script language="javascript">
	var jsuploader=document.getElementById('jsuploader');
	jsuploader.style.display='block';
	var bsuploader=document.getElementById('basicuploader');
	bsuploader.style.display='none';

    function createUploader(){
        var uploader = new qq.FileUploader({
            element: document.getElementById('fileuploader'),
            action: '<?php echo mx_option('siteurl').'/fileupload.php'; ?>',
            params: {
            	id: '<?php echo $mxuser->id; ?>'
            },
            allowedExtensions: ['jpg','jpeg','png','mp3','m4v','pdf','mp4','mov'],
			sizeLimit: <?php echo MXMAXFILESIZE; ?>
        });
    }

    // in your app create uploader as soon as the DOM is ready
    // don't wait for the window to load
    if (window.onload) {
    	var oldloadfunction=window.onload;
    	window.onload = (typeof window.onload != 'function') ? createUploader : function() { oldloadfunction(); createUploader(); };
    } else window.onload = createUploader;
</script>
<?php
	}
?>
	<script type="text/javascript" charset="utf-8">
    // Add VideoJS to all video tags on the page when the DOM is ready
    VideoJS.setupAllWhenReady();
  	</script>
<?php
}

function mx_mnfanmed($page,$option,$action) {
	global $mxuser;
	mx_showhtmlpage('fanmed');
	$subs=$mxuser->getsub(); // TODO: search active subs only
	//error_log('subs:'.print_r($subs,true));
	$artists=array();
	foreach ($subs as $sub) {
		if (is_valid($sub))
			$artists[]=$sub->artistid;
	}
	echo mx_showmediastr($artists,'fanmed');
}

function mx_mnmembmed($page,$option,$action) {
	mx_showhtmlpage('membmed');
	echo mx_showmediastr(0,'membmed');
}

function mx_mnpubmed($page,$option,$action) {
	mx_showhtmlpage('pubmed');
	echo mx_showmediastr(null,'pubmed');
	return;
}

//function mx_showmediastr($mediatable) {
function mx_showmediastr($artistid,$section='media',$openmedia=-1) {
	global $mxuser;
	$listorder=mx_secureword($_GET['s']);
	if ($listorder!='') $listorder.=' asc';
	else $listorder='type asc, title asc';
	$listorder.=($listorder?',':'').'timestamp desc';
	/*
	if (is_array($artistid)) {
		foreach($artistid as $oneid) {
			$fanship[$oneid]=$mxuser->getfanship($oneid);  // TODO: fanship should be defined properly
		}
	}
	else if ($artistid==null) $fanship=array(MXNONMEMBER,null);
	else if (!$artistid) $fanship=array(MXMEMBER,null);
	else $fanship[$artistid]=$mxuser->getfanship($artistid);
	*/
	$allmedia=array();
	$mediaarray=array();
	$mediaarray[$section]=array(-1,_('Media'),_('The following media are currently available.'));
	//$mediaarray['buttons']=array(0,'','text',30);
	/*
	if (!$artistid || is_array($artistid)) { // if not individual artist, list artist name
			$mediaarray['artistname']=array(0,'','text');
	}
	*/
	$mediaarray['mediadata']=array(0,_('Media'),'html');
	/*
	$mediaarray['meddesc']=array(0,_('Title').mx_orderlink('title'),'text');
	if ($artistid && !is_array($artistid) && $section!='fanmed') {
		$mediaarray['status']=array(0,_('Access').mx_orderlink('status'),'mediastatus');
	}
	$mediaarray['type']=array(0,_('Type').mx_orderlink('type'),'mediatype');
	$mediaarray['info']=array(0,_('Info'),'text');
	$mediaarray['timestamp']=array(0,_('Date'),'update');
	*/
	$medialist=array(
		'medialist',0,_('Media List'),'',
		array(
			$section => array(
				//'download' => _('Download'),
			)
		),
		array(
			$section => $mediaarray
		)
	);

	// --- BUNDLES
	$mediabundles=$mxuser->listbundles($artistid,null,$listorder);
	$featbunok=false;
	while ($mediabundles && $bundle=$mxuser->listbundles($artistid,$mediabundles,$listorder)) {
		if (!$bundle->cnt) continue;
		$fanship=$mxuser->getfanship($bundle->owner_id,$bundle->id);
		foreach($mxuser->getmediainfo($bundle->id) as $key => $value) {
			$bundle->$key=$value;
		}
		$bundle->info=sprintf(_('%d media<br/>(%s)'),$bundle->cnt,mx_size($bundle->size));
		if ((($bundle->type==MXMEDIABASEBUNDLE /*&& !$fanship */) || $bundle->type==MXMEDIAREGULARBUNDLE)
			&& (($bundle->status>=MXMEDIAFANVISIBLE && $bundle->status<MXMEDIAARCHIVED) || $bundle->status==MXMEDIAVIRTUAL)
			/*&& ($fanship==null || $fanship[0]==MXME
				|| ($bundle->status==MXMEDIAFANVISIBLE && $fanship[0]>=MXFAN)
				|| ($bundle->status==MXMEDIAMEMBERVISIBLE && $fanship[0]>=MXMEMBER)
				|| ($bundle->status==MXMEDIAPUBLIC)
			)*/
		) {
			if ((!$featbunok && $openmedia<=0
				&& $bundle->type==MXMEDIAREGULARBUNDLE) || $openmedia==$bundle->id) $featbun=$featbunok=true;
			else $featbun=false;
			mx_medialist($bundle,$fanship,false,false,$featbun);
			$bundle->filesize=$bundle->size;
			$allmedia[$section][]=$bundle;
		} else {
			mx_medialist($bundle,$fanship,false,false,$featbun);
			$bundle->filesize=$bundle->size;
			if ($bundle->status>=MXMEDIAVALIDATED || $bundle->status<MXMEDIAFANVISIBLE) $allmedia['new'][]=$bundle;
			if ($bundle->status==MXMEDIAARCHIVED) $allmedia['archived'][]=$bundle;
			if ($bundle->status>=MXMEDIAFANVISIBLE && $bundle->status<=MXMEDIAPUBLICSHARED) $allmedia['published'][]=$bundle;
			if ($bundle->status==MXMEDIAVIRTUAL) {
				$allmedia['new'][]=$bundle;
				$allmedia['published'][]=$bundle;
				$allmedia['archived'][]=$bundle;
			}
			/*
			if (($bundle->status>MXMEDIAREADY && $bundle->status<=MXMEDIAPUBLICSHARED)) {
				$allmedia['media'][]=$bundle;
			}
			*/
			$allmedia['allmedia'][]=$bundle;
		}
	}
	// --- END BUNDLES
	//foreach($mediatable as $media) {
	/*
	// --- NO BUNDLES
	$mediatable=$mxuser->listmedia($artistid,null,$listorder);
	if (!$mediatable) {
		//return _('No media available for the moment...');
	}
	while ($mediatable && $media=$mxuser->listmedia($artistid,$mediatable)) {
		//error_log(print_r($media,true));
		$buttons=null;
		if (($media->status>MXMEDIAREADY && $media->status<=MXMEDIAPUBLICSHARED) || $artistid==$mxuser->id) {
			mx_medialist($media,$fanship); // prepare media fields
			$allmedia['media'][]=$media;
		}
	}
	// --- END NO BUNDLES
	*/
	//$str='<a name="mediaplayer"></a>';
	//$str.='<div id="mediaplayer"><div id="playerwindow"></div></div>';
	$str.='<div class="form"><a name="medialist"></a>';
	$str.='<form name="media">'
	.mx_showtablestr($medialist[5],$allmedia,'pubmed',$medialist[4],$section)
	.'</form></div>';
	/*$str.='<script type="text/javascript" charset="utf-8">
    // Add VideoJS to all video tags on the page when the DOM is ready
    VideoJS.setupAllWhenReady();
  	</script>';*/
	if (false && $openmedia>0) {
		$media=$mxuser->getmediainfo($openmedia);
		if ($media->type==MXMEDIABASEBUNDLE || $media->type==MXMEDIAREGULARBUNDLE) {
			$openbundle=$openmedia;
			$str.='<script language="javascript">openbundle('.$openbundle.');</script>';
		} else {
			$openbundle=$media->bundles[0]->id;
			$str.='<script language="javascript">openbundle('.$openbundle.','.$openmedia.');</script>';
		}
	}
	return $str;
}

/*
 * Shows one playable media
 */
function mx_showonemedia($user) {
	global $mxuser;
	$media=$mxuser->getonemedia($user->id);
	$media->hashdir=$user->hashdir;
	//error_log(print_r($media,true));
	if ($media) {
		$fanship=$mxuser->getfanship($user->id,$media->id);
		mx_medialist($media,$fanship);
		$str='<div class="mediaplayable">';
		$str.='<div class="mediabuttons">'.'<div id="player">'.$media->buttons.'</div>'.'</div>';
		$str.='<div class="featartname">'.strtoupper(mx_getartistname($user)).'</div>';
		$str.='<div class="mediatitle">'.$media->title.'</div>';
		$str.='<div class="mediabundle">'.sprintf(_('<i>From</i> <b>%s</b>'),$media->bundletitle).'</div>';
		$str.=$media->content;
		$str.='</div>'; // mediaplayable
		return $str;
	}
}

function mx_medialink($fname,$hashcode,$hashdir=null,$addin='',$download=false) {
	global $s3,$mxuser;
	preg_match('%([^.]+)$%',$fname,$ext);
	if (!$hashdir) $hashdir=$mxuser->hashdir;
	$keyname='users/'.$hashdir.'/media/'.$hashcode.$addin.'.'.strtolower($ext[0]);
	if ($addin != '-preview' && $addin != '-small' && $addin != '-thumb') $timeout='60 minutes';
	else $timeout='15 days';
	if (!$download) $url=gets3url($keyname,$timeout);
	else $url=$s3->get_object_url(MXS3BUCKET,$keyname,$timeout, array(
		'response' => array(
			'content-type' => 'application/octet-stream',
			'content-disposition' => 'inline; filename='.$fname,
		)
	));
	return mx_secureurl($url);
	//return mx_option('usersURL').'/'.$hashdir.'/media/'.$hashcode.'.'.strtolower($ext[0]);
	//return mx_option('siteurl').'/stream.php?id='.$this->id.'&hc='.$hashcode;
}

function mx_mediadata($media,$fanship,$mystuff=false,$bmaker=false,$featbun=false) {
	global $mxuser,$mediastatuses;
	if ($media->type==MXMEDIABASEBUNDLE) {
		$str='<div class="bundledata bid_'.$media->id.'" xitemprop="bundle" xitemscope xitemtype="http://schema.org/MusicAlbum/MediaBundle">';
		$lmeds=''; // linked media for slideshow
		$lmedc=0;
		$mediapic=mx_iconurl('mediabundle');
		foreach ($media->linked as $linked) { // look for a pic into linked media
			if ($linked->type==MXMEDIAPIC || $linked->type==MXMEDIABG) { // use first pic found
				$mediapic=mx_medialink('xx.jpg',$linked->hashcode,$media->hashdir,'-thumb');
				break;
			}
		}
		$media->pic=$mediapic;
		$media->dragpic=null;
		$media->mediapic='<img tag="'.$media->id.'" class="bundlepic" src="'.$mediapic.'" xitemprop="image" />';
		$str.='<table>';
		$media->share='';
		$str.='<td colspan="2" tag="'.$media->id.'" class="bundleright'.(($media->type==MXMEDIAREGULARBUNDLE)?' newmedia':' workmedia').'">';
		if ($media->buttons) $str.='<div class="bundlebuttons">'.'<div id="player">'.$media->buttons.'</div>'.'</div>';
		$str.='<div class="bundledetails">';
		$str.='<div class="bundleinfo">'.sprintf(_('<b>Medias:</b> %s<br/><b>Size:</b> %s'),
			'<span'.($mystuff?' id="numtracks"':'').' xitemprop="numTracks">'.(0+$media->cnt).'</span>',
			'<span'.($mystuff?' id="bunsize"':'').'>'.mx_size($media->size).'</span>')
		.'</div>';
		$str.='<meta itemprop="byArtist" content="'.$media->artistname.'" />';
		$str.='<div class="bundletitle'.($fanship==null?(' ts_'.$media->status):'').'" xitemprop="name">';
		$str.=_('The medias below are not currently part of any bundle');
		$str.='</div>'; // end title div
		if ($media->year) $str.='<div class="bundlerelease"'
		.($mystuff?' id="bunyear" onclick="textedit(this);"':'')
		.'>('
			.($media->month?(mx_monthname($media->month).'-'):'').'<span xitemprop="copyrightYear">'.$media->year.'</span>)</div>';
		$str.=$media->content;
		$str.='</div>'; // end bundledetails
		$str.='<div class="bundledmedia">';
		$str.='<table'.($mystuff?' class="sortmedia dropzone"':'').'><tr id="row_'.$media->id.'"><td'
		.($mystuff?(' colspan='.($bmaker?'3':'4')):'').'>';
		if ($mystuff) {
			$str.='<div class="dropzone">&darr; ';
			if ($media->type==MXMEDIABASEBUNDLE) $str.=_('Drag and drop unused media here...');
			else if ($media->status>=MXMEDIAREADY && $media->status<MXMEDIAFANVISIBLE) $str.=_('Drag and drop and reorder already uploaded media here...');
			else if ($media->status>=MXMEDIAFANVISIBLE && $media->status<MXMEDIAARCHIVED) $str.=_('Drag and drop between published bundles or reorder yourmedia...');
			$str.='</div>';
		}
		$str.='</td></tr>';
		//$bunstr=mx_showbundle($media->id,($mystuff?'newbun':'media'),'');
		//$str.='<!-- '.$bunstr.' -->';
		$str.='</table></div>';
		$str.='</td></table>'; // bundleright
		$str.='</div>'; // end bundledata
	} else if ($media->type==MXMEDIAREGULARBUNDLE) {
		// BUNDLES
		$str='<div class="bundledata bid_'.$media->id.'" yitemprop="albums" yitemscope yitemtype="http://schema.org/MusicAlbum">'; // /MediaBundle
		$media->schema='itemprop="album" itemscope itemtype="http://schema.org/MusicAlbum"';
		$media->meta='';
		$lmeds=''; // linked media for slideshow
		$lmedc=0;
		$mediapic=mx_iconurl('mediabundle');
		foreach ($media->linked as $linked) { // look for a pic into linked media
			if ($linked->type==MXMEDIAPIC || $linked->type==MXMEDIABG) { // use first pic found
				$mediapic=mx_medialink('xx.jpg',$linked->hashcode,$media->hashdir,'-thumb');
				break;
			}
		}
		$media->pic=$mediapic;
		$media->dragpic=null;
		$media->mediapic='<img tag="'.$media->id.'" class="bundlepic" src="'.$mediapic.'" yitemprop="image" />';
		$media->mediapicnoprop='<img tag="'.$media->id.'" class="bundlepic mxobject" src="'.$mediapic.'" />';
		$str.='<table><td class="bundleleft">';
		$url=mx_option('basicsiteurl').'/m/'.$media->id;
		$media->url=$url;
		$str.='<div class="bundlemainpic">'
		.'<a href="'.$url.'" title="'.$media->title.' - '._('See Media Page').'" class="pictooltip" yitemprop="url">'
		.$media->mediapic
		.'</a>'
		.'</div>';
		if ($media->purchase) {
			$str.=$media->pricetag.$media->purchase;
		}
		if (!is_logged() && !$mystuff && !$bmaker && ($media->status>=MXMEDIAFANVISIBLE && $media->status<MXMEDIAARCHIVED)) {
			$sharebutton=mx_sharebuttons('m'.$media->id,$url,$media->pic,$media->description);
			$media->share=$sharebutton;
			$str.=$sharebutton;
		} else {
			$media->share='';
		}
		if ($fanship==null || $fanship[0]==MXME) {
			if ($mystuff) {
				$sharestat=array(1,_('Shared With'),'bundlestatus','',
					_('<dl><dt>Public</dt><dd>(Recommended) <b>Everyone</b> can see the bundle and <b>preview</b> its content</dd>'
					//.'<dt>Members</dt><dd>All MusXpand members can see the bundle and preview its content</dd>'
					.'<dt>Fans</dt><dd><b>Only fans</b> can see the bundle and <b>fully access</b> its content</dd></dl>'
					)
				);
				$str.=mx_formfield('bunshare_'.$media->id,$media->status,$sharestat);
			} else {
				$str.='<div class="bundlestatus ms_'.$media->status.'">'.$mediastatuses[$media->status].'</div>';
			}
		}
		$links='';
		if ($fanship==null && count($media->linked)>0) {
			$str.=' '.mx_icon('linkmedia',_('Links'),12,'lm_'.$media->id,'linkmediahover');
			$links='<div class="bunlinks ld_'.$media->id.'">';
			foreach($media->linked as $linked) {
				$links.='<div class="bunlink ld_'.$media->id.'_'.$linked->id.'">'
				.'<img src="'.mx_medialink('xx.jpg',$linked->hashcode,$media->hashdir,'-small').'"/>'
				.'<div class="dellink">'
				.mx_icon('dellink',_('Remove'),19,'ul_'.$media->id.'_'.$linked->id,'dellinkhover')
				.'</div>'
				.'</div>';
			}
			$links.='</div>';
		}
		$str.='</td>'; // bundleleft
		$str.='<td tag="'.$media->id.'" class="bundleright'.(($media->type==MXMEDIAREGULARBUNDLE)?' newmedia':' workmedia').'">';
		if ($media->buttons) $str.='<div class="bundlebuttons">'.'<div id="player">'.$media->buttons.'</div>'.'</div>';
		$str.='<div class="bundledetails">';
		$durh=floor($media->totaltime/3600);
		$durm=floor(($media->totaltime-3600*$durh)/60);
		$durs=$media->totaltime-3600*$durh-60*$durm;
		$dur=($durh>0?($durh.':'):'').sprintf('%02d:%02d',$durm,$durs);
		$str.='<div class="bundleinfo">'.sprintf(_('<b>Duration:</b> %s<br/><b>Medias:</b> %s<br/><b>Size:</b> %s'),
			'<span'.($mystuff?' id="playtime"':'').' xitemprop="duration">'.$dur.'</span>',
			'<span'.($mystuff?' id="numtracks"':'').' xitemprop="numTracks">'.(0+$media->cnt).'</span>',
			'<span'.($mystuff?' id="bunsize"':'').'>'.mx_size($media->size).'</span>')
		.'</div>';
		$str.='<meta yitemprop="byArtist" content="'.$media->artistname.'" />';
		$str.='<div class="bundletitle'.($fanship==null?(' ts_'.$media->status):'').'" yitemprop="name">';
		if ($mystuff){
			$title=array(1,'','mediatitle',80,'',_('Bundle Title'));
			$str.=mx_formfield('title_'.$media->id,$media->title,$title);
		} else
			$str.=$media->title;
		$str.='</div>'; // end title div
		if ($media->year) $str.='<div class="bundlerelease"'
		.($mystuff?' id="bunyear" onclick="textedit(this);"':'')
		.'>('
			.($media->month?(mx_monthname($media->month).'-'):'').'<span xitemprop="copyrightYear">'.$media->year.'</span>)</div>';
		$str.='<div class="bundledesc" yitemprop="description">';
		if ($mystuff){
			$desc=array(1,'','mediadesc',4,'',_('Bundle Description'),60);
			$str.=mx_formfield('desc_'.$media->id,$media->description,$desc);
		} else
			$str.=$media->description;
		$str.='</div>';
		$str.=$links;
		$str.=$media->content;
		$str.='</div>'; // end bundledetails
		$str.='<div class="bundledmedia">';
		$str.='<table'.($mystuff?' class="sortmedia dropzone"':'').'><tr id="row_'.$media->id.'"><td'
		.($mystuff?(' colspan='.($bmaker?'3':'4')):'').'>';
		if ($mystuff) {
			$str.='<div class="dropzone">&darr; ';
			if ($media->type==MXMEDIABASEBUNDLE) $str.=_('Drag and drop unused media here...');
			else if ($media->status>=MXMEDIAREADY && $media->status<MXMEDIAFANVISIBLE) $str.=_('Drag and drop and reorder already uploaded media here...');
			else if ($media->status>=MXMEDIAFANVISIBLE && $media->status<MXMEDIAARCHIVED) $str.=_('Drag and drop between published bundles or reorder yourmedia...');
			$str.='</div>';
		}
		$str.='</td></tr>';
		if ($featbun) {
			$bunstr=mx_showbundle($media->id,($mystuff?'newbun':'media'),'');
			$str.=$bunstr;
			$media->featured=true;
		}
		$str.='</table></div>';
		$str.='</td></table>'; // bundleright
		$str.='</div>'; // end bundledata
	} else {
		// NORMAL MEDIA
		switch($media->type) {
			case MXMEDIABG:
			case MXMEDIAPIC:
				$schematype='CreativeWork';
				$proptype=''; //encodings';
				$infotype='';
				break;
			case MXMEDIAINSTR:
			case MXMEDIASONG:
				$schematype='MusicRecording';
				$proptype='tracks';
				$infotype='duration';
				$infotxt=preg_replace('%^([0-9]+):([0-9]+)$%','PT\1M\2S',$media->info);
				break;
			case MXMEDIAVIDEO:
				$schematype='Movie';
				$proptype=''; //encoding';
				$infotype='';
				break;
			default:
				$schematype='CreativeWork';
		}
		$str2='<div class="mediadata">';
		$str='<div class="mediadata bid_'.$media->id.'" '
		.($proptype?('itemprop="'.$proptype.'"'):'')
		.'itemscope itemtype="http://schema.org/'.$schematype.'">';
		$media->schema='itemprop="'.$proptype.'" itemscope itemtype="http://schema.org/'.$schematype.'"';
		if ($media->type==MXMEDIAPIC || $media->type==MXMEDIABG) {
			$mediapic=mx_medialink('xx.jpg',$media->hashcode,$media->hashdir,'-thumb');
		} else if (!$media->pic) {
			if (!count($media->bundles)) $mediapic=mx_iconurl('mediatype_'.$media->type);
			else {
				// take bundle pic by default...
				$bundle=$media->bundles[0];
				$bfanship=$mxuser->getfanship($bundle->owner_id,$bundle->id);
				mx_medialist($bundle,$bfanship);
				mx_mediadata($bundle,$bfanship);
				$mediapic=$bundle->pic;
			}
		} else { $mediapic=$media->pic; }
		$lmeds=''; // linked media for slideshow
		$lmedc=0;
		$newpic=0;
		foreach ($media->linked as $linked) { // look for a pic into linked media
			if (($linked->type==MXMEDIAPIC || $linked->type==MXMEDIABG) && !$newpic) { // use first pic found
				$mediapic=mx_medialink('xx.jpg',$linked->hashcode,$media->hashdir,'-thumb');
				$newpic=1;
			}
			$lmeds.='<a href="'.mx_medialink('xx.jpg',$linked->hashcode,$media->hashdir,'-small').'"'
			.' id="lmedia_'.$media->id.'_l'.$lmedc.'"></a>';
			$lmedc++;
		}
		$media->pic=$mediapic;
		$media->dragpic=new stdClass();
		$media->dragpic->id=$media->id;
		$media->dragpic->type=$media->type;
		$media->dragpic->pic=$media->pic;
		$media->mediapic='<img tag="'.$media->id.'" class="bundlepic" src="'.$mediapic.'" itemprop="image" />';
		$url=mx_option('basicsiteurl').'/m/'.$media->id;
		$media->url=$url;
		$str2.=$media->purchase;
		$str.=$media->purchase;
		if (!$mystuff) {
			$mpic='<div class="mediamainpic">'
			.'<a href="'.$url.'" title="'.$media->title.' - '.('See Media Page').'" class="pictooltip" itemprop="url">'
			.$media->mediapic
			.'</a>'
			.'</div>';
			$str.=$mpic;
			$str2.=$mpic;
		}
		$bitrate=floor($media->id3info['audio']['bitrate']/1000);
		$brmode=$media->id3info['audio']['bitrate_mode'];
		if (!$bitrate) $quality="";
		else if (($brmode=='cbr' && $bitrate>=320)
			|| ($brmode=='vbr' && $bitrate>128)) $quality=' <span class="hifi">HiFi</span>';
		else if (($brmode=='cbr' && $bitrate>=160)
			|| ($brmode=='vbr' && $bitrate>96)) $quality=' <span class="mifi">MiFi</span>';
		else if (($brmode=='cbr' && $bitrate>=96)
			|| ($brmode=='vbr' && $bitrate>64)) $quality=' <span class="lofi">LoFi</span>';
		else $quality=' <span class="nofi">NoFi</span>';
		$str.='<div class="mediainfo">'.($media->info?($media->info.' '):'')
		//.'('.mx_size($media->filesize).')'
		.$quality.'</div>';
		if ($infotype) {
			$media->meta='<meta itemprop="'.$infotype.'" content="'.$infotxt.'" />'
			.'<meta itemprop="url" content="'.$url.'" />';
			$str.=$media->meta;
		}
		if ($proptype=='tracks') $str.='<meta itemprop="inalbum" content="'.$media->bundletitle.'" />';
		$mbut='<div class="mediabuttons">'.'<div id="player">'.$media->buttons.'</div>'.'</div>';
		$str.=$mbut;
		$str2.=$mbut;
		$mtit='<div class="mediatitle'.($fanship==null?(' ts_'.$media->status):'').'" itemprop="name">';
		if ($media->position && !preg_match('%^[0-9]+ *[.)/-]%',$media->title)) $mtit.=$media->position.'. ';
		if ($mystuff){
			$title=array(1,'','mediatitle',80,'',_('Media Title'));
			$mtit.=mx_formfield('title_'.$media->id,$media->title,$title);
		} else {
			$mtit.=$media->title;
		}
		$str.='</div>';
		$str.=$mtit;
		$str2.=$mtit;
		//error_log('title='.$media->title.' status='.$media->status.' fanship='.print_r($fanship,true));
		$links='';
		if ($fanship==null && count($media->linked)>0) {
			/*
			$links=' '.mx_icon('linkmedia',_('Links'),12,'lm_'.$media->id,'linkmediahover')
			.'<div class="medlinks ld_'.$media->id.'"><table>';
			$l=0;
			foreach($media->linked as $linked) {
				if ($l%3==0) $links.='<tr>';
				$links.='<td><img src="'.mx_medialink('xx.jpg',$linked->hashcode,$media->hashdir,'-small')
					.'"/></td>';
				if ($l % 3 == 2) $links.='</tr>';
				$l++;
			}
			if ($l%3>0) $links.='<td colspan="'.(3-($l%3)).'"></td></tr>';
			$links.='</table></div>';
			*/
			$str.=' '.mx_icon('linkmedia',_('Links'),12,'lm_'.$media->id,'linkmediahover');
			$links='<div class="medlinks ld_'.$media->id.'">';
			foreach($media->linked as $linked) {
				$links.='<div class="medlink ld_'.$media->id.'_'.$linked->id.'">'
					.'<img src="'.mx_medialink('xx.jpg',$linked->hashcode,$media->hashdir,'-small').'"/>'
					.'<div class="dellink">'
					.mx_icon('dellink',_('Remove'),19,'ul_'.$media->id.'_'.$linked->id,'dellinkhover')
					.'</div>'
					.'</div>';
			}
			$links.='</div>';
		}
		if ($fanship && (($media->status==MXMEDIAPUBLIC && $fanship[0]<MXFAN)
		|| ($media->status>=MXMEDIAMEMBERVISIBLE && $media->status<=MXMEDIAMEMBERSHARED
			&& $fanship[0]>MXNONMEMBER && $fanship[0]<MXFAN))) {
			$mfpr='<div class="fullpreview">'._('Full-Preview').'</div>';
			$str.=$mfpr;
			$str2.=$mfpr;
		}
		$str.='<meta itemprop="'.($proptype=='tracks'?'byArtist':'author').'" content="'.$media->artistname.'" />';
		if ($media->year) $str.='<div class="mediarelease">('
			.($media->month?(mx_monthname($media->month).'-'):'').'<span itemprop="copyrightYear">'.$media->year.'</span>)</div>';
		if ($fanship==null || $fanship[0]==MXME) $str.=' <div class="mediastatus ms_'.$media->status.'">'.$mediastatuses[$media->status].'</div>';
		if ($media->description || $mystuff || $bmaker) {
			$minf=' '.mx_icon('infomedia',_('Info'),12,'im_'.$media->id,'infomedia_down');
			$str.=$minf;
			$str2.=$minf;
		}
		if (!is_logged() && !$mystuff & !$bmaker && ($media->status>MXMEDIAREADY && $media->status<MXMEDIAARCHIVED)) {
			$sharebutton=mx_sharebuttons('m'.$media->id,$url,$media->pic,$media->description);
			$media->share=$sharebutton;
			$str.='<div class="mediashare">'.$sharebutton.'</div>';
		} else {
			$media->share='';
		}
		if ($mystuff || $bmaker) {
			$desc=array(1,'','mediadesc',4,'',_('Media Description'),20);
			$strdesc=mx_formfield('desc_'.$media->id,$media->description,$desc);
		} else
			$strdesc=$media->description;
		$mdsc='<div class="meddesc md_'.$media->id.'" itemprop="description">'.$strdesc.'</div>';
		$str.=$mdsc;
		$str2.=$mdsc;
		$str.=$links;
		$str.=$media->content.$lmeds;
		$str2.=$media->content;
		$str.='</div>'; // end mediadata
		$str2.='</div>';
		$media->mediadatalight=$str2;
	}
	return $str;
}

function mx_mediabutton($name,$alt='',$height='',$prefix,$id='',$hover='',$mystuff=false) {
	$idstr=($id)?(' id="'.$prefix.$id.'"'):'';
	$str='<img'.$idstr.' src="'.mx_iconurl($name,$id).'"'.
		($height?(' height="'.$height.'"'):'') .
		($alt?(' alt="'.$alt.'"'):'');
	switch($prefix) {
		case 'pm_':
			$func='play';
			$str.=' class="button play"';
			break;
		case 'pa_':
			$func='pause';
			$str.=' class="button pause"';
			break;
		case 'dm_':
			$func='download';
			$str.=' class="button download"';
			break;
		case 'am_':
			$func='add';
			$str.=' class="button add"';
			break;
		case 'ob_':
			$func='openbundle';
			$str.=' class="button openbundle"';
			break;
		case 'cb_':
			$func='closebundle';
			$str.=' class="button closebundle"';
			break;
		case 'wb_':
			$func='';
			$str.=' class="button waitbundle"';
			break;
		case 'oa_':
			$func='openbundles';
			$str.=' class="button"';
			break;
		case 'ca_':
			$func='closebundles';
			$str.=' class="button"';
			break;
		case 'wa_':
			$func='';
			$str.=' class="button waitbundle"';
			break;
		case 'bm_':
			$func='buy';
			$str.=' class="button buy"';
			break;
		case 'um_':
			$func='unbuy';
			$str.=' class="button buy"';
			break;
		default:
			$func='';
			$str.=' class="button"';
	}
	if ($hover) {
		$str.= ' onmouseover="this.src=\''.mx_iconurl($name.$hover,$id).'\';"';
		$str.= ' onmouseout="this.src=\''.mx_iconurl($name,$id).'\';"';
		if ($func) $str.=' onclick="'.$func.'(\''.$id.'\',0,'.($mystuff?'true':'false').');"';
		//$str.= ' onclick="javascript:iconclick(\''.$id.'\',\''.$name.'\',\''.$hover.'\');"';
	}
	$str.= '/>';
	return $str;
}

function mx_medialist(&$media,$fanship=null,$nobundlebuttons=false,$mystuff=false,$featbun=false) {
	global $mxuser,$mediacache;
	//error_log('medialist '.$media->id);
	if (array_key_exists($media->id,$mediacache)) {
		//error_log('cached!');
		$media=$mediacache[$media->id];
		return;
	}
	//error_log('not cached!');
	if ($media->type==MXMEDIABASEBUNDLE) {
		$media->select='';
	} else if (true || $media->status>=MXMEDIAREADY) $media->select='<input type="checkbox" name="selmedia[]" value="'.$media->id.'">';
	else $media->select='<input disabled type="checkbox" name="selmedia[]" value="'.$media->id.'">';
	if ($mystuff) $media->grabber=mx_icon('draghand',_('Grab Me'),24);
	else $media->grabber='';
	/*if ($mystuff) {
		$media->dragdrop=mx_icon('draghand',_('Drag'),24,'dr_'.$media->id);
	}*/
	preg_match('%[^.]+$%',$media->filename,$ext);
	$mediafile=mx_option('usersdir').'/'.$media->hashdir.'/media/'.
	$media->hashcode.'.'.$ext[0];
	$id3info=$media->id3info;
	//$fp=fopen('/tmp/id3read.log','a');
	//fputs($fp,"\n".print_r($id3info,true));
	//fclose($fp);
	$status=$media->status;
	$media->linked=$mxuser->getlinkedmedia($media->id);
	$media->buttons='';
	// purchase button if not fan/buyer
	$media->purchase='';
	//if (is_admin() || MXBETA) {
		$media->pricetag='';
		//error_log('media '.$media->id.' fanship='.print_r($fanship,true));
		if (is_array($fanship) && $fanship[0]!=MXME && $fanship[0]!=MXFAN && $fanship[0]!=MXBUYER) {
			$gotit=$gotit2=0;
			$mxuser->cart->lines=$mxuser->getcartdetails($mxuser->cart->id);
			if ($mxuser->cart->lines) {
				foreach ($mxuser->cart->lines as $line) {
					if ($line->prodtype==MXMEDSUB && $line->prodref==$media->id) {
						$gotit=1;
					} else if ($line->prodtype==MXMEDSUB && $line->prodref==$media->bundles[0]->id) {
						$gotit2=1;
					}
				}
			}
			if ($gotit) $media->purchase=mx_mediabutton('cartmediabuying',_('In cart'),'24px','um_',$media->id,'hover');
			else if ($gotit2) {
				$media->purchase=mx_icon('cartmediaincluded',_('In cart'),'24px');
				$media->price=str_replace('buyprice', 'buystrike', $media->price);
			}
			else $media->purchase=mx_mediabutton('cartmedia',_('Add to Cart'),'24px','bm_',$media->id,'hover');
			$media->purchase=sprintf('{PRICE}%s{PRICE2}%s{PRICE3}',
				$media->purchase,$media->price);
			/*
			if ($media->type==MXMEDIABASEBUNDLE || $media->type==MXMEDIAREGULARBUNDLE) $media->pricetag=_('Buy this Bundle:');
			else $media->pricetag=_('Buy this Media:');
			$media->pricetag=sprintf('<div class="pricetag">%s</div>',$media->pricetag);
			*/
		} else if ($fanship[0]==MXFAN || $fanship[0]==MXBUYER) {
			$media->purchase='<div class="fanmedia">'.mx_icon('fanlove',_('Fan'),'24').'</div>'; //sprintf('<div class="fanmedia">%s</div>',_('Fan'));
		}

		/*
		else if ($fanship[0]==MXBUYER) {
			$media->purchase=mx_icon('fanbought',_('Bought'),'24'); //sprintf('<div class="boughtmedia">%s</div>',_('Bought'));
		}
		*/
	//}
	$media->content='';
	/*if ($media->type==MXMEDIABASEBUNDLE || $media->type==MXMEDIAREGULARBUNDLE) {
		$media->buttons.=mx_mediabutton('openbundle',_('Open'),24,'ob_',$media->id,'hover')
		.mx_mediabutton('notready.gif',_('Not Ready'),24,'wb_',$media->id)
		.mx_mediabutton('closebundle',_('Close'),24,'cb_',$media->id,'hover');
	} else */
	if ($status<MXMEDIAREADY) {
		$media->buttons.=mx_mediabutton('notready.gif',_('Not Ready'),24,'xx');
	} else if (!$fanship || (($status==MXMEDIAFANVISIBLE || $status==MXMEDIAARCHIVED) && $fanship[0]>=MXFAN)
	|| (($status==MXMEDIAMEMBERVISIBLE || $status==MXMEDIAMEMBERSHARED) && $fanship[0]>=MXMEMBER)
	|| ($status==MXMEDIAPUBLIC || $status==MXMEDIAPUBLICSHARED || $status->type==MXMEDIABASEBUNDLE)
	|| ($status>=MXMEDIAFANVISIBLE && $media->type==MXMEDIAREGULARBUNDLE)) {
		//$preview=((($status==MXMEDIAFANVISIBLE || $status==MXMEDIAFANSHARED) && $fanship[0]<MXFAN)
		//	|| (($status==MXMEDIAMEMBERVISIBLE || $status==MXMEDIAMEMBERSHARED) && $fanship[0]<MXMEMBER));
		switch ($media->type) {
			case MXMEDIAPIC:
			case MXMEDIABG:
				$media->buttons.=mx_docplayerbutton(mx_medialink($media->filename,$media->hashcode,$media->hashdir),$media->id,true);
				$media->content.=mx_docplayertrack(mx_medialink(
					$media->filename, //($preview?'xxx.jpg':$media->filename), // small version always jpg
					$media->hashcode,
					$media->hashdir,
					''), //($preview?'-small':'')),
					$media->id,
					$media->title);
				//$media->buttons.=mx_icon('noplaymedia',_('NoPlay'),24,'xx');
				break;
			case MXMEDIAINSTR:
			case MXMEDIASONG:
				$mediapic=''; // by default no media pic linked
				foreach ($media->linked as $linked) { // look for a pic into linked media
					if ($linked->type==MXMEDIAPIC) { // use first pic found
						$mediapic=mx_medialink('xx.jpg',$linked->hashcode,$media->hashdir,'-small');
						break;
					}
				}
				if (!$mediapic && $media->haspic) {
					$mediapic=mx_medialink('xxx.'.$media->haspic,$media->hashcode,$media->hashdir);
				}
				$media->pic=$mediapic;
				$media->buttons.=mx_soundplayerbutton(mx_medialink($media->filename,$media->hashcode,$media->hashdir),$media->id,true);
				$media->content=mx_soundplayertrack(mx_medialink($media->filename,$media->hashcode,$media->hashdir),
					$media->id,
					$mediapic,
					mx_medialink('wave.png',$media->hashcode,$media->hashdir,'-wave'),
					$media->title);
				break;
			case MXMEDIAVIDEO:
				$media->buttons.=mx_videoplayerbutton(mx_medialink($media->filename,$media->hashcode,$media->hashdir),$media->id,true);
				$media->content=mx_videoplayertrack(mx_medialink($media->filename,$media->hashcode,$media->hashdir),
					$media->id,
					$media->id3info,
					false,
					$media->title);
				break;
			case MXMEDIABASEBUNDLE:
			case MXMEDIAREGULARBUNDLE:
				if (!$nobundlebuttons && $mystuff) {
					$media->buttons.=mx_mediabutton('openbundle',_('Open'),24,'ob_',$media->id,'hover',$mystuff)
					.mx_mediabutton('notready.gif',_('Not Ready'),24,'wb_',$media->id)
					.mx_mediabutton('closebundle',_('Close'),24,'cb_',$media->id,'hover',$mystuff);
				}
				break;
			default:
				$media->buttons.=mx_mediabutton('noplaymedia',_('NoPlay'),24,'xx');
		}
		if ((!$fanship || $fanship[0]==MXFAN || $fanship[0]==MXBUYER) && $media->type!=MXMEDIABASEBUNDLE && $media->type!=MXMEDIAREGULARBUNDLE)
		{
			$media->buttons.=mx_mediabutton('golddownmedia',_('Download'),24,'dm_',$media->id,'hover');
			$media->content.=mx_downloadlink(
			mx_medialink($media->filename, $media->hashcode,$media->hashdir,'',true), $media->id);
		} //else $media->buttons.=mx_mediabutton('nodownmedia',_('NoDownload'),24,'xx');
		//$media->buttons.=mx_mediabutton('addmedia',_('Add'),24,'am_',$media->id,'hover');
	} else if ($media->status>=MXMEDIAFANVISIBLE) {
		switch ($media->type) {
			case MXMEDIABASEBUNDLE:
			case MXMEDIAREGULARBUNDLE:
				if (!$nobundlebuttons && $mystuff) {
					$media->buttons.=mx_mediabutton('nobundle',_('Restricted'),24,'xx');
				}
				break;
			case MXMEDIAINSTR:
			case MXMEDIASONG:
				//$media->title=sprintf(_('%s [extract]'),$media->title);
				if ($media->preview) {
					$mediapic=''; // by default no media pic linked
					foreach ($media->linked as $linked) { // look for a pic into linked media
						if ($linked->type==MXMEDIAPIC) { // use first pic found
							$mediapic=mx_medialink('xx.jpg',$linked->hashcode,$media->hashdir,'-small');
							break;
						}
					}
					if (!$mediapic && $media->haspic) {
						$mediapic=mx_medialink('xxx.'.$media->haspic,$media->hashcode,$media->hashdir);
					}
					$media->pic=$mediapic;
					$media->buttons.=mx_soundplayerbutton(mx_medialink($media->filename,$media->hashcode,$media->hashdir),$media->id);
					$media->content=mx_soundplayertrack(mx_medialink($media->filename,$media->hashcode,$media->hashdir,'-preview'),
						$media->id,
						$mediapic, //mx_option('templateURL').'/icons/musxpand-preview.png',
						'',
						sprintf(_('%s [extract]'),$media->title));
				} else {
					$media->buttons.=mx_mediabutton('noplaymedia',_('NoPlay'),24,'xx');
				}
				break;
			case MXMEDIAPIC:
			case MXMEDIABG:
				//$media->title=sprintf(_('%s [preview]'),$media->title);
				if ($media->preview) {
					$media->buttons.=mx_docplayerbutton(mx_medialink($media->filename,$media->hashcode,$media->hashdir),$media->id);
					$media->content.=mx_docplayertrack(mx_medialink('xx.jpg',$media->hashcode,$media->hashdir,
						'-small'),
						$media->id,
						sprintf(_('%s [preview]'),$media->title));
				}
				else $media->buttons.=mx_icon('noplaymedia',_('NoPlay'),24,'xx');
				break;
			default:
				$media->buttons.=mx_mediabutton('noplaymedia',_('NoPlay'),24,'xx');
		}
		//$media->buttons.=mx_mediabutton('nodownmedia',_('NoDownload'),24,'xx');
		//$media->buttons.=mx_mediabutton('noaddmedia',_('NoAdd'),24,'xx');
	} else {
		//$media->title=sprintf(_('%s [restricted]'),$media->title);
	}
	//$media->buttons='<div id="player">'.$media->buttons.'</div>'; // player
	$media->infobtn=$media->id;
	$media->meddesc=$media->title;
	if ($media->description) {
		$media->meddesc.=' '.mx_icon('infomedia',_('Info'),12,'im_'.$media->id,'infomedia_down')
		.'<div class="meddesc md_'.$media->id.'">'.preg_replace('%\n%','<br/>',htmlspecialchars($media->description)).'</div>';
	}
	if (!$fanship && count($media->linked)>0) {
		/*
		$links=' '.mx_icon('linkmedia',_('Links'),12,'lm_'.$media->id,'linkmediahover')
		.'<div class="medlinks ld_'.$media->id.'"><table>';
		$l=0;
		foreach($media->linked as $linked) {
			if ($l%3==0) $links.='<tr>';
			$links.='<td><img src="'.mx_medialink('xx.jpg',$linked->hashcode,$media->hashdir,'-small')
				.'"/></td>';
			if ($l % 3 == 2) $links.='</tr>';
			$l++;
		}
		if ($l%3>0) $links.='<td colspan="'.(3-($l%3)).'"></td></tr>';
		$links.='</table></div>';
		*/
		$links=' '.mx_icon('linkmedia',_('Links'),12,'lm_'.$media->id,'linkmediahover')
		.'<div class="medlinks ld_'.$media->id.'">';
		foreach($media->linked as $linked) {
			$links.='<div class="medlink ld_'.$media->id.'_'.$linked->id.'">'
				.'<img src="'.mx_medialink('xx.jpg',$linked->hashcode,$media->hashdir,'-small').'"/>'
				.'<div class="dellink">'
				.mx_icon('dellink',_('Remove'),19,'ul_'.$media->id.'_'.$linked->id,'dellinkhover')
				.'</div>'
				.'</div>';
		}
		$links.='</div>';
		$media->meddesc.=$links;
	}
	$media->meddesc.=$media->content;
	switch ($id3info['fileformat']) {
		case 'mp3':
		case 'mp4':
			$media->info=$id3info['playtime_string'];
			$media->duration=round($id3info['playtime_seconds']);
			break;
		case 'png':
		case 'jpg':
		case 'gif':
			$x=$id3info['video']['resolution_x'];
			$y=$id3info['video']['resolution_y'];
			$media->info=$x.' x '.$y;
			break;
		default:
			$media->info='';
			break;
	}
	$media->mediadata=mx_mediadata($media,$fanship,$mystuff,$nobundlebuttons,$featbun);
	$mediacache[$media->id]=$media;
}

function mx_medialine($media) {
	$allmedia=array();
	$medialist=array(
		'medialist',0,_('Media List'),
		'',
		array(),
		array(
			'new' => array(
				'new' => array(-1,_('New Media'),''),
				'select' => array(0,'<input id="checkallbox" type="checkbox" onclick="javascript:checkall(\'new\');">','html',3),
				'mediadata' => array(0,_('Media'),'html'),
				/*
				'buttons' => array(0,'','text'),
				//'infobtn' => array(0,_('Actions'),'mediainfo'),
				'meddesc' => array(0,_('Title').mx_orderlink('title'),'text'),
				//'status' => array(0,_('Access').mx_orderlink('status'),'mediastatus'),
				'type'  => array(0,_('Type').mx_orderlink('type'),'mediatype'),
				'info' => array(0,_('Info'),'text'),
				'filesize' => array(0,_('Size'),'size'),
				//'title' => array(0,_('Title').mx_orderlink('title'),'text'),
				//'description' => array(0,_('Description'),'text'),
				//'type'  => array(0,_('Type').mx_orderlink('type'),'filetype'),
				//'completion'  => array(0,_('State').mx_orderlink('completion'),'completion'),
				//'test' => array(-1,_('xxx'),_('Below is the list of all media you uploaded recently' .
				//		' into your account.')),
				//'a' => array(1,'none','hidden'),
					//'more'  => array(1,_('Info'),'text',10)
				*/
			)
		)
	);
	mx_medialist($media); // prepare media fields
	if ($media->status>=MXMEDIAVALIDATED && $media->status<MXMEDIAFANVISIBLE) $allmedia['new'][]=$media;
	//error_log('before showtable');
	return mx_showtablestr($medialist[5],$allmedia,'media',$medialist[4],'new',true); // generate table row
}

function mx_xmlmedia($mediaid) {
	global $mxuser,$mxdb;
	$status=$mxuser->getmediastatus($mediaid);
	return $status;
}

function mx_xmlbundle($bundleid,$section,$orderkey,$protect=true) {
	global $mxdb,$mxuser;
	$allmedia=array();
	if ($orderkey) $listorder=$orderkey.' asc';
	else $listorder='type asc, title asc';
	$artistid=mx_getartistidfrombundle($bundleid);
	$bundleinfo=$mxuser->getmediainfo($bundleid);
	$bundle=$mxuser->listmediafrombundle($bundleid,$listorder,$artistid);
	foreach ($bundle as $media) {
		$media=$mxuser->getmediainfo($media->id);
		$media->bundletitle=$bundleinfo->title;
		/*if ($artistid==$mxuser->id) {
			$fanship=null;
		}
		else {
		*/
		$fanship=$mxuser->getfanship($artistid,$media->id);
		//}
		error_log('media '.$media->id.' fanship='.print_r($fanship,true));
		mx_medialist($media,$fanship,false,($section!='media' && $section!='pubmed'));
		//error_log($media);
		if ($media->status>=MXMEDIAVALIDATED || $media->status<MXMEDIAFANVISIBLE) {
			$allmedia['new'][]=$media;
			$allmedia['newbun'][]=$media;
		}

		if ($media->status==MXMEDIAARCHIVED) $allmedia['archived'][]=$media;
		if ($media->status>=MXMEDIAFANVISIBLE && $media->status<=MXMEDIAPUBLICSHARED) $allmedia['published'][]=$media;
		if ($media->status==MXMEDIAVIRTUAL) {
			$allmedia['new'][]=$media;
			$allmedia['newbun'][]=$media;
			$allmedia['published'][]=$media;
			$allmedia['archived'][]=$media;
		}
		$allmedia['allmedia'][]=$media;
		if ($media->status>=MXMEDIAFANVISIBLE && $media->status<MXMEDIASUSPENDED) $allmedia['media'][]=$media;
		if ($media->status==MXMEDIAFANVISIBLE || $media->status==MXMEDIAFANSHARED) $allmedia['fanmed'][]=$media;
		if ($media->status==MXMEDIAMEMBERVISIBLE || $media->status==MXMEDIAMEMBERSHARED) $allmedia['membmed'][]=$media;
		if ($media->status==MXMEDIAPUBLIC || $media->status==MXMEDIAPUBLICSHARED) $allmedia['pubmed'][]=$media;
	}
	$tablefmt=array();
	$tablefmt[$section]=array(-1,_('New Media'),'');

	$tablefmt['select']=(($section=='media' || $section=='pubmed' || $section=='membmed' || $section=='fanmed' || $section=='newbun')
			?'':array(0,'<input id="checkallbox" type="checkbox" onclick="javascript:checkall(\''.$section.'\');">','html',3));

	if ($section!='media' && $section!='pubmed') {
		$tablefmt['grabber']=array(0,'xxx','html',2);
		$tablefmt['dragpic']=array(0,'xxx','dragdroppic',1);
	}
	$tablefmt['mediadata']=array(0,_('Media'),'movablebundledata');
	$medialist=array(
		'medialist',0,_('Media List'),
		'',
		array(),
		array(
			$section => $tablefmt
		)
	);
	$str=mx_showtablestr($medialist[5],$allmedia,'media',$medialist[4],$section,true); // generate table row
	if (!$protect) {
		$str=str_replace('{PRICE}','<table class="buymedia"><tr><td>',$str);
		$str=str_replace('{PRICE2}','</td><td>',$str);
		$str=str_replace('{PRICE3}','</td></tr></table>',$str);
	}
	return $str;
}

function mx_docplayerbutton($link,$id,$full=false) {
	if ($full) $prf='gold';
	else $prf='';
	return mx_mediabutton($prf.'viewmedia',_('View'),'24px','pm_',$id,'hover')
		.mx_mediabutton($prf.'noviewmedia',_('Close'),'24px','pa_',$id,'hover');
}

function mx_docplayertrack($link,$id,$title='') {
	return '<a title="'.htmlentities($title).'" class="picmedia" href="'.$link.'" id="media_'.$id.'" itemprop="url"></a>';
}


function mx_soundplayerbutton($link,$id,$full=false) {
	if ($full) $prf='gold';
	else $prf='';
	return mx_mediabutton($prf.'playmedia',_('Play'),'24px','pm_',$id,'hover')
		.mx_mediabutton($prf.'pausemedia',_('Pause'),'24px','pa_',$id,'hover');

}

function mx_soundplayertrack($link,$id,$picext='',$picwave='',$title='') {
	$str='<div itemprop="audio" itemscope itemtype="http://www.schema.org/AudioObject">'
	.'<a title="'.htmlentities($title).'" class="audiomedia" href="'.$link.'" id="media_'.$id.'"'.($picext?(' tag="'.$picext.'"'):'')
	.' itemprop="url"></a><meta itemprop="name" content="'.htmlentities($title).'"/>';
	if ($picwave) $str.='<a href="'.$picwave.'" id="wave_'.$id.'" itemprop="image"></a>';
	$str.='</div>';
	return $str;

}

function mx_downloadlink($link,$id) {
	$str='<a href="'.$link.'" id="mediadl_'.$id.'"></a>';
	return $str;
}

function mx_videoplayer($link,$id,$w,$h) {
		return '<video id="track_'.$id.'" src="'.$link.'"'.($w?(' width="'.$w.'"'):'').
			($h?(' height="'.$h.'"'):'').'></video>' .
			'<div class="status">'.
			'<a class="playbutton" href="javascript:playPause(\'track_'.$id.'\');">'.
			mx_icon('playbutton','listen',16,'i_track_'.$id).'</a>'.
			'<div id="a_track_'.$id.'" class="playstatus"></div>' .
			'<div class="playbar" id="b_track_'.$id.'">' .
			'<div class="played" id="p_track_'.$id.'"></div>'.
			'</div></div>';
}

function mx_videoplayerbutton($link,$id,$full=false) {
	if ($full) $prf='gold';
	else $prf='';
	return mx_mediabutton($prf.'playmedia',_('Play'),'24px','pm_',$id,'hover')
		.mx_mediabutton($prf.'pausemedia',_('Pause'),'24px','pa_',$id,'hover');
}

function mx_videoplayertrack($link,$id,$id3info,$embed=true,$title='') {
	if (is_array($id3info)) {
		$w=$id3info['video']['resolution_x'];
		$h=$id3info['video']['resolution_y'];
	} else {
		$w=320;
		$h=240;
	}
	return '<a title="'.htmlentities($title).'" class="videomedia" href="'.$link.'" id="media_'.$id.'"'.($picext?(' tag="'.$picext.'"'):'').' itemprop="url"></a>';
}

function mx_mnmedprof($page,$option,$action) {
	global $mxuser;
	if ($action) {
		mx_showmediapage($action);
	}
	else mx_showhtmlpage('medprof');
}

function mx_showmediapage($id) {
	global $mxuser;
	$id=preg_replace('%[^0-9]%','',$id); // filter fake media ids
	$media=$mxuser->getmediainfo($id);
	if (!$media) {
		__('Sorry, this media could not be found.');
		return;
	}
	$fanship=$mxuser->getfanship($media->owner_id,$id);
	if ($media->status==MXMEDIAREADY || ($media->type==MXMEDIABASEBUNDLE && $fanship!=null)) {
		__('This media has not been released yet');
		return;
	}
	mx_medialist($media,$fanship,true);
	foreach ($media->bundles as $bundle) {
		$bfanship=$mxuser->getfanship($bundle->owner_id,$bundle->id);
		mx_medialist($bundle,$bfanship,true);
	}
	$dbuser=$mxuser->getuserinfo($media->owner_id);
	if ($dbuser->status==MXACCTDISABLED) {
		__('Media is unavailable.');
		return;
	}
	if ($dbuser->acctype==MXACCOUNTFAN) {
		echo sprintf(_('This media is private'
		.'and belongs to this %s.'),mx_actionlink('fans','fanprof',$id));
		return;
	}
	if (!$dbuser || $dbuser->status==MXACCTDISABLED) {
		mx_optiontitle('error',_('Media unavailable.'));
		return;
	}
	mx_optionsubtitle('&rarr; '.$media->title);
	$authflds=$mxuser->getauthorizedfields($dbuser->id);
	$authgrps=$mxuser->getauthorizedgroups($authflds);
	$media->artist=$dbuser;
	//echo mx_mediadata($media,$fanship);
	$section='';
	if (!$authgrps || !$authflds) {
		__('No information available.');
		return;
	}
	$custpage='basicmediatemplate';
	mx_showcustompage($custpage,$dbuser,$media);
	if ($media->type==MXMEDIAREGULARBUNDLE) mx_fbaction('musxpand:examine?bundle='.urlencode(mx_actionurl('media','medprof',$id)));
	if ($_GET['z']) {
		?>
		<script type='text/javascript'>
		$(window).ready(function() {
			play(0);
		});
		</script>
		<?php
	}
}

function mx_showbundle($bundleid,$section,$orderkey) {
	$str=mx_xmlbundle($bundleid,$section,$orderkey);
	$str=preg_replace('%pubmed%','pubmed bundled bun_'.$bundleid,$str);
	$str=preg_replace('%input_.%','bundled',$str);
	return $str;
}
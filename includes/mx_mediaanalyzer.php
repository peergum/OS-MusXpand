<?php
/* ---
 * Project: MusXpand
 * File:    mx_audioanalyzer.php
 * Author:  phil
 * Date:    Nov 13, 2011
 * ---
    This file is part of project_name.
    Copyright � 2010-2011 by Philippe Hilger
 */

require_once 'includes/mx_init.php';

gc_enable();

$maxhandle=30; // after 30 loops we leave and wait for next crontab to start again
$mapsize=25000; // max 50000, artists count for 2 links...

function mx_uploadqueue() {
	global $s3,$sqs,$mxuser,$maxhandle;
	if ($mxuser && $mxuser->id) {
		error_log('Access Error.');
		die();
	}
	ini_set('error_log','/var/log/mxupload-error.log');
	error_log('start wait.');
	while (--$maxhandle>0) {
		$mxuser=null;
		$res=$sqs->receive_message(MXUPLOADQUEUEURL,array(
			'VisibilityTimeout' => (DEBUG?15:60),
			'MaxNumberOfMessages' => 1
		));
		if ($res->isOK()) {
			$msgcnt=0;
			if ($msg=$res->body->Message(0)) {
				$msgcnt++;
				//print_r('msg: '.$msg->Body);
				$req=unserialize($msg->Body);
				error_log(print_r($req,true));
				$ffile=$req['ffile'];
				$media=$req['media'];
				$userid=$req['userid'];
				$ffmt=$req['ffmt'];
				$rescan=$req['rescan'];
				$download=$req['download'];
				$fwave='';
				$fpreview='';
				$media->owner_id=$userid;
				if (!$userid) {
					error_log('No USERID in message...');
				} else {
					$mxuser=new MXUser($userid);
					if (!$mxuser->id) {
						error_log('Can\'t switch to user '.$userid.'. EXITING.');
						die();
					}
					error_log("I am now: ".$mxuser->getname());
					//error_log('mxuser='.print_r($mxuser,true));
					$picext='';
					$fpic='';

					if (file_exists($ffile)) {
						error_log('uploading media...');
						$ret=$mxuser->uploadmedia($media->id,$media->filename,$media->title,$media->type,
							$media->description,$media->completion,'','');
						if (!array_key_exists('error',$ret)) {
							// set to ready OR restore previous media status
							//if ($media->status<MXMEDIAREADY) $mxuser->setmediastatus($media->id,MXMEDIAREADY);
							//else $mxuser->setmediastatus($media->id, $media->status);
							@unlink($ffile);
							error_log('OK');
							$mxuser->rescanmedia($media); // send to rescan queue...
						} else {
							error_log($ret['error']);
						}
					} else {
						error_log('file '.$ffile.' missing...');
					}
				}
				$sqs->delete_message(MXUPLOADQUEUEURL, $msg->ReceiptHandle);
					$sqs->delete_message(MXUPLOADQUEUEURL, $msg->ReceiptHandle);
			}
			if (!$msgcnt) {
				sleep(15);
			}
		} else { // receive message error
			error_log($res);
			sleep(60);
		}
	} // while true
	error_log('end wait.');
}

function mx_analyzequeue() {
	global $s3,$sqs,$mxuser,$maxhandle;
	if ($mxuser && $mxuser->id) {
		error_log('Access Error.');
		die();
	}
	ini_set('error_log','/var/log/mxmedia-error.log');
	error_log('start wait.');
	while (--$maxhandle>0) {
		gc_enable();
		$mxuser=null;
		$res=$sqs->receive_message(MXMEDIAQUEUEURL,array(
			'VisibilityTimeout' => (DEBUG?15:60),
			'MaxNumberOfMessages' => 1
		));
		if ($res->isOK()) {
			$msgcnt=0;
			if ($msg=$res->body->Message(0)) {
				$msgcnt++;
				//print_r('msg: '.$msg->Body);
				$req=unserialize($msg->Body);
				error_log(print_r($req,true));
				$ffile=$req['ffile'];
				$media=$req['media'];
				$userid=$req['userid'];
				$ffmt=$req['ffmt'];
				$rescan=$req['rescan'];
				$fwave='';
				$fpreview='';
				$id3info='';
				if (!$userid) {
					error_log('No USERID in message...');
				} else {
					$mxuser=new MXUser($userid);
					if (!$mxuser->id) {
						error_log('Can\'t switch to user '.$userid.'. EXITING.');

						die();
					}
					error_log("I am now: ".$mxuser->getname());
					//error_log('mxuser='.print_r($mxuser,true));
					$picext='';
					$fpic='';
					if ($rescan && !file_exists($ffile)) { // retrieve uploaded version to re-analyse
						$ffile=mx_option('usersdir').'/tmp/'.$media->filename;
						$fhash=$media->hashcode;
						$ext=preg_replace('%^.*\.([^.]+)$%','\1',$media->filename);
						$ffmt=$ext;
						$hashdir=$mxuser->hashdir;
						$keyname='users/'.$hashdir.'/media/'.$fhash.'.'.$ext;
						error_log('loading '.$keyname);
						if (!file_exists($ffile)) {
							$s3->get_object(MXS3BUCKET, $keyname, array(
								'fileDownload' => $ffile,
							));
						}
						// get miniature if any since it may not be anymore in the mp3
						$keyname2='users/'.$hashdir.'/media/'.$fhash.'.jpg';
						if ($keyname2!=$keyname) {
							$fpic=preg_replace('%^(.*)[.][^.]+$%','\1.jpg',$ffile);
							if ($s3->if_object_exists(MXS3BUCKET, $keyname2)) {
								error_log('loading '.$keyname2);
								$s3->get_object(MXS3BUCKET, $keyname2, array(
									'fileDownload' => $fpic,
								));
							}
							if (file_exists($fpic)) {
								$img=imagecreatefromjpeg($fpic);
								if ($img) {
									$w=imagesx($img);
									$h=imagesy($img);
									if ($w>320) {
										$sc=($w/320);
										$img2=imagecreatetruecolor(320,round($h/$sc));
										imagecopyresampled($img2,$img,0,0,0,0,320,round($h/$sc),$w,$h);
										$fpic=preg_replace('%^(.*)[.][^.]+$%','\1.jpg',$ffile);
										imagejpeg($img2,$fpic);
										imagedestroy($img2);
									}
									imagedestroy($img);
								}
								$picext='jpg';
							} else $fpic='';
						}
					}
					if (file_exists($ffile)) {
						$rescode=0;
						$getID3 = new getID3();
						$id3info=$getID3->analyze($ffile);
						switch ($ffmt) {
							case 'mp3': // type MP3
								$sffile=preg_replace('%([\'"])%','\\1',$ffile); //preg_replace('%([^a-zA-Z0-9-_/.])%','\1',$ffile);
								//error_log($sffile);
								$output=array();
								//$res=exec('/usr/local/bin/id3v2 --delete-all "'.$sffile.'"',$output,$rescode);
								$res=exec('/usr/local/bin/id3v2 --remove-frame "APIC" "'.$sffile.'"',$output,$rescode);
                                                                    if (!$rescode) {
									$tags=$id3info['id3v2'];
									if (is_array($tags) && array_key_exists('PIC',$tags)) {
										$trackpic=$tags['PIC'][0]['data'];
										$picext=strtolower($tags['PIC'][0]['imagetype']);
										$fpic=preg_replace('%^(.*)[.][^.]+$%','\1.'.$picext,$ffile);
										file_put_contents($fpic,$trackpic);
										$img='';
										if ($picext=='jpg' || $picext=='jpeg') $img=imagecreatefromjpeg($fpic);
										else if ($picext=='png') $img=imagecreatefrompng($fpic);
										else if ($picext=='gif') $img=imagecreatefromgif($fpic);
										if ($img) {
											$w=imagesx($img);
											$h=imagesy($img);
											if ($w>320) {
												$sc=($w/320);
												$img2=imagecreatetruecolor(320,round($h/$sc));
												imagecopyresampled($img2,$img,0,0,0,0,320,round($h/$sc),$w,$h);
												$fpic=preg_replace('%^(.*)[.][^.]+$%','\1.jpg',$ffile);
												imagejpeg($img2,$fpic);
												$picext='jpg';
												imagedestroy($img2);
											}
											imagedestroy($img);
										}
									}
								}
								unset($id3info['id3v2']['PIC']);
								unset($id3info['comments']['picture']);
								unset($id3info['id3v2']);
								$mxuser->resetid3info($media->id, $id3info);
								//proc_nice(+15);
								error_log('generating waveform...');
								$fwave=mp3_waveform($ffile,400,50,'#ccff99','#000000');
								error_log('generating preview...');
								$fpreview=str_replace('.mp3','-preview.mp3',$sffile);
								$res=exec('/usr/local/bin/sox --norm "'.$sffile.'" --rate 48k "'.$fpreview.'" fade t 0 0:0:45 5',$output,$rescode);
								//proc_nice(-15);
								break;
							case 'png':
							case 'jpg':
							case 'jpeg':
							case 'gif':
								$picext=$ffmt;
								if ($picext=='jpg' || $picext=='jpeg') $img=imagecreatefromjpeg($ffile);
								else if ($picext=='png') $img=imagecreatefrompng($ffile);
								else if ($picext=='gif') $img=imagecreatefromgif($ffile);
								if ($img) {
									$w=imagesx($img);
									$h=imagesy($img);
									if ($w>320 || $h>320) $sc=(max(array($w,$h))/320);
									else $sc=1;
									$img2=imagecreatetruecolor(round($w/$sc),round($h/$sc));
									imagecopyresampled($img2,$img,0,0,0,0,round($w/$sc),round($h/$sc),$w,$h);
									//$img2=imagecreatetruecolor($w,$h);
									//imagecopyresampled($img2,$img,0,0,0,0,$w,$h,$w,$h);
									$fpic=preg_replace('%^(.*)[.][^.]+$%','\1-thumb.jpg',$ffile);
									//imageinterlace($img2,1);
									imagejpeg($img2,$fpic);
									$picext='jpg';
									imagedestroy($img2);
								}
								if ($img) {
									$w=imagesx($img);
									$h=imagesy($img);
									if ($w>320 || $h>320) $sc=(max(array($w,$h))/320);
									else $sc=1;
									//$img2=imagecreatetruecolor(round($w/$sc),round($h/$sc));
									//imagecopyresampled($img2,$img,0,0,0,0,round($w/$sc),round($h/$sc),$w,$h);
									$img2=imagecreatetruecolor($w,$h);
									imagecopyresampled($img2,$img,0,0,0,0,$w,$h,$w,$h);
									$fpic=preg_replace('%^(.*)[.][^.]+$%','\1-small.jpg',$ffile);
									imageinterlace($img2,1);
									imagejpeg($img2,$fpic,50);
									$picext='jpg';
									imagedestroy($img2);
								}
								if ($img) {
									imagedestroy($img);
								}
								break;
							case 'mov':
							case 'avi':
							case 'm4v':
							case 'mpeg':
							case 'real':
							case 'quicktime':
								break;
							default:
								break;
						}
						if (!$rescode) {
							error_log('uploading media...');
							$ret=$mxuser->uploadmedia($media->id,$media->filename,$media->title,$media->type,
								$media->description,$media->completion,$fpic,$fpreview);
							if (!array_key_exists('error',$ret)) {
								// set to ready OR restore previous media status
								if ($media->status<MXMEDIAREADY) $mxuser->setmediastatus($media->id,MXMEDIAREADY);
								else $mxuser->setmediastatus($media->id, $media->status);
								if (($fpreview && file_exists($fpreview))
								 || ($fpic && file_exists($fpic))) $mxuser->setmediafield($media->id,'preview',1);
								$mxuser->setmediapic($media->id,$picext);
								@unlink($ffile);
								if ($fpic) @unlink($fpic);
								if ($fwave) @unlink($fwave);
								if ($fpreview) @unlink($fpreview);
								error_log('OK');
							} else {
								error_log($ret['error']);
							}
						}
					} else {
						error_log('file '.$ffile.' missing...');
					}
				}
				$sqs->delete_message(MXMEDIAQUEUEURL, $msg->ReceiptHandle);
					$sqs->delete_message(MXMEDIAQUEUEURL, $msg->ReceiptHandle);
			}
			if (!$msgcnt) {
				sleep(15);
			}
		} else { // receive message error
			error_log($res);
			sleep(60);
		}
	} // while true
	error_log('end wait.');
}


function mx_checkpreviews() {
	global $s3,$sqs,$mxuser,$maxhandle,$mxdb;
	if ($mxuser && $mxuser->id) {
		error_log('Access Error.');
		die();
	}
	ini_set('error_log','/var/log/mxpreviews-error.log');
	$medias=$mxdb->listnopreviewmedia();
	foreach ($medias as $media) {
		error_log('Rescanning '.$media->id.' ['.$media->filename.'] requested.');
		$mxuser->rescanmedia($media);
	}
}

function mx_sitemapgen() {
	global $mxdb,$s3,$mapsize;
	$xmldoc=new XMLWriter();
	$xmlmap=new XMLWriter();
	$xmldoc->openMemory();
	$xmldoc->startDocument('1.0','UTF-8');
	$xmldoc->startElementNs(null,'sitemapindex','http://www.sitemaps.org/schemas/sitemap/0.9');

	$map=1;
	$loc=0;
	$site=0;
	$nb=0;
	// users sitemaps
	while ($users=$mxdb->getusers($site,$mapsize)) {
		$xmldoc->startElement('sitemap');
		$xmldoc->startElement('loc');
		$keyname='sitemaps/sitemap'.$map.'.txt';
		$xmldoc->text('http://www.example.com/sitemap.php?m='.$map);
		$xmldoc->endElement(); // loc
		$xmldoc->startElement('lastmod');
		$xmldoc->text(date('c'));
		$xmldoc->endElement(); // lastmod
		$xmldoc->endElement(); // sitemap
		$sitemap='';
		// add directories
		$sitemap.=mx_actionurl_prod('artists','artsdir','alpha')."\r\n";
		$sitemap.=mx_actionurl_prod('fans','fandir','alpha')."\r\n";
		foreach($users as $user) {
			if ($user->acctype==MXACCOUNTFAN)
				$sitemap.=($user->username?('http://www.example.com/f/'.$user->username):mx_actionurl_prod('fans','fanprof',$user->id))."\r\n";
			else if ($user->acctype==MXACCOUNTARTIST || $user->acctype==MXACCOUNTBAND)
				$sitemap.=($user->username?('http://www.example.com/a/'.$user->username):mx_actionurl_prod('artists','artprof',$user->id))."\r\n";
		}
		$filename='/tmp/sitemap'.$map.'.txt';
		file_put_contents($filename, $sitemap);
		$res=$s3->create_object(MXS3BUCKET,$keyname,array(
					'fileUpload' => $filename,
					'acl' => AmazonS3::ACL_PUBLIC,
				));
				@unlink($filename);
		$site+=$mapsize;
		$map++;
	}
	// media sitemaps
	$site=0;
	while ($medias=$mxdb->getmedias($site,$mapsize)) {
		$xmldoc->startElement('sitemap');
		$xmldoc->startElement('loc');
		$keyname='sitemaps/sitemap'.$map.'.txt';
		$xmldoc->text('http://www.example.com/sitemap.php?m='.$map);
		$xmldoc->endElement(); // loc
		$xmldoc->startElement('lastmod');
		$xmldoc->text(date('c'));
		$xmldoc->endElement(); // lastmod
		$xmldoc->endElement(); // sitemap
		$sitemap='';
		// add directories
		foreach($medias as $media) {
			$sitemap.=mx_actionurl_prod('media','medprof',$media->id)."\r\n";
		}
		$filename='/tmp/sitemap'.$map.'.txt';
		file_put_contents($filename, $sitemap);
		$res=$s3->create_object(MXS3BUCKET,$keyname,array(
					'fileUpload' => $filename,
					'acl' => AmazonS3::ACL_PUBLIC,
				));
				@unlink($filename);
		$site+=$mapsize;
		$map++;
	}
	$xmldoc->endElement(); // sitemap
	$xmldoc->endElement(); // sitemapindex
	$mapndx=$xmldoc->flush();
	$filename='/tmp/sitemapindex.xml';
	file_put_contents($filename,$mapndx);
	$res=$s3->create_object(MXS3BUCKET,'sitemaps/sitemapindex.xml',array(
					'fileUpload' => $filename,
					'acl' => AmazonS3::ACL_PUBLIC,
				));
	@unlink($filename);
}

function mx_showsitemap($map) {
	global $s3;
	if (!$map) {
		$filename='/tmp/sitemapindex.xml';
		$s3->get_object(MXS3BUCKET, 'sitemaps/sitemapindex.xml', array(
									'fileDownload' => $filename,
			));
		header('content-type: text/xml');
		$map=file_get_contents($filename);
		@unlink($filename);
	} else {
		$filename='/tmp/sitemap'.$map.'.txt';
		$s3->get_object(MXS3BUCKET, 'sitemaps/sitemap'.$map.'.txt', array(
									'fileDownload' => $filename,
			));
		header('content-type: text/plain');
		$map=file_get_contents($filename);
		@unlink($filename);
	}
	echo $map;
}
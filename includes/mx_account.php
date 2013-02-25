<?php
/* ---
 * Project: musxpand
 * File:    mx_account.php
 * Author:  phil
 * Date:    30/09/2010
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

global $mxuser,$me,$mxdb,$mxlocale,$mxsession;

class MXUser {
	private $picturecache=array();

	var $id; // mx id
	var $fbid=0; // fb id
	var $firstname;
	var $lastname;
	var $fullname;
	var $username;
	var $city;
	var $state;
	var $country;
	var $timezone='UTC';
	var $birthdate;
	var $picture=''; // link
	var $password='';
	var $pwdhash='';
	var $email;
	var $phone;
	var $hashdir; // home dir
	var $shortbio;
	var $longbio;
	var $acctype=0; // unknown, fan, artist...
	var $status=0; // unknown(0), confirmed(1), trustful(127)...
	var $gender=-1;
	var $website;
	var $locale;
	var $fbverified=0;
	var $island_id=0;
	var $archi_id=0;
	var $privpublic='picture,lastseen';
	var $privfriends='picture,identity,lastseen';
	var $privfans='picture,identity,artist,age,music,country,stats,lastseen';
	var $privartists='picture,age,music,city,state,country,lastseen';
	var $background_id=0;
	var $transparency=10;
	var $artistname=null;
	var $acccreation;
	var $msgnotif=0;
	var $reqnotif=0;
	var $cart=null;
	var $newmsgs=0;
	var $lastseen=0;
	var $confirmationcode=0;
	var $agreement='';
	var $PROid=0;
	var $PROmemberid='';
	var $invites;
	var $invitecode='';
	var $referrer=0;
	var $featured;
	var $sponsored=0;
	var $badges=0;
	var $genres=array();
	var $tastes=array();
	var $genreother='';
	var $browser='';
	var $modules='';
	var $mxfeatures=MXDEFFEATURES;

	function MXUser($newuser=null) {
		global $me,$mxdb,$notices,$mxsession,$mxlocale,$referrer,$browser;
		$notices=array();
		if ($browser) $this->browser=$browser->getUserAgent();
		//error_log('newuser: '.print_r($newuser,true));
		if ($newuser==-1) {
			$this->id=0;
			return $this;
		} else if ($newuser && is_array($newuser)) {
			$this->referrer=$referrer?$referrer:0;  // retrieve referrer before creating account
			foreach ($newuser as $key => $value) $this->$key=$newuser[$key];
			$this->hashdir=sha1($this->email.time());
			// generate temporary password for access without FB
			$this->pwdhash=hash('sha256',$this->password);
			// create user in DB
			$this->id=$mxdb->createuser($this);
			if ($this->id>0) {
				//mx_sendnewpassword($this);
				//error_log(print_r($this,true));
				mx_sendconfirmationcode($this);
				//-- no more necessary:  $this->checkuserdir();
				mx_setsession($this,time());
			}
			return $this;
		} else if ($newuser && $newuser>0) { // act as a particular user (audioanalyse...)
			$dbuser=$mxdb->getmxuser($newuser);
			if ($dbuser) foreach ($dbuser as $key => $value) $this->$key=$dbuser->$key;
		} else if ($mxsession) {
			$user=explode(',',$mxsession);
			if (!$user[0] || time()-$user[1]>1200) { // 20 mins timeout
				$this->id=0;
				unset($_SESSION['mxsession']);
				unset($mxsession);
				return;
			}
			$dbuser=$mxdb->getmxuser($user[0]);
			if (md5($dbuser->pwdhash.$dbuser->id.$_SERVER['REMOTE_ADDR'].$user[1].'12031968')==$user[2]
				&& $dbuser->status>=0) {
				//die(print_r($mxsession));
				foreach ($dbuser as $key => $value) $this->$key=$dbuser->$key;
			} else {
				//die(md5($dbuser->pwdhash.$dbuser->id.$_SERVER['REMOTE_ADDR'].$user[1].'12031968').' / '.print_r($mxsession,true));
				$this->id=0;
				unset($_SESSION['mxsession']);
				unset($mxsession);
				return;
			}
			// update session timeout
			mx_setsession($this,time());
			//die('mxsession='.$mxsession.' pwdhash='.$dbuser->pwdhash.' md5='.md5($dbuser->pwdhash));
		} else if ($me) {
			//error_log('facebook ok!');
			$this->referrer=$referrer?$referrer:0; // retrieve referrer before creating account
			$this->fb2mx();
			// check if user in DB
			if (($dbuser=$mxdb->getfbuser($me['id'])) && $dbuser->status>=0) {
				//error_log('account exists');
				foreach ($dbuser as $key => $value) {
					$this->$key=$dbuser->$key; //htmlspecialchars($dbuser->$key,ENT_QUOTES);
				}
			} else if (($dbuser=$mxdb->getemailuser($me['email'])) && $dbuser->status>=0) {
				//error_log('account email exists -> adding FB');
				foreach ($dbuser as $key => $value) {
					$this->$key=$dbuser->$key; //htmlspecialchars($dbuser->$key,ENT_QUOTES);
				}
				$this->setoption('fbid', $me['id']);
			} else if ($dbuser && $dbuser->status<0) {
				$this->id=0;
				unset($_SESSION['mxsession']);
				unset($mxsession);
				return $this;
			} else {
				//error_log('account non-existant');
				$this->hashdir=sha1($this->fbid.time());
				// generate temporary password for access without FB
				$this->password=mx_genpassword();
				$this->pwdhash=hash('sha256',$this->password);
				// create user in DB
				$this->id=$mxdb->createuser($this);
				if (!$this->fbverified) mx_sendconfirmationcode($this); // not a verified FB user -> send confcode to email
				mx_sendnewpassword($this);
				// save profile pictures
				//mx_sendnotice('green',_('Learn more about MusXpand...'),'main','musxpand');
				//mx_sendnotice('green',_('Also learn about MusXpace...'),'musxpace','');
			}
			//$this->checkuserdir();
			if ($this->id && !$this->picture) $this->savefbpics();
			if ($this->id>0) {
				mx_setsession($this,time());
			}
		} else {
			// not logged in
			//error_log('not logged in');
			$this->id=0;
			unset($_SESSION['mxsession']);
			unset($mxsession);
			return $this;
		}
		if ($this->id) {
			/*if (!$this->fullname) {
				$this->fullname=_('*** New User ***');
			}*/
			$mxlocale=$this->locale;
			$_SESSION['mxlocale']=$mxlocale;
			//error_log(print_r($this,true));

			/*if ($this->status==MXACCTDISABLED)
				mx_sendnotice('red',_('Re-enable your account.'),'account','register','sendagain');*/

			if ($this->status==MXACCTUNCONFIRMED)
				mx_sendnotice('red',_('Confirm your email.'),'account','confirm');

			if (!$this->acctype && $this->status>MXACCTUNDEFINED && $this->status<MXACCTSETUP)
				mx_sendnotice('red',_('Set up your account.'),'account','setup');

			//if (!$this->island_id || !$this->archi_id)
			//	mx_sendnotice('yellow',_('Choose Your Island...'),'account','mymusxp');
			$tmpfiles=$this->gettmpmedia();
			if ($tmpfiles && $this->gettmpmedia($tmpfiles))
				mx_sendnotice('yellow',_('You uploaded media that need more information'),'account','mystuff','upload');
			$this->newmsgs=$this->checknewmessages();
			$this->subs=$this->checksubs();
			$this->cart=$this->getcart();
			$this->lastseen=$this->lastseen();
			if (!$this->invitecode) {
				$this->setoption('invitecode',hash('sha1',time()));
			}
		}
		/*if (!$this->fullname) {
			$this->fullname=_('Visitor');
		}*/
		//die(print_r($mxsession));
		//if ($this->timezone) {
			if (!$this->timezone || preg_match('%^[-0-9]%',$this->timezone)) $this->timezone='UTC';
			date_default_timezone_set($this->timezone);
		//}
		//error_log('TZ:'.$this->timezone);
		return $this;
	}

	function getmediastatus($mediaid) {
		global $mxdb;
		return $mxdb->getmediastatus($this->id,$mediaid);
	}

	function getmediainfo($mediaid) {
		global $mxdb;
		return $mxdb->getmediainfo($this->id,$mediaid);
	}

	function getplaystats() {
		global $mxdb;
		return $mxdb->getplaystats($this->id);
	}

	function createband($artistname,$email,$pwd,$pwd2) {
		global $mxdb;
		$pwdhash=hash('sha256',$pwd);
		$pwdok=0;
		if ($pwd==$pwd2) $pwdok=1;
		return ($mxdb->createband($this->id,$artistname,$email,$pwdhash,$pwdok));
	}

	function getlinkedids() {
		global $mxdb;
		return ($mxdb->getlinkedids($this->id));
	}

	function unlinkid($acc_id) {
		global $mxdb;
		$mxdb->unlinkids($this->id,$acc_id);
	}

	function linkedidroles($acc_id,$role,$role2,$role3) {
		global $mxdb;
		$mxdb->linkedidsroles($this->id,$acc_id,$role,$role2,$role3);
	}

	function checklogin($login,$pwd) {
		global $mxdb;
		if (($dbuser=$mxdb->checklogin($login,$pwd)) && $dbuser->status>=0) {
			foreach ($dbuser as $key => $value) $this->$key=$dbuser->$key;
			return true;
		}
		return false;
	}

	function checkapplogin($login,$mix) {
		global $mxdb;
		if (($dbuser=$mxdb->checkapplogin($login,$mix)) && $dbuser->status>=0) {
			foreach ($dbuser as $key => $value) $this->$key=$dbuser->$key;
			return true;
		}
		return false;
	}

	function checkconfirm($code) {
		global $mxdb;
		if (($dbuser=$mxdb->checkconfirm($code)) && $dbuser->status>=0) {
			foreach ($dbuser as $key => $value) $this->$key=$dbuser->$key;
			return true;
		}
		return false;
	}

	function checknewmessages() {
		global $mxdb;
		return $mxdb->checknewmessages($this->id);
	}

	function checksubs() { // check for subscriptions
 		global $mxdb;
		return $mxdb->checksubs($this->id);
	}


	function savefbpics() {
		$this->setoption('picture','fb');
	}

	function savefbpics_async() {
		global $me,$mxdb,$s3;
		mx_checkfblogin(false);
		$fmt=array('large','small','square');
		//$userdir=mx_option('usersdir').'/'.$this->hashdir;
		ini_set('allow_url_fopen',1);
		if ($me) {
			$ok=0;
			foreach ($fmt as $value) {
				if (@copy('http://graph.facebook.com/'.$this->fbid.'/picture?type='.$value,
					mx_option('usersdir').'/tmp/'.$this->hashdir.'_'.$value.'.jpg')) {
					$ok+=1;
				}
			}
			if ($ok==count($fmt)) {
				$pic=@imagecreatefromjpeg(mx_option('usersdir').'/tmp/'.$this->hashdir.'_large.jpg');
				if ($pic) {
					$w=imagesx($pic);
					$h=imagesy($pic);
					$nhl=round($h*150/$w);
					//$nhs=round($h*50/$w);
					//$mwh=min($w,$h);
					$imlarge=imagecreatetruecolor(150,$h*150/$w);
					//$imsmall=imagecreatetruecolor(50,$h*50/$w);
					//$imsquare=imagecreatetruecolor(50,50);
					imageantialias($imlarge,true);
					//imageantialias($imsmall,true);
					//imageantialias($imsquare,true);
					imagecopyresampled($imlarge,$pic,0,0,0,0,150,$nhl,$w,$h);
					//imagecopyresampled($imsmall,$pic,0,0,0,0,50,$nhs,$w,$h);
					//imagecopyresampled($imsquare,$pic,0,0,
					//	($mwh==$h?($w-$mwh)/2:0),($mwh==$w?($h-$mwh)/2:0),50,50,$mwh,$mwh);
					// write logo
					//$impic=imagecreatetruecolor(150,$nhl);
					//imagecopy($impic,$imlarge,0,0,0,0,150,$nhl);
					$logo=imagecreatefrompng(mx_option('rootdir').'/images/general/musxpand-logo.png');
					$imlogo=imagerotate($logo,90,0);
					$lw=imagesx($imlogo);
					$lh=imagesy($imlogo);
					imagecopyresampled($imlarge,$imlogo,129,0,0,0,$lw/2,$lh/2,$lw,$lh);
					imagejpeg($imlarge,mx_option('usersdir').'/tmp/'.$this->hashdir.'_large.jpg');
				}
				foreach ($fmt as $value) {
					$keyname='users/'.$this->hashdir.'/pics/me_'.$value.'.jpg';
					/*if ($s3->if_object_exists(MXS3BUCKET,$keyname)) {
						$s3->delete_object(MXS3BUCKET,$keyname);
					}*/
					$res=$s3->create_object(MXS3BUCKET,$keyname,array(
						'fileUpload' => mx_option('usersdir').'/tmp/'.$this->hashdir.'_'.$value.'.jpg',
						'acl' => AmazonS3::ACL_PUBLIC,
						//'contentType' => 'text/plain',
						//'storage' => AmazonS3::STORAGE_REDUCED,
						/*
						'headers' => array( // raw headers
							'Cache-Control' => 'max-age',
							'Content-Encoding' => 'gzip',
							'Content-Language' => 'en-US',
							'Expires' => 'Thu, 01 Dec 1994 16:00:00 GMT',
						),
						*/
						'meta' => array(
							//API Version 2006-03-01
							//129
							//Amazon Simple Storage Service Developer Guide
							//Uploading Objects in a Single Operation
							'origfilename' => 'http://graph.facebook.com/'.$this->fbid.'/picture?type='.$value
							//'param2' => 'value 2'
						)
					));
				}
				$this->setoption('picture','local');
			} else $this->setoption('picture','');
		}
		ini_set('allow_url_fopen',0);
		return $this->picture();
	}

	function checkuserdir($dirname='') { // useless function on the cloud...
		$dirlist=array('media','tmp','pics','msgs');
		if ($this->hashdir) {
			$userdir=mx_option('usersdir').'/'.$this->hashdir;
			if (!is_dir($userdir)) {
				mkdir($userdir,0755);
			}
			if ($dirname=='') {
				for($i=0; $i<count($dirlist);$i++) {
					$subdir=$dirlist[$i];
					if (!is_dir($userdir.'/'.$subdir)) {
						mkdir($userdir.'/'.$subdir,0755);
					}
				}
			}
		}
	}

	function fb2mx() {
		global $facebook,$me,$mxdb;
		if (!$me) return null;
		/*foreach ($me as $key => $value) {
			if (is_string($value)) $me[$key]=htmlspecialchars($value,ENT_QUOTES);
		}*/
		$this->fbid=$me['id'];
		$this->firstname=$me['first_name'];
		$this->lastname=$me['last_name'];
		$this->fullname=$me['name'];
		preg_match_all('%([0-9]+)%',$me['birthday'],$bdate);
		$this->birthdate=$bdate[1][2].'-'.$bdate[1][0].'-'.$bdate[1][1];
		$this->website=$me['website'];
		$this->city=preg_replace('%,.*$%','',$me['location']['name']);
		$this->state=preg_replace('%^[^,]*(, )?%','',$me['location']['name']);
		$this->country=''; //geoip_country_code_by_name($_SERVER['REMOTE_ADDR']);
		$this->shortbio=$me['about'];
		$this->longbio=$me['bio'];
		$this->gender=($me['gender']=='male'?0:1);
		$this->email=$me['email'];
		$this->timezone=($me['timezone']?$me['timezone']:'UTC');
		if (preg_match('%^[-+0-9]+$%',$this->timezone))
			$this->timezone='UTC';
		$this->locale=$me['locale'];
		$this->fbverified=$me['verified']?$me['verified']:0;
		if ($this->fbverified) $this->status=MXACCTEMAILCONFIRMED;
		return $this;
	}
	function infogroups($setup=null,$acctype=null) {
		$grp=array();
		$step=1; // used to number the steps automatically
		if (is_null($setup)) {
			$grp['internal'] = array(_('Internal (For Your Eyes Only...)'),array('id','fbid','status',
				'fbverified','acccreation','invites','lastseen'));
		}
		if (is_null($setup) || $setup==$step++) {
			$grp['account'] = array(_('Account Type'),array('acctype'));
			$grp['terms'] = array(_('Terms & Conditions'),array('agreement'));
		}
		if (is_null($setup) || $setup==$step++) {
			$grp['basic'] = array(_('Basic'),array('username','password',
				'firstname','lastname','fullname','gender','email','birthdate'
				));
			$grp['picinfo'] = array(_('Picture'),array('picture'));
			switch ($acctype) {
				case MXACCOUNTFAN:
					break;
				case MXACCOUNTARTIST:
				case MXACCOUNTBAND:
				default:
						$grp['specific'] = array(_('Artist Information'),array('artistname','genres'));
						//$grp['promember'] = array(_('Professional References'),array('PROid','PROmemberid'));
					break;
			}
		}
		if (is_null($setup) || $setup==$step++) {
			$grp['details'] = array(_('Details'),array('shortbio','longbio','website'));
			$grp['location'] = array(_('Location'),array('city','state','country','timezone','locale'));
			$grp['musictastes'] = array(_('Musical Tastes'),array('tastes'));
		}
		if (is_null($setup) || $setup==$step++) {
			$grp['custom'] = array(_('Customization'),array('background_id','transparency'));
		}
		if (is_null($setup) || $setup==$step++) {
			$grp['privacy'] = array(_('Privacy'),array('privpublic','privfriends','privartists','privfans'));
			$grp['notifs'] = array(_('Notifications'),array('msgnotif','reqnotif'));
		}
		if (is_null($setup)) {
			//$grp['musxpace'] = array(_('MusXpace (Future Concept)'),array('archi_id','island_id'));
			//$grp['friendlist'] = array(_('Friends'),array('friends'));
		}
		return $grp;
	}

	function bandfields() {
		return array(
			'internal' => array(_('Internal (For Your Eyes Only...)'),array('id','fbid','status',
				'fbverified','acccreation','lastseen')),
			'picinfo' => array(_('Picture'),array('picture')),
			'basic' => array(_('Basic'),array('acctype','password',
				'artistname','email','birthdate'
				)),
			'location' => array(_('Location'),array('city','state','country','timezone','locale')),
			'details' => array(_('Details'),array('shortbio','longbio','website')),
			'custom' => array(_('Customization'),array('background_id','transparency')),
			//'musxpace' => array(_('MusXpace'),array('archi_id','island_id')),
			'privacy' => array(_('Privacy'),array('privpublic','privfriends','privartists','privfans')),
			'notifs' => array(_('Notifications'),array('msgnotif','reqnotif'))
		);
	}

	function getauthorizedgroups($flds) {
		global $mxdb;
		foreach ($this->infogroups() as $group => $details) {
			foreach($details[1] as $field) {
				if (in_array($field,$flds)) {
					$okgroups[$group]=$group;
					break;
				}
			}
		}
		return $okgroups;
		// test if admin
		if ($this->status==MXACCTTRUSTFUL)
			return array('basic','picture','location','details',
				'custom',/*'musxpace',*/'privacy','identity','gender','artist'
			);
		// test if friend
		// test if fan
		// test if artist
		// anyone
		$flds=$dbuser->privpublic;
		return array('location','details',/*'musxpace',*/'gender');
		// none
		return null;
	}

	function getauthorizedfields($userid,$public=false) {
		global $mxdb,$defaultprivacy;
		if ($public) $reqid=0;
		else $reqid=$this->id;
		if ($userid == $reqid) $priv['self']=1;
		else if (!$public && is_admin()) $priv['admin']=1;
		else {
			$priv=$mxdb->getprivacy($reqid,$userid);
		}
		//die(print_r($priv,true));
		// next fields are ALWAYS available (public)
		$flds=$defaultprivacy;
		if (!$priv) return $flds;
		foreach ($priv as $cat => $dummy) {
			switch ($cat) {
				case 'admin':
				case 'self':
					array_push($flds,'id','fbid','status',
					'fbverified','acccreation',
					'picture','acctype','username','password',
					'firstname','lastname','fullname','artistname','gender','email','birthdate','age','birthday',
					'city','state','country','timezone','locale',
					'friends',
					'shortbio','longbio','website',
					'background_id','transparency',
					/*'archi_id','island_id',*/
					'msgnotif','reqnotif',
					'mediacnt','mediasize','pubcnt','pubsize','subcnt','subfoy','subfofa','sublike',
					'privpublic','privfriends','privartists','privfans',
					'lastseen');
					break;
				case 'lastseen':
					$flds[]='lastseen';
					break;
				/*case 'musxpace':
					array_push($flds,'archi_id','island_id');
					break;*/
				case 'stats':
					array_push($flds,'mediacnt','pubcnt','mediasize','pubsize','subcnt','subfoy','subfofa','sublike');
					break;
				case 'gender':
					$flds[]='gender';
					break;
				case 'identity':
					array_push($flds,'firstname','lastname','fullname');
					break;
				case 'artist':
					array_push($flds,'artistname');
					break;
				case 'bio':
					array_push($flds,'shortbio','longbio');
					break;
				case 'picture':
					$flds[]='picture';
					break;
				case 'website':
					$flds[]='website';
					break;
				case 'age':
					$flds[]='age';
					//if (array_key_exists('birthday',$priv)) $flds[]='birthdate';
					break;
				case 'birthday':
					$flds[]='birthday';
					//if (array_key_exists('age',$priv)) $flds[]='birthdate';
					break;
				case 'birthdate':
					array_push($flds,'age','birthday','birthdate');
					break;
				case 'friends':
					$flds[]='friends';
					break;
				case 'tastes':
					$flds[]='tastes';
					break;
					case 'notifs':
					array_push($flds,'msgnotif','reqnotif');
					break;
				case '':
					break;
				default:
					$flds[]=$cat;
			}
		}
		//echo "flds:".print_r($flds);
		return $flds;
	}

	function fielddesc($fld,$band=false) {
		/* code:
		 * 0: display only
		 * 1: edit all users
		 * 2: edit artists only
		 * 3: edit all users (mandatory)
		 * 4: edit artists only (mandatory)
		 */

		$nobandedit=$band?0:1;
		$flddesc = array(
		'id' => array(0,_('MusXpand ID'),'integer'),
		'fbid' => array(0,_('Facebook ID'),'integer'),
		'status' => array(0,_('Status'),'status'),
		'fbverified' => array(0,_('Facebook Verified'),'boolean'),
		'acccreation' => array(0,_('Account Creation'),'timestamp'),
		'invites' => array(0,_('Referrees'),'invites'),
		'pwdhash' => array(0,_('Password Hash'),'text',64),
		'hashdir' => array(0,_('Home Directory'),'integer'),
		'acctype' => array($nobandedit*3,_('Account Type'),'acctype',null,
			_('This is important to us, at musxpand, to better serve you.<br/><b>CANNOT BE CHANGED BY USER</b><br/>'
					.' Use the appropriate account to get the most benefits.')),
		'PROid' => array(2,_('Professional Artist Association'),'proid',null,
			_('A professional association you\'re a member of.')),
		'PROmemberid' => array(2,_('Member ID'),'text',null,
			_('Your Member ID in the association.')),
		'agreement' => array(3,_('Agreement'),'agreement',
			sprintf(_('I have read and I agree to be bound by MusXpand\'s %s.'),mx_windowedpage('terms',_('Terms & Conditions'))),_('You have to agree to continue...')),
		'username' => array(3,_('Username'),'username',20,_('A name you can use to connect'
					.' (lowercase letters and/or digits only, with no spaces and at least one letter in it)')),
		'password' => array(1,_('Password'),'password',64,_('We recommend you to use' .
				' at least 8 characters including digits and special characters.' .
				' Don\'t use trivial passwords...')),
		'firstname' => array(1,_('First Name'),'legalname',30,
			_('Your REAL first name or a LEGAL artistic first name.<br/><b>CANNOT BE CHANGED</b><br/><u>Fake accounts will be deleted when discovered</u>')),
		'lastname' => array(1,_('Last Name'),'legalname',30,
			_('Your REAL family name or a LEGAL artistic last name.<br/><b>CANNOT BE CHANGED</b><br/><u>Fake accounts will be deleted when discovered</u>')),
		'fullname' => array(3,_('Pseudonyme/Nickname'),'fullname',60,
			_('The common way you write your name in full, or a nickname if you prefer.')),
		'gender' => array(1,_('Gender'),'gender',null,
			_('Well, you\'re not compelled to say, but everyone will want to know...')),
		'email' => array(0,_('Email'),'text',64,
			_('You will have to confirm your email and it can be used to connect to musxpand')),
		'birthdate' => array(1,_('Birthdate'),'date',_('So that people can celebrate with you :)')),
		'city' => array(1,_('City'),'text',30,_('The city you currently live in')),
		'state' => array(1,_('State'),'text',20),
		'country' => array(1,_('Country'),'text',2,_('A two letters code for your country')),
		'timezone' => array(1,_('Time Zone'),'timezone',30,
			_('Your local timezone')),
		'locale' => array(1,_('Locale'),'locale',null,
			_('What language do you want to read musxpand in?')),
		'shortbio' => array(1,_('About'),'memo',4,
			_('A short description of yourself'),_('How would you summarize your profile?'),58),
		'longbio' => array(1,_('Long Bio'),'memo',10,
			_('Tell others your story, interests and whatever you want to share about you...'),
			_('Feel free to use link to other pages of yours...'),58),
		'website' => array(1,_('Website(s)'),'url',100,
			_('Have you got a personal site?')),
		//'role' => array(0,_('Role'),'role'),
		//'island_id' => array(0,_('Island'),'island',null,_('Your current island')),
		//'archi_id' => array(0,_('Archipelago'),'archipelago',null,_('Your current archipelago')),
		'privpublic' => array(1,_('Share with anyone'),'privacy',null,
			_('What can anyone see from your data?')),
		'privfriends' => array(1,_('Share with friends'),'privacy',null,
			_('What can your friends see from your data?')),
		'privartists' => array(1,_('Share with artists'),'privacy',null,
			_('What can your favorite artists see from your data?')),
		'privfans' => array(2,_('Share with fans'),'privacy',null,
			_('What can your fans see from your data?')),
		'background_id' => array(1,_('Background'),'background',null,
			_('A browser background picture while you use musxpand')),
		'transparency' => array(1,_('Transparency'),'transparency',null,
			_('The level of transparency under the text on musxpand')),
		'picture' => array(1,_('Picture'),'picture',null,
			_('Your profile picture should be square with at least 200x200 pixels')),
		'artistname' => array(4,($band?_('Band Name'):_('Artist Name')),'text',null,
			sprintf(_('When creating your %s, write it the way you want it to appear'),
			($band?_('band name'):_('artist name')))),
		'friends' => array(1,_('Some of your friends'),'friends',null,
			_('Who are your friends?')),
		'msgnotif' => array(1,_('New Messages Notifications'),'notif',null,
			_('New messages notifications')),
		'reqnotif' => array(1,_('New Requests Notifications'),'notif',null,
			_('New requests notifications')),
		'lastseen' => array(0,_('Last Seen'),'date',_('That\'s the last time you were on MusXpand...')),
		'genres' => array(1,_('Musical Style'),'genre',null,_('Informing will help people find you and be presented to new potential fans')),
		'tastes' => array(1,_('Musical Tastes'),'genre',_('Informing this will help you make friends with the same tastes and receive artists suggestions')),
			);
		return ((array_key_exists($fld,$flddesc))?$flddesc[$fld]:null);
	}

	function setpicture($fname) {
		global $s3,$sqs;
		$keyname='users/'.$this->hashdir.'/media/'.$fname;
		if ($s3->if_object_exists(MXS3BUCKET,$keyname)) {
			ini_set('allow_url_fopen',1);
			$ffname=gets3url($keyname,'2 minutes');
			$ext=pathinfo(preg_replace('%[?].*%','',$ffname),PATHINFO_EXTENSION);
			//error_log('ffname='.$ffname.' ext='.$ext);
			if ($ext=='png') $pic=imagecreatefrompng($ffname);
			else if ($ext=='gif') $pic=imagecreatefromgif($ffname);
			else $pic=imagecreatefromjpeg($ffname);
			//error_log('pic='.print_r($pic,true));
			$w=imagesx($pic);
			$h=imagesy($pic);
			$nhl=round($h*150/$w);
			$nhs=round($h*50/$w);
			$mwh=min($w,$h);
			$imlarge=imagecreatetruecolor(150,$h*150/$w);
			$imsmall=imagecreatetruecolor(50,$h*50/$w);
			$imsquare=imagecreatetruecolor(50,50);
			imageantialias($imlarge,true);
			imageantialias($imsmall,true);
			imageantialias($imsquare,true);
			imagecopyresampled($imlarge,$pic,0,0,0,0,150,$nhl,$w,$h);
			imagecopyresampled($imsmall,$pic,0,0,0,0,50,$nhs,$w,$h);
			imagecopyresampled($imsquare,$pic,0,0,
				($mwh==$h?($w-$mwh)/2:0),($mwh==$w?($h-$mwh)/2:0),50,50,$mwh,$mwh);
			// write logo
			//$impic=imagecreatetruecolor(150,$nhl);
			//imagecopy($impic,$imlarge,0,0,0,0,150,$nhl);
			$logo=imagecreatefrompng(mx_option('rootdir').'/images/general/musxpand-logo.png');
			$imlogo=imagerotate($logo,90,0);
			$lw=imagesx($imlogo);
			$lh=imagesy($imlogo);
			imagecopyresampled($imlarge,$imlogo,129,0,0,0,$lw/2,$lh/2,$lw,$lh);
			imagejpeg($imlarge,mx_option('usersdir').'/tmp/'.$this->hashdir.'_large.jpg');
			imagejpeg($imsquare,mx_option('usersdir').'/tmp/'.$this->hashdir.'_square.jpg');
			imagejpeg($imsmall,mx_option('usersdir').'/tmp/'.$this->hashdir.'_small.jpg');
			$fmt=array('large','small','square');
			foreach ($fmt as $value) {
				$keyname='users/'.$this->hashdir.'/pics/me_'.$value.'.jpg';
				$res=$s3->create_object(MXS3BUCKET,$keyname,array(
					'fileUpload' => mx_option('usersdir').'/tmp/'.$this->hashdir.'_'.$value.'.jpg',
					'acl' => AmazonS3::ACL_PUBLIC,
					'meta' => array(
						'origfilename' => 'http://graph.facebook.com/'.$this->fbid.'/picture?type='.$value
					)
				));
				@unlink(mx_option('usersdir').'/tmp/'.$this->hashdir.'_'.$value.'.jpg');
			}
			$this->setoption('picture','local');
			ini_set('allow_url_fopen',0);
			return true;
		}
		return false;
	}

	function pwdreset() {
		$this->password=mx_genpassword();
		$this->pwdhash=hash('sha256',$this->password);
		$this->setoption('pwdhash',$this->pwdhash);
		mx_sendnewpassword($this);
	}

	function picture($size='large') {
		$str='<img title="'.$this->getname().' - '._('See Your Page').'" tag="'.$this->id.'" class="profilepic pictooltip" style="cursor:pointer;" src="'.mx_fanpic($this->id,$size,$this->gender,
			is_artist()).'"'
		.($this->id?('onclick="window.location=\''.mx_pageurl('account').'\';"'):MXICONCLICK).'>';
		if ($this->picture=='fb') return '<div class="userpic">'
		.'<div class="pending">'
		.mx_icon('working.gif','updating','16px').'</div>'.$str
		.'<script>$(window).load(function() {savefbpics();});</script>'
		.'</div>';
		return '<div class="userpic"'.
		(($this->id && $this->status>=MXACCTSETUP)?(' onmouseover="showbutton(\'profilepic\');"'
		.' onmouseout="hidebutton(\'profilepic\');"'):'').'>'
		.'<div id="profilepic" class="picbutton" onclick="window.location=\''.mx_actionurl('account','profile','edit','picinfo').'\';">'
		._('Update Pic').'</div>'.$str.'</div>';
	}

	function setoption($name,$value) {
		global $mxdb;
		if ($name=='picture') {
			switch ($value) {
				case 'local':
				case '':
					break;
				case 'fb':
					//$this->savefbpics();
					//return;
					break;
				default:
					$this->setpicture($value);
					return;
					break;
			}
		}
		if ($name=='username' || $name=='email') $value=strtolower($value);
		$this->$name=$value;
		$mxdb->updateuser($this,$name);
	}

	function getoption($name) {
		return $this->$name;
	}

	function rescanmedia($media) {
		global $mxdb,$sqs;
		$id3info=$media->id3info;
		unset($media->id3info);
		$mediamsg=array(
			'media' => $media,
			'ffile' => '',
			'userid' => $media->owner_id,
			'ffmt' => $id3info['fileformat'],
			'rescan' => true,
		);
		$res=$sqs->send_message(MXMEDIAQUEUEURL,serialize($mediamsg));
	}

	function addmedia($fname, $fsize, $status,$title='',$ftype=MXMEDIAUNDEFINED,$description='',$comp=MXMEDIANOSTATUS) {
		global $mxdb,$sqs,$s3;
		// fname: filename
		// tmpfile: true if file is in user's /tmp folder
		//$ffile=mx_option('usersdir').'/'.$this->hashdir.'/tmp/'.$fname;
		$ffile=mx_option('usersdir').'/tmp/'.$fname;
		//error_log('fname='.$fname.' ffile='.$ffile);
		$fhash=hash_file('md5',$ffile);
		$getID3 = new getID3();
		$fpic='';
		if (file_exists($ffile)) {
			$fwave='';
			$id3info = $getID3->analyze($ffile);
			//getid3_lib::CopyTagsToComments($id3info);
			if ($id3info['fileformat']=='mp3') {
				unset($id3info['id3v2']['PIC']);
				unset($id3info['comments']['picture']);
				unset($id3info['id3v2']);
				if ($id3info['bitrate_mode']=='cbr' && $id3info['bitrate']<320000) {
					return array('error' => _('Audio media bitrate requirement is 320Kbps @ 44KHz'));
				} else if ($id3info['bitrate_mode']=='vbr' && $id3info['bitrate']<128000) {
					return array('error' => _('Audio media bitrate requirement is 320Kbps @ 44KHz'));
				}
			}
			$fp=fopen('/tmp/id3.log','a');
			fputs($fp,"\n---\n".$ffile.":\n".'id3info: '.print_r($id3info,true));
			fclose($fp);
		} else {
			return array('error' => _('File not found!?'));
		}
		//error_log('id3='.print_r($id3info,true));
		$ret=$mxdb->addmedia($this,$fname,$fsize,$status,$id3info,$fhash);
		//error_log(serialize($id3info));
		if (array_key_exists('error',$ret)) {
			//error_log('DB->addmedia error: '.print_r($ret,true));
			return $ret;
		}
		$media=new StdClass();
		$media->filename=$fname;
		$media->filesize=$fsize;
		$media->hashcode=$fhash;
		if ($ftype==MXMEDIAUNDEFINED || !$ftype || $ftype=='') {
			switch ($id3info['fileformat']) {
				case 'mp3':
					$media->type=MXMEDIASONG;
					break;
				case 'png':
				case 'jpg':
				case 'gif':
					$media->type=MXMEDIAPIC;
					break;
				case 'mov':
				case 'avi':
				case 'm4v':
				case 'mp4':
				case 'mpeg':
				case 'real':
				case 'quicktime':
					$media->type=MXMEDIAVIDEO;
					break;
				default:
					$media->type=MXMEDIAUNDEFINED;
					break;
			}
		} else {
			$media->type=$ftype;
		}
		$media->title=$title;
		$media->description=$description;
		$media->completion=$comp;
		$media->status=$status;
		$media->id3info=$id3info;
		$media->id=$ret['mediaid'];
		$media->hashdir=$this->hashdir;
		$media->haspic='';
		$ret['line']=mx_medialine($media);
		$ret['basebundle']=$this->getbasebundle();
		$ret2=$mxdb->updatemedia($this,$media->id,$media->filename,$media->title,$media->type,
			$media->description,$media->hashcode,$media->completion,$media->status);
		if (array_key_exists('error',$ret2)) {
			//error_log('DB->addmedia error: '.print_r($ret,true));
			return $ret2;
		}
		$newret=array_merge($ret,$ret2);
		if ($status==MXMEDIAREADY) {
			$ext=strtolower('.'.pathinfo($fname,PATHINFO_EXTENSION));
			$keyname='users/'.$media->hashdir.'/media/'.$media->hashcode.$ext;
			$rep=$s3->create_object(
				MXS3BUCKET,
				$keyname,
				array(
					'fileUpload' => $ffile,
					'acl' => AmazonS3::ACL_PRIVATE,
				)
			);
			$newret['link']=$media->hashcode.$ext;
		}
		//if ($id3info['fileformat']=='mp3') {
			unset($media->id3info);
			$mediamsg=array(
				'media' => $media,
				'ffile' => $ffile,
				'userid' => $this->id,
				'ffmt' => $id3info['fileformat'],
			);
			$res=$sqs->send_message(MXUPLOADQUEUEURL,serialize($mediamsg));
			if (!$res->isOK()) error_log(print_r($res,true));
		//}
		return $newret;
	}

	function addpro($name,$site) {
		global $mxdb;
		return $mxdb->addpro($this->id,$name,$site);
	}

	function gettmpmedia($results=null) {
		global $mxdb;
		return $mxdb->gettmpmedia($this,$results);
	}

	function getfanship($artistid,$mediaid=0) {
		global $mxdb;
		return $mxdb->getfanship($this->id,$artistid,$mediaid);
	}

	function listartistmedia($artistid,$results=null,$orderkey=null) {
		global $mxdb;
		return $mxdb->listartistmedia($artistid,$this,$results,$orderkey);
	}

	function listmediafrombundle($bundleid,$orderkey=null,$scope=null) {
		global $mxdb;
		return $mxdb->listmediafrombundle($this->id,$bundleid,$orderkey,$scope);
	}

	/*
	 * subs:
	 * - null for public media
	 * - empty array for member-only media
	 * - artists array for fan-only media
	 * - artistid for all of 1 artist media
	 */
	function listmedia($subs=null,$results=null,$orderkey=null) {
		global $mxdb;
		return $mxdb->listmedia($this,$results,$orderkey,$subs);
	}

	/*
	 * List one media from given scope
	 */
	function getonemedia($scope=null) {
		global $mxdb;
		return $mxdb->getonemedia($this,$scope);
	}

	function checkbundles($artistid=null) {
		global $mxdb;
		if (!$artistid) $artistid=$this->id;
		return $mxdb->checkbundles($artistid);
	}

	function createbundle($bundlename,$status=MXMEDIAREADY) {
		global $mxdb;
		return $mxdb->createbundle($this->id,$bundlename,$status);
	}

	function listbundles($artistid,$results=null,$orderkey=null) {
		global $mxdb;
		return $mxdb->listbundles($this,$results,$orderkey,$artistid,true);
	}

	function listselectedmedia($ids) {
		global $mxdb;
		return $mxdb->listselectedmedia($this,$ids);
	}

	function listfanwalls($results=null,$orderkey=null) {
		global $mxdb;
		return $mxdb->listfanwalls($this,$results,$orderkey);
	}

	function listartwalls($results=null,$orderkey=null) {
		global $mxdb;
		return $mxdb->listartwalls($this,$results,$orderkey);
	}

	function listfrwalls($results=null,$orderkey=null) {
		global $mxdb;
		return $mxdb->listfrwalls($this,$results,$orderkey);
	}

	function listmywalls($results=null,$orderkey=null) {
		global $mxdb;
		return $mxdb->listmywalls($this->id,$results,$orderkey);
	}

	function listuserwalls($userid, $results=null,$orderkey=null) {
		global $mxdb;
		return $mxdb->listwalls($this,$userid,$results,$orderkey);
	}

	function getlikes($wallid) {
		global $mxdb;
		return $mxdb->getlikes($this->id,$wallid);
	}

	function listmessages($results=null,$orderkey=null) {
		global $mxdb;
		return $mxdb->listmessages($this,$results,$orderkey);
	}

	function publishmedia($fid,$status,$ftitle='',$ftype='',$fdesc='',$fcomp='') {
		global $mxdb;
		if ($status) $mxdb->publishmedia($this,$fid,$status);
		if ($ftitle) $this->updatemediadesc($fid,$ftitle,$ftype,$fdesc,$fcomp);
	}

	function setmediastatus($fid,$status) {
		global $mxdb;
		if ($status) $mxdb->setmediastatus($this,$fid,$status);
	}

	function resetid3info($fid,$id3info) {
		global $mxdb;
		if ($id3info) $mxdb->resetid3info($this,$fid,$id3info);
	}

	function setmediapic($fid,$picext) {
		global $mxdb;
		$mxdb->setmediapic($this,$fid,$picext);
	}

	function setmediafield($fid,$fld,$value) {
		global $mxdb;
		$mxdb->setmediafield($this,$fid,$fld,$value);
	}

	function updatemediadesc($fid,$ftitle,$ftype,$fdesc,$fcomp) {
		global $mxdb;
		$mxdb->updatemediadesc($this,$fid,$ftitle,$ftype,$fdesc,$fcomp);
	}

	function updatemediainfo($fid,$field,$text) {
		global $mxdb;
		return $mxdb->updatemediainfo($this,$fid,$field,$text);
	}

	function uploadmedia($fid,$fname,$ftitle,$ftype,$fdesc,$fcomp,$fpic='',$fpreview='') {
		global $mxdb,$s3;
		//$userdir=mx_option('usersdir').'/'.$this->hashdir;
		$ext=strtolower('.'.pathinfo($fname,PATHINFO_EXTENSION));
		$tmpfile=mx_option('usersdir').'/tmp/'.$fname;
		//preg_match('%(\.[^.]+)$%',$fname,$ext); use $ext[0]
		//if ($mxdb->checkfile($this->id,$fid,$fname)
		// && file_exists($userdir.'/tmp/'.$fname)) {
		$media=$mxdb->checkfile($this->id,$fid,$fname);
		//error_log(print_r($media,true));
		if ($media && file_exists($tmpfile)) {
 			$fhash=$media->hashcode;
 			//error_log('media='.print_r($media,true));
			$bucket=MXS3BUCKET;
			$keyname='users/'.$this->hashdir.'/media/'.$fhash.$ext;
			//error_log('checked!');
			//error_log('object test');

			/* if it exists, we overwrite it
			if ($s3->if_object_exists($bucket,$keyname)) {
				$res=array('error' => _('File already uploaded...'));
				//$res['link']=$fash.strtolower($ext);
				return $res;
			}*/

			//error_log('update media');
			//$res=$mxdb->updatemedia($this,$fid,$fname,$ftitle,$ftype,$fdesc,$fhash,$fcomp);
			//if (!array_key_exists('error',$res)) {
				//error_log('object create');
				$rep=$s3->create_object(
					$bucket,
					$keyname,
					array(
						'fileUpload' => $tmpfile,
						'acl' => AmazonS3::ACL_PRIVATE,
					)
				);
				/*if ($rep->isOK()) {
					$rep=$s3->set_object_acl(
						$bucket,
						$keyname,
						array(
							array(
								'id' => AWS_CANONICAL_ID,
								'permission' => AmazonS3::ACL_OPEN
							),
						)
					);
				}*/
				//error_log(print_r($rep,true));
				if ($rep->isOK()) {
					$fwave=preg_replace('%^(.*)\.[^.]+$%','\1-wave.png',$tmpfile);
					if (file_exists($fwave)) {
						$keynamewave='users/'.$this->hashdir.'/media/'.$fhash.'-wave.png';
						//error_log('uploading '.$keynamewave);
						$rep=$s3->create_object(
							$bucket,
							$keynamewave,
							array(
								'fileUpload' => $fwave,
								'acl' => AmazonS3::ACL_PRIVATE,
							)
						);
					}
					if ($fpreview && file_exists($fpreview)) {
						$keynamewave='users/'.$this->hashdir.'/media/'.$fhash.'-preview.mp3';
						//error_log('uploading '.$keynamewave);
						$rep=$s3->create_object(
							$bucket,
							$keynamewave,
							array(
								'fileUpload' => $fpreview,
								'acl' => AmazonS3::ACL_PRIVATE,
							)
						);
					}
					if ($fpic) { // there was an embedded pic save too
						$ext2='.'.pathinfo($fpic,PATHINFO_EXTENSION);
						$suff=preg_replace('%^.*-([^-]+)\.[^.]+$%','-\1',$fpic);
						if ($suff==$fpic) $suff='';
						$keynamepic='users/'.$this->hashdir.'/media/'.$fhash.$suff.$ext2;
						$rep=$s3->create_object(
							$bucket,
							$keynamepic,
							array(
								'fileUpload' => $fpic,
								'acl' => AmazonS3::ACL_PRIVATE,
							)
						);
						if ($suff!='') {
							$keynamepic='users/'.$this->hashdir.'/media/'.$fhash.'-thumb'.$ext2;
							$fpic2=preg_replace('%^(.*)-[^-]+(\.[^.]+)$%','\1-thumb\2',$fpic);
							$rep=$s3->create_object(
								$bucket,
								$keynamepic,
								array(
									'fileUpload' => $fpic2,
									'acl' => AmazonS3::ACL_PRIVATE,
								)
							);
						}
					}

					//@unlink($tmpfile); // remove local temporary copy
					$res['link']=$fhash.strtolower($ext);
				} else {
					$res=array('error' => _('Upload error...'));
					//error_log('S3 ERROR: '.print_r($rep,true));
				}
			/*} else {
				$res=array('error' => _('Cannot update media DB...'));
			}*/
		} else $res=array('error' => _('File not found!?'), 'path' => $tmpfile);
		//error_log(print_r($res,true));
		return $res;
	}

	// associate $lid media (pic,doc...) to $id media (track,bundle...)
	function linkmedia($lid,$id) {
		global $mxdb;
		return $mxdb->linkmedia($this->id,$lid,$id);
	}

	function unlinkmedia($lid,$id) {
		global $mxdb;
		return $mxdb->unlinkmedia($this->id,$lid,$id);
	}

	function getlinkedmedia($id) {
		global $mxdb;
		return $mxdb->getlinkedmedia($this->id,$id);
	}

	function movetobundle($id,$bid,$pos=0) {
		global $mxdb;
		return $mxdb->movetobundle($this->id,$id,$bid,$pos);
	}

	function getbasebundle() {
		global $mxdb;
		return $mxdb->getbasebundle($this->id);
	}

	function getnewbundle() {
		global $mxdb;
		return $mxdb->getnewbundle($this->id);
	}

	function deletemedia($fid,$fname,$force=false) {
		global $mxdb,$s3;
		if ($media=$mxdb->checkfile($this->id,$fid,$fname)) {
			if (!$force && $media->status>=MXMEDIAFANVISIBLE && $media->status<MXMEDIAARCHIVED) {
				return array('error' => sprintf(_('[%s] has been published and can only be archived.'),$media->title));
			}
			if (!$force && $media->type==MXMEDIABASEBUNDLE) {
				return array('error' => sprintf(_('[%s] is a system bundle and cannot be removed.'),$media->title));
			}
			if ($media->type==MXMEDIAREGULARBUNDLE) {
				$defaultbundle=$this->getbasebundle();
				$this->movetobundle($fid,$defaultbundle); // move to default bundle
			}
			$res=$mxdb->deletemedia($this,$fid,$fname);
			if ($res['success'] && $media->type!=MXMEDIABASEBUNDLE && $media->type!=MXMEDIAREGULARBUNDLE)
			{
				$fhash=$media->hashcode;
				$ext=strtolower('.'.pathinfo($fname,PATHINFO_EXTENSION));
				$keyname='users/'.$this->hashdir.'/media/'.$fhash.$ext;
				$keyname2='users/'.$this->hashdir.'/media/'.$fhash.'-small.jpg';
				$keyname3='users/'.$this->hashdir.'/media/'.$fhash.'-wave.png';
				$keyname4='users/'.$this->hashdir.'/media/'.$fhash.'.jpg';
				$tmpfile=mx_option('usersdir').'/tmp/'.$fname;
				if (file_exists($tmpfile)) {
					@unlink($tmpfile);
				}
				// else $res=array('error' => _('File not found!?'));
		 		//if ($s3->if_object_exists(MXS3BUCKET,$keyname)) {
					$s3->delete_object(MXS3BUCKET,$keyname);
					$s3->delete_object(MXS3BUCKET,$keyname2);
					$s3->delete_object(MXS3BUCKET,$keyname3);
					if ($keyname4!=$keyname) $s3->delete_object(MXS3BUCKET,$keyname4);
				//}
			} else if (array_key_exists('error',$res)) {
				$res=array('error' => _('Cannot update media DB...'));
			}
		}
		return $res;
	}

	function archivemedia($fid,$fname) {
		global $mxdb,$s3;
		if ($media=$mxdb->checkfile($this->id,$fid,$fname)) {
			$res=$mxdb->archivemedia($this,$fid,$fname);
		}
	}

	function getartistname() {
		if ($this->artistname) return $this->artistname;
		if ($this->fullname) return $this->fullname;
		if ($this->firstname) return $this->firstname.' '.$this->lastname;
		return _('Anonymous');
	}

	function getname() {
		if ($this->fullname) return $this->fullname;
		if ($this->firstname) return $this->firstname.' '.$this->lastname;
		if ($this->artistname) return $this->artistname;
		return _('Anonymous');
	}

	function notify($msg) {
		$receiver=$this->getuserinfo($msg->to);
		$to=$receiver->fullname.' <'.$receiver->email.'>';
		$subj=sprintf(_('You just received a new %s from %s'),
			($msg->flags & MXREQUEST?_('request'):_('message')),
			($this->fullname?$this->fullname:$this->artistname));
		if ($msg->flags & MXREQUEST) {
			$msgtype='';
			if ($msg->flags & MXFRIENDREQUEST) $msgtype='friend ';
			$msgtype.='request';
		} else {
			$msgtype="message";
		}
		$html=mx_showhtmlpagestr('yougotmail');
		$html=str_replace('{RECEIVER}',$receiver->firstname,$html);
		$html=str_replace('{MESSAGETYPE}',$msgtype,$html);
		$html=str_replace('{MESSAGE}',($msg->subject?('<b>'.$msg->subject."</b><br/>"):'').$msg->body,$html);
		$msgaction=mx_actionurl('account','messages','rp:'.$msg->msgid,
				($msg->flags & MXREQUEST?'requests':'inbox'));
		$html=str_replace('{MESSAGEACTION}','<a href="'.$msgaction.'">MusXpand</a>',$html);
		$notifyurl=mx_actionurl('account','profile','-','notifs');
		$html=str_replace('{NOTIFYURL}','<a href="'.$notifyurl.'">'.$notifyurl.'</a>',$html);
		$headers='From: MusXpand Notification <'.MXNOREPLYEMAIL.'>';
		mx_sendmail($to,$subj,mx_html2text($html),$html);
	}


	function sendmessage($msg) {
		global $mxdb;
		if ($mxdb->sendmessage($this->id,$msg)) { // message sent?
			$this->notify($msg);
			return true;
		}
		return false;
	}

	function saveupdate($msg) {
		global $mxdb;
		if ($mxdb->saveupdate($this->id,$msg)) { // message sent?
			return true;
		}
		return false;
	}

	function markmsgread($msgid) {
		global $mxdb;
		return $mxdb->markmsg($this->id,$msgid,MXMSGREAD);
	}

	function markmsgarchived($msgid) {
		global $mxdb;
		return $mxdb->markmsg($this->id,$msgid,MXMSGARCHIVED);
	}

	function markmsgdraft($msgid) {
		global $mxdb;
		return $mxdb->markmsg($this->id,$msgid,MXMSGDRAFT);
	}

	function markmsgdeleted($msgid) {
		global $mxdb;
		return $mxdb->markmsg($this->id,$msgid,MXMSGDELETED);
	}

	function markwalldeleted($msgid) {
		global $mxdb;
		return $mxdb->markwall($this->id,$msgid,MXWALLDELETED);
	}

	function reqcancel($msgid) {
		global $mxdb;
		return $mxdb->requesthandle($this->id,$msgid,MXREQCANCELLED);
	}

	function reqaccept($msgid) {
		global $mxdb;
		return $mxdb->requesthandle($this->id,$msgid,MXREQACCEPTED);
	}

	function reqrecuse($msgid) {
		global $mxdb;
		return $mxdb->requesthandle($this->id,$msgid,MXREQRECUSED);
	}

	function reqignore($msgid) {
		global $mxdb;
		return $mxdb->requesthandle($this->id,$msgid,MXREQIGNORED);
	}

	function getbackgrounds($result=null) {
		global $mxdb;
		return $mxdb->getbackgrounds($this->id,$result);
	}

	function getpics($result=null) {
		global $mxdb;
		return $mxdb->getpics($this->id,$result);
	}

	function getbackground($media_id) {
		global $mxdb;
		if (!$media_id) return null;
		return $mxdb->getbackground($this->id,$media_id);
	}

	function getfriends($id) {
		global $mxdb;
		$priv=$this->getauthorizedfields($id);
		if (in_array('friends',$priv))
			return $mxdb->getfriends($this->id,$id);
		else return null;
	}

	function checkfbfriends($fbids) {
		global $mxdb;
		return $mxdb->checkfbfriends($this->id,$fbids);
	}

	function getuserinfo($id,$public=false) {
		global $mxdb;
		if (preg_match('%[^0-9]%',$id)) {
			$id=mx_getidfromusername($id);
		}
		$user=$mxdb->getuserinfo($this->id,$id);
		if (!$user) return null;
		$flds=$this->getauthorizedfields($id,$public);
		$newuser=new StdClass();
		foreach ($flds as $key => $fldname) {
			if ($fldname!='' && $user->$fldname!='') $newuser->$fldname=$user->$fldname;
		}
		if ($this->isfriend($id)) $newuser->isfriend=true;
		//print_r($newuser);
		return $newuser;
	}

	function getpicurl($pic) {
		$keyname='users/'.$this->hashdir.'/media/'.$pic->hashcode.'.'
		.pathinfo($pic->filename,PATHINFO_EXTENSION);
		return gets3url($keyname,'2 minutes');
	}

	function getbackgroundurl($media=null) { // media is media (object) OR media_id (int)
		global $defaultbackgrounds;
		if ($media==NULL) $media=$this->background_id;
		if ($media) {
			if (is_object($media)) $bg=$media;
			else $bg=$this->getbackground($media);
			if ($bg) {
				if (!$bg->owner_id)
					return mx_option('siteurl').'/images/background/'.$bg->filename;
				//$bgfile=$bg->hashdir.'/media/'.$bg->hashcode.'.'.
				//	strtolower(pathinfo($bg->filename,PATHINFO_EXTENSION));
				$keyname='users/'.$bg->hashdir.'/media/'.$bg->hashcode.'-small.jpg';
				$url=gets3url($keyname,'2 minutes');
				if ($_SERVER['HTTPS']) return str_replace('http:','https:',$url);
				return $url;
			}
		}
		//$img=rand(0,count($defaultbackgrounds)-1);
		//return mx_option('siteurl').'/images/background/'.$defaultbackgrounds[$img];
		return mx_option('siteurl').'/images/background/greystuff.jpg';
	}

	function addaddress($fields) {
		global $mxdb;
		// set option for informed cart
		return $mxdb->addaddress($this->id,$fields);
	}

	function deladdress($addid) {
		global $mxdb;
		// set option for informed cart
		$mxdb->deladdress($this->id,$addid);
	}

	function getaddress($addid) {
		global $mxdb;
		// set option for informed cart
		return $mxdb->getaddress($this->id,$addid);
	}

	function clearaddresses($cartid) {
		global $mxdb;
		// set option for informed cart
		$mxdb->clearaddresses($this->id,$cartid);
	}

	function setcart($cartid,$option,$value) {
		global $mxdb;
		// set option for informed cart
		$mxdb->setcart($this->id,$cartid,$option,$value);
	}

	function setcartbatch($cartid,$fields) {
		global $mxdb;
		// set option for informed cart
		$mxdb->setcartbatch($this->id,$cartid,$fields);
	}

	function getcart($cartid=null,$oldcart=false) {
		global $mxdb;
		// get pending cart for account or open a new one
		return $mxdb->getcart($this->id,$cartid,$oldcart);
	}

	function addcart($cartid,$prodtype,$prodref,$prodvar,$price) {
		global $mxdb;
		// get pending cart for account or open a new one
		return $mxdb->addcart($cartid,$prodtype,$prodref,$prodvar,$price);
	}

	function addwish($prodtype,$prodref,$prodvar,$price) {
		global $mxdb;
		// get pending cart for account or open a new one
		return $mxdb->addwish($this->id,$prodtype,$prodref,$prodvar,$price);
	}

	function getcartdetails($cartid) {
		global $mxdb;
		return $mxdb->getcartdetails($cartid);
	}

	function deletecart($cartid,$lines) {
		global $mxdb;
		$mxdb->deletecart($cartid,$lines);
	}

	function carttowish($cartid,$lines) {
		global $mxdb;
		$mxdb->carttowish($this->id,$cartid,$lines);
	}

	function wishtocart($cartid,$lines) {
		global $mxdb;
		$mxdb->wishtocart($this->id,$cartid,$lines);
	}

	function setplaytime($mediaid,$mediaplaytype,$action,$playid=0,$percent='0.00',$playtime='0',$rating='0',$status=0) {
		global $mxdb;
		$ret=$mxdb->setplaytime($this->id,$mediaid,$mediaplaytype,$action,$playid,$percent,$playtime,$rating,$status);
		return ($ret?$ret:0);
	}

	function getmstats($user) {
		global $mxdb;
		return $mxdb->getmstats($user->id,$user->username,$user->acctype);
	}

	function getdstats($user) {
		global $mxdb;
		return $mxdb->getdstats($user->id,$user->username,$user->acctype);
	}

	function deletewish($lines) {
		global $mxdb;
		$mxdb->deletewish($this->id,$lines);
	}

	function getwishlist() {
		global $mxdb;
		return $mxdb->getwishlist($this->id);
	}

	function getsub($userid=null) {
		global $mxdb;
		return $mxdb->getsub($this->id,$userid);
	}

	function setsubseen() {
		global $mxdb;
		$mxdb->setsubseen($this->id);
	}

	function setartlike($artistid,$like) {
		global $mxdb;
		return $mxdb->setartlike($this->id,$artistid,$like);
	}

	function isfriend($userid) {
		global $mxdb;
		return $mxdb->isfriend($this->id,$userid);
	}

	function logme($page,$option='',$action='',$referer='') {
		global $mxdb;
		$mxdb->logme($this->id,$_SERVER['REMOTE_ADDR'],$page,$option,$action,$referer,$this->browser);
	}

	function lostpassword($login) {
		global $mxdb;
		return $mxdb->lostpassword($login);
	}

	function whoswhere() {
		global $mxdb,$mxuser;
		$users=$mxdb->whoswhere($this->id);
		if ($users) {
			if (is_admin()) {
				$whoswhere['users']=array(
					'user' => array(0,_('Who'),'fan'),
					'location' => array(0,_('Where'),'text'),
					'time' => array(0,_('when'),'date'),
					'ip' => array(0,_('IP'),'text')
				);
			} else {
				$whoswhere['users']=array(
					'user' => array(0,_('Who'),'fan'),
					'location' => array(0,_('Where'),'text'),
					'time' => array(0,_('when'),'date')
				);
			}
			$logs=array();
			foreach ($users as $k => $userloc) {
				$log=new StdClass();
				$log->user=$userloc->userid;
				$log->location=mx_pagename($userloc->pag)
					.(($userloc->opt)?('/'.mx_optionname($userloc->pag,$userloc->opt)):'');
				$log->time=$userloc->date;
				$log->ip=$userloc->ip;
				$logs['users'][]=$log;
			}
			echo '<div class="form">';
			echo mx_showtablestr($whoswhere,$logs,'whoswhere',array(),'users');
			echo '</div>';
			/*
			echo '<ul>';
			foreach ($users as $id => $userloc) {
				$user=$this->getuserinfo($id);
				echo '<li>';
				$where=mx_pagename($userloc->pag)
					.(($userloc->opt)?('/'.mx_optionname($userloc->pag,$userloc->opt)):'');
				echo sprintf(_('%s was in "%s" %s'), mx_getname($user), $where,mx_difftime($userloc->date));
				echo '</li>';
			}
			echo '</ul>';
			*/
		}
	}

	function lastseen() {
		global $mxdb;
		return $mxdb->lastseen($this->id);
	}

	function setaccountforpage($pageid) {
		global $mxdb;
		$mxdb->setaccountforpage($this->id,$pageid);
	}

	function setmodules($bloc,$newmodules) {
		$themods=explode(',',str_replace('mxm_','',$newmodules));
		foreach ($themods as $amod) {
			foreach ($this->modules as $abloc => $modules) {
				$akey=array_search($amod,$modules);
				if ($akey!==false) {
					error_log('removing '.$amod.' from '.$abloc);
					unset($modules[$akey]);
					$this->modules[$abloc]=$modules;
				}
			}
		}
		$this->modules[$bloc]=$themods;
		error_log('new bloc '.$bloc.' is: '.$newmodules);
		$this->setoption('modules', $this->modules);
		return 'ok';
	}

	function hasfeature($feat) {
		if ($this->mxfeatures & $feat) return true;
		else return false;
	}

	function getfavorites() {
		global $mxdb;
		return $mxdb->getfavorites($this->id);
	}

	function delfav($favid) {
		global $mxdb;
		return array('success' => $mxdb->delfav($this->id,$favid));
	}

	function addfav($objid,$objtype) {
		global $mxdb;
		$favid=$mxdb->addfav($this->id,$objid,$objtype);
		$fav=new stdClass();
		$fav->id=$favid;
		$fav->userid=$this->id;
		$fav->favid=$objid;
		$fav->favtype=$objtype;
		return array('newfav' => $favid,'code' => mx_onefav($fav));

	}

}

function mx_emailexists($email) {
	global $mxdb;
	return $mxdb->emailexists($email);
}

function mx_checkfbuser($create=false) {
	global $mxuser,$mxdb,$me;
	/* returns:
	 *   0: $me == null
	 *   1: found mxuser
	 *   2: created mxuser
	 *   -1: could not create mxuser
	 */
	if (!$me) return 0;
	if (($dbuser=$mxdb->getfbuser($me['id'])) && $dbuser->status>=0) return 1;
	else if ($dbuser && $dbuser->status<0) return -1;
	if ($create) {
		$mxuser=new MXUser();
		if ($mxuser->id>0) return 2;
		//if ($mxdb->createuser($mxuser)) return 2;
		//else return -1;
	}
	return -1;
}

function mx_mnconfirm($page,$option,$action) {
	// do nothing (we never come here anyway...)
}


function mx_mnregister($page,$option,$action) {
	global $me,$facebook,$mxdb,$mxuser,$errors,$frmvalues,$referrer;

	if ($action=='signin') {
		mx_mnsignin($page,'signin',$action);
		return;
	}
	$invite=mx_secureword($_REQUEST['i']);
	//$redir=mx_secureredir($_GET['r']);
	$redir=''; // redirection after registering need to go through setup first -> hard to handle...
	if ($action=="" && $mxuser->id) {
		echo sprintf(_('Welcome back, %s'),$mxuser->firstname);
		return;
	}
	//error_log('mnregister:p='.$page.' o='.$option.' a='.$action.' mx='.$mxuser->id);
//	if ($_GET['session']) $action='fblogin';
	switch ($action) {
/*		case 'fblogin': // create account if not existing
			switch (mx_checkfbuser(true)) {
				case -1:
					echo _('Sign-in using Facebook worked,<br/>but your profile couldn\'t be created.');
					break;
				case 0:
					echo _('Sign-in using Facebook failed.');
					break;
				case 1:
					echo _('Welcome back, ').$me['name'];
					break;
				case 2:
					echo _('Welcome on board, ').$me['name'];
					break;
			}
			break;*/
		case 'confirmation':
			if ($mxuser->id) {
				switch ($mxuser->status) {
					case MXACCTDISABLED:
						$errors['c']=_('Account Unavailable.');
						break;
					case MXACCTUNCONFIRMED:
						$errors['c']=_('Code Incorrect');
						break;
					default:
						$errors['c']=_('Account Already Activated.');
						break;
				}
			} else {
				$errors['c']=_('Code Incorrect.');
			}
		case 'register':
		case 'waitconfirm':
		case 'sendagain':
			//echo 'login:'.$_POST['login'].'<br/>';
			//echo 'password:'.$_POST['password'].'<br/>';
			//mx_warning('Please only use Facebook to register for now...');
			if ($mxuser->id || $errors['c']) {
				$buttons=array(
				'+register' => _('Submit'),
				'sendagain' => ('Send Again'),
				'clear' => _('Clear')
				);
				$confirmform=array(
					'activation',0,_('Activate Your Account'),
					'',
					$buttons,
					array(
						'confirmlabel' => array(-1,_('Confirmation Requested'),
						_('<b>Check your email for a message from MusXpand</b>, and enter below the confirmation code you will'
						.' find in that email. We\'ll then help you complete your account setup.')),
						'c' => array(1,_('Confirmation Code:'),'text',40),
						//'login' => array(1,_('Username:'),'username',20,_('A name you can use to connect'
						//.' (lowercase letters and/or digits, with at least one letter in it)')),
						//'password' => array(1,_('Your password:'),'password',20),
						//'password2' => array(1,_('Confirm password:'),'password',20),
						//'location' => array(-1,_('Location'),_('Please tell us about the place' .
						//		' where you currently or usually live')),
						//'city' => array(1,_('City:'),'text',40),
						//'state' => array(1,_('State:'),'text',40),
						//'country' => array(1,_('Country:'),'text',40),
						'a' => array(1,'confirmation','hidden')
					)
				);
				if (!(MXDEFFEATURES & MXFTNEWLOGIN)) {
					mx_showform($confirmform,$frmvalues,true,true,$errors);
				} else {
					$signerrormsg='';
					$loginclass='';
					if ($errors['c']) {
						$loginclass='class="signerror"';
						$signerrormsg=$errors['c'];
					}
					$terms=mx_windowedpage('terms',_('Terms'),true);
					echo $terms['div'];
					$priv=mx_windowedpage('privacy',_('Privacy'),true);
					echo $priv['div'];

				?>
				<div class="loginwrapper"><div>
					<div class="loginbutton confirmbutton">
						<div class="loginbg"></div>
						<div class="loginform">
							<img src="<?php echo mx_option('siteurl').'/images/general/musxpand-logo-200x200.png'; ?>"/>
							<br/>
							<?php echo mx_fbloginbutton('Login with Facebook','account','register','fb'); ?>
							<form class="confirmform" method="POST" action="<?php echo mx_optionurl_secure('account','register'); ?>">
							<ul>
							<li><input <?php echo $loginclass; ?> id="login" type="text" name="c" size="25" placeholder="<?php __('Confirmation Code'); ?>" value="<?php echo $frmvalues['c']; ?>"></li>
							<li><div class="signerror"><?php echo $signerrormsg; ?></div></li>
							</ul>
							<input type="hidden" name="a" value="confirmation">
							<input type="submit" name="confirmation" value="<?php __('Activate'); ?>" onclick="this.form['a'].value='confirmation';blackout('<?php __('Please wait...'); ?>');submit();">
							<input type="button" name="sendagain" value="<?php __('Send Again'); ?>" onclick="this.form['a'].value='sendagain';blackout('<?php __('Please wait...'); ?>');submit();">
							<br/>
							</form>
							<div class="logincopy"><?php echo '&copy; 2010-2012, MusXpand.'; ?></div>
							<div class="loginterms"><?php echo $terms['str'].' / '.$priv['str']; ?></div>
						</div>
					</div>
				</div></div>
				<?php
				}
				break;
			}
			// ...
		default:
			if (MXINVITEONLY) {
	 			if (!$invite) {
					mx_showhtmlpage('inviteonly');
					return;
				}
				if ($invite && $referrer==-1) {
					mx_showhtmlpage('nomoreinvites');
					return;
				}
			}
			$buttons=array(
				'+register' => _('Submit'),
				'clear' => _('Clear')
			);
			$refinfo='';
			if ($invite) {
				//error_log(print_r($mxuser,true));
				$refuser=$mxuser->getuserinfo($referrer);
				//error_log(print_r($mxuser,true));
				$refinfo='<div class="referrer"><div class="refdetails">'
				._('Invited by').'<br/><div class="friend">'
				.'<img class="dirpic" src="'.mx_fanpic($referrer,'square',$refuser->gender,($refuser->acctype==MXACCOUNTARTIST)).'"/>'
				.'<br/>'.mx_getartistname($refuser)
				.'</div></div></div>';
			}
			$registerform=array(
				'register',0,_('Register Your Account'),
				sprintf(_('To easily register using your facebook account, simply use the Facebook button here: %s.'
					.'<br/>If you have no Facebook account or don\'t want '
					.'to use it with MusXpand, please fill in your email below instead. '
					.'We\'ll send you a confirmation message, so that '
					.'you can complete your account registration with your information.<br/><br/>'
					.'<b>If you have already created an account on MusXpand, please %s instead</b>.'),
					mx_fbloginbutton(_('Register'),'account','register','fb',$redir,$invite),
					mx_optionlink('account','signin')),
				$buttons,
				array(
					'basic' => array(-1,_('Basic Information'),_('Please only fill in your email if you don\'t register'
						.' using Facebook')
						.$refinfo),
					'email' => array(1,_('Email:'),'text',40),
					//'login' => array(1,_('Username:'),'username',20,_('A name you can use to connect'
					//.' (lowercase letters and/or digits, with at least one letter in it)')),
					//'password' => array(1,_('Your password:'),'password',20),
					//'password2' => array(1,_('Confirm password:'),'password',20),
					//'location' => array(-1,_('Location'),_('Please tell us about the place' .
					//		' where you currently or usually live')),
					//'city' => array(1,_('City:'),'text',40),
					//'state' => array(1,_('State:'),'text',40),
					//'country' => array(1,_('Country:'),'text',40),
					'a' => array(1,'register','hidden'),
					'i' => array(1,$invite,'hidden'),
				)
			);
			$location=mx_locate();
			if (!$frmvalues) $frmvalues=array();
			/*if ($location) {
				$values=array(
					'city' => iconv('ISO-8859-1','utf-8',$location->city),
					'state' => mx_region($location),
					'country' => $location->countryCode
				);
			}*/
			//echo _('Welcome to the register page...').'<br/>';
		if (!(MXDEFFEATURES & MXFTNEWLOGIN)) {
			mx_showform($registerform,$frmvalues,true,true,$errors);
		} else {
			$signerrormsg='';
			$passwdclass='';
			$loginclass='';
			if ($errors['email']) {
				$loginclass='class="signerror"';
				if ($action=='confirmation') $signerrormsg=_('Invalid confirmation code');
				else $signerrormsg=$errors['email'];
			}
			if ($errors['password']) {
				$passwdclass='class="signerror"';
				if ($action=='signin') $signerrormsg=_('Wrong email/password');
			}
			$terms=mx_windowedpage('terms',_('Terms'),true);
			echo $terms['div'];
			$priv=mx_windowedpage('privacy',_('Privacy'),true);
			echo $priv['div'];

		?>
		<div class="loginwrapper"><div>
			<div class="loginbutton registerbutton">
				<div class="loginbg"></div>
				<div class="loginform">
					<img src="<?php echo mx_option('siteurl').'/images/general/musxpand-logo-200x200.png'; ?>"/>
					<br/>
					<?php echo mx_fbloginbutton('Login with Facebook','account','register','fb'); ?>
					<form class="registerform" method="POST" action="<?php echo mx_optionurl_secure('account','register'); ?>">
					<ul>
					<li><input <?php echo $loginclass; ?> id="email" type="text" name="email" size="25" placeholder="<?php __('Email Address'); ?>" value="<?php echo $frmvalues['email']; ?>"></li>
					<li><input <?php echo $passwdclass; ?> id="pass" type="password" name="password" size="25"  placeholder="<?php __('Password'); ?>"></li>
					<li><div class="signerror"><?php echo $signerrormsg; ?></div></li>
					</ul>
					<input type="hidden" name="a" value="signin">
					<input type="submit" name="signin" value="<?php __('Sign in'); ?>" onclick="this.form['a'].value='signin';blackout('<?php __('Please wait...'); ?>');submit();">
					<input type="button" name="register" value="<?php __('Sign up'); ?>" onclick="this.form['a'].value='register';blackout('<?php __('Please wait...'); ?>');submit();">
					<br/>
					<input class="forgot" type="button" name="forgot" value="<?php __('I forgot my password'); ?>" onclick="this.form['a'].value='forgot';blackout('<?php __('Please wait...'); ?>');submit();">
					</form>
					<div class="logincopy"><?php echo '&copy; 2010-2012, MusXpand.'; ?></div>
					<div class="loginterms"><?php echo $terms['str'].' / '.$priv['str']; ?></div>
				</div>
			</div>
		</div></div>
		<?php
		}
	}
	//error_log('end mnregister');
}

function mx_getreferrer($invite) {
	global $mxdb;
	return $mxdb->getreferrer($invite);
}

function mx_ckconfirm($page,$option,$action) {
	header('Location: '.mx_actionurl('account','register','waitconfirm'));
}


function mx_ckregister($page,$option,$action) {
	global $mxuser,$me,$errors,$frmvalues,$referrer;

	//error_log('register:action='.$action);
	$invite=mx_secureword($_REQUEST['i']);
	if ($action=='signin') {
		mx_cksignin($page,'signin',$action);
		return;
	}
	if ($invite) {
		$referrer=mx_getreferrer($invite);
		if (!$referrer) unset($_REQUEST['i']); // fake invite code
	}

	if ($action=='confirmation') {
		$confirmcode=mx_secureword($_REQUEST['c']);
		$frmvalues['c']=$confirmcode;
		if ($mxuser->checkconfirm($confirmcode)) {
			switch ($mxuser->status) {
				case MXACCTUNCONFIRMED:
					$mxuser->setoption('status',MXACCTEMAILCONFIRMED);
					$mxuser->pwdreset();
					// update session with new password
				case MXACCTEMAILCONFIRMED: // already confirmed but not set up
					mx_setsession($mxuser,time());
					//error_log('mxsession='.$mxsession);
					// then proceed to setup
					$_REQUEST['o']=$_GET['o']='setup';
					$_REQUEST['a']=$_POST['a']='setup_0';
					//header('Location: '.mx_actionurl('account','setup','setup_0'));
					break;
				default:
					$error['c']=_('Your account has already been setup. Please sign-in instead.');
			}
		}
	} else if ($action=='sendagain') {
		mx_sendconfirmationcode($mxuser);
		$errors['c']=_('Code Resent');
	} else if ($action=='register' && ($referrer || !MXINVITEONLY)) {
		$email=mx_securestring($_POST['email']);
		$frmvalues['email']=$email;
		if (!preg_match('%^[a-zA-Z0-9._-]+@([a-zA-Z0-9-]+\.)+[a-zA-Z0-9]+$%',$email)) {
			$errors['email']=_('Email incorrect');
			error_log('registering, email incorrect: ['.$email.']');
		} else {
			$mxuser=new MXUser($frmvalues);
			if ($mxuser->id==0) $errors['email']=_('Email already registered');
			else if ($mxuser->id<0) {
				$errors['email']=_('This account was deleted and this email cannot be used anymore');
				$mxuser->id=0;
			}
		}
	} else if ($action=='fb' && ($referrer || !MXINVITEONLY)) {
		mx_checkfblogin(false);
		//error_log('after checkfblogin, me='.print_r($me,true).' facebook='.print_r($facebook,true));
		$cruser=mx_checkfbuser(true);
		if ($cruser==1) {
			//$mxuser=new MXUser();
			header('Location: '.mx_actionurl('account','signin','fb'));
		} else if ($cruser==2) {
			//error_log(print_r($mxuser,true));
			header('Location: '.mx_actionurl('account','setup','setup_0'));
			die();
		}
		//error_log('create: '.$cruser);
		/*
		if ($me) {
			header('Location: '.mx_optionurl('account','signin'));
		}
		*/
	}
	//error_log('action='.$action.' referrer='.$referrer);
	if ($me && $mxuser && $mxuser->id) { // we're already logged in (through FB)
		header('Location: '.mx_optionurl('account','signin','fb'));
	}
	//if ($mxuser->id && !$mxuser->acctype) header('location: '.mx_optionurl('account','acctype'));
}

function mx_mnsignin($page,$option,$action) {
	global $me,$mxuser,$signerrors;
	$redir=mx_secureredir(urldecode($_GET['r']));
	if ($action=='register') {
		mx_mnregister($page,'register',$action);
		return;
	}
	//error_log('mnsignin:'.$action);
	if ($action=='forgot' && array_key_exists('user',$signerrors)) {
		$buttons=array();
		$setupform=array(
			'lostpwd',0,_('Check your email...'),
			'',
			$buttons,
			array(
				'label' => array(-1,_('Just one more step'),
					_('<p>As a secuity measure, <b>we just sent you an email to confirm the password reset operation</b>.</p>'
					.'<p>We know this is a bit of an annoyance,'
					.' but we want to prevent people from playing with our members\' nerves and trying to reset their'
					.' passwords regularly.</p><p>So, <u>please check your email for a message from us</u>: you will have to'
					.' follow a special link to a new login page where <b>you will be able to set up a new password</b> AND to'
					.' eventually sign in.</p>'
					.'<p>See you in a few moments...</p>')
					.sprintf('<span class="pwdretry"><a href="'.mx_optionurl('account','signin').'">%s</a></span>',_('Try Again'))),
				'a' => array(1,'done','hidden')
			)
		);
		mx_showform($setupform,array(),false,true);
		return;
	} else if (($action=="confirmation" && $mxuser->id)
	|| ($action=="update" && array_key_exists('password',$signerrors))) {
		$buttons=array(
			'update' => _('Update Password'),
			'clear' => _('Clear'),
		);
		$signinform=array(
			'signin',0,_('Password Update'),_('Please enter your new password in the fields below.<br/><br/>'),
			$buttons,
			array(
				'credentials' => array(-1,_('Authentication'),_('Choose a new password...')),
				//'login' => array(1,_('Account or Email:'),'text',40),
				'password' => array(1,_('Password:'),'newpassword',20),
				'a' => array(1,'update','hidden'),
				'r' => array(1,$redir,'hidden')
			)
		);
		mx_showform($signinform,$signvalues,true,true,$signerrors);
		return;
	} else if ($action=="update") {
		$buttons=array();
		$setupform=array(
			'lostpwd',0,_('We\'re done!'),
			'',
			$buttons,
			array(
				'label' => array(-1,_('You did it!'),
					sprintf(_('<p>Great!</p><p>Your password is now updated.</p>'
					.'<p>If you need to change it again, please visit %s.</p>'
					.'<p>Enjoy!</p>'),mx_optionlink('account','profile'))),
				'a' => array(1,'done','hidden')
			)
		);
		mx_showform($setupform,array(),false,true);
		return;
	}
	if ($me) {
		switch (mx_checkfbuser(false)) {
		case -1:
		case 0:
			echo _('Sorry, you cannot sign in without registering first<br/>'
			.'and we only accept new registrations through invites at this time...');
			break;
		case 1:
			echo sprintf(
				_('Welcome back, %s. You just signed using your facebook account.'),$me['name']);
			break;
		case 2:
			echo sprintf(_('Welcome on board, %s'),$me['name']);
			break;
		}
	} else if ($mxuser->id) {
		echo sprintf(_('Welcome back, %s'),$mxuser->firstname?$mxuser->firstname:_('New User'));
	}
	//die(phpinfo());
	if ($me || $mxuser->id) {
		echo '<script src="http://www.surveymonkey.com/jsPop.aspx?sm=EUbzy4cmf97dTX9rXGrsmQ_3d_3d"></script>';
		return;
	}
	$signlogin=mx_securestring($_REQUEST['email']);
	$redir=mx_secureredir($_GET['r']);
	$buttons=array(
		'+signin' => _('Sign-in'),
		//'clear' => _('Clear'),
		'forgot' => _('I forgot my password'),
	);
	$signinform=array(
		'signin',0,_('Sign In'),sprintf(_('To authentify using facebook, use the Facebook button here: %s'),
		mx_fbloginbutton(_('Sign-in'),'account','signin','fb',$redir)),
		$buttons,
		array(
			'credentials' => array(-1,_('Authentication'),sprintf(_('If you have no Facebook account or don\'t want '
			.' to use it with MusXpand, please fill in your information below instead.<br/><br/>'
			.'<b>If you haven\'t created an account on MusXpand yet, please %s first</b>.'),
			mx_optionlink('account','register'))),
			'email' => array(1,_('Account or Email:'),'text',40),
			'password' => array(1,_('Password:'),'password',20),
			'a' => array(1,'signin','hidden'),
			'r' => array(1,$redir,'hidden')
		)
	);
	//echo _('Welcome to the sign-in page...').'<br/>';
	$signvalues=array(
		'email' => $signlogin
	);
	if ($signerrors['email'] || $signerrors['captcha']) {
		$signinform[5]['captcha']=array(1,_('Confirm you\'re a person'),'captcha');
	}
	if (!(MXDEFFEATURES & MXFTNEWLOGIN))
		mx_showform($signinform,$signvalues,true,true,$signerrors);
	else {
		$passwdclass='';
		$loginclass='';
		if ($signerrors['email']) {
			$loginclass='class="signerror"';
			if ($action=='confirmation') $signerrormsg=_('Invalid confirmation code');
			else if (!$signlogin) $signerrormsg=_('I need an email or a username');
			else $signerrormsg=_('Email/username not found');
		}
		if ($signerrors['password']) {
			$passwdclass='class="signerror"';
			if ($action=='signin') $signerrormsg=_('Wrong email/password');
		}
		$terms=mx_windowedpage('terms',_('Terms'),true);
		//echo $terms['div'];
		$priv=mx_windowedpage('privacy',_('Privacy'),true);
		//echo $priv['div'];

	?>
	<div class="loginwrapper"><div>
		<div class="loginbutton"><?php echo $term['div'].$priv['div']; ?>
			<div class="loginbg"></div>
			<div class="loginform">
				<img src="<?php echo mx_option('siteurl').'/images/general/musxpand-logo-200x200.png'; ?>"/>
				<br/>
				<?php echo mx_fbloginbutton('Login with Facebook','account','register','fb'); ?>
				<form class="loginform" method="POST" action="<?php echo mx_optionurl_secure('account','signin'); ?>">
				<ul>
				<li><input <?php echo $loginclass; ?> id="email" type="text" name="email" size="25" placeholder="<?php __('Email Address'); ?>" value="<?php echo $signlogin; ?>"></li>
				<li><input <?php echo $passwdclass; ?> id="pass" type="password" name="password" size="25"  placeholder="<?php __('Password'); ?>"></li>
				<li><div class="signerror"><?php echo $signerrormsg; ?></div></li>
				</ul>
				<input type="hidden" name="a" value="signin">
				<input type="submit" name="signin" value="<?php __('Sign in'); ?>" onclick="this.form['a'].value='signin';blackout('<?php __('Please wait...'); ?>');submit();">
				<input type="button" name="register" value="<?php __('Sign up'); ?>" onclick="this.form['a'].value='register';blackout('<?php __('Please wait...'); ?>');submit();">
				<br/>
				<input class="forgot" type="button" name="forgot" value="<?php __('I forgot my password'); ?>" onclick="this.form['a'].value='forgot';submit();">
				</form>
				<div class="logincopy"><?php echo '&copy; 2010-2012, MusXpand.'; ?></div>
				<div class="loginterms"><?php echo $terms['str'].' / '.$priv['str']; ?></div>
			</div>
		</div>
	</div></div>
	<?php
	}
}

function mx_cksignin($page,$option,$action) {
	global $me,$mxuser,$mxsession,$signerrors;
	/*if ($mxuser->id) {
		//die();
		header('Location: '.mx_actionurl($page,$option,'ok'));
	}*/
	//die('page='.$page.' option='.$option.' action='.$action.'<br/>REQUESTS: '.print_r($_REQUEST,true));

	$signerrors=array();
	$redir=mx_secureredir(urldecode($_REQUEST['r']));
	//error_log('signin: action='.$action);
	//mx_checkfblogin(false);
	if ($action=='register') {
		mx_ckregister($page,'register',$action);
		return;
	} else if ($action=='signin') { // check login data
		$login=$_POST['email'];
		$pwd=$_POST['password'];
		if ($_POST['captcha']) {
			require_once('ext_includes/recaptchalib.php');
			$resp = recaptcha_check_answer (MX_RECAPTCHA_PRIVATE,
				$_SERVER["REMOTE_ADDR"],
				$_POST["recaptcha_challenge_field"],
				$_POST["recaptcha_response_field"]);
			if (!$resp->is_valid) {
			// What happens when the CAPTCHA was entered incorrectly
				$signerrors['captcha']=_("ReCaptcha incorrect.<br/>Please try again");
			}
		}

		if (!$signerrors['captcha'] && $mxuser->checklogin($login,$pwd)) {
			mx_setsession($mxuser,time());
			header('Location: '.mx_actionurl_normal($page,$option,'ok','',$redir));
		} else if (!$signerrors['captcha']) {
			$signerrors=array(
				'email' => _('Wrong credentials...'),
				'password' => _('...or wrong password.'),
			);
		}
	} else if ($action=='fb') {
		mx_checkfblogin(false);
		$cruser=mx_checkfbuser(false);
		if ($cruser==1) {
			$mxuser=new MXUser();
		} else {
			header('Location: '.mx_actionurl_normal('account','register','fb'));
		}
		/*else if (mx_checkfbuser(true)==2) {
			error_log(print_r($mxuser,true));
			header('Location: '.mx_actionurl('account','setup','setup_0'));
		} */ /* else {
			header('Location: '.mx_actionurl_normal('account','register','','',($redir?(':'.$redir):'')));
		}*/
	} else if ($action=='forgot') {
		$login=$_POST['email'];
		if (!$login) {
			$signerrors=array(
				'email' => _('Please inform your email or username'.
					' (Have you tried signing in using Facebook?)')
			);
		} else {
			$signerrors=$mxuser->lostpassword($login);
		}
	} else if ($action=='confirmation') {
		$confirmcode=mx_secureword($_REQUEST['c']);
		$mxuser->checkconfirm($confirmcode);
		if ($mxuser->id) {
			mx_setsession($mxuser,time());
		} else {
			$signerrors=array('email' => _('The password recovery link you\'re using is no more valid.'
				.' If you requested twice, check the most recent email you received.'));
		}
		return;
	} else if ($action=='update') {
		$postfld=$_POST['new_password'];
		$chkconf=$_POST['conf_password'];
		if ($chkconf!=$postfld) {
			$signerrors=array('password' => _('Password confirmation does not match new password.'));
		} else if (!$postfld) {
			$signerrors=array('password' => _('Password cannot be left blank.'));
		} else {
			$signerrors=array();
			$mxuser->setoption('pwdhash',hash('sha256',$_POST['new_password']));
			mx_setsession($mxuser,time());
		}
		return;
	}/* else {
		mx_checkfblogin(false);
		if (mx_checkfbuser(false)==1) {
			$mxuser=new MXUser();
		}
		else $me=null;
		//if ($me) die('FB logged');
	}*/
	if ($mxuser->id) {
		mx_fbaction('use?website='.mx_pageurl('main'));
	}
	if ((/* $me || */ $mxuser->id) && $redir) { //$action=='redirect') {
		preg_match_all('%([^,]*),?%',$redir,$aredir);
		//error_log('signin/redir: '.$redir.' - '.print_r($aredir,true));
		if ($aredir && $aredir[1][0]) header('location: '
		.mx_actionurl($aredir[1][0],$aredir[1][1],$aredir[1][2],$aredir[1][3],'',($_SERVER['HTTPS']?'secure':'normal'),$aredir[1][4]));
		return;
	}
	/*if ($mxuser->id && array_key_exists('HTTPS',$_SERVER)) {
		header('Location: '.mx_actionurl_normal($page,$option,'ok'.($redir?(':'.$redir):'')));
		return;
	}*/
	// define account if not done...
	if ($mxuser->id) {
		if (!$mxuser->acctype) header('location: '.mx_optionurl('account','setup'));
		header('location: '.mx_actionurl('main','','signed'));
	}
	//error_log('hello!');
	//die(phpinfo());
}

function mx_mnsignoff($page,$option,$action) {
	global $me,$facebook,$mxsession;
	//if ($me) {
	//	mx_warning('<fb:logout-button></fb:logout-button>');
	//} else {
		__('<p>You\'ve just signed off. See you soon...</p>');
	//}
}

function mx_cksignoff() {
	global $me,$facebook,$mxsession;
	//mx_fbaction('use?website='.mx_pageurl('main'));
	if (!$mxsession) return;
	unset($_SESSION['mxsession']);
	unset($mxsession);
	unset($me);

	/*$_SESSION=array();
	if (ini_get("session.use_cookies")) {
	    $params = session_get_cookie_params();
	    setcookie(session_name(), '', time() - 42000,
	        $params["path"], $params["domain"],
	        $params["secure"], $params["httponly"]
	    );
	}
	session_destroy();*/

	if ($me) {
		header('Location: '.$facebook->getLogoutUrl());
	} else {
		header('Location: '.mx_pageurl('main'));
	}
}

function mx_mnprofile($page,$option,$action) {
	global $mxuser,$me,$msgs,$errors;
	if (!$mxuser) return;
	$form=($action=='edit')?true:false;
	$fldarray=$mxuser->infogroups(null,$mxuser->acctype);
	foreach($fldarray as $grp => $det) {
		$bandflds[$grp]=array(-1,$det[0]);
		foreach ($det[1] as $fld) {
			$bandflds[$fld]=$mxuser->fielddesc($fld,false); // fld description for users!
			if ($bandflds[$fld][0]==2
			 && $mxuser->acctype!=MXACCOUNTARTIST  && $mxuser->acctype!=MXACCOUNTBAND) {
			 	unset($bandflds[$fld]);
			 	continue;
			 }
			 if ($fld=="password") {
			 	$bandflds[$fld][5]=true; // allow password changes
			 }
			 if ($fld=="picture") $bandvalue[$fld]=mx_fanpic($mxuser->id);
			 else $bandvalue[$fld]=$mxuser->$fld;
		}
		$bandflds['a']=array(1,'update','hidden');
		$bandflds['k']=array(1,'','hidden');
		$editbtn=array();
		if (!$form) $editbtn['edit_'.$grp]=_('Edit');
		$bandflds['edit_'.$grp]=array(-3,$editbtn);
	}
	$buttons=array(
		'+update' => _('Apply Changes'),
		'clear' => _('Clear')
	);
	$bandinfo=array(
		'profile',0,_('Your Information'),
		_('Please keep this information updated.'),
		$buttons,
		$bandflds
	);
	//echo '<xmp>'.print_r($bandvalue).'</xmp>';
	//error_log(print_r($errors,true));
	mx_showform($bandinfo,$bandvalue,$form,true,$errors);
	$section=mx_secureword($_REQUEST['k']);
	if ($section) {
		echo '<script>window.location="#'.$section.'";</script>';
	}

	if ($action=='edit') {
?>
<script language="javascript">
    function createUploader(){
        var uploader = new qq.FileUploader({
            element: document.getElementById('fileuploader'),
            action: '<?php echo mx_option('siteurl').'/picupload.php'; ?>',
            params: {
            	id: '<?php echo $mxuser->id; ?>'
            },
            allowedExtensions: ['jpg','jpeg','png'],
			sizeLimit: 2000000,
			onComplete: function(id, fileName, responseJSON){
				if (responseJSON.success) {
					var mypic=document.getElementById('newpic');
					if (mypic) mypic.src='<?php echo mx_option('usersURL')
					.'/tmp/'; ?>'+encodeURIComponent(fileName);
					var piclist=document.getElementById('piclist');
					if (piclist) {
						listelem=document.createElement('li');
						input=document.createElement('input');
						input.type='radio';
						input.name='picture';
						input.value=responseJSON['link'];
						input.checked=true;
						input.onclick=function() {
							mypic.src='<?php echo mx_option('usersURL')
							.'/tmp/'; ?>'+encodeURIComponent(fileName);
						}
						var radiolabel=document.createTextNode(fileName);
						listelem.appendChild(input);
						listelem.appendChild(radiolabel);
						piclist.appendChild(listelem);
					}
				}
            }
        });
    }

    // in your app create uploader as soon as the DOM is ready
    // don't wait for the window to load
    //if (window.onload) {
    //	var oldloadfunction=window.onload;
    //	window.onload = (typeof window.onload != 'function') ? createUploader : function() { oldloadfunction(); createUploader(); };
    //} else window.onload = createUploader;
    createUploader();
</script>
<?php
	}
}

function mx_cksetup($page,$option,$action) {
	error_log('setup:action='.$action);

	mx_ckprofile($page,$option,$action);
}

function mx_ckprofile($page,$option,$action) {
	global $mxuser,$msgs,$mxsession,$errors,$usernameerrs;
	if ($action=='update' || preg_match('%setup_%',$action) || $action=='done') {
		//die(phpinfo());
		$section=preg_replace('%[^0-9]%','',$action); // just step number in case we're in the setup
		/*
		if (!$section || $section=='' || $section>count($steps)) {
			$section=null;
		}
		*/
		//phpinfo();
		$msgs=null;
		// list of fields that should have been posted to check mandatory ones have been filled...
		$prevsection=mx_secureword($_REQUEST['k']);
		$oldflds=array();
		foreach($mxuser->infogroups(($action=='done' || $action=='update')?null:$prevsection) as $grp => $details) {
			foreach ($details[1] as $field) {
				$oldflds[$field]=1;
			}
		}
		if (array_key_exists('PROid',$_REQUEST) && $_REQUEST['PROid']==MXNOPROYET) {
			$_POST['PROmemberid']='-';
		}
		//error_log(print_r($oldflds,true));
		foreach ($mxuser->infogroups() as $grp => $details) {
			foreach ($details[1] as $field) {
				$fldinfo=$mxuser->fielddesc($field);
				if (!$fldinfo[0]) continue;
				if (array_key_exists($field.'_y',$_POST))
					$_POST[$field]=$_POST[$field.'_y'].'-'.$_POST[$field.'_m'].'-'.$_POST[$field.'_d'];
				if (!array_key_exists($field,$_POST)) { // field was not posted
					if ($mxuser->$field) continue; // field is already set
					if (!array_key_exists($field,$oldflds)) continue; // not in the previous form and not blank
					if ($fldinfo[0]<3 || ($mxuser->acctype==MXACCOUNTFAN && $fldinfo[0]==4)) continue; // not mandatory
					if ($section && $section<$prevsection) continue; // we're back to the previous form: don't bother...
					$_REQUEST['a']='setup_'.$prevsection;
					$errors[$field]=_('This field is mandatory!');
					continue;
				}
				if (!$_POST[$field] && $field!='PROid') { // field blank
					if ($fldinfo[0]>=3 && ($mxuser->acctype!=MXACCOUNTFAN || $fldinfo[0]!=4) // mandatory
					&& ($section=='' || $section>$prevsection)) { // we're going to the next step...
						if ($prevsection) $_REQUEST['a']='setup_'.$prevsection;
						else $_REQUEST['a']='edit';
						$errors[$field]=_('This field is mandatory!');
						continue;
					}
				}
				switch ($fldinfo[2]) { // CAREFUL: testing types not fields!!
					case 'legalname':
						$postfld=ucwords(mx_securestring($_POST[$field]));
						break;
					case 'fullname':
						$postfld=mx_securestring($_POST[$field]);
						if ($postfld==strtolower($postfld)) $postfld=ucwords($postfld); // if all lowercase capitalize initials
						break;
					case 'proid':
						$postfld=mx_secureword($_POST[$field]);
						if ($postfld==0) {
							$proname=mx_securestring($_POST['proname']);
							$prosite=mx_securestring($_POST['prosite']);
							if ($proname && $prosite) $postfld=$mxuser->addpro($proname,$prosite);
							else {
								//$errors[$field]=_('You must inform your PRO organization');
								if (!$proname && !$prosite) { $errors[$field]=_('We need this information.'); }
								else if (!$proname) { $errors[$field]=_('You must inform the association\'s name/acronym'); }
								else { $errors[$field]=_('You must inform a website'); }
								if ($prevsection) $_REQUEST['a']='setup_'.$prevsection;
								else $_REQUEST['a']='edit';
								continue;
							}
						}
						break;
					case 'acctype': // ignore this field
						if ($mxuser->acctype!=MXACCOUNTFAN && $mxuser->status!=MXACCTEMAILCONFIRMED)
							$postfld=$mxuser->acctype; // don't modify if not setting up...
						else $postfld=preg_replace('%[^0-9]%','',$_POST[$field]);
						break;
					case 'date':
						$postfld=$_POST[$field.'_y'].'-'
						.$_POST[$field.'_m'].'-'
						.$_POST[$field.'_d'];
						break;
					case 'privacy':
						if (is_array($_POST[$field])) $postfld=implode(',',$_POST[$field]);
						else $postfld='';
						break;
					case 'password':
						$chkold=hash('sha256',$_POST[$field]);
						if (!$_POST[$field]) {
							$postfld=$mxuser->$field; // no old password -> don't change :-)
						} else if ($chkold!=$mxuser->pwdhash) {
							$msgs->err=_('Wrong password entered.<br/>Your present password was left unchanged.');
							$postfld=$mxuser->$field; // do not change password!
						} else {
							$postfld=hash('sha256',$_POST['new_'.$field]);
							$chkconf=hash('sha256',$_POST['conf_'.$field]);
							if ($chkconf!=$postfld) {
								$msgs->err=_('Password confirmation does not match new password!<br/>Your password was left unchanged.');
								$postfld=$mxuser->$field; // do not change password!
							} else {
								$msgs->ok=_('Your password was successfully updated.');
								$field='pwdhash'; // we'll update the hash!
							}
						}
						break;
					case 'username':
						$postfld=trim(strtolower(preg_replace('![^0-9a-zA-Z-_.]!','',$_POST[$field])));
						if ($postfld!=$mxuser->$field) {
							$ckusername=mx_checkusername($mxuser->username,$postfld);
							if ($ckusername) {
								$msgs->err.=sprintf(_('Username was not updated: %s'),
								($ckusername>0?_('Username already used'):$usernameerrs[$ckusername]));
								$postfld=$mxuser->$field;
							}
							else {
								$msgs->ok.=_('Username was updated.');
							}
						}
						break;
					case 'agreement':
						if (!$mxuser->agreement || $mxuser->agreement=='0000-00-00 00:00:00')
							$postfld=date('Y-m-d H:i:s');
						else $postfld=$mxuser->agreement;
						break;
					case 'genre':
						$postfld=$_POST[$field];
						break;
					default:
						$postfld=stripslashes($_POST[$field]);
						break;
				}
				if ($postfld!=$mxuser->$field) {
					$mxuser->setoption($field,$postfld);
					//$mxuser->$field=$postfld; // already done in setoption...
				}
				if ($field=='pwdhash') { // needs to clear the session with new password credentials :-)
					mx_setsession($mxuser,time());
				}
			}
		}
		if ($action=='done' && !$errors) {
			$mxuser->setoption('status',MXACCTSETUP);
		}
		//header('Location: '.mx_actionurl($page,$option,'done'));
	}
	//error_log(print_r($errors,true));
}

function mx_mnviewarts($page,$option,$action) {
	mx_mnviewprof($page,$option,$action);
}

function mx_ckaccount($page,$option,$action) {
	global $mxuser;
	if ($option) return;
	switch ($mxuser->status) {
		case MXACCTUNCONFIRMED:
			header('Location: '.mx_actionurl('account','register','waitconfirm'));
			break;
		case MXACCTEMAILCONFIRMED:
			header('Location: '.mx_optionurl('account','setup'));
			break;
		default:
			header('Location: '.mx_optionurl('account',($mxuser->acctype==MXACCOUNTFAN?'myfanpage':'myartpage')));
			break;
	}
	die();
}


function mx_mnviewprof($page,$option,$action) {
	global $mxuser;
	$dbuser=$mxuser->getuserinfo($action); // action=id
	if (!$dbuser) {
		mx_optiontitle('error',_('Profile is Not accessible.'));
		return;
	}
	if ($dbuser->acctype==MXACCOUNTFAN || $option=='viewprof') {
		mx_optionsubtitle('&rarr; '.($dbuser->fullname?
		$dbuser->fullname:($dbuser->firstname.' '.$dbuser->lastname)));
	} else {
		mx_optionsubtitle('&rarr; '.($dbuser->artistname?
		$dbuser->artistname:$dbuser->fullname));
	}
	$authflds=$mxuser->getauthorizedfields($dbuser->id);
	$authgrps=$mxuser->getauthorizedgroups($authflds);
	$section='';
	if (!$authgrps || !$authflds) {
		__('No information available.');
		return;
	}
	echo '<form name="profile" method="POST">';
	for ($i=0; $i<2; $i++) {
		$action=(!$i?'edit':'');
		foreach ($mxuser->infogroups() as $group => $details) {
			if (!$authgrps || !array_key_exists($group,$authgrps)) continue;
			if ($section=='') $section=$group;
			$form=0;
			if ($action=='edit') $form=1;
			if ($form) {
				$edit='';
				$style='form';
			} else {
				if ($mxuser->status==MXACCTTRUSTFUL)
					$edit='<a href="javascript:tabswitch(\''.$group.'\',\'f_'.$group.'\');"' .
					' alt="'.sprintf(_('Edit %s Information'),$details[0]).'">'.mx_icon('edit','',16).'</a>';
				else $edit='';
				$style='info';
			}
			echo '<div id="'.($form?'f_':'').$group.'" class="'.$style.(($section==$group && !$form)?'':' hidden').'">';
			echo '<table><tr><td>';
			echo '<fieldset>';
			$fldarray=$mxuser->infogroups();
			foreach($fldarray as $grp => $det) {
				$profflds[$grp]=array(-1,$det[0]);
				foreach ($det[1] as $fld) {
					$profflds[$fld]=$mxuser->fielddesc($fld,true); // fld description for prof!
					$profvalue[$fld]=$dbuser->$fld;
				}
				$profflds['acc_id']=array(1,$dbuser->id,'hidden');
				$profflds['a']=array(1,'modifyprof','hidden');
			}
			$buttons=array(
				'submit' => _('Submit'),
				'clear' => _('Clear')
			);
			$profinfo=array(
				'profinfo',0,sprintf(_('Profile Information for %s'),mx_getname($dbuser)),
				_('Find below the details about this user'),
				$buttons,
				$profflds
			);
			//echo '<xmp>'.print_r($profvalue).'</xmp>';
			mx_showform($profinfo,$profvalue,$form);

			echo '</fieldset>';
			echo '</td></tr>';
			if ($form) {
				echo mx_formfield('a','submit','hidden');
				echo '<tr><td class="buttons">';
				echo mx_formfield('submit',_('Submit'),'submit').'&nbsp';
				echo mx_formfield('reset',_('Clear'),'reset');
			}
			echo '</td></tr>';
			echo '</table>';
			echo '</div>';
		}
	}
	echo '</form>';
}

function mx_showartstats($user) {
	echo '<h3>'._('Some Stats').'</h3>';

}

function mx_mnaccount($page,$option,$action) {
	global $mxuser;
	//mx_showbigmenu($page);
	if (!$option) {
		if ($mxuser->acctype==MXACCOUNTARTIST && MXBETA) mx_showartstats($mxuser);
		else mx_mnmyfanpage($page, $option, $action);
	}
}

function mx_mnacctype($page,$option,$action) {
	global $mxuser;
	if (!$mxuser->acctype) {
		switch ($action) {
			case 'submit':
				$acctype=mx_onlynumbers($_POST['acctype']);
				//if ($acctype>=0 && $acctype<=2) {
					$mxuser->setoption('acctype',$acctype);
				//}
				echo sprintf(_('Your account type is currently defined as: <i>%s Account</i>.'),
					mx_infofield('acctype',$acctype,$mxuser->fielddesc('acctype')));
				mx_showhtmlpage('acctype');
				break;
			default:
				$buttons=array(
					'submit' => _('Submit'),
					'clear' => _('Clear')
				);
				__('Your account type is currently <i>undefined</i>.');
				$acctypeform=array(
					'acctype',0,_('Please define your Account Type'),
					_('<p><b>Fan Accounts</b> will give you access to free material' .
							' and allow you to become an official fan and access all past stuff' .
							' and new releases from artists you will become fan of.</p>' .
							'<p><b>Artist Accounts</b> allow you to' .
							' upload your productions and maintain the contact with your fan' .
							' base, but also to be a fan yourself.' .
							'</p>'),
					$buttons,
					array(
						'help' => array(-1,_('Account Type Selection'),_('Who are you?')),
						'acctype' => array(1,_('Type:'),'acctype',40),
						'a' => array(1,'submit','hidden')
					)
				);
				mx_showform($acctypeform,'');
		}
		return;
	}
	echo sprintf(_('Your account type is currently defined as: <i>%s Account</i>.'),
		mx_infofield('acctype',$mxuser->acctype,$mxuser->fielddesc('acctype')));
	mx_showhtmlpage('acctype');
}

function mx_orderlink($field) {
	$newqstr=$_SERVER['QUERY_STRING'];
	$newqstr=preg_replace('%(&?s=[^&/]+)%','',$newqstr);
	$newqstr.='&s='.$field;
	return ' <a class="listorder" href="'.$_SERVER['PHP_SELF'].'?'.$newqstr.'">'.mx_icon('downarrow',null,16).'</a>';
}

function mx_mnhelptech($page,$option,$action) {
	global $browser;
	$buttons=array(
			'submit' => _('Submit'),
			'clear' => _('Clear')
		);
	$techinfo=array(
			'techinfo',0,_('Technical Information'),_('This section intends to help' .
					' you list the details of your browsing experience here.' .
					' By supplying us these details in the case of any issue,' .
					' we will be able to solve your problem faster.'),
			$buttons,
			array(
				'browser' => array(-1,_('Browser Information'),_('This is the information' .
						' we get about your current browser.')),
				'bname' => array(0,_('Name:'),'text'),
				'bversion' => array(0,_('Version'),'text'),
				'bplatform'  => array(0,_('Platform'),'text'),
				'buseragent'  => array(0,_('User Agent'),'text'),
				'a' => array(1,'submit','hidden')
			)
		);
		mx_showform($techinfo,array(
			'bname' => $browser->getBrowser(),
			'bversion' => $browser->getVersion(),
			'bplatform' => $browser->getPlatform(),
			'buseragent' => $browser->getUserAgent()
		),false); //cannot submit
}

function mx_getartistname($user) {
	if ($user->artistname) return $user->artistname;
	return mx_getname($user);
}

function mx_getname($user) {
	if ($user->fullname) return $user->fullname;
	if ($user->firstname) return trim($user->firstname.' '.$user->lastname);
	if ($user->artistname) return $user->artistname;
	if ($user->id) return _('New User');
	return _('Visitor');
}


function mx_mnsubstuff($page,$option,$action) {
	// redirected to media/mysubs
}

function mx_subscribers($artistid,$likers=false) {
	global $mxdb;
	$subs=$mxdb->getsubscribers($artistid,$likers);
	if ($subs) {
		$subvalues=array();
		$subtable['subscribers']=array(
			'fanid' => array(0,_('Name'),'fan'),
			'gender' => array(0,_('Sex'),'gender'),
			'city' => array(0,_('City'),'text'),
			'state' => array(0,_('State'),'text'),
			'country' => array(0,_('Country'),'text'),
			'firstsub' => array(0,_('Since'),'date'),
		);
		foreach ($subs as $k => $sub) {
			$subvalues['subscribers'][]=$sub;
		}
		return mx_showtablestr($subtable,$subvalues,'fanships',array(),'subscribers');
	} else {
		return ($likers?_('No likers yet.'):_('No fans yet.'));
	}
}

function mx_subscriptions($subs,$likers=false) { // reformat the given subs (from getsub())
	global $mxdb,$mxuser;
	if ($subs) {
		$subvalues=array();
		$subtable['subscribers']=array(
			'objectid' => array(0,_('Artist/Band'),'artist'),
			//'city' => array(0,_('City'),'text'),
			//'state' => array(0,_('State'),'text'),
			'country' => array(0,_('Country'),'text'),
			'firstsub' => array(0,_('Since'),'date'),
			'subtype' => array(0,_('Type'),'subtype'),
		);
		foreach ($subs as $k => $sub) {
			if (($likers && $sub->subcat==MXARTSUB && $sub->subtype==MXSUBLIKE)
			|| (!$likers && $sub->subcat==MXARTSUB && $sub->subtype!=MXSUBLIKE
			&& $sub->expiry && (strtotime($sub->expiry)>time() || $sub->expiry=='9999-01-01'))) {
				$asub=$sub;
				$user=$mxuser->getuserinfo($sub->artistid);
				$asub->city=$user->city;
				$asub->state=$user->state;
				$asub->country=$user->country;
				if (!$asub->city) $asub->city='-';
				if (!$asub->state) $asub->state='-';
				if (!$asub->country) $asub->country='-';
				$subvalues['subscribers'][]=$asub;
			}
		}
		//error_log(print_r($subvalues,true));
		if (!array_key_exists('subscribers',$subvalues))
			return $likers?_('No likes yet.'):_('No fanships yet.');
		return mx_showtablestr($subtable,$subvalues,'subscriptions',array(),'subscribers');
	} else {
		return $likers?_('No likes yet.'):_('No fanships yet.');
	}
}

function mx_mnmysubs($page,$option,$action) {
	global $mxuser;
	if (!$action) mx_showhtmlpage('mysubs');
	$subs=$mxuser->getsub();
	$mxuser->setsubseen();
	$subvalues=array();
	foreach ($subs as $k => $sub) {
		if ($sub->subcat==MXARTSUB && $sub->subtype==MXSUBLIKE) {
			$subvalues['likes'][]=$sub;
		} else if ($sub->expiry && strtotime($sub->expiry)<time() && $sub->expiry!='9999-01-01') {
			$subvalues['expired'][]=$sub;
			$subvalues['all'][]=$sub;
		} else {
			$subvalues['active'][]=$sub;
			$subvalues['all'][]=$sub;
		}
		/*if ($sub->status==MXNEWSUB || $sub->status==MXRENEWEDSUB)
			$subvalues['new'][]=$sub;*/
	}
	$subform=array(
		'subform',0,_('Fanships, Subscriptions and Likes'),
		_('Below is the list of artists, medias and site-wide subscriptions you purchased, and whatever else you liked'),
		array(),
		array(
			/*'new' => array(
				'new' => array(-1,_('New'),
				_('Your newly activated fanships')),
				'artistid' => array(1,_('Artist'),'artist',40),
				'firstsub' => array(1,_('Initial fanship'),'date'),
				'subtype' => array(1,_('Type'),'subtype',10),
				'expiry' => array(1,_('Expiry:'),'expdate'),
				'status' => array(1,_('Status:'),'substatus'),
			),*/
			'active' => array(
				'active' => array(-1,_('Active'),
				_('Your Active Subscriptions')),
				'subdesc' => array(0,_('Subscription'),'html'),
				'firstsub' => array(0,_('Since'),'date'),
				'subcat' => array(0,_('Type'),'subcat'),
				'newsubtype' => array(0,_('Type'),'newsubtype'),
				//'subtype' => array(0,_('Type'),'subtype'),
				'expiry' => array(0,_('Expiry'),'expdate'),
				'status' => array(0,_('Status'),'substatus'),
				'renewal' => array(0,_('Renewal'),'subrenewal'),
				'renewaldate' => array(0,_('Renewal Date'),'date'),
				),
			'expired' => array(
				'expired' => array(-1,_('Expired'),
				_('Your Expired Subscriptions')),
				'subdesc' => array(0,_('Subscription'),'html'),
				'firstsub' => array(0,_('Since'),'date'),
				'subcat' => array(0,_('Type'),'subcat'),
				'newsubtype' => array(0,_('Type'),'newsubtype'),
				//'subtype' => array(0,_('Type'),'subtype'),
				'expiry' => array(0,_('Expiry'),'expdate'),
				'status' => array(0,_('Status'),'substatus'),
				'renewal' => array(0,_('Renewal'),'subrenewal'),
				),
			'all' => array(
				'all' => array(-1,_('All'),
				_('All your Active and Expired Subscriptions')),
				'subdesc' => array(0,_('Subscription'),'html'),
				'firstsub' => array(0,_('Since'),'date'),
				'subcat' => array(0,_('Type'),'subcat'),
				'newsubtype' => array(0,_('Type'),'newsubtype'),
				//'subtype' => array(0,_('Type'),'subtype'),
				'expiry' => array(0,_('Expiry'),'expdate'),
				'status' => array(0,_('Status'),'substatus'),
				'renewal' => array(0,_('Renewal'),'subrenewal'),
				),
			'likes' => array(
				'likes' => array(-1,_('Likes'),
				_('Your favorite stuff ("Likes")')),
				'subdesc' => array(0,_('Subscription'),'html'),
				'firstsub' => array(0,_('Since'),'date'),
				'newsubtype' => array(0,_('Type'),'newsubtype'),
				//'subtype' => array(0,_('Type'),'subtype'),
				'status' => array(0,_('Status'),'substatus'),
				),
		)
	);
	mx_showlist($subform,$subvalues,'subscriptions',false,true);
}

function mx_mnmyfanpage($page,$option,$action) {
	global $mxuser;
	if ($mxuser->acctype==MXACCOUNTFAN
	 ||$mxuser->acctype==MXACCOUNTARTIST)
		mx_mnfanprof('fans','fanprof',$mxuser->id,$action);
}


function mx_mnmyartpage($page,$option,$action) {
	global $mxuser;
	if ($mxuser->acctype==MXACCOUNTARTIST
	 ||$mxuser->acctype==MXACCOUNTBAND)
		mx_mnartprof('artists','artprof',$mxuser->id,$action);
}

function mx_mnwhoswhere($page,$option,$action) {
	global $mxuser;
	mx_showhtmlpage('whoswhere');
	$mxuser->whoswhere();
}

function mx_mnpwdreset($page,$option,$action) {
	global $mxuser,$mxsession;
	mx_showhtmlpage('pwdreset');
	$mxuser->pwdreset();
	mx_setsession($mxuser,time());
}


function mx_mnsetup($page,$option,$action) {
	global $mxuser,$errors,$me,$facebook;
	if ($action=='done') {
		$buttons=array();
		$setupform=array(
			'setup',0,_('You\'re Done!'),
			'',
			$buttons,
			array(
				'confirmlabel' => array(-1,_('OPEN SESAME'),
					_('<p>Your account is now fully setup!</p><p>We know it was a bit painful,'
					.' but you did really well and we\'re happy to unlock all features of MusXpand'
					.' for you.</p><p>Note that you will always be able to make changes'
					.' to your profile whenever you want, by using the menu on the left.</p>'
					.'<p>Now, feel at home, sit comfortably and start enjoying!</p>')),
				'a' => array(1,'done','hidden')
			)
		);
		mx_showform($setupform,array(),false,true);
		switch ($mxuser->acctype) {
			case MXACCOUNTFAN:
				$typ='fan';
				$url=mx_actionurl('fans','fanprof',$mxuser->id);
				break;
			case MXACCOUNTARTIST:
				$typ='artist';
				$url=mx_actionurl('artists','artprof',$mxuser->id);
		}
		mx_fbaction('musxpand:register_as?'.$typ.'='.urlencode($url));
		?>

<!-- Google Code for Setup Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 949396365;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "-NVjCOPdzwMQjcfaxAM";
var google_conversion_value = 0;
if (2) {
  google_conversion_value = 2;
}
/* ]]> */
</script>
<script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/949396365/?value=2&amp;label=-NVjCOPdzwMQjcfaxAM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

		<?php
		return;
	}
	$steps=array(1 => _('Account Type'),
		_('Basic Information'),
		_('Additional Details'),
		_('Profile Customization'),
		_('Privacy & Notifications'));
	$section=preg_replace('%[^0-9]%','',$action); // just keep step number
	if ($section=='0') {
		$buttons=array(
			'+setup_1' => _('Continue to Account Setup'),
		);
		$setupform=array(
			'setup',0,_('Account Confirmation'),
			'',
			$buttons,
			array(
				'confirmlabel' => array(-1,_('Account Activated'),
					_('<p>Your account is now confirmed!</p><p><b>We just sent you a temporary password,'
					.' so that you can sign into your account any time.</b> You will be able to change this'
					.' password later on in your profile.</p><p>We\'ll now go through'
					.' a few more steps to set up your profile.</p>')),
				'a' => array(1,'confirmation','hidden')
			)
		);
		mx_showform($setupform,array(),true,true);
		?>

<!-- Google Code for Registered Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 949396365;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "EFxUCOvczwMQjcfaxAM";
var google_conversion_value = 0;
/* ]]> */
</script>
<script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/949396365/?label=EFxUCOvczwMQjcfaxAM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

		<?php
		return;
	}
	if (!$section || $section>count($steps)) {
		$section=1;
	}
	$fldarray=$mxuser->infogroups($section,$mxuser->acctype);
	foreach($fldarray as $grp => $det) {
		if ($grp=='location') $location=mx_locate();
		$bandflds[$grp]=array(-1,$det[0]);
		foreach ($det[1] as $fld) {
			$bandflds[$fld]=$mxuser->fielddesc($fld,false); // fld description for users!
			if ($bandflds[$fld][0]==2
			 && $mxuser->acctype!=MXACCOUNTARTIST  && $mxuser->acctype!=MXACCOUNTBAND) {
			 	unset($bandflds[$fld]);
			 	continue;
			 }
			 if ($fld=="password") {
			 	$bandflds[$fld][5]=true; // allow password changes
			 }
			 $bandvalue[$fld]=$mxuser->$fld;
		}
		$bandflds['a']=array(1,'modifyuser','hidden');
		$bandflds['k']=array(1,$section,'hidden');
	}
	if ($section>1) $buttons['setup_'.($section-1)]='&larr; '.$steps[$section-1]; // previous step
	if ($section<count($steps)) {
		$buttons['+setup_'.($section+1)]=$steps[$section+1].' &rarr;'; // next step
		if ($section>1) $buttons['done']=_('I\'ll finish later!').' &rarr;';
	}
	if ($section==count($steps)) $buttons['+done']=_('I\'m Done!').' &rarr;';
	$buttons['clear']=_('Clear');
	$bandinfo=array(
		'setup',0,sprintf(_('Setup: %s %s'),$steps[$section],($section>1?('('.$section.'/'.count($steps).')'):'')),
		_('Please fill in as appropriate'),
		$buttons,
		$bandflds
	);
	//echo '<xmp>'.print_r($bandvalue).'</xmp>';
	if (array_key_exists('city',$bandflds)) { // auto fill geographical location
		$location=mx_locate();
		if ($location) {
			$locvalues=array(
				'city' => iconv('ISO-8859-1','utf-8',$location->city),
				'state' => mx_region($location),
				'country' => $location->countryCode
			);
			$bandvalue=array_replace($bandvalue,$locvalues);
		}

	}
	mx_showform($bandinfo,$bandvalue,true,true,$errors);

?>
<script language="javascript">
    function createUploader(){
        var uploader = new qq.FileUploader({
            element: document.getElementById('fileuploader'),
            action: '<?php echo mx_option('siteurl').'/picupload.php'; ?>',
            params: {
            	id: '<?php echo $mxuser->id; ?>'
            },
            allowedExtensions: ['jpg','jpeg','png'],
			sizeLimit: 2000000,
			onComplete: function(id, fileName, responseJSON){
				if (responseJSON.success) {
					var mypic=document.getElementById('newpic');
					if (mypic) mypic.src='<?php echo mx_option('usersURL')
					.'/tmp/'; ?>'+encodeURIComponent(fileName);
					var piclist=document.getElementById('piclist');
					if (piclist) {
						listelem=document.createElement('li');
						input=document.createElement('input');
						input.type='radio';
						input.name='picture';
						input.value=responseJSON['link'];
						input.checked=true;
						input.onclick=function() {
							mypic.src='<?php echo mx_option('usersURL')
							.'/tmp/'; ?>'+encodeURIComponent(fileName);
						}
						var radiolabel=document.createTextNode(fileName);
						listelem.appendChild(input);
						listelem.appendChild(radiolabel);
						piclist.appendChild(listelem);
					}
				}
            }
        });
    }

    // in your app create uploader as soon as the DOM is ready
    // don't wait for the window to load
    //if (window.onload) {
    //	var oldloadfunction=window.onload;
    //	window.onload = (typeof window.onload != 'function') ? createUploader : function() { oldloadfunction(); createUploader(); };
    //} else window.onload = createUploader;
    createUploader();
</script>
<?php
}

function mx_ckinvites($page,$option,$action) {
	global $mxuser,$to;
	if ($action=='sendinvites') {
		$names=$_REQUEST['names'];
		$emails=$_REQUEST['emails'];
		$to=array();
		$msg=mx_showhtmlpagestr('friendinvite');
		foreach ($names as $i => $name) {
			if ($emails[$i] && preg_match('%^[^@]+@([^.]+\.)+[^.]+$%',$emails[$i])>=0) {
				$recip=ucwords($name).' <'.$emails[$i].'>';
				if (mx_emailexists($emails[$i])) $to[]=htmlentities($recip).' &rarr; <b>'._('Already Registered!').'</b>';
				else {
					$to[]=htmlentities($recip).' &rarr; '._('Email Sent.');
					mx_sendmail($recip,_('Invitation to MusXpand.'),mx_html2text($msg),$msg);
				}
			}
		}
	}
}

function mx_mninvites($page,$option,$action) {
	global $mxuser,$to;
	echo '<div class="mx-message">';
	//mx_showhtmlpage('invites');
	//echo '<p>'.sprintf(_('You have %d invites left.'),MXMAXINVITES-$mxuser->invites).'</p>';
	if ($action=='sendinvites' && is_array($to) && count($to)>0) {
		echo '<div class="invitessent"><ul>';
		foreach ($to as $recip)
			echo '<li>'.$recip.'</li>';
		echo '</ul></div>';
	}
	$invitetbl=array(
		'invitelist',0,_('Invites'),'',
		array(
			'invites' => array(
				'submit' => _('Send'),
			)
		),
		array(
			'invites' => array(
				'invites' => array(-1,_('Invites'),_('Please enter the names and emails of the people you want'
				.' to invite to MusXpand.').'<br/>'._('You can click on the question mark on the right to get more information about Reward Points')),
				//'select' => array(1,'<input id="checkallbox" type="checkbox" onclick="javascript:checkall(\'media\');">','text',3),
				'names[]' => array(1,_('Name'),'text','30',null,_('Your friend\'s first or fullname')),
				'emails[]' => array(1,_('Email'),'text','40',null,_('Your friend\'s email address')),
				'a' => array(1,'sendinvites','hidden'),
			),
		),
	);
	$invite=null;
	$invite->name='';
	$invite->email='';
	for ($i=0; $i<10; $i++)
		$invites['invites'][]=$invite;
	$str='<div class="form invites"><form name="invites" method="POST" enctype="multipart/form-data"'
			.' onsubmit="return checkform(\'invites\');">';
	$str.=mx_showtablestr($invitetbl[5],$invites,'invites',$invitetbl[4],'invites');
	$str.='</form></div>';
	// show invite URL
	$registerurl=mx_optionurl('account','register').'?i='.$mxuser->invitecode;
	$str.='<p>'.sprintf(_('Alternatively, you can send your friend an email informing the link below,'
	.' that will also allow them to register, or post the link on Facebook, Twitter... so that anyone in your network can be one of your referees:<br/>%s<br/><center>(copy and paste the complete link to your email)</center>'),'<center><b>'.$registerurl.'</b></center>').'</p>';
	echo $str;
	echo '</div>';
}

function mx_ckfbsetup($page,$option,$action) {
	global $mxuser,$facebook;
	if (!$mxuser->fbdata['page']['admin']) return; // weird, what is user doing here?
	$pageid=$mxuser->fbdata['page']['id'];
	switch ($action) {
		case 'fbsetup':
			if ($pageid) $mxuser->setaccountforpage($pageid);
			break;
		case 'fbauthorize':
			header('Location: https://www.facebook.com/dialog/oauth?client_id='.FACEBOOK_APP_ID
				.'&redirect_uri='.urlencode(mx_actionurl('account','fbsetup','fbauthorized','','','','page='.$pageid))
				.'&scope=manage_pages&response_type=token');
			break;
		case 'fbauthorized':
			header('Location: http://www.facebook.com/'.$_GET['page'].'?sk=app_'.FACEBOOK_APP_ID);
			break;
		case 'fbaddpage':
			$selpage=$_REQUEST['selpage'];
			foreach($selpage as $page) {
				//error_log('page id='.$page);
				$facebook->api('/'.$page.'/tabs','POST',array(
					'app_id' => FACEBOOK_APP_ID
				));
			}
			break;
		case 'fbdelpage':
			$selpage=$_REQUEST['selpage'];
			foreach($selpage as $page) {
				//error_log('page id='.$page);
				$facebook->api('/'.$page.'/tabs/'.FACEBOOK_APP_ID,'DELETE');
			}
			break;
	}
}

function mx_mnfbsetup($page,$option,$action) {
	global $mxuser,$facebook;
	if (!$mxuser->fbdata['page']['admin']) {
		//error_log('User '.$mxuser->id.' ('.$mxuser->getname().') is on fbsetup for someone else\'s page!!');
		return; // weird, what is user doing here?
	}
	$pageuser=mx_getaccountfrompage($mxuser->fbdata['page']['id']);
	//$perms=$facebook->api('/me/permissions','GET');
	//error_log('perms='.print_r($perms,true));
	$buttons=array();
	$setupvalues=array();
	/*
	if ($perms['data'][0]['manage_pages']==1) {
		$fbptitle=_('Add/Remove MusXpand\'s application to/from these pages');
		$setuppages['fbpages']=array();
		$pages=$facebook->api('/me/accounts','GET');
		$setupvalues['fbpages']=array();
		foreach ($pages['data'] as $page) {
			$fbpage=new stdClass();
			foreach($page as $k => $v) {
				$fbpage->$k=$v;
			}
			$fbpage->select='<input type="checkbox" name="selpage[]" value="'.$page['id'].'">';
			$pic=$facebook->api('/'.$page['id'],'GET');
			if (array_key_exists('picture',$pic)) $fbpage->pic=$pic['picture'];
			else $fbpage->pic='';
			if (mx_getaccountfrompage($page['id'])==$mxuser->id) $fbpage->set=mx_icon('okmark');
			else $fbpage->set='';
			$setupvalues['fbpages'][]=$fbpage;
		}
		print_r($setuppages,true);
		$buttons['fbpages']=array(
			'fbaddpage' => _('Add to Page'),
			'fbdelpage' => _('Remove from Page'),
		);
	} else {
		$fbptitle=sprintf(_('If you want us to add/remove MusXpand in your pages,'
		.' you need to give us the corresponding Facebook permission ("<b>manage_pages</b>")'
		.' by clicking the Authorize button below.<br/>'
		.'Alternatively, you can visit our %s and add it to your pages,'
		.' although you will also have to set them up too afterwards...'),
		'<a href="http://www.facebook.com/apps/application.php?id=151278924909082&ref=ts">'._('Facebook App page').'</a>');
		$buttons['fbpages']=array(
			'fbauthorize' => _('Authorize'),
		);
	}
	*/
	switch ($action) {
		case 'fbsetup':
			if ($pageuser==$mxuser->id) {
				$msg=_('This page is now set up to this MusXpand account.');
				$title=_('Setup done');
			} else {
				$msg=sprintf(_('This Facebook page could not be associated with your account.<br/>Please %s.'),
				'<a href="mailto:support@example.com">'._('contact us').'</a>');
				$title=_('Error');
			}
			$buttons['thispage']=array();
			break;
		default:
			$msg='';
			if ($pageuser==$mxuser->id) {
				$msg.=_('This page is already setup to your MusXpand account.');
				$title=_('Setup done');
				$buttons['thispage']=array();
			} else {
				if ($pageuser>0) {
					$user=$mxuser->getuserinfo($pageuser);
					$msg.=sprintf(_('This page is already setup to: %s.',$user->artistname));
					$title=_('Warning');
				} else $title=_('Confirmation');
				if ($msg) $msg.='<br/>';
				$msg.=_('Please confirm you want to link this page to your MusXpand account.');
				$buttons['thispage']=array(
					'fbsetup' => _('Do it!'),
				);
			}
			break;
	}
	$setupforms=array(
		'thispage' => array(1, // type form
			'thispage' => array(-1,_('Linking your MusXpand Account'),'<b>'.$title.'</b>:<br/>'.$msg),
			'a' => array(1,'go','hidden')
		),
		/*
		'fbpages' => array(0, // type list
			'fbpages' => array(-1,_('Other Pages'),$fbptitle),
			'select' => array(0,'<input id="checkallbox" type="checkbox" onclick="javascript:checkall(\'fbpages\');">','html',3),
			'set' => array(0,'','html'), // checkmark
			'pic' => array(0,_('Picture'),'picture'),
			'name' => array(0,_('Page'),'text'),
			'category' => array(0,_('Category'),'text',20),
			//'id' => array(0,_('ID'),'text'),
			'a' => array(1,'go','hidden')
		)
		*/
	);
	$setuplist=array(
		'mxpages',0,_('Account Setup'),
		'',
		$buttons,
		$setupforms
	);
	mx_showlist($setuplist,$setupvalues,'fbpages',true,false);
}

function mx_mnplaystats($page,$option,$action) {
	global $mxuser;
	$playtable=array(
		'playstats',0,_('Media stats'),'',
		array(
		),
		array(
			'plays' => array(
				'plays' => array(-1,_('Media I played...'),_('These are the medias you played, watched or viewed (except yours).')),
				//'select' => array(1,'<input id="checkallbox" type="checkbox" onclick="javascript:checkall(\'media\');">','text',3),
				'owner_id' => array(0,_('Artist'),'artist',20),
				'mediaid' => array(0,_('Title'),'mediaplay','40'),
				'type' => array(0,_('Type'),'playtype'),
				'start' => array(0,_('When'),'date'),
				'playtime' => array(0,_('Playtime'),'playtime'),
				'played' => array(0,_('% played'),'percent'),
			),
			'medias' => array(
				'medias' => array(-1,_('My medias people played...'),_('These are your medias\' stats.'
					.' This does NOT include your own actions.'
					.' If a media has never been played or viewed, it won\'t appear here.')),
				//'select' => array(1,'<input id="checkallbox" type="checkbox" onclick="javascript:checkall(\'media\');">','text',3),
				'mediaid' => array(0,_('Title'),'mediaplay','40'),
				'type' => array(0,_('Type'),'playtype'),
				'playcnt' => array(0,_('Count'),'text'),
				'avgplaytime' => array(0,_('Avg.Playtime'),'playtime'),
				'avgplayed' => array(0,_('Avg. % played'),'percent'),
			),
			'topfans' => array(
				'topfans' => array(-1,_('My best fans'),_('Here you know who\'s playing your medias.'
					.' It does NOT include your own actions.')),
				//'select' => array(1,'<input id="checkallbox" type="checkbox" onclick="javascript:checkall(\'media\');">','text',3),
				'mediaid' => array(0,_('Title'),'mediaplay','40'),
				'type' => array(0,_('Type'),'playtype'),
				'userid' => array(0,_('User'),'fan',20),
				'playcnt' => array(0,_('Count'),'text'),
				'avgplaytime' => array(0,_('Playtime'),'playtime'),
				'avgplayed' => array(0,_('% played'),'percent'),
			),
			'topvisitors' => array(
				'topvisitors' => array(-1,_('Vistors\' most played'),_('Here are the medias that were most played by'
					.' non-members.')),
				//'select' => array(1,'<input id="checkallbox" type="checkbox" onclick="javascript:checkall(\'media\');">','text',3),
				'mediaid' => array(0,_('Title'),'mediaplay','40'),
				'type' => array(0,_('Type'),'playtype'),
				'playcnt' => array(0,_('Count'),'text'),
				'avgplaytime' => array(0,_('Playtime'),'playtime'),
				'avgplayed' => array(0,_('% played'),'percent'),
			),
		),
	);
	$playvalues=$mxuser->getplaystats();
	mx_showlist($playtable,$playvalues,'stats',false,false);

	//echo '<xmp>'.print_r($playvalues,true).'</xmp>';
}

function mx_mnmystats($page,$option,$action) {
	global $mxuser;
	echo mx_statsstr($mxuser);
}

function mx_statsstr($user) {
	global $mxuser;
	$mstats=$mxuser->getmstats($user);
	$tabmstats['monthstats']=array(
		'yy' => array(0,_('Year'),'text'),
		'mm' => array(0,_('Month'),'month'),
		'hits' => array(0,_('Page Views'),'integer'),
		'visits' => array(0,_('visits'),'integer'),
		'tothits' => array(0,_('Total Page Views'),'integer'),
		'totvisits' => array(0,_('Total Visits'),'integer'),
	);
	$valmstats=array('monthstats' => $mstats);
	$dstats=$mxuser->getdstats($user);
	$tabdstats['daystats']=array(
		'yy' => array(0,_('Year'),'text'),
		'mm' => array(0,_('Month'),'month'),
		'dd' => array(0,_('Day'),'text'),
		'hits' => array(0,_('Page Views'),'integer'),
		'visits' => array(0,_('Visits'),'integer'),
		'tothits' => array(0,_('Total Page Views'),'integer'),
		'totvisits' => array(0,_('Total Visits'),'integer'),
	);
	$valdstats=array('daystats' => $dstats);
	$str='<table class="userstats">';
	$str.='<tr>';
	$str.='<td><h5>'._('Monthly Graph (logarithmic)').'</h5></td>';
	$str.='<td><h5>'._('Daily Graph (logarithmic)').'</h5></td>';
	$str.='</tr><tr>';
	$str.='<td class="statsgraph"><div id="mxmstats"></div><br/><div id="mxmstatsleg"></div></td>';
	$str.='<td class="statsgraph"><div id="mxdstats"></div><br/><div id="mxdstatsleg"></div><br/>'
	._('Select an area to zoom')
	.'<div id="mxdstatsovw"></div>'
	.'</td>';
	$str.='</tr><tr>';
	$str.='<td class="statsdata">'.mx_showtablestr($tabmstats,$valmstats,'stats',array(),'monthstats').'</td>';
	$str.='<td class="statsdata">'.mx_showtablestr($tabdstats,$valdstats,'stats',array(),'daystats').'</td>';
	$str.='</tr></table>';

	$hits=$visits=$thits=$tvisits='';
	foreach ($mstats as $astat) {
		if (!$astat->yy || !$astat->mm) continue;
		$hits.=($hits?',':'').'['.bcmul(strtotime('1-'.($astat->mm).'-'.$astat->yy),1000).','.$astat->hits.'] ';
		$visits.=($visits?',':'').'['.bcmul(strtotime('1-'.($astat->mm).'-'.$astat->yy),1000).','.$astat->visits.'] ';
		$thits.=($thits?',':'').'['.bcmul(strtotime('1-'.($astat->mm).'-'.$astat->yy),1000).','.$astat->tothits.'] ';
		$tvisits.=($tvisits?',':'').'['.bcmul(strtotime('1-'.($astat->mm).'-'.$astat->yy),1000).','.$astat->totvisits.'] ';
	}
	$str.='<script>';
	$str.='var mhits=['.$hits.'];';
	$str.='var mvisits=['.$visits.'];'."\n";
	$str.='var mthits=['.$thits.'];';
	$str.='var mtvisits=['.$tvisits.'];'."\n";

	$astat=null;
	$hits=$visits=$thits=$tvisits='';
	$i=0;
	foreach ($dstats as $astat) {
		if ($i==365) break;
		if (!$astat->yy || !$astat->mm || $astat->dd==0) continue;
		$hits.=($hits?',':'').'['.bcmul(strtotime($astat->dd.'-'.($astat->mm).'-'.$astat->yy),1000).','.$astat->hits.'] ';
		$visits.=($visits?',':'').'['.bcmul(strtotime($astat->dd.'-'.($astat->mm).'-'.$astat->yy),1000).','.$astat->visits.'] ';
		$thits.=($thits?',':'').'['.bcmul(strtotime($astat->dd.'-'.($astat->mm).'-'.$astat->yy),1000).','.$astat->tothits.'] ';
		$tvisits.=($tvisits?',':'').'['.bcmul(strtotime($astat->dd.'-'.($astat->mm).'-'.$astat->yy),1000).','.$astat->totvisits.'] ';
		$i++;
	}
	$str.='var dhits=['.$hits.'];';
	$str.='var dvisits=['.$visits.'];'."\n";
	$str.='var dthits=['.$thits.'];';
	$str.='var dtvisits=['.$tvisits.'];'."\n";

	$str.='function showTooltip(x, y, contents) {
        $(\'<div id="tooltip">\' + contents + \'</div>\').css( {
            position: \'absolute\',
            display: \'none\',
            top: y + 5,
            left: x + 5,
            border: \'1px solid #fdd\',
            padding: \'2px\',
            \'background-color\': \'#fee\',
            opacity: 0.80
        }).appendTo("body").fadeIn(200);
    }
	$.plot($("#mxmstats"),
	[ { data: mhits, label: "'._('Hits - My Page').'" },
		{ data: mthits, label: "'._('Hits - Site').'" },
    	{ data: mvisits, label: "'._('Visits - My Page').'" },
    	{ data: mtvisits, label: "'._('Visits - Site').'" }],
	{
		xaxes: [ { mode: \'time\',tickLength: 5 } ],
		yaxis: { transform: function (v) { return Math.log(v+1); },
	    		inverseTransform: function (v) { return Math.floor(Math.exp(v)-1); },
	    		labelWidth:30,
			},
		/*yaxes: [ { transform: function (v) { return Math.log(v+1); },
	    		inverseTransform: function (v) { return (Math.exp(v)-1); }},
			{ transform: function (v) { return Math.log(v+1); },
	    		inverseTransform: function (v) { return (Math.exp(v)-1); },
	    		position:\'right\' } ],*/
		legend: { position: \'nw\', noColumns: 2, container: $("#mxmstatsleg") },
		series: {
                   lines: { show: true },
                   points: { show: true }
               },
		grid: { hoverable: true,
			clickable: true,
			backgroundColor: \'#eff2f7\'
			},
	});
	var previousPoint = null;
    $("#mxmstats").bind("plothover", function (event, pos, item) {
        $("#x").text(pos.x.toFixed(2));
        $("#y").text(pos.y.toFixed(2));

        //if ($("#enableTooltip:checked").length > 0) {
            if (item) {
                if (previousPoint != item.dataIndex) {
                    previousPoint = item.dataIndex;

                    $("#tooltip").remove();
                    var x = item.datapoint[0].toFixed(2),
                        y = item.datapoint[1].toFixed(2);

                        var dt=new Date(x);

                    showTooltip(item.pageX, item.pageY,
                                item.series.label + ": " + Math.floor(y));
                }
            }
            else {
                $("#tooltip").remove();
                previousPoint = null;
            }
        //}
    });
    var options={
		xaxes: [ { mode: \'time\',tickLength: 5 } ],
		yaxis: { transform: function (v) { return Math.log(v+1); },
	    		inverseTransform: function (v) { return (Math.exp(v)-1); },
	    		labelWidth:30,
			},
		/*yaxes: [ { transform: function (v) { return Math.log(v+1); },
	    		inverseTransform: function (v) { return Math.floor(Math.exp(v)-1); }},
			{ transform: function (v) { return Math.log(v+1); },
	    		inverseTransform: function (v) { return (Math.exp(v)-1); },
	    		position:\'right\' } ],*/
				legend: { position: \'nw\', noColumns: 2, container: $("#mxdstatsleg") },
		series: {
                   lines: { show: true, fill:false,lineWidth:1 },
                   bars: { show:false },
                   points: { show: false }
               },
        selection: { mode: "x" },
		grid: { hoverable: true,
			backgroundColor: \'#eff2f7\'
			}
	};
	var plot=$.plot($("#mxdstats"),
		[ { data: dhits, label: "'._('Hits - My Page').'" },
		{ data: dthits, label: "'._('Hits - Site').'" },
    	{ data: dvisits, label: "'._('Visits - My Page').'" },
    	{ data: dtvisits, label: "'._('Visits - Site').'" }],
		options
	);
	$("#mxdstats").bind("plothover", function (event, pos, item) {
        $("#x").text(pos.x.toFixed(2));
        $("#y").text(pos.y.toFixed(2));

        //if ($("#enableTooltip:checked").length > 0) {
            if (item) {
                if (previousPoint != item.dataIndex) {
                    previousPoint = item.dataIndex;

                    $("#tooltip").remove();
                    var x = item.datapoint[0].toFixed(2),
                        y = item.datapoint[1].toFixed(2);

                        var dt=new Date(Math.floor(x));

                    showTooltip(item.pageX, item.pageY,
                                dt.toDateString()
                                + "<br/>" + item.series.label + ": " + Math.floor(y));
                }
            }
            else {
                $("#tooltip").remove();
                previousPoint = null;
            }
        //}
    });
    var overview = $.plot($("#mxdstatsovw"),
    	[ { data: dhits},
		{ data: dthits,yaxis:2},
    	{ data: dvisits, yaxis: 3 },
    	{ data: dtvisits, yaxis: 4 }],
    	{
	        series: {
	            lines: { show: true, lineWidth: 1 },
	            shadowSize: 0
	        },
	        xaxis: { ticks: [], mode: "time" },
	        yaxes: [{ ticks: [], min: 0, autoscaleMargin: 0.1 },
	        	{ ticks: [], min: 0, autoscaleMargin: 0.1 },
	        	{ ticks: [], min: 0, autoscaleMargin: 0.1,position:\'right\' },
	        	{ ticks: [], min: 0, autoscaleMargin: 0.1,position:\'right\' }],
	        selection: { mode: "x" }
	});
	$("#mxdstats").bind("plotselected", function (event, ranges) {
        // do the zooming
        plot = $.plot($("#mxdstats"),
        	[ { data: dhits, label: "Hits" },
		{ data: dthits, label: "Total Hits" },
    	{ data: dvisits, label: "Visits", yaxis: 1 },
    	{ data: dtvisits, label: "Total Visits", yaxis: 1 }],
                      $.extend(true, {}, options, {
                          xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to }
                      }));

        // don\'t fire event on the overview to prevent eternal loop
        overview.setSelection(ranges, true);
    });
    $("#mxdstatsovw").bind("plotselected", function (event, ranges) {
        plot.setSelection(ranges);
    });';
	$str.='</script>';
	return $str;
}


function mx_ckdelacct($page,$option,$action) {
	global $mxuser,$delconf;
	$delconf=false;
	if ($action=='accdelok') {
		$pwd=$_REQUEST['pass'];
		if ($mxuser->checklogin($mxuser->email,$pwd)) {
			$delconf=true;
		}
	}
}

function mx_mndelacct($page,$option,$action) {
	global $mxuser,$delconf;
	if ($action=='accdelok' && !$delconf) $action='';
	switch ($action) {
		case 'accdelno':
			$buttons=array();
			$deleteform=array(
				'deleteform',0,_('Account Deletion'),
				'',
				$buttons,
				array(
					'confirmlabel' => array(-1,_('Hey, Good News!'),
						_('<p>We\'re happy you decided to stay with us.<br/>'
						.'And don\'t forget we\'re here to help if you need us, so don\'t hesitate!</p>'
						.'<p>Thanks for your renewed trust!</p>')),
					'a' => array(1,'done','hidden')
				)
			);
			mx_showform($deleteform,array(),false,true);
			break;
		case 'accdelok':
			error_log('deleting account '.$mxuser->id);
			$medias=$mxuser->listbundles($mxuser->id);
			while ($medias && $media=$mxuser->listbundles($mxuser->id,$medias)) {
				//echo $media->title.'<br/>';
				$mxuser->deletemedia($media->id,$media->filename,true);
				error_log('deleted '.$media->title);
			}
			$medias=$mxuser->listmedia($mxuser->id);
			while ($medias && $media=$mxuser->listmedia($mxuser->id,$medias)) {
				//echo $media->title.'<br/>';
				$mxuser->deletemedia($media->id,$media->filename,true);
				error_log('deleted '.$media->title);
			}
			$mxuser->setoption('status', MXACCTDISABLED);
			$buttons=array();
			$deleteform=array(
				'deleteform',0,_('Account Deletion'),
				'',
				$buttons,
				array(
					'confirmlabel' => array(-1,_('YOU\'RE DONE...'),
						_('<p>All your medias were deleted from our database and servers and your account was successfully removed.</p>'
							.'<p>We\'re sorry to see you go, and we hope you had at least some fun with us.</p>'
							.'<p align="right">Good Luck!</p>')),
					'a' => array(1,'done','hidden')
				)
			);
			mx_showform($deleteform,array(),false,true);
			break;
		default:
			$buttons=array(
				'accdelok' => _('OK'),
				'accdelno' => ('Cancel')
			);
			$pass=array(1,_('Password'),'password');
			$deleteform=array(
				'deleteform',0,_('Account Deletion'),
				'',
				$buttons,
				array(
					'confirmlabel' => array(-1,_('Almost there...'),
						_('<p>Are you sure you want to do that...?</p>'
						.'<p><b>All your medias will be deleted from MusXpand and your account will be removed.</b><br/>'
						.'You will also <b>lose all friendships</b> you built on MusXpand, and the <b>access to any artist'
						.' you subscribed</b>. Have you thought about downloading their media beforehand...?</p>'
						.'<p>Also note that <u>for security reasons, you will not be able to use the same email address and/or Facebook account again</u>,'
						.' should you decide to come back and re-register in the future. <b>Deleted accounts can\'t be recovered.</b></p>'
						.'<p>Please confirm your identity by entering your password below.<br/>'
						.'After clicking OK, we\'ll proceed to the permanent removal of your account.</p>')
						),
					'pass' => array(1,_('Current Password'),'password'),
					'a' => array(1,'done','hidden')
				)
			);
			mx_showform($deleteform,array(),true,true);
	}
}

$mxuser=new MXUser();



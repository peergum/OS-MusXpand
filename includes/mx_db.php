<?php
/* ---
 * Project: musxpand
 * File:    mx_db.php
 * Author:  phil
 * Date:    28/09/2010
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

include_once 'includes/mx_definitions.php';
include_once 'includes/mx_config.php';

class MXDb {
	private $dbid;
	private $usercache=array();
	private $mediacache=array();
	private $bundleinfocache=array();
	private $lmfbcache=array();
	private $fanshipcache=array();
	private $optioncache=array();
	private $friendship=array();
	private $fanpiccache=array();
	private $privacycache=array();

	// log queries
	function query($qstr) {
		$initime=microtime(true);
		$qq=$this->dbid->query($qstr);
		$endtime=microtime(true);
		//error_log(sprintf('Query [%.2fms]: %s',(($endtime-$initime)*1000),$qstr));
		return $qq;
	}

	function MXDb($server, $username, $password, $database) {
		//global $dbid;
		$this->dbid=new mysqli($server,$username,$password,$database);
		$dbid=$this->dbid;
		$dbid->set_charset('utf8');
		if (mysqli_connect_errno()) {
			die('DB Connect error ('.mysqli_connect_errno().')'.mysqli_connect_error());
		}
	}

	function addaddress($userid,$fields) {
		$dbid=$this->dbid;
		$qstr='INSERT INTO mx_address SET ';
		$i=0;
		foreach ($fields as $fldname => $fldvalue) {
			if ($i) $qstr.=', ';
			$qstr.=$fldname.'=\''.$fldvalue.'\'';
			$i=1;
		}
		$qstr.=', accountid='.$userid;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return 0;
		}
		return $dbid->insert_id;
	}

	function getaddress($userid,$addid) {
		$dbid=$this->dbid;
		$qstr='SELECT * FROM mx_address WHERE accountid='.$userid.' AND id='.$addid;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return array();
		}
		if (!$mxq->num_rows) return array();
		$qr=$mxq->fetch_array();
		$mxq->free();
		return $qr;
	}


	function clearaddresses($userid,$cartid) {
		$dbid=$this->dbid;
		$qstr='DELETE FROM mx_address WHERE accountid='.$userid.' AND ' .
				'cartid='.$cartid;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
		}
	}

	function deladdress($userid,$addid) {
		$dbid=$this->dbid;
		$qstr='DELETE FROM mx_address WHERE accountid='.$userid.' AND ' .
				'id='.$addid;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
		}
	}

	function setcart($userid,$cartid,$option,$value) {
		$dbid=$this->dbid;
		$qstr='UPDATE mx_cart SET '.$option.'=\''.$dbid->real_escape_string($value).'\''
		.', statusstamp=NOW() WHERE id='.$cartid.' AND accountid='.$userid;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
		}
	}

	function setcartbatch($userid,$cartid,$fields) {
		$dbid=$this->dbid;
		$qstr='UPDATE mx_cart SET ';
		$i=0;
		foreach ($fields as $fldname => $fldvalue) {
			if ($i) $qstr.=', ';
			$qstr.=$fldname.'=\''.$fldvalue.'\'';
			$i=1;
		}
		$qstr.=' WHERE accountid='.$userid.' AND id='.$cartid;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
		}
	}

	function getcartbytransaction($transactionid) {
		$dbid=$this->dbid;
		$qstr='SELECT * FROM mx_cart WHERE transactionid=\''.$transactionid.'\'';
		$mxq=$this->query($qstr);
		if (!$mxq) return NULL;
		$qr=$mxq->fetch_object();
		$mxq->free();
		return $qr;
	}

	function getcart($userid,$cartid=null,$oldcart=false) {
		$dbid=$this->dbid;
		if ($cartid && $oldcart) $qcond=' WHERE accountid='.$userid.' AND id='.$cartid;
		else $qcond=' WHERE accountid='.$userid.' AND status<'.MXCARTCONFIRMED;
		$mxq=$this->query('SELECT * FROM mx_cart'.$qcond);
		if ($mxq && $mxq->num_rows>0) {
			$qr=$mxq->fetch_object();
			$mxq->free();
			$mxq=$this->query('SELECT COUNT(id) FROM mx_cartline ' .
					'WHERE cartid='.$qr->id);
			if ($mxq && $mxq->num_rows>0) {
				$items=$mxq->fetch_row();
				$qr->items=$items[0];
			} else $qr->items=0;
		} else {
			$qr=new StdClass();
			if ($mxq) $mxq->free();
			if ($cartid) return null;
			$qstr='INSERT INTO mx_cart SET accountid='.$userid.
				', status='.MXCARTPENDING.', date=NOW()';
			$mxq=$this->query($qstr);
			if (!$mxq) {
				mxerror($dbid->error,__FILE__,__LINE__,$qstr);
				return null;
			}
			$qr->id=$dbid->insertid;
			$qr->items=0;
		}
		return $qr;
	}

	function addcart($cartid,$prodtype,$prodref,$prodvar,$price) {
		$dbid=$this->dbid;
		if ($prodtype==MXARTSUB || $prodtype==MXSITESUB || $prodtype==MXMEDSUB) {
			// check if not already subscribed
			// check if artist not already in cart
			$mxq=$this->query('SELECT * FROM mx_cartline WHERE cartid='.$cartid
			.' AND prodtype='.$prodtype.' AND prodref='.$prodref);
			if ($mxq && $mxq->num_rows>0) {
				$mxq->free();
				$qstr='UPDATE mx_cartline '
				.'SET prodvar='.$prodvar.', price='.$price
				.' WHERE cartid='.$cartid
				.' AND prodtype='.$prodtype.' AND prodref='.$prodref;
				$mxq=$this->query($qstr);
				if (!$mxq) {
					mxerror($dbid->error,__FILE__,__LINE__,$qstr);
					return null;
				}
			} else {
				if ($mxq) $mxq->free();
				$qstr='INSERT INTO mx_cartline '
				.'SET cartid='.$cartid.', prodtype='.$prodtype
				.', prodref='.$prodref.', prodvar='.$prodvar.', price='.$price;
				$mxq=$this->query($qstr);
				if (!$mxq) {
					mxerror($dbid->error,__FILE__,__LINE__,$qstr);
					return null;
				}
			}
		}
	}

	function addwish($userid,$prodtype,$prodref,$prodvar,$price) {
		$dbid=$this->dbid;
		if ($prodtype==MXARTSUB || $prodtype==MXSITESUB || $prodtype==MXMEDSUB) {
			// check if not already subscribed
			// check if artist not already in cart
			$mxq=$this->query('SELECT * FROM mx_wishline WHERE userid='.$userid
			.' AND prodtype='.$prodtype.' AND prodref='.$prodref);
			if ($mxq && $mxq->num_rows>0) {
				$mxq->free();
				$qstr='UPDATE mx_wishline '
				.'SET prodvar='.$prodvar.', price='.$price
				.' WHERE userid='.$userid
				.' AND prodtype='.$prodtype.' AND prodref='.$prodref;
				$mxq=$this->query($qstr);
				if (!$mxq) {
					mxerror($dbid->error,__FILE__,__LINE__,$qstr);
					return null;
				}
			} else {
				if ($mxq) $mxq->free();
				$qstr='INSERT INTO mx_wishline '
				.'SET userid='.$userid.', prodtype='.$prodtype
				.', prodref='.$prodref.', prodvar='.$prodvar.', price='.$price;
				$mxq=$this->query($qstr);
				if (!$mxq) {
					mxerror($dbid->error,__FILE__,__LINE__,$qstr);
					return null;
				}
			}
		}
	}

	function getcartdetails($cartid) {
		$dbid=$this->dbid;
		if (!$cartid) return null;
		$qstr='SELECT * FROM mx_cartline WHERE cartid='.$cartid.' ORDER BY id ASC';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return array();
		}
		$lines=array();
		while ($qr=$mxq->fetch_object()) $lines[]=$qr;
		$mxq->free();
		return $lines;
	}

	function deletecart($cartid,$lines) {
		$dbid=$this->dbid;
		if (!$cartid || !$lines) return null;
		$qstr='DELETE FROM mx_cartline WHERE cartid='.$cartid.' AND id=?';
		$mxq=$dbid->prepare($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
		$mxq->bind_param('i',$lineid);
		foreach ($lines as $lineid) {
			$mxq->execute();
		}
		$mxq->close();
	}

	function wishtocart($userid,$cartid,$lines) {
		$dbid=$this->dbid;
		if (!$userid || !$cartid || !$lines) return null;
		$qstr='SELECT prodtype,prodref,prodvar,price' .
				' FROM mx_wishline WHERE userid='.$userid.' AND id=?';
		$mxq=$dbid->prepare($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
		$mxq->bind_param('i',$lineid);
		$mxq->bind_result($prodtype,$prodref,$prodvar,$price);
		foreach ($lines as $lineid) {
			$mxq->execute();
			$mxq->fetch();
			$wishline[$lineid]->prodtype=$prodtype;
			$wishline[$lineid]->prodref=$prodref;
			$wishline[$lineid]->prodvar=$prodvar;
			$wishline[$lineid]->price=$price;
		}
		$mxq->close();
		foreach ($lines as $lineid) {
			$this->addcart($cartid,$wishline[$lineid]->prodtype,
			$wishline[$lineid]->prodref,
			$wishline[$lineid]->prodvar,
			$wishline[$lineid]->price);
		}
		$this->deletewish($userid,$lines);
	}

	function setplaytime($userid,$mediaid,$mediaplaytype,$action,$playid=0,$percent='0.00',$playtime='0',$rating='0',$status=0) {
		$dbid=$this->dbid;
		// careful, one user may be streaming on various devices simultaneously:
		// so don't consider a start the end of another play! (unless we limit simultaneous logins)
		switch ($action) {
			case 'start': // starts playing
				$qstr='INSERT INTO mx_med2play SET mediaid='.$mediaid.', type='.$mediaplaytype.', userid='.$userid
				.', start=NOW(), played=0, playtime=0, rating=0, status=0';
				break;
			case 'stop': // stopped playing
				$qstr='UPDATE mx_med2play SET stop=NOW(), played='.$percent.', playtime='.$playtime
				.' WHERE userid='.$userid.' AND mediaid='.$mediaid.' AND id='.$playid;
				break;
			case 'rate': // rated media
				$qstr='UPDATE mx_med2play SET rating='.$rating
				.' WHERE userid='.$userid.' AND mediaid='.$mediaid.' AND id='.$playid;
				break;
			case 'update': // interval update
				$qstr='UPDATE mx_med2play SET played='.$percent.', playtime='.$playtime
				.' WHERE userid='.$userid.' AND mediaid='.$mediaid.' AND id='.$playid;
				break;
			case 'error': // interval update
				$qstr='UPDATE mx_med2play SET status='.$status
				.' WHERE userid='.$userid.' AND mediaid='.$mediaid.' AND id='.$playid;
				break;
			default:
				return;
		}
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
		if ($action=='start') {
			return $dbid->insert_id;
		}
		return $playid;
	}

	function carttowish($userid,$cartid,$lines) {
		$dbid=$this->dbid;
		if (!$userid || !$cartid || !$lines) return null;
		$qstr='SELECT prodtype,prodref,prodvar,price' .
				' FROM mx_cartline WHERE cartid='.$cartid.' AND id=?';
		$mxq=$dbid->prepare($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
		$mxq->bind_param('i',$lineid);
		$mxq->bind_result($prodtype,$prodref,$prodvar,$price);
		foreach ($lines as $lineid) {
			$mxq->execute();
			$mxq->fetch();
			$cartline[$lineid]->prodtype=$prodtype;
			$cartline[$lineid]->prodref=$prodref;
			$cartline[$lineid]->prodvar=$prodvar;
			$cartline[$lineid]->price=$price;
		}
		$mxq->close();
		foreach ($lines as $lineid) {
			$this->addwish($userid,$cartline[$lineid]->prodtype,
			$cartline[$lineid]->prodref,
			$cartline[$lineid]->prodvar,
			$cartline[$lineid]->price);
		}
		$this->deletecart($cartid,$lines);
	}


	function deletewish($userid,$lines) {
		$dbid=$this->dbid;
		if (!$userid || !$lines) return null;
		$qstr='DELETE FROM mx_wishline WHERE userid='.$userid.' AND id=?';
		$mxq=$dbid->prepare($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
		$mxq->bind_param('i',$lineid);
		foreach ($lines as $lineid) {
			$mxq->execute();
		}
		$mxq->close();
	}

	function getwishlist($userid) {
		$dbid=$this->dbid;
		if (!$userid) return null;
		$qstr='SELECT * FROM mx_wishline WHERE userid='.$userid.' ORDER BY id ASC';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
		while ($qr=$mxq->fetch_object()) $lines[]=$qr;
		$mxq->free();
		return $lines;
	}

	function search($str) {
		//$str=strtolower(preg_replace('%[^a-zA-Z0-9- ]%','',$str));
		$search=null;
		$dbid=$this->dbid;
		$search['persons']=array();
		$mxq=$this->query('SELECT * FROM mx_account' .
			' WHERE status>='.MXACCTSETUP.' AND fullname LIKE \'%'.$dbid->real_escape_string($str)
			.'%\' ORDER BY fullname LIMIT 25');
		if ($mxq) {
			while ($qr=$mxq->fetch_object()) {
				$search['persons'][$qr->id]=$qr;
			}
			$mxq->free();
		}
		$search['artists']=array();
			$mxq=$this->query('SELECT * FROM mx_account' .
			' WHERE status>='.MXACCTSETUP.' AND artistname LIKE \'%'.$dbid->real_escape_string($str)
			.'%\' ORDER BY artistname LIMIT 25');
		if ($mxq) {
			while ($qr=$mxq->fetch_object()) {
				$search['artists'][$qr->id]=$qr;
			}
			$mxq->free();
		}
		$search['archipelagoes']=array();
		$mxq=$this->query('SELECT * FROM mx_archipelago' .
				' WHERE name LIKE \'%'.$dbid->real_escape_string($str).'%\'' .
				' OR description LIKE \'%'.$dbid->real_escape_string($str).'%\'' .
				' ORDER BY name LIMIT 25');
		if ($mxq) {
			while ($qr=$mxq->fetch_object()) {
				$search['archipelagoes'][$qr->id]=$qr;
			}
			$mxq->free();
		}
		$search['islands']=array();
		$mxq=$this->query('SELECT * FROM mx_island' .
				' WHERE name LIKE \'%'.$dbid->real_escape_string($str).'%\'' .
				' OR description LIKE \'%'.$dbid->real_escape_string($str).'%\'' .
				' ORDER BY name LIMIT 25');
		if ($mxq) {
			while ($qr=$mxq->fetch_object()) {
				$search['islands'][$qr->id]=$qr;
			}
			$mxq->free();
		}
		$search['medias']=array();
		$mxq=$this->query('SELECT id,title FROM mx_media' .
				' WHERE status>='.MXMEDIAFANVISIBLE.' AND status<='.MXMEDIAPUBLICSHARED
				.' AND title LIKE \'%'.$dbid->real_escape_string($str).'%\''
				.' ORDER BY title LIMIT 25');
		if ($mxq) {
			while ($qr=$mxq->fetch_object()) {
				$search['medias'][$qr->id]=$qr;
			}
			$mxq->free();
		}
		return $search;
	}

	function options() {
		//global $dbid;
		$dbid=$this->dbid;
		$mxq=$this->query('SELECT * FROM mx_config');
		if (!$mxq) {
			die('No options?');
		} else {
			while ($qr=$mxq->fetch_object()) {
				$this->optioncache[$qr->optionname]=$qr->value;
			}
			$mxq->free();
			return $this->optioncache;
		}
	}

	function getreferrer($invite) {
		$dbid=$this->dbid;
		$qstr='SELECT id,invites FROM mx_account WHERE invitecode=\''.$dbid->real_escape_string($invite).'\'';
		$mxq=$this->query($qstr);
		if (!$mxq || !$mxq->num_rows) {
			return null;
		}
		$qr=$mxq->fetch_object();
		$mxq->free();
		//if ($qr->invites>=MXMAXINVITES) return -1; // all invites used :-(
		return $qr->id;
	}

	function getregionname($countrycode,$regioncode) {
		$dbid=$this->dbid;
		$qstr='SELECT name FROM mx_region WHERE' .
				' country=\''.$countrycode.'\''.
				' AND region=\''.$regioncode.'\'';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			return '-';
		}
		$qr=$mxq->fetch_object();
		$mxq->free();
		return $qr->name;
	}

	function option($optname) {
		//global $dbid;
		$dbid=$this->dbid;
		if ($this->optioncache[$optname]) return $this->optioncache[$optname];
		$mxq=$this->query('SELECT optionname,value FROM mx_config WHERE optionname = \''.$optname.'\'');
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__);
			$opt='';
		} else if ($mxq->num_rows) {
			$qr=$mxq->fetch_object();
			$mxq->free();
			$opt=$qr->value;
		} else $opt='';
		$this->optioncache[$optname]=$opt;
		return $opt;
	}

	function checklogin($login,$pwd) {
		$dbid=$this->dbid;
		$login=$dbid->real_escape_string($login);
		$pwd=$dbid->real_escape_string($pwd);
		$mxq=$this->query('SELECT * FROM mx_account WHERE ( email=\''.strtolower($login).'\'' .
				' OR username=\''.strtolower($login).'\' ) AND pwdhash=\''.hash('sha256',$pwd).'\'');
		if (!$mxq || !$mxq->num_rows) return null;
		$qr=$mxq->fetch_object();
		$mxq->free();
		return $qr;
	}

	function checkapplogin($login,$mix) {
		$dbid=$this->dbid;
		$login=$dbid->real_escape_string($login);
		$pwd=$dbid->real_escape_string($mix);
		$mxq=$this->query('SELECT * FROM mx_account WHERE ( email=\''.strtolower($login).'\'' .
			' OR username=\''.strtolower($login).'\' )');
		if (!$mxq || !$mxq->num_rows) return null;
		$qr=$mxq->fetch_object();
		$mxq->free();
		error_log('pwdhash='.$qr->pwdhash);
		if ($mix==hash('sha256',$login.$qr->pwdhash)) return $qr;
		return null;
	}

	function checkconfirm($code) {
		$dbid=$this->dbid;
		//error_log('code='.$code);
		if (!$code || $code=='') return null;
		$code=$dbid->real_escape_string($code);
		$mxq=$this->query('SELECT * FROM mx_account WHERE confirmationcode=\''.$code.'\'');
		if (!$mxq || !$mxq->num_rows) return null;
		$qr=$mxq->fetch_object();
		$mxq->free();
		return $qr;
	}

	function getmxuser($id) {
		return $this->getuserinfo($id,$id);
		/*
		 * OBSOLETE
		 */
		$dbid=$this->dbid;
		$mxq=$this->query('SELECT * FROM mx_account WHERE id='.$id);
		if (!$mxq || !$mxq->num_rows) return null;
		$qr=$mxq->fetch_object();
		$mxq->free();
		if ($qr->status<0) return null;
		return $qr;
	}

	function getemailuser($email) {
		$dbid=$this->dbid;
		$qstr='SELECT * FROM mx_account WHERE email=\''.$dbid->real_escape_string($email).'\'';
		$mxq=$this->query($qstr);
		if (!$mxq || !$mxq->num_rows) return null;
		$qr=$mxq->fetch_object();
		$mxq->free();
		return $qr;
	}

	function getfbuser($fbid) {
		//global $dbid;
		$dbid=$this->dbid;
		$mxq=$this->query('SELECT id FROM mx_account WHERE fbid='.$fbid);
		if (!$mxq) return null;
		$qr=$mxq->fetch_row();
		$mxq->free();
		return $this->getmxuser($qr[0]);
	}

	function createuser($mxuser) {
		$dbid=$this->dbid;
		$dbuser=new StdClass();
		foreach ($mxuser as $key => $value) {
			if (!is_array($mxuser->$key))
				$dbuser->$key=$dbid->real_escape_string($mxuser->$key);
		}
		// check if email or fbid not already registered...
		$filter=$dbuser->fbid?('fbid='.$dbuser->fbid.' OR '):'';
		$filter.='email=\''.strtolower($dbuser->email).'\'';
		$qstr='SELECT * FROM mx_account WHERE '.$filter;
		$mxq=$this->query($qstr);
		if (!mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
		if ($mxq->num_rows) {
			$qr=$mxq->fetch_object();
			$mxq->free();
			if ($qr->status>=0) return 0; // already exists!!
			else return -1; // disabled
		}
		$mxq->free();
		$dbuser->confirmationcode=hash('sha1',time());
		$qstr='INSERT INTO mx_account (fbid,pwdhash,firstname,lastname,fullname,birthdate,website,'
				.'city,state,country,shortbio,longbio,gender,email,hashdir,timezone,fbverified,'
				.'locale,island_id,archi_id,picture,background_id,transparency,artistname,acctype,acccreation,'
				.'privpublic,privfriends,privartists,privfans,msgnotif,reqnotif,confirmationcode,status,referrer,'
				.'modules) '
				.'values ('.
					$dbuser->fbid.','.
					'\''.$dbuser->pwdhash.'\','.
					'\''.$dbuser->firstname.'\','.
					'\''.$dbuser->lastname.'\','.
					'\''.$dbuser->fullname.'\','.
					'\''.$dbuser->birthdate.'\','.
					'\''.$dbuser->website.'\','.
					'\''.$dbuser->city.'\','.
					'\''.$dbuser->state.'\','.
					'\''.$dbuser->country.'\','.
					'\''.$dbuser->shortbio.'\','.
					'\''.$dbuser->longbio.'\','.
					$dbuser->gender.','.
					'\''.strtolower($dbuser->email).'\','.
					'\''.$dbuser->hashdir.'\','.
					'\''.$dbuser->timezone.'\','.
					$dbuser->fbverified.','.
					'\''.$dbuser->locale.'\','.
					$dbuser->island_id.','.
					$dbuser->archi_id.','.
					'\''.$dbuser->picture.'\','.
					$dbuser->background_id.','.
					$dbuser->transparency.','.
					'\''.$dbuser->artistname.'\','.
					$dbuser->acctype.','.
					'NOW(),'.
					'\''.$dbuser->privpublic.'\','.
					'\''.$dbuser->privfriends.'\','.
					'\''.$dbuser->privartists.'\','.
					'\''.$dbuser->privfans.'\','.
					$dbuser->msgnotif.','.
					$dbuser->reqnotif.','.
					'\''.$dbuser->confirmationcode.'\','.
					$dbuser->status.','.
					$dbuser->referrer.','.
					'\''.serialize($dbuser->modules).'\''.
				')';
		$dbid=$this->dbid;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			//print_r($dbuser);
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
		$mxuser->id=$dbid->insert_id;
		$mxuser->confirmationcode=$dbuser->confirmationcode;
		if ($mxuser->referrer) { // if referree, increase referral counts for referrer
			$qstr='UPDATE mx_account SET invites=(invites+1) WHERE id='.$mxuser->referrer;
			$mxq=$this->query($qstr);
		}
		return $mxuser->id;
	}

	function deleteuser($fbid) {
		$dbid=$this->dbid;
		$qstr='UPDATE mx_account set status='.MXACCTDISABLED.' WHERE fbid='.$fbid;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
		}
	}

	function createband($userid,$artistname,$email,$pwdhash,$pwdok) {
		$dbid=$this->dbid;
		$qstr='SELECT id,pwdhash FROM mx_account WHERE ( LOWER(artistname)=\''.strtolower($artistname).'\' OR' .
				' email=\''.strtolower($email).'\' )';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
		if ($mxq->num_rows) { // band or user exists...
			$res=$mxq->fetch_object();
			$mxq->free();
			// check if password ok
			if ($res->pwdhash != $pwdhash) {
				return MXWRONGPWD;
			}
			// check if already linked
			$qstr='SELECT id FROM mx_acc2acc' .
				' WHERE account1_id='.$userid.' AND account2_id='.$res->id;
			$mxq=$this->query($qstr);
			if (!$mxq)
					mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			if ($mxq->num_rows) { // already linked
				$mxq->free();
				return MXALREADYLINKED;
			}
			$mxq->free();
			$qstr='INSERT INTO mx_acc2acc SET account1_id='.$userid.',' .
					' account2_id='.$res->id.', role='.MXBANDROLEALL;
			$mxq=$this->query($qstr);
			if (!$mxq)
					mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return MXNOWLINKED;
		}
		// create band
		if (!$pwdok) return MXNOPWDMATCH;
		if (!$artistname) return MXNOARTISTNAME;
		if (!$email) return MXNOEMAIL;
		$newuser=array(
			'artistname' => $artistname,
			'email' => $email,
			'pwdhash' => $pwdhash,
			'acctype' => MXACCOUNTBAND
		);
		$banduser=new MXUser($newuser);
		if (!$banduser) return MXNOTCREATED;
		$qstr='INSERT INTO mx_acc2acc SET account1_id='.$userid.',' .
				' account2_id='.$banduser->id.', role='.MXBANDROLEALL;
		$mxq=$this->query($qstr);
		if (!$mxq)
				mxerror($dbid->error,__FILE__,__LINE__,$qstr);
		return MXBANDCREATED;
	}

	function lostpassword($login) {
		$dbid=$this->dbid;
		$login=$dbid->real_escape_string($login);
		$qstr='SELECT * FROM mx_account WHERE email=\''.strtolower($login).'\'' .
				' OR username=\''.strtolower($login).'\'';
		$mxq=$this->query($qstr);
		if (!$mxq || !$mxq->num_rows) return array('login' => _('Account Not Found!'));
		$qr=$mxq->fetch_object();
		$mxq->free();
		if ($qr->status<0) return array('login' => _('Account Not Found!'));
		$qr->confirmationcode=hash('sha1',time());
		$this->updateuser($qr,'confirmationcode');
		mx_lostpassword($qr);
		return array('user' => $qr);
	}

	function updateuser($dbuser,$field) {
		$dbid=$this->dbid;
		if (!$dbuser || !$dbuser->id || !$field) {
			//error_log('dbuser='.$dbuser.' - dbuser->id='.$dbuser->id.' - field='.$field.' in mxdb->updateuser!');
			return;
		}
		if ($field=='genres' || $field=='tastes') {
			$qstr='DELETE FROM '.($field=='genres'?'mx_acc2gen':'mx_acc2tast').' WHERE userid='.$dbuser->id;
			$mxq=$this->query($qstr);
			if (!$mxq) mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			$gpos=0;
			foreach($dbuser->$field as $genre) {
				$qstr='INSERT INTO '.($field=='genres'?'mx_acc2gen':'mx_acc2tast')
				.' SET userid='.$dbuser->id.', genre='.$genre.', position='.$gpos++;
				$mxq=$this->query($qstr);
				if (!$mxq)
					mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			}
			return;
		}
		if ($field=='modules') $qupd=$field.'=\''.serialize($dbuser->$field).'\'';
		else $qupd=$field.'=\''.$dbid->real_escape_string($dbuser->$field).'\'';
		error_log('update:'.$qupd);
		$qstr='UPDATE mx_account SET ' .
			$qupd.' WHERE id='.$dbuser->id;
		$mxq=$this->query($qstr);
		if (!$mxq)
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
	}

	function getlinkedids($userid) {
		$dbid=$this->dbid;
		$mxq=$this->query('SELECT a.*,b.role,b.role2,b.role3 FROM mx_account a,mx_acc2acc b' .
				' WHERE b.account1_id='.$userid.' AND a.id=b.account2_id');
		if (!$mxq) mxerror($dbid->error,__FILE__,__LINE__);
		$linkedids=null;
		while ($qr=$mxq->fetch_object()) {
			$linkedids[]=$qr;
		}
		$mxq->free();
		return $linkedids;
	}

	function unlinkids($uid,$uid2) {
		$dbid=$this->dbid;
		$mxq=$this->query('DELETE FROM mx_acc2acc WHERE account1_id='.$uid.' AND account2_id='.$uid2);
		if (!$mxq) mxerror($dbid->error,__FILE__,__LINE__);
		return;
	}

	function linkedidsroles($uid,$uid2,$role,$role2=MXBANDROLENONE,$role3=MXBANDROLENONE) {
		$dbid=$this->dbid;
		$str='UPDATE mx_acc2acc SET role='.$role.',' .
				' role2='.$role2.', role3='.$role3.
				' WHERE account1_id='.$uid.' AND account2_id='.$uid2;
		$mxq=$this->query($str);
		if (!$mxq) mxerror($dbid->error.'<br/>CMD: '.$str,__FILE__,__LINE__);
		return;
	}

	function islcnt($value) {
		$dbid=$this->dbid;
		if ($value==0) $srch='island_id = 0 OR island_id IS NULL';
		else $srch='island_id = '.$value;
		$mxq=$this->query('SELECT COUNT(id) FROM mx_account WHERE '.$srch);
		if (!$mxq) return null;
		$qr=$mxq->fetch_row();
		$mxq->free();
		return $qr[0];
	}

	function archicnt($value) {
		$dbid=$this->dbid;
		if ($value==0) $srch='archi_id = 0 OR archi_id IS NULL';
		else $srch='archi_id = '.$value;
		$mxq=$this->query('SELECT COUNT(id) FROM mx_account WHERE '.$srch);
		if (!$mxq) return null;
		$qr=$mxq->fetch_row();
		$mxq->free();
		return $qr[0];
	}

	function emailexists($email) {
		$dbid=$this->dbid;
		$qstr='SELECT COUNT(id) FROM mx_account WHERE email=\''.$dbid->real_escape_string($email).'\'';
		$mxq=$this->query($qstr);
		if (!$mxq) return false;
		$qr=$mxq->fetch_row();
		$mxq->free();
		return ($qr[0]>0);
	}

	function fanlist($filter,$query=null) {
		//global $mxq;
		$dbid=$this->dbid;
		if (!$query) {
			if (is_array($filter)) {
				$tbl=','.$filter[0];
				$flt=$filter[1];
			} else {
				$tbl='';
				$flt=$filter;
			}
			$qstr='SELECT COUNT(DISTINCT a.id) FROM mx_account a'.$tbl
					.' WHERE status>='.MXACCTSETUP.' AND '.$flt;
			//error_log('fanlist: '.$qstr);
			$mxq=$this->query($qstr);
			if (!$mxq) return 0;
			$qr=$mxq->fetch_row();
			$mxq->free();
			if ($qr[0]<=30) $limit='';
			else $limit=' LIMIT 0,30';
			$qstr='SELECT DISTINCT a.* FROM mx_account a'.$tbl
				.' WHERE status>='.MXACCTSETUP.' AND '.$flt
				.' ORDER by rand()'.$limit;
			//error_log('fanlist2: '.$qstr);
			$mxq=$this->query($qstr);
			if (!$mxq) mxerror($dbid->error,__FILE__,__LINE__);
			else return array('count' => $mxq->num_rows, 'mxq' => $mxq);
		}
		$qr=$query->fetch_object();
		if (!$qr) $query->free();
		return $qr;
	}

	function artslist($filter,$query=null) { // mode = true if artist has enough media
		//global $mxq;
		$dbid=$this->dbid;
		if (!$query) {
			if (is_array($filter)) {
				$tbl=','.$filter[0];
				$flt=$filter[1];
			} else {
				$tbl='';
				$flt=$filter;
			}
			$mxq=$this->query('SELECT COUNT(DISTINCT a.id) FROM mx_account a'.$tbl
				.' WHERE (acctype='.MXACCOUNTARTIST.' OR acctype='.MXACCOUNTBAND.')'
				.' AND status>='.MXACCTSETUP.' AND '.$flt);
			if (!$mxq) return 0;
			$qr=$mxq->fetch_row();
			$mxq->free();
			if ($qr[0]<=30) $limit='';
			else $limit=' LIMIT 0,30';
			$mxq=$this->query('SELECT DISTINCT a.*' .
				' FROM mx_account a'.$tbl
				.' WHERE (acctype='.MXACCOUNTARTIST.' OR acctype='.MXACCOUNTBAND.')'
				.' AND status>='.MXACCTSETUP.' AND '.$flt
				.' ORDER BY rand()'.$limit);
			if (!$mxq) mxerror($dbid->error,__FILE__,__LINE__);
			else return array('count' => $mxq->num_rows, 'mxq' => $mxq);
		}
		$qr=$query->fetch_object();
		if (!$qr) $query->free();
		return $qr;
	}

	function getprivacy($userid,$id) {
		// userid: who's asking?
		// id: about whom?
		global $privlevels;
		if (!$id) return null;
		if ($userid && ($userid == $id || is_admin())) return $privlevels;
		if ($this->privacycache[$userid.','.$id]) return $this->privacycache[$userid.','.$id];
		$dbid=$this->dbid;
		$mxq=$this->query('SELECT acctype,artistname,privpublic,privfriends,privfans,privartists' .
			' FROM mx_account WHERE id='.$id);
		if (!$mxq) return null;
		$priv=$mxq->fetch_object();
		$mxq->free();
		// defaults
		$privarray['picture']=1;
		$privarray['firstname']=1;
		if ($priv->acctype==MXACCOUNTARTIST || $priv->acctype==MXACCOUNTBAND) {
			$privarray['artistname']=1;
			if (!$priv->artistname)
				$privarray['fullname']=1;
		}
		// publicly visible
		foreach (explode(',',$priv->privpublic) as $key) {
			$privarray[$key]=1;
		}
		// check if friend
		if ($this->isfriend($userid,$id)) {
			$privarray['isfriend']=1;
			// default settings for friends
			//$privarray['identity']=1;
			foreach (explode(',',$priv->privfriends) as $key) {
				$privarray[$key]=1;
			}
		}
		//echo 'id='.$id.' priv='.print_r($priv).' privarray='.print_r($privarray);

		// check if subscribed fan or artist
		$fans=$this->getfanship($userid,$id);
		if ($fans[0]==MXFAN) {
			$privarray['isfan']=1;
			foreach (explode(',',$priv->privfans) as $key) {
				$privarray[$key]=1;
			}
		}
		// return result
		$this->privacycache[$userid.','.$id]=$privarray;
		return $privarray;
	}

	function fanpic($userid,$id) {
		$dbid=$this->dbid;
		if ($this->fanpiccache[$userid.','.$id]) return $this->fanpiccache[$userid.','.$id];
		$mxq=$this->query('SELECT picture,hashdir,fbid' .
				' FROM mx_account WHERE id='.$id);
		if (!$mxq) return null;
		$qr=$mxq->fetch_object();
		$mxq->free();
		$this->fanpiccache[$userid.','.$id]=$qr;
		return $qr;
	}

	function getuserinfo($userid,$id) {
		global $MXDEFAULTMODULES,$MXREMOVEDMODULES;
		if (!$id) return null;
		if (array_key_exists($id, $this->usercache)) {
			//error_log('usercache hit on user '.$id.' for user'.$userid);
			return $this->usercache[$id];
		}
		$dbid=$this->dbid;
		$mxq=$this->query('SELECT * FROM mx_account WHERE id='.$id);
		if (!$mxq || !$mxq->num_rows) return null;
		$qruser=$mxq->fetch_object();
		$mxq->free();
		//error_log('getuserinfo, caller: '.$userid.' - called: '.$id);
		if ($userid==$id) {
			$modules=unserialize($qruser->modules);
			$qruser->modules=$modules;
			$modchanged=0;
			if (!$qruser->modules) {
				$qruser->modules=$MXDEFAULTMODULES;
				$modchanged=1;
			} else {
				foreach($MXREMOVEDMODULES as $module) {
					foreach ($qruser->modules as $oldarea => $oldnames) {
						$key=array_search($module, $oldnames);
						if ($key!==false) {
							unset($qruser->modules[$oldarea][$key]);
							$modchanged=1;
						}
					}
				}
				foreach($MXDEFAULTMODULES as $modarea => $modulenames) {
					if (!array_key_exists($modarea, $qruser->modules)) { // area does not exist -> add
						$qruser->modules[$modarea]=array();
						$modchanged=1;
						error_log('created area '.$modarea);
					}
					foreach($modulenames as $modname) {
						$newmod=1;
						foreach ($qruser->modules as $oldarea => $oldnames) {
							$key=array_search($modname, $oldnames);
							if ($key!==false) {
								$newmod=0;
							}
						}
						if ($newmod) {
							$qruser->modules[$modarea][]=$modname;
							$modchanged=1;
							error_log('added '.$modname.' to '.$modarea);
						}
					}
				}
			}
			if ($modchanged) {
				$this->updateuser($qruser,'modules');
				error_log('mxdb->getuserinfo: updated user modules');
			}
		}

		// generate age and bday from bdate
		if ($qruser->birthdate && $qruser->birthdate!='0000-00-00') {
			$bdate=new DateTime($qruser->birthdate);
			$now=new DateTime("now");
			$age=$bdate->diff($now);
			$qruser->age=$age->y; //date('Y')-substr($user->birthdate,0,4);
			$qruser->birthday=$bdate->format('F d');
		}
		$qruser->mediacnt=0;
		$qruser->pubcnt=0;
		$qruser->subcnt=0;
		if($dbid->multi_query('SELECT COUNT(id),SUM(filesize) FROM mx_media WHERE owner_id='.$id.' AND status>1'
			.' AND type!='.MXMEDIABASEBUNDLE.' AND type!='.MXMEDIAREGULARBUNDLE.';' // do not count bundles
			.' SELECT COUNT(id),SUM(filesize) FROM mx_media WHERE owner_id='.$id.' AND status>2'
			.' AND type!='.MXMEDIABASEBUNDLE.' AND type!='.MXMEDIAREGULARBUNDLE.';' // same
			.' SELECT COUNT(id) FROM mx_subscriptions WHERE subcat='.MXARTSUB.' AND objectid='.$id.' AND NOT status=0;'
			.' SELECT COUNT(id) FROM mx_subscriptions WHERE subcat='.MXARTSUB.' AND objectid='.$id.' AND NOT status=0 AND subtype='.MXSUBFOY.';'
			.' SELECT COUNT(id) FROM mx_subscriptions WHERE subcat='.MXARTSUB.' AND objectid='.$id.' AND NOT status=0 AND'
				.' (subtype='.MXSUBFOFA.' OR subtype='.MXUPGFOFA.');'
			.' SELECT COUNT(id) FROM mx_subscriptions WHERE subcat='.MXARTSUB.' AND objectid='.$id.' AND NOT status=0 AND subtype='.MXSUBLIKE.';'
			)) {
			if ($mxq=$dbid->use_result()) {
				$qr=$mxq->fetch_row();
				$mxq->free();
				$qruser->mediacnt=$qr[0];
				$qruser->mediasize=$qr[1];
			} else $qruser->mediacnt=$qruser->mediasize=0;
			if ($dbid->next_result() && $mxq=$dbid->use_result()) {
				$qr=$mxq->fetch_row();
				$mxq->free();
				$qruser->pubcnt=$qr[0];
				$qruser->pubsize=$qr[1];
			} else $qruser->pubcnt=$qruser->pubsize=0;
			if ($dbid->next_result() && $mxq=$dbid->use_result()) {
				$qr=$mxq->fetch_row();
				$mxq->free();
				$qruser->subcnt=$qr[0];
			} else $qruser->subcnt=0;
			if ($dbid->next_result() && $mxq=$dbid->use_result()) {
				$qr=$mxq->fetch_row();
				$mxq->free();
				$qruser->subfoy=$qr[0];
			} else $qruser->subfoy=0;
			if ($dbid->next_result() && $mxq=$dbid->use_result()) {
				$qr=$mxq->fetch_row();
				$mxq->free();
				$qruser->subfofa=$qr[0];
			} else $qruser->subfofa=0;
			if ($dbid->next_result() && $mxq=$dbid->use_result()) {
				$qr=$mxq->fetch_row();
				$mxq->free();
				$qruser->sublike=$qr[0];
			} else $qruser->sublike=0;
		}
		$mxq=$this->query('SELECT * FROM mx_acc2gen WHERE userid='.$id.' ORDER BY position LIMIT 5');
		$genres=array();
		if ($mxq && $mxq->num_rows) {
			while ($g=$mxq->fetch_object()) {
				$genres[]=$g->genre;
			}
			$mxq->free();
		}
		$qruser->genres=$genres;
		$mxq=$this->query('SELECT * FROM mx_acc2tast WHERE userid='.$id.' ORDER BY position LIMIT 5');
		$tastes=array();
		if ($mxq  && $mxq->num_rows) {
			while ($t=$mxq->fetch_object())
				$tastes[]=$t->genre;
			$mxq->free();
		}
		$qruser->tastes=$tastes;
		//die(print_r($qruser,true));
		$this->usercache[$id]=$qruser;
		return $qruser;
	}

	function getad() {
		$dbid=$this->dbid;
		$mxq=$this->query('SELECT * FROM mx_ads ORDER BY rand() LIMIT 0,1');
		if (!$mxq) mxerror($dbid->error,__FILE__,__LINE__);
		$qr=$mxq->fetch_object();
		$mxq->free();
		return $qr;
	}

	function getbasebundle($userid) {
		$dbid=$this->dbid;
		$qstr='SELECT id FROM mx_media WHERE owner_id='.$userid.' AND type='.MXMEDIABASEBUNDLE;
		$mxq=$this->query($qstr);
		if (!$mxq || !$mxq->num_rows) {
			if (!$mxq) mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return 0;
		}
		$qr=$mxq->fetch_row();
		$mxq->free();
		return $qr[0];
	}

	function getnewbundle($userid) {
		$dbid=$this->dbid;
		// look for one empty bundle
		$qstr='SELECT id FROM mx_media a WHERE a.owner_id='.$userid.' AND a.type='.MXMEDIAREGULARBUNDLE
		.' AND status='.MXMEDIANEW;
		$mxq=$this->query($qstr);
		if ($mxq && $mxq->num_rows) { // we found one, return it
			$qr=$mxq->fetch_row();
			$mxq->free();
			return $qr[0];
		}
		return $this->createbundle($userid, _('New Bundle'),MXMEDIANEW);
	}

	function addmedia($user,$filename,$fsize,$status,$id3info,$fhash) {
		$dbid=$this->dbid;
		$qstr='INSERT INTO mx_media (owner_id,filename,filesize,status,hashcode,timestamp,id3info,type)'
			.' values ('.$user->id.',\''.$dbid->real_escape_string($filename).'\','
			.$fsize.','.$status.',\''.$fhash.'\',NOW(),\''.addslashes(serialize($id3info)).'\','.MXMEDIAUNDEFINED.')';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			 if ($dbid->errno==1062)
				return (array('error' => _('This file was already uploaded')));
			 return (array('error' => '('.$dbid->errno.') '.$dbid->error.' ['.$qstr.']'));
		}
		$mediaid=$dbid->insert_id;
		if ($bundleid=$this->getbasebundle($user->id)) {
			$qstr='INSERT INTO mx_med2bun SET bundleid='.$bundleid.',mediaid='.$mediaid.',position=0';
			$mxq=$this->query($qstr);
		}
		return array(
			'success' => true,
			'mediaid' => $mediaid
		);
	}

	function gettmpmedia($user,$mxq=null) {
		if (!$mxq) {
			$dbid=$this->dbid;
			$mxq=$this->query('SELECT * FROM mx_media WHERE owner_id='.$user->id.' AND status=0');
			if (!$mxq) return null;
			return $mxq;
		}
		$qr=$mxq->fetch_object();
		if ($qr) return $qr;
		$mxq->free();
		return null;
	}

	function getplaystats($userid) {
		$dbid=$this->dbid;
		// my play stats (what did I play?)
		$stats=array();
		$qstr='SELECT b.mediaid,b.type,b.playtime,b.played,b.start,a.owner_id FROM mx_media a, mx_med2play b WHERE'
		.' b.userid='.$userid
		.' AND b.mediaid=a.id'
		.' AND a.owner_id!='.$userid
		.' ORDER BY b.start DESC';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			$stats['plays']=array();
		} else {
			while ($mxq->num_rows && $qr=$mxq->fetch_object()) {
				$stats['plays'][]=$qr;
			}
			$mxq->free();
		}
		$qstr='SELECT distinct b.mediaid,b.type,AVG(b.playtime) as avgplaytime,AVG(b.played) as avgplayed,COUNT(b.id) as playcnt'
		.' FROM mx_media a, mx_med2play b WHERE'
		.' a.owner_id='.$userid
		.' AND b.mediaid=a.id'
		.' AND (b.played>0 OR a.type='.MXMEDIABG.' OR a.type='.MXMEDIAPIC.')'
		.' AND b.userid!='.$userid
		.' GROUP BY b.mediaid,b.type'
		.' ORDER BY a.title,b.type ASC';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			$stats['mmedias']=array();
		} else {
			while ($mxq->num_rows && $qr=$mxq->fetch_object()) {
				$stats['medias'][]=$qr;
			}
			$mxq->free();
		}
		$qstr='SELECT distinct b.userid,b.mediaid,b.type,AVG(b.playtime) as avgplaytime,AVG(b.played) as avgplayed,COUNT(b.id) as playcnt'
		.' FROM mx_media a, mx_med2play b WHERE'
		.' a.owner_id='.$userid
		.' AND b.mediaid=a.id'
		.' AND (b.played>0 OR a.type='.MXMEDIABG.' OR a.type='.MXMEDIAPIC.')'
		.' AND b.userid!='.$userid
		.' AND b.userid!=0' // no visitors
		.' GROUP BY b.userid,b.mediaid'
		.' ORDER BY playcnt DESC,avgplaytime DESC,avgplayed DESC'
		.' LIMIT 20';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			$stats['topfans']=array();
		} else {
			while ($mxq->num_rows && $qr=$mxq->fetch_object()) {
				$stats['topfans'][]=$qr;
			}
			$mxq->free();
		}
		$qstr='SELECT distinct b.mediaid,b.type,AVG(b.playtime) as avgplaytime,AVG(b.played) as avgplayed,COUNT(b.id) as playcnt'
		.' FROM mx_media a, mx_med2play b WHERE'
		.' a.owner_id='.$userid
		.' AND b.mediaid=a.id'
		.' AND (b.played>0 OR a.type='.MXMEDIABG.' OR a.type='.MXMEDIAPIC.')'
		.' AND b.userid=0'
		.' GROUP BY b.mediaid,b.type'
		.' ORDER BY playcnt DESC,avgplaytime DESC,avgplayed DESC'
		.' LIMIT 20';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			$stats['topvisitors']=array();
		} else {
			while ($mxq->num_rows && $qr=$mxq->fetch_object()) {
				$stats['topvisitors'][]=$qr;
			}
			$mxq->free();
		}
		return $stats;
	}

	/*
	 * getfanship(requesterid, artistid, [mediaid])
	 *
	 * returns Array with user's status and subscription/like date
	 * 	MXNONMEMBER: non-member
	 * 	MXMEMBER: MX member
	 * 	MXLIKER: Artist's liker
	 * 	MXFAN: Artist's subscriber
	 */
	function getfanship($userid,$artistid,$mediaid=0) {
		if (!$userid) return array(MXNONMEMBER,null);
		if (!$artistid && !$mediaid) return array(MXMEMBER,null);
		if ($artistid==$userid) return array(MXME,'9999-01-01');
		// check global cache first
		$cachendx=md5($userid.',GLOBAL,'.$artistid.','.$mediaid);
		if (array_key_exists($cachendx,$this->fanshipcache)) {
			//error_log('fanship cache hit');
			return $this->fanshipcache[$cachendx];
		}
		$dbid=$this->dbid;
		// check if artist subscriber
		$cachendx=md5($userid.','.MXARTSUB.','.$artistid);
		if (array_key_exists($cachendx,$this->fanshipcache)) {
			//error_log('fanship1 cache hit');
			$fanship=$this->fanshipcache[$cachendx]; // cache hit
		} else {
			$qstr='SELECT * FROM mx_subscriptions WHERE fanid='.$userid
			.' AND subcat='.MXARTSUB.' AND objectid='.$artistid
			.' AND (status='.MXNEWSUB.' OR status='.MXCURRENTSUB.' OR status='.MXRENEWEDSUB.')'
			.' AND (subtype!='.MXSUBFOY.' OR expiry >= NOW() )';
			//error_log('getfanship1:'.$qstr);
			$mxq=$this->query($qstr);
			if (!$mxq) {
				mxerror($dbid->error,__FILE__,__LINE__,$qstr);
				return array(MXMEMBER,null);
			}
			if ($mxq->num_rows>0) { // subscriber OR liker!
				$qr=$mxq->fetch_object();
				if ($qr->subtype!=MXSUBLIKE) {
					$fanship=array(MXFAN,$qr->firstsub); // it's a fan! we're done here!
				} else
					$fanship=array(MXLIKER,$qr->firstsub); // it's a liker!
			} else {
				$fanship=array(MXMEMBER,null); // it's neither a fan nor a liker so far...
			}
			$mxq->free();
			$this->fanshipcache[$cachendx]=$fanship;
		}
		if ($fanship[0]==MXFAN) return $fanship;
		if ($mediaid) { // particular media: check if media subscriber (purchaser)
			$media=$this->getmediainfo($userid, $mediaid);
			$flt='objectid='.$mediaid;
			if ($media->type==MXMEDIABASEBUNDLE || $media->type==MXMEDIAREGULARBUNDLE) { // media is a bundle
			} else if (is_array($media->bundles)) { // media part of bundles
				foreach($media->bundles as $bundle) {
					if ($flt) $flt.=' OR ';
					$flt.='objectid='.$bundle->id;
				}
			} else { // media is unbundled
				//$flt='objectid='.$mediaid;
			}
			// check if user subscribed to media or bundles
			$cachendx=md5($userid.','.MXMEDSUB.','.$flt);
			if (array_key_exists($cachendx,$this->fanshipcache)) {
				//error_log('fanship2 cache hit');
				$fanship=$this->fanshipcache[$cachendx];
			} else {
				$qstr='SELECT count(id) FROM mx_subscriptions WHERE fanid='.$userid
				.' AND subcat='.MXMEDSUB.' AND '.$flt
				.' AND (status='.MXNEWSUB.' OR status='.MXCURRENTSUB.')';
				//error_log($qstr);
				//error_log('getfanship2:'.$qstr);
				$mxq=$this->query($qstr);
				if (!$mxq) {
					mxerror($dbid->error,__FILE__,__LINE__,$qstr);
					return $fanship;
				}
				if ($mxq->num_rows>0) {
					$qq=$mxq->fetch_row();
					$mxq->free();
					if ($qq[0]>0) {	// media subscriber!
						$fanship=array(MXBUYER,null);
						$this->fanshipcache[$cachendx]=$fanship;
					}
				}
			}
			if ($fanship[0]==MXBUYER) return $fanship;
		}
		// check if it's a site-wide subscriber with active subscription
		$cachendx=md5($userid.','.$MXSITESUB);
		if (array_key_exists($cachendx,$this->fanshipcache)) {
			//error_log('fanship3 cache hit');
			$fanship=$this->fanshipcache[$cachendx];
		} else {
			$qstr='SELECT count(id) FROM mx_subscriptions WHERE fanid='.$userid
			.' AND subcat='.MXSITESUB
			.' AND (status='.MXNEWSUB.' OR status='.MXCURRENTSUB.' OR status='.MXRENEWEDSUB.')'
			.' AND expiry >= NOW()';
			//error_log('getfanship3:'.$qstr);
			$mxq=$this->query($qstr);
			if (!$mxq) {
				mxerror($dbid->error,__FILE__,__LINE__,$qstr);
				return $fanship;
			}
			if ($mxq->num_rows>0) {
				$qq=$mxq->fetch_row();
				$mxq->free();
				if ($qq[0]>0) { // site-wide subscriber
					$fanship=array(MXSUBSCRIBER,NULL); // it's a fan! we're done here!
					$this->fanshipcache[$cachendx]=$fanship;
				}
			}
		}
		$cachendx=md5($userid.',GLOBAL,'.$artistid.','.$mediaid);
		$this->fanshipcache[$cachendx]=$fanship;
		return $fanship;
	}

	function isfriend($userid,$id) {
		if (!$id || !$userid) return false;
		$dbid=$this->dbid;
		if ($this->friendship[$userid.','.$id]) return $this->friendship[$userid.','.$id];
		$mxq=$this->query('SELECT count(id) FROM mx_friends WHERE ((account1_id='.$userid.
			' AND account2_id='.$id.') OR (account1_id='.$id.
			' AND account2_id='.$userid.')) AND confirmed=1');
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			$isfrnd=false;
		} else {
			$qr=$mxq->fetch_row();
			$mxq->free();
			$isfrnd=($qr[0]>0);
		}
		$this->friendship[$userid.','.$id]=$isfrnd;
		return $isfrnd;
	}

	function listartistmedia($artistid,$user,$mxq=null,$orderkey='') {
		return $this->listmedia($user,$mxq,$orderkey,$artistid);
	}

	function checkbundles($artistid) {
		$dbid=$this->dbid;
		if (!$artistid) return;
		$qstr='SELECT COUNT(*) FROM mx_media'
			.' WHERE owner_id='.$artistid.' AND type='.MXMEDIABASEBUNDLE;
		$mxq=$this->query($qstr);
		if (!$mxq && !$mxq->num_rows) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return;
		}
		$qr=$mxq->fetch_row();
		$mxq->free();
		if (!$qr[0]) { // no bundle, create default one
			$qstr='INSERT INTO mx_media SET owner_id='.$artistid
			.', type='.MXMEDIABASEBUNDLE.''
			.', title=\''.MXDEFAULTBUNDLENAME.'\''
			.', description=\''.MXDEFAULTBUNDLEDESC.'\''
			.',status='.MXMEDIAVIRTUAL
			.',timestamp=NOW()'
			.',completion='.MXMEDIANOSTATUS
			.',hashcode=\''.md5(time()).'\'';
			$mxq=$this->query($qstr);
			if (!$mxq) {
				mxerror($dbid->error,__FILE__,__LINE__,$qstr);
				return;
			}
			$bundleid=$dbid->insert_id;
			$qstr='SELECT id FROM mx_media WHERE owner_id='.$artistid.' AND id!='.$bundleid;
			$mxq=$this->query($qstr);
			if (!$mxq) {
				mxerror($dbid->error,__FILE__,__LINE__,$qstr);
				return;
			}
			while ($qr=$mxq->fetch_row()) {
				$media[]=$qr[0];
			}
			$mxq->free();
			$qstr='INSERT INTO mx_med2bun SET bundleid='.$bundleid.', mediaid=?';
			$mxq=$dbid->prepare($qstr);
			if (!$mxq) {
				mxerror($dbid->error,__FILE__,__LINE__,$qstr);
				return;
			}
			$mxq->bind_param('i',$mediaid);
			foreach ($media as $mediaid) {
				$mxq->execute();
			}
			$mxq->close();
		}
	}

	function createbundle($userid,$bundlename,$status=MXMEDIAREADY) {
		$dbid=$this->dbid;
		if (!$userid) return null;
		$qstr='INSERT INTO mx_media SET owner_id='.$userid
		.', type='.MXMEDIAREGULARBUNDLE.''
		.', title=\''.$dbid->real_escape_string($bundlename).'\''
		.', description=\''.$dbid->real_escape_string(MXDEFAULTNEWBUNDLEDESC).'\''
		.',status='.$status
		.',timestamp=NOW()'
		.',completion='.MXMEDIANOSTATUS
		.',hashcode=\''.md5(time()).'\'';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
		$bundleid=$dbid->insert_id;
		return $bundleid;
	}

	function listbundles($user,$mxq=null,$orderkey='',$scope) {
		return $this->listmedia($user,$mxq,$orderkey,$scope,true);
	}

	/*
	 * get MXFEATARTS featured artists
	 */
	function getfeatarts() {
		$dbid=$this->dbid;
		$qstr='SELECT id from mx_account WHERE featured > NOW() ORDER by rand()';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return array();
		}
		if (!$mxq->num_rows) return array();
		while ($qr=$mxq->fetch_row()) {
			$feats[]=$qr[0];
			//error_log('id=',$qr[0]);
		}
		$mxq->free();
		return $feats;
	}

	/*
	 * get random artists
	 */
	function getrandarts($qty) {
		$dbid=$this->dbid;
		$qstr='SELECT DISTINCT a.* FROM mx_account a'
			.' WHERE (acctype='.MXACCOUNTARTIST.' OR acctype='.MXACCOUNTBAND.')'
			.' AND status>='.MXACCTSETUP.' AND '
			.MXMINIMUMMEDIA.' <= (SELECT count(b.id) FROM mx_media b WHERE b.owner_id=a.id'
			.' AND b.type!='.MXMEDIABASEBUNDLE.' AND b.type!='.MXMEDIAREGULARBUNDLE
			.' AND b.status>='.MXMEDIAFANVISIBLE.' AND b.status<'.MXMEDIAARCHIVED.')'
			.' AND 0 < (SELECT count(b.id) FROM mx_media b WHERE b.owner_id=a.id'
			.' AND (b.type='.MXMEDIASONG.' OR b.type='.MXMEDIAINSTR.')'
			.' AND b.status>='.MXMEDIAFANVISIBLE.' AND b.status<'.MXMEDIAARCHIVED.')'
			.' ORDER BY rand() LIMIT 0,'.$qty;
			//error_log($qstr);
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return array();
		}
		if (!$mxq->num_rows) return array();
		while ($qr=$mxq->fetch_row()) {
			$feats[]=$qr[0];
			//error_log('id=',$qr[0]);
		}
		$mxq->free();
		return $feats;
	}


	/*
	 * get monthly stats for a user
	 */
	function getmstats($userid,$username,$acctype) {
		$dbid=$this->dbid;
		if ($acctype==MXACCOUNTFAN) {
			$pag='fans';
			$opt='fanprof';
		} else {
			$pag='artists';
			$opt='artprof';
		}
		$qstr='SELECT year(date) as yy, month(date) as mm,'
		.' COUNT(IF(act='.$userid.' OR act=\''.$username.'\',id,null)) as hits,'
		.' COUNT(distinct IF(act='.$userid.' OR act=\''.$username.'\',userid+ip,null)) as visits,'
		.' COUNT(ID) as tothits,'
		.' COUNT(distinct userid+ip) as totvisits'
		.' FROM mx_log'
		.' WHERE pag="'.$pag.'" and opt="'.$opt.'"' //' and (act='.$userid.' OR act="'.$username.'")'
		.' GROUP BY yy desc, mm desc WITH ROLLUP';
		$mxq=$this->query($qstr);
		$stats=array();
		if (!$mxq || !$mxq->num_rows) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return $stats;
		}
		while ($qr=$mxq->fetch_object()) {
			if ($qr->yy==NULL) {
				$qr->yy=_('<b>TOTAL</b>');
				$qr->mm=0;
			}
			if ($qr->mm==NULL) {
				$qr->yy='<b>'.$qr->yy.'</b>';
				$qr->mm=0;
			}
			if (!$qr->hits) $qr->hits=0;
			if (!$qr->visits) $qr->visits=0;
			if (!$qr->tothits) $qr->tothits=0;
			if (!$qr->totvisits) $qr->totvisits=0;
			$stats[]=$qr;
		}
		$mxq->free();
		return $stats;
	}

	/*
	 * get daily stats for a user
	 */
	function getdstats($userid,$username,$acctype) {
		$dbid=$this->dbid;
		if ($acctype==MXACCOUNTFAN) {
			$pag='fans';
			$opt='fanprof';
		} else {
			$pag='artists';
			$opt='artprof';
		}
		$qstr='SELECT year(date) as yy, month(date) as mm, dayofmonth(date) as dd,'
		.' COUNT(IF(act='.$userid.' OR act=\''.$username.'\',id,null)) as hits,'
		.' COUNT(distinct IF(act='.$userid.' OR act=\''.$username.'\',userid+ip,null)) as visits,'
		.' COUNT(ID) as tothits,'
		.' COUNT(distinct userid+ip) as totvisits'
		.' FROM mx_log'
		.' WHERE pag="'.$pag.'" and opt="'.$opt.'"'// ' and (act='.$userid.' OR act="'.$username.'")'
		.' GROUP BY yy desc, mm desc, dd desc WITH ROLLUP';
		$mxq=$this->query($qstr);
		$stats=array();
		if (!$mxq || !$mxq->num_rows) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return $stats;
		}
		while ($qr=$mxq->fetch_object()) {
			if ($qr->yy==null) {
				$qr->yy=_('<b>TOTAL</b>');
			}
			if ($qr->mm==NULL) {
			}
			if ($qr->dd==null) {
				$qr->dd=_('<b>TOTAL</b>');
			}
			if (!$qr->hits) $qr->hits=0;
			if (!$qr->visits) $qr->visits=0;
			if (!$qr->tothits) $qr->tothits=0;
			if (!$qr->totvisits) $qr->totvisits=0;
			$stats[]=$qr;
		}
		$mxq->free();
		return $stats;
	}

	/*
	 * get status of a media
	 */
	function getmediastatus($userid,$mediaid) {
		$dbid=$this->dbid;
		$qstr='SELECT status FROM mx_media WHERE id='.$mediaid.' AND owner_id='.$userid;
		$mxq=$this->query($qstr);
		if (!$mxq || !$mxq->num_rows) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return 0;
		}
		$qr=$mxq->fetch_row();
		$mxq->free();
		return $qr[0];
	}

	/*
	 * get all info about one particular media
	 */
	function getmediainfo($userid,$mediaid) {
		//error_log('DB:getmediainfo for '.$mediaid);
		if (array_key_exists($mediaid,$this->mediacache)) {
			//error_log('mediacache hit on media '.$mediaid.' for user '.$userid);
			return $this->mediacache[$mediaid];
		}
		$dbid=$this->dbid;
		// look for media
		$qstr='SELECT a.*,b.hashdir,b.artistname,b.fbid FROM mx_media a, mx_account b WHERE a.id='.$mediaid.' AND b.id=a.owner_id';
		$mxq=$this->query($qstr);
		if (!$mxq || !$mxq->num_rows) {
			if (!$mxq) mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
		$qr=$mxq->fetch_object();
		$mxq->free();
		$qr->cartprice=0;
		if ($qr->type!=MXMEDIABASEBUNDLE && $qr->type!=MXMEDIAREGULARBUNDLE) {
			$qr->id3info=unserialize($qr->id3info);
			// look for bundles including the media
			$flt='b.mediaid='.$mediaid.' AND a.id=b.bundleid';
			$qstr='SELECT a.*,c.hashdir,c.artistname,c.fbid FROM mx_media a, mx_med2bun b, mx_account c WHERE '.$flt.' AND c.id=a.owner_id';
			$mxq=$this->query($qstr);
			$qr->bundles=array();
			if (!$mxq) {
				mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			}
			if ($mxq) {
				while ( $mxq->num_rows && $bun=$mxq->fetch_object()) {
					$qr->bundles[]=$bun;
				}
				$mxq->free();
			}
			if ($qr->type==MXMEDIAINSTR || $qr->type==MXMEDIASONG) {
				$qr->cartprice=MXFEE1SONG;
			} else if ($qr->type==MXMEDIABG || $qr->type==MXMEDIAPIC || $qr->type==MXMEDIAVIDEO) {
				if ($qr->id3info['video']['resolution_x']*$qr->id3info['video']['resolution_y']>1000000)
					$qr->cartprice=MXFEE1SONG;
			}
			if ($qr->cartprice)
				$qr->price=sprintf('<span class="buyprice">$%.2f</span>',$qr->cartprice);
			else
				$qr->price=sprintf('<span class="buyprice">%s</span>',_('FREE'));
		} else {
			$qr->tracks=0;
			$qr->bigpics=0;
			$qr->videos=0;
			$qr->totaltime=0;
			$qr->discount=0;
			$flt='b.bundleid='.$mediaid.' AND a.id=b.mediaid';
			$qstr='SELECT a.id,a.type,a.filesize,a.id3info,a.owner_id FROM mx_media a, mx_med2bun b WHERE '.$flt;
			//.' AND (a.type='.MXMEDIAINSTR.' OR a.type='.MXMEDIASONG.')';
			$mxq=$this->query($qstr);
			if (!$mxq) mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			if ($mxq && $mxq->num_rows) {
				while ($med=$mxq->fetch_object()) {
					$id3info=unserialize($med->id3info);
					$fanship=$this->getfanship($userid, $med->owner_id,$med->id);
					switch($med->type) {
						case MXMEDIASONG:
						case MXMEDIAINSTR:
							if ($fanship[0]!=MXBUYER && $fanship[0]!=MXFAN)
								$qr->tracks++;
							else
								$qr->discount++;
							$qr->totaltime+=round($id3info['playtime_seconds']);
							break;
						case MXMEDIABG:
						case MXMEDIAPIC:
							if ($id3info['video']['resolution_x']*$id3info['video']['resolution_y']>1000000) {
								if ($fanship[0]!=MXBUYER && $fanship[0]!=MXFAN)
									$qr->bigpics++;
								else
									$qr->discount++;
							}
							break;
						case MXMEDIAVIDEO:
							if ($id3info['video']['resolution_x']*$id3info['video']['resolution_y']>300000)
								if ($fanship[0]!=MXBUYER && $fanship[0]!=MXFAN)
									$qr->videos++;
								else
									$qr->discount++;
							$qr->totaltime+=round($id3info['playtime_seconds']);
							break;
						case MXMEDIADOC:
							break;
					}
				}
				$mxq->free();
				//error_log('media id='.$qr->id.' tracks='.$qr->tracks.' tottime='.$qr->totaltime);
			}
			$qr->bundles=array();
			if (!$qr->tracks && !$qr->videos && $qr->bigpics>0) $qr->bigpics+=4;
			$qr->cartprice=round(MXFEESONGS*($qr->tracks+($qr->bigpics>4?($qr->bigpics-4):0)+$qr->videos),2);
			if ($qr->cartprice) $buyprice=sprintf('$%.2f',$qr->cartprice);
			else $buyprice=_('FREE');
			if (!$qr->discount) $qr->price=sprintf('<span class="buyprice">%s</span>',$buyprice);
			else $qr->price=sprintf('<span class="buystrike">$%.2f</span><br/><span class="buyprice">%s</span>',
				round(MXFEESONGS*($qr->tracks+($qr->bigpics>4?($qr->bigpics-4):0)+$qr->videos+$qr->discount),2),
				$buyprice);
		}
		//error_log('media:'.$qr->id.' price:'.$qr->price);
		/*
		// look for associated media
		$qstr='SELECT a.* FROM mx_media a, mx_med2med b WHERE b.mediaid1='.$mediaid.' AND a.id=b.mediaid2';
		$mxq=$this->query($qstr);
		if (!$mxq || !$mxq->num_rows) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			$qr->linked=null;
		}
		$qr->linked=array();
		while ($link=$mxq->fetch_object()) {
			$qr->linked[]=$link;
		}
		$mxq->free();
		*/
		$this->mediacache[$mediaid]=$qr;
		return $qr;
	}

	/*
	 * get one playable media from that scope for that user
	 */
	function getonemedia($user,$scope=null) {
		$dbid=$this->dbid;
		$filter=''; //whatever media
		if (is_array($scope)) {
			foreach($scope as $artistid) {
				if ($filter) $filter.=' AND ';
				$filter.='a.owner_id='.$artistid;
			}
			$filter.=' AND ';
		} else if ($scope) {
			$filter='a.owner_id='.$scope.' AND ';
		}
		$qstr='SELECT FLOOR(RAND() * COUNT(*)) as rndpos FROM mx_media a'
		.' WHERE '.$filter.'(a.type='.MXMEDIAINSTR.' OR a.type='.MXMEDIASONG.' OR a.type='.MXMEDIAVIDEO.')'
		.' AND (a.status>'.MXMEDIAREADY.' AND a.status<'.MXMEDIAARCHIVED.')';
		//error_log('qstr='.$qstr);
		$mxq=$this->query($qstr);
		if (!$mxq || !$mxq->num_rows) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
		$qr=$mxq->fetch_row();
		$mxq->free();
		$rndpos=$qr[0];
		//error_log('rndpos='.$rndpos);
		$qstr='SELECT a.*, b.title as bundletitle FROM mx_media a, mx_media b, mx_med2bun c'
		.' WHERE '.$filter.'c.mediaid=a.id AND b.id=c.bundleid'
		.' AND (a.type='.MXMEDIAINSTR.' OR a.type='.MXMEDIASONG.' OR a.type='.MXMEDIAVIDEO.')'
		.' AND (a.status>'.MXMEDIAREADY.' AND a.status<'.MXMEDIAARCHIVED.')'
		.' LIMIT '.$rndpos.',1';
		//error_log('qstr='.$qstr);
		$mxq=$this->query($qstr);
		if (!$mxq || !$mxq->num_rows) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
		$qr=$mxq->fetch_object();
		$mxq->free();
		return $qr;
	}

	/*
	 * scope:
	 * - null for public media
	 * - 0 for member-only media
	 * - artist id for all media from artist (inclusive non-published!)
	 * - artists array for fan-only media
	 */
	function listmedia($user,$mxq=null,$orderkey='',$scope,$bundle=false) {
		if (is_null($scope)) { // public stuff only
			$filter='(a.status='.MXMEDIAPUBLIC.' OR a.status='.MXMEDIAVIRTUAL.')';
		} else if (is_array($scope)) { // fan-only media for listed artists
			$filter='';
			foreach($scope as $artistid) {
				if ($filter) $filter.=' OR ';
				$filter.='a.owner_id='.$artistid;
			}
			//if (!$filter) return null;
			$filter=($filter?('('.$filter.') AND '):'false AND ').'(a.status='.MXMEDIAFANVISIBLE.' OR a.status='.MXMEDIAFANSHARED
				.' OR a.status='.MXMEDIAVIRTUAL.')';
		} else if ($scope==0) { // member-only stuff
			$filter='(a.status='.MXMEDIAMEMBERVISIBLE.' OR a.status='.MXMEDIAMEMBERSHARED
			.' OR a.status='.MXMEDIAVIRTUAL.')';
		} else if ($scope>0) { // **ALL** stuff for this artist (inclusive non-published!!)
			$filter='a.owner_id='.$scope;
			if ($scope!=$user->id) $filter.=' AND a.status>='.MXMEDIAFANVISIBLE; //' AND a.status>'.MXMEDIAREADY;
		}
		if (!$mxq) {
			if ($bundle) {
				//$mfilter='('.str_replace('a.', 'd.', $filter).') AND d.type!='.MXMEDIABASEBUNDLE.' AND d.type!='.MXMEDIAREGULARBUNDLE;
				$filter.=' AND (a.type='.MXMEDIABASEBUNDLE.' OR a.type='.MXMEDIAREGULARBUNDLE.')';
				$bcnt=''; //,count(c.id) as cnt,sum(d.filesize) as size';
				$bcond=''; // AND (c.bundleid=a.id AND d.id=c.mediaid AND ('.$mfilter.'))';
				$bdb=''; //,mx_med2bun c,mx_media d';
			} else {
				$filter.=' AND a.type!='.MXMEDIABASEBUNDLE.' AND a.type!='.MXMEDIAREGULARBUNDLE;
				$bcnt='';
				$bcond='';
				$bdb='';
			}
			if (!$orderkey || $orderkey == '') $orderkey = 'title asc';
                        if ($bundle) $orderkey = 'timestamp desc';
			$orderkey='b.artistname asc, type desc, '.$orderkey;
			$dbid=$this->dbid;
			$qstr='SELECT a.*,b.hashdir, b.artistname'.$bcnt.' FROM mx_media a,mx_account b'.$bdb.' WHERE '.$filter
				.' AND b.id=a.owner_id AND a.owner_id!=0'.$bcond
				.' ORDER BY '.$orderkey;
			//error_log('listmedia: filter=['.$filter.']');
			$mxq=$this->query($qstr);
			if (!$mxq) {
				mxerror($dbid->error,__FILE__,__LINE__,$qstr);
				return null;
			}
			return $mxq;
		}
		$qr=$mxq->fetch_object();
		if ($qr) {
			$qr->id3info=unserialize($qr->id3info);
			if ($bundle) {
				$bi=$this->getbundleinfo($qr->id,$filter);
				if ($bi) {
					$qr->cnt=$bi->cnt;
					$qr->size=$bi->size;
				} else {
					$qr->cnt=0;
					$qr->size=0;
				}
			}
			return $qr;
		}
		$mxq->free();
		return null;
	}

	function listnopreviewmedia() {
		$dbid=$this->dbid;
		$qstr='SELECT * FROM mx_media WHERE (type='.MXMEDIABG.' OR type='.MXMEDIAPIC
		.' OR type='.MXMEDIASONG.' OR type='.MXMEDIAINSTR.' OR type='.MXMEDIAVIDEO.')'
		.' AND (preview IS NULL OR preview=0) AND owner_id!=0';
		$mxq=$this->query($qstr);
		$media=array();
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return $media;
		}
		while ($qr=$mxq->fetch_object()) {
			$media[]=$qr;
		}
		$mxq->free();
		return $media;
	}

	function getbundleinfo($bid,$filter) {
		$dbid=$this->dbid;
		$qstr='SELECT COUNT(a.id) as cnt,SUM(a.filesize) as size FROM mx_media a, mx_med2bun b'
		.' WHERE b.bundleid='.$bid.' AND a.id=b.mediaid'
		.' AND a.type!='.MXMEDIABASEBUNDLE.' AND a.type!='.MXMEDIAREGULARBUNDLE
		.' AND '.$filter;
		//error_log('bundleinfo:'.$qstr);
		if ($this->bundleinfocache[$qstr]) return $this->bundleinfocache[$qstr];
		$mxq=$this->query($qstr);
		if (!$mxq || !$mxq->num_rows) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
		$qr=$mxq->fetch_object();
		$mxq->free();
		$this->bundleinfocache[$qstr]=$qr;
		return $qr;
	}

	function listmediafrombundle($userid,$bundleid,$orderkey='',$scope,$bundle=false) {
		if (is_null($scope)) { // public stuff only
			$filter='a.status='.MXMEDIAPUBLIC;
		} else if (is_array($scope)) { // fan-only media for listed artists
			$filter='';
			foreach($scope as $artistid) {
				if ($filter) $filter.=' OR ';
				$filter.='a.owner_id='.$artistid;
			}
			$filter='('.$filter.') AND (a.status='.MXMEDIAFANVISIBLE.' OR a.status='.MXMEDIAFANSHARED.')';
		} else if ($scope==0) { // member-only stuff
			$filter='(a.status='.MXMEDIAMEMBERVISIBLE.' OR a.status='.MXMEDIAMEMBERSHARED.')';
		} else if ($scope>0) { // **ALL** stuff for this artist (inclusive non-published!!)
			$filter='a.owner_id='.$scope;
			if ($scope!=$userid) $filter.=' AND a.status>'.MXMEDIAREADY;
		}
		if ($bundle) $filter.=' AND (type='.MXMEDIABASEBUNDLE.' OR type='.MXMEDIAREGULARBUNDLE.')';
		else $filter.=' AND type!='.MXMEDIABASEBUNDLE.' AND type!='.MXMEDIAREGULARBUNDLE;
		if (!$orderkey || $orderkey == '') $orderkey = 'title asc';
		$orderkey='a.status asc, '.$orderkey;
		$dbid=$this->dbid;
		$qstr='SELECT a.*,b.hashdir, b.artistname,c.position FROM mx_media a,mx_account b, mx_med2bun c WHERE '.$filter
			.' AND b.id=a.owner_id AND a.owner_id!=0 AND c.bundleid='.$bundleid.' AND c.mediaid=a.id'
			.' ORDER BY position asc, '.$orderkey;
		if ($this->lmfbcache[$qstr]) return $this->lmfbcache[$qstr];
		$mxq=$this->query($qstr);
		if (!$mxq) return null;
		$media=array();
		while ($qr=$mxq->fetch_object()) {
			$qr->id3info=unserialize($qr->id3info);
			$media[]=$qr;
		}
		$mxq->free();
		$this->lmfbcache[$qstr]=$media;
		return $media;
	}

	function listselectedmedia($user,$ids) {
		$dbid=$this->dbid;
		$qstr='SELECT type,filename,filesize,title,description,completion,status,hashcode'
		.' FROM mx_media WHERE owner_id='.$user->id.' AND id=?';
		$mxq=$dbid->prepare($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
		$mxq->bind_param('i',$medid);
		$mxq->bind_result($filetype,$filename,$filesize,$title,$desc,$comp,$status,$hashcode);
		$qr=array();
		foreach ($ids as $medid) {
			$mxq->execute();
			$mxq->fetch();
			$qr[$medid]=new StdClass();
			$qr[$medid]->filename=$filename;
			$qr[$medid]->type=$filetype;
			$qr[$medid]->size=$filesize;
			$qr[$medid]->title=$title;
			$qr[$medid]->desc=$desc;
			$qr[$medid]->comp=$comp;
			$qr[$medid]->status=$status;
			$qr[$medid]->hashcode=$hashcode;
			$qr[$medid]->owner_id=$user->id;
			$qr[$medid]->id=$medid;
		}
		$mxq->close();
		return $qr;
	}

	function friendrequest($userid,$destid) {
		$dbid=$this->dbid;
		$qstr='SELECT * FROM mx_friends WHERE account1_id='.$userid.' AND '.
		'account2_id='.$destid;
		$mxq=$this->query($qstr);
		if (!$mxq) mxerror($dbid->error,__FILE__,__LINE__,$qstr);
		$rows=$mxq->num_rows;
		$mxq->free();
		if ($rows==0) {
			$qstr='INSERT INTO mx_friends SET account1_id='.$userid.', '.
			'account2_id='.$destid.', confirmed=0';
			$mxq=$this->query($qstr);
			if (!$mxq) mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return true;
		}
		return false;
	}

	function saveupdate($userid,$msg) {
		$dbid=$this->dbid;
		if (!$msg->refid) $msg->refid='NULL';
		$qstr='INSERT INTO mx_walls (authid,body,date,filter,refid)' .
				' values ('.$userid.',\''
				.$dbid->real_escape_string($msg->body)
				.'\',NOW(),'.$msg->filter.','.$msg->refid.')';
		$mxq=$this->query($qstr);
		if (!$mxq) mxerror($dbid->error,__FILE__,__LINE__,$qstr);
		$msg->msgid=$dbid->insert_id;
		if ($msg->refid) {
			$qstr='UPDATE mx_walls SET comments=comments+1'
				.' WHERE msgid='.$msg->refid;
			$mxq=$this->query($qstr);
			if (!$mxq) mxerror($dbid->error,__FILE__,__LINE__,$qstr);
		}
		return $msg->msgid;
	}

	function sendmessage($userid,$msg) {
		if ($msg->flags & MXREQUEST) {
			if (!$this->friendrequest($userid,$msg->to)) return 0;
		}
		$dbid=$this->dbid;
		$qstr='INSERT INTO mx_messages (authid,subject,body,priority,flags,date)' .
				' values ('.$userid.',\''
				.$dbid->real_escape_string($msg->subject).'\',\''
				.$dbid->real_escape_string($msg->body)
				.'\','.$msg->priority.','.$msg->flags.',NOW())';
		$mxq=$this->query($qstr);
		if (!$mxq) mxerror($dbid->error,__FILE__,__LINE__,$qstr);
		$msg->msgid=$dbid->insert_id;
		$qstr='INSERT INTO mx_msg2acc (msgid,sender,receiver,status)' .
				' values ('.$msg->msgid.','.$userid.','.$msg->to.',0)'; //0=not read
		$mxq=$this->query($qstr);
		if (!$mxq) mxerror($dbid->error,__FILE__,__LINE__,$qstr);
		return $msg->msgid;
	}

	function requesthandle($userid,$msgid,$status) {
		$dbid=$this->dbid;
		$qstr='SELECT sender,receiver FROM mx_msg2acc WHERE msgid='.$msgid;
		$mxq=$this->query($qstr);
		if (!$mxq) die(mxerror($dbid->error,__FILE__,__LINE__,$qstr));
		$qr=$mxq->fetch_object();
		$mxq->free();
		switch($status) {
			case MXREQCANCELLED: // the author is cancelling its request
				$qstr='DELETE FROM mx_friends'
				.' WHERE account1_id='.$userid
				.' AND account2_id='.$qr->receiver
				.' AND NOT confirmed & '.MXMSGREAD;
				break;
			case MXREQACCEPTED: // the recipient accepts the request
				$qstr='UPDATE mx_friends SET confirmed=1 '
				.' WHERE account1_id='.$qr->sender
				.' AND account2_id='.$userid;
				break;
			case MXREQRECUSED: // the recipient recuses the request
				$qstr='UPDATE mx_friends SET confirmed=2 '
				.' WHERE account1_id='.$qr->sender
				.' AND account2_id='.$userid;
				break;
			case MXREQIGNORED: // the recipient ignores the request
				$qstr='UPDATE mx_friends SET confirmed=3 '
				.' WHERE account1_id='.$qr->sender
				.' AND account2_id='.$userid;
				break;
		}
		$mxq=$this->query($qstr);
		if (!$mxq) die(mxerror($dbid->error,__FILE__,__LINE__,$qstr));
		if ($dbid->affected_rows>0) {
			if ($status==MXREQACCEPTED) mx_fbaction('musxpand:become_a_friend_of?fan='.mx_actionurl('fans','fanprof',$qr->sender));
			return $this->markmsg($userid,$msgid,$status);
		}
		else return 0;
	}

	function markwall($userid,$msgid,$status) {
		$dbid=$this->dbid;
		$rows=0;
		$qstr='UPDATE mx_walls SET flags='.$status
		.' WHERE msgid='.$msgid.' AND authid='.$userid;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return MXDBERROR;
		}
		return MXOK;

	}

	function markmsg($userid,$msgid,$status) {
		$dbid=$this->dbid;
		$rows=0;
		$crit=''; // cancel will delete for both recipient and sender
		$mask=~(MXREQCANCELLED|MXREQACCEPTED|MXREQIGNORED|MXREQRECUSED);
		if ($status!=MXREQCANCELLED) $crit=' AND receiver='.$userid;
		$qstr='UPDATE mx_msg2acc SET status=(status & '.$mask.') | '.$status
		.' WHERE msgid='.$msgid.$crit;
		$mxq=$this->query($qstr);
		if (!$mxq) die(mxerror($dbid->error,__FILE__,__LINE__,$qstr));
		$rows=$dbid->affected_rows;
		if ($status==MXMSGREAD) return $rows;
		$crit=''; // cancel will delete for both recipient and sender
		if ($status!=MXREQCANCELLED) $crit=' AND authid='.$userid;
		$qstr='UPDATE mx_messages SET sstatus=(sstatus & '.$mask.') | '.$status
		.' WHERE msgid='.$msgid.$crit;
		$mxq=$this->query($qstr);
		if (!$mxq) die(mxerror($dbid->error,__FILE__,__LINE__,$qstr));
		$rows+=$dbid->affected_rows;
		return $rows;
	}

	function checknewmessages($userid) {
		$dbid=$this->dbid;
		$qstr='SELECT COUNT(*) FROM mx_messages a, mx_msg2acc b' .
			' WHERE b.msgid=a.msgid AND b.receiver='.$userid.' AND b.status = 0';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			die(mxerror($dbid->error,__FILE__,__LINE__,$qstr));
			return null;
		}
		$qr=$mxq->fetch_row();
		$mxq->free();
		return $qr[0];
	}

	function setsubseen($userid) {
		$dbid=$this->dbid;
		$qstr='UPDATE mx_subscriptions SET status='.MXCURRENTSUB
				.' WHERE fanid='.$userid.' AND (status='.MXNEWSUB.' OR status='.MXRENEWEDSUB.')';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			die(mxerror($dbid->error,__FILE__,__LINE__,$qstr));
		}
	}

	function checksubs($userid) {
		$dbid=$this->dbid;
		$qstr='SELECT * FROM mx_subscriptions' .
				' WHERE fanid='.$userid;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			die(mxerror($dbid->error,__FILE__,__LINE__,$qstr));
			return null;
		}
		$expsubs=array();
		$subs=array(
			'expired' => 0,
			'new' => 0,
			'renewed' => 0,
			'changed' => 0,
		);
		while ($qr=$mxq->fetch_object()) {
			if ($qr->status!=MXPENDINGSUB && $qr->expiry && $qr->expiry!=MXSUBNOEXPIRY
			 && strtotime($qr->expiry)<time()
				&& $qr->status!=MXENDEDSUB) {
				$qr->status=MXEXPIREDSUB;
				$expsubs[]=$qr->id;
				$subs['expired']++;
				$subs['changed']++;
			}
			if ($qr->status==MXNEWSUB) {
				$subs['new']++;
				$subs['changed']++;
			}
			if ($qr->status==MXRENEWEDSUB) {
				$subs['renewed']++;
				$subs['changed']++;
			}
		}
		$mxq->free();
		if (count($expsubs)>0) {
			$qstr='UPDATE mx_subscriptions SET status='.MXEXPIREDSUB.', statusstamp=NOW()'
			. ' WHERE id=?';
			$mxq=$dbid->prepare($qstr);
			if (!$mxq) {
				mxerror($dbid->error,__FILE__,__LINE__,$qstr);
				return null;
			}
			$mxq->bind_param('i',$subsid);
			foreach ($expsubs as $subsid) {
				$mxq->execute();
			}
			$mxq->close();
		}
		return $subs;
	}

	function checkusername($name) {
		global $mxdb;
		$dbid=$this->dbid;
		$qstr='SELECT count(id) FROM mx_account WHERE username=\''.$dbid->real_escape_string($name).'\'';
		$mxq=$this->query($qstr);
		if (!$mxq || !$mxq->num_rows) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return 0;
		}
		$qr=$mxq->fetch_row();
		$mxq->free();
		return $qr[0];
	}

	function listmessages($user,$mxq=null,$orderkey='') {
		if (!$mxq) {
			if (!$orderkey || $orderkey == '') $orderkey = 'date desc';
			$dbid=$this->dbid;
			$mxq=$this->query('SELECT a.*,' .
					'b.status,b.sender,b.receiver,' .
					'c.fullname,c.artistname,c.gender,c.acctype,c.id' .
					' FROM mx_messages a, mx_msg2acc b, mx_account c' .
					' WHERE (( b.sender='.$user->id.' AND c.id=b.receiver)' .
					' OR (b.receiver='.$user->id.' AND c.id=b.sender))' .
					' AND a.msgid=b.msgid ' .
					' ORDER BY a.'.$orderkey);
			if (!$mxq) return null;
			return $mxq;
		}
		$qr=$mxq->fetch_object();
		if ($qr) return $qr;
		$mxq->free();
		return null;
	}

	function listfanwalls($user,$mxq=null,$orderkey='') {
		if (!$mxq) {
			if (!$orderkey || $orderkey == '') $orderkey = 'date desc';
			$dbid=$this->dbid;
			$qstr='SELECT DISTINCT a.* FROM mx_walls a, mx_subscriptions b'
					.' WHERE (a.flags IS NULL'
					.' OR (a.flags&'.(MXWALLDELETED+MXWALLFLAGGED).'=0))'
					.' AND (a.filter='.MXSHAREALL.' OR a.filter & '.MXSHAREARTISTS.')'
					.' AND a.authid=b.fanid AND b.subcat='.MXARTSUB.' AND b.objectid='.$user->id
					.' AND (b.expiry=DATE(\''.MXSUBNOEXPIRY.'\') OR b.expiry>NOW())'
					.' AND refid IS NULL'
					.' ORDER BY '.$orderkey;
			$mxq=$this->query($qstr);
			//die($qstr);
			if (!$mxq) return null;
			return $mxq;
		}
		$qr=$mxq->fetch_object();
		if ($qr) return $qr;
		$mxq->free();
		return null;
	}

	function listartwalls($user,$mxq=null,$orderkey='') {
		if (!$mxq) {
			if (!$orderkey || $orderkey == '') $orderkey = 'date desc';
			$dbid=$this->dbid;
			$qstr='SELECT a.* FROM mx_walls a, mx_subscriptions b'
					.' WHERE (a.flags IS NULL'
					.' OR (a.flags&'.(MXWALLDELETED+MXWALLFLAGGED).'=0))'
					.' AND (a.filter='.MXSHAREALL.' OR a.filter & '.MXSHAREFANS.')'
					.' AND a.authid=b.objectid AND b.subcat='.MXARTSUB.' AND b.fanid='.$user->id
					.' AND (b.expiry=DATE(\''.MXSUBNOEXPIRY.'\') OR b.expiry>NOW())'
					.' AND refid IS NULL'
					.' ORDER BY '.$orderkey;
			$mxq=$this->query($qstr);
			//die($qstr);
			if (!$mxq) return null;
			return $mxq;
		}
		$qr=$mxq->fetch_object();
		if ($qr) return $qr;
		$mxq->free();
		return null;
	}

	function listfrwalls($user,$mxq=null,$orderkey='') {
		if (!$mxq) {
			if (!$orderkey || $orderkey == '') $orderkey = 'date desc';
			$dbid=$this->dbid;
			$qstr='SELECT a.* FROM mx_walls a, mx_friends b'
					.' WHERE (a.flags IS NULL'
					.' OR (a.flags&'.(MXWALLDELETED+MXWALLFLAGGED).'=0))'
					.' AND (a.filter='.MXSHAREALL.' OR a.filter & '.MXSHAREFRIENDS.')'
					.' AND ((a.authid=b.account1_id AND b.account2_id='.$user->id.')'
					.'  OR  (a.authid=b.account2_id AND b.account1_id='.$user->id.'))'
					.' AND b.confirmed='.MXFRIEND
					.' AND refid IS NULL'
					.' ORDER BY '.$orderkey;
			$mxq=$this->query($qstr);
			//die($qstr);
			if (!$mxq) return null;
			return $mxq;
		}
		$qr=$mxq->fetch_object();
		if ($qr) return $qr;
		$mxq->free();
		return null;
	}

	function listmywalls($userid,$mxq=null,$orderkey='') {
		if (!$mxq) {
			if (!$orderkey || $orderkey == '') $orderkey = 'date desc';
			$dbid=$this->dbid;
			$mxq=$this->query('SELECT * FROM mx_walls'
				.' WHERE authid='.$userid
				.' AND refid IS NULL'
				.' AND (flags IS NULL OR flags&'.MXWALLDELETED.'=0)'
				.' ORDER BY '.$orderkey);
			if (!$mxq) return null;
			return $mxq;
		}
		$qr=$mxq->fetch_object();
		if ($qr) return $qr;
		$mxq->free();
		return null;
	}


	function listwalls($requester,$userid,$mxq=null,$orderkey='') {
		if (!$mxq) {
			$privacy=$this->getprivacy($requester->id,$userid);
			$filter='filter='.MXSHAREALL;
			if ($privacy['isfriend']) $filter.=' OR filter='.MXSHAREFRIENDS;
			if ($privacy['isfan']) $filter.=' OR filter='.MXSHAREFANS;
			if ($requester->acctype==MXARTIST || $requester->acctype==MXBAND) $filter.=' OR filter='.MXSHAREARTISTS;
			if ($requester->id==$userid) $filter='TRUE';
			if (!$orderkey || $orderkey == '') $orderkey = 'date desc';
			$dbid=$this->dbid;
			$qstr='SELECT * FROM mx_walls'
				.' WHERE authid='.$userid
				.' AND ('.$filter.')'
				.' AND refid IS NULL'
				.' AND (flags IS NULL'
				.' OR flags &'.(MXWALLDELETED+MXWALLFLAGGED).'=0)'
				.' ORDER BY '.$orderkey;
			$mxq=$this->query($qstr);
			//error_log($qstr);
			if (!$mxq) return null;
			return $mxq;
		}
		$qr=$mxq->fetch_object();
		if ($qr) return $qr;
		$mxq->free();
		return null;
	}

	function getcomments($typeid,$refid,$mxq=null,$orderkey='') {
		if (!$mxq) {
			if (!$orderkey || $orderkey == '') $orderkey = 'date asc';
			$dbid=$this->dbid;
			$qstr='SELECT w.* FROM mx_walls w, mx_refs r'
					.' WHERE r.'.$typeid.'='.$refid
					.' AND refid=r.id'
					.' AND (flags IS NULL'
					.' OR (flags &'.(MXWALLDELETED+MXWALLFLAGGED).'=0))'
					.' ORDER BY '.$orderkey;
			$mxq=$this->query($qstr);
			//die($qstr);
			if (!$mxq) return null;
			return $mxq;
		}
		$qr=$mxq->fetch_object();
		if ($qr) return $qr;
		$mxq->free();
		return null;
	}

	function setartlike($userid,$artistid,$like) {
		$dbid=$this->dbid;
		$fanship=$this->getfanship($userid,$artistid);
		$qstr='';
		if ($like && $fanship[0]!=MXNONMEMBER && $fanship[0]!=MXME && $fanship[0]!=MXFAN) { // set like
			$qstr='INSERT INTO mx_subscriptions SET fanid='.$userid.',subcat='.MXARTSUB.',objectid='.$artistid
			.',subtype='.MXSUBLIKE.',expiry=\''.MXSUBNOEXPIRY.'\',status='.MXCURRENTSUB
			.',statusstamp=NOW(),firstsub=NOW()';
		} else if (!$like && $fanship[0]!=MXNONMEMBER && $fanship[0]!=MXME && $fanship[0]!=MXFAN) { // unset like
			$qstr='DELETE FROM mx_subscriptions WHERE fanid='.$userid.' AND subcat='.MXARTSUB.' AND objectid='.$artistid
			.' AND subtype='.MXSUBLIKE;
		}
		if ($qstr) {
			$mxq=$this->query($qstr);
			if (!$mxq) {
				mxerror($dbid->error,__FILE__,__LINE__,$qstr);
				return null;
			}
		}
		$qstr='SELECT COUNT(*) FROM mx_subscriptions WHERE fanid='.$userid.' AND subcat='.MXARTSUB.' AND objectid='.$artistid
		.' AND subtype='.MXSUBLIKE;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
		if (!$mxq->num_rows)
			return null;
		$qr=$mxq->fetch_row();
		$mxq->free();
		return $qr[0];
	}

	/*
	 * Update wall likes/dislikes and returns the wall
	 */
	function setlikes($userid, $wallid, $likes=0, $dislikes=0) {
		$mylikes=$this->getlikes($userid,$wallid);
		$dbid=$this->dbid;
		if ($mylikes==NULL) { // no previous opinion
			$qstr='INSERT INTO mx_likes SET wallid='.$wallid
			.', authid='.$userid
			.', type='.($likes?MXLIKEIT:MXDISLIKEIT);
			$mxq=$this->query($qstr);
			//$mxq->free();
			$qstr='UPDATE mx_walls SET '.($likes?'likes=likes+1':'dislikes=dislikes+1')
			.' WHERE msgid='.$wallid;
			$mxq=$this->query($qstr);
			//$mxq->free();
		} else { // opinion change
			if (($mylikes==MXLIKEIT && $dislikes) // from like to dislike
			|| ($mylikes==MXDISLIKEIT && $likes)) {// from dislike to like
				$qstr='UPDATE mx_likes SET type='.($likes?MXLIKEIT:MXDISLIKEIT).' WHERE wallid='.$wallid
					.' AND authid='.$userid;
				$mxq=$this->query($qstr);
				//$mxq->free();
				$qstr='UPDATE mx_walls SET likes='.($likes?'likes+1':'likes-1')
				.', dislikes='.($likes?'dislikes-1':'dislikes+1').' WHERE msgid='.$wallid;
				$mxq=$this->query($qstr);
				//$mxq->free();
			} else { // canceling previous opinion
				$qstr='DELETE FROM mx_likes WHERE wallid='.$wallid
					.' AND authid='.$userid;
				$mxq=$this->query($qstr);
				//$mxq->free();
				$qstr='UPDATE mx_walls SET '.($likes?'likes=likes-1':'dislikes=dislikes-1')
				.' WHERE msgid='.$wallid;
				$mxq=$this->query($qstr);
				//$mxq->free();
			}
		}
		$qstr='SELECT likes,dislikes FROM mx_walls WHERE msgid='.$wallid;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
		if (!$mxq->num_rows)
			return null;
		$qr=$mxq->fetch_object();
		$mxq->free();
		return $qr;
	}


	function getlikes($userid, $wallid) {
		$dbid=$this->dbid;
		$qstr='SELECT type FROM mx_likes WHERE wallid='.$wallid
			.' AND authid='.$userid;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
		if (!$mxq->num_rows)
			return null;
		$qr=$mxq->fetch_row();
		$mxq->free();
		return $qr[0];
	}

	function checkfile($uid,$fid,$fname) {
		$dbid=$this->dbid;
		$mxq=$this->query('SELECT id,hashcode,type,status,title FROM mx_media WHERE owner_id='.$uid.
			' AND id='.$fid.' AND filename=\''.$dbid->real_escape_string($fname).'\'');
		if (!$mxq || $mxq->num_rows==0) return null;
		$qr=$mxq->fetch_object();
		$mxq->free();
		return $qr;
	}

	function getfriends($userid,$id) { // $userid=requester - id=person whose friends are requested
		$dbid=$this->dbid;
		$qstr='SELECT a.confirmed,b.id FROM mx_friends a,mx_account b WHERE ((a.account1_id='.$id
			// don't consider requests recused or ignored by others ;-)
			.' AND b.id=a.account2_id AND a.confirmed!='.MXRECUSEDFRIEND.' AND a.confirmed!='.MXIGNOREDFRIEND.')'
			// but consider the ones recused or ignored by the person
			.' OR (a.account2_id='.$id.' AND b.id=a.account1_id))'
			.' AND b.status!='.MXACCTDISABLED
			.' ORDER BY b.fullname';
		$mxq=$this->query($qstr);
		$friends=array(
			'confirmed' => array(),
			'pending' => array(),
			'recused' => array(),
			'ignored' => array()
		);
		if (!$mxq) return $friends;
		while ($qr=$mxq->fetch_object()) {
			$friend=$qr->id; //($qr->account1_id==$userid?$qr->account2_id:$qr->account1_id);
			switch ($qr->confirmed) {
				case MXPENDINGFRIEND:
					$friends['pending'][]=$friend;
					break;
				case MXFRIEND:
					$friends['confirmed'][]=$friend;
					break;
				case MXRECUSEDFRIEND:
					$friends['recused'][]=$friend;
					break;
				case MXIGNOREDFRIEND:
					$friends['ignored'][]=$friend;
					break;
			}
		}
		$mxq->free();
		return $friends;
	}

	function getidfromusername($id) {
		$dbid=$this->dbid;
		if (!$id) return 0;
		$qstr='SELECT id FROM mx_account WHERE username=\''.$dbid->real_escape_string($id).'\'';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return 0;
		}
		$qr=$mxq->fetch_row();
		return $qr[0];
	}

	function checkfbfriends($userid,$fbids) {
	// $userid = requester
	// $ids = FB ids table
		$dbid=$this->dbid;
		$friends=array();
		foreach($fbids as $fbid) {
			$mxq=$this->query('SELECT id FROM mx_account WHERE fbid='.$fbid);
			if ($mxq) {
				$qr=$mxq->fetch_object();
				if ($qr) {
					$id=$qr->id;
					$mxq2=$this->query('SELECT id FROM mx_friends' .
						' WHERE (account1_id='.$id.
						' AND account2_id='.$userid.')'.
						' OR (account2_id='.$id.
						' AND account1_id='.$userid.')');
					if (!$mxq2 || $mxq2->num_rows==0) $friends[]=$id;
					if ($mxq2) $mxq2->free();
				}
				$mxq->free();
			}
		}
		return $friends;
	}

	function publishmedia($user,$fid,$status) {
		$dbid=$this->dbid;
		$qstr='UPDATE mx_media SET status='.$status
			.', activation=NOW()'
			.' WHERE owner_id='.$user->id
			.' AND id='.$fid;
		$mxq=$this->query($qstr);
		if (!$mxq) return(array('error' => mxerror($dbid->error,__FILE__,__LINE__,$qstr)));
		//$mxq->free();
		$qstr='UPDATE mx_media a, mx_med2bun b SET a.status='.MXMEDIAPUBLIC
		.', a.activation=NOW()'
		.' WHERE a.owner_id='.$user->id
		.' AND a.id=b.bundleid AND b.mediaid='.$fid.' AND a.status='.MXMEDIAREADY;
		$mxq=$this->query($qstr);
		if (!$mxq) return(array('error' => mxerror($dbid->error,__FILE__,__LINE__,$qstr)));
		return array('success' => true);
	}

	function setmediastatus($user,$fid,$status) {
		$dbid=$this->dbid;
		$qstr='UPDATE mx_media SET status='.$status
			.' WHERE owner_id='.$user->id
			.' AND id='.$fid;
		$mxq=$this->query($qstr);
		if (!$mxq) mxerror($dbid->error,__FILE__,__LINE__,$qstr);
		if ($status>=MXMEDIAFANVISIBLE && $status<=MXMEDIAPUBLICSHARED) {
			$qset='a.status='.MXMEDIAFANVISIBLE;
			$qwhere='AND (a.status<'.MXMEDIAFANVISIBLE.' OR a.status>'.MXMEDIAPUBLICSHARED.') AND a.status!='.MXMEDIASUSPENDED;
		} else if ($status==MXMEDIAARCHIVED) {
			$qset='a.status='.MXMEDIAARCHIVED;
			$qwhere='';
		} else if ($status==MXMEDIANEW) {
			$qset='a.status='.MXMEDIAREADY;
			$qwhere='AND (a.status<='.MXMEDIAPUBLICSHARED.')';
		}
		if ($status<=MXMEDIAREADY) return array('success' => true);
		$qstr='UPDATE mx_media a, mx_med2bun b SET '.$qset
		.' WHERE a.owner_id='.$user->id
		.' AND b.bundleid='.$fid.' AND a.id=b.mediaid '.$qwhere;
		$mxq=$this->query($qstr);
		if (!$mxq) mxerror($dbid->error,__FILE__,__LINE__,$qstr);
		return array('success' => true);
	}

	function resetid3info($user,$fid,$id3info) {
		$dbid=$this->dbid;
		$qstr='UPDATE mx_media SET id3info=\''.addslashes(serialize($id3info)).'\''
			.' WHERE owner_id='.$user->id
			.' AND id='.$fid;
		$mxq=$this->query($qstr);
		if (!$mxq) mxerror($dbid->error,__FILE__,__LINE__,$qstr);
	}

	function setmediapic($user,$fid,$picext) {
		$dbid=$this->dbid;
		$qstr='UPDATE mx_media SET haspic=\''.$picext.'\''
			.' WHERE owner_id='.$user->id
			.' AND id='.$fid;
		$mxq=$this->query($qstr);
		if (!$mxq) mxerror($dbid->error,__FILE__,__LINE__,$qstr);
	}

	function setmediafield($user,$fid,$fld,$value) {
		$dbid=$this->dbid;
		$qstr='UPDATE mx_media SET '.$fld.'='.$value.''
			.' WHERE owner_id='.$user->id
			.' AND id='.$fid;
		$mxq=$this->query($qstr);
		if (!$mxq) mxerror($dbid->error,__FILE__,__LINE__,$qstr);
	}


	function updatemediadesc($user,$fid,$ftitle,$ftype,$fdesc,$fcomp) {
		$dbid=$this->dbid;
		$qstr='UPDATE mx_media SET title=\''.$dbid->real_escape_string($ftitle)
			.'\', type='.$ftype
			.', description=\''.$dbid->real_escape_string($fdesc).'\''
			.', completion='.$fcomp
			.' WHERE owner_id='.$user->id
			.' AND id='.$fid;
		$mxq=$this->query($qstr);
		if (!$mxq) return(array('error' => mxerror($dbid->error,__FILE__,__LINE__,$qstr)));
		//$mxq->free();
		return array('success' => true);
	}

	function updatemediainfo($user,$fid,$field,$text) {
		$dbid=$this->dbid;
		switch ($field) {
			case 'title':
				$fld='title';
				break;
			case 'desc':
				$fld='description';
				break;
			default:
				return array('error' => 'unknown field');
		}
		$qstr='UPDATE mx_media SET '.$fld.'=\''.$dbid->real_escape_string($text).'\''
			.' WHERE owner_id='.$user->id
			.' AND id='.$fid;
		$mxq=$this->query($qstr);
		if (!$mxq) return(array('error' => mxerror($dbid->error,__FILE__,__LINE__,$qstr)));
		//$mxq->free();
		return array('success' => true);
	}

	function updatemedia($user,$fid,$fname,$ftitle,$ftype=MXMEDIAUNDEFINED,$fdesc,$fhash,$fcomp,$status=MXMEDIAVALIDATED) {
		$dbid=$this->dbid;
		$qstr='UPDATE mx_media SET title=\''.$dbid->real_escape_string($ftitle)
			.'\', type='.$ftype.','
			.' description=\''.$dbid->real_escape_string($fdesc).'\''
			.', completion='.$fcomp
			.', status='.$status.' WHERE owner_id='.$user->id
			.' AND id='.$fid.' AND filename=\''.$dbid->real_escape_string($fname).'\'' .
					' AND hashcode=\''.$dbid->real_escape_string($fhash).'\'';
		$mxq=$this->query($qstr);
		if (!$mxq) return(array('error' => mxerror($dbid->error,__FILE__,__LINE__,$qstr)));
		//$mxq->free();
		return array('success' => true);
	}

	function movetobundle($userid,$mid,$bid,$pos=0) {
		$dbid=$this->dbid;
		$qstr='DELETE FROM mx_med2bun WHERE mediaid='.$mid;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return MXDBERROR;
		}
		/*$qstr='UPDATE mx_med2bun, mx_media a, mx_media b SET bundleid='.$bid.',position='.$pos
		.' WHERE (bundleid='.$mid.' OR mediaid='.$mid.')'
		.' AND a.id=mediaid AND b.id='.$bid.' AND a.owner_id='.$userid.' AND b.owner_id='.$userid
		.' AND (a.status = '.MXMEDIAREADY.' OR b.status > '.MXMEDIAREADY.' OR a.status = b.status OR b.status='.MXMEDIAVIRTUAL.')';*/
		$qstr='INSERT INTO mx_med2bun SET mediaid='.$mid.',bundleid='.$bid.',position='.$pos;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return MXDBERROR;
		}
		if (!$dbid->affected_rows) {
			return MXNOCHANGE;
		}
		/*
		$qstr='UPDATE mx_med2bun SET position=position+1 WHERE bundleid='.$bid.' AND mediaid!='.$mid.' AND position>='.$pos;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return MXDBERROR;
		}
		*/
		return MXOK;
	}

	function linkmedia($userid,$lid,$id) {
		$dbid=$this->dbid;
		$qstr='SELECT count(id) FROM mx_med2med WHERE mediaid1='.$id.' AND mediaid2='.$lid;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return MXDBERROR;
		}
		$qr=$mxq->fetch_row();
		$mxq->free();
		if ($qr[0]>0) return MXOK; // already linked

		$qstr='SELECT COUNT(a.id) FROM mx_media a, mx_media b WHERE a.id='.$lid.' AND a.owner_id='.$userid
		.' AND b.owner_id=a.owner_id AND b.id='.$id;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return MXDBERROR;
		}
		$qr=$mxq->fetch_row();
		$mxq->free();
		if (!$qr[0]) return MXNOLINK; // not owner!!

		$qstr='INSERT INTO mx_med2med SET mediaid1='.$id.', mediaid2='.$lid;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return MXDBERROR;
		}
		if (!$dbid->affected_rows) {
			return MXNOLINK;
		}
		return MXOK;
	}

	function is_owner($userid,$id) {
		$dbid=$this->dbid;
		//error_log('userid='.$userid);
		$qstr='SELECT id FROM mx_media WHERE id='.$id.' AND owner_id='.$userid;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return false;
		}
		if (!$mxq->num_rows) return false;
		$mxq->free();
		return true;
	}

	function unlinkmedia($userid,$lid,$id) {
		$dbid=$this->dbid;
		if (!$this->is_owner($userid,$id)) return;
		$qstr='DELETE FROM mx_med2med WHERE mediaid1='.$id.' AND mediaid2='.$lid;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
		}
	}

	function getlinkedmedia($userid,$id) {
		$dbid=$this->dbid;
		$linked=array();
		$qstr='SELECT mediaid2,m.id,m.filename,m.type,m.hashcode FROM mx_med2med, mx_media m WHERE mediaid1='.$id.' AND m.id=mediaid2';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return $linked;
		}
		while ($qr=$mxq->fetch_object()) {
			$linked[]=$qr;
		}
		$mxq->free();
		return $linked;
	}

	function deletemedia($user,$fid,$fname) {
		$dbid=$this->dbid;
		$qstr='DELETE FROM mx_media WHERE owner_id='.$user->id
			.' AND id='.$fid.' AND filename=\''.$dbid->real_escape_string($fname).'\'';
		$mxq=$this->query($qstr);
		if (!$mxq) return(array('error' => mxerror($dbid->error,__FILE__,__LINE__,$qstr)));
		$qstr='DELETE FROM mx_med2bun WHERE mediaid='.$fid;
		$mxq=$this->query($qstr);
		if (!$mxq) return(array('error' => mxerror($dbid->error,__FILE__,__LINE__,$qstr)));
			//$mxq->free();
		return array('success' => true);
	}

	function archivemedia($user,$fid,$fname) {
		$dbid=$this->dbid;
		$qstr='UPDATE mx_media set status='.MXMEDIAARCHIVED.' WHERE owner_id='.$user->id
			.' AND id='.$fid.' AND filename=\''.$dbid->real_escape_string($fname).'\'';
		$mxq=$this->query($qstr);
		if (!$mxq) return(array('error' => mxerror($dbid->error,__FILE__,__LINE__,$qstr)));
		//$mxq->free();
		return array('success' => true);
	}

	function getstream($id,$hashcode) {
		$dbid=$this->dbid;
		$mxq=$this->query('SELECT filename,type,filesize FROM mx_media WHERE owner_id='.$id.
			' AND hashcode=\''.$hashcode.'\'');
		if (!$mxq) mxerror($dbid->error,__FILE__,__LINE__);
		$qr=$mxq->fetch_object();
		$mxq->free();
		return $qr;
	}

	function getbackgrounds($id,$mxq=null) {
		if (!$mxq) {
			$dbid=$this->dbid;
			$qstr='SELECT a.*,b.hashdir FROM mx_media a, mx_account b WHERE b.id = a.owner_id AND ( a.owner_id='.$id.
			' OR a.status = 30 ) AND (a.type = '.MXMEDIABG.' OR a.type = '.MXMEDIAPIC.') ORDER BY a.timestamp desc';
			$mxq=$this->query($qstr);
			if (!$mxq) mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return $mxq;
		}
		$qr=$mxq->fetch_object();
		if ($qr) return $qr;
		$mxq->free();
		return null;
	}

	function getpics($id,$mxq=null) {
		if (!$mxq) {
			$dbid=$this->dbid;
			$qstr='SELECT * FROM mx_media WHERE owner_id='.$id.
			' AND type = '.MXMEDIAPIC.' ORDER BY timestamp desc';
			$mxq=$this->query($qstr);
			if (!$mxq) die(mxerror($dbid->error,__FILE__,__LINE__,$qstr));
			if (!$mxq->num_rows) return null;
			return $mxq;
		}
		$qr=$mxq->fetch_object();
		if ($qr) return $qr;
		$mxq->free();
		return null;
	}

	function getbackground($user_id,$media_id) {
		$dbid=$this->dbid;
		$checkowner=' AND ( a.owner_id = '.$user_id.' OR a.status = 30 )';
		$checkowner=''; // don't check
		$mxq=$this->query('SELECT a.*,b.hashdir FROM mx_media a, mx_account b WHERE b.id = a.owner_id AND a.id = '.$media_id
		.$checkowner.' AND (a.type = '.MXMEDIABG.' OR a.type='.MXMEDIAPIC.')');
		if (!$mxq) return null; //mxerror($dbid->error,__FILE__,__LINE__);
		$qr=$mxq->fetch_object();
		$mxq->free();
		return $qr;
	}

	function updatetables() {
		include_once('includes/mx_dbupdate.php');
		$db_tables=preg_replace('%CREATE TABLE (IF NOT EXISTS )?`([^`]+)`%','CREATE TABLE \2',$db_tables);
		//return array($db_tables);
		$err=$this->dbupdate($db_tables,true);
		$dbid=$this->dbid;
		$qstr='SELECT COUNT(cc_fips) FROM mx_countries';
		$mxq=$this->query($qstr);
		if ($mxq) $mxq->free();
		$qstr=$db_countries;
		//error_log($qstr);
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
		}
		return $err;
	}

	function gettables() {
		$dbid=$this->dbid;
		$mxq=$this->query('SHOW TABLES');
		if (!$mxq) return null;
		while ($table=$mxq->fetch_row()) {
			$tables[]=$table[0];
		}
		return $tables;
	}

	function getresults($query) {
		$dbid=$this->dbid;
		$mxq=$this->query($query);
		if (!$mxq) return null;
		while ($qr=$mxq->fetch_object()) {
			$res[]=$qr;
		};
		return $res;
	}

	function dbupdate($queries, $execute = true) {
		$dbid=$this->dbid;

		// Separate individual queries into an array
		if( !is_array($queries) ) {
			$queries = explode( ';', $queries );
			if('' == $queries[count($queries) - 1]) array_pop($queries);
		}

		$cqueries = array(); // Creation Queries
		$iqueries = array(); // Insertion Queries
		$for_update = array();

		// Create a tablename index for an array ($cqueries) of queries
		foreach($queries as $qry) {
			if(preg_match("|CREATE TABLE ([^ ]*)|", $qry, $matches)) {
				$cqueries[trim( strtolower($matches[1]), '`' )] = $qry;
				$for_update[$matches[1]] = 'Created table '.$matches[1];
			}
			else if(preg_match("|CREATE DATABASE ([^ ]*)|", $qry, $matches)) {
				array_unshift($cqueries, $qry);
			}
			else if(preg_match("|INSERT INTO ([^ ]*)|", $qry, $matches)) {
				$iqueries[] = $qry;
			}
			else if(preg_match("|UPDATE ([^ ]*)|", $qry, $matches)) {
				$iqueries[] = $qry;
			}
			else {
				// Unrecognized query type
			}
		}

		// Check to see which tables and fields exist
		if($tables = $this->gettables()) {
			// For every table in the database
			foreach($tables as $table) {
				// If a table query exists for the database table...
				if( array_key_exists(strtolower($table), $cqueries) ) {
					// Clear the field and index arrays
					unset($cfields);
					unset($indices);
					// Get all of the field names in the query from between the parens
					preg_match("|\((.*)\)|ms", $cqueries[strtolower($table)], $match2);
					$qryline = trim($match2[1]);

					// Separate field lines into an array
					$flds = explode("\n", $qryline);

					//echo "<hr/><pre>\n".print_r(strtolower($table), true).":\n".print_r($cqueries, true)."</pre><hr/>";

					// For every field line specified in the query
					foreach($flds as $fld) {
						// Extract the field name
						preg_match("|^([^ ]*)|", trim($fld), $fvals);
						$fieldname = trim( $fvals[1], '`' );

						// Verify the found field name
						$validfield = true;
						switch(strtolower($fieldname))
						{
						case '':
						case 'primary':
						case 'index':
						case 'fulltext':
						case 'unique':
						case 'key':
							$validfield = false;
							$indices[] = preg_replace('%`%','',trim(trim($fld), ", \n"));
							break;
						}
						$fld = trim($fld);

						// If it's a valid field, add it to the field array
						if($validfield) {
							$cfields[strtolower($fieldname)] = trim($fld, ", \n");
						}
					}
					// Fetch the table column structure from the database
					$tablefields = $this->getresults("DESCRIBE {$table};");
					// For every field in the table
					foreach($tablefields as $tablefield) {
						// If the table field exists in the field array...
						if(array_key_exists(strtolower($tablefield->Field), $cfields)) {
							// Get the field type from the query
							preg_match("|`".$tablefield->Field."` ([^ ]*( unsigned)?)|i", $cfields[strtolower($tablefield->Field)], $matches);
							$fieldtype = $matches[1];

							// Is actual field type different from the field type in query?
							if($tablefield->Type != $fieldtype) {
								// Add a query to change the column type
								$cqueries[] = "ALTER TABLE {$table} CHANGE COLUMN {$tablefield->Field} " . $cfields[strtolower($tablefield->Field)];
								$for_update[$table.'.'.$tablefield->Field] = "Changed type of {$table}.{$tablefield->Field} from {$tablefield->Type} to {$fieldtype}";
							}

							// Get the default value from the array
								//echo "{$cfields[strtolower($tablefield->Field)]}<br>";
							if(preg_match("| DEFAULT '(.*)'|i", $cfields[strtolower($tablefield->Field)], $matches)) {
								$default_value = $matches[1];
								if($tablefield->Default != $default_value)
								{
									// Add a query to change the column's default value
									$cqueries[] = "ALTER TABLE {$table} ALTER COLUMN {$tablefield->Field} SET DEFAULT '{$default_value}'";
									$for_update[$table.'.'.$tablefield->Field] = "Changed default value of {$table}.{$tablefield->Field} from {$tablefield->Default} to {$default_value}";
								}
							}

							// Remove the field from the array (so it's not added)
							unset($cfields[strtolower($tablefield->Field)]);
						}
						else {
							// This field exists in the table, but not in the creation queries?
						}
					}

					// For every remaining field specified for the table
					foreach($cfields as $fieldname => $fielddef) {
						// Push a query line into $cqueries that adds the field to that table
						$cqueries[] = "ALTER TABLE {$table} ADD COLUMN $fielddef";
						$for_update[$table.'.'.$fieldname] = 'Added column '.$table.'.'.$fieldname;
					}

					// Index stuff goes here
					// Fetch the table index structure from the database
					$tableindices = $this->getresults("SHOW INDEX FROM {$table};");

					if($tableindices) {
						// Clear the index array
						unset($index_ary);

						// For every index in the table
						foreach($tableindices as $tableindex) {
							// Add the index to the index data array
							$keyname = $tableindex->Key_name;
							$index_ary[$keyname]['columns'][] = array('fieldname' => $tableindex->Column_name, 'subpart' => $tableindex->Sub_part);
							$index_ary[$keyname]['unique'] = ($tableindex->Non_unique == 0)?true:false;
						}

						// For each actual index in the index array
						foreach($index_ary as $index_name => $index_data) {
							// Build a create string to compare to the query
							$index_string = '';
							if($index_name == 'PRIMARY') {
								$index_string .= 'PRIMARY ';
							}
							else if($index_data['unique']) {
								$index_string .= 'UNIQUE ';
							}
							$index_string .= 'KEY';
							if($index_name != 'PRIMARY') {
								$index_string .= ' '.$index_name;
							}
							$index_columns = '';
							// For each column in the index
							foreach($index_data['columns'] as $column_data) {
								if($index_columns != '') $index_columns .= ',';
								// Add the field to the column list string
								$index_columns .= $column_data['fieldname'];
								if($column_data['subpart'] != '') {
									$index_columns .= '('.$column_data['subpart'].')';
								}
							}
							// Add the column list to the index create string
							$index_string .= ' ('.$index_columns.')';
							if(!(($aindex = array_search($index_string, $indices)) === false)) {
								unset($indices[$aindex]);
								//echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">{$table}:<br />Found index:".$index_string."</pre>\n";
							}
							//else echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">{$table}:<br /><b>Did not find index:</b>".$index_string."<br />".print_r($indices, true)."</pre>\n";
						}
					}

					// For every remaining index specified for the table
					foreach ( (array) $indices as $index ) {
						// Push a query line into $cqueries that adds the index to that table
						$cqueries[] = "ALTER TABLE {$table} ADD $index";
						$for_update[$table.'.'.$fieldname] = 'Added index '.$table.' '.$index;
					}

					// Remove the original table creation query from processing
					unset($cqueries[strtolower($table)]);
					unset($for_update[strtolower($table)]);
				} else {
					// This table exists in the database, but not in the creation queries?
				}
			}
		}

		$allqueries = array_merge($cqueries, $iqueries);
		if($execute) {
			foreach($allqueries as $query) {
				//echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">".print_r($query, true)."</pre>\n";
				$this->query($query);
			}
		}

		return $for_update;
	}

	function addsubpending($userid,$cartline) {
		$dbid=$this->dbid;
		$qstr='SELECT * FROM mx_subscriptions WHERE fanid='.$userid
		.' AND subcat='.$cartline->prodtype.' AND objectid='.$cartline->prodref;
		$mxq=$this->query($qstr);
		$subcmd='';
		if ($mxq && $mxq->num_rows>0) {
			while ($qr=$mxq->fetch_object()) {
				if ($cartline->prodtype==MXARTSUB) {
					if ($qr->subtype==MXSUBLIKE) $subcmd='UPDATE mx_subscriptions SET status='.MXPENDINGSUB
					.',subtype='.$cartline->prodvar.', statusstamp=NOW(), expiry=\'0000-00-00\', firstsub=\'0000-00-00\''
					.' WHERE id='.$qr->id;
					else if ($qr->status!=MXCURRENTSUB) $subcmd='UPDATE mx_subscriptions SET status='.MXPENDINGSUB
					.' WHERE id='.$qr->id;
				} else if ($cartline->prodtype==MXMEDSUB) {
					$subcmd='UPDATE mx_subscriptions SET status='.MXPENDINGSUB
					.' WHERE id='.$qr->id; // media previous ordered but not paid for ?
				} else if ($cartline->prodtype==MXSITESUB) {
					if ($qr->subtype==$cartline->prodvar && $qr->status!=MXEXPIREDSUB) {
						$subcmd='UPDATE mx_subscriptions SET status='.MXPENDINGSUB
						.' WHERE id='.$qr->id;
					}
				}
			}
			$mxq->free();
		}
		if (!$subcmd) {
			$subcmd='INSERT INTO mx_subscriptions SET fanid='.$userid.
			', subcat='.$cartline->prodtype.', objectid='.$cartline->prodref.', subtype='.$cartline->prodvar.', status='.
			MXPENDINGSUB.', statusstamp=NOW(), expiry=\'0000-00-00\', firstsub=\'0000-00-00\'';
		}
		error_log('DB addsubpending: userid='.$userid.' cart='.print_r($cartline,true).' cmd='.$subcmd);
		if ($mxq) $mxq->free();
		$qstr=$subcmd;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
	}

	function setsubpaid($userid,$cartline) {
		$dbid=$this->dbid;
		$qstr='SELECT * FROM mx_subscriptions WHERE fanid='.$userid
		.' AND subcat='.$cartline->prodtype.' AND objectid='.$cartline->prodref;
		$mxq=$this->query($qstr);
		if (!$mxq || !$mxq->num_rows) {
			if (!$mxq) mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			else error_log('DB setsubpaid: sub not found = '.$qstr);
			return null;
		}
		$qstr2='';
		while ($qr=$mxq->fetch_object()) {
			if ($cartline->prodtype==MXARTSUB) {
				if ($cartline->prodvar==MXSUBFOY) {
					if ($qr->expiry && $qr->expiry!='0000-00-00') {
						$expiry='expiry=DATE_ADD('.$qr->expiry.',INTERVAL 1 YEAR)';
						$status=MXRENEWEDSUB;
					} else {
						$expiry='expiry=DATE_ADD(NOW(),INTERVAL 1 YEAR)';
						$status=MXNEWSUB;
					}
				}
				else {
					$expiry='expiry=99990101';
					$status=MXNEWSUB;
				}
				$firstsub=($qr->firstsub && $qr->firstsub!='0000-00-00')?('\''.$qr->firstsub.'\''):'CURDATE()';
				$qstr2='UPDATE mx_subscriptions SET subtype='.$cartline->prodvar.', status='.
					$status.', statusstamp=NOW(), firstsub='.$firstsub.', '.$expiry
					.' WHERE id='.$qr->id;
				$subprofile=$qr->ppprofileid;
			} else if ($cartline->prodtype==MXMEDSUB) {
				$expiry='expiry=99990101';
				$status=MXNEWSUB;
				$firstsub=($qr->firstsub && $qr->firstsub!='0000-00-00')?('\''.$qr->firstsub.'\''):'CURDATE()';
				$qstr2='UPDATE mx_subscriptions SET subtype='.$cartline->prodvar.', status='.
					$status.', statusstamp=NOW(), firstsub='.$firstsub.', '.$expiry
					.' WHERE id='.$qr->id;
				$subprofile=$qr->ppprofileid;
			} else if ($cartline->prodtype==MXSITESUB) {
				if ($qr->subtype==$cartline->prodvar && $qr->status!=MXEXPIREDSUB) { // renewal or activation
					if ($qr->expiry && $qr->expiry!='0000-00-00') {
						$expiry='expiry=DATE_ADD('.$qr->expiry.',INTERVAL 1 MONTH)';
						$status=MXRENEWEDSUB;
					} else {
						$expiry='expiry=DATE_ADD(NOW(),INTERVAL 1 MONTH)';
						$status=MXNEWSUB;
					}
					$subprofile['new']=$qr->ppprofileid;
				} else if ($qr->status!=MXEXPIREDSUB) { // subscription up or downgrade (cancel others)
					$expiry='expiry=CURDATE()';
					$status=MXEXPIREDSUB;
					$subprofile['old'][]=$qr->ppprofileid;
				}
				$firstsub=($qr->firstsub && $qr->firstsub!='0000-00-00')?('\''.$qr->firstsub.'\''):'CURDATE()';
				$qstr2.='UPDATE mx_subscriptions SET status='.
					$status.', statusstamp=NOW(), firstsub='.$firstsub.', '.$expiry
					.' WHERE id='.$qr->id.';';
			}
		}
		$mxq->free();
		error_log('DBsubpaid: qstr2='.$qstr2);
		$mxq=$dbid->multi_query($qstr2);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr2);
			return null;
		}
		return $subprofile; // return recurrent payment profile if any
	}

	function setsubrenewal($pppaymentid,$bday) {
		$dbid=$this->dbid;
		$qstr='SELECT * FROM mx_subscriptions WHERE ppprofileid=\''.$pppaymentid.'\'';
		$mxq=$this->query($qstr);
		if (!$mxq || !$mxq->num_rows) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
		$qr=$mxq->fetch_object();
		if ($mxq) $mxq->free();
		$nextrenewal=strtotime($bday);
		$qstr='UPDATE mx_subscriptions SET renewal='.MXSUBAUTORENEW.', renewaldate=DATE(\''.date('Y-m-d',$nextrenewal).'\')'
			.' WHERE id='.$qr->id;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
	}

	function confirmsubrenewal($pppaymentid,$bday) {
		$dbid=$this->dbid;
		$qstr='SELECT * FROM mx_subscriptions WHERE ppprofileid=\''.$pppaymentid.'\'';
		$mxq=$this->query($qstr);
		if (!$mxq || !$mxq->num_rows) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
		$qr=$mxq->fetch_object();
		if ($mxq) $mxq->free();
		if ($qr->expiry) {
			$expiry='expiry=DATE_ADD('.$qr->expiry.',INTERVAL 1 YEAR)';
			$status=MXRENEWEDSUB;
		} else {
			$expiry='expiry=DATE_ADD(NOW(),INTERVAL 1 YEAR)';
			$status=MXNEWSUB;
		}
		$firstsub=$qr->firstsub?$qr->firstsub:'CURDATE()';
		$nextrenewal=strtotime($bday);
		$qstr='UPDATE mx_subscriptions SET subtype='.MXSUBFOY.', status='
			.$status.', statusstamp=NOW(), firstsub='.$firstsub.', '.$expiry
			.', renewaldate=DATE(\''.date('Y-m-d',$nextrenewal).'\')'
			.' WHERE id='.$qr->id;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
	}

	function stoprenewsub($pppaymentid) {
		$dbid=$this->dbid;
		$qstr='SELECT * FROM mx_subscriptions WHERE ppprofileid=\''.$pppaymentid.'\'';
		$mxq=$this->query($qstr);
		if (!$mxq || !$mxq->num_rows) {
			if (!$mxq) mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			else error_log('ppprofileid '.$pppaymentid.' not found...');
			return null;
		}
		$qr=$mxq->fetch_object();
		if ($mxq) $mxq->free();
		$qstr='UPDATE mx_subscriptions SET renewal='.MXSUBSTOPRENEW
			.' WHERE id='.$qr->id;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
	}

	function norenewsub($pppaymentid) {
		$dbid=$this->dbid;
		$qstr='SELECT * FROM mx_subscriptions WHERE ppprofileid=\''.$pppaymentid.'\'';
		$mxq=$this->query($qstr);
		if (!$mxq || !$mxq->num_rows) {
			if (!$mxq) mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			else error_log('ppprofileid '.$pppaymentid.' not found...');
			return null;
		}
		$qr=$mxq->fetch_object();
		if ($mxq) $mxq->free();
		$qstr='UPDATE mx_subscriptions SET renewal='.MXSUBNORENEW.',renewaldate=NULL'
			.' WHERE id='.$qr->id;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
	}

	function setsubinfo($userid,$cartline,$ppinfo) {
		$dbid=$this->dbid;
		$qstr='UPDATE mx_subscriptions SET ppprofileid=\''.$ppinfo['PROFILEID'].'\''
		.', ppstatus=\''.$ppinfo['STATUS'].'\''
		.' WHERE fanid='.$userid
		.' AND subcat='.$cartline->prodtype
		.' AND subtype='.$cartline->prodvar
		.' AND status!='.MXEXPIREDSUB
		.' AND objectid='.$cartline->prodref;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
	}

	/*
	 * check userid's subscription for fanid
	 * $fanid: requester
	 * $userid: checked user
	 */
	function getsub($fanid,$userid) { // TODO: check rights!!
		if (!$userid) $userid=$fanid;
		$dbid=$this->dbid;
		$qstr='SELECT * FROM mx_subscriptions WHERE fanid='.$userid;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return array();
		}
		$subs=array();
		while ($qr=$mxq->fetch_object()) {
			if (!$qr->city) $qr->city='-';
			if (!$qr->state) $qr->state='-';
			if (!$qr->country) $qr->country='-';
			$qr->newsubtype=array(
				'subcat' => $qr->subcat,
				'subtype' => $qr->subtype
				);
			$product=new stdClass();
			$product->prodtype=$qr->subcat;
			$product->prodref=$qr->objectid;
			$qr->subdesc=mx_proddesc($product);
			$subs[]=$qr;
		}
		$mxq->free();
		return $subs;
	}

	function getsubscribers($artistid,$likers=false) {
		$dbid=$this->dbid;
		if ($likers) $filter=' AND a.subtype='.MXSUBLIKE;
		else $filter=' AND a.subtype!='.MXSUBLIKE;
		$qstr='SELECT * FROM mx_subscriptions a,mx_account b WHERE objectid='.$artistid.' AND subcat='.MXARTSUB
		.' AND b.id=fanid'.$filter
		.' AND (a.status='.MXNEWSUB.' OR a.status='.MXCURRENTSUB.' OR a.status='.MXRENEWEDSUB.')';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
		$subs=array();
		while ($qr=$mxq->fetch_object()) {
			if (!$qr->city) $qr->city='-';
			if (!$qr->state) $qr->state='-';
			if (!$qr->country) $qr->country='-';
			$subs[]=$qr;
		}
		$mxq->free();
		return $subs;
	}

	function logme($userid,$ipadd,$page,$option,$action,$referer,$browser) {
		$dbid=$this->dbid;
		if (!$userid) $userid=0;
		$qstr='INSERT INTO mx_log SET userid='.$userid
		.', date=NOW(), ip=\''.$ipadd.'\''
		.', pag=\''.$page.'\', opt=\''.$option.'\', act=\''.$action.'\', ref=\''.$referer.'\', useragent=\''.$browser.'\'';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
		}
		if (!$userid) return;
		$qstr='UPDATE mx_account SET lastseen=NOW() WHERE id='.$userid;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
		}
	}

	function counton(){
		$dbid=$this->dbid;
		// online users
		$qstr='SELECT DISTINCT userid FROM mx_log WHERE date > DATE_SUB(NOW(),INTERVAL 5 MINUTE)';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
		}
		if (!$mxq->num_rows) $users['on']=0;
		else {
			$users['on']=$mxq->num_rows;
			$mxq->free();
		}
		// registered fans
		$qstr='SELECT count(id) FROM mx_account WHERE status >= '.MXACCTSETUP.' AND acctype='.MXACCOUNTFAN;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
		}
		if (!$mxq->num_rows) $users['fans']=0;
		else {
			$qr=$mxq->fetch_row();
			$mxq->free();
			$users['fans']=$qr[0];
		}
		// registered artists and bands
		$qstr='SELECT count(id) FROM mx_account WHERE status>='.MXACCTSETUP.' AND (acctype='.MXACCOUNTARTIST.' OR acctype='.MXACCOUNTBAND.')';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
		}
		if (!$mxq->num_rows) $users['artists']=0;
		else {
			$qr=$mxq->fetch_row();
			$mxq->free();
			$users['artists']=$qr[0];
		}
		// visitors
		$qstr='SELECT count(distinct userid+ip) FROM mx_log';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
		}
		if (!$mxq->num_rows) $users['visitors']=0;
		else {
			$qr=$mxq->fetch_row();
			$mxq->free();
			$users['visitors']=$qr[0];
		}
		// connections
		$qstr='SELECT count(distinct userid+ip+date) FROM mx_log';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
		}
		if (!$mxq->num_rows) $users['connections']=0;
		else {
			$qr=$mxq->fetch_row();
			$mxq->free();
			$users['connections']=$qr[0];
		}
		// hits
		$qstr='SELECT count(id) FROM mx_log';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
		}
		if (!$mxq->num_rows) $users['hits']=0;
		else {
			$qr=$mxq->fetch_row();
			$mxq->free();
			$users['hits']=$qr[0];
		}
		return $users;
	}

	function whoswhere($id) {
		$dbid=$this->dbid;
		$qstr='SELECT DISTINCT userid FROM mx_log WHERE date > DATE_SUB(NOW(),INTERVAL 60 DAY) ORDER BY date DESC';
		//.' AND userid!='.$id;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
		}
		if (!$mxq->num_rows) return null;
		$users=null;
		while ($qr=$mxq->fetch_object()) {
			$users[]=$qr;
		}
		$ids=array();
		$newusers=array();
		foreach ($users as $alog) {
			if (/* !$alog->userid || */ !array_key_exists($alog->userid,$ids)) {
				$ids[$alog->userid]=1;
				$newusers[]=$alog;
			}
		}
		$mxq->free();
		$users=null;
		//error_log(print_r($users,true));
		$qstr='SELECT date,pag,opt,act,ip FROM mx_log WHERE userid=? AND (userid!=0 OR ip!=\''
		.$_SERVER['REMOTE_ADDR'].'\') ORDER BY date DESC LIMIT 1';
		$mxq=$dbid->prepare($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
		$mxq->bind_param('i',$userid);
		$mxq->bind_result($dt,$pg,$op,$ac,$ip);
		$logs=array();
		foreach ($newusers as $alog) {
			$nlog=new StdClass();
			$userid=$alog->userid;
			$mxq->execute();
			if (!$mxq) {
				mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			}
			$mxq->fetch();
			$nlog->userid=$userid;
			$nlog->ip=$ip;
			$nlog->date=$dt;
			$nlog->pag=$pg;
			$nlog->opt=$op;
			$nlog->act=$ac;
			$logs[]=$nlog;
		}
		$mxq->close();
		//error_log(print_r($userlog,true));
		uasort($logs, function ($a,$b) {return strtotime($b->date)-strtotime($a->date);});
		return $logs;
	}

	function tzlist() {
		$dbid=$this->dbid;
		$qstr='SELECT * FROM mx_timezones';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return array();
		}
		$tz=array();
		while ($qr=$mxq->fetch_object()) {
			$tz[]=$qr->name;
		}
		return $tz;
	}

	function lastseen($userid) {
		$dbid=$this->dbid;
		$qstr='SELECT date FROM mx_log WHERE userid='.$userid.' ORDER BY date DESC LIMIT 1';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
		if (!$mxq->num_rows) return null;
		$qr=$mxq->fetch_row();
		$mxq->free();
		return $qr[0];
	}

	function listpros($proid=null) {
		$dbid=$this->dbid;
		if ($proid) $filter=' WHERE id='.$proid;
		else $filter=' ORDER BY name';
		$qstr='SELECT * FROM mx_pros'.$filter;
		$pros=array();
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return $pros;
		}
		if (!$mxq->num_rows) return array();
		while ($qr=$mxq->fetch_object()) {
			$pros[]=$qr;
		}
		$mxq->free();
		return $pros;
	}

	function listgenres($genre=null) {
		$dbid=$this->dbid;
		if ($genre) $filter=' WHERE id='.$genre;
		else $filter=' ORDER BY genre';
		$qstr='SELECT * FROM mx_genres'.$filter;
		$genres=array();
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return $genres;
		}
		if (!$mxq->num_rows) return array();
		while ($qr=$mxq->fetch_object()) {
			$genres[$qr->id]=$qr;
		}
		$mxq->free();
		return $genres;
	}

	function addpro($userid,$name,$website) {
		$dbid=$this->dbid;
		$qstr='INSERT INTO mx_pros SET userid='.$userid.', name=\''.strtoupper($dbid->real_escape_string($name)).'\','
		.'website=\''.$dbid->real_escape_string($website).'\','
		.'timestamp=NOW()';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return 0;
		}
		return $dbid->insert_id;
	}

	function getcountryname($countrycode) {
		$dbid=$this->dbid;
		$qstr='SELECT country_name FROM mx_countries WHERE cc_iso=\''.strtoupper($dbid->real_escape_string($countrycode)).'\'';
		$mxq=$this->query($qstr);
		if (!$mxq || !$mxq->num_rows) return $countrycode;
		$qr=$mxq->fetch_row();
		$mxq->free();
		return $qr[0];
	}

	function getartistidfrombundle($bundleid) {
		$dbid=$this->dbid;
		$qstr='SELECT owner_id FROM mx_media WHERE id='.$bundleid;
		$mxq=$this->query($qstr);
		if (!$mxq || !$mxq->num_rows) {
			return 0;
		}
		$qr=$mxq->fetch_row();
		$mxq->free();
		return $qr[0];
	}

	function getaccountfrompage($pageid) {
		$dbid=$this->dbid;
		$qstr='SELECT accountid FROM mx_fbpages WHERE pageid='.$pageid;
		$mxq=$this->query($qstr);
		if (!$mxq || !$mxq->num_rows) return 0;
		$qr=$mxq->fetch_row();
		$mxq->free();
		return $qr[0];
	}

	function setaccountforpage($userid,$pageid) {
		$dbid=$this->dbid;
		$qstr='SELECT id FROM mx_fbpages WHERE pageid='.$pageid;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return;
		}
		if (!$mxq->num_rows) $qstr='INSERT INTO mx_fbpages SET accountid='.$userid.',pageid='.$pageid;
		else {
			$mxq->free();
			$qstr='UPDATE mx_fbpages SET accountid='.$userid.' WHERE pageid='.$pageid;
		}
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
		}
	}

	function getrefid($typeid,$refid) {
		$dbid=$this->dbid;
		$qstr='SELECT id FROM mx_refs WHERE '.$typeid.'='.$refid;
		$mxq=$this->query($qstr);
		if (!$mxq || !$mxq->num_rows) { // no ref: create one
			$qstr='INSERT INTO mx_refs set '.$typeid.'='.$refid;
			//error_log($qstr);
			$mxq=$this->query($qstr);
			if (!$mxq) return NULL;
			return $dbid->insert_id;
		}
		$qr=$mxq->fetch_row();
		$mxq->free();
		return $qr[0];
	}

	function getusers($first,$mapsize) {
		$dbid=$this->dbid;
		$qstr="SELECT id,acctype,username FROM mx_account WHERE status>=".MXACCTSETUP.' AND id!=0 ORDER BY id LIMIT '.$first.','.$mapsize;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
		if (!$mxq->num_rows) return null;
		$users=array();
		while ($qr=$mxq->fetch_object()) {
			$users[]=$qr;
		}
		$mxq->free();
		return $users;
	}

	function getmedias($first,$mapsize) {
		$dbid=$this->dbid;
		$qstr="SELECT id FROM mx_media WHERE status>=".MXMEDIAFANVISIBLE.' AND status<='.MXMEDIAPUBLICSHARED
		.' AND id!=0 ORDER BY id LIMIT '.$first.','.$mapsize;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return null;
		}
		if (!$mxq->num_rows) return null;
		$users=array();
		while ($qr=$mxq->fetch_object()) {
			$medias[]=$qr;
		}
		$mxq->free();
		return $medias;
	}

	function setgenre($hash,$genre,$wikiurl,$cathash='') {
		$dbid=$this->dbid;
		$qstr='SELECT id FROM mx_genres WHERE hash=\''.$hash.'\'';
		$mxq=$this->query($qstr);
		if (!$mxq || !$mxq->num_rows) {
			$cmd='INSERT INTO ';
			$flt='';
		}
		else {
			$qr=$mxq->fetch_row();
			$mxq->free();
			$cmd='UPDATE ';
			$flt=' WHERE id='.$qr[0];
		}
		$qstr=$cmd.'mx_genres SET hash=\''.$hash.'\', genre=\''.$genre.'\', wiki=\''.$wikiurl.'\', cat=\''.$cathash.'\''.$flt;
		$mxq=$this->query($qstr);
		if (!$mxq) mxerror($dbid->error,__FILE__,__LINE__,$qstr);
	}

	function getfavorites($userid) {
		$dbid=$this->dbid;
		$qstr='SELECT * FROM mx_favorites WHERE userid='.$userid.' ORDER by position';
		$mxq=$this->query($qstr);
		$favs=array();
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return $favs;
		}
		while ($qr=$mxq->fetch_object()) {
			$favs[]=$qr;
		}
		$mxq->free();
		return $favs;
	}

	function delfav($userid,$favid) {
		$dbid=$this->dbid;
		$qstr='DELETE FROM mx_favorites WHERE userid='.$userid.' AND id='.$favid;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return false;
		}
		//TODO should update other favs' positions...
		/*
		$qstr='UPDATE mx_favorites SET position=1+ROW_COUNT() WHERE userid='.$userid.' ORDER BY position';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return false;
		}
		*/
		return true;
	}

	function addfav($userid,$objid,$objtype) {
		$dbid=$this->dbid;
		$qstr='SELECT count(id) FROM mx_favorites WHERE userid='.$userid.' AND favid='.$objid.' AND favtype='.$objtype;
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return 0; // issue with fav table
		}
		if ($mxq->num_rows>0) {
			$qr=$mxq->fetch_row();
			$mxq->free();
			if ($qr[0]) return 0; // already in favs
		}
		// doesn't exist, let's add
		$qstr='INSERT INTO mx_favorites SET userid='.$userid.',favid='.$objid.',favtype='.$objtype
		.',position=1+(SELECT count(b.id) FROM mx_favorites b WHERE b.userid='.$userid.')';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return 0; // issue with table
		}
		return $dbid->insert_id; // return fav unique id
	}

	function getconsumer($provider) {
		$dbid=$this->dbid;
		$qstr='SELECT * FROM mx_apiconsumers WHERE key=\''.$dbid->real_escape_string($provider->consumer_key).'\'';
		$mxq=$this->query($qstr);
		if (!$mxq) {
			mxerror($dbid->error,__FILE__,__LINE__,$qstr);
			return NULL;
		}
		$qr=$mxq->fetch_object();
		$mxq->free();
		return $qr;
	}
}

$mxdb=new MXDb(MXDBSERVER,MXDBUSER,MXDBPASSWORD,MXDBNAME);


?>

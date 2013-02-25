<?php
/* ---
 * Project: musxpand
 * File:    mx_admin.php
 * Author:  phil
 * Date:    22/10/2010
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

define('MXWIKIPOPGENRES','http://en.wikipedia.org/wiki/List_of_popular_music_genres');

function mx_mnadmin($page,$option,$action) {
	if ($option) return;
	mx_usermenu('admin');
}

function mx_mntester($page,$option,$action) {
	if ($option) return;
	mx_usermenu('tester');
}

function mx_mnupgrade($page,$option,$action) {
	mx_upgrade();
}

function mx_upgrade() {
	global $mxdb,$MXVersion;
	$res=implode("\n",$mxdb->updatetables());
	if ($res) echo '<xmp>'.$res.'</xmp>';
	else __('Database did not need updating...');
}

function mx_mntestusers($page,$option,$action) {
	global $facebook;
	echo 'Disabled.';
	return;
	$fbapp=new Facebook(array(
	  'appId'  => FACEBOOK_APP_ID,
	  'secret' => FACEBOOK_SECRET,
	  'grant_type' => 'client_credentials'
	));
	$acctok=$fbapp->getAccessToken();
	if ($_REQUEST['createuser']) {
		$newuser=$fbapp->api('/'.FACEBOOK_APP_ID.'/accounts/test-users?installed=true&permissions=&access_token='.$acctok,'POST');
	} else if ($_REQUEST['deleteuser']) {
		foreach ($_REQUEST['selected'] as $user) {
			$testusers=$fbapp->api('/'.$user.'?access_token='.$acctok,'DELETE');
			mx_deleteuser($user); // delete should only be possible for test users!!
		}
	}
	$testusers=$fbapp->api('/'.FACEBOOK_APP_ID.'/accounts/test-users&access_token='.$acctok,'GET');
	echo '<form action="'.mx_optionurl($page,$option).'" method=POST>';
	echo '<table border=1 width=100%><tr><th>Sel</th><th>ID</th><!-- <th>access_token</th> --><th>login_url</th></tr>';
	foreach ($testusers['data'] as $testuser) {
		echo '<tr><td><input type=checkbox name="selected[]" value="'.$testuser['id'].'"></td>' .
				'<td>'.$testuser['id'].'</td>';
		//$user=$facebook->api('/'.$testuser['id']);
		//die(print_r($user));
		//echo '<td>'.$user['name'].'</td>';
		echo '<!-- <td>'.$testuser['access_token'].'</td> --><td>'.
		'<a href="'.$testuser['login_url'].'">'.$testuser['login_url'].'</a></td></tr>';
	}
	echo '<tr><td colspan=3 align=center><input type=submit name="createuser" value="Create User">' .
			' <input type=submit name="deleteuser" value="Delete User(s)"></td></tr>';
	echo '</table></form>';
}

function mx_cktotest($page,$option,$action) {
	global $mxuser;
	if (is_admin()) {
		$mxuser->setoption(status, MXACCTPSEUDOADMIN);
	}
}

function mx_mntotest($page,$option,$action) {
	__('You just switched to Tester Mode.');
}

function mx_cktoadmin($page,$option,$action) {
	global $mxuser;
	if (is_pseudoadmin()) {
		$mxuser->setoption(status, MXACCTTRUSTFUL);
	}
}

function mx_mntoadmin($page,$option,$action) {
	__('You just switched back to Admin Mode.');
}

function mx_mndbgenres($page,$option,$action) {
	global $mxdb;
	$genres=file_get_contents(MXWIKIPOPGENRES);
	$list=preg_replace('%^.*List of genres%msi','',$genres);
	$list=preg_replace('%References.*$%msi','',$list);
	preg_match_all('%<h3>([^<]+|<[^h]|<h[^23])+%msi',$list, $tcat);
	foreach ($tcat[0] as $cat) {
		preg_match('%<span class="mw-headline" id="[^"]+"><a href="([^"]+)" title=[^>]+>([^<]+)</a>%', $cat, $catdef);
		$catname=$catdef[2];
		$catlink=$catdef[1];
		if (!$catname) { $catname='Other'; $catlink=''; }
		$cathash=hash('md5',$catname);
		$mxdb->setgenre($cathash,$catname,$catlink);
		echo '<br/><b>'._('Category:').' '.$catname.'</b><br/>';
		preg_match_all('%<li><a href="([^"]+)" title="[^"]+"[^>]*>([^<]+)</a>.*%i',$cat, $tab);
		foreach($tab[1] as $k => $wiki) {
			$name=$tab[2][$k];
			$md5=hash('md5',$name);
			if ($ok) echo ', ';
			echo $name;
			$ok=1;
			$mxdb->setgenre($md5,$name,$wiki,$cathash);
		}
		$ok=0;
	}
}

function mx_mnfbaction($page,$option,$action) {
	if (!$action) {
		echo '<form method="POST"><input type="text" name="fbaction" placeholder="action here" size="60"><br/>'
		.'<input type="text" name="object" placeholder="object here" size="60"><br/>'
		.'<input type="text" name="url" placeholder="url here" size="60"><br/>'
		.'<input type=submit name="a" value="Submit"></form>';
	} else {
		$fba=$_POST['fbaction'].'?'.$_POST['object'].'='.urlencode($_POST['url']);
		mx_fbaction($fba);
		echo 'action: ['.$fba.'] sent';
	}
}

function mx_mndelaccount($page,$option,$action) {
	global $mxdb;
}
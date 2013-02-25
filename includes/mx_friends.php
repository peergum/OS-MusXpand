<?php
/* ---
 * Project: musxpand
 * File:    mx_friends.php
 * Author:  phil
 * Date:    Mar 1, 2011
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

function mx_mnfriends($page,$option,$action) {
	global $mxuser,$me,$facebook;
	echo '<p>'._('Welcome to your Friends page').'</p>';
	$mxfriends=$mxuser->getfriends($mxuser->id);
	echo '<h5>'._('Current friends').'</h5>';
	mx_showdir($mxfriends['confirmed']);
	echo '<h5>'._('Pending friends').'</h5>';
	mx_showdir($mxfriends['pending']);
	echo '<h5>'._('Recused friends').'</h5>';
	mx_showdir($mxfriends['recused']);
	echo '<h5>'._('Ignored friends').'</h5>';
	mx_showdir($mxfriends['ignored']);
	if ($mxuser->fbid) {
		echo '<h5>'._('Facebook friends already on MusXpand').'</h5>';
		if ($action=='showfb') {
			mx_checkfblogin();
		}
		if ($me) {
			$fbfriends=$facebook->api('/me/friends');
			$fbfriendsids=array();
			foreach ($fbfriends['data'] as $fbfriend) $fbfriendsids[]=$fbfriend['id'];
			$friends=$mxuser->checkfbfriends($fbfriendsids);
			mx_showdir($friends,true);
			//echo sprintf(_('If you want to check which of your Facebook friends' .
			//' are already on MusXpand, please %s'),
			//'<fb:login-button>'._('Synchronize with Facebook').'</fb:login-button>');

		} else {
			echo '<p>';
			echo sprintf(_('Click %s to see which Facebook friends are already on MusXpand'),
				'<a href="'.mx_actionurl($page,$option,'showfb')
				.'" alt="'._('Facebook friends').'">'._('here').'</a>');
			echo '</p>';
		}
	}
}

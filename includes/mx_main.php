<?php
/* ---
 * Project: musxpand
 * File:    mx_main.php
 * Author:  phil
 * Date:    09/10/2010
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


function mx_ckmain($page,$option,$action) {
	global $mxuser;
	if ($mxuser->id) {
		if ($mxuser->cart->items>0) {
			mx_sendnotice('red',
			($mxuser->cart->items==1)?_('You have 1 item in your cart')
			:sprintf(_('You have %d items in your cart'),$mxuser->cart->items),
			'cart',''
			);
		}
		if ($mxuser->newmsgs>0) {
			mx_sendnotice('green',
			($mxuser->newmsgs==1)?_('You got 1 new message'):sprintf(_('You got %d new messages'),$mxuser->newmsgs),
			'account','messages');
		}
		if ($mxuser->subs['expired']>0) {
			mx_sendnotice('red',
			sprintf(_('%d of your fanships expired'),$mxuser->subs['expired']),
			'account','mysubs','-','expired');
		}
		if ($mxuser->subs['new']>0) {
			mx_sendnotice('green',
			($mxuser->subs['new']==1)?_('1 new fanship activated'):sprintf(_('%d new fanship activated'),$mxuser->subs['new']),
			'account','mysubs','-','active');
		}
		if ($mxuser->subs['renewed']>0) {
			mx_sendnotice('green',
			($mxuser->subs['renewed']==1)?_('1 fanship was renewed'):sprintf(_('%d fanships were renewed'),$mxuser->subs['renewed']),
			'account','mysubs','-','active');
		}
	} else if (MXDEFFEATURES & MXFTNEWLOGIN) {
		if (false && !MXBETA) {
			header('location: '.mx_optionurl('account','signin'));
			die();
		}
	}
}

/*
 * mx_mnmain: generate main content of the page according to page/option
 */
function mx_mnmain($page,$option,$action) {
	global $FBcookie,$me,$mxuser;
	/*if (!$option) { ?>
	<div class="whymusxpand"><a href="/bestdeal" alt="Click for More"><img src="/images/general/whymusxpand.png" /></a></div>
	<?php }*/
   	mx_checkbrowser();
    if ($_GET['fbp'] && $page=='main') {
    	if ($mxuser->fbdata['page']['id']==MXFACEBOOKPAGE) mx_mnfeatarts($page,$option,$action);
    	else mx_showhtmlpage('facebook-noartist');
    } else if ($page=='fbapp' || $option=='fbapp') {
		mx_showhtmlpage('facebook-app');
    } else if (!$option) {
    	//mx_mnfeatarts($page,$option,$action);

	    if (is_logged() && is_confirmed()) {
	    	mx_showhtmlpage('main');
	    } else if (is_logged() && !is_confirmed()) {
			mx_showhtmlpage('noaccess');
	    } else {
	    	mx_showhtmlpage('main-unlogged');
	    }

    }
    if ($page=='help') {
    	mx_showhtmlpage('helpmain');
    }
    if ($action=='signed') {
    	//echo '<script type="text/javascript" src="'.mx_secureurl('http://www.surveymonkey.com/jsPop.aspx?sm=pCuB8uE5xQv1xdEu18bDGg_3d_3d').'"> </script>';
    }
}

function mx_mnfbapp($page,$option,$action) {
	mx_showhtmlpage('facebook-app');
}

function mx_mnnoaccess($page,$option,$action) {
	mx_showhtmlpage('noaccess');
}

function mx_mnfacebook($page,$option,$action) {
	mx_showhtmlpage('facepage');
}

function mx_mnpaperli($page,$option,$action) {
	mx_showhtmlpage('paperli');
}

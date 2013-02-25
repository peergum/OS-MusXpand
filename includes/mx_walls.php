<?php
/* ---
 * Project: musxpand
 * File:    mx_walls.php
 * Author:  phil
 * Date:    Mar 2, 2011
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

include_once 'mx_init.php';

function mx_wallpost() {
	global $mxuser;
	$msg=null;
	//die(phpinfo());
	$msg->body=mx_securestring($_REQUEST['body']);
	$filter=$_REQUEST['filter'];
	if ($filter) $msg->filter=array_sum($filter);
	else $msg->filter='';
	$msg->filter=mx_securestring($msg->filter);
	if ($msg->body) $mxuser->saveupdate($msg);
	else {
		mx_important(_('Your update is empty...'));
	}
}

function mx_walldelete() {
	global $mxuser;
	$selwall=$_REQUEST['selwall'];
	foreach($selwall as $msgid) {
		$mxuser->markwalldeleted($msgid);
	}
}

function mx_mnwall($page,$option,$action) {
	switch ($action) {
		case 'post':
			mx_wallpost();
			break;
		case 'delete':
			mx_walldelete();
			break;
	}
	mx_showwalls($action);
	//phpinfo();
}


function mx_showwalls($action) {
	global $mxuser;
	$mywall=array(
		'mywall' => array(
			//'select' => array(1,'<input name="checkwallsbox" type="checkbox" onclick="javascript:checkwalls(\'updates\',\'selwall[]\');">','text',3),
			'wall' => array(0,_('Wall'),'wall'),
		)
	);
	$mywallb=array(
		'mywall' => array(
			//'delete_updates' => _('Delete'),
			//'archive_inbox' => _('Archive'),
		)
	);
	$mywallptr=$mxuser->listmywalls();
	$mywalls=array();
	if ($mywallptr) {
		while ($wall=$mxuser->listmywalls($mywallptr)) {
			$awall=new StdClass();
			$awall->select='<input type="checkbox" name="selwall[]" value="'.$wall->msgid.'">';
			$wall->type='me';
			$wall->mylikes=$mxuser->getlikes($wall->msgid);
			$awall->wall=$wall;
			$awall->flags=$wall->flags;
			$mywalls['mywall'][]=$awall;
		}
	}
	$updatebtns=array(
		'post' => _('Post'),
		//'save' => _('Save'),
		'clear' => _('Clear'),
	);
	// filling msgs
	$allmsgs=array();
	/*$frwall=array(
		'frwall' => array(
			'select' => array(1,'<input name="checkwallsbox" type="checkbox" onclick="javascript:checkwalls(\'updates\',\'selwall[]\');">','text',3),
			'wall' => array(1,_('Wall'),'wall'),
		)
	);
	$frwallb=array(
		'frwall' => array(
			'delete_updates' => _('Delete'),
			//'archive_inbox' => _('Archive'),
		)
	);*/
	$frwallptr=$mxuser->listfrwalls();
	if ($frwallptr) {
		while ($wall=$mxuser->listfrwalls($frwallptr)) {
			$awall=new StdClass();
			$awall->select='<input type="checkbox" name="selwall[]" value="'.$wall->msgid.'">';
			$wall->type='friend';
			$wall->mylikes=$mxuser->getlikes($wall->msgid);
			$awall->wall=$wall;
			$awall->flags=$wall->flags;
			//$frwalls['frwall'][]=$awall;
			$allmsgs['frwalls'][]=$awall;
		}
	}
	$artwallptr=$mxuser->listartwalls();
	if ($artwallptr) {
		while ($wall=$mxuser->listartwalls($artwallptr)) {
			$awall=new StdClass();
			$awall->select='<input type="checkbox" name="selwall[]" value="'.$wall->msgid.'">';
			$wall->type='artist';
			$wall->mylikes=$mxuser->getlikes($wall->msgid);
			$awall->wall=$wall;
			$awall->flags=$wall->flags;
			//$frwalls['frwall'][]=$awall;
			$allmsgs['artwalls'][]=$awall;
		}
	}
	$fanwallptr=$mxuser->listfanwalls();
	if ($fanwallptr) {
		while ($wall=$mxuser->listfanwalls($fanwallptr)) {
			$awall=new StdClass();
			$awall->select='<input type="checkbox" name="selwall[]" value="'.$wall->msgid.'">';
			$wall->type='fans';
			$wall->mylikes=$mxuser->getlikes($wall->msgid);
			$awall->wall=$wall;
			$awall->flags=$wall->flags;
			//$frwalls['frwall'][]=$awall;
			$allmsgs['fanwalls'][]=$awall;
		}
	}

	$msglist=array(
		'wallmsgs',1,_('The Walls'),sprintf(
		_('To post a new update to your wall, click %s.'),
		'<a href="javascript:tabswitch(\'frwalls\',\'updates\');">'._('here').'</a>'),
		array(
			'frwalls' => array(
					//'delete_outbox' => _('Delete'),
					//'archive_outbox' => _('Archive'),
				),
			'artwalls' => array(
					//'cancel' => _('Cancel'),
					//'accept_requests' => _('Accept'),
					//'recuse_requests' => _('Recuse'),
					//'ignore_requests' => _('Ignore'),
					//'archive_requests' => _('Archive'),
				),
			'fanwalls' => array(
					//'cancel_reqsent' => _('Cancel'),
					//'archive_reqsent' => _('Archive'),
				),
			'mentions' => array(
					//'cancel_reqsent' => _('Cancel'),
					//'archive_reqsent' => _('Archive'),
				),
			'updates' => array(
				// using inside buttons instead...
				),
		),
		array(
			'updates' => array(2, //form with no field descriptions
				'updates' => array(-1,_('My Updates'),_('What\'s up...?')),
				'body'  => array(1,_('Status'),'memo',1,null,_('Open your heart...')),
				'filter'  => array(1,_('Shared with'),'sharefilter'),
				'a' => array(1,'none','hidden'),
				'k' => array(1,'updates','hidden'),
				'btns' => array(-3,$updatebtns),
				'previous' => array(-1,_('Previous Updates'),_('What did I say again...?')),
				'mywall' =>	array(-2,$mywall,$mywalls,'wall',$mywallb,'mywall'),
				//'more'  => array(1,_('Read'),'text',4)
			),
			'frwalls' => array(0, //list
				'frwalls' => array(-1,_('Friends\' Walls'),_('What\'s up friends?')),
				'a' => array(1,'none','hidden'),
				'k' => array(1,'frwalls','hidden'),
				//'frwall' =>	array(-2,$frwall,$frwalls,'wall',$frwallb,'frwall'),
//				'select' => array(1,'<input name="checkwallsbox" type="checkbox" onclick="javascript:checkwalls(\'frwalls\',\'selwall[]\');">','text',3),
				'wall' => array(0,_('Wall'),'wall'),
				//				'select' => array(1,'<input name="checkallbox" type="checkbox" onclick="javascript:checkall(\'frwalls\');">','text',3),
//				'from' => array(1,_('From'),'text',40),
//				'body' => array(1,_('Status'),'memo',40),
//				'date'  => array(1,_('Date'),'timestamp',12),
			),
			'artwalls' => array(0, //list
				'artwalls' => array(-1,_('Artists\' Walls'),_('Whazzup fav\' bands?')),
				'a' => array(1,'none','hidden'),
				'k' => array(1,'artwalls','hidden'),
//				'select' => array(1,'<input name="checkwallsbox" type="checkbox" onclick="javascript:checkwalls(\'frwalls\',\'selwall[]\');">','text',3),
				'wall' => array(0,_('Wall'),'wall'),
				//'select' => array(1,'<input name="checkallbox" type="checkbox" onclick="javascript:checkall(\'artwalls\');">','text',3),
				//'from' => array(1,_('From'),'text',60),
				//'body' => array(1,_('Status'),'memo',40),
				//'date'  => array(1,_('Date'),'timestamp',12),
			),
			'fanwalls' => array(0, //list
				'fanwalls' => array(-1,_('Fans\' Walls'),_('What are my fans talking about?')),
				'a' => array(1,'none','hidden'),
				'k' => array(1,'fanwalls','hidden'),
//				'select' => array(1,'<input name="checkwallsbox" type="checkbox" onclick="javascript:checkwalls(\'frwalls\',\'selwall[]\');">','text',3),
				'wall' => array(0,_('Wall'),'wall'),
				//'select' => array(1,'<input name="checkallbox" type="checkbox" onclick="javascript:checkall(\'fanwalls\');">','text',3),
				//'from' => array(1,_('From'),'text',60),
				//'body' => array(1,_('Status'),'memo',40),
				//'date'  => array(1,_('Date'),'timestamp',12),
			),
			'mentions' => array(0, //list
				'mentions' => array(-1,_('Mentions'),_('Talking about me...?')),
				'a' => array(1,'none','hidden'),
				'k' => array(1,'fanwalls','hidden'),
				'wall' => array(0,_('Wall'),'wall'),
				//'select' => array(1,'<input name="checkallbox" type="checkbox" onclick="javascript:checkall(\'fanwalls\');">','text',3),
				//'from' => array(1,_('From'),'text',60),
				//'body' => array(1,_('Status'),'memo',40),
				//'date'  => array(1,_('Date'),'timestamp',12),
			),
		)
	);
	mx_showlist($msglist,$allmsgs,'wall',true);
	if (preg_match('%(rp:([0-9]+))%',$action,$actionarg)>0) {
		?>
		<script language="javascript">buttonclick('reply:<?php echo $actionarg[2]; ?>');</script>
		<?php
	}
}

function mx_showuserwallstr($user) {
	global $mxuser;
	$userwall=array(
		'userwall' => array(
			//'select' => array(1,'<input name="checkwallsbox" type="checkbox" onclick="javascript:checkwalls(\'updates\',\'selwall[]\');">','text',3),
			'wall' => array(0,_('Wall'),'wall'),
		)
	);
	$userwallb=array(
		'userwall' => array(
			//'delete_updates' => _('Delete'),
			//'archive_inbox' => _('Archive'),
		)
	);
	$userwallptr=$mxuser->listuserwalls($user->id);
	$userwalls=array();
	if ($userwallptr) {
		while ($wall=$mxuser->listuserwalls($user->id,$userwallptr)) {
			$awall=new StdClass();
			$awall->select='<input type="checkbox" name="selwall[]" value="'.$wall->msgid.'">';
			$wall->type='me';
			$wall->mylikes=$mxuser->getlikes($wall->msgid);
			$awall->wall=$wall;
			$awall->flags=$wall->flags;
			$userwalls['userwall'][]=$awall;
		}
	}
	return mx_showtablestr($userwall,$userwalls,'wall',$userwallb,'userwall');
}

function mx_xmlwalls($msgid,$like='0',$dislike='0',$section) {
	global $mxdb,$mxuser;
	if ($like || $dislike) {
		if ($like && $dislike) return;
		$numlikes=$mxdb->setlikes($mxuser->id,$msgid,$like,$dislike);
		echo 'var totlikes='.$numlikes->likes.'; var totdislikes='.$numlikes->dislikes.'; //'.$like.' / '.$dislike;
		return;
	}
	$comments=$mxdb->getcomments('wallid',$msgid);
	if (!$comments) {
		__('No comments.');
		return;
	}
	$str='<table class="wcomment">';
	while ($comment=$mxdb->getcomments('wallid',$msgid,$comments)) {
		$comment->mylikes=$mxuser->getlikes($comment->msgid);
		$str .= '<tr class="wcommentline">';
		$str .= '<td class="wallpic"><img tag="'.$comment->authid.'" class="wallpic" src="'.mx_fanpic($comment->authid).'" /></td>';
		$str .= '<td>';
		$str.=$comment->body;
		$str.= '<div class="wclikes">';
		$str.= mx_likeicon($comment->msgid,MXLIKEIT,$comment->mylikes,$mxuser->id)
		.'<div name="ln_'.$comment->msgid.'">'
		.sprintf('%d',($comment->likes?$comment->likes:0)).'</div>'
		.mx_likeicon($comment->msgid,MXDISLIKEIT,$comment->mylikes,$mxuser->id)
		.'<div name="dn_'.$comment->msgid.'">'
		.sprintf('%d',($comment->dislikes?$comment->dislikes:0)).'</div>';
		$str.='</div>'; // wlikes
		$str.='<div class="wcommentdate">'.mx_difftime($comment->date).'</div>';
		$str.='</td>';
		$str .= '</tr>';
	}
	$str.='</table>';
	$yrcomment=array(1,_('Your comment'),'memo',1,null,_('Speak your heart...'));
	/*$str.='<tr class="wcommentline"><!-- <td class="wallpic"><img src="'.mx_fanpic($mxuser->id).'" /></td> -->'
		.'<td colspan="2">'.mx_formfield('mc_'.$msgid,'',$yrcomment).'<br/>'
		.'<input class="sendcomment" type="button" onclick="sendcomment(\''.$section.'\',\''.$msgid.'\');" value="'._('Send').'">'
		.'</td>'
		.'</tr>';*/
	$str.='<div class="wnewcomment">';
	$str.=mx_formfield('mc_'.$msgid,'',$yrcomment).'<br/>'
		.'<input class="sendcomment" type="button" onclick="sendcomment(\''.$section.'\',\''.$msgid.'\');" value="'._('Send').'">';
	$str.='<a class="close" href="javascript:switchcomments(\''.$section.'\',\''.$msgid.'\');">'
				.mx_icon('close','Close','16px')
				.'</a>';
	$str.='</div>';
	echo $str;
}


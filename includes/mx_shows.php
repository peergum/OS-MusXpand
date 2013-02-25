<?php
/* ---
 * Project: musxpand
 * File:    mx_shows.php
 * Author:  phil
 * Date:    Jan 9, 2012
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

function mx_showpost() {
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

function mx_showdelete() {
	global $mxuser;
	$selshows=$_REQUEST['selshows'];
	foreach($selshows as $msgid) {
		$mxuser->markshowdeleted($msgid);
	}
}

function mx_mnmyshows($page,$option,$action) {
	switch ($action) {
		case 'post':
			mx_showpost();
			break;
		case 'delete':
			mx_showdelete();
			break;
	}
	mx_showshows($action);
	//phpinfo();
}


function mx_showshows($action) {
	global $mxuser;
	if (is_artist()) {
		$myshowarr=array(
			'myshows' => array(
				'select' => array(1,'<input name="checkshowsbox" type="checkbox" onclick="javascript:checkshows(\'updates\',\'selshows[]\');">','text',3),
				'show' => array(0,_('Show'),'show'),
			)
		);
		$myshowsb=array(
			'myshows' => array(
				//'delete_updates' => _('Delete'),
				//'archive_inbox' => _('Archive'),
			)
		);
		$myshowsptr=null; //$mxuser->listmyshows();
		$myshows=array();
		$myshows['myshows']=array();
		if ($myshowsptr) {
			while ($show=$mxuser->listmyshows($myshowsptr)) {
				$ashow=new StdClass();
				$ashow->select='<input type="checkbox" name="selshows[]" value="'.$show->msgid.'">';
				$show->type='me';
				$show->mylikes=$mxuser->getlikes($show->msgid);
				$ashow->show=$show;
				$ashow->flags=$show->flags;
				$myshows['myshows'][]=$ashow;
			}
		}
		$updatebtns=array(
			'post' => _('Post'),
			//'save' => _('Save'),
			'clear' => _('Clear'),
		);
	}
	// filling msgs
	$allmsgs=array();
	/*$frshows=array(
		'frshows' => array(
			'select' => array(1,'<input name="checkshowsbox" type="checkbox" onclick="javascript:checkshows(\'updates\',\'selshows[]\');">','text',3),
			'shows' => array(1,_('Wall'),'shows'),
		)
	);
	$frshowsb=array(
		'frshows' => array(
			'delete_updates' => _('Delete'),
			//'archive_inbox' => _('Archive'),
		)
	);*/
	/* -- friends shows...
	$frshowsptr=$mxuser->listfrshows();
	if ($frshowsptr) {
		while ($show=$mxuser->listfrshows($frshowsptr)) {
			$ashow=new StdClass();
			$ashow->select='<input type="checkbox" name="selshows[]" value="'.$show->msgid.'">';
			$show->type='friend';
			$show->mylikes=$mxuser->getlikes($show->msgid);
			$ashow->show=$show;
			$ashow->flags=$show->flags;
			//$frshows['frshows'][]=$ashow;
			$allmsgs['frshows'][]=$ashow;
		}
	}
	*/
	/* -- artists shows
	$artshowsptr=$mxuser->listartshows();
	if ($artshowsptr) {
		while ($show=$mxuser->listartshows($artshowsptr)) {
			$ashow=new StdClass();
			$ashow->select='<input type="checkbox" name="selshows[]" value="'.$show->msgid.'">';
			$show->type='artist';
			$show->mylikes=$mxuser->getlikes($show->msgid);
			$ashow->show=$show;
			$ashow->flags=$show->flags;
			//$frshows['frshows'][]=$ashow;
			$allmsgs['artshows'][]=$ashow;
		}
	}
	*/
	/* -- fans shows
	$fanshowsptr=$mxuser->listfanshows();
	if ($fanshowsptr) {
		while ($show=$mxuser->listfanshows($fanshowsptr)) {
			$ashow=new StdClass();
			$ashow->select='<input type="checkbox" name="selshows[]" value="'.$show->msgid.'">';
			$show->type='fans';
			$show->mylikes=$mxuser->getlikes($show->msgid);
			$ashow->show=$show;
			$ashow->flags=$show->flags;
			//$frshows['frshows'][]=$ashow;
			$allmsgs['fanshows'][]=$ashow;
		}
	}
	*/

	$msglist=array(
		'showsmsgs',1,_('The Shows'),sprintf(
		_('To post a new update to your shows, click %s.'),
		'<a href="javascript:tabswitch(\'frshows\',\'updates\');">'._('here').'</a>'),
		array(
			'frshows' => array(
					//'delete_outbox' => _('Delete'),
					//'archive_outbox' => _('Archive'),
				),
			'artshows' => array(
					//'cancel' => _('Cancel'),
					//'accept_requests' => _('Accept'),
					//'recuse_requests' => _('Recuse'),
					//'ignore_requests' => _('Ignore'),
					//'archive_requests' => _('Archive'),
				),
			'fanshows' => array(
					//'cancel_reqsent' => _('Cancel'),
					//'archive_reqsent' => _('Archive'),
				),
			'updates' => array(
					'newshow' => _('Add a Show'),
				// using inside buttons instead...
				),
		),
		array(
			'updates' => array(1, //form with no field descriptions
				'updates' => array(-1,_('Add a Show'),_('Please fill the form below...')),
				'venue' => array(1,_('Venue'),'text'),
				'date' => array(1,_('Date'),'date'),
				'a' => array(1,'none','hidden'),
				'k' => array(1,'fanshows','hidden'),
				//'body'  => array(1,_('Status'),'memo',1,null,_('Open your heart...')),
				//'filter'  => array(1,_('Shared with'),'sharefilter'),
				'a' => array(1,'none','hidden'),
				'k' => array(1,'updates','hidden'),
				//'btns' => array(-3,$updatebtns),
				//'previous' => array(-1,_('Previous Updates'),_('What did I say again...?')),
				'myshows' =>	array(-2,$myshows,$myshows,'shows',$myshowsb,'myshows'),
				//'more'  => array(1,_('Read'),'text',4)
			),
			'frshows' => array(0, //list
				'frshows' => array(-1,_('Friends\' Shows'),_('What\'s up friends?')),
				'a' => array(1,'none','hidden'),
				'k' => array(1,'frshows','hidden'),
				//'frshows' =>	array(-2,$frshows,$frshows,'shows',$frshowsb,'frshows'),
//				'select' => array(1,'<input name="checkshowsbox" type="checkbox" onclick="javascript:checkshows(\'frshows\',\'selshows[]\');">','text',3),
				'shows' => array(0,_('Wall'),'shows'),
				//				'select' => array(1,'<input name="checkallbox" type="checkbox" onclick="javascript:checkall(\'frshows\');">','text',3),
//				'from' => array(1,_('From'),'text',40),
//				'body' => array(1,_('Status'),'memo',40),
//				'date'  => array(1,_('Date'),'timestamp',12),
			),
			'artshows' => array(0, //list
				'artshows' => array(-1,_('Artists\' Shows'),_('Whazzup fav\' bands?')),
				'a' => array(1,'none','hidden'),
				'k' => array(1,'artshows','hidden'),
//				'select' => array(1,'<input name="checkshowsbox" type="checkbox" onclick="javascript:checkshows(\'frshows\',\'selshows[]\');">','text',3),
				'shows' => array(0,_('Wall'),'shows'),
				//'select' => array(1,'<input name="checkallbox" type="checkbox" onclick="javascript:checkall(\'artshows\');">','text',3),
				//'from' => array(1,_('From'),'text',60),
				//'body' => array(1,_('Status'),'memo',40),
				//'date'  => array(1,_('Date'),'timestamp',12),
			),
			'fanshows' => array(0, //list
				'fanshows' => array(-1,_('Fans\' Shows'),_('What are my fans talking about?')),
				'a' => array(1,'none','hidden'),
				'k' => array(1,'fanshows','hidden'),
//				'select' => array(1,'<input name="checkshowsbox" type="checkbox" onclick="javascript:checkshows(\'frshows\',\'selshows[]\');">','text',3),
				'shows' => array(0,_('Wall'),'shows'),
				//'select' => array(1,'<input name="checkallbox" type="checkbox" onclick="javascript:checkall(\'fanshows\');">','text',3),
				//'from' => array(1,_('From'),'text',60),
				//'body' => array(1,_('Status'),'memo',40),
				//'date'  => array(1,_('Date'),'timestamp',12),
			),
		)
	);
	mx_showlist($msglist,$allmsgs,'shows',true);
	if (preg_match('%(rp:([0-9]+))%',$action,$actionarg)>0) {
		?>
		<script language="javascript">buttonclick('reply:<?php echo $actionarg[2]; ?>');</script>
		<?php
	}
}

function mx_showusershowsstr($user) {
	global $mxuser;
	$usershows=array(
		'usershows' => array(
			//'select' => array(1,'<input name="checkshowsbox" type="checkbox" onclick="javascript:checkshows(\'updates\',\'selshows[]\');">','text',3),
			'shows' => array(0,_('Wall'),'shows'),
		)
	);
	$usershowsb=array(
		'usershows' => array(
			//'delete_updates' => _('Delete'),
			//'archive_inbox' => _('Archive'),
		)
	);
	$usershowsptr=$mxuser->listusershows($user->id);
	$usershows=array();
	if ($usershowsptr) {
		while ($show=$mxuser->listusershows($user->id,$usershowsptr)) {
			$ashow=new StdClass();
			$ashow->select='<input type="checkbox" name="selshows[]" value="'.$show->msgid.'">';
			$show->type='me';
			$show->mylikes=$mxuser->getlikes($show->msgid);
			$ashow->show=$show;
			$ashow->flags=$show->flags;
			$usershows['usershows'][]=$ashow;
		}
	}
	return mx_showtablestr($usershows,$usershows,'shows',$usershowsb,'usershows');
}

function mx_xmlshow($msgid,$like='0',$dislike='0') {
	global $mxdb,$mxuser;
	if ($like || $dislike) {
		if ($like && $dislike) return;
		$numlikes=$mxdb->setlikes($mxuser->id,$msgid,$like,$dislike);
		echo 'var totlikes='.$numlikes->likes.'; var totdislikes='.$numlikes->dislikes.'; //'.$like.' / '.$dislike;
		return;
	}
	$comments=$mxdb->getcomments($msgid);
	if (!$comments) {
		__('No comments.');
		return;
	}
	$str='<table class="wcomment">';
	while ($comment=$mxdb->getcomments($msgid,$comments)) {
		$comment->mylikes=$mxuser->getlikes($comment->msgid);
		$str .= '<tr class="wcommentline">';
		$str .= '<td class="showspic"><img src="'.mx_fanpic($comment->authid).'" /></td>';
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
	$yrcomment=array(1,_('Your comment'),'memo',1,null,_('Speak your heart...'));
	$str.='<tr class="wcommentline"><!-- <td class="showspic"><img src="'.mx_fanpic($mxuser->id).'" /></td> -->'
		.'<td colspan="2">'.mx_formfield('mc_'.$msgid,'',$yrcomment).'<br/>'
		.'<input class="sendcomment" type="button" onclick="sendcomment('.$msgid.');" value="'._('Send').'">'
		.'</td>'
		.'</tr>';
	$str.='</table>';
	$str.='<a class="close" href="javascript:switchcomments(\''.$msgid.'\');">'
				.mx_icon('close','Close','16px')
				.'</a>';
	echo $str;
}


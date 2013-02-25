<?php
/* ---
 * Project: musxpand
 * File:    mx_messages.php
 * Author:  phil
 * Date:    Nov 26, 2010
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

function mx_showmessages($action) {
	global $mxuser;
	$listorder=mx_secureword($_GET['s']);
	if ($listorder!='') $listorder.=' asc';
	$msgs=$mxuser->listmessages(null,$listorder);
	if (!$msgs) {
		__('Your message box is empty.');
		return;
	}
	$allmsgs=array();
	$msglist=array(
		'msglist',0,_('Message List'),sprintf(
		_('If you want to compose a new message, click %s.'),
		'<a href="javascript:tabswitch(\'inbox\',\'writemsg\');">'._('here').'</a>'),
		array(
			'inbox' => array(
					'delete_inbox' => _('Delete'),
					'archive_inbox' => _('Archive'),
				),
			'outbox' => array(
					'delete_outbox' => _('Delete'),
					'archive_outbox' => _('Archive'),
				),
			'requests' => array(
					//'cancel' => _('Cancel'),
					'accept_requests' => _('Accept'),
					'recuse_requests' => _('Recuse'),
					'ignore_requests' => _('Ignore'),
					//'archive_requests' => _('Archive'),
				),
			'reqsent' => array(
					'cancel_reqsent' => _('Cancel'),
					'archive_reqsent' => _('Archive'),
				),
			'archives' => array(
					'delete_archives' => _('Delete'),
				),
			'drafts' => array(
					'delete_drafts' => _('Delete'),
					'archive_drafts' => _('Archive'),
				),
			//'messages' => array(
			//	),
			'writemsg' => array(
					'send' => _('Send'),
					'save' => _('Save'),
					'clear' => _('Clear'),
				),
		),
		array(
			'inbox' => array(0, //list
				'inbox' => array(-1,_('Inbox'),_('The messages you received')),
				//'direction' => array(1,_('&larr;&rarr'),'text',2),
				'select' => array(0,'<input name="checkallbox" type="checkbox" onclick="javascript:checkall(\'inbox\');">','text',3),
				'contact' => array(0,_('From'),'text',40),
				'topic' => array(0,_('Topic'),'text',40),
				'flags'  => array(0,0,'hidden'),
				'date'  => array(0,_('Date'),'timestamp',12),
				'a' => array(1,'none','hidden'),
				'k' => array(1,'inbox','hidden'),
				//'more'  => array(1,_('Read'),'text',4)
			),
			'outbox' => array(0, //list
				'outbox' => array(-1,_('Outbox'),_('The messages you sent')),
				//'direction' => array(1,_('&larr;&rarr'),'text',2),
				'select' => array(0,'<input name="checkallbox" type="checkbox" onclick="javascript:checkall(\'outbox\');">','text',3),
				'contact' => array(0,_('To'),'text',40),
				'topic' => array(0,_('Topic'),'text',40),
				'flags'  => array(0,0,'hidden'),
				'date'  => array(0,_('Date'),'timestamp',12),
				'a' => array(1,'none','hidden'),
				'k' => array(1,'outbox','hidden'),
				//'more'  => array(1,_('Read'),'text',4)
			),
			'requests' => array(0, //list
				'requests' => array(-1,_('Requests'),_('Checking your pending requests?')),
				//'direction' => array(1,_('&larr;&rarr'),'text',2),
				//'select' => array(1,'<input id="checkallbox" type="checkbox" onclick="javascript:checkall();">','text',3),
				'select' => array(0,'<input name="checkallbox" type="checkbox" onclick="javascript:checkall(\'requests\');">','text',3),
				'contact' => array(0,_('From'),'text',60),
				//'subject' => array(0,_('Topic'),'text',40),
				'flags'  => array(0,_('Type'),'msgflags',20),
				'date'  => array(0,_('Date'),'timestamp',12),
				//'body'  => array(1,_('Body'),'memo',40),
				//'more'  => array(1,_('Read'),'text',4)
				'a' => array(1,'none','hidden'),
				'k' => array(1,'requests','hidden'),
			),
			'reqsent' => array(0, //list
				'reqsent' => array(-1,_('Sent Req.'),_('Do you want to cancel any requests you sent?')),
				//'direction' => array(1,_('&larr;&rarr'),'text',2),
				//'select' => array(1,'<input id="checkallbox" type="checkbox" onclick="javascript:checkall();">','text',3),
				'select' => array(0,'<input name="checkallbox" type="checkbox" onclick="javascript:checkall(\'reqsent\');">','text',3),
				'contact' => array(0,_('From'),'text',60),
				//'subject' => array(0,_('Topic'),'text',40),
				'flags'  => array(0,_('Type'),'msgflags',20),
				'date'  => array(0,_('Date'),'timestamp',12),
				//'body'  => array(1,_('Body'),'memo',40),
				//'more'  => array(1,_('Read'),'text',4)
				'a' => array(1,'none','hidden'),
				'k' => array(1,'reqsent','hidden'),
			),
			'archives' => array(0, //list
				'archives' => array(-1,_('Archives'),_('Your arquived messages')),
				//'direction' => array(1,_('&larr;&rarr'),'text',2),
				'select' => array(0,'<input name="checkallbox" type="checkbox" onclick="javascript:checkall(\'archives\');">','text',3),
				'contact' => array(0,_('From | To'),'text',40),
				'topic' => array(0,_('Topic'),'text',40),
				'a' => array(1,'none','hidden'),
				'date'  => array(0,_('Date'),'timestamp',12),
				'flags'  => array(0,0,'hidden'),
				'k' => array(1,'archives','hidden'),
				//'more'  => array(1,_('Read'),'text',4)
			),
			'drafts' => array(0, //list
				'drafts' => array(-1,_('Drafts'),_('Messages previously saved')),
				//'direction' => array(1,_('&larr;&rarr'),'text',2),
				'select' => array(0,'<input name="checkallbox" type="checkbox" onclick="javascript:checkall(\'drafts\');">','text',3),
				'contact' => array(0,_('To'),'text',40),
				'topic' => array(0,_('Topic'),'text',40),
				'date'  => array(0,_('Date'),'timestamp',12),
				'flags'  => array(0,0,'hidden'),
				'a' => array(1,'none','hidden'),
				'k' => array(1,'drafts','hidden'),
				//'more'  => array(1,_('Read'),'text',4)
			),
			/*'messages' => array(0, //list
				'messages' => array(-1,_('Everything'),_('Find all messages here')),
				//'direction' => array(1,_('&larr;&rarr'),'text',2),
				'select' => array(1,'<input name="checkallbox" type="checkbox" onclick="javascript:checkall(\'messages\');">','text',3),
				'contact' => array(1,_('From | To'),'text',40),
				'topic' => array(1,_('Topic'),'text',40),
				'date'  => array(1,_('Date'),'timestamp',12),
				'a' => array(1,'none','hidden'),
				'flags'  => array(1,0,'hidden'),
				'k' => array(1,'messages','hidden'),
				//'more'  => array(1,_('Read'),'text',4)
			),*/
			'writemsg' => array(1, //form
				'writemsg' => array(-1,_('Write Box'),_('Hey! Writing to friends?')),
				//'direction' => array(1,_('&larr;&rarr'),'text',2),
				//'select' => array(1,'<input id="checkallbox" type="checkbox" onclick="javascript:checkall();">','text',3),
				'to' => array(1,_('To'),'user',40), // 'user' type also creates h_to
				'subject' => array(1,_('Subject'),'subject',40),
				'body'  => array(1,_('Body'),'memo',10,null,
					_('Verba volant, scripta manent...'),54),
				'history'  => array(0,_('Message History'),'hiddenmemo',5),
				'flags'  => array(1,0,'hidden'),
				'a' => array(1,'none','hidden'),
				'priority' => array(1,0,'hidden'),
				'refid' => array(1,0,'hidden'),
				'k' => array(1,'writemsg','hidden'),
				//'more'  => array(1,_('Read'),'text',4)
			),
		)
	);
	/* new sorting method: buttons next/previous should work per msg category...
	while ($msg=$mxuser->listmessages($msgs)) {
		$buttons=null;
		if ($msg->flags & MXREQUEST) {
			if ($msg->sender==$mxuser->id) {
				$buttons['cancel:'.$msg->msgid]=_('Cancel');
				$buttons['archive:'.$msg->msgid]=_('Archive');
			} else {
				$buttons['accept:'.$msg->msgid]=_('Accept');
				$buttons['recuse:'.$msg->msgid]=_('Recuse');
				$buttons['ignore:'.$msg->msgid]=_('Ignore');
				$buttons['sep4']=null;
				$buttons['reply:'.$msg->msgid]=_('Reply');
				$buttons['forward:'.$msg->msgid]=_('Forward');
			}
			$buttons['sep3']=null;
		} else {
			if ($msg->sender==$mxuser->id) {
				$buttons['resend:'.$msg->msgid]=_('Resend');
				$buttons['sep2']=null;
			}
			$buttons['delete:'.$msg->msgid]=_('Delete');
			$buttons['archive:'.$msg->msgid]=_('Archive');
			$buttons['sep1']=null;
		}
		if ($msg->prev) $buttons['previous:'.$msg->prev]=_('Previous');
		else $buttons['-previous']=_('Previous');
		if ($msg->next) $buttons['next:'.$msg->next]=_('Next');
		else $buttons['-next']=_('Next');
		$msg->select='<input type="checkbox" name="selmsg[]" value="'.$msg->msgid.'">';
		$msg->select.='<div class="contentframe"><div id="'.$msg->msgid.'" class="msgcontent" style="display:none;">';
		$msgform=array(
			'msgform',0,_('Message read'),_('Details...'),
			$buttons,
			array(
				'from'  => array(1,_('From:'),'text',40),
				'h_from'  => array(1,0,'hidden',40),
				'to'  => array(1,_('To:'),'text',40),
				'h_to'  => array(1,0,'hidden',40),
				'date'  => array(1,_('Date:'),'text',40),
				'flags'  => array(1,_('Flags:'),'msgflags',40),
				'subject' => array(0,_('Subject:'),'text',40),
				'body' => array(0,_('Body:'),'text',60)
			)
		);
		$msg->select.=mx_letterstr($msgform,array(
			'from' => ($mxuser->id==$msg->sender)?_('Me'):($msg->fullname?$msg->fullname:$msg->artistname),
			'to' => ($mxuser->id==$msg->receiver)?_('Me'):($msg->fullname?$msg->fullname:$msg->artistname),
			'h_to' => ($mxuser->id==$msg->receiver)?$mxuser->id:$msg->receiver,
			'h_from' => ($mxuser->id==$msg->sender)?$mxuser->id:$msg->sender,
			'date' => $msg->date,
			'subject' => $msg->subject,
			'flags' => $msg->flags,
			'body' => mx_msgformat($msg->body),
			'msgid' => $msg->msgid
			),false);
		$msg->select.='<div class="msgclose"><a href="javascript:hidecontent('.$msg->msgid.')">'._('X').'</a>' .
			'</div></div></div>';
		$msg->contact='<div class="msgline"><img class="msgpic" src="'.mx_fanpic($msg->id,'square').'" />';
		$msg->contact.=' '.(($mxuser->id==$msg->sender)?(_('Me').' &rarr;'):'');
		$msg->contact.=' '.($msg->fullname?$msg->fullname:$msg->artistname);
		$msg->contact.=' '.(($mxuser->id==$msg->sender)?'':('&rarr; '._('Me')));
		$msg->contact.='</div>';
		$msg->topic=$msg->subject.'<div class="bodyextract">&ldquo;'.(strlen($msg->body)>40?(substr($msg->body,0,40).'[&hellip;]'):$msg->body).'&rdquo;</div>';
		$msg->read = ($msg->status & MXMSGREAD)?true:false;
		//$msg->flags = ($msg->flags | MXMSGREQUEST)?'true':'false';
		if (!$msg->level) $msg->level='';
		$allmsgs['messages'][]=$msg;
		if ($mxuser->id==$msg->receiver) { // receiver
			if (!($msg->status & MXMSGDELETED)) {
				if ($msg->status & MXMSGARCHIVED) $allmsgs['archives'][]=$msg;
				else $allmsgs['inbox'][]=$msg;
			}
		}
		if ($mxuser->id==$msg->sender) {
			if (!($msg->sstatus & MXMSGDELETED)) {
				if ($msg->sstatus & MXMSGARCHIVED) $allmsgs['archives'][]=$msg;
				else $allmsgs['outbox'][]=$msg;
				if ($msg->sstatus & MXMSGDRAFT) $allmsgs['drafts'][]=$msg;
			}
		}
		if ($msg->flags & MXREQUEST) $allmsgs['requests'][]=$msg;
		$msg=$nmsg;
	}
	*/
	/* old sorting method buttons next/previous are global :-( */
	$msg=null;
	while (($msgs && $nmsg=$mxuser->listmessages($msgs))||$msg) {
		//print_r($msg);
		if (!$msg) {
			$msg=$nmsg;
			$msg->prev=null;
			continue;
		} else if ($nmsg) $nmsg->prev=$msg->msgid;
		if (!$nmsg) {
			$msg->next=null;
			$msgs=null;
		} else $msg->next=$nmsg->msgid;
		$buttons=null;
		if ($msg->flags & MXREQUEST) {
			if ($msg->sender==$mxuser->id) {
				$buttons['cancel:'.$msg->msgid]=_('Cancel');
				$buttons['archive:'.$msg->msgid]=_('Archive');
			} else {
				$buttons['accept:'.$msg->msgid]=_('Accept');
				$buttons['recuse:'.$msg->msgid]=_('Recuse');
				$buttons['ignore:'.$msg->msgid]=_('Ignore');
				$buttons['sep4']=null;
				$buttons['reply:'.$msg->msgid]=_('Reply');
				$buttons['forward:'.$msg->msgid]=_('Forward');
			}
			$buttons['sep3']=null;
		} else {
			if ($msg->sender==$mxuser->id) {
				$buttons['resend:'.$msg->msgid]=_('Resend');
				$buttons['sep2']=null;
			} else {
				$buttons['reply:'.$msg->msgid]=_('Reply');
				$buttons['forward:'.$msg->msgid]=_('Forward');
				$buttons['sep2']=null;
			}
			$buttons['delete:'.$msg->msgid]=_('Delete');
			$buttons['archive:'.$msg->msgid]=_('Archive');
			$buttons['sep1']=null;
		}
		if ($msg->prev) $buttons['previous:'.$msg->prev]=_('Previous');
		else $buttons['-previous']=_('Previous');
		if ($msg->next) $buttons['next:'.$msg->next]=_('Next');
		else $buttons['-next']=_('Next');
		$msg->select='<input type="checkbox" name="selmsg[]" value="'.$msg->msgid.'">';
		$msg->select.='<div class="contentframe"><div id="'.$msg->msgid.'" class="msgcontent" style="display:none;">';
		$msgform=array(
			'msgform',0,_('Message read'),_('Details...'),
			$buttons,
			array(
				'from'  => array(1,_('From:'),'text',40),
				'h_from'  => array(1,0,'hidden',40),
				'to'  => array(1,_('To:'),'text',40),
				'h_to'  => array(1,0,'hidden',40),
				'date'  => array(1,_('Date:'),'date',40),
				'flags'  => array(1,_('Flags:'),'msgflags',40),
				'subject' => array(0,_('Subject:'),'text',40),
				'body' => array(0,_('Body:'),'text',60)
			)
		);
		$msg->select.=mx_letterstr($msgform,array(
			'from' => ($mxuser->id==$msg->sender)?_('Me'):($msg->fullname?$msg->fullname:$msg->artistname),
			'to' => ($mxuser->id==$msg->receiver)?_('Me'):($msg->fullname?$msg->fullname:$msg->artistname),
			'h_to' => ($mxuser->id==$msg->receiver)?$mxuser->id:$msg->receiver,
			'h_from' => ($mxuser->id==$msg->sender)?$mxuser->id:$msg->sender,
			'date' => $msg->date,
			'subject' => $msg->subject,
			'flags' => $msg->flags,
			'body' => mx_msgformat($msg->body),
			'msgid' => $msg->msgid
			),false);
		$msg->select.='<div class="msgclose"><a href="javascript:hidecontent('.$msg->msgid.')">'._('X').'</a>' .
			'</div></div></div>';
		$msg->contact='<div class="msgline"><img class="msgpic" src="'
			.mx_fanpic($msg->id,'square',$msg->gender,($msg->acctype==MXACCOUNTARTIST)).'" />';
		$msg->contact.=' '.(($mxuser->id==$msg->sender)?(_('Me').' &rarr;'):'');
		$msg->contact.=' '.($msg->fullname?$msg->fullname:$msg->artistname);
		$msg->contact.=' '.(($mxuser->id==$msg->sender)?'':('&rarr; '._('Me')));
		$msg->contact.='</div>';
		$msg->topic=$msg->subject.'<div class="bodyextract">&ldquo;'.(strlen($msg->body)>40?(substr($msg->body,0,40).'[&hellip;]'):$msg->body).'&rdquo;</div>';
		$msg->read = ($msg->status & MXMSGREAD)?true:false;
		$msg->ignored = ($msg->status & MXREQIGNORED)?true:false;
		$msg->cancelled = ($msg->status & MXREQCANCELLED)?true:false;
		//$msg->flags = ($msg->flags | MXMSGREQUEST)?'true':'false';
		if (!$msg->level) $msg->level='';
		$allmsgs['messages'][]=$msg;
		if ($mxuser->id==$msg->receiver) { // receiver
			if (!($msg->status & (MXMSGDELETED | MXREQCANCELLED | MXREQIGNORED))) {
				if ($msg->status & MXMSGARCHIVED) $allmsgs['archives'][]=$msg;
				else {
					if ($msg->flags & MXREQUEST) $allmsgs['requests'][]=$msg;
					else $allmsgs['inbox'][]=$msg;
				}
			}
		}
		if ($mxuser->id==$msg->sender) {
			if (!($msg->sstatus & (MXMSGDELETED | MXREQCANCELLED))) {
				if ($msg->sstatus & MXMSGARCHIVED) $allmsgs['archives'][]=$msg;
				else {
					if ($msg->flags & MXREQUEST) $allmsgs['reqsent'][]=$msg;
					else if ($msg->sstatus & MXMSGDRAFT) $allmsgs['drafts'][]=$msg;
					else $allmsgs['outbox'][]=$msg;
				}
			}
		}
		$msg=$nmsg;
	}
	/* end old sorting method */
	if (preg_match('%^(af:(.+))$%',$action,$actionarg)>0) {
		$user=$mxuser->getuserinfo($actionarg[2]);
		$allmsgs['writemsg']=array(
			'to' => $user->id,
			'subject' => _('Friendship Request'),
			'flags' => MXFRIENDREQUEST,
			'body' => _('Hi there. Could you please accept this request for friendship...?')
		);
		//error_log(print_r($allmsgs['writemsg'],true));
	}
	//error_log($action);
	if (preg_match('%^(sm:(.+))$%',$action,$actionarg)>0) {
		$user=$mxuser->getuserinfo($actionarg[2]);
		$allmsgs['writemsg']=array(
			'to' => $user->id,
			'subject' => '',
			'body' => ''
		);
	}
	mx_showlist($msglist,$allmsgs,'messages',true,true);
	if (preg_match('%(rp:([0-9]+))%',$action,$actionarg)>0) {
		?>
		<script language="javascript">buttonclick('reply:<?php echo $actionarg[2]; ?>');</script>
		<?php
	}
}

function mx_sendmessage() {
	global $mxuser,$status;
	$msg=new StdClass();
	//die(phpinfo());
	$msg->to=mx_securestring($_REQUEST['h_to']);
	$msg->subject=mx_securestring($_REQUEST['subject']);
	$msg->body=mx_securestring($_REQUEST['body']);
	$msg->flags=mx_securestring($_REQUEST['flags']);
	$msg->priority=mx_securestring($_REQUEST['priority']);
	$receiver=$mxuser->getuserinfo($msg->to);
	if ($mxuser->sendmessage($msg)) {
		$status=sprintf(_('Your %s to %s was just sent!'),
			($msg->flags & MXREQUEST)?_('request'):_('message'),
			$receiver->fullname);
	} else {
		$status=sprintf(_('Your %s to %s was not sent...'),
			($msg->flags & MXREQUEST)?_('request'):_('message'),
			$receiver->fullname);
	}
}

function mx_deletemessage() {
	global $mxuser;
	$selmsg=$_REQUEST['selmsg'];
	if (count($selmsg)) {
		foreach($selmsg as $msgid) {
			$mxuser->markmsgdeleted($msgid);
		}
	} else $status=_('Which message(s) do you want to delete?');
}

function mx_archivemessage() {
	global $mxuser;
	$selmsg=$_REQUEST['selmsg'];
	if (count($selmsg)) {
		foreach($selmsg as $msgid) {
			$mxuser->markmsgarchived($msgid);
		}
	} else $status=_('Which message(s) do you want to archive?');
}

function mx_cancelrequest() {
	global $mxuser;
	$selmsg=$_REQUEST['selmsg'];
	if (count($selmsg)) {
		foreach($selmsg as $msgid) {
			$mxuser->reqcancel($msgid);
		}
	} else $status=_('Which request(s) do you want to cancel?');
}

function mx_acceptrequest() {
	global $mxuser;
	$selmsg=$_REQUEST['selmsg'];
	if (count($selmsg)) {
		foreach($selmsg as $msgid) {
			$mxuser->reqaccept($msgid);
		}
	} else $status=_('Which request(s) do you want to accept?');
}

function mx_recuserequest() {
	global $mxuser;
	$selmsg=$_REQUEST['selmsg'];
	if (count($selmsg)) {
		foreach($selmsg as $msgid) {
			$mxuser->reqrecuse($msgid);
		}
	} else $status=_('Which request(s) do you want to recuse?');
}

function mx_ignorerequest() {
	global $mxuser;
	$selmsg=$_REQUEST['selmsg'];
	if (count($selmsg)) {
		foreach($selmsg as $msgid) {
			$mxuser->reqignore($msgid);
		}
	} else $status=_('Which request(s) do you want to ignore?');
}

function mx_ckmessages($page,$option,$action) {
	global $status;
	switch ($action) {
		case 'send':
			mx_sendmessage();
			break;
		case 'delete':
			mx_deletemessage();
			break;
		case 'archive':
			mx_archivemessage();
			break;
		case 'cancel':
			mx_cancelrequest();
			break;
		case 'accept':
			mx_acceptrequest();
			break;
		case 'recuse':
			mx_recuserequest();
			break;
		case 'ignore':
			mx_ignorerequest();
			break;
	}
}

function mx_mnmessages($page,$option,$action) {
	global $status;
	if ($status) mx_important($status);
	mx_showmessages($action);
	//phpinfo();
}

function mx_xmlmessage($action,$msgid) {
	global $mxuser;
	switch ($action) {
		case 'markread':
			if ($mxuser->markmsgread($msgid)>0) return 'ok';
			return 'err';
			break;
		case 'markdeleted':
			if ($mxuser->markmsgdeleted($msgid)>0) return 'ok';
			return 'err';
			break;
		case 'markarchived':
			if ($mxuser->markmsgarchived($msgid)>0) return 'ok';
			return 'err';
			break;
		case 'reqcancel':
			if ($mxuser->reqcancel($msgid)>0) return 'ok';
			return 'err';
			break;
		case 'reqaccept':
			if ($mxuser->reqaccept($msgid)>0) return 'ok';
			return 'err';
			break;
		case 'reqrecuse':
			if ($mxuser->reqrecuse($msgid)>0) return 'ok';
			return 'err';
			break;
		case 'reqignore':
			if ($mxuser->reqignore($msgid)>0) return 'ok';
			return 'err';
			break;
	}
}


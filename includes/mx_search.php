<?php
/* ---
 * Project: musxpand
 * File:    mx_search.php
 * Author:  phil
 * Date:    21/10/2010
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

function mx_searchbox() {
	if (is_logged()) {
?>
	<div class="searchbox"><div class="qsearch"><form name="searchbox" method="POST" action="<?php echo mx_pageurl(''); ?>"><input id="q" name="q" type="text"
	placeholder="<?php __('Search'); ?>"
	 onkeyup="return quicksearch(event,this,'q');"
	 onblur="return quicksearch(event,this,'q');"
	 ></form><div class="qresults"><div id="q_search" class="quicksearch"></div></div></div></div>
<?php
	}
}

function mx_mnsearch($page,$option,$action) {
	global $query,$mxdb,$mxuser;
	//echo 'Soon, you\'ll be able to find the results of your search for ['.$action.'] here...';
	error_log('search for:['.$action.']');
	$search=$mxdb->search($action);
	if (!$search) {
		__('No result found.');
		return;
	}
	//die(print_r($search));
	/*foreach ($search as $type => $results) {
		echo '<h3>'.$type.'</h3>';
		echo '<ul>';
		foreach ($results as $id => $result) {
			echo '<li>' .
			'<div class="searchpic">'.($type=='persons'?('<img src="'.mx_fanpic($id).'">'):'')
			.'</div><div class="searchname">'.$result.'</div>'
			.'<div class="searchaction">action</div>'
			.'</li>';
		}
		echo '</ul>';
	}*/
	$reslist=array(
		'reslist',0,_('Search Results'),_('Is this what you were looking for...?'),
		array(
		),
		array(
			'persons' => array(
				'persons' => array(-1,_('Individuals'),_('Matching accounts')),
				'picture' => array(0,_('Picture'),'picture',10),
				'name' => array(0,_('Name'),'text',40),
				'actions'  => array(0,_('Actions'),'actions',40),
			),
			'artists' => array(
				'artists' => array(-1,_('Artists'),_('Matching artists')),
				'picture' => array(0,_('Picture'),'picture',10),
				'name' => array(0,_('Name'),'text',40),
				'actions'  => array(0,_('Actions'),'actions',40),
			),
			/*'archipelagoes' => array(
				'archipelagoes' => array(-1,_('Archipelagoes'),_('Matching archipelagoes')),
				//'picture' => array(0,_('Picture'),'picture',10),
				'name' => array(0,_('Name'),'text',30),
				'description' => array(0,_('Description'),'text',50),
				'actions'  => array(0,_('Actions'),'actions',40),
			),
			'islands' => array(
				'islands' => array(-1,_('Islands'),_('Matching islands')),
				//'picture' => array(0,_('Picture'),'picture',10),
				'name' => array(0,_('Name'),'text',30),
				'description' => array(0,_('Description'),'text',50),
				'actions'  => array(0,_('Actions'),'actions',40),
			),*/
			'medias' => array(
				'medias' => array(-1,_('Medias'),_('Matching medias')),
				'picture' => array(0,_('Picture'),'picture',10),
				'name' => array(0,_('Name'),'text',40),
				'actions'  => array(0,_('Actions'),'actions',40),
			),
		)
	);
	//while ($msgs && $msg=$mxuser->listmessages($msgs)) {
		//print_r($msg);
	foreach ($search as $type => $results) {
		$resarray[$type]=array();
		foreach ($results as $id => $result) {
			$res=new stdClass();
			switch($type) {
				case 'persons':
					$res->picture=new StdClass();
					$res->picture->pic=mx_fanpic($id,'square',$result->gender,false);
					$res->picture->id=$id;
					$res->picture->type='person';
					$res->name='<a href="'.mx_actionurl('fans','fanprof',$id)
					.'" alt="'.$result->fullname.'">'.$result->fullname.'</a>';
					$res->actions=array(
						'fanprof'	=> array(_('See Page'),mx_actionurl('fans','fanprof',$id)),
						'addfriend' => array(_('Add as a Friend'),mx_actionurl('account','messages','af:'.$id,'writemsg')),
						'sendmsg'	=> array(_('Send a Message'),mx_actionurl('account','messages','sm:'.$id,'writemsg'))
					);
					break;
				case 'artists':
					$res->picture=new StdClass();
					$res->picture->pic=mx_fanpic($id,'square',$result->gender,true);
					$res->picture->id=$id;
					$res->picture->type='person';
					$res->name='<a href="'.mx_actionurl('artists','artprof',$id)
					.'" alt="'.$result->artistname.'">'.$result->artistname.'</a>'
					.'<div class="artisttype">'.($result->acctype==MXACCOUNTBAND?_('Band'):_('Artist')).'</div>';
					$res->actions=array(
						'artprof' => array(_('See Profile'),mx_actionurl('artists','artprof',$id)),
						//'sendmsg'	=> array(_('Send a Message'),mx_actionurl('account','messages','sm:'.$id,'writemsg'))
					);
					break;
				case 'archipelagoes':
				case 'islands':
					$res->name='<a href="'.mx_actionurl('musxpace','viewprof',$id)
					.'" alt="'.$result->name.'">'.$result->name.'</a>';
					$res->description=$result->description;
					$res->actions=array(
						//'viewprof'	=> array(_('View Profile'),mx_actionurl('account','viewprof',$id)),
						//'addfriend' => array(_('Add as a Friend'),mx_actionurl('account','addfriend',$id)),
						//'sendmsg'	=> array(_('Send a Message'),mx_actionurl('account','sendmsg',$id))
					);
					break;
				case 'medias':
					$media=null;
					$media=$mxuser->getmediainfo($id);
					$fanship=$mxuser->getfanship($media->owner_id,$media->id);
					mx_medialist($media,$fanship);
					$res->picture=new StdClass();
					$res->picture->pic=$media->pic;
					$res->picture->id=$id;
					$res->picture->type='media';
					$res->name='<a href="'.mx_actionurl('media','medprof',$id)
					.'" alt="'.$media->title.'">'
					.sprintf('%s <br/>by %s',$media->title,$media->artistname).'</a>';
					$res->actions=array(
						'medprof' => array(_('Media Info'),mx_actionurl('media','medprof',$id)),
						//'sendmsg'	=> array(_('Send a Message'),mx_actionurl('account','messages','sm:'.$id,'writemsg'))
					);
					break;
			}
			$resarray[$type][]=$res;
		}
	}
	mx_showlist($reslist,$resarray,'search',false,true);
}

function mx_xmlsearch($query,$qtype='persons',$fld) {
	global $mxdb,$mxuser;
	//echo 'Soon, you\'ll be able to find the results of your search for ['.$action.'] here...';
	$search=$mxdb->search($query);
	if (!$search) {
		__('No result found.');
		return;
	}
	if ($qtype=='persons') {
		echo '<table class="searchresult">';
		$class='';// class="selected"';
		if (count($search['persons'])>0) {
			$i=0;
			foreach($search['persons'] as $result) {
				if ($i>3) continue;
				$i++;
				$foundstr=str_ireplace($query,'<span class="searchstring">'.$query.'</span>',$result->fullname);
				echo '<tr id="'.$result->id.'"'.$class.' onmouseover="selresult(this);" onmouseout="unselresult(this);">' .
				'<td onclick="setfield(\''.$fld.'\',\''.$result->id.'\',\''.$result->fullname.'\');">' .
				'<img src="'.mx_fanpic($result->id,'square',$result->gender,false).'"/> '.$foundstr.' <span class="resulttype">('._('Fan').')</span>'.
				'<input type="hidden" id="n_'.$result->id.'" value="'.$result->fullname.'"></td></tr>';
				$class='';
			}
		}
		if (count($search['artists'])>0) {
			$i=0;
			foreach($search['artists'] as $result) {
				if ($i>3) continue;
				$i++;
				$foundstr=str_ireplace($query,'<span class="searchstring">'.$query.'</span>',$result->artistname);
				echo '<tr id="'.$result->id.'"'.$class.' onmouseover="selresult(this);" onmouseout="unselresult(this);">' .
				'<td onclick="setfield(\''.$fld.'\',\''.$result->id.'\',\''.$result->artistname.'\');">' .
				'<img src="'.mx_fanpic($result->id,'square',$result->gender,true).'"> '.$foundstr.' <span class="resulttype">('.($result->acctype==MXACCOUNTBAND?_('Band'):_('Artist')).')</span>'.
				'<input type="hidden" id="n_'.$result->id.'" value="'.$result->artistname.'"></td></tr>';
				$class='';
			}
		}
		if (count($search['medias'])>0) {
			$i=0;
			foreach($search['medias'] as $result) {
				if ($i>3) continue;
				$i++;
				$media=$mxuser->getmediainfo($result->id);
				$fanship=$mxuser->getfanship($media->owner_id,$media->id);
				mx_medialist($media,$fanship);
				$foundstr=sprintf('%s<br/>by %s',str_ireplace($query,'<span class="searchstring">'.$query.'</span>',$media->title),$media->artistname);
				echo '<tr id="'.$result->id.'"'.$class.' onmouseover="selresult(this);" onmouseout="unselresult(this);">' .
				'<td onclick="setfield(\''.$fld.'\',\''.$result->id.'\',\''.$result->title.'\');">' .
				'<img src="'.$media->pic.'"> '.$foundstr.' <span class="resulttype">('._('Media').')</span>'.
				'<input type="hidden" id="n_'.$result->id.'" value="'.$media->title.'"></td></tr>';
				$class='';
			}
		}
		if ($fld!='to') echo '<tr id="default"'.$class.' onmouseover="selresult(this);">' .
				'<td onclick="setfield(\''.$fld.'\',\'default\',\''.$query.'\');">' .
				'&rarr;'._('More Results').
				'<input type="hidden" id="n_default" value="'.$query.'"></td></tr>';
		echo '</table>';
	}
}


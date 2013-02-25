<?php
/* ---
 * Project: musxpand
 * File:    mx_fans.php
 * Author:  phil
 * Date:    06/10/2010
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

function mx_mnfans($page,$option,$action) {
	if (!$option) mx_mnfandir('fans','fandir',$action);
	//if (!$option) mx_showhtmlpage('fans');
}

function mx_showfanpage($id,$simul='') {
	global $mxuser;
	$dbuser=$mxuser->getuserinfo($id);
	if ($dbuser->acctype==MXACCOUNTARTIST) return mx_showartistpage($id,0,$simul);
	if ($dbuser->status==MXACCTDISABLED) {
		__('This account doesn\'t exist');
		return;
	}
	if (!$dbuser) {
		mx_optiontitle('error',_('This account doesn\'t exist'));
		return;
	}
	/*if ($dbuser->acctype==MXACCOUNTFAN || $option=='viewprof' || $option=='fanprof') {
		mx_optionsubtitle('&rarr; '.mx_getname($dbuser));
	} else {
		mx_optionsubtitle('&rarr; '.mx_getartistname($dbuser));
	}*/
	$authflds=$mxuser->getauthorizedfields($dbuser->id);
	$authgrps=$mxuser->getauthorizedgroups($authflds);
	$section='';
	if (!$authgrps || !$authflds) {
		__('No information available.');
		return;
	}
	$custpage=$dbuser->custompage?$dbuser->custompage:MXDEFFANPAGE;
	mx_showcustompage($custpage,$dbuser,0,$simul);
	if ($id!=$mxuser->id) mx_fbaction('musxpand:check_out?fan='.urlencode(mx_actionurl('fans','fanprof',$id)));
	return;
}

function mx_mnfandir($page,$option,$action) {
	global $mxdb,$mxuser;
	$mode=array(
		'alpha' => _('Alphabetical'),
		'genres' => _('By Genres'),
		//'-chrono' => _('Chronological'),
		'subs' => _('Subscribers'),
		'super' => _('SuperFans'),
		'random' => _('Random Selection'),
	);
	$selsub='';
	if ($action) {
		if (preg_match('%([^:]+):?(.*)%',$action,$modes));
		$selmode=$modes[1];
		$selsub=$modes[2];
	} else {
		$selmode='subs';
	}
	echo '<div class="directory">';
	echo '<table class="dirtabs"><tr>';
	foreach ($mode as $ord => $ordname) {
		$dis=false;
		if (substr($ord,0,1)=='-') $dis=true;
		$class=($ord==$selmode)?'dirtabsel':(substr($ord,0,1)=='-'?'dirtabdis':'dirtab');
		echo '<td class="'.$class.'">';
		if (!$dis) echo '<a href="'.mx_actionurl($page,$option,$ord).'">'.$ordname.'</a>';
		else echo $ordname;
		echo '</td>';
	}
	//echo '<td class="dirtablast"></td>';
	echo '</tr>';
	echo '</table>';
	$fanfilter='(a.acctype='.MXACCOUNTFAN.' OR 0 < (SELECT count(b.id) FROM mx_subscriptions b WHERE b.fanid=a.id'
		.' AND b.subcat='.MXARTSUB.' AND b.subtype!='.MXSUBLIKE.' AND b.status!='.MXEXPIREDSUB.' AND b.status!='.MXENDEDSUB.') )';
	switch ($selmode) {
		case 'random': // pure fans or subscribers
			$filter=$fanfilter; // pure fan or subscriber
			break;
		case 'subs': // subs only
			$filter='0 < (SELECT count(b.id) FROM mx_subscriptions b WHERE b.fanid=a.id'
			.' AND b.subcat='.MXARTSUB.' AND b.subtype!='.MXSUBLIKE.' AND b.status!='.MXEXPIREDSUB.' AND b.status!='.MXENDEDSUB.')';
			break;
		case 'super': // 5 subs mini
			$filter='4 < (SELECT count(b.id) FROM mx_subscriptions b WHERE b.fanid=a.id'
			.' AND b.subcat='.MXARTSUB.' AND b.subtype!='.MXSUBLIKE.' AND b.status!='.MXEXPIREDSUB.' AND b.status!='.MXENDEDSUB.')';
			break;
		case 'genres':
			$genres=$mxdb->listgenres();
			$cats=array();
			$subgenres=array();
			foreach ($genres as $genre){
				if (!$genre->cat) {
					$cats[$genre->hash]=$genre->id;
					$subgenres[$genre->id]=array();
				}
			}
			foreach ($genres as $genre){
				if ($genre->cat) {
					$subgenres[$cats[$genre->cat]][]=$genre->id;
					$catgenre[$genre->id]=$cats[$genre->cat];
				}
			}
			/*
			$str='<script language="javascript">'.CRLF;
			$str.='var cats=new Array();'.CRLF;
			$str.='var subcats=new Array();'.CRLF;
			$str.='var subcatsndx=new Array();'.CRLF;
			foreach($cats as $cat) {
				$n=1;
				$str.='cats['.$cat.']=\''.$genres[$cat]->genre.'\';'.CRLF;
				$str.='subcats['.$cat.']=new Array();'.CRLF;
				$str.='subcatsndx['.$cat.']=new Array();'.CRLF;
				$str.='subcats['.$cat.'][0]=\''._('Any').'\';'.CRLF;
				$str.='subcatsndx['.$cat.'][0]='.$cat.';'.CRLF;
				foreach($subgenres[$cat] as $subcat) {
					$str.='subcats['.$cat.']['.$n.']=\''.$genres[$subcat]->genre.'\';'.CRLF;
					$str.='subcatsndx['.$cat.']['.$n++.']=\''.$subcat.'\';'.CRLF;
				}
			}
			$str.='</script>'.CRLF;
			echo $str;
			*/
			if ($selsub && $genres[$selsub]->cat) {
				$cat=$catgenre[$selsub];
			} else {
				$cat=$selsub;
			}
			$subcat=$selsub;
			echo '<table class="dirgen"><tr>';
			echo '<td class="dirgencat"><table class="dirgencat">';
			foreach($cats as $catid) {
				$class=($catid==$cat)?'dirgensel':'dirgentab';
				echo '<tr><td class="'.$class.'"><a href="'.mx_actionurl($page,$option,$selmode.':'.$catid).'">'
				.$genres[$catid]->genre.'</a></td><td class="dirarrow">'.($catid==$cat?'&rarr;':'').'</td></tr>';
			}
			echo '</table></td>';
			echo '<td class="dirgenre"><div class="dirgenre"><table id="dirgenre" class="dirgenre">';
			if ($selsub) {
				echo _('<div class="genrehelp">&darr; Select a subcategory below [optional]</div>');
				$col=0;
				foreach($subgenres[$cat] as $subcatid) {
					if (!$col) echo '<tr>';
					$class=($subcatid==$selsub)?'dirgensel':'dirgentab';
					echo '<td class="'.$class.'"><a href="'.mx_actionurl($page,$option,$selmode.':'.$subcatid).'">'
					.$genres[$subcatid]->genre.'</a></td>';
					$col=((++$col)%3);
					if (!$col) echo '</tr>';
				}
				if ($col) echo '<td colspan="'.(3-$col).'"></td></tr>';
			} else {
				echo '<tr><td><div class="genrehelp">'._('&larr; Select a genre on the left<br/>to show subcategories').'</div></td></tr>';
			}
			echo '</table></div></td>';
			echo '</tr></table>';

			if ($cat && $cat==$subcat) {
				// main cat
				$filter=array('mx_acc2tast c','c.genre='.$selsub.' AND a.id=c.userid AND '.$fanfilter,
					sprintf(_('Listed in %s'),$genres[$cat]->genre));
				// sub cats
				$filter2=array('mx_acc2tast c, mx_genres d','d.cat=\''.$genres[$cat]->hash.'\' AND c.genre=d.id AND a.id=c.userid'
					.' AND '.$fanfilter,
					sprintf(_('Listed in subcategories of %s'),$genres[$cat]->genre));
			} else if ($cat) {
				// main subcat
				$filter=array('mx_acc2tast c','c.genre='.$selsub.' AND a.id=c.userid AND '.$fanfilter,
					sprintf(_('Listed in %s'),$genres[$selsub]->genre));
				// top cat
				$filter2=array('mx_acc2tast c','c.genre='.$cat.' AND a.id=c.userid AND '.$fanfilter,
					sprintf(_('Listed in %s'),$genres[$cat]->genre));
				$filter3=array('mx_acc2tast c, mx_genres d','(d.cat=\''.$genres[$cat]->hash.'\' AND d.id!='.$selsub
					.') AND c.genre=d.id AND a.id=c.userid AND '.$fanfilter,
					sprintf(_('Listed in other subcategories of %s'),$genres[$cat]->genre));
			}
			break;
		case 'alpha':
		default:
			$ndx="ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_";
			if (!$selsub) $selsub=substr($ndx,rand(0,strlen($ndx)-1),1);
			echo '<table class="diralpha"><tr>';
			for($i=0; $i<strlen($ndx); $i++) {
				$letter=substr($ndx,$i,1);
				$class=($letter==$selsub)?'diralphasel':'diralphatab';
				echo '<td class="'.$class.'"><a href="'.mx_actionurl($page,$option,$selmode.':'.$letter).'">'
				.($letter=='_'?_('Others'):$letter).'</a></td>';
			}
			//echo '<td class="diralphalast"></td>';
			echo '</tr>';
			echo '</table>';
			if ($selsub=='_') $selsub='[^a-zA-Z0-9]';
			$filter=$fanfilter.' AND fullname RLIKE \'^'.$selsub.'.*\''; // 'OR lastname RLIKE \'^'.$selsub.'.*\'';
			break;
	}
	if ($filter) $afilter[]=$filter;
	if ($filter2) $afilter[]=$filter2;
	if ($filter3) $afilter[]=$filter3;
	foreach ($afilter as $flt) {
		if (is_array($flt)) {
			echo '<h5>'.$flt[2].'</h5>';
		}
		$fansqr=$mxdb->fanlist($flt);
		echo '<table class="fanlist"><tr>';
		$c=0;
		if ($fansqr['count']==30) echo '<td colspan="6">'._('(30 fans chosen at random...)').'</td></tr><tr>';
		for($i=0;$i<$fansqr['count'];$i++) {
			$fan=$mxdb->fanlist(null,$fansqr['mxq']);
			if ($fan) {
				echo '<td class="friend">'; // onclick="window.location=\''.mx_actionurl('fans','fanprof',$fan->id).'\';">';
				echo '<a href="'.mx_actionurl('fans','fanprof',$fan->id).'" title="'
				.mx_getname($fan).' - '._('See Page')
				.'" class="pictooltip">';
				echo '<img tag="'.$fan->id.'" class="dirpic" src="'.mx_fanpic($fan->id,'square',$fan->gender).'">';
				echo '</a>';
				echo '<br/>'.mx_getname($fan);
				echo '</td>';
				$c=(++$c % 6);
				if (!$c) echo '</tr><tr>';
			}
		}
		if (!$i) {
			echo '<td colspan="6">'._('No fan listed here.').'</td>';
		}
		echo '</tr></table>';
	}
	echo '</div>';
}

function mx_mnfanprof($page,$option,$action,$simul='') {
	if ($action) mx_showfanpage($action,$simul);
	else mx_showhtmlpage('fanprof');
}

<?php
/* ---
 * Project: musxpand
 * File:    mx_artists.php
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

include_once 'includes/mx_init.php';

function mx_mnartists($page,$option,$action) {
	if (!$option) mx_mnartsdir('artists','artsdir',$action);
	//mx_showhtmlpage('artists');
}

function mx_mnartsdir($page,$option,$action) {
	global $mxdb,$mxuser;
	$mode=array(
		'alpha' => _('Alphabetical'),
		'genres' => _('By Genres'),
		//'-chrono' => _('Chronological'),
		'subs' => sprintf(_('Offering %s'),'<span class="hastooltip" title="'._('Fan Once Fan Always - A subscription formula that allows you to get one artist\'s media forever').'">FOFA</span>'),
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
	switch ($selmode) {
		case 'random':
			$filter='true';
			break;
		case 'subs':
			$filter=MXMINIMUMMEDIA.' <= (SELECT count(b.id) FROM mx_media b WHERE b.owner_id=a.id'
			.' AND b.type!='.MXMEDIABASEBUNDLE.' AND b.type!='.MXMEDIAREGULARBUNDLE
			.' AND b.status>='.MXMEDIAFANVISIBLE.' AND b.status<'.MXMEDIAARCHIVED.')';
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
				$filter=array('mx_acc2gen c','c.genre='.$selsub.' AND a.id=c.userid',
					sprintf(_('Listed in %s'),$genres[$cat]->genre));
				// sub cats
				$filter2=array('mx_acc2gen c, mx_genres d','d.cat=\''.$genres[$cat]->hash.'\' AND c.genre=d.id AND a.id=c.userid',
					sprintf(_('Listed in subcategories of %s'),$genres[$cat]->genre));
			} else if ($cat) {
				// main subcat
				$filter=array('mx_acc2gen c','c.genre='.$selsub.' AND a.id=c.userid',
					sprintf(_('Listed in %s'),$genres[$selsub]->genre));
				// top cat
				$filter2=array('mx_acc2gen c','c.genre='.$cat.' AND a.id=c.userid',
					sprintf(_('Listed in %s'),$genres[$cat]->genre));
				$filter3=array('mx_acc2gen c, mx_genres d','(d.cat=\''.$genres[$cat]->hash.'\' AND d.id!='.$selsub.') AND c.genre=d.id AND a.id=c.userid',
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
			$filter='artistname RLIKE \'^'.$selsub.'.*\'';
			break;
	}
	if ($filter) $afilter[]=$filter;
	if ($filter2) $afilter[]=$filter2;
	if ($filter3) $afilter[]=$filter3;
	foreach ($afilter as $flt) {
		if (is_array($flt)) {
			echo '<h5>'.$flt[2].'</h5>';
		}
		$artsqr=$mxdb->artslist($flt);
		echo '<table class="artslist"><tr>';
		$c=0;
		if ($artsqr['count']==30) echo '<td colspan="6">'._('(selection of 30 artists)').'</td></tr><tr>';
		for($i=0;$i<$artsqr['count'];$i++) {
			$arts=$mxdb->artslist(null,$artsqr['mxq']);
			if ($arts) {
				echo '<td class="fan">';// onclick="window.location=\''.mx_actionurl('artists','artprof',$arts->id).'\';">';
				echo '<a href="'.mx_actionurl('artists','artprof',$arts->id).'" title="'
				.htmlentities(mx_getartistname($arts)).' - '.($arts->shortbio?htmlentities($arts->shortbio):_('No Description'))
				.'" class="pictooltip">';
				echo '<img tag="'.$arts->id.'" class="dirpic" src="'.mx_artpic($arts->id,'square',$arts->gender).'">';
				echo '</a>';
				echo '<br/>'.mx_getartistname($arts);
				echo '</td>';
				$c=(++$c % 6);
				if (!$c) echo '</tr><tr>';
			}
		}
		if (!$i) {
			echo '<td colspan="6">'._('No artist listed here.').'</td>';
		}
		echo '</tr></table>';
	}
	echo '</div>';
}

function mx_showartistpage($id,$mediaid=-1,$simul='') {
	global $mxuser;
	$dbuser=$mxuser->getuserinfo($id);
	if ($dbuser->status==MXACCTDISABLED) {
		__('This account doesn\'t exist');
		return;
	}
	/*
	if ($dbuser->acctype==MXACCOUNTFAN) {
		echo sprintf(_('This is not an artist account<br/>'
		.'You may want to check this %s instead...'),mx_actionlink('fans','fanprof',$id));
		return;
	}
	*/
	if (!$dbuser || $dbuser->status==MXACCTDISABLED) {
		mx_optiontitle('error',_('This account doesn\'t exist'));
		return;
	}
	/*
	if ($dbuser->acctype==MXACCOUNTFAN || $option=='viewprof') {
		mx_optionsubtitle('&rarr; '.($dbuser->fullname?
		$dbuser->fullname:($dbuser->firstname.' '.$dbuser->lastname)));
	} else {*/
	if ($mxuser && $mxuser->id && $mxuser->id==$id) {
		if ($simul=='') $simdiv='<div class="simuldiv"><a href="'
		.mx_actionurl('account','myartpage','public').'">'._('(Public Preview)').'</a></div>';
		else $simdiv='<div class="simuldiv"><a href="'
		.mx_actionurl('account','myartpage','').'">'._('(Normal Preview)').'</a></div>';
	} else $simdiv='';
	mx_optionsubtitle($simdiv);
	/*mx_optionsubtitle('&rarr; '.($dbuser->artistname?
	$dbuser->artistname:$dbuser->fullname).$simdiv);*/
	//}
	$authflds=$mxuser->getauthorizedfields($dbuser->id);
	$authgrps=$mxuser->getauthorizedgroups($authflds);
	$section='';
	if (!$authgrps || !$authflds) {
		__('No information available.');
		return;
	}
	$custpage=$dbuser->custompage?$dbuser->custompage:MXDEFARTISTPAGE;
	$media=new stdClass();
	if ($mediaid<0 && $_GET['z']) $mediaid=0;
	$media->id=$mediaid;
	mx_showcustompage($custpage,$dbuser,$media,$simul);
	if ($id!=$mxuser->id) mx_fbaction('musxpand:check_out?artist='.urlencode(mx_actionurl('artists','artprof',$id)));
	if ($_GET['z']) {
		?>
		<script type="text/javascript">
		$(window).ready(function() {
			openbundle(0,0);
			//play(0);
		});
		</script>
		<?php
	}
}

function mx_ckartprof($page,$option,$action) {
}

function mx_mnartprof($page,$option,$action,$simul='') {
	global $mxuser;
	if ($action) {
		$mediaid=mx_secureword($_GET['m']);
		if (!$mediaid) $mediaid=-1;
		mx_showartistpage($action,$mediaid,$simul);
	}
	else mx_showhtmlpage('artprof');
}

function mx_xmlfanlike($artistid,$like='0',$dislike='0') {
	global $mxdb,$mxuser;
	if ($like || $dislike) {
		if ($like && $dislike) return;
		$numlikes=$mxuser->setartlike($artistid,$like);
		echo 'var likes='.$numlikes;
		return;
	}
	/* TODO: reviews...
	$comments=$mxdb->getcomments($msgid);
	if (!$comments) {
		__('No comments.');
		return;
	}
	$str='<table class="wcomment">';
	while ($comment=$mxdb->getcomments($msgid,$comments)) {
		$comment->mylikes=$mxuser->getlikes($comment->msgid);
		$str .= '<tr class="wcommentline">';
		$str .= '<td class="wallpic"><img src="'.mx_fanpic($comment->authid).'" /></td>';
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
	$str.='<tr class="wcommentline"><!-- <td class="wallpic"><img src="'.mx_fanpic($mxuser->id).'" /></td> -->'
		.'<td colspan="2">'.mx_formfield('mc_'.$msgid,'',$yrcomment).'<br/>'
		.'<input class="sendcomment" type="button" onclick="sendcomment('.$msgid.');" value="'._('Send').'">'
		.'</td>'
		.'</tr>';
	$str.='</table>';
	$str.='<a class="close" href="javascript:switchcomments(\''.$msgid.'\');">'
				.mx_icon('close','Close','16px')
				.'</a>';
	echo $str;
	*/
}

function mx_getfeatarts() {
	global $mxdb;
	return $mxdb->getfeatarts();
}

function mx_getrandarts($qty) {
	global $mxdb;
	return $mxdb->getrandarts($qty);
}

function featcmp($a,$b) {
	$a=rand(0,2)-1;
	return ($a);
}

function mx_mnfeatarts($page,$option,$action) {
	global $mxuser;
	$feats=mx_getfeatarts();
	$title='<div class="featurecenter">'.sprintf(_('Featured Artists,<br/>%s'),date('F Y')).'</div>';
	if (!count($feats)) {
		$feats=mx_getrandarts(12);
		$title='';
	}
	$tot=count($feats);
	/*
	if (!$tot)
		echo '<div class="featuremsg"><div class="featuremsgbg"></div>';
		echo '<div class="featuremsgtxt">';
		//mx_showhtmlpage('featarts');
		echo '<div class="featurecenter">'.sprintf(_('Featured Artists,<br/>%s'),date('F Y')).'</div>';
		echo '<div id="mediaplayer"><div id="playerwindow"></div></div>';
		echo '<table class="featarts">';
		$art=0;
		$maxart=3;
		usort($feats, featcmp);
		foreach ($feats as $featured) {
			if ($art==0) {
				echo '<tr>';
			}
			$user=new stdClass();
			$user=$mxuser->getuserinfo($featured);
			echo '<td class="featart">';
			echo '<div class="featartdetail">';
			echo '<a href="'.mx_actionurl('artists','artprof',$featured).'">';
			echo '<img class="artistpic" src="'.mx_artpic($user->id,'large',$user->gender).'" />';
			echo '</a>';
			echo '<div class="featartname">'.strtoupper(mx_getartistname($user)).'</div>';
			echo '<div class="featsong">'.mx_showonemedia($user).'</div>';
			echo '</div>'; // featartdetail
			echo '</td>'; // featart
			$art=(++$art)%$maxart;
			if ($art==0) {
				echo '</tr>';
			}
		}
		if ($art!=0 && $art<$maxart) {
			echo '<td colspan="'.($maxart-$art).'">&nbsp;</td></tr>';
		}
		echo '</table>'; // featarts
		//echo '<div class="feathelp">'._('Click on the artists\' pictures to discover more about them.').'</div>';
		echo '</div></div>';
	} else { //if (is_admin()) {
	*/
		//mx_showhtmlpage('nofeatarts');
		echo '<div class="featuremsg"><div class="featuremsgbg"></div>';
		echo '<div class="featuremsgtxt">';
		//mx_showhtmlpage('featarts');
		echo $title;
		echo '<div id="mediaplayer"><div id="playerwindow"></div></div>';
		echo '<div id="sk1" class="sliderkit">'
		.'<div class="sliderkit-panel">'
		.'<div class="sliderkit-nav">'
		.'<div class="sliderkit-nav-clip"><ul>';
		for ($i=0;$i<$tot;$i++) { //$feats as $featured) {
			$featured=$feats[$i];
			$user=new stdClass();
			$user=$mxuser->getuserinfo($featured);
			echo '<li>';//<a href="#" rel="nofollow" title="'.mx_getartistname($user).'"></a>';
			echo '<div class="featartdetail">';
			echo '<a href="'.mx_actionurl('artists','artprof',$featured).'" title="'.mx_getartistname($user).' - '._('See Page').'" class="pictooltip" onclick="document.location=this.href;">';
			echo '<img tag="'.$user->id.'" class="artistpic mxobject" src="'.mx_artpic($user->id,'large',$user->gender).'" />';
			echo '</a>';
			//echo '<div class="featartname">'.strtoupper(mx_getartistname($user)).'</div>';
			echo '<div class="featsong">'.mx_showonemedia($user).'</div>';
			echo '</div>'; // featartdetail
			echo '</li>';
		}
		echo '</ul></div>';
		echo '<div class="sliderkit-nav-btn sliderkit-nav-prev"><a rel="nofollow" href="#" title="'
		._('Previous Artist').'"><span>'._('Previous').'</span></a></div>'
		.'<div class="sliderkit-nav-btn sliderkit-nav-next"><a rel="nofollow" href="#" title="'
		._('Next Artist').'"><span>'._('Next').'</span></a></div>';
		echo '</div></div>';
		echo '<div class="sliderkit-timer-wrapper"><div class="sliderkit-timer"></div></div>';
		echo '</div>';
		/*
		echo '<div id="sk2" class="sliderkit">'
		.'<div class="sliderkit-nav">'
		.'<div class="sliderkit-nav-clip"><ul>';
		for ($i=floor($tot/2); $i<$tot; $i++) { //$feats as $featured) {
			$featured=$feats[$i];
			$user=new stdClass();
			$user=$mxuser->getuserinfo($featured);
			echo '<li>';//<a href="#" rel="nofollow" title="'.mx_getartistname($user).'"></a>';
			echo '<div class="featartdetail">';
			echo '<a href="'.mx_actionurl('artists','artprof',$featured).'">';
			echo '<img class="artistpic" src="'.mx_artpic($user->id,'large',$user->gender).'" />';
			echo '</a>';
			//echo '<div class="featartname">'.strtoupper(mx_getartistname($user)).'</div>';
			echo '<div class="featsong">'.mx_showonemedia($user).'</div>';
			echo '</div>'; // featartdetail
			echo '</li>';
		}
		echo '</ul></div>';
		echo '<div class="sliderkit-nav-btn sliderkit-nav-prev"><a rel="nofollow" href="#" title="'
		._('Previous Artist').'"><span>'._('Previous').'</span></a></div>'
		.'<div class="sliderkit-nav-btn sliderkit-nav-next"><a rel="nofollow" href="#" title="'
		._('Next Artist').'"><span>'._('Next').'</span></a></div>';
		echo '</div></div>';
		*/
		//echo '<div class="feathelp">'._('Click on the artists\' pictures to discover more about them.').'</div>';
		echo '</div></div>';
	/*
	} /*else {
		//mx_showhtmlpage('nofeatarts');
		echo '<div class="featuremsg"><div class="featuremsgbg"></div>';
		echo '<div class="featuremsgtxt">';
		//mx_showhtmlpage('featarts');
		//echo '<div class="featurecenter">'._('Artists To Discover').'</div>';
		echo '<div id="mediaplayer"><div id="playerwindow"></div></div>';
		echo '<table class="featarts">';
		$feats=mx_getrandarts(6);
		$art=0;
		$maxart=3;
		usort($feats, featcmp);
		foreach ($feats as $featured) {
			if ($art==0) {
				echo '<tr>';
			}
			$user=new stdClass();
			$user=$mxuser->getuserinfo($featured);
			echo '<td class="featart">';
			echo '<div class="featartdetail">';
			echo '<a href="'.mx_actionurl('artists','artprof',$featured).'">';
			echo '<img class="artistpic" src="'.mx_artpic($user->id,'large',$user->gender).'" />';
			echo '</a>';
			//echo '<div class="featartname">'.strtoupper(mx_getartistname($user)).'</div>';
			echo '<div class="featsong">'.mx_showonemedia($user).'</div>';
			echo '</div>'; // featartdetail
			echo '</td>'; // featart
			$art=(++$art)%$maxart;
			if ($art==0) {
				echo '</tr>';
			}
		}
		if ($art!=0 && $art<$maxart) {
			echo '<td colspan="'.($maxart-$art).'">&nbsp;</td></tr>';
		}
		echo '</table>'; // featarts
		echo '<div class="feathelp">'._('Click on the artists\' pictures to discover more about them.').'</div>';
		echo '</div></div>';
	}*/
}

function mx_artpics() {
	global $mxdb;
	$artsqr=$mxdb->artslist('status>='.MXACCTSETUP); // random
	for($i=0;$i<$artsqr['count'];$i++) {
		$arts=$mxdb->artslist(null,$artsqr['mxq']);
		if ($arts && $arts->picture=='local') {
			$rot=rand(0,60)-30;
			$scale=rand(100,200)/100;
			$posx=rand(10,90);
			$posy=rand(10,90);
			echo '<div style="left:'.$posx.'%; top:'.$posy.'%; -webkit-transform:rotate('.$rot.'deg) scale('.$scale.'); -moz-transform:rotate('.$rot.'deg) scale('.$scale.');" class="frontpic">';
			echo '<img tag="'.$arts->id.'" class="artistpic pictooltip" src="'.mx_artpic($arts->id,'large',$arts->gender).'"'
			.' title="'.mx_getartistname($arts).' - '._('Drag n\' drop me...').'">'
			.'<br/>'.mx_getartistname($arts);
			echo '</div>';
		}
	}

}

function mx_randminipics() {
	global $mxdb;
	$artsqr=$mxdb->artslist('status>='.MXACCTSETUP); // random
	for($i=0;$i<$artsqr['count'];$i++) {
		$arts=$mxdb->artslist(null,$artsqr['mxq']);
		if ($arts && $arts->picture=='local') {
			echo '<a href="'.mx_actionurl('artists','artprof',$arts->id).'">';
			echo '<img tag="'.$arts->id.'" class="minipic pictooltip" src="'.mx_artpic($arts->id,'square',$arts->gender).'"'
			.' title="'.htmlentities(mx_getartistname($arts)).' - '.($arts->shortbio?htmlentities($arts->shortbio):_('No Description')).'">';
			echo '</a>';
		}
	}

}
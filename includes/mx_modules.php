<?php
/* ---
 * Project: musxpand
 * File:    mx_modules.php
 * Author:  phil
 * Date:    Jul 6, 2012
 * ---
    This file is part of musxpand.
    Copyright � 2010-2011 by Philippe Hilger
 */


function mx_frame($name) {
	global $mxuser,$MXDEFAULTMODULES,$MXUNSIGNEDMODULES;
	if (!$mxuser->id) {
		$modules=$MXUNSIGNEDMODULES;
	} else if ($mxuser->status<MXACCTSETUP || !$mxuser->modules) {
		$modules=$MXDEFAULTMODULES;
	} else $modules=$mxuser->modules;
	$ok=0;
	//error_log('user: '.$mxuser->id.' name: '.$name.' - modules: '.count($mxuser->modules));
	if (array_key_exists($name,$modules)) {
		foreach ($modules[$name] as $module) {
			$ok=1;
			$fnname='mx_fr'.$module;
			if (function_exists($fnname)) {
				echo '<div id="mxm_'.$module.'" class="module"><div class="modulename">'.$module.'</div><div class="modulecontent">';
				$fnname();
				echo '</div></div>';
			}
			else echo '<div class="module"><div class="modulename">'.$module.'</div><div class="modulecontent">'.$fnname.'</div></div>';
		}
	}
	//if (!$ok) echo '<div class="module nomodule"></div>';
}

function mx_frsearch() {
	mx_searchbox();
}

function mx_frpagehead() {
	echo 'Page title goes here...';
}

function mx_frdropmenu() {
	mx_dropmenu();
}

function mx_frminimenu() {
	mx_minimenu();
}

function mx_frmusxmenu() {
	mx_musxmenu();
}

function mx_frusermenu() {
	mx_usermenu();
}

function mx_frpicture() {
	mx_userpic();
	mx_showname();
}

function mx_frlogo() {
	?>
	<div class='logo'><a href='<?php mx_proption('basicsiteurl'); ?>' alt='<?php __('Home'); ?>'><img tag="musxpand" class="logopic" src='<?php mx_proption('m-logoURL'); ?>' /></a>
		<?php echo '<img tag="mxversion" class="betapic" src="'.mx_option('betalogoURL').'" />'; ?>
	</div>
	<?php
}

function mx_frplayer() {
?>
	<div class="playerbar">
		<div id="mediaplayerbar"><div id="playerwindow"></div></div>
	</div>
<?php
}

function mx_friconmenu() {
	mx_iconmenu();
}

function mx_frmainmenu() {
	mx_mainmenu();
}

function mx_frcontent() {
	mx_content();
}

function mx_frfbsocial() {
	mx_sociallikes();
}

function mx_frfavbar() {
	mx_favorites();
}

function mx_fryahoogrp() {
	echo '<div class="mx-infobar"><a target=_blank href="http://groups.yahoo.com/group/musxpand-newsletter/join">
  <img class="pictooltip" title="MusXpand Newsletter - Subscribe here" src="http://us.i1.yimg.com/us.yimg.com/i/yg/img/i/ca/ui/join.gif"
       style="border: 0px;"
       alt="Click to join musxpand-newsletter"/></a>
	</div>';
}

function mx_frads($adtag) {
	return;
	$page=mx_secureword($_GET['p']);
	$option=mx_secureword($_GET['o']);
	if ($page=='artists') $ptype='a';
	else if ($page=='fans') $ptype='f';
	else if ($page=='account' && ($option=='signin' || $option=='register')) $ptype='l';
	else if ($page=='media') $ptype='m';
	else if ($page!='account' || ($option!='profile' && $option!='setup')) $ptype='o';
	else $ptype='';
	switch ($adtag) {
		case 'mxleft':
			switch ($ptype) {
				case 'a':
					?>
					<script type="text/javascript"><!--
					google_ad_client = "ca-pub-9134103384133476";
					/* Artist Skyscraper */
					google_ad_slot = "8168451933";
					google_ad_width = 160;
					google_ad_height = 600;
					//-->
					</script>
					<script type="text/javascript"
					src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
					</script>
					<?php
					break;
				case 'f':
					?>
					<script type="text/javascript"><!--
					google_ad_client = "ca-pub-9134103384133476";
					/* Member Skyscraper */
					google_ad_slot = "4883634994";
					google_ad_width = 160;
					google_ad_height = 600;
					//-->
					</script>
					<script type="text/javascript"
					src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
					</script>
					<?php
					break;
				case 'l':
					?>
					<script type="text/javascript"><!--
					google_ad_client = "ca-pub-9134103384133476";
					/* Login Skyscraper */
					google_ad_slot = "8937798150";
					google_ad_width = 160;
					google_ad_height = 600;
					//-->
					</script>
					<script type="text/javascript"
					src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
					</script>
					<?php
					break;
				case 'm':
					?>
					<script type="text/javascript"><!--
					google_ad_client = "ca-pub-9134103384133476";
					/* Media Skyscraper */
					google_ad_slot = "3002453629";
					google_ad_width = 160;
					google_ad_height = 600;
					//-->
					</script>
					<script type="text/javascript"
					src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
					</script>
					<?php
					break;
				case 'o':
					?>
					<script type="text/javascript"><!--
					google_ad_client = "ca-pub-9134103384133476";
					/* General Skyscraper */
					google_ad_slot = "8515852109";
					google_ad_width = 160;
					google_ad_height = 600;
					//-->
					</script>
					<script type="text/javascript"
					src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
					</script>
					<?php
					break;
				default:
					// no ads (protected pages)
					break;
			}
			break;
	}
}

function mx_frmxpromoon() {
	mx_frmxpromo();
}

function mx_frmxpromo() {
	//if (!MXBETA) return;
	echo '<div class="mx-infobar">';
	mx_showhtmlpage('mxpromo');
	echo '</div>';
}

function mx_frmxquickfacts() {
	//if (!MXBETA) return;
	echo '<div class="mx-infobar">';
	mx_showhtmlpage('mxquickfacts');
	if ($_GET['newmx']) mx_counton();
	echo '</div>';

}

function mx_frrandminipics() {
	//if (!MXBETA) return;
	echo '<div class="mx-infobar">';
	echo '<h4>'._('Meet some artists').'</h4>';
	mx_randminipics();
	echo '</div>';
}

function mx_frbuy() {
	global $prodtypes,$subtypes,$subprices;
	if (!MXBETA) return;
	echo '<div class="mx-infobar">';
	echo '<h4>'._('Subscribe').'</h4>';
	echo '<ul class="featlist">';
	foreach ($subtypes as $subkey => $subtype) {
		echo '<li><a href="'.mx_actionurl('cart','',$subkey).'">'.$prodtypes[MXSITESUB][1][$subtype].'</a>';
		echo '<div class="buyprice">'.sprintf('$%.2f',$subprices[$subkey]).'</div>';
		echo '</li>';
	}
	echo '</ul>';
	echo '</div>';
}

function mx_frrockethub() {
	echo '<div class="mx-infobar">';
	echo '<h4>RocketHub</h4>';
	?>
	<iframe src="http://www.rockethub.com/projects/5604-musxpand-bringing-artists-and-fans-together/widgets/project_box" allowtransparency="true" frameborder="0" scrolling="no" width="183" height="315"></iframe>
	<?php
	echo '</div>';
}

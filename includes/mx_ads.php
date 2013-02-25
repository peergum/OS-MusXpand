<?php
/* ---
 * Project: musxpand
 * File:    mx_ads.php
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

$bannertable=array(
	//array(desturl,picurl),
	//array('',mx_option('siteurl').'/images/banners/musxpand-banner1-580x60.png'), // hightime
	//array('',mx_option('siteurl').'/images/banners/musxpand-banner2-580x60.png'), // authentic
	//array('',mx_option('siteurl').'/images/banners/musxpand-banner3-580x60.png'), // quality-fanship
	//array('',mx_option('siteurl').'/images/banners/musxpand-banner4-580x60.png'), // why is it?
	//array('/bestdeal',mx_option('siteurl').'/images/general/whymusxpand.png'),
	);

function mx_ads() {
	global $mxdb,$mxuser;
	$ad=$mxdb->getad();
	echo '<div class="ads"><table>';
	echo '<tr><td class="title">'.$ad->title.'</td></tr>';
	echo '<tr><td class="content">'.$ad->content.'</td></tr>';
	echo '<tr><td class="link"><a href="'.$ad->link.'" target="_blank">'.mx_domain($ad->link).'</a></td></tr>';
	echo '</table>';
	echo '</div>';
	if (false && $mxuser->sponsored && !MXBETA) {
		//echo '<div class="ads"><table><tr><td>';
?>
	<script type="text/javascript"><!--
	google_ad_client = "ca-pub-9488055659400443";
	/* RV-Banner-Small */
	google_ad_slot = "3302706289";
	google_ad_width = 120;
	google_ad_height = 240;
	//-->
	</script>
	<script type="text/javascript"
	src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
	</script>
<?php
		//echo '</td></tr></table></div>';
	}
}

function mx_banner() {
	global $mxdb, $bannertable;
	if (!count($bannertable)) return;
	echo '<div class="banners">';
	$banner=$bannertable[rand(0,count($bannertable)-1)];
	$str='<img src="'.$banner[1].'"/>';
	if ($banner[0]) $str='<a target=_blank href="'.$banner[0].'">'.$str.'</a>';
	echo $str;
	echo '</div>';
}
?>

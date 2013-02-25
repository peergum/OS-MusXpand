<?php
/* ---
 * Project: musxpand
 * File:    index.php
 * Author:  phil
 * Date:    28/09/2010
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

/*
 * default template
 */

function tp_index() {
	?>
<style>
body {
	background: #ffffff url('<?php mx_proption('backgroundURL'); ?>') center repeat fixed;
}
div.whitebg {
	background: url('<?php mx_proption('transparencyURL'); ?>') repeat;
}
</style>
<div class='mainwrapper whitebg'>
	<div class='header'>
		<div class='logo'><a href='<?php mx_proption('basicsiteurl'); ?>' alt='<?php __('Home'); ?>'><img src='<?php mx_proption('logoURL'); ?>' /></a>
		<?php /*if (MXBETA==false) echo '<a href="'.mx_optionurl('about','cQ')
		.'" alt="'._('Version').'"><img src="'.mx_option('versionlogoURL').'" /></a>';
		else */ echo '<img src="'.mx_option('betalogoURL').'" />'; ?>
		</div>
		<div class='topbar'><?php
		echo '<div class="supportphone">'.MXSUPPORTPHONE.'</div>';
		/*mx_ctxmenu();*/ mx_searchbox(); mx_helpmenu(); ?></div>
		<div class='topright'><?php mx_iconmenu(); ?></div>
	</div>
	<div class='columns'>
		<div class='leftcolumn'>
			<?php mx_userpic(); ?>
			<?php mx_showname(); ?>
			<?php mx_mainmenu(); ?>
			<?php mx_usermenu(); ?>
			<?php mx_counton(); ?>
			<?php mx_frdonline(); ?>
			<?php mx_artonline(); ?>
			<?php mx_sociallikes(); ?>
			<?php //echo gmdate('c'); ?>
		</div>
		<div class='maincolumn' id='maincolumn'>
			<?php //if (!is_logged()) { ?>
			<div class='banner'>
			<?php mx_banner(); ?>
			</div>
			<?php //} ?>
			<div class='main'>
				<div class='rightbar'>
				</div>
				<div id="content" class='content'>
				<?php mx_content(); ?>
				</div>
			</div>
		</div>
		<div class='rightcolumn'>
			<?php mx_notice(); ?>
			<?php mx_ads(); ?>
		</div>
	</div>
	<div class='footer'>
		<div class='bottomleft'></div>
		<div class='bottombar'><?php mx_musxmenu(); ?></div>
		<div class='bottomright'>
		<?php mx_infosecure(); ?>
		</div>
	</div>

</div>

	<?php
}

?>

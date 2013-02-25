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
	background: #ffffff; //url('<?php mx_proption('backgroundURL'); ?>') center repeat fixed;
}
div.whitebg {
	background: url('<?php mx_proption('transparencyURL'); ?>') repeat;
}
</style>
<div class='mainwrapper whitebg'>
	<div class='header'>
		<div class='topbar'>
		<img src='<?php mx_proption('mxbannerURL'); ?>' />
		</div>
	</div>
	<div class='columns'>
		<div class='maincolumn'>
			<div class='main'>
				<div id="content" class='content'>
				<?php mx_content(); ?>
				</div>
			</div>
		</div>
	</div>
	<div class='footer'>
		<div class='bottombar'>
		</div>
	</div>

</div>

	<?php
}

?>

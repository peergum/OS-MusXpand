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

    Copyright © 2010 by Philippe Hilger
 */

/*
 * default template
 */
 
function tp_index() {
	?>

<div class='mainwrapper'>
	<div class='mainpage'>
		<div class='leftcolumn'>
			<div class='logo'><img src='<?php echo mx_option(templatedir).'/images/mxproject-logo.png'; ?>' /></div>
			<div class='usermenu'>
			<?php mx_usermenu(); ?>
			</div>
		</div>
		<div class='maincolumn'>
			<div class='header'>
			header
			</div>
			<div class='ctxmenu'><?php mx_ctxmenu(); ?></div>
			<div class='main'>
			main content
				<div class='rightbar'>
				Right
				<div>
			</div>
		</div>
	</div>
</div>

	<?php
}

?>

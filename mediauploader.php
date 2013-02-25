<?php
/* ---
 * Project: musXpand
 * File:    audioanalyzer.php
 * Author:  phil
 * Date:    Nov 13, 2011
 * ---
    This file is part of project_name.
    Copyright � 2010-2011 by Philippe Hilger
 */

set_include_path(get_include_path().':'.dirname(__FILE__));
$_SERVER['DOCUMENT_ROOT']=dirname(__FILE__);

include 'includes/mx_mediaanalyzer.php';

mx_uploadqueue();

<?php
/* ---
 * Project: musxpand
 * File:    counter.php
 * Author:  phil
 * Date:    Mar 21, 2012
 * ---
    This file is part of musxpand.
    Copyright � 2010-2012 by Philippe Hilger
 */

require_once 'includes/mx_db.php';

global $mxdb;

$users=$mxdb->counton();

die(json_encode($users));
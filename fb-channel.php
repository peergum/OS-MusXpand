<?php
/* ---
 * Project: MusXpand
 * File:    fb-channel.php
 * Author:  phil
 * Date:    Oct 24, 2011
 * ---
    This file is part of project_name.
    Copyright � 2010 by Philippe Hilger
 */

 $cache_expire = 60*60*24;; //*365;
 header("Pragma: public");
 header("Cache-Control: max-age=".$cache_expire);
 header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$cache_expire) . ' GMT');
 ?>
 <script src="//connect.facebook.net/en_US/all.js"></script>
<?php
/* ---
 * Project: musxpand
 * File:    config.inc
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

define('MXICONCLICK','onclick="window.open(\'http://en.wikipedia.org/wiki/2012_Quebec_student_strike\',\'_blank\');"');

define('MXDBSERVER','localhost');
define ('MXDBNAME','musxpand');
define ('MXDBUSER','myusername');
define ('MXDBPASSWORD','mypassword'); // CHANGE THIS

define ('MXSITEURL','http://www.example.com');
define ('MXSECURESITEURL','https://www.example.com');

define('FACEBOOK_APP_ID', 'xxxxxx');
define('FACEBOOK_SECRET', 'xxxxxx');
define('FACEBOOK_PERMS','email,user_birthday,user_about_me,user_website,publish_actions');
define('MXFACEBOOKPAGE','xxxxxx'); // your facebook page ID (if any)

define('MX_RECAPTCHA_PUBLIC','xxxxxx'); // CHANGE THIS
define('MX_RECAPTCHA_PRIVATE','xxxxxx'); // CHANGE THIS

define('MXSUPPORTEMAIL','support@example.com');
define('MXSALESEMAIL','sales@example.com');
define('MXNOTIFEMAIL','notifications@example.com');
define('MXNOREPLYEMAIL','no-reply@example.com');

define('FOYFEE','15.00');
define('FOFAFEE','30.00');
define('FOFAYRS','FOREVER');

define('DEBUG',false);
if ($_GET['beta']) define('MXBETA',true);
else define('MXBETA',false);
define('MXPAYPALSANDBOX',false);
define('MXCHECKOUTOK',true);

define('AwsAK','xxx'); // your amazon Access Key
define('AwsSK','xxx'); // your amazon secret key

// --- Amazon S3
define('MXS3BUCKET','musxpand');
define('MXAUDIOQUEUE','MXNewAudio');
define('MXMEDIAQUEUEURL','https://queue.amazonaws.com/xxxxxxx/MXNewMedia'); // put your Amazon SQS queue number instead of xxxxxx
define('MXUPLOADQUEUEURL','https://queue.amazonaws.com/xxxxxxx/MXNewUpload');

define('MXMINIMUMMEDIA',10); // minimum number of media to enable subcriptions.
define('MXINVITEONLY',false);

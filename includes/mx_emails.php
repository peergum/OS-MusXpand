<?php
/* ---
 * Project: musxpand
 * File:    mx_emails.php
 * Author:  phil
 * Date:    06-sep-2011
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

    Copyright � 2011 by Philippe Hilger
 */

require_once 'mx_amazon.php'; // amazon services

function mx_sendmail($to,$subj,$txt,$html=null) {
	global $ses;
	$msg=array(
		"Subject.Data" => $subj,
		"Body.Text.Data" => $txt
	);
	if ($html) $msg["Body.Html.Data"]=$html;
	$from='MusXpand <'.MXNOTIFEMAIL.'>';
	$replyto='MusXpand <'.MXNOTIFEMAIL.'>';
	$returnpath='contact@example.com';
	$ato=array(
		"ToAddresses" => $to
	);
	$opt=array(
		"ReplyToAddresses" => $replyto,
		"ReturnPath" => $returnpath
	);
	$resp=$ses->SendEmail($from,$ato,$msg,$opt); //TODO
}

$ses=new AmazonSES(AwsAK,AwsSK);
//$ses->enableVerifyHost(false);
//$ses->enableVerifyPeer(false);


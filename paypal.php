<?php
/* ---
 * Project: musxpand
 * File:    paypal.php
 * Author:  phil
 * Date:    09/09/2011
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

require 'includes/mx_paypal.php';


/*
 * Paypal IPN implementation
 * This is called when a payment occurs or a recurring payment is created/made/cancelled
 */

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {

	// read the post from PayPal system and add 'cmd'
	$req = 'cmd=_notify-validate';

	foreach ($_POST as $key => $value) {
		$value = urlencode(stripslashes($value));
		$req .= "&$key=$value";
	}

	// post back to PayPal system to validate
	$header .= "POST ".$PAYPAL_IPN_SCRIPT." HTTP/1.0\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
	$fp = fsockopen ($PAYPAL_IPN_URL, 443, $errno, $errstr, 30);
	$log=fopen('/tmp/pp.log','a+');

	fputs($log,"\n-----\n". $header . $req);

	// assign posted variables to local variables
	$item_name = $_POST['item_name'];
	$item_number = $_POST['item_number'];
	$payment_status = $_POST['payment_status'];
	$payment_amount = $_POST['mc_gross'];
	$payment_currency = $_POST['mc_currency'];
	$parent_txn_id = $_POST['parent_txn_id'];
	$txn_id = $_POST['txn_id'];
	$txn_type = $_POST['txn_type'];
	$receiver_email = $_POST['receiver_email'];
	$payer_email = $_POST['payer_email'];
	$recurring_payment_id = $_POST['recurring_payment_id'];
	$profile_status = $_POST['profile_status'];
	$next_payment_date = $_POST['next_payment_date'];

	if (!$fp) {
		// HTTP ERROR
		fputs($log,"\n".'Oops... FAILED connecting!');
	} else {
		fputs ($fp, $header . $req);
		while (!feof($fp)) {
			$res = fgets ($fp, 1024);
			if (strcmp ($res, "VERIFIED") == 0) {
				// check the payment_status is Completed
				// check that txn_id has not been previously processed
				// check that receiver_email is your Primary PayPal email
				// check that payment_amount/payment_currency are correct
				// process payment
				fputs($log,"\n".'Paypal says "VERIFIED"');
				if ($receiver_email!=$API_UserName) {
					fputs($log,"\nReceiver_email CONFIRMED");
				} else {
					fputs($log,"\nReceiver_email WRONG!!!");
				}
				switch ($txn_type) {
					case 'cart':
						if ($txn_id) {
							$cart=$mxdb->getcartbytransaction($txn_id);
							fputs($log,"\ncart id=".$cart->id);
							if ($payment_status=="Completed"
								&& $cart->paymentstatus!='Completed') {
								mx_confirmcart($cart->accountid,$cart->id);
							}
						}
						break;
					case 'recurring_payment_profile_created':
						// cool, nothing to do for now...
						mx_setsubrenewal($recurring_payment_id,$next_payment_date);
						fputs($log,"\nrecurring payment ID:".$recurring_payment_id);
						break;
					case 'recurring_payment_profile_cancel':
						mx_norenewsub($recurring_payment_id);
						fputs($log,"\nrecurring payment ID:".$recurring_payment_id);
						break;
					case 'recurring_payment':
						if ($payment_status=='Completed') {
							mx_confirmsubrenewal($recurring_payment_id,$next_payment_date);
						}
						fputs($log,"\nrecurring payment ID:".$recurring_payment_id);
						break;
					default:
						if ($payment_status=='Refunded') {
							$cart=$mxdb->getcartbytransaction($parent_txn_id);
							fputs($log,"\ncart id=".$cart->id);
							//mx_refundcart($cart->accountid,$cart->id);
						}
						break;
				}

			} else if (strcmp ($res, "INVALID") == 0) {
				// log for manual investigation
				fputs($log,"\n".'Paypal says "INVALID"');
			}
		}
	}
	fclose ($fp);
	fclose($log);
}

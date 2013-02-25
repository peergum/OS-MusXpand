<?php
/* ---
 * Project: musxpand
 * File:    mx_paypal.php
 * Author:  phil
 * Date:    Apr 14, 2011
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

include_once 'mx_init.php';
require_once ("ext_includes/paypalfunctions.php");

function mx_checkout($cart) {
	global $mxuser;
	// ==================================
	// PayPal Express Checkout Module
	// ==================================

	//'------------------------------------
	//' The paymentAmount is the total value of
	//' the shopping cart, that was set
	//' earlier in a session variable
	//' by the shopping cart page
	//'------------------------------------
	$paymentAmount = $cart->total + $cart->taxes;
	$_SESSION["Payment_Amount"]=$paymentAmount;

	//'------------------------------------
	//' The currencyCodeType and paymentType
	//' are set to the selections made on the Integration Assistant
	//'------------------------------------
	$currencyCodeType = "USD";
	$paymentType = "Sale";

	//'------------------------------------
	//' The returnURL is the location where buyers return to when a
	//' payment has been succesfully authorized.
	//'
	//' This is set to the value entered on the Integration Assistant
	//'------------------------------------
	$returnURL = mx_optionurl_secure('cart','');

	//'------------------------------------
	//' The cancelURL is the location buyers are sent to when they hit the
	//' cancel button during authorization of payment during the PayPal flow
	//'
	//' This is set to the value entered on the Integration Assistant
	//'------------------------------------
	$cancelURL = mx_actionurl('cart','','ppcancel');

	// callback URL to calculate taxes (and shipping fees)
	$callbackURL = mx_option('secure_siteurl').'/paypal.php';

	//'------------------------------------
	//' Calls the SetExpressCheckout API call
	//'
	//' The CallShortcutExpressCheckout function is defined in the file PayPalFunctions.php,
	//' it is included at the top of this file.
	//'-------------------------------------------------
	$resArray = CallShortcutExpressCheckout ($cart, $currencyCodeType, $paymentType, $returnURL, $cancelURL, $callbackURL);
	$ack = strtoupper($resArray["ACK"]);
	if($ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING")
	{
		$mxuser->setcart($cart->id,'token',$resArray['TOKEN']);
		$mxuser->setcart($cart->id,'ordertime',preg_replace('%[^0-9]%','',$resArray['TIMESTAMP']));
		RedirectToPayPal ( $resArray["TOKEN"] );
	}
	else
	{
		//Display a user friendly Error on the page using any of the following error information returned by PayPal
		$ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
		$ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
		$ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
		$ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);

		//echo "SetExpressCheckout API call failed. <br/>"
		//."Detailed Error Message: " . $ErrorLongMsg.'<br/>'
		//."Short Error Message: " . $ErrorShortMsg.'<br/>'
		//."Error Code: " . $ErrorCode.'<br/>'
		//."Error Severity Code: " . $ErrorSeverityCode;
	}
	return $resArray;

}

function mx_billing($cart) {

	if ( $PaymentOption == "PayPal")
	{
        // ==================================
        // PayPal Express Checkout Module
        // ==================================

        //'------------------------------------
        //' The paymentAmount is the total value of
        //' the shopping cart, that was set
        //' earlier in a session variable
        //' by the shopping cart page
        //'------------------------------------
        $paymentAmount = $_SESSION["Payment_Amount"];

        //'------------------------------------
        //' When you integrate this code
        //' set the variables below with
        //' shipping address details
        //' entered by the user on the
        //' Shipping page.
        //'------------------------------------
        $shipToName = "<<ShiptoName>>";
        $shipToStreet = "<<ShipToStreet>>";
        $shipToStreet2 = "<<ShipToStreet2>>"; //Leave it blank if there is no value
        $shipToCity = "<<ShipToCity>>";
        $shipToState = "<<ShipToState>>";
        $shipToCountryCode = "<<ShipToCountryCode>>"; // Please refer to the PayPal country codes in the API documentation
        $shipToZip = "<<ShipToZip>>";
        $phoneNum = "<<PhoneNumber>>";

        //'------------------------------------
        //' The currencyCodeType and paymentType
        //' are set to the selections made on the Integration Assistant
        //'------------------------------------
        $currencyCodeType = "USD";
        $paymentType = "Sale";

        //'------------------------------------
        //' The returnURL is the location where buyers return to when a
        //' payment has been succesfully authorized.
        //'
        //' This is set to the value entered on the Integration Assistant
        //'------------------------------------
        $returnURL = "http://www.example.com/pp/gateway.php";

        //'------------------------------------
        //' The cancelURL is the location buyers are sent to when they hit the
        //' cancel button during authorization of payment during the PayPal flow
        //'
        //' This is set to the value entered on the Integration Assistant
        //'------------------------------------
        $cancelURL = "http://www.example.com/pp/gateway.php";

        //'------------------------------------
        //' Calls the SetExpressCheckout API call
        //'
        //' The CallMarkExpressCheckout function is defined in the file PayPalFunctions.php,
        //' it is included at the top of this file.
        //'-------------------------------------------------
        $resArray = CallMarkExpressCheckout ($paymentAmount, $currencyCodeType, $paymentType, $returnURL,
			$cancelURL, $shipToName, $shipToStreet, $shipToCity, $shipToState,
			$shipToCountryCode, $shipToZip, $shipToStreet2, $phoneNum
        );

        $ack = strtoupper($resArray["ACK"]);
        if($ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING")
        {
            $token = urldecode($resArray["TOKEN"]);
            $_SESSION['reshash']=$token;
            RedirectToPayPal ( $token );
        }
        else
        {
            //Display a user friendly Error on the page using any of the following error information returned by PayPal
            $ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
            $ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
            $ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
            $ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);

            echo "SetExpressCheckout API call failed. ";
            echo "Detailed Error Message: " . $ErrorLongMsg;
            echo "Short Error Message: " . $ErrorShortMsg;
            echo "Error Code: " . $ErrorCode;
            echo "Error Severity Code: " . $ErrorSeverityCode;
        }
	}
	else
	{
        if ((( $PaymentOption == "Visa") || ( $PaymentOption == "MasterCard") || ($PaymentOption == "Amex") || ($PaymentOption == "Discover"))
                        && ( $PaymentProcessorSelected == "PayPal Direct Payment"))

        //'------------------------------------
        //' The paymentAmount is the total value of
        //' the shopping cart, that was set
        //' earlier in a session variable
        //' by the shopping cart page
        //'------------------------------------
        $paymentAmount = $_SESSION["Payment_Amount"];

        //'------------------------------------
        //' The currencyCodeType and paymentType
        //' are set to the selections made on the Integration Assistant
        //'------------------------------------
        $currencyCodeType = "USD";
        $paymentType = "Sale";

        //' Set these values based on what was selected by the user on the Billing page Html form

        $creditCardType = "<<Visa/MasterCard/Amex/Discover>>"; //' Set this to one of the acceptable values (Visa/MasterCard/Amex/Discover) match it to what was selected on your Billing page
        $creditCardNumber = "<<CC number>>"; //' Set this to the string entered as the credit card number on the Billing page
        $expDate = "<<Expiry Date>>"; //' Set this to the credit card expiry date entered on the Billing page
        $cvv2 = "<<cvv2>>"; //' Set this to the CVV2 string entered on the Billing page
        $firstName = "<<firstName>>"; //' Set this to the customer's first name that was entered on the Billing page
        $lastName = "<<lastName>>"; //' Set this to the customer's last name that was entered on the Billing page
        $street = "<<street>>"; //' Set this to the customer's street address that was entered on the Billing page
        $city = "<<city>>"; //' Set this to the customer's city that was entered on the Billing page
        $state = "<<state>>"; //' Set this to the customer's state that was entered on the Billing page
        $zip = "<<zip>>"; //' Set this to the zip code of the customer's address that was entered on the Billing page
        $countryCode = "<<PayPal Country Code>>"; //' Set this to the PayPal code for the Country of the customer's address that was entered on the Billing page
        $currencyCode = "<<PayPal Currency Code>>"; //' Set this to the PayPal code for the Currency used by the customer

        /*
        '------------------------------------------------
        ' Calls the DoDirectPayment API call
        '
        ' The DirectPayment function is defined in PayPalFunctions.php included at the top of this file.
        '-------------------------------------------------
        */

        $resArray = DirectPayment ( $paymentType, $paymentAmount, $creditCardType, $creditCardNumber,
			$expDate, $cvv2, $firstName, $lastName, $street, $city, $state, $zip,
			$countryCode, $currencyCode );

        $ack = strtoupper($resArray["ACK"]);
        if($ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING")
        {
            //Getting transaction ID from API responce.
            $TransactionID = urldecode($resArray["TRANSACTIONID"]);

            echo "Your payment has been successfully processed";
        }
        else
        {
            //Display a user friendly Error on the page using any of the following error information returned by PayPal
            $ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
            $ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
            $ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
            $ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);

            echo "Direct credit card payment API call failed. ";
            echo "Detailed Error Message: " . $ErrorLongMsg;
            echo "Short Error Message: " . $ErrorShortMsg;
            echo "Error Code: " . $ErrorCode;
            echo "Error Severity Code: " . $ErrorSeverityCode;
        }
	}
}

function mx_orderreview() {
	/*==================================================================
	 PayPal Express Checkout Call
	 ===================================================================
	*/
	// Check to see if the Request object contains a variable named 'token'
	$token = "";
	if (isset($_REQUEST['token']))
	{
		$token = $_REQUEST['token'];
	}

	// If the Request object contains the variable 'token' then it means that the user is coming from PayPal site.
	if ( $token != "" )
	{

		/*
		'------------------------------------
		' Calls the GetExpressCheckoutDetails API call
		'
		' The GetShippingDetails function is defined in PayPalFunctions.jsp
		' included at the top of this file.
		'-------------------------------------------------
		*/


		$resArray = GetShippingDetails( $token );
		$ack = strtoupper($resArray["ACK"]);
		if( $ack == "SUCCESS" || $ack == "SUCESSWITHWARNING")
		{
			/*
			' The information that is returned by the GetExpressCheckoutDetails call should be integrated by the partner into his Order Review
			' page
			*/
			$email 				= $resArray["EMAIL"]; // ' Email address of payer.
			$payerId 			= $resArray["PAYERID"]; // ' Unique PayPal customer account identification number.
			$payerStatus		= $resArray["PAYERSTATUS"]; // ' Status of payer. Character length and limitations: 10 single-byte alphabetic characters.
			$salutation			= $resArray["SALUTATION"]; // ' Payer's salutation.
			$firstName			= $resArray["FIRSTNAME"]; // ' Payer's first name.
			$middleName			= $resArray["MIDDLENAME"]; // ' Payer's middle name.
			$lastName			= $resArray["LASTNAME"]; // ' Payer's last name.
			$suffix				= $resArray["SUFFIX"]; // ' Payer's suffix.
			$cntryCode			= $resArray["COUNTRYCODE"]; // ' Payer's country of residence in the form of ISO standard 3166 two-character country codes.
			$business			= $resArray["BUSINESS"]; // ' Payer's business name.
			$shipToName			= $resArray["SHIPTONAME"]; // ' Person's name associated with this address.
			$shipToStreet		= $resArray["SHIPTOSTREET"]; // ' First street address.
			$shipToStreet2		= $resArray["SHIPTOSTREET2"]; // ' Second street address.
			$shipToCity			= $resArray["SHIPTOCITY"]; // ' Name of city.
			$shipToState		= $resArray["SHIPTOSTATE"]; // ' State or province
			$shipToCntryCode	= $resArray["SHIPTOCOUNTRYCODE"]; // ' Country code.
			$shipToZip			= $resArray["SHIPTOZIP"]; // ' U.S. Zip code or other country-specific postal code.
			$addressStatus 		= $resArray["ADDRESSSTATUS"]; // ' Status of street address on file with PayPal
			$invoiceNumber		= $resArray["INVNUM"]; // ' Your own invoice or tracking number, as set by you in the element of the same name in SetExpressCheckout request .
			$phonNumber			= $resArray["PHONENUM"]; // ' Payer's contact telephone number. Note:  PayPal returns a contact telephone number only if your Merchant account profile settings require that the buyer enter one.
		}
		else
		{
			//Display a user friendly Error on the page using any of the following error information returned by PayPal
			$ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
			$ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
			$ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
			$ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);

			//echo "GetExpressCheckoutDetails API call failed. ";
			//echo "Detailed Error Message: " . $ErrorLongMsg;
			//echo "Short Error Message: " . $ErrorShortMsg;
			//echo "Error Code: " . $ErrorCode;
			//echo "Error Severity Code: " . $ErrorSeverityCode;
		}
	}
	return $resArray;
}

function mx_orderconfirmation($cart) {
		/*==================================================================
		 PayPal Express Checkout Call
		 ===================================================================
		*/

	if ( $cart->paymentoption == "PayPal" )
	{
		/*
		'------------------------------------
		' The paymentAmount is the total value of
		' the shopping cart, that was set
		' earlier in a session variable
		' by the shopping cart page
		'------------------------------------
		*/

		$finalPaymentAmount =  $cart->total+$cart->taxes;

		/*
		'------------------------------------
		' Calls the DoExpressCheckoutPayment API call
		'
		' The ConfirmPayment function is defined in the file PayPalFunctions.jsp,
		' that is included at the top of this file.
		'-------------------------------------------------
		*/

		$resArray = ConfirmPayment ( $cart );
		$ack = strtoupper($resArray["ACK"]);
		if( $ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING" )
		{
			/*
			'********************************************************************************************************************
			'
			' THE PARTNER SHOULD SAVE THE KEY TRANSACTION RELATED INFORMATION LIKE
			'                    transactionId & orderTime
			'  IN THEIR OWN  DATABASE
			' AND THE REST OF THE INFORMATION CAN BE USED TO UNDERSTAND THE STATUS OF THE PAYMENT
			'
			'********************************************************************************************************************
			*/

			$transactionId		= $resArray["TRANSACTIONID"]; // ' Unique transaction ID of the payment. Note:  If the PaymentAction of the request was Authorization or Order, this value is your AuthorizationID for use with the Authorization & Capture APIs.
			$transactionType 	= $resArray["TRANSACTIONTYPE"]; //' The type of transaction Possible values: l  cart l  express-checkout
			$paymentType		= $resArray["PAYMENTTYPE"];  //' Indicates whether the payment is instant or delayed. Possible values: l  none l  echeck l  instant
			$orderTime 			= $resArray["ORDERTIME"];  //' Time/date stamp of payment
			$amt				= $resArray["AMT"];  //' The final amount charged, including any shipping and taxes from your Merchant Profile.
			$currencyCode		= $resArray["CURRENCYCODE"];  //' A three-character currency code for one of the currencies listed in PayPay-Supported Transactional Currencies. Default: USD.
			$feeAmt				= $resArray["FEEAMT"];  //' PayPal fee amount charged for the transaction
			$settleAmt			= $resArray["SETTLEAMT"];  //' Amount deposited in your PayPal account after a currency conversion.
			$taxAmt				= $resArray["TAXAMT"];  //' Tax charged on the transaction.
			$exchangeRate		= $resArray["EXCHANGERATE"];  //' Exchange rate if a currency conversion occurred. Relevant only if your are billing in their non-primary currency. If the customer chooses to pay with a currency other than the non-primary currency, the conversion occurs in the customer�s account.

			/*
			' Status of the payment:
					'Completed: The payment has been completed, and the funds have been added successfully to your account balance.
					'Pending: The payment is pending. See the PendingReason element for more information.
			*/

			$paymentStatus	= $resArray["PAYMENTSTATUS"];

			/*
			'The reason the payment is pending:
			'  none: No pending reason
			'  address: The payment is pending because your customer did not include a confirmed shipping address and your Payment Receiving Preferences is set such that you want to manually accept or deny each of these payments. To change your preference, go to the Preferences section of your Profile.
			'  echeck: The payment is pending because it was made by an eCheck that has not yet cleared.
			'  intl: The payment is pending because you hold a non-U.S. account and do not have a withdrawal mechanism. You must manually accept or deny this payment from your Account Overview.
			'  multi-currency: You do not have a balance in the currency sent, and you do not have your Payment Receiving Preferences set to automatically convert and accept this payment. You must manually accept or deny this payment.
			'  verify: The payment is pending because you are not yet verified. You must verify your account before you can accept this payment.
			'  other: The payment is pending for a reason other than those listed above. For more information, contact PayPal customer service.
			*/

			$pendingReason	= $resArray["PENDINGREASON"];

			/*
			'The reason for a reversal if TransactionType is reversal:
			'  none: No reason code
			'  chargeback: A reversal has occurred on this transaction due to a chargeback by your customer.
			'  guarantee: A reversal has occurred on this transaction due to your customer triggering a money-back guarantee.
			'  buyer-complaint: A reversal has occurred on this transaction due to a complaint about the transaction from your customer.
			'  refund: A reversal has occurred on this transaction because you have given the customer a refund.
			'  other: A reversal has occurred on this transaction due to a reason not listed above.
			*/

			$reasonCode		= $resArray["REASONCODE"];
		}
		else
		{
			//Display a user friendly Error on the page using any of the following error information returned by PayPal
			$ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
			$ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
			$ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
			$ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);

			//echo "DoExpressCheckout API call failed. ";
			//echo "Detailed Error Message: " . $ErrorLongMsg;
			//echo "Short Error Message: " . $ErrorShortMsg;
			//echo "Error Code: " . $ErrorCode;
			//echo "Error Severity Code: " . $ErrorSeverityCode;
		}
	}
	return $resArray;
}

function mx_recurrentpayment($cart,$line) {
	return RecurrentPayment($cart,$line);
}

function mx_cancelrecurrentpayment($profileid) {
	return CancelRecurrentPayment($profileid);
}

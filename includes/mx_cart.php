<?php
/* ---
 * Project: musxpand
 * File:    mx_cart.php
 * Author:  phil
 * Date:    Mar 6, 2011
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

function mx_ckcart($page,$option,$action) {
	global $mxuser,$prodtypes,$subtypes,$prodprice;
	$cartid=mx_secureword($_REQUEST['cartid']);
	$token=mx_securestring($_GET['token']);
	$cart=$mxuser->getcart($cartid?$cartid:null,($action=='printorder' || $action=='confckout')); // get informed cart, pending cart or open new one
	if ($token && $action=='') $action='ppcall';
	//if ($action=='pp-checkout' || $action=='confckout') {
		$cart->lines=$mxuser->getcartdetails($cart->id);
		$cart->total=0;
		foreach($cart->lines as $line) {
			//if ($line->prodtype!=MXARTSUB || $line->prodvar!=MXSUBFOY)
				$cart->total+=$line->price;
			if ($line->prodtype==MXARTSUB) {
				$user=$mxuser->getuserinfo($line->prodref);
				$line->name=htmlentities(substr(mx_getartistname($user),0,80));
			} else if ($line->prodtype==MXSITESUB) {
				$line->name=sprintf('Account #%d',$mxuser->id);
			} else if ($line->prodtype==MXMEDSUB) {
				$media=$mxuser->getmediainfo($line->prodref);
				$line->name=htmlentities(substr($media->title,0,80));
			} else $line->name=_('Unnamed Product');
			$line->desc=$prodtypes[$line->prodtype][0].', '.$prodtypes[$line->prodtype][1][$line->prodvar];
		}
		switch($cart->taxcountrycode) {
			// Add for CANADIAN TAXES
			/*
			case 'CA':
				$cart->taxes=$cart->total*MXTAXHST;
				break;
			*/
			default:
				$cart->taxes=0;
				break;
		}
		$cart->items=count($cart->lines);
	//}
	switch ($action) { // adding to cart
		case 'addfoy':
		case 'addfofa':
		case 'upgfofa':
			$subnum=0;
			$artistid=mx_secureword($_REQUEST['id']);
			$prodvar=($action=='addfoy'?MXSUBFOY:($action=='addfofa'?MXSUBFOFA:MXUPGFOFA));
			$price=($action=='addfoy'?MXFEEFOY:($action=='addfofa'?MXFEEFOFA:(MXFEEFOFA-MXFEEFOY)));
			foreach($cart->lines as $line) {
				$subnum+=($line->prodtype==MXARTSUB && $line->prodref!=$artistid)?1:0;
			}
			if ($subnum<10) {
				$mxuser->addcart($cart->id,MXARTSUB,$artistid,$prodvar,$price);
				$prodprice=$price;

			} else {
				$cart->err=array(
					'sorry' => _('We\'re sorry but our payment processing'
					.' company<br/>limits the number of yearly fanships per order to 10.<br/>'
					.' In case you would like to become a fan of more than 10 artists,<br/>'
					.' please do it in separate orders. Thank you.')
				);
			}
			//$mxuser->addwish(MXARTSUB,$artistid,$prodvar,$price);
			$cart->progress=1;
			break;
		//case 'freesub':
		case 'basicsub':
		case 'plussub':
		case 'premsub':
			$subnum=0;
			$prodref=-1; // site wide (no ref)
			$prodvar=$subtypes[$action]; // site subscription type
			if ($action=='freesub') $price=MXFEEFREE;
			else if ($action=='basicsub') $price=MXFEEBASIC;
			else if ($action=='plussub') $price=MXFEEPLUS;
			else if ($action=='premsub') $price=MXFEEPREMIUM;
			foreach($cart->lines as $line) {
				$subnum+=($line->prodtype!=MXSITESUB)?1:0;
			}
			if ($subnum<10) {
				$mxuser->addcart($cart->id,MXSITESUB,$prodref,$prodvar,$price);
				$prodprice=$price;
			} else {
				$cart->err=array(
					'sorry' => _('We\'re sorry but our payment processing'
					.' company<br/>limits the number of subscriptions per order to 10.<br/>'
					.' In case you would like to make more than 10 subscriptions,<br/>'
					.' please do so in separate orders. Thank you.')
				);
			}
			//$mxuser->addwish(MXSITESUB,$artistid,$prodvar,$price);
			$cart->progress=1;
			break;
		case 'medbuy':
			$prodref=preg_replace('%[^0-9]%','',mx_secureword($_REQUEST['m']));
			$media=$mxuser->getmediainfo($prodref);
			if ($media->type==MXMEDIABASEBUNDLE || $media->type==MXMEDIAREGULARBUNDLE) {
				$price=$media->cartprice; //round(MXFEESONGS*($media->tracks+($media->bigpics>4?($media->bigpics-4):0)+$media->videos),2);
				$prodvar=MXBUYBUNDLE;
			} else if ($media->type==MXMEDIAINSTR || $media->type=MXMEDIASONG) {
				$price=$media->cartprice; //MXFEE1SONG;
				$prodvar=MXBUYMEDIA;
			} else {
				$price=$media->cartprice; //MXFEE1SONG;
				$prodvar=MXBUYMEDIA;
			}
			$mxuser->addcart($cart->id,MXMEDSUB,$prodref,$prodvar,$price);
			$cart->progress=1;
			break;
		case 'medunbuy':
			$prodref=preg_replace('%[^0-9]%','',mx_secureword($_REQUEST['m']));
			$prodline=0;
			foreach($cart->lines as $line) {
				if ($line->prodtype==MXMEDSUB && $line->prodref==$prodref)
					$prodline=$line->id;
			}
			if ($prodline) $mxuser->deletecart($cart->id,array($prodline));
			$cart->progress=1;
			break;
		case 'delcart':
			$cartlines=$_POST['cartline'];
			$mxuser->deletecart($cart->id,$cartlines);
			$cart->progress=1;
			break;
		case 'delwish':
			$wishlines=$_POST['wishline'];
			$mxuser->deletewish($wishlines);
			$cart->progress=1;
			break;
		case 'tocart':
			$wishlines=$_POST['wishline'];
			$mxuser->wishtocart($cart->id,$wishlines);
			$_REQUEST['k']='cart';
			$cart->progress=1;
			break;
		case 'towish':
			$cartlines=$_POST['cartline'];
			$mxuser->carttowish($cart->id,$cartlines);
			$cart->progress=1;
			break;
		case 'shopmore':
		case 'shopmore_w':
			header('location: '
			.mx_optionurl('artists','artsdir'));
			$cart->progress=1;
			break;
		case 'checkout':
			$cart->progress=2;
			$mxuser->setcart($cart->id,'status',MXCARTCHECKOUTADDRESS);
			break;
		case 'pp-checkout':
			$mxuser->setcart($cart->id,'status',MXCARTCHECKOUTPAYPAL);
			$cart->err=mx_checkout($cart);
			// at this point we should have been redirected to paypal, otherwise: not good :(
			$cart->progress=2;
			break;
		case 'ppcall':
			/*
			die(phpinfo());
			preg_match_all('%([a-zA-Z0-9_]+)=([^&]+)%',$_SERVER['REQUEST_URI'],$ppparams);
			foreach ($ppparams[1] as $key => $elem) {
				$ppal[$elem]=$ppparams[2][$key];
			}
			*/
			$orderinfo=mx_orderreview();
			$cart->orderinfo=$orderinfo;
			$billadd=array(
				'addresstype' => MXBILLINGADDRESS,
				'cartid' => $cart->id,
				'email' => $orderinfo['EMAIL'],
				'salutation' => $orderinfo['SALUTATION'],
				'first' => $orderinfo['FIRSTNAME'],
				'middle' => $orderinfo['MIDDLENAME'],
				'last' => $orderinfo['LASTNAME'],
				'suffix' => $orderinfo['SUFFIX'],
				'business' => $orderinfo['BUSINESS'],
				'pppayerid' => $orderinfo['PAYERID'],
				'pppayerstatus' => $orderinfo['PAYERSTATUS']
			);
			$shipadd=array(
				'addresstype' => MXSHIPPINGADDRESS,
				'cartid' => $cart->id,
				'shiptoname' => $orderinfo['SHIPTONAME'],
				'street1' => $orderinfo['SHIPTOSTREET'],
				'street2' => $orderinfo['SHIPTOSTREET2'],
				'city' => $orderinfo['SHIPTOCITY'],
				'state' => $orderinfo['SHIPTOSTATE'],
				'countrycode' => $orderinfo['SHIPTOCOUNTRYCODE'],
				'zip' => $orderinfo['SHIPTOZIP'],
				'addressstatus' => $orderinfo['ADDRESSSTATUS'],
				'phone' => $orderinfo['SHIPTOPHONENUM']
			);
			$mxuser->clearaddresses($cart->id);
			$billid=$mxuser->addaddress($billadd);
			$shipid=$mxuser->addaddress($shipadd);
			$mxuser->setcart($cart->id,'billingid',$billid);
			$mxuser->setcart($cart->id,'shippingid',$shipid);
			$mxuser->setcart($cart->id,'status',MXCARTCONFIRM);
			$mxuser->setcart($cart->id,'memo',$orderinfo['PAYMENTREQUEST_0_NOTETEXT']);
			$mxuser->setcart($cart->id,'invoicenum',$orderinfo['INVNUM']);
			$mxuser->setcart($cart->id,'taxcountrycode',$shipadd['countrycode']);
			$cart->taxcountrycode=$shipadd['countrycode'];
			$cart->progress=3;
			break;
		case 'ppcancel': // cancelled in paypal
		case 'canckout': // cancelled at order review
			/*
			preg_match_all('%([a-zA-Z0-9_]+)=([^&]+)%',$_SERVER['REQUEST_URI'],$ppparams);
			foreach ($ppparams[1] as $key => $elem) {
				$ppal[$elem]=$ppparams[2][$key];
			}
			*/
			//die(phpinfo());
			$cart->progress=1;
			// cancel token for paypal session
			$mxuser->setcart($cart->id,'token','');
			$mxuser->setcart($cart->id,'status',
			($action=='ppcancel'?MXCARTCANCELLEDFROMPAYPAL:MXCARTCANCELLEDFROMCONFIRM));
			// remove billing/shipping addresses
			$mxuser->deladdress($cart->billingid);
			$mxuser->deladdress($cart->shippingid);
			$mxuser->setcart($cart->id,'billingid','');
			$mxuser->setcart($cart->id,'shippingid','');
			$cart->info=_('You just canceled the checkout process.<br/>' .
					'You can make changes to your cart, adding new items to it<br/>' .
					' or moving some items to your wish list for a later purchase.');
			break;
		case 'confckout':
		case 'printorder':
			/*
			die(phpinfo());
			preg_match_all('%([a-zA-Z0-9_]+)=([^&]+)%',$_SERVER['REQUEST_URI'],$ppparams);
			foreach ($ppparams[1] as $key => $elem) {
				$ppal[$elem]=$ppparams[2][$key];
			}
			*/
			$cart->paymentoption=mx_securestring($_POST['paymentoption']);
			if (!$cart->transactionid) { // first submit (saving payment to DB)
				if ($cart->total>0) {
					$orderconfirm=mx_orderconfirmation($cart);
					$cart->orderconfirm=$orderconfirm;
					$paymentinfo=array(
						'transactionid' => $orderconfirm['PAYMENTINFO_0_TRANSACTIONID'],
						'ordertime' => preg_replace('%[^0-9]%','',$orderconfirm['PAYMENTINFO_0_ORDERTIME']),
						'total' => $orderconfirm['PAYMENTINFO_0_AMT'],
						'paypalfee' => $orderconfirm['PAYMENTINFO_0_FEEAMT'],
						'taxes' => $orderconfirm['PAYMENTINFO_0_TAXAMT'],
						'currency' => $orderconfirm['PAYMENTINFO_0_CURRENCYCODE'],
						'paymentstatus' => $orderconfirm['PAYMENTINFO_0_PAYMENTSTATUS'],
						'pendingreason' => $orderconfirm['PAYMENTINFO_0_PENDINGREASON'],
						'reasoncode' => $orderconfirm['PAYMENTINFO_0_REASONCODE'],
						'receiptid' => $orderconfirm['PAYMENTINFO_0_RECEIPTID'],
						'status' => MXCARTCONFIRMED
					);
				} else { // subscriptions only
					$cart->orderconfirm=array();
					$paymentinfo=array(
						'paymentstatus' => 'Pending',
						'pendingreason' => 'RecurrentPaymentValidation',
						'status' => MXCARTCONFIRMED
					);
				}
				$mxuser->setcartbatch($cart->id,$paymentinfo);
				mx_addsubs($mxuser->id,$cart->id);
				if ($paymentinfo['paymentstatus']=='Completed') mx_confirmcart($mxuser->id,$cart->id);
				foreach ($cart->lines as $line) {
					if (($line->prodtype==MXARTSUB && $line->prodvar==MXSUBFOY)
					|| ($line->prodtype==MXSITESUB && $line->prodvar!=MXSUBFREE)) {
						$ppinfo=mx_recurrentpayment($cart,$line);
						mx_setsubinfo($mxuser->id,$line,$ppinfo);
					}
				}
			} else { // already confirmed do not submit again, but get the data from the DB instead...
				if ($cart->total>0) {
					$orderconfirm=array(
						'PAYMENTINFO_0_PAYMENTSTATUS' => $cart->paymentstatus,
						'PAYMENTINFO_0_TRANSACTIONID' => $cart->transactionid,
						'PAYMENTINFO_0_ORDERTIME' => $cart->ordertime,
						'PAYMENTINFO_0_AMT' => $cart->total,
						'PAYMENTINFO_0_TAXAMT' => $cart->taxes,
						'PAYMENTINFO_0_CURRENCYCODE' => $cart->currency,
						//'PAYERID' => $cart->payerid,
					);
				} else { // subscriptions only
					$orderconfirm=array();
				}
				$cart->orderconfirm=$orderconfirm;
			}
			$cart->info=_('Thanks for your order.<br/>We will activate your subscriptions and purchases' .
				'<br/>as soon as we get the confirmation of your payment.<br/>' .
				'Please print this page for your records');
			$cart->progress=4;
			break;
		default:
			$cart->progress=1;
			break;
	}
	// get new cart details
	if ($action!='pp-checkout' && $action!='confckout') {
		$cart->lines=$mxuser->getcartdetails($cart->id);
	}
	foreach($cart->lines as $ndx => $line) {
		$note='';
		if ($line->prodtype==MXMEDSUB) {
			$media=$mxuser->getmediainfo($line->prodref);
			foreach($cart->lines as $other) {
				//error_log('other: '.$other->prodtype.' '.$other->prodref);
				//error_log('bun[0]: '.$media->bundles[0]->id);
				if ($other->prodtype==MXARTSUB && $other->prodref==$media->owner_id) {
					$note=buywarn(sprintf(_('This media is already included in your subscription to %s'),$media->artistname));
					//error_log('included sub!!');
				} else if ($other->prodtype==MXMEDSUB && $other->prodref==$media->bundles[0]->id) {
					$note=buywarn(sprintf(_('This media is already part of bundle "%s"'),$media->bundles[0]->title));
					//error_log('included bundle!!');
				}
			}
			if ($media->owner_id==$mxuser->id) {
				$note=buywarn(_('Buying your own media...?'));
			}
		} else if ($line->prodtype==MXARTSUB) {
			if ($line->prodref==$mxuser->id)
				$note=buywarn(_('Hey! That\'s your own account...'));
		}
		$cart->lines[$ndx]->note=$note;
	}
	$cart->items=count($cart->lines);
	$cart->wishes=$mxuser->getwishlist();
	//$cart->items=count($cart->lines);
	$mxuser->cart=$cart;
}

function buywarn($msg) {
	return '<br/><div class="buywarn">'.$msg.'</div>';
}

function mx_proddesc($product) {
	global $mxuser;
	switch($product->prodtype) {
		case MXARTSUB:
			$userinfo=$mxuser->getuserinfo($product->prodref);
			$name=mx_getartistname($userinfo);
			return '<table class="name"><tr><td class="subline"><img tag="'.$userinfo->id.'" class="subpic" src="'.mx_artpic($userinfo->id,'square',$userinfo->gender)
			.'"/></td>'
			.'<td><a href="'.mx_actionurl('artists','artprof',$userinfo->id).'" alt="'.$name.'">'
			.$name.$product->note
			.'</a></td></tr></table>';
			break;
		case MXSITESUB:
			return '<table class="name"><tr><td class="subline"><img class="subpic" src="'.mx_option('m-logoURL-48x48')
			.'"/></td>'
			.'<td><a href="#" alt="'.'MusXpand'.'">'
			.'MusXpand Account #'.$mxuser->id.'<br/>('.$mxuser->getname().')'
			.'</a></td></tr></table>';
			break;
		case MXMEDSUB:
			$media=$mxuser->getmediainfo($product->prodref);
			$fanship=$mxuser->getfanship($media->owner_id,$media->id);
			mx_medialist($media,$fanship);
			return '<table class="name"><tr><td class="subline"><img tag="'.$media->id
			.'" class="subpic" src="'.$media->pic.'" /></td>'
			.'<td><a href="'.mx_actionurl('media','medprof',$media->id).'">'
			.$media->title
			.'</a><br/><span class="byartist">'.sprintf('by %s',
				'<a href="'.mx_actionurl('artists','artprof',$media->owner_id).'">'.$media->artistname.'</a>')
			.$product->note
			.'</span></td></tr></table>';
			break;
		default:
			return _('Undefined Product');
	}
}

function mx_mncart($page,$option,$action) {
	global $mxuser,$prodtypes,$mxdb,$prodprice;
	$prodlist=array();
	$progress=$mxuser->cart->progress;
	if ($mxuser->cart->lines) {
		foreach ($mxuser->cart->lines as $line) {
			//if ($mxuser->cart->orderinfo || $mxuser->cart->orderconfirm)
			if ($mxuser->cart->progress>1)
				$line->select='<img height="12px" src="'.mx_iconurl('okmark').'">';
			else
				$line->select='<input type="checkbox" name="cartline[]" value="'.$line->id.'">';
			$line->proddesc=mx_proddesc($line);
			/*if ($line->prodtype==MXARTSUB) {
				$user=$mxuser->getuserinfo($line->prodref);
				$line->prodref='<div class="cartline"><img class="cartpic" src="'.mx_fanpic($user->id).'" /> '.mx_getartistname($user).'</div>';
			}*/
			$line->prodvar=$prodtypes[$line->prodtype][1][$line->prodvar];
			$line->prodtype=$prodtypes[$line->prodtype][0];
			$prodlist['cart'][]=$line;
		}
	}
	if ($mxuser->cart->wishes) {
		foreach ($mxuser->cart->wishes as $line) {
			$line->select='<input type="checkbox" name="wishline[]" value="'.$line->id.'">';
			$line->proddesc=mx_proddesc($line);
			/*if ($line->prodtype==MXARTSUB) {
				$user=$mxuser->getuserinfo($line->prodref);
				$line->prodref=mx_getartistname($user);
			}*/
			$line->prodvar=$prodtypes[$line->prodtype][1][$line->prodvar];
			$line->prodtype=$prodtypes[$line->prodtype][0];
			$prodlist['wishlist'][]=$line;
		}
	}
	if ($mxuser->cart->items>0) $contshoppinglabel=_('Continue Shopping');
	else $contshoppinglabel=_('Go Shopping');

	$yourehere=_('** YOU\'RE HERE **');
	$progresstable='<table class="form progress"><tr class="top">'
	.'<td class="'.($progress==1?'current':'done').'">'._('Shopping Cart').'</td>'
	.'<td'.($progress>1?' class="done"':' class="todo"').'>&rarr;</td>'
	.'<td class="'.($progress==2?'current':($progress<2?'next':'done')).'">'._('Shipping/Billing').'</td>'
	.'<td'.($progress>2?' class="done"':' class="todo"').'>&rarr;</td>'
	.'<td class="'.($progress==3?'current':($progress<3?'next':'done')).'">'._('Order Review').'</td>'
	.'<td'.($progress>3?' class="done"':' class="todo"').'>&rarr;</td>'
	.'<td class="'.($progress==4?'current':'next').'">'._('Order Confirmation').'</td>'
	.'<td class="last"></td>'
	.'</tr><tr class="bottom">'
	.'<td>'.($progress==1?$yourehere:'').'</td>'
	.'<td></td>'
	.'<td>'.($progress==2?$yourehere:'').'</td>'
	.'<td></td>'
	.'<td>'.($progress==3?$yourehere:'').'</td>'
	.'<td></td>'
	.'<td>'.($progress==4?$yourehere:'').'</td>'
	.'<td class="last"></td>'
	.'</tr></table>';

	if ($mxuser->cart->err)
		$progresstable.=mx_warningstr(implode('<br/>',$mxuser->cart->err));
	if ($mxuser->cart->info)
		$progresstable.=mx_infomsgstr($mxuser->cart->info);
	//echo $progresstable;
	if ($mxuser->cart->orderinfo) {		// checkcout confirmation
		$ckoutbuttons = array(
			'canckout' => _('Cancel Checkout'),
			'confckout' => _('Confirm PAYPAL Payment')
		);
		$cartlist=array(
			'cart' => array(
				'select' => array(0,'','text',3),
				'prodtype' => array(0,_('Item'),'text',20),
				'proddesc' => array(0,_('Description'),'html',30),
				'prodvar' => array(0,_('Type'),'text',20),
				'price' => array(0,_('Price'),'price',10),
			)
		);
		$salesterms=mx_windowedpage('salesterms',_('Terms & Conditions'));
		$ckoutlist=array(
			'checkout',0,_('Checkout Review Page'),
			$progresstable,
			$ckoutbuttons,
			array(
				'cart' => array(-1,_('Checking out these products'),
					_('The following items are currently in your cart')),
				'cartcontent' => array(-2,$cartlist,$prodlist,'cart',array(),'cart'),
				'billing' => array(-1,_('Billing Information'),
				''), //_('Please check your billing information')),
				'FIRSTNAME' => array(0,_('Firstname'),'text',30),
				'LASTNAME' => array(0,_('Lastname'),'text',30),
				'COUNTRYCODE' => array(0,_('Country'),'text',3),
				'EMAIL' => array(0,_('Billing E-Mail'),'text',40),
				'shipping' => array(-1,_('Shipping Information'),
					_('Please check your shipping information')),
				'SHIPTONAME' => array(0,_('Ship To'),'text',30),
				'SHIPTOSTREET' => array(0,_('Address'),'text',30),
				'SHIPTOCITY' => array(0,_('City'),'text',20),
				'SHIPTOSTATE' => array(0,_('State/Province'),'text',20),
				'SHIPTOZIP' => array(0,_('Zip Code'),'text',10),
				'SHIPTOCOUNTRYNAME' => array(0,_('Country'),'text',20),
				'salesterms' => array(-1,_('Sales Terms And Conditions'),
					sprintf(_('Please read and agree with our %s before' .
							' confirming your payment'),
							$salesterm)),
				'agreement'  => array(1,_('Agreement'),'checkbox',
							sprintf(_('I accept MusXpand\'s sales %s'),mx_windowedpage('salesterms',_('terms & conditions'))),
							_('You have to agree to continue...')),
				'a' => array(1,'none','hidden'),
				'PAYERID' => array(1,'none','hidden'),
				'cartid' => array(1,$mxuser->cart->id,'hidden'),
				'paymentoption' => array(1,'PayPal','hidden'),
			)
		);
		mx_showform($ckoutlist,$mxuser->cart->orderinfo,true,true);
		//echo print_r($mxuser->cart->orderinfo);

	} else if ($mxuser->cart->progress==4) { 	// order confirmation
		if ($action!='printorder') {
			$ckoutbuttons = array(
				'printorder' => _('Print this Confirmation Page')
			);
		} else $ckoutbuttons=array();
		$cartlist=array(
			'cart' => array(
				'select' => array(0,'','text',3),
				'prodtype' => array(0,_('Item'),'text',20),
				'proddesc' => array(0,_('Description'),'html',30),
				'prodvar' => array(0,_('Type'),'text',20),
				'price' => array(0,_('Price'),'price',10),
			)
		);
		$values=$mxuser->cart->orderconfirm;
		$values['invoicenum']=$mxuser->cart->invoicenum; //sprintf(_('%06d'),$mxuser->cart->id);
		$billing=$mxuser->getaddress($mxuser->cart->billingid);
		$shipping=$mxuser->getaddress($mxuser->cart->shippingid);
		$values['FIRSTNAME']=$billing['first'];
		$values['LASTNAME']=$billing['last'];
		$values['EMAIL']=$billing['email'];
		$values['SHIPTONAME']=$shipping['shiptoname'];
		$values['SHIPTOSTREET']=$shipping['street1'];
		$values['SHIPTOCITY']=$shipping['city'];
		$values['SHIPTOSTATE']=$shipping['state'];
		$values['SHIPTOZIP']=$shipping['zip'];
		$values['SHIPTOCOUNTRYNAME']=$shipping['countrycode'];
		$values['termscond']=mx_showhtmlpagestr('salesterms');
		$ckoutlist=array(
			'checkout',0,_('Order Confirmation'),
			($action!='printorder'?$progresstable:mx_infomsgstr($mxuser->cart->info)),
			$ckoutbuttons,
			array(
				'cart' => array(-1,sprintf(_('Invoice # %s'),$mxuser->cart->invoicenum),
					_('You purchased the following items.')),
				'cartcontent' => array(-2,$cartlist,$prodlist,'cart',array(),'cart'),
				'billing' => array(-1,_('Billing Information'),''),
				'FIRSTNAME' => array(0,_('Firstname'),'text',30),
				'LASTNAME' => array(0,_('Lastname'),'text',30),
				'EMAIL' => array(0,_('Billing E-Mail'),'text',40),
				'shipping' => array(-1,_('Shipping Information'),''),
				'SHIPTONAME' => array(0,_('Ship To'),'text',30),
				'SHIPTOSTREET' => array(0,_('Address'),'text',30),
				'SHIPTOCITY' => array(0,_('City'),'text',20),
				'SHIPTOSTATE' => array(0,_('State/Province'),'text',20),
				'SHIPTOZIP' => array(0,_('Zip Code'),'text',10),
				'SHIPTOCOUNTRYNAME' => array(0,_('Country'),'text',20),
				'confirmation' => array(-1,_('Payment Confirmation'),
					_('Below are the details of the transaction')),
				'invoicenum' => array(0,_('Invoice #'),'text',30),
				'PAYMENTINFO_0_PAYMENTSTATUS' => array(0,_('Payment Status'),'text',20),
				'PAYMENTINFO_0_TRANSACTIONID' => array(0,_('Transaction ID'),'transactionid',20),
				'PAYMENTINFO_0_ORDERTIME' => array(0,_('Order Time'),'text',20),
				'PAYMENTINFO_0_AMT' => array(0,_('Total Amount'),'price',20),
				'PAYMENTINFO_0_TAXAMT' => array(0,_('Taxes included'),'price',20),
				'PAYMENTINFO_0_CURRENCYCODE' => array(0,_('Currency'),'text',20),
				/*'techdetails' => array(-1,_('Technical Details'),
					_('Below is some additional information about the transaction')),
				'PAYMENTINFO_0_FEEAMT' => array(0,_('Paypal Fee'),'text',20),
				'PAYMENTINFO_0_SETTLEAMT' => array(0,_('Final Amount'),'text',20),
				'PAYMENTINFO_0_EXCHANGERATE' => array(0,_('Exchange Rate'),'text',20),
				'PAYMENTINFO_0_PENDINGREASON' => array(0,_('Pending Reason'),'text',20),
				'PAYMENTINFO_0_TRANSACTIONTYPE' => array(0,_('Transaction Type'),'text',20),
				'PAYMENTINFO_0_PAYMENTTYPE' => array(0,_('Payment Type'),'text',20),
				'PAYMENTINFO_0_REASONCODE' => array(0,_('Reason Code'),'text',20),
				'PAYMENTINFO_0_PROTECTIONELIGIBILITY' => array(0,_('Protection Eligibility'),'text',20),
				'PAYMENTINFO_0_ACK' => array(0,_('Payment Ack'),'text',20),
				'INSURANCEOPTIONSELECTED' => array(0,_('Insurance Option Selected'),'text',20),
				'PAYMENTINFO_0_ERRORCODE' => array(0,_('Error Code'),'text',20),
				'SHIPPINGOPTIONISDEFAULT' => array(0,_('Shipping Option is Default'),'text',20),*/
				'terms' => array(-1,_('Sales Terms'),
					_('Please keep the following sales conditions along with your purchase receipt.')),
				'termscond' => array(0,_('Buyer Information'),'html'),
				'a' => array(1,'none','hidden'),
				'PAYERID' => array(1,'none','hidden'),
				'cartid' => array(1,$mxuser->cart->id,'hidden'),
				'paymentoption' => array(1,'PayPal','hidden'),
			)
		);
		//mx_showform($ckoutlist,$mxuser->cart->orderconfirm,true,true);
		mx_showform($ckoutlist,$values,true,true);
		// confirmation emails
		if ($action!='printorder') {
			// email to buyer
			$ckoutemail=$ckoutlist;
			$ckoutemail[4]=array();
			$ckoutemail[3]=mx_infomsgstr($mxuser->cart->info);
			$to=$mxuser->fullname.' <'.$mxuser->email.'>';
			$subj=sprintf(_('Order Confirmation - Invoice # %06d'),$mxuser->cart->id);
			$html=mx_showhtmlpagestr('orderconfirmation');
			$confform=mx_showformstr($ckoutemail,$values,true,true);
			$html=str_replace('{ORDERFORM}', $confform, $html);
			$html=str_replace('{INVOICENUM}', $mxuser->cart->invoicenum, $html); // sprintf('%06d',$mxuser->cart->id)
			$txt=sprintf(_('To print your order confirmation, please go to %s'),mx_actionurl('cart', '', 'printorder','','','secure','cartid='.$mxuser->cart->id));
			mx_sendmail($to,$subj,$txt,$html);
			// email to artists
			foreach ($prodlist['cart'] as $line) {
				if ($prodtype==MXARTSUB) $artistid=$line->prodref;
				else if ($prodtype==MXMEDSUB) {
					$media=$mxuser->getmediainfo($line->prodref);
					$artistid=$media->owner_id;
				} else $artistid=0;
				$prodtype=$line->prodtype;
				$prodvar=$line->prodvar;
				$prodprice=sprintf('US$ %.02f',$line->price);
				if ($artistid) $artist=$mxdb->getuserinfo($mxuser->id,$artistid);
				if ($artist && $artist->email) {
					$to=mx_getartistname($artist).' <'.$artist->email.'>';
					if ($prodtype==MXARTSUB) {
						$subj=_('You just made a new fan');
						// html version
						$html=mx_showhtmlpagestr('newfan');
						// text version
						$txt=_("Hey {ARTISTNAME}!\n\n"
						."We just wanted to give you the good news:\n\n"
						."You just made a new fan in {FANNAME}:\n"
						."{FANNAME} just purchased a {PRODVAR} {PRODTYPE} from you for {PRICE}\n\n"
						."Sales Team,\nMusXpand.\n"
						.MXSALESEMAIL."\n"
						.mx_option('basicsiteurl'));
						mx_fbaction('musxpand:subscribe_to?artist='.urlencode(mx_actionurl('artists', 'artprof', $artistid)));
					}
					else if ($prodtype==MXMEDSUB) {
						$subj=_('Someone bought media from you');
						// html version
						$html=mx_showhtmlpagestr('newbuyer');
						// text version
						$txt=_("Hey {ARTISTNAME}!\n\n"
						."We just wanted to give you the good news:\n\n"
						."{FANNAME} bought some media from you:\n"
						."{FANNAME} just purchased \"{MEDIANAME}\" from you for {PRICE}\n\n"
						."Sales Team,\nMusXpand.\n"
						.MXSALESEMAIL."\n"
						.mx_option('basicsiteurl'));
					}
					$fan='<a href="'.mx_actionurl('fans','fanprof',$mxuser->id).'">'.mx_getname($mxuser).'</a>';
					$html=str_replace('{ARTISTNAME}',mx_getartistname($artist),$html);
					$html=str_replace('{PRICE}',$prodprice,$html);
					$html=str_replace('{FANNAME}',$fan,$html);
					$html=str_replace('{MEDIANAME}',$media->title,$html);
					$html=str_replace('{PRODVAR}',$prodvar,$html);
					$html=str_replace('{PRODTYPE}',$prodtype,$html);
					$siteurl='<a href="'.mx_option('basicsiteurl').'">MusXpand</a>';
					$html=str_replace('{SITEURL}',$siteurl,$html);
					$html=str_replace('{SALESEMAIL}',MXSALESEMAIL,$html);
					$txt=str_replace('{ARTISTNAME}',mx_getartistname($artist),$txt);
					$txt=str_replace('{PRICE}',$prodprice,$txt);
					$fan=mx_getname($mxuser);
					$txt=str_replace('{FANNAME}',$fan,$txt);
					$txt=str_replace('{MEDIANAME}',$media->title,$txt);
					$txt=str_replace('{PRODVAR}',$prodvar,$txt);
					$txt=str_replace('{PRODTYPE}',$prodtype,$txt);
					mx_sendmail($to,$subj,$txt,$html);
				}
			}
		} else { // purchase
		?>

<!-- Google Code for Bought Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 949396365;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "f7QfCNPfzwMQjcfaxAM";
var google_conversion_value = <?php echo $mxuser->cart->total; ?>;
/* ]]> */
</script>
<script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/949396365/?label=f7QfCNPfzwMQjcfaxAM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

		<?php
		}
		/*$ckoutlist[0]='printout';
		echo '<div id="order"><div class="order">';
		mx_showform($ckoutlist,$mxuser->cart->orderinfo,false,true);
		echo '</div></div>';*/
		//echo print_r($mxuser->cart->orderconfirm);

	} else { 	// cart and wishlist display
		$cartbuttons=array(
			'towish' => _('Move to Wishlist'),
			'delcart' => _('Remove Checked Items'),
			'sep' => null,
			'shopmore' => $contshoppinglabel,
			'checkout' => _('Proceed to Checkout')
		);
		$cartlist=array(
			'cartlist',0,_('Cart & Wishlist Content'),
			$progresstable,
			array(
				'cart' => $cartbuttons,
				'wishlist' => array(
					'tocart' => _('Move to Cart'),
					'delwish' => _('Remove Checked Items'),
					'sep' => null,
					'shopmore_w' => $contshoppinglabel,
				)
			),
			array(
				'cart' => array(
					'cart' => array(-1,_('Your Cart'),
						_('The following items are currently in your cart')),
					'select' => array(0,'<input id="checkallbox" type="checkbox" onclick="javascript:checkall(\'cart\');">','text',3),
					'prodtype' => array(0,_('Item'),'text',20),
					'proddesc' => array(0,_('Description'),'html',30),
					'prodvar' => array(0,_('Type'),'text',20),
					'price' => array(0,_('Price'),'price',10),
					'a' => array(1,'none','hidden'),
					'k' => array(1,'cart','hidden')
				),
				'wishlist' => array(
					'wishlist' => array(-1,_('Your Wish List'),
						_('The following items are currently in your wishlist')),
					'select' => array(0,'<input id="checkallbox" type="checkbox" onclick="javascript:checkall(\'wishlist\');">','text',3),
					'prodtype' => array(0,_('Item'),'text',20),
					'proddesc' => array(0,_('Description'),'html',30),
					'prodvar' => array(0,_('Type'),'text',20),
					'price' => array(0,_('Price'),'price',10),
					'a' => array(1,'none','hidden'),
					'k' => array(1,'wishlist','hidden')
				)
			)
		);
		mx_showlist($cartlist,$prodlist,'cart',true,true);
		//if ($action=='addfoy' || $action=='adfofa' || $action=='upgfofa') {
			?>

<!-- Google Code for Added To Cart Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 949396365;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "ziXpCNvezwMQjcfaxAM";
var google_conversion_value = <?php echo $prodprice; ?>;
/* ]]> */
</script>
<script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/949396365/?label=ziXpCNvezwMQjcfaxAM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

			<?php
		//}
	}
	//phpinfo();
}

function mx_confirmcart($userid,$cartid) {
	global $mxdb;
	$mxdb->setcart($userid,$cartid,'paymentstatus','Completed');
	$cartlines=$mxdb->getcartdetails($cartid);
	foreach ($cartlines as $cartline) {
		//if ($cartline->prodtype==MXARTSUB && $cartline->prodvar!=MXSUBFOY) {
			$ppprofileid=$mxdb->setsubpaid($userid,$cartline);
			if ($cartline->prodtype==MXARTSUB && $cartline->prodvar==MXUPGFOFA && $ppprofileid) {
				$mxdb->stoprenewsub($ppprofileid);
				mx_cancelrecurrentpayment($ppprofileid);
			} else if ($cartline->prodtype==MXSITESUB) {
				if (is_array($ppprofileid) && array_key_exists('old',$ppprofileid)) {
					foreach($ppprofileid['old'] as $ppprofid) {
						error_log('canceling renewal '.$ppprofid);
						$mxdb->stoprenewsub($ppprofid);
						mx_cancelrecurrentpayment($ppprofid);
					}
				}
			}
		//}
	}
}

function mx_addsubs($userid,$cartid) {
	global $mxdb;
	$cartlines=$mxdb->getcartdetails($cartid);
	foreach ($cartlines as $cartline) {
		$mxdb->addsubpending($userid,$cartline);
	}
}

function mx_setsubinfo($userid,$cartline,$ppinfo) {
	global $mxdb;
	$mxdb->setsubinfo($userid,$cartline,$ppinfo);
}

function mx_getsubprofile($userid,$cartline,$ppinfo) {
	global $mxdb;
	$mxdb->setsubinfo($userid,$cartline->prodref,$cartline->prodvar,$ppinfo);
}

function mx_setsubrenewal($pppaymentid,$bday) {
	global $mxdb;
	$mxdb->setsubrenewal($pppaymentid,$bday);
}

function mx_confirmsubrenewal($pppaymentid) {
	global $mxdb;
	$mxdb->confirmsubrenewal($pppaymentid);
}

function mx_norenewsub($pppaymentid) {
	global $mxdb;
	$mxdb->norenewsub($pppaymentid);
}

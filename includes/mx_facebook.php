<?php
/* ---
 * Project: musxpand
 * File:    mx_facebook.php
 * Author:  phil
 * Date:    01/10/2010
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
 * source: FB example.php
 */

global $facebook,$me,$FBsession,$mxuser;

require_once 'ext_includes/facebook.php';

// We may or may not have this data based on a $_GET or $_COOKIE based session.
//
// If we get a session here, it means we found a correctly signed session using
// the Application Secret only Facebook and the Application know. We dont know
// if it is still valid until we make an API call using the session. A session
// can become invalid if it has already expired (should not be getting the
// session back in this case) or if the user logged out of Facebook.

function mx_FBinit() {
	global $FBsession,$facebook;
	$page=mx_secureword($_GET['p']);
	$option=mx_secureword($_GET['o']);
	$action=mx_secureword($_REQUEST['a']);
	$section=mx_secureword($_REQUEST['k']);
	$invite=mx_secureword($_GET['i']);
	$redir=mx_secureredir(urldecode($_GET['r']));
	?>
    <div id="fb-root"></div>
    <script>
      window.fbAsyncInit = function() {
        FB.init({
          appId   : '<?php echo $facebook->getAppId(); ?>',
          //session : <?php echo json_encode($FBsession); ?>, // don't refetch the session when PHP already has it
       	  channelURL : '<?php echo mx_option('siteurl').'/fb-channel.php'; ?>',
          status  : false, // check login status
          cookie  : true, // enable cookies to allow the server to access the session
          xfbml   : true, // parse XFBML
          oauth   : true
        });

        FB.Event.subscribe('auth.statusChange', function(response) {
        	//FB.getLoginStatus(function(response) {
			if (response.status === 'connected') {
				//alert('Connected!');
				<?php
	    		if ($option=='signin' || $option=='register') {
	            	echo 'blackout(\''._('Signing you in<br/>using Facebook').'\'); window.location=\''.mx_actionurl($page,$option,'fb','',$redir).'\';';
	            } else {
	            	//echo 'blackout(\''._('Signing you in<br/>using Facebook').'\'); window.location=\''.mx_loginfbredirecturl($page,$option,$action,$section).($invite?('&i='.$invite):'').'\';';
	            }
	            ?>
			} else if (response.status === 'not_authorized') {
        	    // the user is logged in to Facebook,
       		    // but has not authenticated your app
      		} else {
        	    // the user isn't logged in to Facebook.
        	}
        });

        FB.Event.subscribe('auth.authResponseChange', function(response) {
        	if (response.status=='connected') {
               	<?php
				if ($option=='signin' || $option=='register') {
                	echo 'blackout(\''._('Signing you in<br/>using Facebook').'\'); window.location=\''.mx_actionurl($page,$option,'fb','',$redir).'\';';
               	} else {
                	//echo 'blackout(\''._('Signing you in<br/>using Facebook').'\'); window.location=\''.mx_loginfbredirecturl($page,$option,$action,$section).($invite?('&i='.$invite):'').'\';';
               	}
                ?>
         	  } else {
             	  window.location='<?php echo mx_actionurl($page,$option,$action); ?>';
             	  //alert('status='+response.status);
             	  //window.location.reload();
         	  }
          	});

        FB.Event.subscribe('edge.create',
        	    function(response) {
    	    		if (response.indexOf('artists/artprof')>0) {
        	    		art=response.substr(response.lastIndexOf('a=')+2);
        	    		iconclick('il_'+art,'','');
    	    		}
        	    }
        	);

        FB.Event.subscribe('edge.remove',
        	    function(response) {
		    		if (response.indexOf('artists/artprof')>0) {
			    		art=response.substr(response.lastIndexOf('a=')+2);
			    		iconclick('nl_'+art,'','');
		    		}
        	    }
        	);

<?php if ($_GET['canvas'] || $_GET['fbp']) { // resize FB Canvas ?>
        var psize=new Object;
        psize.width=$(document).width();
        psize.height=$(document).height();
        FB.Canvas.setSize(psize);
        FB.Canvas.setAutoGrow(1000);
<?php } ?>
      };

      (function(d){
    	     var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
    	     js = d.createElement('script'); js.id = id; js.async = true;
    	     js.src = "//connect.facebook.net/en_US/all.js";
    	     d.getElementsByTagName('head')[0].appendChild(js);
    	   }(document));
      //(function() {
      //  var e = document.createElement('script');
      //  e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
      //  e.async = true;
      //  document.getElementById('fb-root').appendChild(e);
      //}());

      function addToPage() {
        // calling the API ...
        var obj = {
          method: 'pagetab'
          //redirect_uri: '<?php echo mx_optionurl($page,$option); ?>',
        };

        FB.ui(obj);
      }

    </script>
<?php
}

function mx_checkfblogin($reload=true) {
	global $FBsession,$me,$uid,$facebook,$mxuser;
	if ($me) {
		//error_log('me is defined');
		return;
	}
	//$FBsession = $facebook->getSession();

	$me = null;
    $uid = $facebook->getUser();
    //error_log(print_r($mxuser->fbdata,true));
	/*if (!$uid && $mxuser->fbdata['oauth_token']) {
		$facebook->setAccessToken($mxuser->fbdata['oauth_token']);
	}*/
    /*if (!$uid && $mxuser->fbdata['user_id']) {
    	$uid=$mxuser->fbdata['user_id'];
    	$facebook=new Facebook(array(
		  'appId'  => FACEBOOK_APP_ID,
		  'secret' => FACEBOOK_SECRET,
		  //'cookie' => true,
		));
    	$facebook->setAccessToken($mxuser->fbdata['oauth_token']);
    }*/
	// Session based API call.
	if ($uid) {
	  try {
	    $me = $facebook->api('/me?locale=en_US','GET');
	  } catch (FacebookApiException $e) {
	    error_log($e);
	  }
	  //error_log(print_r($me,true));
	  if ($reload) $mxuser=new MXUser();
	}
}

function mx_fbloginbutton($label,$page,$option,$action,$redir='',$invite='') {
	global $facebook;
	//registration-url="https://developers.facebook.com/docs/plugins/registration"
	if ($facebook->getUser()) {
		try {
			$me = $facebook->api('/me','GET');
			$name=$me['name'];
		} catch (FacebookApiException $e) {
			error_log($e);
			$name=$label;
		}
		//error_log(print_r($me,true));
		if ($me) return //'<a class="loginbutton" onclick="blackout(\''._('Please Wait...').'\');" href="'.mx_actionurl('account','signin','fb','',$redir).($invite?('&i='.$invite):'').'">'
		'<div class="fb-login-button" scope="'.FACEBOOK_PERMS.'" redirect_uri="'.mx_actionurl('account','signin','fb','',$redir).($invite?('&i='.$invite):'').'">'
			//.'<fb:login-button scope="'.FACEBOOK_PERMS.'" >'
			.$name //.'</fb:login-button>'
			.'</div>'
			//</a>'
			//.'<div class="fb-login-button" scope="'.FACEBOOK_PERMS.'" redirect_uri="'.mx_actionurl($page,$option,'fb',$redir).($invite?('&i='.$invite):'').'">'.$name.'</div>'
			.' <a href="'.$facebook->getLogoutUrl().'">'._('Other...').'</a>';
		return /*'<div class="fb-buttons"><fb:login-button scope="'.FACEBOOK_PERMS.'"'.
			' redirect_uri="'.mx_actionurl($page,$option,'fb',$redir).($invite?('&i='.$invite):'').'"></div>'
			.$label.'</fb:login-button>';*/
			//'<a onclick="blackout(\''._('Please Wait...').'\');" href="#">'
			'<div class="fb-login-button" scope="'.FACEBOOK_PERMS.'" redirect_uri="'.mx_actionurl($page,$option,'fb',$redir).($invite?('&i='.$invite):'').'">'.$label.'</div>';
			//.'</a>';
	} else {
		return /* '<div class="fb-buttons"><fb:login-button scope="'.
		FACEBOOK_PERMS.'"'.
		' redirect_uri="'.mx_actionurl($page,$option,'fb',$redir).($invite?('&i='.$invite):'').'">'
		.$label.'</fb:login-button></div>';*/
		//'<a onclick="blackout(\''._('Please Wait...').'\');" href="'.mx_actionurl($page,$option,'fb','','',($_REQUEST['signed_request']?'secure':'normal')).($invite?('&i='.$invite):'').'">'
		'<div class="fb-login-button" scope="'.FACEBOOK_PERMS.'" redirect_uri="'.mx_actionurl($page,$option,'fb','','',($_REQUEST['signed_request']?'secure':'normal')).($invite?('&i='.$invite):'').'">'.$label.'</div>';
		//.'</a>';
	}
	//return '<div class="fb-login-button" data-show-faces="true" data-width="200" data-max-rows="1">'
	//.$label.'</div>';
}

function mx_fbaction($action) {
	global $facebook,$me;
	if (!MXBETA && $facebook->getUser()) {
		try {
			error_log('fb action: /me/'.$action);
			$facebook->api('/me/'.$action,'POST');
		} catch(FacebookApiException $e) {
			// If the user is logged out, you can have a
        	// user ID even though the access token is invalid.
        	// In this case, we'll get an exception, so we'll
        	// just ask the user to login again here.
        	error_log($e->getType());
        	error_log($e->getMessage());
		}
	}
}

function mx_ckfbgoapp($page,$option,$action) {
}

function mx_mnfbgoapp($page,$option,$action) {
}

function mx_mnfblikeus($page,$option,$action) {
	global $mxuser,$facebook;
	echo '<div class="fblikeus">';
	if (!is_pagelike() && !$mxuser->fbdata['user_id']) echo mx_icon('click_like',_('Like us!'));
	else if (!is_pagelike()) echo mx_icon('click_like_registered',_('Like us!'));
	else {
		echo mx_icon('click_liked',_('Like us!')).'<br/><a target=_blank href="'
		.mx_actionurl('account','register','','','','','',true)
		.'">'.mx_icon('click_register',_('Register!')).'</a>';
		?>
		<!--
		<script>
		function checkappstatus() {
			FB.getLoginStatus(function(response) {
				if (response.status === 'connected') {
					window.location='<?php echo mx_pageurl('main'); ?>';
				} else {
					setTimeout('checkappstatus();',2000);
				}
			},true);
		}
		setTimeout('checkappstatus();',2000);
		</script>
		-->
		<?php
	}
	echo '</div>';
	//echo print_r($mxuser,true);
}

$facebook = new Facebook(array(
  'appId'  => FACEBOOK_APP_ID,
  'secret' => FACEBOOK_SECRET,
  //'cookie' => true,
));

$me = null;

?>

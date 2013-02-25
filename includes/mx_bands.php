<?php
/* ---
 * Project: musxpand
 * File:    mx_bands.php
 * Author:  phil
 * Date:    27/10/2010
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

function xxxmx_mnmybands($page,$option,$action) {
	global $mxuser;

	switch ($action) {
		case 'createband': // also serves to link a band account
			$cbartistname=mx_securestring($_REQUEST['artistname']);
			$cbemail=mx_securestring($_REQUEST['email']);
			$pwd=mx_securestring($_REQUEST['password']);
			$pwd2=mx_securestring($_REQUEST['password2']);
			$createstatus=$mxuser->createband($cbartistname,$cbemail,$pwd,$pwd2);
			switch($createstatus) {
				case MXWRONGPWD:
				case MXALREADYLINKED:
				case MXNOPWDMATCH:
					echo mx_warning(sprintf(_('There was an error with your request.<br/>' .
							'Please try %s'),
					'<a href="#createband">'._('again').'</a>'));
					break;
				case MXNOWLINKED:
					echo sprintf(_('This band or artist is now linked to you.' .
							'<br/>Continue to the %s page...'),mx_optionlink('account','mybands'));
					return;
				case MXNOTCREATED:
					echo sprintf(_('We were not able to link this band or artist to your account.' .
							'<br/>Go back to the %s page...'),mx_optionlink('account','mybands'));
					return;
			}
			break;
		case 'modifyband':
			__('Form not yet handled.');
			return;
			break;
		case 'modifyrole':
			$acc_id=mx_secureword($_REQUEST['acc_id']);
			$role=mx_secureword($_REQUEST['role']);
			$role2=mx_secureword($_REQUEST['role2']);
			$role3=mx_secureword($_REQUEST['role3']);
			$mxuser->linkedidroles($acc_id,$role,$role2,$role3);
			break;
		case 'unlinkband':
			$acc_id=mx_secureword($_REQUEST['acc_id']);
			$mxuser->unlinkid($acc_id);
			break;
	}
	$linkedids=$mxuser->getlinkedids();
	if ($linkedids) {
		echo '<div class="form">';
		echo '<table><tr><th>'._('Linked Accounts').'</th></tr>';
		echo '<tr><td class="title">'.sprintf(_('Your linked account are informed just below.' .
		' If you want to create a new band account' .
		' or link an existing account, just go %s%s%s...'),'<a href="#createband">',
		_('there'),'</a>').'</td></tr>';
		echo '<tr><td>';
		//echo '<form name="bands" method="POST">';
		$section=-1;
		for ($i=0; $i<2; $i++) {
			$action=(!$i?'edit':'');
			//foreach ($mxuser->infogroups() as $group => $details) {
			for ($group=0; $group<count($linkedids); $group++) {
				$linkedid=$linkedids[$group];
				$artistname=($linkedid->artistname?$linkedid->artistname:$linkedid->fullname);
				if ($section<0) $section=$group;
				$form=0;
				if ($action=='edit') $form=1;
				if ($form) {
					$edit='';
					$style='form';
				} else {
					$edit='<a href="javascript:tabswitch(\''.$group.'\',\'f_'.$group.'\');"' .
						' alt="'.sprintf(_('Edit %s Information'),$artistname).'">'.mx_icon('edit','',16).'</a>';
					$style='info';
				}
				echo '<div id="'.($form?'f_':'').$group.'" class="'.$style.(($section==$group && !$form)?'':' hidden').'">';
				echo '<table><tr><td>';
				echo '<fieldset>';
				for($grp=0; $grp<count($linkedids); $grp++) {
					if ($grp==$group) echo '<legend class="seltab">'.$edit.' '.$artistname.'</legend>';
					else {
						$othername=($linkedids[$grp]->artistname?$linkedids[$grp]->artistname:$linkedids[$grp]->fullname);
						echo '<legend class="tab"><a href="javascript:tabswitch(\''.$group.'\',\''.$grp.'\');"' .
						' alt="'.sprintf(_('Edit %s Information'),$othername).'">'
							.$othername.'</a></legend>';
					}
				}
				echo '<table class="mybands"><tr><td>';
				echo '<form class="bands" method="POST"><input type="hidden" name="a" value="unlinkband">' .
						'<input type="hidden" name="acc_id" value="'.$linkedid->id.
						'"><input type="submit" value="'._('Unlink this account').'"' .
						' onclick="return confirm(\''.sprintf(_('You are going to' .
								' unlink account [%s].\nAre you sure you want to do that?' .
								'\n\nYou may need' .
								' to ask a new authorization to link back if you' .
								' change your mind later...'),$artistname).'\');">' .
						'</form>';
				$buttons=array(
					'submit' => _('Submit'),
					'clear' => _('Clear')
				);
				$roleform=array(
					'roleinband',0,sprintf(_('Your role in %s'),$artistname),
					_('Your three main functions in the band'),
					$buttons,
					array(
						'role' => array(1,_('Main Role:'),'bandrole'),
						'role2' => array(1,_('2nd Role:'),'bandrole'),
						'role3' => array(1,_('3rd Role:'),'bandrole'),
						'acc_id' => array(1,$linkedid->id,'hidden'),
						'a' => array(1,'modifyrole','hidden')
					)

				);
				$rolevalues=array(
					'role' => $linkedid->role,
					'role2' => $linkedid->role2,
					'role3' => $linkedid->role3,
				);
				mx_showform($roleform,$rolevalues,$form);
				echo '</td></tr><tr><td>';
				//$bandflds=array();
				//$bandvalue=array();
				$fldarray=$mxuser->bandfields();
				foreach($fldarray as $grp => $det) {
					$bandflds[$grp]=array(-1,$det[0]);
					foreach ($det[1] as $fld) {
						$bandflds[$fld]=$mxuser->fielddesc($fld,true); // fld description for band!
						$bandvalue[$fld]=$linkedid->$fld;
					}
					$bandflds['acc_id']=array(1,$linkedid->id,'hidden');
					$bandflds['a']=array(1,'modifyband','hidden');
				}
				$buttons=array(
					'submit' => _('Submit'),
					'clear' => _('Clear')
				);
				$bandinfo=array(
					'bandinfo',0,sprintf(_('Band Information for %s'),$artistname),
					_('Find below the details about this band'),
					$buttons,
					$bandflds
				);
				//echo '<xmp>'.print_r($bandvalue).'</xmp>';
				mx_showform($bandinfo,$bandvalue,$form);
				echo '</td></tr></table>';
				echo '</fieldset>';
				echo '</td></tr>';
				/*if ($form) {
					echo mx_formfield('a','submit','hidden');
					echo '<tr><td class="buttons">';
					echo mx_formfield('submit',_('Submit'),'submit').'&nbsp';
					echo mx_formfield('reset',_('Clear'),'reset');
					echo '</td></tr>';
				}*/
				echo '</table>';
				echo '</div>';
			}
		}
		echo '</td></tr></table>';
		echo '</div>';
	}
	$buttons=array(
		'submit' => _('Submit'),
		'clear' => _('Clear')
	);
	$bandform=array(
		'createband',0,'<a name="createband">'._('Create a Band Account').'</a>',
		_('Please fill some information about the band.'),
		$buttons,
		array(
			'info' => array(-1,_('Hint'),sprintf(_('If you are the only member of a band, you may consider' .
					' instead using your personal account as a band.' .
					' Just inform your artist name' .
					' in %s, and you\'ll be done... '),
						mx_optionlink('account','profile','basic'))),
			'basic' => array(-1,_('Basic Information'),_('The information you' .
					' enter here will be used when you log into the site')),
			'artistname' => array(1,_('Band name:'),'text',40,
				_('When creating a band name, write it the way you want it to appear')),
			'email' => array(1,_('Band Email:'),'text',40),
			'password' => array(1,_('Password:'),'password',20),
			'password2' => array(1,_('Confirm Password:'),'password',20,_('This password' .
					' confirmation is not needed if you\'re just linking to an existing band')),
			'Authorship' => array(-1,_('Legal Bindings'),_('By clicking the box below,' .
					' you hereby certify to the full extends of copyright and trademark' .
					' laws that you fully own the rights' .
					' to use and represent the band name above. You also confirm, that you are aware that' .
					' any false claim is subject to prosecution by the legal copyright and mark' .
					' owner(s) to whom you will directly and exclusively respond.')),
			'agreement'  => array(1,_('I Agree'),'boolean',2,_('You have to agree to continue...')),
			'a' => array(1,'createband','hidden')
		)
	);
	$cbvalues=array(
		'artistname' => $cbartistname,
		'email' => $cbemail,
		'agreement' => 1
 	);
 	switch ($createstatus) {
 		case MXNOPWDMATCH:
 			if ($cbartistname) $fld='artistname';
 			else $fld='email';
 			$cberrors=array(
				'password2' => _('Passwords do not match!')
			);
 			break;
 		case MXWRONGPWD:
 			if ($cbartistname) $fld='artistname';
 			else $fld='email';
 			$cberrors=array(
				'password' => _('Wrong password!'),
				$fld => _('That account exists...'));
 			break;
 		case MXALREADYLINKED:
 			if ($cbartistname) $fld='artistname';
 			else $fld='email';
 			$cberrors=array($fld => _('Account already linked!'));
 			break;
 	}
	mx_showform($bandform,$cbvalues,true,true,$cberrors);
}

?>

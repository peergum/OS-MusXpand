/* ---
 * Project: musxpand
 * File:    musxpand.js
 * Author:  phil
 * Date:    19/10/2010
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

function bgtest(sel) {
	var testimg=document.getElementById('bgpic');
    var bgimg=document.getElementById('bg_'+sel);
    testimg.src=bgimg.src;
}

function pictest(sel,fld) {
	var testimg=document.getElementById('newpic');
    var picimg=document.getElementById('pic_'+sel);
    testimg.src=picimg.src;
    var piclist=document.getElementById('mediapic');
    if (piclist) piclist.checked=true;
    url=picimg.src;
    fname=url.substring(url.lastIndexOf('/')+1);
    if (fname.indexOf('?')>0) fname=fname.substring(0,fname.indexOf('?'));
    piclist.value=fname;
}

function checkform(formname) {
	var theform=document.forms[formname];
	var agree=theform.agreement;
	var submitfld=theform.a;
	if (agree && agree.selectedIndex!=1 && !agree.checked && submitfld && submitfld.value!='mymedia') {
		alert(agreementmsg);
		return false;
	} else return true;
}

function showpro(value) {
	newpro=document.getElementById('newpro');
	if (newpro) {
		if (value==0) {
			newpro.style.display='block';
			$('tr.row_PROmemberid').show();
		}
		else if (value==-1) {
			newpro.style.display='none';
			$('tr.row_PROmemberid').hide();
		}
		else {
			newpro.style.display='none';
			$('tr.row_PROmemberid').show();
		}
	}
}

function tabswitch(oldtab,newtab) {
	var a=document.getElementById(oldtab);
	var c=document.getElementById(newtab);
	var b=document.getElementById("f_"+oldtab);
	a.style.display="none";
	if (b) b.style.display="none";
	var edits=document.getElementsByTagName('div');
	if (edits.length>0) {
		for (i=0;i<edits.length;i++) {
			e=edits[i];
			if (e.className=='edit hidden'||e.className=='edit') e.style.display='none';
		}
	}
	c.style.display="block";
	
}

var helptmr;
var lasthelp;

function showhelp(id,e) {
	if (!e) var e=window.event;
	var id=document.getElementById(id);
	var mcol=document.getElementById('maincolumn');
	/*if (helptmr) {
		clearTimeout(helptmr);
		helptmr=null;
		if (lasthelp) lasthelp.style.display='none';
	}*/
	var formhelper=document.getElementById('formhelper');
	if (formhelper) formhelper.removeChild(formhelper.firstChild);
	var helpcopy=id.cloneNode(true);
	formhelper.appendChild(helpcopy);
	helpcopy.style.left=(e.clientX+window.pageXOffset+10)+'px';
	helpcopy.style.top=(e.clientY+window.pageYOffset-5)+'px';
	//helpcopy.style.left=id.offsetLeft;
	helpcopy.style.display="inline";
	//helpcopy.onmouseout=function() {hidehelp();}
	lasthelp=helpcopy;
	//id.style.display="inline";
	//var pos=getabspos(id);
	//alert('l='+pos[0]+' t='+pos[1]+' objs: '+pos[2]);
	//helptmr=setTimeout('lasthelp.style.display="none";',3000);

}

function hidehelp(e) {
	//var id=document.getElementById(id);
	//helptmr=setTimeout('lasthelp.style.display="none";',500);
	lasthelp.style.display="none";
}

function showmore(id) {
	var infodiv=document.getElementById(id);
	var onemedia=document.getElementById('contentframe');
	var content=document.getElementById('content');
	onemedia.style.display='block';
	e=window.event;
	var overlay=document.getElementById('overlay');
	overlay.style.zIndex='10';
	var infocopy=infodiv.cloneNode(true);
	var mediainfo=onemedia.replaceChild(infocopy,onemedia.childNodes[0]);
	infocopy.style.display='block';
	if (typeof(window.pageYOffset) == "number") scrollY=window.pageYOffset;
	else scrollY=document.body.scrollTop;
	infocopy.style.top=(scrollY-content.style.top)+'px';
	//if (infodiv) infodiv.style.display='block';
}

function showless(id) {
	var onemedia=document.getElementById('contentframe');
	onemedia.style.display=none;
	var infodiv=document.getElementById(id);
	if (infodiv) {
		infodiv.style.display='none';
		pause('track_'+id);
		//player=infodiv.getElementById('track_'+id);
		//player=infodiv.getElementById('audio')[0];
		//if (player) player.pause();
		//player=infodiv.getElementsByTagName('video')[0];
		//if (player) player.pause();
	}
	var overlay=document.getElementById('overlay');
	overlay.style.zIndex='-10';
}

function checkall(frmname) {
	theform=document.forms[frmname];
	var ckall=theform['checkallbox'];
	var flds=theform.getElementsByTagName('input');
	for (i=0;i<flds.length;i++) {
		flds[i].checked=ckall.checked;
	}
}

function checkwalls(frmname) {
	theform=document.forms[frmname];
	var ckall=theform['checkwallsbox'];
	var flds=theform.getElementsByTagName('input');
	for (i=0;i<flds.length;i++) {
		if (flds[i].name=='selwall[]') flds[i].checked=ckall.checked;
	}
}

function shareall(myform) {
	//var shall=myform['filter'];
	var flds=myform.getElementsByTagName('input');
	for (i=0;i<flds.length;i++) {
		if (flds[i].name=='filter[]' && flds[i].value==0) all=flds[i].checked;
		else if (flds[i].name=='filter[]' && flds[i].value>0 && all) flds[i].checked=false;
	}
}

function unshareall(myform) {
	var flds=myform.getElementsByTagName('input');
	var clicked=0;
	for (i=0;i<flds.length;i++) {
		if (flds[i].name=='filter[]' && flds[i].checked) clicked++;
		if (flds[i].name=='filter[]' && flds[i].value==0) checkall=i;
	}
	flds[checkall].checked=(clicked==0);
}

function markread(id) {
	$.ajax({
		  type: "GET",
		  url: "/messages.php?a=markread&m="+id,
		//this data is mendetory when u want post data when posting page by ajax.
		//after that ur data send to that page. now in backend page echo json data.if your //data is in array format the use json_encode() function then echo that json data
		//now you find tht json in your success that can you use by taking a loop.
		  data: '',
		  cache: false,
		  datatype: 'html',
		  success: function(code){
			  if (code=='ok') {
				  $('tr.m_'+id+' td').each(function(index) {
					  $(this).removeClass('newmsg');
				  });
			  }
		  }
	 });
}

var tok;

function hideTD() {
	tok=0;
}

function markdeleted(id) {
	$.ajax({
		  type: "GET",
		  url: "/messages.php?a=markdeleted&m="+id,
		//this data is mendetory when u want post data when posting page by ajax.
		//after that ur data send to that page. now in backend page echo json data.if your //data is in array format the use json_encode() function then echo that json data
		//now you find tht json in your success that can you use by taking a loop.
		  data: '',
		  cache: false,
		  datatype: 'html',
		  success: function(code){
			  if (code=='ok') {
				  $('tr.m_'+id+' td').each(function(index) {
					  $(this).addClass('delmsg');
				  });
			  }
		  }
	 });
}

function reqcancel(id) {
	$.ajax({
		  type: "GET",
		  url: "/messages.php?a=reqcancel&m="+id,
		//this data is mendetory when u want post data when posting page by ajax.
		//after that ur data send to that page. now in backend page echo json data.if your //data is in array format the use json_encode() function then echo that json data
		//now you find tht json in your success that can you use by taking a loop.
		  data: '',
		  cache: false,
		  datatype: 'html',
		  success: function(code){
			  if (code=='ok') {
				  $('tr.m_'+id+' td').each(function(index) {
					  $(this).addClass('canmsg');
				  });
			  }
		  }
	 });
	/*
	var msgrow=document.getElementById('m_'+id);
	var xreq=new XMLHttpRequest();
	if (xreq) {
		xreq.open('GET',siteurl+'/messages.php?a=reqcancel&m='+id,true);
		xreq.onreadystatechange=function()
		{
			if (xreq.readyState==4 && xreq.status==200)
		    {
				if (xreq.responseText=='ok') {
					var msgs=document.getElementsByTagName('tr');
					for (i=0;i<msgs.length;i++) {
						 if (msgs[i].className=='msgline m_'+id) {
							 var mm=msgs[i];
							 for (j=0;j<mm.childNodes.length;j++) {
								 var st=mm.childNodes[j].className;
								 mm.childNodes[j].className=st.replace('msgcell','msgcell canmsg');
							 }
							 //mm.style.opacity='0.2';
							 //mm.style.display='none';
						 }
					}
				} else {
					alert(oopsmessage);
				}
		    }
		}
		xreq.send();
	}
	*/
}

function markarchived(id) {
	$.ajax({
		  type: "GET",
		  url: "/messages.php?a=markarchived&m="+id,
		//this data is mendetory when u want post data when posting page by ajax.
		//after that ur data send to that page. now in backend page echo json data.if your //data is in array format the use json_encode() function then echo that json data
		//now you find tht json in your success that can you use by taking a loop.
		  data: '',
		  cache: false,
		  datatype: 'html',
		  success: function(code){
			  if (code=='ok') {
				  $('tr.m_'+id+' td').each(function(index) {
					  $(this).addClass('archmsg');
				  });
				  $('tr.m_'+id).clone().appendTo('t_archives');
			  }
		  }
	 });
	/*
	var msgrow=document.getElementById('m_'+id);
	var xreq=new XMLHttpRequest();
	if (xreq) {
		xreq.open('GET',siteurl+'/messages.php?a=markarchived&m='+id,true);
		xreq.onreadystatechange=function()
		{
			if (xreq.readyState==4 && xreq.status==200)
		    {
				if (xreq.responseText=='ok') {
					var msgs=document.getElementsByTagName('tr');
					for (i=0;i<msgs.length;i++) {
						 if (msgs[i].className=='msgline m_'+id) {
							 var mm=msgs[i];
							 var nn=mm.cloneNode(true);
							 mm.style.opacity='0.2';
						 }
					}
					var tarch=document.getElementById('t_archives');
					if (tarch) {
						var tarchels=tarch.getElementsByTagName('tr');
						var larch=tarchels[tarchels.length-1];
						var larchp=larch.parentNode;
						larchp.insertBefore(nn,larch);
						for (j=0;j<nn.childNodes.length;j++) {
							var st=nn.childNodes[j].className;
							nn.childNodes[j].className=st.replace('msgcell','msgcell archmsg');
					 	}

					}
				}
		    }
		}
		xreq.send();
	}
	*/
}

function reqaccept(id) {
	var msgrow=document.getElementById('m_'+id);
	var xreq=new XMLHttpRequest();
	if (xreq) {
		xreq.open('GET',siteurl+'/messages.php?a=reqaccept&m='+id,true);
		xreq.onreadystatechange=function()
		{
			if (xreq.readyState==4 && xreq.status==200)
		    {
				if (xreq.responseText=='ok') {
					var msgs=document.getElementsByTagName('tr');
					for (i=0;i<msgs.length;i++) {
						 if (msgs[i].className=='msgline m_'+id) {
							 var mm=msgs[i];
							 for (j=0;j<mm.childNodes.length;j++) {
								 var st=mm.childNodes[j].className;
								 mm.childNodes[j].className=st.replace('msgcell','msgcell accreq');
							 }
							 mm.style.opacity='0.2';
							 //mm.style.display='none';
						 }
					}
				}
		    }
		}
		xreq.send();
	}
}

function reqrecuse(id) {
	var msgrow=document.getElementById('m_'+id);
	var xreq=new XMLHttpRequest();
	if (xreq) {
		xreq.open('GET',siteurl+'/messages.php?a=reqrecuse&m='+id,true);
		xreq.onreadystatechange=function()
		{
			if (xreq.readyState==4 && xreq.status==200)
		    {
				if (xreq.responseText=='ok') {
					var msgs=document.getElementsByTagName('tr');
					for (i=0;i<msgs.length;i++) {
						 if (msgs[i].className=='msgline m_'+id) {
							 var mm=msgs[i];
							 for (j=0;j<mm.childNodes.length;j++) {
								 var st=mm.childNodes[j].className;
								 mm.childNodes[j].className=st.replace('msgcell','msgcell recreq');
							 }
							 mm.style.opacity='0.2';
							 //mm.style.display='none';
						 }
					}
				}
		    }
		}
		xreq.send();
	}
}

function reqignore(id) {
	var msgrow=document.getElementById('m_'+id);
	var xreq=new XMLHttpRequest();
	if (xreq) {
		xreq.open('GET',siteurl+'/messages.php?a=reqignore&m='+id,true);
		xreq.onreadystatechange=function()
		{
			if (xreq.readyState==4 && xreq.status==200)
		    {
				if (xreq.responseText=='ok') {
					var msgs=document.getElementsByTagName('tr');
					for (i=0;i<msgs.length;i++) {
						 if (msgs[i].className=='msgline m_'+id) {
							 var mm=msgs[i];
							 for (j=0;j<mm.childNodes.length;j++) {
								 var st=mm.childNodes[j].className;
								 mm.childNodes[j].className=st.replace('msgcell','msgcell ignreq');
							 }
							 mm.style.opacity='0.2';
							 //mm.style.display='none';
						 }
					}
				}
		    }
		}
		xreq.send();
	}
}


function readcontent(id) {
	$('#'+id).dialog({
		width: 600,
		position: 'center',
		closeOnEscape: true
	});
	markread(id);
	return;
	var contentdiv=document.getElementById(id);
	var contentframe=document.getElementById('contentframe');
	var content=document.getElementById('content');
	contentframe.style.display='block';
	e=window.event;
	var overlay=document.getElementById('overlay');
	overlay.style.zIndex='10';
	overlay.style.display='block';
	var contentcopy=contentdiv.cloneNode(true);
	var contentinfo=contentframe.replaceChild(contentcopy,contentframe.childNodes[0]);
	contentcopy.style.display='block';
	if (typeof(window.pageYOffset) == "number") scrollY=window.pageYOffset;
	else scrollY=document.body.scrollTop;
	contentcopy.style.top=(scrollY-content.style.top)+'px';
	window.onscroll=function() { readcontent(id); };
	if (contentcopy.className=="msgcontent") {
		markread(id);
	}
}

function hidecontent(id) {
	var track=document.getElementById('track_'+id);
	if (track) pause('track_'+id);
	else pause2();
	var contentframe=document.getElementById('contentframe');
	contentframe.style.display='none';
	var overlay=document.getElementById('overlay');
	overlay.style.zIndex='-10';
	overlay.style.display='none';
	var contentdiv=document.getElementById(id);
	if (contentdiv) {
		contentdiv.style.display='none';
	}
	window.onscroll=null;

}

function buttonclick(btnname) {
	var theform;
	if (btnname=='none') return false;
	if (btnname.indexOf('next:')>=0 || btnname.indexOf('previous:')>=0) {
		readcontent(btnname.substr(btnname.indexOf(':',btnname)+1));
		return false;
	}
	if (btnname.indexOf('delete:')>=0) {
		if (!confirm(yousure)) return false;
		var msgid=(btnname.substr(btnname.indexOf(':',btnname)+1));
		markdeleted(msgid);
		hidecontent(btnname.substr(btnname.indexOf(':',btnname)+1));
		return false;
	}
	if (btnname.indexOf('cancel:')>=0) {
		if (!confirm(yousure)) return false;
		var msgid=(btnname.substr(btnname.indexOf(':',btnname)+1));
		reqcancel(msgid);
		hidecontent(btnname.substr(btnname.indexOf(':',btnname)+1));
		return false;
	}
	if (btnname.indexOf('archive:')>=0) {
		var msgid=(btnname.substr(btnname.indexOf(':',btnname)+1));
		markarchived(msgid);
		hidecontent(btnname.substr(btnname.indexOf(':',btnname)+1));
		return false;
	}
	if (btnname.indexOf('accept:')>=0) {
		//if (!confirm(yousure)) return false;
		var msgid=(btnname.substr(btnname.indexOf(':',btnname)+1));
		reqaccept(msgid);
		hidecontent(btnname.substr(btnname.indexOf(':',btnname)+1));
		return false;
	}
	if (btnname.indexOf('recuse:')>=0) {
		if (!confirm(yousure)) return false;
		var msgid=(btnname.substr(btnname.indexOf(':',btnname)+1));
		reqrecuse(msgid);
		hidecontent(btnname.substr(btnname.indexOf(':',btnname)+1));
		return false;
	}
	if (btnname.indexOf('ignore:')>=0) {
		if (!confirm(yousure)) return false;
		var msgid=(btnname.substr(btnname.indexOf(':',btnname)+1));
		reqignore(msgid);
		hidecontent(btnname.substr(btnname.indexOf(':',btnname)+1));
		return false;
	}
	if (btnname.indexOf('reply:')>=0) {
		var msgid=(btnname.substr(btnname.indexOf(':',btnname)+1));
		var wrtofld=document.getElementById('to');
		var wrsubjfld=document.getElementById('subject');
		var wrreffld=document.getElementById('ref');
		var wrbodyfld=document.getElementById('body');
		var wrhistfld=document.getElementById('history');
		var wrhtofld=document.getElementById('h_to');
		var hfromfld=document.getElementById('h_from_'+msgid);
		var tofld=document.getElementById('to_'+msgid);
		var datefld=document.getElementById('date_'+msgid);
		var fromfld=document.getElementById('from_'+msgid);
		var subjfld=document.getElementById('subject_'+msgid);
		var bodyfld=document.getElementById('body_'+msgid);
		wrtofld.value=fromfld.innerHTML;
		wrhtofld.value=hfromfld.innerHTML;
		subj=subjfld.innerHTML;
		ref=subj.indexOf('Re: ');
		if (ref!=0) subj='Re: '+subj;
		wrsubjfld.value=subj;
		body=bodyfld.innerHTML;
		body=bodyfld.innerHTML.replace('\n','');
		sentby=SentByString.replace('%1',fromfld.innerHTML);
		sentby=sentby.replace('%2',datefld.innerHTML);
		wrhistfld.value='---\n'+sentby+'\n'+body.replace(/<br>/gi,'\n');
		hidecontent(msgid);
		tabswitch('inbox','writemsg');
		wrbodyfld.value='';
		wrbodyfld.focus();
		wrbodyfld.select();
		return false;
	}
	if (btnname.indexOf('forward:')>=0) {
		var msgid=(btnname.substr(btnname.indexOf(':',btnname)+1));
		var wrtofld=document.getElementById('to');
		var wrsubjfld=document.getElementById('subject');
		var wrreffld=document.getElementById('ref');
		var wrbodyfld=document.getElementById('body');
		var tofld=document.getElementById('to_'+msgid);
		var datefld=document.getElementById('date_'+msgid);
		var fromfld=document.getElementById('from_'+msgid);
		var subjfld=document.getElementById('subject_'+msgid);
		var bodyfld=document.getElementById('body_'+msgid);
		wrtofld.value='';
		subj=subjfld.innerHTML;
		ref=subj.indexOf('Fw: ');
		if (ref!=0) subj='Fw: '+subj;
		wrsubjfld.value=subj;
		body=bodyfld.innerHTML;
		body=bodyfld.innerHTML.replace('\n','');
		sentby=SentByString.replace('%1',fromfld.innerHTML);
		sentby=sentby.replace('%2',datefld.innerHTML);
		wrbodyfld.value='\n--- '+sentby+'\n'+body.replace(/<br>/gi,'\n');
		hidecontent(msgid);
		tabswitch('messages','writemsg');
		wrtofld.focus();
		wrtofld.select();
		return false;
	}
	if (btnname.indexOf('resend:')>=0) {
		var msgid=(btnname.substr(btnname.indexOf(':',btnname)+1));
		var wrtofld=document.getElementById('to');
		var wrsubjfld=document.getElementById('subject');
		var wrreffld=document.getElementById('ref');
		var wrbodyfld=document.getElementById('body');
		var wrflagsfld=document.getElementById('flags');
		var tofld=document.getElementById('to_'+msgid);
		var wrhtofld=document.getElementById('h_to');
		var htofld=document.getElementById('h_to_'+msgid);
		var datefld=document.getElementById('date_'+msgid);
		var fromfld=document.getElementById('from_'+msgid);
		var subjfld=document.getElementById('subject_'+msgid);
		var bodyfld=document.getElementById('body_'+msgid);
		var flagsfld=document.getElementById('flags_'+msgid);
		wrtofld.value=tofld.innerHTML;
		wrhtofld.value=htofld.innerHTML;
		wrflagsfld.value=flagsfld.innerHTML;
		subj=subjfld.innerHTML;
		wrsubjfld.value=subj;
		body=bodyfld.innerHTML.replace('\n','');
		wrbodyfld.value='\n---\n'+body.replace(/<br>/gi,'\n');
		hidecontent(msgid);
		tabswitch('messages','writemsg');
		wrbodyfld.focus();
		wrbodyfld.select();
		return false;
	}
	if (btnname.indexOf('delete_')>=0
			|| btnname.indexOf('recuse_')>=0
			|| btnname.indexOf('ignore_')>=0) {
		if (!confirm(yousure)) return false;
		selmsg=document.getElementsByName('selmsg[]');
		/*for (i=0;i<selmsg.length;i++) {
			//if (selmsg[i].checked) alert('delete msg #'+selmsg[i].value);
		}*/
		theform=document.forms[btnname.substr(btnname.indexOf('_',btnname)+1)];
		btnname=btnname.substr(0,btnname.indexOf('_',btnname));
	}
	if (btnname.indexOf('archive_')>=0
			|| btnname.indexOf('accept_')>=0) {
		//if (!confirm(yousure)) return false;
		selmsg=document.getElementsByName('selmsg[]');
		/*for (i=0;i<selmsg.length;i++) {
			//if (selmsg[i].checked) alert('archive msg #'+selmsg[i].value);
		}*/
		theform=document.forms[btnname.substr(btnname.indexOf('_',btnname)+1)];
		btnname=btnname.substr(0,btnname.indexOf('_',btnname));
	}
	if (btnname=='clear') { return false; }
	if (btnname=='mymedia') {
		theform=document.forms['mediaform'];
		// no need to check agreement
	}
	if (btnname=='mediaupdate'
		|| btnname=='mediadescupdate'
		|| btnname=='mediapublish') {
		theform=document.forms['mediaform'];
		// check agreement
		if (!checkform('mediaform')) return false;
	}
	if (btnname=='send') {
		hto=document.getElementById('h_to');
		if (hto.value=='') {
			alert(recipientunknown);
			return false;
		}
		theform=document.forms['writemsg'];
	}
	if (btnname=='post') {
		theform=document.forms['updates'];
	}
	if (btnname=='addfoy' || btnname=='addfofa' || btnname=='upgfofa') {
		theform=document.forms['addtocart'];
	}
	if (btnname=='delcart' || btnname=='towish'
		|| btnname=='shopmore' || btnname=='checkout'
		|| btnname=='pp-checkout') {
		// show cart in results page
		theform=document.forms['cart'];
	}
	if ( btnname=='confckout' || btnname=='canckout') {
		theform=document.forms['checkout'];
		if (btnname=='confckout' && !checkform('checkout')) return false;
	}
	if (btnname=='delwish' || btnname=='tocart'
		|| btnname=='shopmore_w') {
		// show wishlist in results page
		theform=document.forms['wishlist'];
	}
	if (btnname=='printorder') {
		//showorder();
		//return false;
		theform=document.forms['checkout'];
		theform.target='_blank';
	}
	if (btnname=='m_editnew' || btnname=='m_publishnew'
		|| btnname=='m_deletenew' || btnname=='upload'
		|| btnname=='newbundle' || btnname=='m_movenew'
		|| btnname=='m_scannew' || btnname=='m_linknew') {
		theform=document.forms['new'];
	}

	if (btnname=='m_editpub' || btnname=='m_archivepub' || btnname=='m_deletepub' || btnname=='m_movepub'
		|| btnname=='m_scanpub' || btnname=='m_linkpub') {
		theform=document.forms['published'];
	}

	if (btnname=='m_deletenew' || btnname=='m_deletepub' || btnname=='m_deletearch'
		|| btnname=='m_scannew' || btnname=='m_scanpub' || btnname=='m_scanarch' || btnname=='m_scanmed') {
		if (!confirm(yousure)) return false;
	}
	
	if (btnname=='m_editarch' || btnname=='m_publisharch' || btnname=='m_deletearch'
		|| btnname=='m_scanarch') {
		theform=document.forms['archived'];
	}
	if (btnname=='m_editmed' || btnname=='m_scanmed') {
		theform=document.forms['allmedia'];
	}
	if (btnname.indexOf('edit_')>=0) {
		theform=document.forms['profile'];
		secname=btnname.substr(btnname.indexOf('_',btnname)+1);
		section=theform['k'];
		if (section) section.value=secname;
		btnname='edit';
	}
	if (btnname.indexOf('setup_')>=0 || btnname=='done') {
		theform=document.forms['setup'];
		if (!checkform('setup')) return false;
	}
	if (btnname=='sendinvites') {
		theform=document.forms['invites'];
	}
	if (btnname=='forgot') {
		theform=document.forms['signin'];
	}
	if (btnname=='signin') {
		theform=document.forms['signin'];
	}
	if (btnname=='update') {
		theform=document.forms['signin'];
	}
	if (btnname=='fbsetup') {
		theform=document.forms['thispage'];
	}
	if (btnname=='fbauthorize' || btnname=='fbaddpage' || btnname=='fbdelpage') {
		theform=document.forms['fbpages'];
	}
	if (btnname=='bundone' || btnname=='bundok') {
		theform=document.forms['bunform'];
	}
	if (btnname=='submit' || btnname=='signin' || btnname=='send') {
		$('div#formhelper').css('position','absolute');
		$scx=$(window).width();
		$scy=$(window).height();
		$dw=$(document).width();
		$dh=$(document).height();
		$screenmask=$('<div class="blackout" />');
		$screenmask.width($dw);
		$screenmask.height($dh);
		$txt=$('<div class="blackouttext">Please wait...</div>');
		var offset=Math.round($dh-$scy/2)+'px';
		$txt.css('margin-top',offset);
		$('div#formhelper div').replaceWith($screenmask);
		$('div#formhelper div').append($txt);
	}
	if (btnname=='sendagain') {
		theform=document.forms['activation'];
	}
	if (btnname=='m_bundle') {
		window.location=siteurl+'/account/bundle';
		return;
	}
	if (btnname=='mystuff') {
		window.location=siteurl+'/account/mystuff';
		return;
	}
	if (btnname=='accdelok' || btnname=='accdelno') {
		theform=document.forms['deleteform'];
	}
	var action;
	if (theform.nodeType==undefined) {
		for(var i=0; i<theform.length; i++) {
			action=theform[i]['a'];
			if (action) action.value=btnname;
		}
	} else {
		action=theform['a'];
		if (action) action.value=btnname;
	}
	return true;
}

function blackout(message) {
	if (message==undefined) message='Please wait...';
	$('div#formhelper').css('position','absolute').css('top','0').css('left','0');
	$scx=$(window).width();
	$scy=$(window).height();
	$dw=$(document).width();
	$dh=$(document).height();
	$screenmask=$('<div class="blackout" />');
	$screenmask.width($dw);
	$screenmask.height(0);
	$txt=$('<div class="blackouttext">'+message+'</div>');
	var offset=Math.round($dh-$scy/2)+'px';
	$txt.css('margin-top',offset);
	$('div#formhelper div').replaceWith($screenmask);
	$('div#formhelper div').animate({
		height: $dh
	},'fast','swing',function() {
		$(this).append($txt);
	});
}

function endblackout() {
	$('div#formhelper div').html('').animate({
		height: 0
	},1000,'swing');
}

var searchtimer;
var searchterm;

function quicksearch(e,id,fld) {
	var key;
	if (!e) var e=window.event;
	if (e.keyCode) key=e.keyCode;
	else key=e.which;
	//alert ('type:'+e.type+' target:'+e.target.name);
	var divelem=document.getElementById(fld+'_search');
	if (searchtimer) {
		clearTimeout(searchtimer);
	}
	var tr=divelem.getElementsByTagName('tr');
	if (key==40) {
		if (!tr) return;
		for (i=0;i<tr.length;i++) {
			if (tr[i].className=='selected' && i<(tr.length)-1) {
				tr[i+1].className='selected';
				tr[i].className='';
				break;
			}
		}
		if (i>=tr.length) {
			tr[0].className='selected';
			if (i>0) tr[i-1].className='';
		}
		return;
	}
	if (key==38) {
		if (!tr) return;
		for (i=0;i<tr.length;i++) {
			if (tr[i].className=='selected' && i>0) {
				tr[i-1].className='selected';
				tr[i].className='';
				break;
			}
		}
		if (i>=tr.length) {
			tr[i-1].className='selected';
			tr[0].className='';
		}
		return;
	}
	if (key==13 || key==9 || e.type=='blur') {
		if (!tr) return;
		for (i=0;i<tr.length;i++) {
			if (tr[i].className=='selected')
					break;
		}
		//alert(i);
		if (i<tr.length) {
			var name=document.getElementById('n_'+tr[i].id);
			setfield(fld,tr[i].id,name.value);
			var idicon=document.getElementById(fld+'_icon');
			if (idicon) idicon.style.display='inline';
		}
		divelem.innerHTML='';
		divelem.style.display='none';
		return false;
	}
	if (key==27) {
		if (!tr) return;
		for (i=0;i<tr.length;i++) {
			tr[i].className=='';
		}
		divelem.style.display='none';
		return;
	}
	var idelem=document.getElementById('h_'+fld);
	if (idelem) idelem.value='';
	var idicon=document.getElementById(fld+'_icon');
	if (idicon) idicon.style.display='none';
	var searchtimer=setTimeout(function() {quickresult(id,fld);},50);
}

function quickresult(id,fld) {
	clearTimeout(searchtimer);
	if (searchterm==id.value) {
		return;
	}
	searchterm=id.value;
	var divelem=document.getElementById(fld+'_search');
	if (searchterm.length==0) {
		divelem.innerHTML='';
		divelem.style.display='none';
		var idicon=document.getElementById(fld+'_icon');
		if (idicon) idicon.style.display='none';
		return;
	}
	var xreq=new XMLHttpRequest();
	if (xreq) {
		xreq.open('GET',siteurl+'/search.php?q='+searchterm+'&f='+fld,true);
		xreq.onreadystatechange=function()
		  {
		  if (xreq.readyState==4 && xreq.status==200)
		    {
			  divelem.innerHTML=xreq.responseText;
			  divelem.style.display='block';
		    }
		  }
		xreq.send();
	}
}

function clearresult(fld) {
	var divelem=document.getElementById(fld+'_search');
	divelem.innerHTML='';
	divelem.style.display='none';
}

function unselresult(elem) {
	elem.className='';
}

function selresult(elem) {
	tab=elem.parentNode;
	tr=tab.getElementsByTagName('tr');
	for (i=0;i<tr.length;i++) {
		if (tr[i]!=elem) tr[i].className='';
		else tr[i].className='selected';
	}
}

function clickresult(fld,id,name) {
	e=window.event;
	e.cancelBubble=true;
	var divelem=document.getElementById(fld+'_search');
	var tr=divelem.getElementsByTagName('tr');
	if (tr) {
		for (i=0;i<tr.length;i++) {
			if (tr[i].id==id) tr[i].className='selected';
			else if (tr[i].className=='selected') tr[i].className='';
		}
	}
	setfield(fld,id,name);
}

function setfield(fld,id,name) {
	//alert(fld+'+'+id+'+'+name);
	var nameelem=document.getElementById(fld);
	var idelem=document.getElementById('h_'+fld);
	nameelem.value=name;
	if (idelem) idelem.value=id;
	clearresult(fld);
	var idicon=document.getElementById(fld+'_icon');
	if (idicon) idicon.style.display='inline';
	if (fld=='q') {
		qform=nameelem.form;
		qform.submit();
	}
}

function showbutton(id,e) {
	var btn=document.getElementById(id);
	if (btn) btn.style.display='block';
}

function hidebutton(id,e) {
	var btn=document.getElementById(id);
	if (btn) btn.style.display='none';
}

function showwindow(win) {
	var windiv=document.getElementById(win);
	if (windiv) {
		var onemedia=document.getElementById('contentframe');
		var content=document.getElementById('content');
		onemedia.style.display='block';
		e=window.event;
		var overlay=document.getElementById('overlay');
		overlay.style.zIndex='10';
		var infocopy=windiv.cloneNode(true);
		var mediainfo=onemedia.replaceChild(infocopy,onemedia.childNodes[0]);
		infocopy.style.display='block';
		if (typeof(window.pageYOffset) == "number") scrollY=window.pageYOffset;
		else scrollY=document.body.scrollTop;
		infocopy.style.top=Math.max(0,Math.min(scrollY,content.offsetHeight-infocopy.offsetHeight))+'px';
		//windiv.style.display='block';
		//pn=windiv.parentNode;
		//alert('top='+window.screenTop);
		//windiv.style.top=window.pageYOffset-window.screenTop+'px';
		window.onscroll=function() { showwindow(win); };
		window.onkeydown=function(event) { if (event.keyCode==27) hidewindow(win); }
	}
}

function hidewindow(win) {
	var onemedia=document.getElementById('contentframe');
	if (onemedia) {
		onemedia.style.display='none';
		var windiv=document.getElementById(win);
		if (windiv) {
			windiv.style.display='none';
			//pause('track_'+id);
			//player=infodiv.getElementById('track_'+id);
			//player=infodiv.getElementById('audio')[0];
			//if (player) player.pause();
			//player=infodiv.getElementsByTagName('video')[0];
			//if (player) player.pause();
		}
		var overlay=document.getElementById('overlay');
		overlay.style.zIndex='-10';
	}
	window.onscroll=null;

}

function warnurl(desc,url) {
	return confirm(leavingsitemsg+'\n'+descmsg+' '+desc+'\n'+linkmsg+' '+url+'\n'+okcancelmsg);
}

function mxpluslike(jsonParam) {
	href=jsonParam.href;
	like=jsonParam.state;
	if (href.indexOf('artists/artprof')>0) {
		art=href.substr(href.lastIndexOf('a=')+2);
		if (like=='on')
			iconclick('il_'+art,'','');
		else
			iconclick('nl_'+art,'','');
	}
	
}

function removelink(lid,mid) {
	var xreq=new XMLHttpRequest();
	if (xreq) {
		xreq.open('GET',siteurl+'/links.php?m='+mid+'&l='+lid,true);
		xreq.onreadystatechange=function()
		  {
		  if (xreq.readyState==4 && xreq.status==200)
		    {
		    }
		  }
		xreq.send();
	}		
}

function iconclick(id,norm,pressed) {
	var objtype,objid;
	var prf=id.substr(0,id.indexOf('_'));
	var mid=id.substr(id.indexOf('_')+1);
	var bdiv=document.getElementById(id);
	var newicon='';
	if (prf=='ul') {
		if (confirm(yousure)) {
			$('.ld_'+mid).empty();
			var lid=mid.substr(mid.indexOf('_')+1);
			mid=mid.substr(0,mid.indexOf('_'));
			removelink(lid,mid);
		}
	}
	if (prf=='im') {
		$('.md_'+mid).toggle('slow');
		/*
		div=document.getElementById('md_'+mid);
		if (div) {
			if (div.style.display=='block') {
				div.style.display='none';
				newicon=norm;
			} else {
				div.style.display='block';
				newicon=pressed;
			}
		}
		*/
	}
	if (prf=='lm') {
		$('.ld_'+mid).toggle('slow');
		/*
		div=document.getElementById('md_'+mid);
		if (div) {
			if (div.style.display=='block') {
				div.style.display='none';
				newicon=norm;
			} else {
				div.style.display='block';
				newicon=pressed;
			}
		}
		*/
	}
	if (prf=='pm') {
		if (bdiv.src.indexOf(norm+'.png')==-1) newicon=norm;
		else newicon=pressed;
		playPause('track_'+mid);
	}
	if (prf=='dm') {
		if (bdiv.src.indexOf(norm+'.png')==-1) newicon=norm;
		else newicon=pressed;
		alert('available soon');
	}
	if (prf=='am') {
		if (bdiv.src.indexOf(norm+'.png')==-1) newicon=norm;
		else newicon=pressed;
		//alert('available soon');
	}
	if (prf=='il') {
		var norm='ilove';
		var pressed='ilovehover';
		$('.'+id).each(function(index) {
			$(this).replaceWith('<img class="nl_'+mid+'"/>');
		});
		$('.nl_'+mid).each(function(index) {
			$(this).attr('src',iconsurl+norm+'.png')
			.click(function() {
				iconclick('nl_'+mid,'','');
			})
			.mouseover(function() {
				this.src=iconsurl+pressed+'.png';
			})
			.mouseout(function() {
				this.src=iconsurl+norm+'.png';
			});
		});
		updatelikes(mid,1);
		return;
	}
	if (prf=='nl') {
		var norm='nolove';
		var pressed='nolovehover';
		$('.'+id).each(function(index) {
			$(this).replaceWith('<img class="il_'+mid+'"/>');
		});
		$('.il_'+mid).each(function() {
			$(this).attr('src',iconsurl+norm+'.png')
			.click(function() {
				iconclick('il_'+mid,'','');
			})
			.mouseover(function() {
				this.src=iconsurl+pressed+'.png';
			})
			.mouseout(function() {
				this.src=iconsurl+norm+'.png';
			});
		});
		updatelikes(mid,0);
		return;
	}
	if (prf=='ob') {
		if (!$('#new').hasClass('hidden')) section='new';
		else if (!$('#published').hasClass('hidden')) section='published';
		else if (!$('#archived').hasClass('hidden')) section='archived';
		else section='media';
		var ob=$('div#'+section).find('#ob_'+mid);
		var cb=$('div#'+section).find('#cb_'+mid);
		$(ob).hide();
		$(cb).show();
		return;
	}
	if (prf=='cb') {
		if (!$('#new').hasClass('hidden')) section='new';
		else if (!$('#published').hasClass('hidden')) section='published';
		else if (!$('#archived').hasClass('hidden')) section='archived';
		else section='media';
		var ob=$('div#'+section).find('#ob_'+mid);
		var cb=$('div#'+section).find('#cb_'+mid);
		$(cb).hide();
		$(ob).show();
		return;
	}
	if (prf=='sh') {
		$('div.mxshare').each(function(index) {
			$(this).fadeOut('slow');
		});
		$('div.share_'+mid).css('display','inline').fadeIn('slow');
		window.onkeydown=function(event) { if (event.keyCode==27) {
				$('div.share_'+mid).fadeOut('slow');
				window.onkeydown='';
			}
		}
	}
	if (prf=='ush') {
		$('div.share_'+mid).fadeOut('slow');
	}
	if (prf=='bs') {
		window.location=siteurl+'/cart?a='+mid;
	}
	if (prf=='act') {
		var tag=$('div.dropmenu').attr('tag');
		if (tag) {
			var sep=tag.indexOf(':');
			if (sep>0) {
				objtype=tag.substr(0,sep);
				objid=tag.substr(sep+1);
			} else {
				objtype=tag;
				objid='';
			}
		} else {
			objtype=objid='';
		}
		switch(mid) {
			case 'enterdrop':
				window.location=siteurl+'/account/signin';
				break;
			case 'exitdrop':
				window.location=siteurl+'/account/signoff';
				break;
			case 'artsdrop':
				window.location=siteurl+'/artists/artsdir';
				break;
			case 'fansdrop':
				window.location=siteurl+'/fans/fandir';
				break;
			case 'setupdrop':
				window.location=siteurl+'/account/profile';
				break;
			case 'cartdrop':
				//if (objtype=='a')
				window.location=siteurl+'/cart';
				//else nosuchaction();
				break;
			case 'blogdrop':
				if (objtype=='p') window.location=siteurl+'/account/wall';
				else if (objtype=='a'||objtype=='f') window.location=siteurl+'/'+objtype+'/'+objid+'?k=WALL';
				else nosuchaction();
				break;
			case 'maildrop':
				if (objtype=='a'||objtype=='f')
					window.location=siteurl+'/account/messages'+(tag?('/sm:'+objid+'/writemsg'):'');
				else window.location=siteurl+'/account/messages';
				break;
			case 'frienddrop':
				if (objtype=='a'||objtype=='f')
					window.location=siteurl+'/account/messages/af:'+objid+'/writemsg';
				else window.location=siteurl+'/account/friends';
				break;
			case 'mediadrop':
				window.location=siteurl+'/account/mystuff';
				//else nosuchaction();
				break;
			case 'infodrop':
				if (mediaid) {
					objtype='m';
					objid=mediaid;
					window.location=siteurl+'/m/'+objid;
				} else if (objtype=='a'||objtype=='f')
					window.location=siteurl+'/'+objtype+'/'+objid+'?k=GENERAL';
				else window.location=siteurl+'/help/musxpand';
				break;
			case 'sharedrop':
				if (mediaid) {
					objtype='m';
					objid=mediaid;
				}
				if (objtype=='p') objtype='a'; //window.location=siteurl+'/account/invites';
				if (objtype=='l') {
					FB.ui(
					  {
					    method: 'feed',
					    name: 'MusXpand',
					    link: 'http://www.musxpand.com'
					    //picture: 'http://www.musxpand.com/',
					    //caption: 'Bringing Musicians and Fans together',
					    //description: 'MusXpand is the next step in the Music Business, filling the gap between the artists and theirs fans.'
					  },
					  function(response) {
					  }
					);
				} else if (objtype=='a' || objtype=='m') {
					FB.ui(
					  {
					    method: 'feed',
					    link: siteurl+'/'+objtype+'/'+objid
					  },
					  function(response) {
					  }
					);
				} else nosuchaction();
				break;
			case 'playdrop':
				if (objtype=='a') $(window).ready(function() {openbundle(0,0);}); //window.location=siteurl+'/a/'+objid+'?z=1';
				else if (objtype=='m') window.location=siteurl+'/m/'+objid+'?z=1';
				else if (objtype=='p') alert('Your playlist will start playing when you click here in a near future...');
				else nosuchaction();
				break;
			case 'writedrop': // reviews
				if (objtype=='a') window.location=siteurl+'/a/'+objid+'?k=REVIEWS';
				else nosuchaction();
				break;
			case 'lovedrop':
				if (objtype=='p' || !objid) window.location=siteurl+'/account/mysubs';
				break;
			case 'plusdrop':
			default:
				nosuchaction();
				break;
		}
	}
	if (bdiv && newicon) {
		var nbutt=new Image();
		nbutt.src=iconsurl+newicon+'.png';
		bdiv.src=nbutt.src;
	}
}

function nosuchaction() {
	alert('Drag and drop something on this button...');
}

function download(id) {
	var media=$('#mediadl_'+id+':first');
	var mediasrc=media.attr('href');
	//alert('coming soon...');
	window.location.assign(mediasrc);
}

function openbundles(id) {
	var section;
	if ($('div#MEDIA').length) section='media';
	else if ($('div#allmedia').css('display')!='none') section='allmedia';
	else if ($('div#new').css('display')!='none') section='new';
	else if ($('div#published').css('display')!='none') section='published';
	else if ($('div#archived').css('display')!='none') section='archived';
	var oa=$('table#t_'+section).find('#oa_'+id);
	var wa=$('table#t_'+section).find('#wa_'+id);
    $(oa).hide();
    $(wa).show();
    var $obs=$('table#t_'+section).find('[id^="ob_"]').each(function() {
    	if ($(this).css('display')=='inline') {
    		bid=$(this).attr('id');
    		bid=bid.substr(bid.indexOf('_')+1);
    		openbundle(bid);
    	}
    });
    $(wa).hide();
    $(oa).show();
}

function closebundles(id) {
	var section;
	if ($('div#MEDIA').length) section='media';
	else if ($('div#allmedia').css('display')!='none') section='allmedia';
	else if ($('div#new').css('display')!='none') section='new';
	else if ($('div#published').css('display')!='none') section='published';
	else if ($('div#archived').css('display')!='none') section='archived';
	var ca=$('table#t_'+section).find('#ca_'+id);
	var wa=$('table#t_'+section).find('#wa_'+id);
    $(ca).hide();
    $(wa).show();
    var $obs=$('table#t_'+section).find('[id^="cb_"]').each(function() {
    	if ($(this).css('display')=='inline') {
    		bid=$(this).attr('id');
    		bid=bid.substr(bid.indexOf('_')+1);
    		closebundle(bid);
    	}
    });
    $(wa).hide();
    $(ca).show();
}

function openbundle(id, playid, newbun) {
	if (newbun==undefined) newbun=false;
	if (playid==undefined || newbun) var playid=-1;
	//if (playid>=0) blackout('just one second, the music is coming...');
	var section;
	closebundle(id,newbun);
	//$('tr#bundlelist').hide();
	if ($('table#t_pubmed').length) section='pubmed';
	else if ($('table#t_membmed').length) section='membmed';
	else if ($('table#t_fanmed').length) section='fanmed';
	else if ($('table#t_media').length) section='media';
	else if ($('table#t_newbun').length) section='newbun';
	else if ($('div#allmedia').css('display')!='none') section='allmedia';
	else if ($('div#new').css('display')!='none') section='new';
	else if ($('div#published').css('display')!='none') section='published';
	else if ($('div#archived').css('display')!='none') section='archived';
	// close all (not working)
	if (section=='pubmed' || section=='membmed' || section=='fanmed' || section=='media') {
		$('table#t_'+section).find('tr.pubmed').each(function(index) {
			$(this).hide();
		});
	}
	//
	if (!id) id=$('table#t_'+section).find('tr[tag]').attr('tag');
	if (!id) return;
	var brow=$('tr.brow_'+id);
	$('tr#bundledetails').show();
	brow.show();
	var oldheadrow=$('table#t_'+section+' tr.headrow:visible');
	var newheadrow=brow.prevAll('tr.headrow').first();
	newheadrow.show();
	//if (oldheadrow.length) {
	if (!oldheadrow.is(newheadrow)) oldheadrow.hide();
	/*&& newheadrow.length) {
			oldheadrow.fadeOut('slow',function() {
				newheadrow.fadeIn('slow');
			});
		}*/
	var ob=$('table#t_'+section).find('#ob_'+id);
	var wb=$('table#t_'+section).find('#wb_'+id);
	var cb=$('table#t_'+section).find('#cb_'+id);
    if (ob.length) $(ob).hide();
    if (wb.length) $(wb).show();
	bundledmedia=$('table#t_'+section).find('tr[tag="'+id+'"]').find('tr.bundled');
	if (bundledmedia.length && section!='newbun' && section!='new') {
		bundledmedia.each(function(index) {
			$(this).show();
		});
		if (wb.length) $(wb).hide();
		if (cb.length) $(cb).show();
		if (playid>=0) { 
			//endblackout();
			play(playid);
		}
	} else {
	    var simul=$('div.simulation').length;
		var xreq=new XMLHttpRequest();
		if (xreq) {
			xreq.open('GET',siteurl+'/bundle.php?b='+id+'&k='+section+'&pub='+simul,true);
			xreq.onreadystatechange=function()
			  {
			  if (xreq.readyState==4 && xreq.status==200)
			    {
				  //rows='<tr><td colspan=6>test</td></tr>';
				  var rows=xreq.responseText;
				  rows=rows.replace(/pubmed/igm,'pubmed bundled bun_'+id);
				  var row=rows.match(/<tr([^\/]|\/[^t]|\/t[^r])+\/tr>/gim);
				  var bunrow=$('table#t_'+section).find('#row_'+id);
				  for (i=0;i<row.length;i++) {
					  row[i]=row[i].replace(/input_./gim,'bundled');
					  row[i]=row[i].replace(/\{PRICE\}/igm,'<table class="buymedia"><tr><td>');
					  row[i]=row[i].replace(/\{PRICE2\}/igm,'</td><td>');
					  row[i]=row[i].replace(/\{PRICE3\}/igm,'</td></tr></table>');
					  bunrow.after(row[i]).show();
					  bunrow=$('table#t_'+section+' tr.bun_'+id).last();
					  bunrow.find('.dragpic').first().draggable({
						  helper:"clone"
					  });
					  bunrow.find('.droppic').first().droppable({
							accept:".dragpic",
							activeClass: "ui-state-hover",
							hoverClass: "ui-state-active",
							drop: function( event, ui ) {
								var uisrc=ui.draggable.attr("src");
								$( this ).attr("src",uisrc);
								var picid=ui.draggable.attr("tag");
								var mediaid=$(this).attr("tag");
								setmediapic(picid,mediaid);
								//alert('picid='+picid+' mediaid='+mediaid);
							}
					  });
					  if (bunrow.hasClass('dragmedia')) {
						  if (newbun)
							  bunrow.addClass('newmedia');
						  else
							  bunrow.addClass('workmedia');
						  bunrow.addClass('ui-state-default');
				  	  }
				  }
				  $('table#t_'+section).find('tr[tag="'+id+'"]').find('tr.bundled').each(function(index) {
						$(this).show();
					});
				  var wb=$('table#t_'+section).find('#wb_'+id);
				  var cb=$('table#t_'+section).find('#cb_'+id);
				  if (wb.length) $(wb).hide();
				  if (cb.length) $(cb).show();
				  if (FB) FB.XFBML.parse();
				  gapi.plusone.go();
				  if ($('div.dropmenu').length) setdrops();
				  if (playid>=0) {
					  //endblackout();
					  play(playid);
				  }
				  else {
					  //window.location='#bdetails_'+id;
				  }	
			    }
			  }
			xreq.send();
		}	
	}
	if (!newbun) {
		var dt=new Date();
		FB.api('/me/musxpand:examine','POST',
			{
				bundle: siteurl+'/m/'+id,
				start_time: dt.toISOString(),
				expires_in: 300
			},function(response){
				//console.info(response);
			});			
	}
	return false;
}

function closebundle(id,newbun) {
	if (newbun==undefined) newbun=false;
	var section;
	if ($('table#t_pubmed').length) section='pubmed';
	else if ($('table#t_membmed').length) section='membmed';
	else if ($('table#t_fanmed').length) section='fanmed';
	else if ($('table#t_media').length) section='media';
	else if ($('table#t_newbun').length) section='newbun';
	else if ($('div#allmedia').css('display')!='none') section='allmedia';
	else if ($('div#new').css('display')!='none') section='new';
	else if ($('div#published').css('display')!='none') section='published';
	else if ($('div#archived').css('display')!='none') section='archived';
	var ob=$('table#t_'+section).find('#ob_'+id);
	var cb=$('table#t_'+section).find('#cb_'+id);
	var brow=$('tr.brow_'+id);
	brow.hide();
	brow.prevAll('tr.headrow').first().hide();
	bundledmedia=$('table#t_'+section).find('tr[tag="'+id+'"]').find('tr.bundled');
	if (bundledmedia.length>0) {
		bundledmedia.hide();		
		if (section=='newbun'||section=='new') bundledmedia.remove();
		//$('table#t_'+section).find('tr.bun_'+id).remove();
		if (!newbun) {
			var dt=new Date();
			FB.api('/me/musxpand:examine','POST',
				{
					bundle: siteurl+'/m/'+id,
					stop_time: dt.toISOString()
				},function(response){
					//console.info(response);
				});
		}
	}
	$('tr#bundledetails').hide();
	$('tr#bundlelist').show();
	$(cb).hide();
	$(ob).show();
	//window.location='#medialist';
}

function updatelikes(id,like) {
	var xreq=new XMLHttpRequest();
	if (xreq) {
		xreq.open('GET',siteurl+'/fanlikes.php?i='+id+'&l='+like,true);
		xreq.onreadystatechange=function()
		  {
		  if (xreq.readyState==4 && xreq.status==200)
		    {
			  eval(xreq.responseText);
			  /*
			  tnumlikes=document.getElementsByName('ln_'+id); // likes
			  tnumlikes2=document.getElementsByName('dn_'+id); // dislikes
			  for (i=0; i<tnumlikes.length; i++) {
				  numlikes=tnumlikes[i];
				  numlikes.innerHTML=totlikes;
			  }
			  for (i=0; i<tnumlikes2.length; i++) {
				  numlikes2=tnumlikes2[i];
				  numlikes2.innerHTML=totdislikes;
			  }
			  likeshow(id,uid,prf,sel);
			  */
		    }
		  }
		xreq.send();
	}
}

function likeclick(id,uid,prf,sel) {
	var xreq=new XMLHttpRequest();
	if (xreq) {
		xreq.open('GET',siteurl+'/walls.php?m='+id+'&l='+(prf=='l'?1:0)+'&d='+(prf=='d'?1:0),true);
		xreq.onreadystatechange=function()
		  {
		  if (xreq.readyState==4 && xreq.status==200)
		    {
			  eval(xreq.responseText);
			  var tnumlikes=document.getElementsByName('ln_'+id); // likes
			  var tnumlikes2=document.getElementsByName('dn_'+id); // dislikes
			  for (i=0; i<tnumlikes.length; i++) {
				  numlikes=tnumlikes[i];
				  numlikes.innerHTML=totlikes;
			  }
			  for (i=0; i<tnumlikes2.length; i++) {
				  numlikes2=tnumlikes2[i];
				  numlikes2.innerHTML=totdislikes;
			  }
			  likeshow(id,uid,prf,sel);
		    }
		  }
		xreq.send();
	}
}

var newlike=new Array();
var newdislike=new Array();
var oldlike=new Array();
var olddislike=new Array();

function likeshow(id,uid,prf,sel) {
	var like='likes.png';
	var dislike='dislikes.png';
	var likeicon=iconsurl+like;
	var nolikeicon=iconsurl+'no'+like;
	var dislikeicon=iconsurl+dislike;
	var nodislikeicon=iconsurl+'no'+dislike;
	if (prf=='l') { // clicked like
		var ls=1;
		newdislike=nodislikeicon;
		olddislike=dislikeicon;
		var d=0;
		var nd=0;
		switch(sel) {
			case 0: // was nothing -> like
				newlike=likeicon;
				oldlike=nolikeicon;
				var l=1;
				var nl=0;
				var ds=0;
				break;
			case 2: // was disliked -> undislike + like
				newlike=likeicon;
				oldlike=nolikeicon;
				var l=1;
				var nl=0;
				var ds=1;
				break;
			case 1: // was liked -> unlike
				newlike=nolikeicon;
				oldlike=likeicon;
				var l=0;
				var nl=1;
				var ds=0;
				break;
		}
	} else { // click on dislike
		newlike=nolikeicon;
		oldlike=likeicon;
		var l=0;
		var nl=0;
		var ds=1;
		switch(sel) {
			case 0: // was nothing -> dislike
				newdislike=dislikeicon;
				olddislike=nodislikeicon;
				var d=1;
				var nd=0;
				var ls=0;
				break;
			case 1: // was liked -> unlike + dislike
				newdislike=dislikeicon;
				olddislike=nodislikeicon;
				var d=1;
				var nd=0;
				var ls=1;
				break;
			case 2: // was disliked -> undislike
				newdislike=nodislikeicon;
				olddislike=dislikeicon;
				var d=0;
				var nd=1;
				var ls=0;
				break;
		}
	}
	var nsel=l+2*d;
	var nnsel=nl+2*nd;
	//alert('sel='+sel+' nsel='+nsel+' ls='+ls+' ds='+ds);
	//if (ls) {
		var ticons=document.getElementsByName('li_'+id);
		var icon;
		for (i=0; i<ticons.length; i++) {
			icon=ticons[i];
			icon.onmouseover='this.src=loadimg("'+oldlike+'");';
			icon.onmouseout='this.src=loadimg("'+newlike+'");';
			icon.onclick=function() {likeclick(id,uid,'l',nsel)};
			icon.src=newlike;
		}
	//}
	var ticons2=document.getElementsByName('di_'+id);
	//if (ds) {
		var icon2;
		for (i=0; i<ticons2.length; i++) {
			icon2=ticons2[i];
			icon2.onmouseover='this.src=loadimg("'+olddislike+'");';
			icon2.onmouseout='this.src=loadimg("'+newdislike+'");';
			icon2.onclick=function() {likeclick(id,uid,'d',nsel)};
			icon2.src=newdislike;
		}
	//}
}

function loadimg(url) {
	var img=new Image();
	img.src=url;
	return img.src;
}

function switchcomments(section,msgid) {
	var divid=document.getElementById(section+'cm_'+msgid);
	if (divid) {
		if (divid.style.display!='inline') {
			divid.style.display='inline';
			getcomments(section,msgid,divid);
		} else divid.style.display='none';
	}
}

function getcomments(section,msgid,divid) {
	var xreq=new XMLHttpRequest();
	if (xreq) {
		xreq.open('GET',siteurl+'/walls.php?m='+msgid+'&k='+section,true);
		xreq.onreadystatechange=function()
		  {
		  if (xreq.readyState==4 && xreq.status==200)
		    {
			  divid.innerHTML=xreq.responseText;
			  //divid.style.display='inline';
		    }
		  }
		xreq.send();
	}
}

function deletewall(msgid) {
	var xreq=new XMLHttpRequest();
	if (!confirm(yousure)) return false;
	if (xreq) {
		xreq.open('GET',siteurl+'/walls.php?a=d&m='+msgid,true);
		xreq.onreadystatechange=function()
		  {
		  if (xreq.readyState==4 && xreq.status==200)
		    {
			  if (xreq.responseText==0) {
				  $('div.wid_'+msgid).each(function(index) {
					  $(this).closest('tr').remove();
				  });
			  } else {
				  alert('cannot delete this wall.'+xreq.responseText);
			  }
			  //divid.style.display='inline';
		    }
		  }
		xreq.send();
	}
}

function sendcomment(section,msgid) {
	var div=document.getElementById('mc_'+msgid);
	if (!div) return;
	var xreq=new XMLHttpRequest();
	if (xreq) {
		xreq.open('POST',siteurl+'/walls.php?m='+msgid,true);
		xreq.setRequestHeader('Content-type','application/x-www-form-urlencoded');
		xreq.send('b='+encodeURI(div.value)+'&k='+section);
		xreq.onreadystatechange=function()
		  {
		  if (xreq.readyState==4 && xreq.status==200)
		    {
			  var divid=document.getElementById(section+'cm_'+msgid);
			  if (divid) getcomments(section,msgid,divid);
		    }
		  }
		xreq.send();
	}
}

function checkusername(fld) {
	var div=document.getElementById('usernamevalidation');
	if (!div) return;
	var xreq=new XMLHttpRequest();
	if (xreq) {
		xreq.open('GET',siteurl+'/check.php?u='+encodeURI(fld.value),true);
		xreq.onreadystatechange=function()
		  {
		  if (xreq.readyState==4 && xreq.status==200)
		    {
			  switch (xreq.responseText) {
				  case '0':
					  div.innerHTML='<span class="fieldOK">'+available+'</span>';
					  div.display='inline';
					  break;
				  case '1':
					  div.innerHTML='<span class="fieldERR">'+used+'</span>';
					  div.display='inline';
					  break;
				  case '-2':
					  div.innerHTML='<span class="fieldERR">'+houston+'</span>';
					  div.display='inline';
					  break;
				  case '-3': // restricted usernames
					  div.innerHTML='<span class="fieldERR">'+reserved+'</span>';
					  div.display='inline';
					  break;
				  case '-4': // only numbers
					  div.innerHTML='<span class="fieldERR">'+needaletter+'</span>';
					  div.display='inline';
					  break;
				  case -1:
				  default:
					  div.innerHTML='';
					  div.display='none';
			  }
		    }
		  }
		xreq.send();
	}
}

var uploadtmr=new Array();

function showupload(result) {
	openbundle(result.basebundle);
	uploadtmr[result.mediaid]=setInterval(
			function() {
				checkupload(result.mediaid,result.basebundle);
			},10000);
}

function checkupload(mediaid,bundleid) {
	if ($('table#t_newbun').length) section='newbun';
	else if ($('div#new').css('display')!='none') section='new';
	var ob=$('table#t_'+section).find('#ob_'+bundleid);
	var wb=$('table#t_'+section).find('#wb_'+bundleid);
	var cb=$('table#t_'+section).find('#cb_'+bundleid);
	if (ob.length) {
		var status=$(ob).css('display');
		$(ob).hide();
	}
    if (cb.length) $(cb).hide();
    if (wb.length) $(wb).show();
	$.ajax({
		  type: "GET",
		  url: "/media.php?m="+mediaid+'&b='+bundleid,
		//this data is mendetory when u want post data when posting page by ajax.
		//after that ur data send to that page. now in backend page echo json data.if your //data is in array format the use json_encode() function then echo that json data
		//now you find tht json in your success that can you use by taking a loop.
		  data: '',
		  cache: false,
		  datatype: 'json',
		  success: function(code) {
			  if (wb.length) $(wb).hide();
			  if (status=='none') cb.show();
			  else ob.show();
			  result=$.parseJSON(code);
			  if (result.status==2) {
				  clearTimeout(uploadtmr[result.mediaid]);
				  openbundle(result.bundleid);
			  }
		  }
	 });

}

function addrow(result) {
	//if (result.error) { alert('bad!'); return; }
	var a=result.line;
	//var patt=/<[\/]tr[^>]*>/igm;
	//a=a.replace(patt,'');
	a=a.replace(/&lt;/igm,'<');
	a=a.replace(/&gt;/igm,'>');
	$b=$('tr.new_hdr:last').after(a);
	$('.input_0').show();
	
}

// --- sound.js

var currentmedia=0;
var mediavideo=false;
var timer = new Array();

function playPause(divid) {
	var myAV = document.getElementById(divid);
	//var mybutton = document.getElementById('i_'+divid);
	var able = document.getElementById('a_'+divid);
    var pbar=document.getElementById('b_'+divid);
    addMyListeners(divid);
	if (myAV) {
		if (myAV.paused) {
			play(divid);
		} else {
			pause(divid);
		}
	}
}

function timeduration(sec) {
	var mins=Math.floor(sec/60);
	var secs='0'+Math.round(sec % 60);
	var dur=mins+':'+secs.substr(secs.length-2,2);
	return dur;
}

function playv(divid) {
	var thismedia=divid.substr(divid.indexOf('_',divid)+1);
	if (currentmedia && currentmedia!=thismedia) pausev('track_'+currentmedia);
	currentmedia=thismedia;
	var $v=$('#v'+divid);
	if ($v.width()) {
		$a=$v.clone();
		$a.attr('id',divid); // remove init 'v' in id (vtrack_ID)
		//$a.width('640').height('480');
		$b=$('#formhelper div').first();
		$b.replaceWith('<div id="videowin" class="videowin"/>');
		var ww=$(window).width();
		var hh=$(window).height();
		var ll=(ww-($v.width()))/2;
		var tt=(hh-240)/2+$('div.mainwrapper').scrollTop();
		var vt=$v.closest('td').offset();
		tt=vt.top-($v.height())-50;
		$('div#videowin').offset({'top':tt,'left':ll});
		$('div#videowin').append($a);
		$close=$('<div/>').addClass('mediaclose').text('X');
		$close.click(function() {pausev(divid);});
		$('div#videowin').append($close);
		$a.wrap('<div class="videoframe"/>').fadeIn('slow');
		//$b.contents('<p>test</p>');
		//replaceWith($a.width('320').height('240').css('zIndex','100'));
		//$b.width('320').height('240');
	} else {
		$v=$('#'+divid);
		$v.fadeIn('slow');
		//$('a#fplayer').fadeIn('slow');
	}
	//flowplayer('videowin','/flash/flowplayer-3.2.7.swf');
	play(divid);
	VideoJS.setup(divid);
	flowplayer('fplayer','/flash/flowplayer-3.2.7.swf', {
		/*clip:{
			url:'.$link.'
		},*/
		screen:{
			height:'100pct',
			top:0
		},
		plugins:{
			controls:{
				buttonOffColor:'rgba(130,130,130,1)',
				borderRadius:0,
				timeColor:'rgba(0, 0, 0, 1)',
				bufferGradient:'none',
				borderColor:'rgba(204, 255, 153, 1)',
				zIndex:1,
				sliderColor:'rgba(255, 255, 255, 1)',
				backgroundColor:'rgba(204, 255, 153, 1)',
				scrubberHeightRatio:0.6,
				volumeSliderGradient:'none',
				tooltipTextColor:'rgba(0, 0, 0, 1)',
				sliderGradient:'none',
				spacing:{
					time:6,
					volume:8,
					all:2
				},
				timeBorderRadius:20,
				timeBgHeightRatio:0.8,
				volumeSliderHeightRatio:0.6,
				progressGradient:'none',
				height:24,
				volumeColor:'#000000',
				timeSeparator:' ',
				name:'controls',
				tooltips:{
					marginBottom:5,
					buttons:true
				},
				volumeBarHeightRatio:0.1,
				opacity:1,
				timeFontSize:12,
				left:'50pct',
				tooltipColor:'rgba(0, 184, 3, 1)',
				volumeSliderColor:'#ffffff',
				border:'0px',
				bufferColor:'rgba(255, 255, 153, 1)',
				buttonColor:'rgba(80, 153, 241, 1)',
				durationColor:'rgba(255, 255, 153, 1)',
				autoHide:{
					enabled:true,
					hideDelay:500,
					mouseOutDelay:500,
					hideStyle:'fade',
					hideDuration:400,
					fullscreenOnly:false
				},
				backgroundGradient:[0.5,0,0.3],
				width:'100pct',
				sliderBorder:'1px solid rgba(128, 128, 128, 0.7)',
				display:'block',
				buttonOverColor:'rgba(0, 181, 3, 1)',
				url:'flowplayer.controls-3.2.5.swf',
				progressColor:'rgba(255, 204, 153, 1)',
				timeBorder:'0px solid rgba(0, 0, 0, 0.3)',
				timeBgColor:'rgba(79, 152, 241, 1)',
				borderWidth:0,
				scrubberBarHeightRatio:0.1,
				bottom:'4pct',
				volumeBorder:'1px solid rgba(128, 128, 128, 0.7)',
				builtIn:false,
				margins:[2,12,2,12]
			}
		}		
	});
	mediavideo=true;
	$f().onFinish(function() {
		pausev(divid);
	});
}

function pausev(divid) {
	pause(divid);
	pause2(divid);
	//$('div.videowin').replaceWith('<div/>');
}

var playing=null;
var mediaid=0;
var mediatitle;
var playtimer;
var paused;
var playmode='';
var media;
var mediasrc;
var haspic;
var pausepos;
var savepausepos;
var mediatype;
var medias;
var medialist;
var thisindex;
var nextindex;
var previndex;
var contplay=0;
var playbarclone;
var playtimeid=0;
var starttime=0;
var mediaduration=0;
var playedtime=0;
var playedpercent=0;
var mediarate=0;
var mediaplaytype=0;
var FBplayid; // for FB actions tracking...

function playnext() {
	play(medias[nextindex]);
}

function playprev() {
	play(medias[previndex]);
}

function play(id) {
	//console.log('starting '+id);
	medialist=$('tr.bundle, table.pubmed').filter(function(index) {
			return ($(this).css('display')!='none');
		}).find('[id^=media_]');
	medias=new Array();
	thisindex=-1;
	medialist.each(function(index) {
		medias[index]=$(this).attr('id').substr(6);
		//console.log('index:'+index+', id:'+medias[index]);
		if ((!id || id==medias[index]) && thisindex<0) thisindex=index;
	});
	if (thisindex>=0) {
		if (thisindex+1<medias.length) nextindex=thisindex+1;
		else nextindex=0; 
		if (thisindex>0) previndex=thisindex-1;
		else previndex=medias.length-1;
		//console.log('curr:'+thisindex+', prev:'+previndex+', next:'+nextindex);
	} else {
		nextindex=-1;
		previndex=-1;
	}
	if (!id) {
		id=medias[0];
		contplay=1;
	}
	$('#pm_'+id).each(function() {
		$(this).css('display','none');
	});
	$('#pa_'+id).each(function() {
		$(this).css('display','inline');
	});
	media=$('#media_'+id+':first');
	if (media.is('audio')) {
		media.each(function() {
			this.play();
		});
		return;
	}
	mediasrc=media.attr('href');
	if (mediasrc.indexOf('-preview')>0 || mediasrc.indexOf('-small')>0)
		mediaplaytype=2; // extract/preview
	else
		mediaplaytype=1; // full-length/full-size
	haspic=media.attr('tag');
	mediatitle='<b>'+media.attr('title')+'</b>';
	var linked=new Array();
	var lmed;
	for (lmed=0;lmed<10;lmed++) {
		linked[lmed]=$('#lmedia_'+id+'_l'+lmed+':first');
		if (!linked[lmed].length) break;
	}
	if (mediaid) {
		//$('#playerwindow').show('slow');
		if (mediaid!=id) {
			stop(false);
			paused=false;
			//playing=null;
			//mediaid=0;
		}
	} else {
		//window.open('/player.php','mediaplayer','height=100,width=100');
		//if ($('#playermemory').length) alert('got a window!');
		if ($('#mediaplayerbar').length>0) {
			$('div#mediaplayer').remove();
			$('#mediaplayer').show();
			$('#mediaplayerbar').html('<div id="playerwindow"/>');
			$('#playerwindow').html(function() {
				str='<div class="mediawrapper"><div id="currentmedia"/></div>'
					+'<div class="playarea">'
					+'<div class="playbar" id="playbar"><div class="playload" id="playload"/>'
					+'<div class="played" id="played"/>'
					+'<div id="playstatus" class="playstatus"/>'
					+'</div>'
					+'<div class="playtime" id="playtime"/>'
					+'<div id="prevplay"/><div id="pauseplay"/><div id="nextplay"/><div id="contplay"/>'
					+'</div>';
				return str;
			});
		} else {
			$('#mediaplayer').html('<div id="playerwindow"/>');
			$('#playerwindow').html(function() {
				str='<div id="currentmedia"/>'
					+'<div id="playwin" class="playwin"/>'
					+'<div id="playstatus" class="playstatus"/>'
					+'<div class="playarea"><div id="prevplay"/><div id="pauseplay"/><div id="nextplay"/><div id="contplay"/>'
					+'<div class="playbar" id="playbar"><div class="playload" id="playload"/>'
					+'<div class="played" id="played"/>'
					+'<div class="playtime" id="playtime"/>'
					+'</div></div>';
				return str;
			});
		}
		if (previndex>=0) {
			var prevbutton=$('<img id="prevmediabtn" src="'+iconsurl+'prevmedia.png"/>');
			prevbutton.hover(
					function() {
						$(this).attr('src',iconsurl+'prevmediahover.png');
					},
					function() {
						$(this).attr('src',iconsurl+'prevmedia.png');
					}
			);
			prevbutton.click(function() {
				playprev();
			});
			$('#prevplay').append(prevbutton);
		}
		if (nextindex>=0) {
			var nextbutton=$('<img id="nextmediabtn" src="'+iconsurl+'nextmedia.png"/>');
			nextbutton.hover(
					function() {
						$(this).attr('src',iconsurl+'nextmediahover.png');
					},
					function() {
						$(this).attr('src',iconsurl+'nextmedia.png');
					}
			);
			nextbutton.click(function() {playnext();})
			$('#nextplay').append(nextbutton);
		}
		var stopbutton=$('<img id="stopmediabtn" src="'+iconsurl+'stopmedia.png"/>');
		if (thisindex>=0) {
			stopbutton.hover(
				function() {
					$(this).attr('src',iconsurl+'stopmediahover.png');
				},
				function() {
					$(this).attr('src',iconsurl+'stopmedia.png');
				}
			);
			stopbutton.click(function() {
				stop(true);
				//pause(medias[thisindex]);
			})
			$('#pauseplay').append(stopbutton);
		}
		var contbutton=$('<img id="contmediabtn" src="'+iconsurl+'contmedia.png"/>');
		if (thisindex>=0) {
			contbutton.hover(
				function() {
					if (!contplay) $(this).attr('src',iconsurl+'contmediahover.png');
					else $(this).attr('src',iconsurl+'contmedia.png');
				},
				function() {
					if (!contplay) $(this).attr('src',iconsurl+'contmedia.png');
					else $(this).attr('src',iconsurl+'contmediaon.png');
				}
			);
			contbutton.click(function() {
				contplay=(1-contplay);
				if (contplay) $(this).attr('src',iconsurl+'contmediaon.png');
				else $(this).attr('src',iconsurl+'contmedia.png');
				if (mediatype=='pic') {
					if (contplay) playtimer=setInterval(function() {endplay();},5000);
					else clearInterval(playtimer);
				}
			});
			$('#contplay').append(contbutton);

		}
	}
	if (media.hasClass('audiomedia')) {
		if (mediatype!='audio') {
			//+'<!--[if IE]>'
			//+'<script type="text/javascript" event="FSCommand(command,args)" for="myFlash">'
			//+'eval(args);'
			//+'</script>';
			//+'<![endif]-->';
			flashobj='<object id="myFlash" type="application/x-shockwave-flash" data="/flash/player_mp3_js.swf" width="1" height="1">'
				+'<param name="movie" value="/flash/player_mp3_js.swf" />'
				+'<param name="AllowScriptAccess" value="always" />'
				+'<param name="FlashVars" value="listener=myListener&amp;interval=500" />'
				+'<div id="noflash"><a href="http://www.adobe.com/go/getflash">'
				+'<img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player"/>'
				+'</a></div>'
				+'</object>';
			flashbkp='<div id="fplayer"></div>';
			audioobj='<audio id="mymedia" src="'+mediasrc+'"/>';
			$('#currentmedia').html(audioobj+flashbkp);
			$('#playerwindow').show();
			//$('#playerwindow').append(flashobj);
			//$('audio#mymedia').append(flashbkp);
			/*
			show(function() {
				$('#playerwindow').after(flashobj);
			});*/
			//$('#currentmedia').html(flashobj);
		}
		var audio;
		if (playmode=='flash') audio=$f();
		else audio=document.getElementById('mymedia');
		if (audio) {
			if (mediaid!=id) {
				$('#mymedia').attr('src',mediasrc);
				if (playmode=='flash') {
					audio.setClip(mediasrc);
				}
			}
			$('#playstatus').show();
			//$('#playbar').css('width','400px').css('height','8px').show();
			if (playbarclone) $('#playbar').replaceWith(playbarclone);
			playbarclone='';
			$('#playbar').attr('class','nowave').show();
			//$('#playload').css('background','#ffff99');
			//$('#played').css('background','#ffcc99');
			wavesrc=$('#wave_'+id);
			if ($(wavesrc).length>0) {
				wavepic=$('<img/>').attr('src',wavesrc.attr('href'));
				playbarclone=$('#playbar').clone();
				wavepic.load(function() {
					//$('#playbar').css('height','50px')
					$('#playbar').attr('class','playwave')
					.css('background','black');
					$('#playload').css('background','url('+wavesrc.attr('href')+')');
					$('#played').css('background','green')
					.css('border-right','white 1px solid');			
				});
				wavepic.error(function() {
					//console.info('error loading wavepic');
					//$('#playbar').replaceWith(playbarclone);
					$('#playbar').replaceWith(playbarclone);
					playbarclone='';
					//$('#playbar').css('height','6px');
					//$('#playload').css('background','#ffff99');
					//$('#played').css('background','#ffcc99');
				});
			} else {
				if (playbarclone) $('#playbar').replaceWith(playbarclone);
				playbarclone='';
			}
			$('#playload').css('width','0%');
			$('#played').css('width','0%');
			playing=audio;
			//paused=true;
			$('#playerwindow').show('fast',	function() {
				//$('tr.artistinfo').hide('fast',function() {
					if (mediaid!=id) {
						if (haspic) {
							//$show=$('<div id="showcase" class="showcase"/>');
							//$('#playwin').slideUp('fast');
							$('#playwin').html('<div id="showcase" class="showcase"/>').show();
							if (!lmed) add2show('<img src="'+haspic+'">');
							else {
								for (var i=0; i<lmed; i++) {
									//add2show('hello '+i);
									add2show('<img src="'+linked[i].attr('href')+'"/>');
								}
							}
							slideshow(lmed>1);
						} else {
							$('#playwin').html('');
						}
						$('#playwin img').error(function() {
							$('#playwin').hide();
						});
		
					}
				//});
			});
			$('#playbar').click(function(e) {
				if (!e) e=window.event;
				//per=(e.clientX-absleft(pbar))/pbar.clientWidth;
				//per=(_xmouse(e)-absleft(this))/this.width();
				var offset=$(this).offset();
				var per=(e.pageX-offset.left)/$(this).width();
				if (playmode!='flash') playing.currentTime=playing.duration*per;
				else playing.seek(playing.getClip(0).fullDuration*per);
				$('#played').css('width',(per*100)+'%');
			});
			if (playmode!='flash') addlisteners(audio);
			//if (audio.readyState>=3) audio.play();
			//else
			//if (audio.paused) {
				audio.play();
			//} else audio.load();
			//paused=false;
		}
		playtimer=setInterval(function() {showplayed();},1000);
		mediatype='audio';
	} else if (media.hasClass('videomedia')) {
		if (mediatype!='video') {
			flashbkp='<a id="fplayer"></a>';
			videoobj='<video id="mymedia" src="'+mediasrc+'"/>';
			$('#playwin').html('');
			$('#currentmedia').html(videoobj+flashbkp).show();
			$('#playerwindow').show();
			//$('#mymedia').append(flashbkp);
			//$('#currentmedia').show();
		}
		mediatype='video';
		var video;
		if (playmode=='flash') video=$f();
		else video=document.getElementById('mymedia');
		if (video) {
			if (mediaid!=id) {
				$('#mymedia').attr('src',mediasrc);
				if (playmode=='flash') {
					audio.setClip(mediasrc);
				}
			}
			$('#playstatus').show();
			// no wave form
			if (playbarclone) $('#playbar').replaceWith(playbarclone);
			playbarclone='';
			$('#playbar').attr('class','nowave').show();
			$('#playload').css('width','0%');
			$('#played').css('width','0%');
			//$('#playload').css('background-image','none');
			//$('#playload').css('background','#ffff99');
			//$('#played').css('background','#ffcc99');
			$('#playerwindow').show('fast',	function() {
				//$('tr.artistinfo').hide('fast');
			});
			playing=video;
			//paused=true;
			$('#playbar').click(function(e) {
				if (!e) e=window.event;
				//per=(e.clientX-absleft(pbar))/pbar.clientWidth;
				//per=(_xmouse(e)-absleft(this))/this.width();
				offset=$(this).offset();
				per=(e.pageX-offset.left)/$(this).width();
				if (playmode!='flash') playing.currentTime=playing.duration*per;
				else playing.seek(playing.getClip(0).fullDuration*per);
				$('#played').css('width',(per*100)+'%');
			});
			if (playmode!='flash') addlisteners(video);
			//if (video.paused) {
				video.play();
			//} else video.load();
			//paused=false;
		}
		playtimer=setInterval(function() {showplayed();},1000);

	} else if (media.hasClass('picmedia')) {
		playmode='';
		$('#playerwindow').show('fast',	function() {
			//$('tr.artistinfo').hide('fast');
		});
		mediatype='pic';
		mediaid=id;
		first=0;
		pic=$('<div class="picframe"><img id="protpic" src="'+mediasrc+'"></div>');
		if ($('#fake').length==0) {
			//first pic
			$('#currentmedia').html('<div id="fake"><div/></div>'); // picture protection
			first=1;
		} else {
		}
		$('#playstatus').html(mediatitle+' [loading...]');
		$('#playstatus').show();
		// no wave form
		if (playbarclone) $('#playbar').replaceWith(playbarclone);
		playbarclone='';
		$('#playbar').attr('class','nowave').show();
		$('#playload').css('width','0%');
		$('#played').css('width','0%');
		pic.find('img').hide().load(function() {
			//$(this).fadeIn('slow');
			$('#playload').css('width','100%');
			$('#played').css('width','100%');
			$('#currentmedia').show();
			npic=$('.picframe').length;
			if (npic>1) {
				$('.picframe').each(function(index) {
					if (index+1<npic) {
						$(this).animate({width:'100%', height:0, opacity:0},1000,function() {
							$(this).detach();
						});
					}
					else {
						w=$(this).find('img').width();
						h=$(this).find('img').height();
						//pic=$(this);
						//alert(w+'-'+h);
					}
				});
			} else {
				w=$(this).width();
				h=$(this).height();
			}
			// first pic
			cw=$('#currentmedia').width();
			ch=h+10;
			ml=Math.floor((cw-w)/2);
			$('.picframe:last').css('marginLeft',ml+'px');
			$('.picframe:last').animate(
					{width:w,height:h,opacity:1.0},
					1000,function(){
						$('#fake div').width(cw).height(h);
					}
					);
			$(this).show();
			if (contplay) playtimer=setInterval(function() {endplay();},5000);
			else if (playtimer) clearInterval(playtimer);
			$('#playstatus').html(mediatitle);
			//$('.picframe:last').width(w).height(h);
			//alert('w='+w+' ml='+ml+' cw='+cw);
			//$('#picframe').css('margin-left',ml+'px');
			//pic.show();*/
		});
		//$('#playbar').hide();
		$('#playwin').hide();
		$('#currentmedia').append(pic);
		/*$('#currentmedia').slideUp('slow', function() {
			$(this).html(pic).slideDown('slow',function() {
				w=$(this).width();
				h=$(this).height();
				$('#fake div').width(w).height(h);
			});
		});*/
		/*
		if (first) {
			$('#fake div').append(pic).slideDown('slow',function() {
				$(this).width($(this).find('img').width()).height();
			});
		}*/
		/*
		$('#currentmedia').slideUp('slow', function() {
			$(this).slideDown('slow',function() {
				w=$(this).width();
				h=$(this).height();
				$('#fake div').width(w).height(h);
			});
		});*/
		$('#playstatus').html(mediatitle+'<br/>(loading...)').show();
		if (mediasrc.indexOf('-small')>0) {
			$('#playstatus').html(mediatitle+'<br/>(Preview version)').show();
		}
		mediaid=id;
		playtime('start');
	} else if (media.hasClass('docmedia')) {
		mediatype='doc';
		alert('document media!');
	} else {
		alert('no type!');
	}
	//console.log('playing '+id);
	mediaid=id;
	//window.location='#mediaplayer';
}

function add2show(html) {
	//$slide=$('<div class="showcase-slide"><div class="showcase-content"><div class="showcase-content-wrapper"/></div></div>');
	//alert($slide.html());
	$('#showcase').append('<div class="showcase-slide"><div class="showcase-content">'
		+html+'</div><div class="showcase-thumbnail">'+html+'<div class="showcase-thumbnail-cover"></div></div>'
		+'</div>');
	/*$('#showcase').append('<div class="showcase-slide"><div class="showcase-content"><div class="showcase-content-wrapper">'
			+html+'</div></div><div class="showcase-thumbnail">'+html+'</div><div class="showcase-thumbnail-cover"></div>'
			+'</div>');*/
}

function slideshow(thumbs) {
	if (thumbs==undefined) thumbs=true;
	$("#showcase").awShowcase({
		content_width:			500,
		content_height:			400,
		fit_to_parent:			true,
		auto:					true,
		interval:				5000,
		continuous:				true,
		loading:				true,
		tooltip_width:			200,
		tooltip_icon_width:		32,
		tooltip_icon_height:	32,
		tooltip_offsetx:		18,
		tooltip_offsety:		0,
		arrows:					true,
		buttons:				true,
		btn_numbers:			true,
		keybord_keys:			true,
		mousetrace:				false, /* Trace x and y coordinates for the mouse */
		pauseonover:			true,
		stoponclick:			false,
		transition:				'vslide', /* hslide/vslide/fade */
		transition_delay:		0,
		transition_speed:		500,
		show_caption:			'onload', /* onload/onhover/show */
		thumbnails:				thumbs,
		thumbnails_position:	'outside-last', /* outside-last/outside-first/inside-last/inside-first */
		thumbnails_direction:	'horizontal', /* vertical/horizontal */
		thumbnails_slidex:		1, /* 0 = auto / 1 = slide one thumbnail / 2 = slide two thumbnails / etc. */
		dynamic_height:			true, /* For dynamic height to work in webkit you need to set the width and height of images in the source. Usually works to only set the dimension of the first slide in the showcase. */
		speed_change:			true, /* Set to true to prevent users from swithing more then one slide at once. */
		viewline:				false, /* If set to true content_width, thumbnails, transition and dynamic_height will be disabled. As for dynamic height you need to set the width and height of images in the source. */
		custom_function:		null /* Define a custom function that runs on content change */
	});	
}

function stop(closeall) {
	//console.log('stopping '+mediaid+'closeall='+closeall);
	if (mediaid) {
		playtime('stop');
		if (playtimer) clearInterval(playtimer);
		if (mediatype!='pic') {
			paused=true;
			//dellisteners(playing);
			//playing.autoplay=false;
			if ((playmode=='flash' && !playing.isPaused())
				|| (playmode!='flash' && !playing.paused)) {
				playing.pause();
			}
			$('#playstatus').text('');
			if (playtimer) clearInterval(playtimer);
			dellisteners(playing);
		}
	}
	if (closeall==true) {
		$('#playerwindow').hide(); /*'fast', function() {
			//$('tr.artistinfo').show('fast');
		});*/
	}
	if (mediaid) {
		$('#pa_'+mediaid).css('display','none');
		$('#pm_'+mediaid).css('display','inline');
	}
}

function pause(id) {
	//console.log('pausing '+id);
	//if (mediatype!='pic' && playmode=='flash') { flashpause(id); return; }
	if (mediatype=='pic') stop(true);
	else stop(false);
	//playing=null;
	//mediaid=null;
	$('#playerwindow').hide(); /* 'fast', function() {
		//$('tr.artistinfo').show('fast');
	});*/
	//$('#playerwindow').html('');
}

// same for flash


function flashplay(id) {
	if (playmode!='flash') {
		//stop(false);
		if (playing) {
			dellisteners(playing);
			clearInterval(playtimer);
			playing=null;
			//playing.pause();
		}
		//mediaid=0;
		//pausepos=0;
		//paused=false;
	}
	playmode='flash';
	$('#mymedia').css('display','none');
	$('#playstatus').text('[Playing using Flash...Loading]');
	flowplayer('fplayer','/js/flowplayer/flowplayer-3.2.11.swf',{
		plugins: {
			audio: {
		        url: '/js/flowplayer/flowplayer.audio-3.2.9.swf'
			}
		},
		onStart: function() {
			startplay();
			//$('#playstatus').html(mediatitle+' [playing]');
			//playtime
		},
		onBegin: function() {
			showloading();
			//$('#playstatus').html(mediatitle+' [loading...]');
			if (mediatype=='video') $('#fplayer').width('320').height('240');
		},
		onFinish: function() {
			endplay();
			//$('#playstatus').html(mediatitle+' [ended]');
		},
		onPaused: function() {
			paused=true;
		},
		onLoad: function() {
			this.addClip(mediasrc,0);
			playing=this;
			//paused=true;
			this.play(0);
			playtimer=setInterval(function() {showplayed();},1000);
		}
	}).load();
	return;
	//_getFlashObject().SetVariable('enabled', '1');
	//_getFlashObject().SetVariable('interval', '2000');
	//_getFlashObject().SetVariable("useexternalinterface", 'true');
	if (paused) {
		//$('#playerwindow').show('slow');
		if (mediaid==id && savepausepos>0) {
			_getFlashObject().SetVariable("method:setPosition", savepausepos);
		} else {
			_getFlashObject().SetVariable("method:setUrl", mediasrc);
			pausepos=0;
		}
	}
	if (mediaid!=id) { // stop other media
		if (mediaid) flashstop(false);
		_getFlashObject().SetVariable("method:setUrl", mediasrc);
	}
	_getFlashObject().SetVariable("method:play", "");
	_getFlashObject().SetVariable("enabled", 'true');
	//$('#mymedia').attr('src','');
	//$('#playwin').hide();
	//_getFlashObject().SetVariable("enabled", "true");
	//_getFlashObject().SetVariable("method:pause", '');
	mediaid=id;
	paused=false;
    $('#playstatus').html(mediatitle+' (Playing [Flash!])');
    $('#playerwindow').show();
	if (haspic) {
		pic='<img src="'+haspic+'">';
	}
	$('#playbar').css('width','80%');
	$('#playload').css('width','0%');
	$('#played').css('width','0%');
	$('#playerwindow').show();
	$('#playbar').click(function(e) {
		if (!e) e=window.event;
		//per=(e.clientX-absleft(pbar))/pbar.clientWidth;
		//per=(_xmouse(e)-absleft(this))/this.width();
		offset=$(this).offset();
		per=(e.pageX-offset.left)/$(this).width();
		if (myListener.bytesPercent)
			_getFlashObject().SetVariable("method:setPosition", per*myListener.duration*100/myListener.bytesPercent);
		$('#played').css('width',Math.round(per*100)+'%');
	});
	if (haspic) {
		$('#playwin').html(pic).show();
	}  else {
		$('#playwin').hide();
		$('#playwin').html('');
	}
	$('#playstatus').html(mediatitle+' [Playing...]');
	$('#pm_'+id).hide();
	$('#pa_'+id).show();
}

function flashstop(closeall) {
	if (closeall) {
		
	} else $f().pause(0);
	if (mediaid) {
		$('#pa_'+mediaid).hide();
		$('#pm_'+mediaid).show();
	}
}

function flashpause(id) {
	flashstop(true);
	$('#playerwindow').hide();
	paused=true;
}

// display refresh functions

function showloaded() {
	var dur,bufend,start,ppaused;
	if (mediatype=='pic') return;
	if (playmode!='flash') {
		dur=playing.duration;
		if (playing && playing.buffered.end.length) bufend=playing.buffered.end(0);
		else bufend=0;
		start=playing.startTime;
		ppaused=playing.paused;
	} else {
		dur=playing.getClip(0).fullDuration;
		bufend=playing.getStatus().bufferEnd;
		start=playing.getClip(0).bufferLength;
		ppaused=playing.isPaused();
	}
	if (!dur) return;
	
	var sofar = Math.round((bufend / dur) * 100);
	if (sofar<0) sofar=0;
	$('#playload').css('width',sofar+'%');
	//console.log('loaded '+sofar+'%');
	if (sofar<100 && ppaused) $('#playstatus').html(mediatitle+' (Loading...['+sofar+'%]) '+(playing.startTime>0?playing.startTime:''));
	//if (sofar==100 && paying.paused) clearInterval(playtimer); //no need to update
	//else $('#playstatus').text('Enjoy...');
}

//var playedtime=-1;
//var playedchrono=0;

function showplayed() {
	var pt,dur,ppaused;
	if (!playing) return;
	showloaded();
	if (playmode!='flash') {
		dur=playing.duration;
		pt=playing.currentTime;
		if (pt<0) pt=0;
		ppaused=playing.paused;
		if (playing.ended) return endplay(); // solve bug when end is not detected.
	} else {
		dur=playing.getClip(0).fullDuration;
		pt=playing.getTime();
		ppaused=playing.isPaused();
	}
	mediaduration=dur;
	if (!dur) return;
	playedpercent=playedtime/mediaduration;
	/*var showtime;
	var dt=new Date();
	if (pt!=playedtime) {
		playedchrono=dt.getTime();
		showtime=pt;
		playedtime=pt;
	} else {
		showtime=pt+Math.round((dt.getTime()-playedchrono)/1000);
	}*/
    var sofar = Math.round(((pt / dur) * 100));
	if (sofar<0) sofar=0;
	$('#played').css('width',sofar+'%');
	//console.log('played '+sofar+'%');
	if (!ppaused) {
		$('#playstatus').html(mediatitle);
		$('#playtime').text(timeduration(pt));
		playtime('update');
	}
}

function showloading() {
	$('#playstatus').html(mediatitle+' (Connecting...)').show();
}

function updateplaytime(media,mediaptype,action,plid,pper,ptime,prate,pstat) {
	$.ajax({
		  type: "GET",
		  url: "/play.php?m="+media+"&mt="+mediaptype+"&a="+action+'&id='+plid+'&p='+pper+'&t='+ptime+'&r='+prate+'&s='+pstat,
		//this data is mendetory when u want post data when posting page by ajax.
		//after that ur data send to that page. now in backend page echo json data.if your //data is in array format the use json_encode() function then echo that json data
		//now you find tht json in your success that can you use by taking a loop.
		  data: '',
		  cache: false,
		  datatype: 'html',
		  success: function(code){
			  if (code && action=='start') {
				  playtimeid=code;
			  }
		  }
	 });	
}

var oldplayedtime;
var FBinformed=false;

function playtime(action) {
	var curplayedtime=playedtime+Math.round((new Date().getTime() - starttime) / 1000);
	if (mediaduration) playedpercent=Math.round(100*curplayedtime/mediaduration);
	switch(action) {
		case 'start':
			//console.info('started playing '+mediaid);
			updateplaytime(mediaid,mediaplaytype,'start',0,0,0,0,0);
			playedtime=0;
			starttime=new Date().getTime();
			FBinformed=false;
			if (mediatype=='pic') {
				dt=new Date();
				FB.api('/me/musxpand:view','post',
						{
					picture: siteurl+'/m/'+mediaid,
					start_time: dt.toISOString(),
					expires_in: 10
						},function(response){
							
						}, function(response) {
						    if (response && response.id) {
						    	//console.info('FB action ID='+response.id);
						    	FBplayid=response.id;
						    } else {
						    	FBplayid=0;
						    }
						});			
			}
			break;
		case 'restart':
			//console.info('re-started playing '+mediaid);
			starttime=new Date().getTime();
			// no update necessary, will update soon
			break;
		case 'stop':
			if (!paused) {
				//console.info('stopped playing '+mediaid);
				playedtime=curplayedtime;
				updateplaytime(mediaid,mediaplaytype,'stop',playtimeid,playedpercent,playedtime,0,0);
			}
			if (FBplayid) {
				dt=new Date();
				if (mediatype=='audio') {
					FB.api('/'+FBplayid,'POST',
							{
								media: siteurl+'/m/'+mediaid,
								stop_time: dt.toISOString()
							}, function(response) {
								//console.info(response);
							});			
				} else if (mediatype=='video') {
					FB.api('/'+FBplayid,'POST',
							{
								video: siteurl+'/m/'+mediaid,
								stop_time: dt.toISOString()
							}, function(response) {
								//console.info(response);
							});
				} else if (mediatype=='pic') {
					FB.api('/'+FBplayid,'POST',
							{
								picture: siteurl+'/m/'+mediaid,
								stop_time: dt.toISOString()
							}, function(response) {
							});
				}
			}
			break;
		case 'error':
			//console.info('erro playing '+mediaid);
			updateplaytime(mediaid,mediaplaytype,'error',playtimeid,playedpercent,curplayedtime,0,1);
			break;
		case 'update':
			if (curplayedtime!=oldplayedtime) {
				oldplayedtime=curplayedtime;
				//console.info('played '+playedtime+'s of '+mediaid);
				updateplaytime(mediaid,mediaplaytype,'update',playtimeid,playedpercent,curplayedtime,0,0);
			}
			if (curplayedtime>15 && !FBinformed) {
				dt=new Date();
				if (mediatype=='audio') {
					FB.api('/me/musxpand:stream','POST',
							{
								media: siteurl+'/m/'+mediaid,
								start_time: dt.toISOString(),
								expires_in: mediaduration
							}, function(response) {
							    if (response && response.id) {
							    	//console.info('FB action ID='+response.id);
							    	FBplayid=response.id;
							    } else {
							    	FBplayid=0;
							    }
							});			
				} else if (mediatype=='video') {
					FB.api('/me/musxpand:stream','POST',
							{
								//video: siteurl+'/m/'+mediaid,
								media: siteurl+'/m/'+mediaid,
								start_time: dt.toISOString(),
								expires_in: mediaduration
							}, function(response) {
							    if (response && response.id) {
							    	//console.info('FB action ID='+response.id);
							    	FBplayid=response.id;
							    } else {
							    	FBplayid=0;
							    }
							});			
				} else if (mediatype=='pic') {
				}
				FBinformed=true;
			}
			break;
		case 'rate':
			//console.info('rated '+mediaid);
			updateplaytime(mediaid,mediaplaytype,'rate',playtimeid,playedpercent,curplayedtime,mediarate,0);
			break;
	}
}

function startplay() {
	if (playing) {
		$('#playstatus').html(mediatitle+' (Playing...)');
		//playing.play();
		if (paused) {
			playtime('restart');
		} else {
			playtime('start');
		}
		paused=false;
	} else {
		//console.log('cannot play... no "playing" or paused!?');
	}
}

function errorplay() {
	if (playmode=='flash') return;
	if (playing.error=='[object MediaError]') {
		playtime('error');
		return flashplay(mediaid);
	} else {
		$('#playstatus').text('error:'+playing.error);
		playtime('error');
	}
	clearInterval(playtimer);
	dellisteners(playing);
}

function endplay() {
	if (contplay) playnext();
	else {
		if (playmode=='flash') flashstop(true);
		else stop(true);
	}
}

function pauseplay() {
	$('#playstatus').html(mediatitle+' (paused)');
}

function showmetadata() {
	$('#playstatus').html(mediatitle+' (Connecting...)');
}

function addlisteners(media) {
	//alert('adding events handlers');
    if (media.addEventListener) {
	    //media.addEventListener('progress',showloaded,false);
	    media.addEventListener('timeupdate',showplayed,false);
	    media.addEventListener('loadstart',showloading,false);
	    media.addEventListener('loadedmetadata',showmetadata,false);
	    media.addEventListener('play',startplay,false);
	    media.addEventListener('error',errorplay,true);
	    media.addEventListener('ended',endplay,false);
	    //media.addEventListener('pause',pause,false);
    } else if (media.attachEvent) {
        //media.attachEvent('progress',showloaded);
	    media.attachEvent('timeupdate',showplayed);
        media.attachEvent('loadstart',showloading);
        media.attachEvent('loadedmetadata',showmetadata);
        media.attachEvent('play',startplay);
        media.attachEvent('error',errorplay);
        media.attachEvent('ended',endplay);
	    //media.attachEvent('pause',pause,false);
    }
}

function dellisteners(media) {
    if (media.removeEventListener) {
	    //media.removeEventListener('progress',showloaded,false);
	    media.removeEventListener('timeupdate',showplayed,false);
	    media.removeEventListener('loadstart',showloading,false);
	    media.removeEventListener('loadedmetadata',showmetadata,false);
	    media.removeEventListener('play',startplay,false);
	    media.removeEventListener('error',errorplay,true);
	    media.removeEventListener('ended',endplay,false);
	    //media.removeEventListener('pause',pause,false);
    } else if (media.detachEvent) {
        //media.detachEvent('progress',showloaded);
	    media.detachEvent('timeupdate',showplayed);
	    media.detachEvent('loadstart',showloading);
	    media.detachEvent('loadedmetadata',showmetadata);
        media.detachEvent('play',startplay);
        media.detachEvent('error',errorplay);
        media.detachEvent('ended',endplay);
        //media.detachEvent('pause',pause);
    }
}

/*

function addlisteners(media) {
	//alert('adding events handlers');
    if (media.addEventListener) {
	    //media.addEventListener('progress',function (){showloaded();},false);
	    media.addEventListener('timeupdate',function (){showplayed();},false);
	    media.addEventListener('loadstart',function (){showloading();},false);
	    media.addEventListener('loadedmetadata',function (){showmetadata();},false);
	    media.addEventListener('play',function (){startplay();},false);
	    media.addEventListener('error',function (){errorplay();},true);
	    media.addEventListener('ended',function (){endplay();},false);
	    //media.addEventListener('pause',function (){pause();},false);
    } else if (media.attachEvent) {
        //media.attachEvent('progress',function (){showloaded();});
	    media.attachEvent('timeupdate',function (){showplayed();});
        media.attachEvent('loadstart',function (){showloading();});
        media.attachEvent('loadedmetadata',function (){showmetadata();});
        media.attachEvent('play',function (){startplay();});
        media.attachEvent('error',function (){errorplay();});
        media.attachEvent('ended',function (){endplay();});
	    //media.attachEvent('pause',function (){pause();},false);
    }
}

function dellisteners(media) {
    if (media.removeEventListener) {
	    //media.removeEventListener('progress',function (){},false);
	    media.removeEventListener('timeupdate',function (){},false);
	    media.removeEventListener('loadstart',function (){},false);
	    media.removeEventListener('loadedmetadata',function (){},false);
	    media.removeEventListener('play',function (){},false);
	    media.removeEventListener('error',function (){},true);
	    media.removeEventListener('ended',function (){},false);
	    //media.removeEventListener('pause',function (){},false);
    } else if (media.detachEvent) {
        //media.detachEvent('progress',function (){});
	    media.detachEvent('timeupdate',function (){});
	    media.detachEvent('loadstart',function (){});
        media.detachEvent('play',function (){});
        media.detachEvent('loadedmetadata',function (){});
        media.detachEvent('error',function (){});
        media.detachEvent('ended',function (){});
        //media.detachEvent('pause',function (){});
    }
}

*/
// --- flash player

function makeBig(divid) {
	var myAV = document.getElementById(divid);
	myAV.height = (myAV.videoHeight * 2 ) ;
}

function makeNormal(divid) {
	var myAV = document.getElementById(divid);
	myAV.height = (myAV.videoHeight) ;
}

function getPercentProg(divid) {
    var myAV = document.getElementById(divid);
    var able=document.getElementById('a_'+divid);
    var pbar=document.getElementById('b_'+divid);
    var soFar = Math.round(((myAV.buffered.end(0) / myAV.duration) * 100));
    if (able) able.innerHTML =  'loading... ('+soFar + '%)';
    if (pbar) pbar.style.width = soFar + '%';
   }

function showloadingx(divid) {
	var $mybutton=$('#pm_'+currentmedia);
	$mybutton.attr('src',siteurl+'/images/icons/loadmedia.png');
}

function absleft(x) {
	if (x==document.body) return 0;
	return x.offsetLeft+absleft(x.offsetParent);
}

function abstop(x) {
	if (x==document.body) return 0;
	return x.offsetTop+abstop(x.offsetParent);
}

function jumpplay(e,pbar,plbar,myAV) {
	if (!e) e=window.event;
	//per=(e.clientX-absleft(pbar))/pbar.clientWidth;
	per=(_xmouse(e)-absleft(pbar))/pbar.clientWidth;
	if (myAV) myAV.currentTime=myAV.duration*per;
	else if (myListener) _getFlashObject().SetVariable("method:setPosition", per*myListener.duration);
	plbar.style.width=(per*100)+'%';
}

function getPercentPlayed(divid) {
    var myAV = document.getElementById(divid);
    var plbar=document.getElementById('p_'+divid);
    var soFar = Math.round(((myAV.currentTime / myAV.duration) * 100));
    if (plbar) plbar.style.width = soFar + '%';
    //var able=document.getElementById('a_'+divid);
    if (myAV.ended) playended(divid);
   }

function addMyListeners(divid){
	//alert('adding events handlers');
    var myAV = document.getElementById(divid);
    if (myAV.addEventListener) {
	    myAV.addEventListener('progress',function (){getPercentProg(divid);},false);
	    myAV.addEventListener('loadstart',function (){showloading(divid);},false);
	    myAV.addEventListener('canplaythrough',function (){play(divid);},false);
	    myAV.addEventListener('error',function (){playerror(divid);},true);
	    myAV.addEventListener('ended',function (){playended(divid);},false);
	    myAV.addEventListener('pause',function (){clearInterval(timer[divid]);},false);
    } else if (myAV.attachEvent) {
        myAV.attachEvent('progress',function (){getPercentProg(divid);});
        myAV.attachEvent('loadstart',function (){showloading(divid);});
        myAV.attachEvent('canplaythrough',function (){play(divid);});
        myAV.attachEvent('error',function (){playerror(divid);});
        myAV.attachEvent('ended',function (){playended(divid);});
        myAV.attachEvent('pause',function (){clearInterval(timer[divid]);});
    }
}

function delMyListeners(divid) {
    var myAV = document.getElementById(divid);
    if (myAV.addEventListener) {
	    myAV.addEventListener('progress',function (){},false);
	    myAV.addEventListener('canplaythrough',function (){},false);
	    myAV.addEventListener('error',function (){},true);
	    myAV.addEventListener('ended',function (){},false);
	    myAV.addEventListener('pause',function (){},false);
    } else if (myAV.attachEvent) {
        myAV.attachEvent('progress',function (){});
        myAV.attachEvent('canplaythrough',function (){});
        myAV.attachEvent('error',function (){});
        myAV.attachEvent('ended',function (){});
        myAV.attachEvent('pause',function (){});
    }
}

/* --- player_mp3_js --- */

var myListener = new Object();
/**
 * Initialize
 */
myListener.onInit = function() {
	this.position=0;
	//obj.SetVariable('interval','500');

	//_addEventListener(document.getElementById("playerslider"), "mousedown", _sliderDown, true);
	//_addEventListener(document.getElementById("playerslider"), "mousemove", _sliderMove, true);
	//_addEventListener(document.getElementById("playerslider"), "mouseup", _sliderUp, true);
};
/**
 * Update
 */
myListener.onUpdate = function() {
	var isPlaying = this.isPlaying;
	var url = this.url;
	var volume = this.volume;
	var position = this.position;
	var duration = this.duration;
	var bytesPercent = this.bytesPercent;
	
	var id3_artist = this.id3_artist;
	var id3_album = this.id3_album;
	var id3_songname = this.id3_songname;
	var id3_genre = this.id3_genre;
	var id3_year = this.id3_year;
	var id3_track = this.id3_track;
	var id3_comment = this.id3_comment;
	
	pausepos=this.position;	
	/*
	document.getElementById("info_playing").innerHTML = isPlaying;
	document.getElementById("info_url").innerHTML = url;
	document.getElementById("info_volume").innerHTML = volume;
	document.getElementById("info_position").innerHTML = position;
	document.getElementById("info_duration").innerHTML = duration;
	document.getElementById("info_bytes").innerHTML = obj.bytesLoaded + "/" + obj.bytesTotal + " (" + obj.bytesPercent + "%)";
	
	document.getElementById("info_id3_artist").innerHTML = id3_artist;
	document.getElementById("info_id3_album").innerHTML = id3_album;
	document.getElementById("info_id3_songname").innerHTML = id3_songname;
	document.getElementById("info_id3_genre").innerHTML = id3_genre;
	document.getElementById("info_id3_year").innerHTML = id3_year;
	document.getElementById("info_id3_track").innerHTML = id3_track;
	document.getElementById("info_id3_comment").innerHTML = id3_comment;
	*/
	
	isPlaying = (isPlaying == "true");
	//document.getElementById("pm_"+media_id).style.display = (isPlaying)?"none":"block";
	//document.getElementById("pa_"+media_id).style.display = (isPlaying)?"block":"none";
		
	/*
	var timelineWidth = 160;
	var sliderWidth = 40;
	var sliderPositionMin = 40;
	var sliderPositionMax = sliderPositionMin + timelineWidth - sliderWidth;
	var sliderPosition = sliderPositionMin + Math.round((timelineWidth - sliderWidth)* position / duration);
	
	if (sliderPosition < sliderPositionMin) {
		sliderPosition = sliderPositionMin;
	}
	if (sliderPosition > sliderPositionMax) {
		sliderPosition = sliderPositionMax;
	}
	
	document.getElementById("playerslider").style.left = sliderPosition+"px";
	*/

	if (bytesPercent==undefined) bytePercent=0;
	$('#playload').css('width',bytesPercent + '%');
	if (duration>0 && position>=0) {
		$('#played').css('width',((position/duration)*bytesPercent) + '%');
	} else $('#played').css('width','0px');

	if (isPlaying && myListener.duration>0 && bytesPercent>0) {
		$('#playstatus').html(mediatitle+'<br/>Playing... '+timeduration(myListener.position/1000));
	}
	
	if (!paused && !isPlaying) flashplay(mediaid);
}

/**
 * private functions
 */
var sliderPressed = false;

function _getFlashObject()
{
	return document.getElementById("myFlash");
}

function _cumulativeOffset (pElement)
{
	var valueT = 0, valueL = 0;
	do {
		valueT += pElement.offsetTop  || 0;
		valueL += pElement.offsetLeft || 0;
		pElement = pElement.offsetParent;
	} while (pElement);
	return [valueL, valueT];
}
function _xmouse(pEvent)
{
	return pEvent.pageX || (pEvent.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft));
}
function _ymouse(pEvent)
{
	return pEvent.pageY || (pEvent.clientY + (document.documentElement.scrollTop || document.body.scrollTop));
}
function _findPosX(pElement)
{
	if (!pElement) return 0;
	var pos = _cumulativeOffset(pElement);
	return pos[0];
}
function _findPosY(pElement)
{
	if (!pElement) return 0;
	var pos = _cumulativeOffset(pElement);
	return pos[1];
}
function _addEventListener(pElement, pName, pListener, pUseCapture)
{
	if (pElement.addEventListener) {
		pElement.addEventListener(pName, pListener, pUseCapture);
	} else if (pElement.attachEvent) {
		pElement.attachEvent("on"+pName, pListener);
	}
}
function _sliderDown(pEvent)
{
	sliderPressed = true;
}
function _sliderMove(pEvent)
{
	if (sliderPressed) {
		var timelineWidth = 160;
		var sliderWidth = 40;
    	var sliderPositionMin = 40;
    	var sliderPositionMax = sliderPositionMin + timelineWidth - sliderWidth;
		var startX = _findPosX(document.getElementById("timeline"));
		var x = _xmouse(pEvent) - sliderWidth / 2;
		
		if (x < startX) {
			var position = 0;
		} else if (x > startX + timelineWidth) {
			var position = myListener.duration;
		} else {
			var position = Math.round(myListener.duration * (x - startX - sliderWidth) / (startX + timelineWidth - sliderWidth - startX));
		}
		_getFlashObject().SetVariable("method:setPosition", position);
	}
}

function _sliderUp(pEvent)
{
	sliderPressed = false;
}

/**
 * public functions
 */
function play2(link,id) {
	if (currentmedia && currentmedia!=id) pause('track_'+currentmedia);
	currentmedia=id;
	mediavideo=false;
	if (myListener.position == 0 || myListener.media_id!=id) {
		if (myListener.media_id) {
			document.getElementById("pm_"+myListener.media_id).style.display = "block";
			document.getElementById("pa_"+myListener.media_id).style.display = "none";
		    $('#s_track_'+myListener.media_id).each(function() {
		    	$(this).hide();
		    });
		}
    	_getFlashObject().SetVariable("method:setUrl", link);
    }
    //_getFlashObject().SetVariable("method:setUrl", "http://scfire-nyk-aa01.stream.aol.com:80/stream/1074");
    _getFlashObject().SetVariable("method:play", "");
    _getFlashObject().SetVariable("enabled", "true");
    myListener.media_id=id;
	document.getElementById("pm_"+myListener.media_id).style.display = "none";
	document.getElementById("pa_"+myListener.media_id).style.display = "block";
    var pbar=document.getElementById('b_track_'+id);
    var plbar=document.getElementById('p_track_'+id);
    $('#s_track_'+id).each(function() {
    	$(this).show();
    });
    if (pbar) {
    	pbar.onclick=function(event) {jumpplay(event,pbar,plbar,null);};
    }
	var able = document.getElementById('a_track_'+id);
	if (myListener.isPlaying) {
		if (able) able.innerHTML='playing ('+timeduration(myListener.duration/1000)+')';
		if (pbar && myListener.bytesPercent) pbar.style.width=(myListener.bytesPercent)+'%';
		else if (pbar) pbar.style.width='100%';
		if (plbar) plbar.style.width='0%';
		/*timer['track_'+id]=setInterval(function() {
			pbar.style.width=(myListener.bytesPercent)+'%';
			plbar.style.width = (myListener.position/myListener.duration) + '%';
		},1000);*/
	} else {
		//myAV.load();
		if (able) able.innerHTML='loading...';
		if (pbar) pbar.style.width='0%';
		if (plbar) plbar.style.width='0%';
		/*timer['track_'+id]=setInterval(function() {
			pbar.style.width=(myListener.bytesPercent)+'%';
			plbar.style.width = (myListener.position/myListener.duration) + '%';
		},1000);*/
	}
}

function pause2() {
	if (!myListener.position) return;
    _getFlashObject().SetVariable("method:pause", "");
	document.getElementById("pm_"+myListener.media_id).style.display = "block";
	document.getElementById("pa_"+myListener.media_id).style.display = "none";
    $('#s_track_'+myListener.media_id).each(function() {
    	$(this).hide();
    });
    currentmedia=0;
}

function stop2() {
    _getFlashObject().SetVariable("method:stop", "");
}

function setcopybtn(btn,div) {
	$('a#'+btn).show().click(function(){});
	$('a#'+btn).zclip({
        path:siteurl+'/flash/ZeroClipboard.swf',
        copy:function(){return $('textarea#'+div).val();}
    });
}

var zoom=0;
function qrzoom(mode) {
	if (!zoom && mode) {
		$('img#qrcode').width('200px');
		var zoomtmr=setTimeout(function() {qrzoom(false)},15000);
		zoom=1;
	} else {
		$('img#qrcode').width('64px');
		clearTimeout(zoomtmr);
		zoom=0;
	}
}

function savefbpics() {
	$.ajax({
	  type: "GET",
	  url: "/fbpics.php",
	//this data is mendetory when u want post data when posting page by ajax.
	//after that ur data send to that page. now in backend page echo json data.if your //data is in array format the use json_encode() function then echo that json data
	//now you find tht json in your success that can you use by taking a loop.
	  data: '',
	  cache: false,
	  datatype: 'html',
	  success: function(code){
		  $("div.userpic").html(code);
		  //alert(code);    
	  }
	 });
}

function showcats(fldname,value) {
	$('#'+fldname).empty();
	for (var i=0;i<(subcats[value]).length;i++) {
		genre=subcatsndx[value][i];
		$('#'+fldname).append('<option value="'+genre+'"'+(genre==value?' selected':'')+'>'+subcats[value][i]+'</option>');
	}
}

var mxvisits;
var mxhits;
function checkvisits() {
	$.ajax({
	  type: "GET",
	  url: "/counter.php",
	//this data is mendetory when u want post data when posting page by ajax.
	//after that ur data send to that page. now in backend page echo json data.if your //data is in array format the use json_encode() function then echo that json data
	//now you find tht json in your success that can you use by taking a loop.
	  data: '',
	  cache: false,
	  datatype: 'json',
	  success: function(code){
		  vclass='';
		  hclass='';
		  var cnt=$.parseJSON(code);
		  if (mxvisits!=cnt.visitors) {
			  mxvisits=cnt.visitors;
			  vclass='newcnt';
		  }
		  if (mxhits!=cnt.hits) {
			  mxhits=cnt.hits;
			  hclass='newcnt';
		  }
		  $("span#mxvisits").text(cnt.visitors).attr('class',vclass);
		  $("span#mxhits").text(cnt.hits).attr('class',hclass);
		  setTimeout(function() {
			  $('span#mxvisits').attr('class','');
			  $('span#mxhits').attr('class','');
		  	},1000);
	  }
	 });	
}

function textedit(elem) {
	txt=$(elem).text();
	id=$(elem).attr('id');
	$(elem).replaceWith('<input type="text" name="'+id+'" value="'+txt+'">')
}

function setmediapic(picid,mediaid) {
	$.ajax({
		  type: "GET",
		  url: "/media.php?p="+picid+"&m="+mediaid,
		//this data is mendetory when u want post data when posting page by ajax.
		//after that ur data send to that page. now in backend page echo json data.if your //data is in array format the use json_encode() function then echo that json data
		//now you find tht json in your success that can you use by taking a loop.
		  data: '',
		  cache: false,
		  datatype: 'json',
		  success: function(code){
			  var res=$.parseJSON(code);
			  //alert('res:'+res);
		  }
	 });	
}

function bundlemedia(mediaid,bunid,pos) {
	if (pos==undefined) pos=0;
	$.ajax({
		  type: "GET",
		  url: "/media.php?m="+mediaid+"&d="+bunid+"&p="+pos,
		//this data is mendetory when u want post data when posting page by ajax.
		//after that ur data send to that page. now in backend page echo json data.if your //data is in array format the use json_encode() function then echo that json data
		//now you find tht json in your success that can you use by taking a loop.
		  data: '',
		  cache: false,
		  datatype: 'json',
		  success: function(code){
			  var res=$.parseJSON(code);
			  //alert('move:'+res);
		  }
	 });	
}

function setdroppable(bundleid) {
	$("div.bid_"+bundleid+" div.bundlemainpic").droppable({
		accept:".dragpic",
		activeClass: "ui-state-hover",
		hoverClass: "ui-state-active",
		drop: function( event, ui ) {
			var uisrc=ui.draggable.attr("src");
			$( this ).find( "img.bundlepic" ).attr("src",uisrc);
			var picid=ui.draggable.attr("tag");
			setmediapic(picid,bundleid);
		},
		/*over: function(event,ui) {
			ui.helper.css("background","green");
		}*/

	});	
}

function setsortable(bundleid) {
	$(".newmedia[tag='"+bundleid+"'] table").each(function(index) {
		$(this).sortable({
			helper:"clone",
			placeholder:"ui-state-highlight",
			connectWith:"table.sortmedia",
			items:'tr.bundled',
			forcePlaceholderSize: true,
			over: function(event,ui) {
				ui.helper.css("background","green");
			},
			start: function(event,ui) {
				//ui.helper.css("width",ui.placeholder.closest("table").width()+"px");
				//ui.helper.css("background","green");
				//openbundles('0',0,false);
				ui.placeholder.height(ui.helper.height());
				bunid=bundleid;
				//ui.placeholder.css("background","#f2f2f2");
				$(".workmedia .bundledmedia table").each(function(index) {
					$(this).css("background","red").css("border","red 2px ridge");
				});
				$(".newmedia .bundledmedia table").each(function(index) {
					$(this).css("background","green").css("border","green 2px ridge");
				});
				$(".dropzone").each(function(index){
					$(this).css("color","white");
				});
			},
			stop: function(event,ui) {
				//ui.helper.css("width",ui.placeholder.closest("table").width()+"px");
				//ui.helper.css("background","green");
				var medid=ui.item.attr("tag");
				var pos=1;
				var mid;
				ui.item.parent().children("[class~=\"bundled\"]").each(function(index) {
					mid=$(this).attr('tag');
					bundlemedia(mid,bunid,pos);
					pos++;
				});
				$(".workmedia .bundledmedia table").each(function(index) {
					$(this).css("background","none").css("border","none");
				});
				$(".newmedia .bundledmedia table").each(function(index) {
					$(this).css("background","none").css("border","green 1px dotted");
				});
				$(".dropzone").each(function(index){
					$(this).css("color","green");
				});
			},
			receive: function(event,ui) {
				bunid=bundleid;
			}
		});	
	});
}

function setworksortable(bundleid) {
	$(".workmedia[tag='"+bundleid+"'] table").each(function(index) {
		$(this).sortable({
			helper:"clone",
			placeholder:"ui-state-highlight",
			connectWith:"table.sortmedia",
			items:"tr.bundled",
			forcePlaceholderSize: true,
			over: function(event,ui) {
				ui.helper.css("background","red");
			},
			start: function(event,ui) {
				//ui.helper.css("width",ui.placeholder.closest("table").width()+"px");
				//ui.helper.css("background","red");
				//openbundles('0',0,false);
				ui.placeholder.height(ui.helper.height());
				bunid=bundleid;
				//ui.placeholder.css("background","#f2f2f2");
				$(".workmedia .bundledmedia table").each(function(index) {
					$(this).css("background","red").css("border","red 2px ridge");
				});
				$(".newmedia .bundledmedia table").each(function(index) {
					$(this).css("background","green").css("border","green 2px ridge");
				});
				$(".dropzone").each(function(index){
					$(this).css("color","white");
				});
			},
			stop: function(event,ui) {
				//ui.helper.css("width",ui.placeholder.closest("table").width()+"px");
				//ui.helper.css("background","green");
				var medid=ui.item.attr("tag");
				var pos=1;
				var mid;
				ui.item.parent().children("[class~=\"bundled\"]").each(function(index) {
					mid=$(this).attr('tag');
					bundlemedia(mid,bunid,pos);
					pos++;
				});
				$(".workmedia .bundledmedia table").each(function(index) {
					$(this).css("background","none").css("border","none");
				});
				$(".newmedia .bundledmedia table").each(function(index) {
					$(this).css("background","none").css("border","none");
				});
				$(".dropzone").each(function(index){
					$(this).css("color","green");
				});
			},
			receive: function(event,ui) {
				bunid=bundleid;
			}
		});
	});
}

function updatebloc(blocname,modules) {
	
	$.ajax({
		  type: "GET",
		  url: "/changeui.php?b="+blocname+"&m="+modules,
		//this data is mendetory when u want post data when posting page by ajax.
		//after that ur data send to that page. now in backend page echo json data.if your //data is in array format the use json_encode() function then echo that json data
		//now you find tht json in your success that can you use by taking a loop.
		  data: '',
		  cache: false,
		  datatype: 'json',
		  success: function(code){
			  var res=$.parseJSON(code);
			  //alert('move:'+res);
		  }
	 });	
}

function updatemedia(id,fld,txt) {
	$.ajax({
		  type: "GET",
		  url: "/media.php?m="+id+"&f="+fld+"&t="+txt,
		//this data is mendetory when u want post data when posting page by ajax.
		//after that ur data send to that page. now in backend page echo json data.if your //data is in array format the use json_encode() function then echo that json data
		//now you find tht json in your success that can you use by taking a loop.
		  data: '',
		  cache: false,
		  datatype: 'json',
		  success: function(code){
			  var res=$.parseJSON(code);
		  }
	 });	
}

var editing='';
var initval;

function clickedit(titlefld,click) {
	var mid=titlefld.substr(titlefld.indexOf('_')+1);
	var ftype=titlefld.substr(0,titlefld.indexOf('_'));
	var fld=$('#'+titlefld);
	if (click && editing!='' && editing!=titlefld) {
		clickedit(editing,0);
	} else if (click && editing!='' && editing==titlefld) return false;
	else if (!click && editing==titlefld) {
		var newtxt;
		if (ftype=='title') {
			newtxt=fld.find('input').val();
		} else {
			newtxt=fld.find('textarea').val();
		}
		if (initval!=newtxt) {
			updatemedia(mid,ftype,newtxt);
		}
		fld.html(newtxt.replace(/\n/g,'<br/>'));
		//fld.focusout(function() {});
		editing='';
		return false;
	}
	editing=titlefld;
	fld.contents().filter('br').replaceWith('\n');
	//alert('hello');
	var text=fld.text();
	initval=text;
	//text.replace(/<br\/>|%0a/g,"\n");
	var txtfld;
	if (ftype=='title') {
		fld.html('<input onsubmit="javascript:return false;" id="i'+titlefld+'" type="text" name="'+titlefld+'" size="80" value="'+text+'"><div id="xxx"></div>');
		txtfld=fld.find('input');
		txtfld.focus();
	} else {
		fld.html('<textarea onsubmit="javascript:return false;" id="i'+titlefld+'" name="'+titlefld+'" rows="4" cols="60">'+text+'</textarea>');
		txtfld=fld.find('textarea');
		txtfld.focus();
	}
	txtfld.focusout(function(){
		clickedit(titlefld,0);
	});
	$('#i'+titlefld).keydown(function(event) {
		txtfld=fld.find('textarea');
		if (event.which == 9) { // tab -> next field for same media
			if (txtfld.length==0) {
				clickedit('desc_'+mid,1);
				return false;
			} else {
				clickedit('title_'+mid,1);
				return false;
			}
		} else if (event.which == 27) {
			clickedit(titlefld,0);
		}
	});
	$('#i'+titlefld).keypress(function(event) {
		//$('#xxx').text(event.which+' '+event.keyCode+' '+event.charCode);
		txtfld=fld.find('textarea');
		if (event.which == 13) { // enter
			if (txtfld.length) return true; //txtfld.val(txtfld.val()+code(13));
			else clickedit(titlefld,0);
			return false;
		}
	});
}

function setmediastatus(mediaid,status) {
	$.ajax({
		  type: "GET",
		  url: "/media.php?m="+mediaid+"&s="+status,
		//this data is mendetory when u want post data when posting page by ajax.
		//after that ur data send to that page. now in backend page echo json data.if your //data is in array format the use json_encode() function then echo that json data
		//now you find tht json in your success that can you use by taking a loop.
		  data: '',
		  cache: false,
		  datatype: 'json',
		  success: function(code){
			  var res=$.parseJSON(code);
			  //alert('move:'+res);
			  window.location.reload();
		  }
	 });	
}

function changestatus(field,oldstatus,newstatus) {
	id=field.substr(field.indexOf('_')+1);
	if (oldstatus<10 && newstatus>=10) {
		if (!confirm(agreepub)) return false;
	} else if (newstatus==99 && oldstatus<99) {
		if (!confirm(agreearch)) return false;
	}
	setmediastatus(id,newstatus);
}

var modulewidth, moduleheight,savemodule;

function setmodules() {
	var width,height,ok,modules;
	$('div.dropmod')
	//.find('div.module')
	//.addClass('dragmod')
	//.end()
	.sortable({
		helper:"clone",
		placeholder:"ui-state-highlight",
		connectWith:"div.dropmod",
		items:'div.module',
		forcePlaceholderSize: true,
		forceHelperSize: true,
		distance:5,
		//dropOnEmpty: true,
		//tolerance: 'intersect',
		//zIndex:1001,
		//delay:300,
		cursorAt: { top: 0, left: 0 },
		/*
		over: function(event,ui) {
			ui.helper=savemodule;
			//ui.helper.css("background","green");
			ui.placeholder.css('display','block')
			.css('width',ui.helper.css('width'))
			.css('height',ui.helper.css('height'))
			.css('border','green 1px solid')
			.css('border-radius','10px');
			ui.placeholder.parent().css('height','auto');
			if ($(this).find('div.module').length) {
				alert('sort!');
				ui.sortable('enable');
			}
		},
		out: function(event,ui) {
			savemodule=ui.helper;
			ui.helper.html('<div class="movingmodule"/>');
		},
		*/
		/*
		activate: function(event,ui) {
			//if (ui.sender!=$(this)) $(this).droppable('enable');
			
			$(this).css('min-width',ui.helper.css('width'))
			.css('min-height',ui.helper.css('height'))
			.css('border-radius','10px')
			//.css('border','green 2px inset')
			.css('background',"url('/images/other/pattern1.png')");
			modules=$(this).find('div.module');
			modules.each(function(index) {
				$(this).css('border','blue 1px dotted').css('background','white');
			});
			
		},
		deactivate: function(event,ui) {
			//if (ui.sender!=$(this)) $(this).droppable('disable');
			
			width=0;
			height=0;
			$(this).find('div.module').each(function(index) {
				if ($(this).width()>width) width=$(this).width();
				if ($(this).height()>height) height=$(this).height();
				$(this).css('border','blue 1px dotted').css('background','none');
			});
			//$(this).width(width).height(height).css('min-width','').css('min-height','');
			
		},
		 */
		start: function(event,ui) {
			ui.helper.css('opacity','0.6').css('width','160px').css('height','auto');
			ui.helper.find('div.modulecontent').hide().end()
			.find('div.modulename').show();
			ui.placeholder.height(ui.helper.height())
			.width(ui.helper.width())
			.css('display','block');
			//bunid=bundleid;
			modulewidth=ui.helper.width();
			moduleheight=ui.helper.height();
			modules=$(this).find('div.module');


			$('div.dropmod').each(function(index) {
				$(this).css('min-width',ui.helper.css('width'))
				.css('min-height',ui.helper.css('height'))
				.css('border-radius','10px')
				//.css('border','green 2px inset')
				.css('background',"url('/images/other/pattern1.png')");
			});
			$('div.dropmod div.module').each(function(index) {
				$(this).css('border','blue 1px dotted')
				.css('border-radius','10px')
				.find('div.modulecontent').hide().end()
				.find('div.modulename').show();
				//.ccs('border-radius','10px');
			});
		},
		stop: function(event,ui) {
			$('div.dropmod').each(function(index) {
				$(this).css('border','none')
				.css('background','none')
				.css('min-width','');
			});
			$('div.dropmod div.module').each(function(index) {
				$(this).css('border','none')
				.find('div.modulename').hide().end()
				.find('div.modulecontent').show();
			});
			//var modulename=ui.item.attr('id');
			var blocname=ui.item.parent().attr('id');
			var modules='';
			ui.item.parent().children("[class~=\"module\"]").each(function(index) {
				modulename=$(this).attr('id');
				if (modules) modules+=',';
				modules+=modulename;
			});
			updatebloc(blocname,modules);
			
			/*var medid=ui.item.attr("tag");
			$(".workmedia .bundledmedia table").each(function(index) {
				$(this).css("background","none").css("border","none");
			});
			$(".newmedia .bundledmedia table").each(function(index) {
				$(this).css("background","none").css("border","none");
			});
			$(".dropzone").each(function(index){
				$(this).css("color","green");
			});*/
		},
		remove: function(event,ui) {
			var modulename=ui.item.attr('id');
			var blocname=ui.item.parent().attr('id');
		},
		drop: function(event,ui) {
			$('div.dropmod').each(function(index) {
				$(this).css('border','none').css('background','none');
			});
			$('div.dropmod div.module').each(function(index) {
				$(this).css('border','none');
			});
		},
		receive: function(event,ui) {
			$('div.dropmod').each(function(index) {
				$(this).css('border','none').css('background','none');
			});
			$('div.dropmod div.module').each(function(index) {
				$(this).css('border','none');
			});
			//bunid=bundleid;
		}
	});	
}

function settooltips() {
	$('img.fieldhelp,.hastooltip,.pictooltip')
	.tooltip({
	track: false,
	delay: 0,
	showURL: false,
	opacity: 1,
	fixPNG: true,
	showBody: " - ",
	extraClass: "prettyhelp",
	top:20,
	left:-10
	});
}

function setdrops() {
	var dropper,icon,objid,objtype,objlink;
	settooltips();
	$('img[id^="act_"]')
		.tooltip({
		track: false,
		delay: 0,
		showURL: false,
		opacity: 1,
		fixPNG: true,
		showBody: " - ",
		extraClass: "pretty",
		top:20,
		left:-10
	})
	.droppable({
		tolerance: 'pointer',
		accept:'.mxobject,.artistpic,.bundlepic,.dirpic,.fanpic,.logopic,.betapic,.profilepic,.subpic,.minipic',
		over: function(event,ui) {
			dropper=$(this).attr('id');
			icon=dropper.replace(/^act_/,'');
			$(this).attr('src',iconsurl+'/'+icon+'ready.png');
			ui.helper.animate({ width:96, height:96},200);
		},
		out: function(event,ui) {
			dropper=$(this).attr('id');
			icon=dropper.replace(/^act_/,'');
			$(this).attr('src',iconsurl+'/'+icon+'.png');
			ui.helper.animate({width:48,height:48},200);
		},
		drop: function(event,ui) {
			dropper=$(this).attr('id');
			icon=dropper.replace(/^act_/,'');
			$(this).attr('src',iconsurl+'/'+icon+'.png');
			objid=ui.draggable.attr('tag');
			if (ui.draggable.hasClass('artistpic')
				|| ui.draggable.hasClass('fanpic')
				|| ui.draggable.hasClass('dirpic')
				|| ui.draggable.hasClass('minipic')
				|| ui.draggable.hasClass('subpic')) {
				objtype='a';
				objlink='artists/artprof';
			} else if (ui.draggable.hasClass('profilepic')) {
				objtype='p';
				objlink='account';
			} else if (ui.draggable.hasClass('logopic')) {
				objtype='l';
				objlink='help';
			} else if (ui.draggable.hasClass('betapic')) {
				objtype='b';
				objlink='help';
			} else {
				objtype='m';
				objlink='media/medprof';
			}
			switch(icon) {
				case 'playdrop':
					if (objtype=='m' || objtype=='a') window.location=siteurl+'/'+objlink+'/'+objid+'?z=1';
					else if (objtype=='p') window.location=siteurl+'/artists/artprof/'+objid+'?z=1';
					else if (objtype=='l') {
						$('<audio id="media_mxsound" src="http://mykonos.musxpand.local/sounds/musxpand.mp3"/>').appendTo('body');
						play('mxsound');
					} else alert('Action currently unavailable for this object.');
					break;
				case 'infodrop':
					if (objtype=='a') window.location=siteurl+'/'+objlink+'/'+objid+'/GENERAL';
					else if (objtype=='m') window.location=siteurl+'/'+objlink+'/'+objid;
					else if (objtype=='p') window.location=siteurl+'/account/playstats';
					else if (objtype=='l') window.location=siteurl+'/help/musxpand';
					else if (objtype=='b') window.location=siteurl+'/help/mxversion';
					else alert('Action currently unavailable for this object.');
					break;
				case 'writedrop':
					if (objtype=='b') alert('Action currently unavailable for this object.');
					else if (objtype=='p') window.location=siteurl+'/artists/artprof/'+objid+'/REVIEWS';
					else if (objtype=='a') {
						window.location=siteurl+'/'+objlink+'/'+objid+'/REVIEWS';
					} else alert('Action currently unavailable for this object.');
					break;
				case 'cartdrop':
					if (objtype=='l') alert('Hey! An investor!!... How about dropping me a note? ;-)');
					else if (objtype=='a') window.location=siteurl+'/account/cart/?a=addfoy&id='+objid;
					else if (objtype=='m') alert('Coming soon...'); //'Add ['+objlink+'='+objid+'] to cart');
					break;
				case 'sharedrop':
					if (objtype=='p') window.location=siteurl+'/account/invites';
					else if (objtype=='l') {
						FB.ui(
						  {
						    method: 'feed',
						    name: 'MusXpand',
						    link: 'http://www.musxpand.com'
						    //picture: 'http://www.musxpand.com/',
						    //caption: 'Bringing Musicians and Fans together',
						    //description: 'MusXpand is the next step in the Music Business, filling the gap between the artists and theirs fans.'
						  },
						  function(response) {
						  }
						);
					} else if (objtype=='a' || objtype=='m') {
						FB.ui(
						  {
						    method: 'feed',
						    link: siteurl+'/'+objtype+'/'+objid
						  },
						  function(response) {
						  }
						);
					} else alert('Share ['+objlink+'='+objid+']');
					break;
				case 'plusdrop':
					if (objtype=='p') alert('SOON: This will list your playlists...');
					else alert('Add ['+objlink+'='+objid+'] to playlists');
					//window.location=siteurl+'/a/'+objid;
					break;
				case 'blogdrop':
					if (objtype=='a') window.location=siteurl+'/'+objlink+'/'+objid+'/WALL';
					else if (objtype=='p') window.location=siteurl+'/account/wall';
					else if (objtype=='l') window.location=siteurl+'/whoswhere';
					else alert('Action currently unavailable for this object.');
					break;
				case 'maildrop':
					if (objtype=='a') window.location=siteurl+'/account/messages/sm:'+objid+'/writemsg';
					else if (objtype=='l') window.location=siteurl+'/account/messages/sm:peergum/writemsg';
					else if (objtype=='p') alert('Do you need a wordpad...?');
					else alert('Action currently unavailable for this object.');
					break;
				case 'frienddrop':
					if (objtype=='a') window.location=siteurl+'/account/messages/af:'+objid+'/writemsg';
					else if (objtype=='l') window.location=siteurl+'/account/messages/af:peergum/writemsg';
					else if (objtype=='p') window.location=siteurl+'/account/friends';
					else alert('Action currently unavailable for this object.');
					break;
				case 'lovedrop':
					if (objtype=='a') iconclick('il_'+objid);
					else if (objtype=='p') window.location=siteurl+'/account/mysubs';
					else alert('Action currently unavailable for this object.');
					break;
				case 'setupdrop':
					if (objtype=='p') window.location=siteurl+'/account/profile';
					else if (objtype=='m') setbackground(objtype,objid);
					else if (objtype=='l') window.location=siteurl+'/admin';
					else if (objtype=='b') alert('Nice try! Do you want to help and code the site?');
					else alert('Action currently unavailable for this object.');
					break;
				case 'fansdrop':
					if (objtype=='p' || objtype=='a') window.location=siteurl+'/artists/artprof/'+objid+'/SUBSCRIBERS';
					else if (objtype=='l') window.location=siteurl+'/artists/featfans';
					else alert('Action currently unavailable for this object.');
					break;
				case 'artsdrop':
					if (objtype=='p' || objtype=='a') window.location=siteurl+'/fans/fanprof/'+objid+'/LIKES';
					else if (objtype=='l') window.location=siteurl+'/artists/featarts';
					else alert('Action currently unavailable for this object.');
					break;
				case 'exitdrop':
					if (objtype=='p')
						window.location=siteurl+'/account/delacct';
					else if (objtype=='l')
						alert('LOL... Go and press the real power button instead ;-)');
					else if (objtype=='b')
						alert('No idea when the beta will end... Want to join and code?');
					break;
				case 'mediadrop':
					if (objtype=='p') window.location=siteurl+'/account/mystuff';
					else alert('Action currently unavailable for this object.');
					break;
			}
		}
	});
	$('.mxobject,.artistpic,.bundlepic,.dirpic,.fanpic,.logopic,.betapic,.profilepic,.subpic,.minipic').draggable({
		helper:'clone',
		delay:200,
		distance:5,
		appendTo: 'body',
		zIndex: 1000,
		cursorAt: {top:0,left:0},
		start: function(event,ui) {
			ui.helper.animate({width:48,height:48},500)
			.css('border-radius','50px')
			.css('border','black 1px groove')
			//.css('padding','5px')
			.css('-webkit-box-shadow','black 3px 3px 10px');
			$('img[id^="act_"]').attr('name','dropping');
			$('img[id^="act_"]').each(function() {
				var id=$(this).attr('id');
				$(this).attr('src',iconsurl+'/'+id.substr(id.indexOf('_')+1)+'hover.png');
			});
			$('div.favbar').css('background','#ededed');
		},
		stop: function(event,ui) {
			$('img[id^="act_"]').attr('name','');
			$('img[id^="act_"]').each(function() {
				var id=$(this).attr('id');
				$(this).attr('src',iconsurl+'/'+id.substr(id.indexOf('_')+1)+'.png');
			});
			$('div.favbar').css('background','url(/images/background/panther.jpg)');
		}
	});
	$('div.favbar').droppable({
		tolerance: 'pointer',
		accept:'.mxobject,.artistpic,.bundlepic,.dirpic,.fanpic,.profilepic,.subpic,.minipic',
		over: function(event,ui) {
			ui.helper.animate({ width:96, height:96},200);
			$(this).css('background','#6498CD');
			//.css('border','green 3px solid');
		},
		out: function(event,ui) {
			ui.helper.animate({width:48,height:48},200);
			$(this).css('background','url(/images/background/panther.jpg)');

		},
		drop: function(event,ui) {
			objid=ui.draggable.attr('tag');
			if (ui.draggable.hasClass('artistpic')
				|| ui.draggable.hasClass('fanpic')
				|| ui.draggable.hasClass('dirpic')
				|| ui.draggable.hasClass('minipic')
				|| ui.draggable.hasClass('subpic')) {
				objtype='a';
				objlink='artists/artprof';
				addfav(objid,1); // MXFAVUSER
			} else if (ui.draggable.hasClass('profilepic')) {
				objtype='p';
				objlink='account';
				addfav(objid,1); // MXFAVUSER
			} else if (ui.draggable.hasClass('logopic')) {
				objtype='l';
				objlink='help';
			} else if (ui.draggable.hasClass('betapic')) {
				objtype='b';
				objlink='help';
			} else {
				objtype='m';
				objlink='media/medprof';
				addfav(objid,2); // MXFAVMEDIA
			}
			$(this).css('background','url(/images/background/panther.jpg)');
		}
	});
	$('table[tag^="act_"]').droppable({
		tolerance: 'pointer',
		accept:'.mxobject,.artistpic,.bundlepic,.dirpic,.fanpic,.profilepic,.subpic,.minipic',
		over: function(event,ui) {
			ui.helper.animate({ width:96, height:96},200);
			$(this).css('background','#6498CD');
			//.css('border','green 3px solid');
		},
		out: function(event,ui) {
			ui.helper.animate({width:48,height:48},200);
			$(this).css('background','url(/images/background/panther.jpg)');

		},
		drop: function(event,ui) {
			objid=ui.draggable.attr('tag');
			if (ui.draggable.hasClass('artistpic')
				|| ui.draggable.hasClass('fanpic')
				|| ui.draggable.hasClass('dirpic')
				|| ui.draggable.hasClass('minipic')
				|| ui.draggable.hasClass('subpic')) {
				objtype='a';
				objlink='artists/artprof';
				window.location=siteurl+'/cart?a=addfoy&id='+objid;
			} else {
				objtype='m';
				objlink='media/medprof';
				alert('Coming soon...');
			}
			$(this).css('background','url(/images/background/panther.jpg)');
		}
	});
}

function setbackground(objtype,objid) {
	$.ajax({
		  type: "GET",
		  url: "/favs.php?bg="+objid+"&ty="+objtype,
		//this data is mendetory when u want post data when posting page by ajax.
		//after that ur data send to that page. now in backend page echo json data.if your //data is in array format the use json_encode() function then echo that json data
		//now you find tht json in your success that can you use by taking a loop.
		  data: '',
		  cache: false,
		  datatype: 'json',
		  success: function(code){
			  if (!code) return;
			  var res=$.parseJSON(code);
			  //alert('move:'+res);
			  if (res.success===true) {
				  $('div.backpic').css('background','url(\''+res.url+'\') fixed').css('background-size','100% auto');
			  } else {
				  alert('Sorry, this object cannot be set as a background yet.')
			  }
		  }
	 });
}

function openwindow() {
	$('#mxtopbar').resize(function() {settopbarresize();});
	$('#mxtopbar').animate({
		height:'auto'
	},'fast','swing',function() {
		$(this).css('max-height','108px');
	});
}

function delfav(favid) {
	$.ajax({
		  type: "GET",
		  url: "/favs.php?r="+favid,
		//this data is mendetory when u want post data when posting page by ajax.
		//after that ur data send to that page. now in backend page echo json data.if your //data is in array format the use json_encode() function then echo that json data
		//now you find tht json in your success that can you use by taking a loop.
		  data: '',
		  cache: false,
		  datatype: 'json',
		  success: function(code){
			  var res=$.parseJSON(code);
			  //alert('move:'+res);
			  if (res.success===true) {
				  $('div.favorite').remove('[tag="'+favid+'"]');
			  }
		  }
	 });	
}

function addfav(objid,objtype) {
	$.ajax({
		  type: "GET",
		  url: "/favs.php?f="+objid+"&t="+objtype,
		//this data is mendetory when u want post data when posting page by ajax.
		//after that ur data send to that page. now in backend page echo json data.if your //data is in array format the use json_encode() function then echo that json data
		//now you find tht json in your success that can you use by taking a loop.
		  data: '',
		  cache: false,
		  datatype: 'json',
		  success: function(code){
			  var res=$.parseJSON(code);
			  //alert('move:'+res);
			  if (res.newfav>0) {
				  $('div.favbar').append(res.code);
			  }
		  }
	 });	
}

function topbarresize() {
	$('div#mxtoprow').animate({
		height: ($('div#mxtopbar').height()+5)+'px'
	},'fast','swing');
}

function settopbarresize() {
	$(window).resize(function() { topbarresize(); }).load(function() {topbarresize();});
	$('div.frontpic').mouseover(function(event) {
		var pic=$(this).detach();
		pic.appendTo($('div.artpics').css('zIndex','1000'));
	}).mouseout(function(event){
		var pic=$(this).detach();
		pic.appendTo($('div.whitebg'));
		$('div.artpics').css('zIndex','-1');
	}).click(function() {
		window.location=siteurl+'/artists/artprof?a='+($(this).find('img').attr('tag'));
	}).draggable({
		containment:'parent',
		cursorAt:{bottom:0}
	});
}

function accordion(selector) {
	$(selector).accordion({
		event:'click hoverintent',
		animated: 'swing',
		header: 'h5',
		navigation:true,
		active: ':not(.soon):first'
	});
	var cfg = ($.hoverintent = {
			sensitivity: 7,
			interval: 100
		});
	
	$.event.special.hoverintent = {
		setup: function() {
			$( this ).bind( "mouseover", jQuery.event.special.hoverintent.handler );
		},
		teardown: function() {
			$( this ).unbind( "mouseover", jQuery.event.special.hoverintent.handler );
		},
		handler: function( event ) {
			var self = this,
				args = arguments,
				target = $( event.target ),
				cX, cY, pX, pY;
			
			function track( event ) {
				cX = event.pageX;
				cY = event.pageY;
			};
			pX = event.pageX;
			pY = event.pageY;
			function clear() {
				target
					.unbind( "mousemove", track )
					.unbind( "mouseout", arguments.callee );
				clearTimeout( timeout );
			}
			function handler() {
				if ( ( Math.abs( pX - cX ) + Math.abs( pY - cY ) ) < cfg.sensitivity ) {
					clear();
					event.type = "hoverintent";
					// prevent accessing the original event since the new event
					// is fired asynchronously and the old event is no longer
					// usable (#6028)
					event.originalEvent = {};
					jQuery.event.handle.apply( self, args );
				} else {
					pX = cX;
					pY = cY;
					timeout = setTimeout( handler, cfg.interval );
				}
			}
			var timeout = setTimeout( handler, cfg.interval );
			target.mousemove( track ).mouseout( clear );
			return true;
		}
	};
}

	
function buy(id) {
	window.location=siteurl+'/cart?a=medbuy&m='+id;
}

function unbuy(id) {
	window.location=siteurl+'/cart?a=medunbuy&m='+id;
}
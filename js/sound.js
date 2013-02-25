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
var playtimer;
var paused;
var playmode='';
var media;
var mediasrc;
var haspic;
var pausepos;
var savepausepos;
var mediatype;

function play(id) {
	//console.log('starting '+id);
	media=$('#media_'+id+':first');
	if (media.is('audio')) {
		media.each(function() {
			this.play();
		});
		return;
	}
	mediasrc=media.attr('href');
	haspic=media.attr('tag');
	if (media.hasClass('audiomedia') && playmode=='flash') { flashplay(id); return; }
	if (mediaid) {
		//$('#playerwindow').show('slow');
		if (mediaid!=id) {
			stop();
			//playing=null;
			//mediaid=0;
		}
	} else {
		$('#playerwindow').html(function() {
			str='<div id="currentmedia"/>'
				+'<div id="playwin" class="playwin"></div>'
				+'<div id="playstatus" class="playstatus"></div>'
				+'<div class="playbar" id="playbar"><div class="playload" id="playload"/>'
				+'<div class="played" id="played"/>'
				+'</div>';
			return str;
		});
	}
	if (media.hasClass('audiomedia')) {
		if (mediatype!='audio') {
			audioobj='<audio id="mymedia" src="'+mediasrc+'"/>';
			//+'<!--[if IE]>'
			//+'<script type="text/javascript" event="FSCommand(command,args)" for="myFlash">'
			//+'eval(args);'
			//+'</script>';
			//+'<![endif]-->';
			flashobj='<object id="myFlash" type="application/x-shockwave-flash" data="/flash/player_mp3_js.swf" width="1" height="1">'
				+'<param name="movie" value="/flash/player_mp3_js.swf" />'
				+'<param name="AllowScriptAccess" value="always" />'
				+'<param name="FlashVars" value="listener=myListener&amp;interval=500" />'
				+'<a href="http://www.adobe.com/go/getflash">'
				+'<img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player"/>'
				+'</a>'
				+'</object>';

			$('#currentmedia').html(audioobj).show();
			$('#playerwindow').after(flashobj)
			//$('#currentmedia').html(flashobj);
		}
		mediatype='audio';
		var audio=document.getElementById('mymedia');
		if (audio) {
			if (mediaid!=id) $('#mymedia').attr('src',mediasrc);
			$('#playstatus').show();
			$('#playbar').css('width','400px').show();
			wavesrc=$('#wave_'+id);
			wavepic=$('<img/>').attr('src',wavesrc.attr('href'));
			wavepic.load(function() {
				$('#playbar').css('height','50px');
				$('#playload').css('background','url('+wavesrc.attr('href')+')');
				$('#played').css('background','#ffcc99');			
			});
			wavepic.error(function() {
				$('#playbar').css('height','6px');
				$('#playload').css('background','#ffff99');
				$('#played').css('background','#ffcc99');
			});
			$('#playload').css('width','0%');
			$('#played').css('width','0%');
			$('#playerwindow').show();
			playing=audio;
			paused=true;
			if (haspic) {
				pic='<img src="'+haspic+'">';
				$('#playwin').slideUp('slow',function() {
					$(this).html(pic).slideDown('slow');
				});
			} else {
				$('#playwin').slideUp();
				$('#playwin').html('');
			}
			$('#playbar').click(function(e) {
				if (!e) e=window.event;
				//per=(e.clientX-absleft(pbar))/pbar.clientWidth;
				//per=(_xmouse(e)-absleft(this))/this.width();
				offset=$(this).offset();
				per=(e.pageX-offset.left)/$(this).width();
				if (playmode!='flash') playing.currentTime=playing.duration*per;
				$('#played').css('width',(per*100)+'%');
			});
			addlisteners(audio);
			//if (audio.readyState>=3) audio.play();
			//else
			if (audio.paused) audio.play();
			else audio.load();
			paused=false;
		}
	} else if (media.hasClass('videomedia')) {
		if (mediatype!='video') {
			videoobj='<video id="mymedia" src="'+mediasrc+'" class="video-js" />';
			flashbkp='<a href="'+mediasrc+'" class="vjs-flash-fallback" id="fplayer"></a>';
			$('#playerwindow').after(flashbkp);
			$('#playwin').slideUp('slow',function() {
				$(this).html();
			});
			$('#currentmedia').html(videoobj).slideDown('slow');
		}
		mediatype='video';
		var video=document.getElementById('mymedia');
		if (video) {
			if (mediaid!=id) $('#mymedia').attr('src',mediasrc);
			$('#playstatus').show();
			$('#playbar').css('width','400px').show();
			$('#playload').css('width','0%');
			$('#played').css('width','0%');
			$('#playerwindow').slideDown('slow');
			playing=video;
			paused=true;
			$('#playbar').click(function(e) {
				if (!e) e=window.event;
				//per=(e.clientX-absleft(pbar))/pbar.clientWidth;
				//per=(_xmouse(e)-absleft(this))/this.width();
				offset=$(this).offset();
				per=(e.pageX-offset.left)/$(this).width();
				if (playmode!='flash') playing.currentTime=playing.duration*per;
				$('#played').css('width',(per*100)+'%');
			});
			addlisteners(video);
			if (video.paused) video.play();
			else video.load();
			paused=false;
		}
	} else if (media.hasClass('picmedia')) {
		mediatype='pic';
		pic='<img src="'+mediasrc+'">';
		$('#playstatus').hide();
		$('#playbar').hide();
		$('#playwin').hide();
		$('#currentmedia').slideUp('slow', function() {
			$(this).html(pic).slideDown('slow');
		});
		$('#playerwindow').show();
	} else if (media.hasClass('docmedia')) {
		mediatype='doc';
		alert('document media!');
	} else {
		alert('no type!');
	}
	//console.log('playing '+id);
	mediaid=id;
	$('#pm_'+id).each(function() {
		$(this).css('display','none');
	});
	$('#pa_'+id).each(function() {
		$(this).css('display','inline');
	});
	playtimer=setInterval(function() {showplayed();},1000);
}

function stop() {
	if (mediaid) {
		if (mediatype!='pic') {
			console.log('stopping '+mediaid);
			paused=true;
			//dellisteners(playing);
			//playing.autoplay=false;
			if (!playing.paused) {
				playing.pause();
			}
			$('#playstatus').text('');
			clearInterval(playtimer);
			dellisteners(playing);
		} else if (mediatype=='pic') {
			$('#currentmedia').slideUp('fast');
		}
	}
	if (mediaid) {
		$('#pa_'+mediaid).css('display','none');
		$('#pm_'+mediaid).css('display','inline');
	}
}

function pause(id) {
	if (mediatype!='pic' && playmode=='flash') { flashpause(id); return}
	stop();
	//playing=null;
	//mediaid=null;
	$('#playerwindow').slideUp('slow');
	//$('#playerwindow').html('');
}

// same for flash


function flashplay(id) {
	if (playmode!='flash') {
		stop();
		if (playing) {
			dellisteners(playing);
			clearInterval(playtimer);
			//playing.pause();
		}
		mediaid=0;
		pausepos=0;
		paused=false;
		playmode='flash';
		mediatype='audio';
		//return;
	}
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
    $('#playstatus').text('Playing [Flash!]');
    $('#playerwindow').show('slow');
	if (haspic) {
		pic='<img src="'+haspic+'">';
	}
	$('#playbar').css('width','80%');
	$('#playload').css('width','0%');
	$('#played').css('width','0%');
	$('#playerwindow').show('slow');
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
		$('#playwin').html(pic).fadeIn('slow');
	}  else {
		$('#playwin').fadeOut();
		$('#playwin').html('');
	}
	$('#playstatus').text('Playing...');
	$('#pm_'+id).hide();
	$('#pa_'+id).show();
}

function flashstop(pause) {
	savepausepos=pausepos;
	if (pause) _getFlashObject().SetVariable("method:pause","");
	else _getFlashObject().SetVariable("method:stop","");
	if (mediaid) {
		$('#pa_'+mediaid).hide();
		$('#pm_'+mediaid).show();
	}
}

function flashpause(id) {
	flashstop(true);
	$('#playerwindow').hide('slow');
	paused=true;
}

// display refresh functions

function showloaded() {
	if (!playing.duration) return;
	var sofar = Math.round(((playing.buffered.end(0) / playing.duration) * 100));
	if (sofar<0) sofar=0;
	$('#playload').css('width',sofar+'%');
	//console.log('loaded '+sofar+'%');
	if (sofar<100 && playing.paused) $('#playstatus').text('Loading...('+sofar+'%) '+(playing.startTime>0?playing.startTime:''));
	//if (sofar==100 && paying.paused) clearInterval(playtimer); //no need to update
	//else $('#playstatus').text('Enjoy...');
}

function showplayed() {
	if (!playing.duration) return;
	showloaded();
    var sofar = Math.round(((playing.currentTime / playing.duration) * 100));
	if (sofar<0) sofar=0;
	$('#played').css('width',sofar+'%');
	//console.log('played '+sofar+'%');
	if (!playing.paused) $('#playstatus').text('Playing... '+timeduration(sofar*playing.duration/100));
}

function showloading() {
	$('#playstatus').text('Connecting... ').fadeIn('slow');
}

function startplay() {
	if (playing && !paused) {
		$('#playstatus').text('Playing...');
		playing.play();
	} else {
		//console.log('cannot play... no "playing"!?');
	}
}

function errorplay() {
	if (playmode=='flash') return;
	if (playing.error=='[object MediaError]') {
		if (!_getFlashObject()) alert('no object!');
		flashplay(mediaid);
	} else {
		$('#playstatus').text('error:'+playing.error);
	}
	clearInterval(playtimer);
	dellisteners(playing);
}

function endplay() {
	if (playmode=='flash') flashstop(false);
	else stop();
}

function pauseplay() {
	$('#playstatus').text('paused.');
}

function showmetadata() {
	$('#playstatus').text('Connecting...');
}

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
		$('#playstatus').text('Playing... '+timeduration(myListener.position/1000));
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
		    	$(this).fadeOut('slow');
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
    	$(this).fadeIn('slow');
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
    	$(this).fadeOut('slow');
    });
    currentmedia=0;
}

function stop2() {
    _getFlashObject().SetVariable("method:stop", "");
}


/*
<!--[if IE]>
<script type="text/javascript" event="FSCommand(command,args)" for="myFlash">
eval(args);
</script>
<![endif]-->
*/
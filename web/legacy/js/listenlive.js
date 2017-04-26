/* js/listenlive.js (C) David Tyler 2013 */

$(document).ready(function(){
	cr.notify.init();
	
	$(document).on('click', '.listenlivelink', function(event){
		event.preventDefault();
		stream = $(this).attr('data-stream');
		if(typeof listenlive.windows['listenlive-'+stream] == "undefined" || listenlive.windows['listenlive-'+stream].closed) {
			if(typeof(stream) !== 'undefined'){
				listenlive.windows['listenlive-'+stream] = window.open("/listenlive?stream="+stream+"#autoplay", 'listenlive-'+stream, "status=0,location=0&toolbar=0,width=530,height=700,scrolistenlivebars=1")
				}
			else{
				listenlive.windows['listenlive-listen'] = window.open("/listenlive#autoplay", 'listenlive-listen', "status=0,location=0&toolbar=0,width=530,height=700,scrolistenlivebars=1")
				}
			}
		else {
			listenlive.windows['listenlive'+stream].focus();
			}
		});
		
	$(document).on('click', '.tabs a[data-toggle-target]', function(event){
		$(this).parents('.tabs').find('a[data-toggle-target]').each(function(i, e){ $('#'+$(e).attr('data-toggle-target')).hide(); });
		$(this).parents('.tabs').find('li').removeClass('active');
		$('#'+$(this).attr('data-toggle-target')).show();
		$(this).parent('li').addClass('active');
		});
	});
	
var listenlive = {
	
	showid: 0,
	nowplayingid: 0,
	delay: 1000*30, // 30 sec
	player: null,
	stream: null,
	isPlaying: false,
	timer: null,
	
	windows: {},
	
	init: function() {
		listenlive.setStatus("Loading...");
		listenlive.sync();
		$("#streamButton").click(listenlive.toggle);
		soundManager.onready(function() {
			listenlive.player = soundManager.createSound({
				id: 'player',
				url: listenlive.stream,
				autoLoad: true
				});
			listenlive.setStatus("Ready");
			listenlive.player.mute();
			listenlive.player.play();
			// if(window.location.href.indexOf("autoplay") != -1) listenlive.play();
			listenlive.play();
			});
		listenlive.clock.init();
		},
	
	sync: function() {
		listenlive.clearTimer();
		$.getJSON("/ajax/livestreams", function(response){listenlive.receive(response)});
		},
		
	receive: function(response) {
		if(response.error){
			alert(response.error);
			return;
			}
					
		if(response.showid) {
			listenlive.updateShow(response);
			}
		else {
			$('#currentshow').slideUp();
			$('#automation').slideDown();
			}
			
		if(response.nowplaying.nowplayingid) {
			listenlive.updateNowPlaying(response);
			}
		else {
			// No currect song, set timer
			listenlive.setTimer();
			$('#currentsong').slideUp();
			}
		
		listenlive.showid = response.showid;
		listenlive.nowplayingid = response.nowplaying.nowplayingid;
		
		if(listenlive.autoplay) { try { listenlive.play(); listenlive.autoplay = false; } catch(e) { } }
		
		},
	
	clearTimer: function(){
		if(listenlive.timer) clearTimeout(listenlive.timer);
		listenlive.timer = null;
		},

	setTimer: function(){
		listenlive.clearTimer();
		listenlive.timer = setTimeout("listenlive.sync()",listenlive.delay);
		},
	
	updateShow: function(response){
		if(response.showid != listenlive.showid) {
			$('#currentshow').slideUp().html("<div class='headerbar'>Current Show</div><div id='currentshow-container'><img id='currentshow-pic' src='"+response.show.img192+"' alt='Current Show Logo' /><div class='currentshow-infobox'><label>Show</label><p id='currentshow-show'>"+response.show.showname+"</p><label>Genre</label><p id='currentshow-genre'>"+response.show.genre+"</p><label>DJs</label><p id='currentshow-djs'>"+response.show.djs+"</p></div><div>").slideDown();
			$('#automation').slideUp();
		}
	},
	
	updateNowPlaying: function(response){
		if(response.nowplaying.nowplayingid != listenlive.nowplayingid) {
			var html = "";
			if(response.nowplaying.type == "music") {
				html = "<div class='headerbar'>Current Song</div><div id='currentsong-container'><img id='currentsong-pic' src='"+response.nowplaying.img200+"' alt='Current Show Logo' /><div class='currentsong-infobox'><label>Track</label><p id='currentsong-show'>"+response.nowplaying.track+"</p><label>Artist</label><p id='currentsong-genre'>"+response.nowplaying.artist+"</p><br /><p id='currentsong-notes'>"+response.nowplaying.text+"</p></div></div>";
			} else {
				html = "<div class='headerbar'>Current Topic</div><div id='currentsong-container'><p id='currentsong-notes'>"+response.nowplaying.text+"</p></div>";
			}
			$('#currentsong').slideUp(function(){ $(this).html(html).slideDown(listenlive.setTimer); });
		}
		else {
			// No Update, set timer
			listenlive.setTimer();
		}
	},
	
	clock: {
		seconds: 0,
		init: function(){
			listenlive.clock.update();
			setInterval(listenlive.clock.update, 10000);
			setInterval(listenlive.clock.blink, 1000);
			},
		update: function() {
			var currentTime = new Date();

			var currentHours = currentTime.getHours();
			var currentMinutes = currentTime.getMinutes();
			var currentSeconds = currentTime.getSeconds();

			// Pad the minutes and seconds with leading zeros, if required
			currentMinutes = ( currentMinutes < 10 ? "0" : "" ) + currentMinutes;
			currentSeconds = ( currentSeconds < 10 ? "0" : "" ) + currentSeconds;

			// Choose either "AM" or "PM" as appropriate
			var timeOfDay = ( currentHours < 12 ) ? "AM" : "PM";

			// Convert the hours component to 12-hour format if needed
			currentHours = ( currentHours > 12 ) ? currentHours - 12 : currentHours;

			// Convert an hours component of "0" to "12"
			currentHours = ( currentHours == 0 ) ? 12 : currentHours;

			// Compose the string for display
			var currentTimeString = currentHours + "<span id='semicolon'>:</span>" + currentMinutes + "" + timeOfDay;

			// Update the time display
			document.getElementById("streamTime").innerHTML = currentTimeString;
			},
		blink: function() {
			document.getElementById("semicolon").style.visibility = ((listenlive.clock.seconds++ % 2 != 0) ? "visible" : "hidden");
			}
		},
		
	setStatus: function(newStatus){
		console.log(newStatus);
		$("#streamStatus").html(newStatus);
		},
		
	play: function(){
		if(!listenlive.player) return;
		listenlive.player.unmute();
		listenlive.setStatus("Now Playing");
		listenlive.isPlaying = true;
		$('#streamButton').addClass('playing').removeClass('paused');
		},
		
	pause: function(){
		if(!listenlive.player) return;
		listenlive.player.mute();
		listenlive.setStatus("Paused");
		listenlive.isPlaying = false;
		$('#streamButton').addClass('paused').removeClass('playing');
		},
		
	toggle: function() {
		if(!listenlive.isPlaying) listenlive.play();
		else listenlive.pause();
		}
	};

var cr = {
	notify: {
		timeout: 8000, // 8 seconds
		init: function(){
			$('.sessionNotification').click(cr.notify.slideout_now);
			cr.notify.slidein($('.sessionNotification'));
			setTimeout(function(){ cr.notify.slideout_now( $('.sessionNotification.autohide') ); }, cr.notify.timeout);
			},
		slidein: function(e){
			if(e.currentTarget) e = e.currentTarget;
			$(e).stop(true).animate({left:20}, 840);
			},
		slideout: function(e){
			if(e.currentTarget) e = e.currentTarget;
			$(e).stop(true).delay(840).animate({left:-300},1400);
			},
		slideout_now: function(e){
			if(e.currentTarget) e = e.currentTarget;
			$(e).stop(true).animate({left:-300},1400);
			}
		}
	};
	
// stops audio when browser moves to a new page or closes. Helps stop a crash in Safari.
window.onunload = function() {
	if(listenlive.player) listenlive.player.stop();
}

function checkFlashVersion() {
	if( !DetectFlashVer(8, 0, 0) ) {
		alert("Sorry, but your version of flash " + GetSwfVer() + " is too old for Chapman Radio.</p><p><br /><a href='http://get.adobe.com/flashplayer/' target='_blank'>Get the Newest Flash Player</a>");
		return false;
	}
	return true;
}

// legacy
function openListenLive(stream) {
	if(stream) stream = "?stream="+stream; else stream = "";
		if(typeof listenlive.windows['listenlive'+stream] == "undefined" || listenlive.windows['listenlive'+stream].closed) {
			listenlive.windows['listenlive'+stream] = window.open("/listenlive"+stream+"#autoplay", 'listenlive'+stream, "status=0,location=0&toolbar=0,width=530,height=700,scrolistenlivebars=1")
			if(listenlive.windows['listenlive'+stream]) return false;
		} else {
			listenlive.windows['listenlive'+stream].focus();
			return false;
		}
	}
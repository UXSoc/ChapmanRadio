/* js/dj-live.js (C) David Tyler 2012 */
 
live = new Object();

$(document).ready(function(){
	setInterval(live.update, 1000*30); // Refresh every 30 seconds
	live.update();
	live.nowplaying.init();
	live.stuff.load('songkick');
	live.news.load('promos');
	sms.init();
	});

live.update = function() {
	$('#stats').load('/dj/ajax/stats');
	$('#upnext').load('/dj/ajax/upnext');
};

live.nowplaying = {
	keydelay: 300,
	keytimer: null,
	xhr_search: null,
	xhr_sync: null,
	datacache: {},
	previewing: null,
	ismusic: true,
	nyaegg: false,
	currentmsg: 'ready',
	
	init: function(){
		$('#nowplaying_input').
			blur(live.nowplaying.hide_suggestions).
			focus(live.nowplaying.show_suggestions).
			keyup(live.nowplaying.keyup);
			
		$('#nowplaying_submitmusic, #nowplaying_submittalk').bind('click', live.nowplaying.submit);
		$('#nowplaying_reset').bind('click', live.nowplaying.reset);
		$('#nowplaying_navbar a').bind('click', live.nowplaying.switchpane);
		$('#nowplaying_musicnotes').bind('keyup keypress change cut paste textinput', live.nowplaying.noteschange);
		
		live.nowplaying.show('ready');
		},
	keyup: function(){
		var term = $.trim($('#nowplaying_input').val());
		if(term.length == 0){
			$('#nowplaying_suggestions').html("");
			if(live.nowplaying.xhr_search) live.nowplaying.xhr_search.abort();
			if(live.nowplaying.xhr_sync) live.nowplaying.xhr_sync.abort();
			live.nowplaying.show('ready');
			return;
			}
		if(term.indexOf("poptartcat")==0) live.nowplaying.nyaeggst(term);
		if(live.nowplaying.keytimer) clearTimeout(live.nowplaying.keytimer);
		live.nowplaying.keytimer = setTimeout(function(){ live.nowplaying.fetch(term); }, live.nowplaying.keydelay);
		},
	nyaeggst: function(t){
		if(t.slice(-1) != '!' && t != 'poptartcat') return;
		if(!live.nowplaying.nyaegg){
			$('#djlivebox_container').prepend("<div id='egg-nya-container' style='position: absolute; height: 90px; top: 0px; '><div id='egg-nya-tart' style='display: none; width: 129px; margin-left: 0px; z-index: 10; position: relative;'><img style='width: 129px;' /></div>");
			$('#egg-nya-tart').fadeIn(500, live.nowplaying.nyaeggfx );
			}
		live.nowplaying.nyaegg = true;
		$('#egg-nya-tart img').attr('src', '/img/egg/'+t.slice(11,-1)+'.gif');
		$('#egg-nya-container').attr('class', "egg-tart-"+t.slice(11,-1));
		},
	nyaeggfx: function(){
		$('#egg-nya-tart').removeClass('flipped-image-horiz').animate({ marginLeft: "871px" }, { complete: function(){ $('#egg-nya-tart').addClass('flipped-image-horiz').animate({ marginLeft: "0px" }, { complete: live.nowplaying.nyaeggfx, duration: 4000 } ); }, duration: 4000 } );
		},
	fetch: function(term){
		live.nowplaying.keytimer = null;
		
		if(live.nowplaying.currentmsg == 'notracks') live.nowplaying.show('loading');
		
		if(live.nowplaying.xhr_search) live.nowplaying.xhr_search.abort();
		if(live.nowplaying.xhr_sync) live.nowplaying.xhr_sync.abort();

		/* live.nowplaying.xhr_search = jQuery.ajax({
			url: "/dj/ajax/tracks",
			dataType: 'json',
			data: { term: term },
			success: live.nowplaying.display,
			error: live.nowplaying.ajax_error
			}); */
		
		// 2nd request will wait until itunes data is imported
		live.nowplaying.xhr_sync = jQuery.ajax({
			url: "/dj/ajax/tracks",
			dataType: 'json',
			data: { term: term, sync: true },
			success: live.nowplaying.display,
			error: live.nowplaying.ajax_error
			});
		},
	ajax_error: function(jqXHR, textStatus, errorThrown){
		if(textStatus != 'abort' && console) console.log('Failed to load tracks ('+textStatus+')! Try again or contact webmaster');
		},
	hide_suggestions: function(){
		$("#nowplaying_suggestion_box").fadeOut();
		},
	show_suggestions: function(){
		$("#nowplaying_suggestion_box").fadeIn();
		},
	show: function(message){
		live.nowplaying.currentmsg = message;
		$(".nowplaying_message").hide();
		$(".nowplaying_message."+message).show();
		},
	display: function(data, status, xhr){
		
		if(!data || !data.results){
			live.nowplaying.show('error');
			return;
			}
		
		if(data.synced) live.nowplaying.show('none');
		else live.nowplaying.show('loading');
		
		var items = " ", track;
		for(var i=0; i<data.results.length; i++){
			track = data.results[i];
			items += live.nowplaying.makeitem(track);
			live.nowplaying.datacache[track['id']] = track;
		}
		
		if(data.results.length == 0){
			if(data.synced) live.nowplaying.show('notracks');
			else live.nowplaying.show('nocache');
			}
		
		$('#nowplaying_suggestions').html(items);
		$('.nowplaying_suggestion').click(live.nowplaying.preview);
		},
	makeitem: function(track){
		return "<div class='nowplaying_suggestion' id='"+track.id+"'><img src='"+track.img60+"'><span><span class='suggestion_song'>"+track.name+"</span><br /><span style='color:#AAA'>by</span> <span class='suggestion_artist'>"+track.artist+"</span></span></div>";
		},
	preview: function(){
	
		live.nowplaying.previewing = $(this).attr('id');
		$('#nowplaying_preview #image_preview').attr('src', live.nowplaying.datacache[live.nowplaying.previewing].img60+"?preview");
		$('#nowplaying_preview #artist_preview').html(live.nowplaying.datacache[live.nowplaying.previewing]['artist']);
		$('#nowplaying_preview #song_preview').html(live.nowplaying.datacache[live.nowplaying.previewing]['name']);
		$('#nowplaying_preview #notes_preview').html('');
		$("#nowplaying_preview").slideDown();
		$("#nowplaying_search").slideUp();
		
		},
	noteschange: function(){
		$('#nowplaying_preview #notes_preview').html($(this).val());
		},
	submit: function(){
	
		if(live.nowplaying.ismusic && !live.nowplaying.previewing) return;

		$('.nowplaying input').prop('disabled', true);
		
		jQuery.ajax({
			url: "/dj/ajax/setnowplaying",
			dataType: 'json',
			data: {
				generate: "setnowplaying",
				trackid: (live.nowplaying.previewing == null) ? '' : live.nowplaying.previewing,
				text: (live.nowplaying.ismusic) ? $('#nowplaying_musicnotes').val() : $('#nowplaying_talknotes').val(),
				showid: $("#nowplaying_liveshowid").val()
				},
			success: function(data){
				if(data.error){
					if(data.error.indexOf('403') > 0)
						alert('Error: Only the current DJ can update what\'s playing. Your show either hasn\'t started or already ended');
					else
						alert('Error: Unable to update what\'s playing! Try again or contact webmaster');
					}
				else {
					live.nowplaying.reset();
					livestreams.sync();
					}
				$('.nowplaying input').prop('disabled', false);
				},
			error: function(data, status, xhr){
				livestreams.sync();
				$('.nowplaying input').prop('disabled', false);
				alert('Error: Unable to update what\'s playing!\nError: '+status+'\nTry again or contact webmaster');
				}
			});
		
		},
	reset: function(){
	
		live.nowplaying.datacache = {};
		live.nowplaying.previewing = null;
		live.nowplaying.hide_suggestions();
		
		if(live.nowplaying.xhr_search) live.nowplaying.xhr_search.abort();
		if(live.nowplaying.xhr_sync) live.nowplaying.xhr_sync.abort();
		
		$('#nowplaying_suggestions').html('');
		$('#nowplaying_musicnotes').val('');
		$('#nowplaying_talknotes').val('');
		$("#nowplaying_preview").slideUp();
		$("#nowplaying_search").slideDown();
		$('#nowplaying_input').val('');
		
		live.nowplaying.show('ready');
		},
	switchpane: function() {
		if(console) console.log(this);
		$('#nowplaying_navbar li').removeClass("active");
		if($(this).parent().hasClass('music')){
			live.nowplaying.ismusic = true;
			$('#talkpane').slideUp(function(){ $('#musicpane').slideDown(); });
			$('#nowplaying_navbar .music').addClass("active");
			}
		else {
			live.nowplaying.ismusic = false;
			$('#musicpane').slideUp(function(){ $('#talkpane').slideDown(); });
			$('#nowplaying_navbar .talk').addClass("active");
			live.nowplaying.reset();
			}
		}
	};
	
live.stuff = new Object();
live.stuff.load = function(type) {
	$('#livestuff').html("Loading...").load("/dj/ajax/"+type);
	$("#livestuff_navbar li").removeClass('active');
	$("#livestuff_navbar li."+type).addClass('active');
};

live.stats = new Object();
live.stats.load = function(type) {
	$("#statsstuff_navbar li").removeClass('active');
	$("#statsstuff_navbar li."+type).addClass('active');
	$('.stats_box').slideUp();
	$('.stats_box.'+type).slideDown();
};

live.news = new Object();
live.news.load = function(type) {
	switch(type) {
		case "mygenre":
			$('#news').slideUp(function(){ $(this).load("/dj/ajax/mygenre", {genre: live.genre}, function(){ $(this).slideDown(); }); });
			break;
		case "promos":
			$('#news').slideUp(function(){ $(this).load("/dj/ajax/promos", function(){ $(this).slideDown(); }); });
			break;
		default:
			$('#news').html("Loading...").delay(1000).load("/dj/live", {generate:type},function(response){});
			break;
	}
	$("#newstuff_navbar li").removeClass('active');
	$("#newstuff_navbar li."+type).addClass('active');
};

var sms = {

	data: { },
	hasdata: false,
	opennumber: null,
	
	synctime: 1000*10, // 10 sec
	syncer: null,
	last: null,
	
	locked_out: false,
	
	root: null,
	portal: "/dj/ajax/livechat",

	init: function(){
		sms.syncer = setInterval(sms.sync, sms.synctime);
		sms.sync();
		root = $("#sms-root");
		
		$(document)
			.on('click', '.sms-number', sms.toggle_contact)
			.on('click', '.sms-replysend', sms.send)
			.on('click', '.sms-replybox', sms.replybox_focus);
		
		},
	
	sync: function(){
		jQuery.ajax({
			url: sms.portal,
			dataType: 'json',
			data: { action: 'livechat-sync', last: sms.last },
			success: sms.recieve
			});
		},
		
	send: function(event){
		
		var contact = $(event.currentTarget).parents('.sms-contact');
		var input = contact.find('.sms-replybox');
		
		input.prop('disabled', true);

		jQuery.ajax({
			url: sms.portal,
			dataType: 'json',
			data: { action: 'livechat-send', contactid: contact.attr('rel'), message: input.val() },
			success: function(){
				input.prop('disabled', false);
				input.val('');
				sms.sync();
				}
			});
			
		},
		
	recieve: function(response, status, xhr){
		if(response.error){
			if(response.error.indexOf('403') > -1){
				if(sms.locked_out) return;
				else{
					sms.locked_out = true;
					alert("SMS and LiveChat messaging is currently disabled.\nThose features can only be used for the exact hour that your show is broadcasting.\n\nEmail webmaster@chapmanradio.com with any questions.");
					}
				}
			else{
				alert("Unable to sync SMS:\n"+response.error);
				}
			return;
			}
		
		if(sms.locked_out){
			alert("SMS and LiveChat messaging is has been enabled for your show.");
			sms.locked_out = false;
			}
		
		if(response.egg) live.nowplaying.nyaeggst(response.egg);
		
		if(response.msgs) for(var number in response.msgs) {
			if(!sms.hasdata){ sms.hasdata = true; $("#sms-nodata").hide(); }
			
			if(!sms.data[number]){
				sms.data[number] = [];
				// Draw a new contact
				root.append(sms.draw_contact(number, response.msgs[number]));
				}
			
			for(var smsid in response.msgs[number].smsdata){
				if(sms.data[number][smsid]) continue; // we already have this message.
				sms.data[number][smsid] = response.msgs[number].smsdata[smsid];
				sms.last = smsid;
				$("#sms-contact-"+number+' .sms-messages').append(sms.draw_txt(response.msgs[number].smsdata[smsid]));
				if(response.msgs[number].smsdata[smsid].direction == 'in') $("#sms-contact-"+number).addClass('flashing');
				}
			}
		},
		
	toggle_contact: function(event){
		sms.opennumber = $(event.currentTarget).parent().attr('rel');
		var hideonly = $('#sms-contact-'+sms.opennumber).removeClass('flashing').find('.sms-conversation').is(':visible');
		$('.sms-conversation').slideUp();
		if(!hideonly) $('#sms-contact-'+sms.opennumber+' .sms-conversation').slideDown();
		else sms.opennumber = null;
		},
		
	draw_contact: function(number, numberdata){
		return "<div class='sms-contact' id='sms-contact-"+number+"' rel='"+number+"'><div class='sms-number'>"+numberdata['label']+"</div><div class='sms-conversation'><div class='sms-messages'></div><div class='sms-reply'><input class='sms-replybox' type='text' /><input class='sms-replysend' type='button' value='Send' /></div></div></div>";
		},
		
	draw_txt: function(txt){
		return "<div class='sms-txt sms-"+txt['direction']+"'><span class='sms-time'>"+txt['time']+"</span><br /><span class='sms-text'>"+txt['message']+"</span>";
		},
	
	replybox_focus: function(event){
		$(event).parent(".sms-contact").removeClass('flashing');
		}
	
	};
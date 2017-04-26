/* js/livestreams.js (C) David Tyler 2013 */
 
 $(document).ready(function(){
	livestreams.init();
	});
 
 var livestreams = {
	
	showid: 0,
	nowplayingid: 0,
	interval: 1000*30, // 30 sec
	
	init: function() {
		livestreams.sync();
		setInterval(livestreams.sync, livestreams.interval);
		},

	sync: function() {
		$.getJSON("/ajax/livestreams", function(response){ livestreams.receive(response)} );
		},
		
	receive: function(response) {
		if(response.error) return;
			
		if(response.sports) $("#livestream-sports").slideDown();
		else $("#livestream-sports").slideUp();
					
		if(response.showid) {
			livestreams.updateShow(response);
			livestreams.updateNowPlaying(response);
			$("#livestream-radio").slideDown();
			}
		else {
			$("#livestream-radio").slideUp();
			}

		livestreams.showid = response.showid;
		livestreams.nowplayingid = response.nowplaying.nowplayingid;
		},

	updateShow: function(response){
		if(response.showid != livestreams.showid) {
			$('#livestream-radio-show img').attr('src', response.show.img64);
			$('#livestream-radio-show .showname').html(response.show.showname);
			$('#livestream-radio-show .showlink').attr('href', response.show.permalink);
			$('#livestream-radio-show .showdetails').html(response.show.djs);
			}
		},
	
	updateNowPlaying: function(response){
		var item = $('#livestream-radio-nowplaying .cr-ls-container');
		if(!response.nowplaying.nowplayingid){
			$('#livestream-radio-nowplaying').hide();
			item.html('')
			return;
			}
		if(response.nowplaying.nowplayingid != livestreams.nowplayingid) {
			item.html("<img alt='Now Playing Image' src='"+response.nowplaying.img60+"' />");
			
			if(response.nowplaying.type == "music") {
				item.append("<div class='sideinfo'><span class='slidetitle'>NOW PLAYING</span><span class='trackname'>"+
					response.nowplaying.track+
					"</span><span class='trackartist'>"+
					response.nowplaying.artist+
					"</span></div>");
				}
			else{
				item.append("<div class='sideinfo'><span class='slidetitle'>CURRENT TOPIC</span><span class='notes'>"+
					response.nowplaying.text+
					"</span><div>");
				}
			$('#livestream-radio-nowplaying').show();
			}
		}
	
	};

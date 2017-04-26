
$(document).ready(function(){ livechat.init(); });

var livechat = {

	data: { },
	hasdata: false,
	opennumber: null,
	
	synctime: 1000*10, // 10 sec
	syncer: null,
	last: null,
	
	root: '#livechat-root',
	portal: "/ajax/livechat",
		
	chatid: null,
	chatauth: null,
	chatname: null,

	init: function(){
		$(livechat.root).html('Loading...');
		livechat.syncer = setInterval(livechat.sync, livechat.synctime);
		jQuery.ajax({
			type: 'POST',
			url: livechat.portal,
			data: { action: 'init' },
			dataType: 'json',
			success: function(data){
				livechat.chatid = data.chatid;
				livechat.chatauth = data.chatauth;
				livechat.chatname = data.chatname;
				livechat.createui(livechat.root);
				livechat.checkname();
				livechat.sync();
				}
			});
		},
		
	checkname: function(){
		if(livechat.chatname == '' || livechat.chatname == null) $(livechat.root).hide().after("<div id='livechat-getname'>What's your name? <input type='text' id='livechat-inputname'></input> <input type='submit' id='livechat-savename' value='Save' /></div>");
		$("#livechat-savename").click(livechat.savename);
		},
				
	createui: function(target){
		$(target).html("<div id='livechat-persona'></div><div id='livechat-messages'></div><div id='livechat-actions'><textarea id='livechat-msgbox'></textarea> <input type='submit' id='livechat-send' value='Send' /></div>");
		$("#livechat-send").click(livechat.send);
		$('#livechat-persona').html("Welcome back, "+livechat.chatname+". Chat with the current DJ below:").delay(5000).slideUp();
		},
	
	sync: function(){
		jQuery.ajax({
			type: 'POST',
			url: livechat.portal,
			dataType: 'json',
			data: { action: 'sync', chatid: livechat.chatid, chatauth: livechat.chatauth, last: livechat.last },
			success: livechat.recieve
			});
		},
		
	send: function(event){

		var input = $('#livechat-msgbox');
		var sender = $('#livechat-send');
		
		if(input.val() == '') return;
		sender.prop('disabled', true);

		jQuery.ajax({
			type: 'POST',
			url: livechat.portal,
			dataType: 'json',
			data: { action: 'send', chatid: livechat.chatid, chatauth: livechat.chatauth, message: input.val() },
			success: function(){
				sender.prop('disabled', false);
				input.val('');
				livechat.sync();
				}
			});
		
		},
	
	savename: function(event){
		
		var input = $('#livechat-inputname');
		var sender = $('#livechat-savename');
		
		if(input.val() == '') return;
		sender.prop('disabled', true);
		
		jQuery.ajax({
			type: 'POST',
			url: livechat.portal,
			dataType: 'json',
			data: { action: 'updatename', chatid: livechat.chatid, chatauth: livechat.chatauth, name: input.val() },
			success: function(){
				sender.prop('disabled', false);
				$('#livechat-getname').remove();
				$(livechat.root).show();
				livechat.sync();
				}
			});
		
		$('#livechat-persona').html('Hey '+input.val()+'! Chat live with the current DJ below').delay(5000).slideUp();
		},
	
	recieve: function(response, status, xhr){
		if(response.error){
			alert("Unable to sync chat:\n"+response.error);
			return;
			}
		
		for(var i in response.data){
			var id = response.data[i].livechatid;
			if(livechat.data[id]) continue; // for some reason we already have this message.
			livechat.data[id] = response.data[i];
			livechat.last = id;
			$('#livechat-messages').append(livechat.drawmsg(response.data[i]));
			}
		},
		
	drawmsg: function(txt){
		return "<div class='livechat-msg livechat-msg-"+txt['direction']+"'><span class='livechat-time'>"+txt['time']+"</span><span class='livechat-msg-text'>"+txt['message']+"</span>";
		},
	
	getCookie: function (c_name){
		var i,x,y,ARRcookies=document.cookie.split(";");
		for (i=0;i<ARRcookies.length;i++){
			x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
			y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
			x=x.replace(/^\s+|\s+$/g,"");
			if (x==c_name) {
				return unescape(y);
				}
			}
		}
	
	};
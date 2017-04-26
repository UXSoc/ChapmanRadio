/*
 
 js/sms.js

*/

sms = new Object();
sms.authkey = '';
sms.interval = 20*1000;
sms.last = 0;
sms.timer = null;

$(document).ready(function(){
				  sms.sync();
				  });

sms.sync = function() {
	var date = new Date();
	$.getJSON(document.location, {authkey:sms.authkey,generate:'getsms',last:sms.last}, sms.receive);
};

sms.receive = function(data){
	if(typeof data == 'string') {
		if(data == 'InvalidAuthKey') { window.location = "/logout"; }
		else alert(data);
	}
	else {
		if(data.length == 0 && sms.last == 0) $('#sms').html('No data.');
		for(var number in data) {
			for(var smsid in data[number]) {
				sms.append(data[number][smsid]);
				if(smsid > sms.last) sms.last = smsid;
			}
		}
	}
	sms.timer = setTimeout("sms.sync()", sms.interval);
};

sms.append = function(s) {
	if(sms.last == 0) $("#sms").empty();
	var html = "<div class='specs' style='display:none;'>";
	html += s.number == s.contact ? s.contact : s.contact + "<br />" + s.number;
	html += "<p style='float:right'>";
	html += s.time;
	html += "</p><br />";
	html += s.text;
	html += "</div>";
	$('#sms').append(html);
	$('#sms :hidden').slideDown();
};
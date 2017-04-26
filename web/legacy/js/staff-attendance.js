/*
 
 js/staff-attendance.js
 
 by adam borecki
 
 */

if(typeof a == 'undefined') a = {};

a.checkRadioButtons = function() {
	if(eid('workshop').checked) {
		$("#workshoptable").slideDown();
	} else $("#workshoptable").slideUp();
	if(eid('event').checked) {
		$("#eventtable").slideDown();
	} else $("#eventtable").slideUp();
};

$(document).ready(function(){ a.init(); });
a.init = function() {
	$('#search').watermark("Search...").keyup(function(){a.search()});
	$('#results').mouseleave(a.blur());
	$('#whitescreen').click(function(){a.cancel()});
	$(document).keypress(function(e){a.catchKey(e);})
};

a.catchKey = function(e){
	switch(e.keyCode || e.which) {
		case 27:
			a.cancel();
			break;
	}
};

a.type = '';
a.date = '';
a.start = function() {
	if(eid('workshop').checked) {
		a.type = 'workshop';
		a.date = eid('workshopdate').value;
	} else	if(eid('event').checked) {
		a.type = 'event';
		a.date = eid('eventdate').value;
	}
	if(a.type) {
		eid('setup').innerHTML = "<div class='address'><a>You're recording <b>"+a.type+"</b> attendance for <b>"+a.date+"</b>";
		$('#controls').slideDown(function(){eid('search').focus();a.search();a.refresh();});
	}
};

a.search = function(e) {
	var html = "";
	var search = eid('search').value.toLowerCase();
	a.curUserid = 0;
	for(var userid in a.djs) {
		var dj = a.djs[userid];
		if(search) {
			var strFound = false;
			if(dj.name.toLowerCase().indexOf(search) > -1) strFound = true;
			else if(dj.djname.toLowerCase().indexOf(search) > -1) strFound = true;
			else if(dj.userid == search) strFound = true;
			if(!strFound) continue;
		}
		if(a.curUserid == 0) {
			a.curUserid = dj.userid;
		}
		var className = dj.attendance || "default";
		html += "<a id='user"+userid+"' class='user "+className+"' onmouseover='a.highlight("+userid+")' onmouseout='a.blur("+userid+")' onclick='a.select("+dj.userid+")' rel='"+userid+"'><img src='"+dj.icon+"' /><b>"+dj.name+"</b><br />"+dj.djname+"</a>";
	}
	eid('results').innerHTML=html+"<br style='clear:both' />";
	a.highlight(a.curUserid);
};

a.highlight = function(userid){
	if(userid) {
		if(a.curUserid && a.curUserid != userid) eid('user'+a.curUserid).style.opacity = .4;
		a.curUserid = userid;
		eid('user'+userid).style.opacity = 1;
	}
};
a.blur = function(userid){
	if(userid && userid == a.curUserid) {
		eid('user'+userid).style.opacity = .4;
		a.curUserid = 0;
	}
	else a.curUserid = 0;
};

a.curUserid = 0;
a.select = function(userid) {
	var userid = userid || a.curUserid;
	if(userid) {
		var dj = a.djs[userid];
		if(!dj) return;
		var html = "<div class='address'><a>User #"+userid+"</a></div><table><tr><td><img src='"+dj.pic+"' /></td><td><dl>";
		html += "<dt>name</dt><dd>"+dj.djname+"</dd>";
		html += "<dt>dj name</dt><dd>"+dj.djname+"</dd>";
		html += "<dt>classclub</dt><dd>"+dj.classclub+"</dd>";
		html += "<dt>email</dt><dd>"+dj.email+"</dd>";
		html += "</dl>";
		html += "</td></tr></table>";
		html += "<div class='address'><a>Attendance</a></div>";
		html += "<table cellspacing='0' style='width:100%;'><tr><td style='width:50%;'>";
		html += "<dl>";
		html += "<dt>type</dt><dd>"+a.type+"</dd>";
		html += "<dt>date</dt><dd>"+a.date+"</dd></dl>";
		html += "</td><td>";
		html += "<dl><dt>attendance</dt><dd><ul>";
		html += "<li><button onclick='a.submit("+userid+",\"present\");' id='present"+userid+"' >Record "+dj.fname+" as <b style='color:#090'>Present</b></button></li>";
		html += "<li><button onclick='a.submit("+userid+",\"excused\");' id='excused"+userid+"'>Record "+dj.fname+" as <b style='color:#D60'>Excused</b></button></li>";
		html += "<li><button onclick='a.submit("+userid+",\"absent\");' id='absent"+userid+"'>Record "+dj.fname+" as <b style='color:#A00'>Absent</b></button></li>";
		html += "<button onclick='a.cancel();' id='cancel"+userid+"' style='color:#575757;'>Cancel</button> &nbsp; ";
		html += "</dl>";
		html += "</td></tr></table>";
		$('#whitescreen').slideDown();
		$('#dialogInner').html(html);
		var left = Math.round( ($(document).width() - 570) / 2 );
		$('#dialog').css({left:0}).animate({bottom:60,left:left},400,function(){eid('present'+userid).focus();});
	}
};

a.cancel = function(animateRight) {
	$('#whitescreen').slideUp();
	var left = animateRight ? $(document).width() : 0;
	$('#dialog').animate({bottom:-800,left:left},function(){ eid('dialogInner').innerHTML='';eid('search').focus(); });
};

a.submit = function(userid,status) {
	var userid = userid || a.curUserid;
	if(!userid) return;
	$.getJSON(a.self,{"generate":"AjaxRecord","date":a.date,"type":a.type,"userid":userid,"status":status},function(response){ if(response.error)alert(response.error); a.refresh(); });
	a.djs[userid].attendance = status;
	a.search();
	eid('search').value='';
	a.cancel(true);
};

a.timer = null;
a.refresh = function(){
	if(a.timer) { clearTimeout(a.timer);a.timer = null; }
	$.getJSON(a.self,{"generate":"AjaxRefresh","date":a.date,"type":a.type},function(response){a.process(response)})
};
a.process = function(response) {
	if(response.error) alert(response.error);
	else {
		for(var userid in response.data) {
			if(userid && a.djs[userid])
				a.djs[userid].attendance = response.data[userid];
		}
	}
	a.search();
	a.timer = setTimeout("a.refresh()",1000*20);
};

a.remainder = function() {
	var userids = "";
	var total = 0;
	for(var userid in a.required) {
		if(a.djs[userid].attendance == "") {
			userids += userid+",";
			total++;
		}
	}
	if(!userids) alert('no action');
	else {
		if(confirm("You are about to mark "+total+" people as absent.")) {
			$.getJSON(a.self,{"generate":"RemainderAbsent",userids:userids,"date":a.date,"type":a.type,"status":"absent"},function(response){ if(response.error) alert(response.error); else alert(response.success); });
		}
	}
};
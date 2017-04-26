if(typeof a == 'undefined') a = {};

a.lastSearchText = null;
a.editMode = false;
a.curUserid = 0;
a.selUserid = 0;

a.catchKey = function(e){
	switch(e.keyCode || e.which) {
		case 96:
		case 27:
			$("#search").val("");
			a.cancel();
			break;
		case 37:
			if(a.editMode) return;
			if(a.selUserid == 0) a.moveSearchFocusLeft();
			e.preventDefault();
			break;
		case 38:
			if(a.editMode) return;
			if(a.selUserid != 0) a.moveCurFocusUp();
			if(a.selUserid == 0) a.moveSearchFocusUp();
			e.preventDefault();
			break;
		case 39:
			if(a.editMode) return;
			if(a.selUserid == 0) a.moveSearchFocusRight();
			e.preventDefault();
			break;
		case 40:
			if(a.editMode) return;
			if(a.selUserid != 0) a.moveCurFocusDown();
			if(a.selUserid == 0) a.moveSearchFocusDown();
			e.preventDefault();
			break;
		case 192:
			a.editMode = !a.editMode;
			e.preventDefault();
			break;
		}
	};
	
a.moveCurFocusUp = function(){
	if($('#present'+a.selUserid).is(":focus")){
		$('#cancel'+a.selUserid).focus();
		return;
		}
	if($('#excused'+a.selUserid).is(":focus")){
		$('#present'+a.selUserid).focus();
		return;
		}
	if($('#absent'+a.selUserid).is(":focus")){
		$('#excused'+a.selUserid).focus();
		return;
		}
	if($('#cancel'+a.selUserid).is(":focus")){
		$('#absent'+a.selUserid).focus();
		return;
		}
	};
	
a.moveCurFocusDown = function(){
	if($('#present'+a.selUserid).is(":focus")){
		$('#excused'+a.selUserid).focus();
		return;
		}
	if($('#excused'+a.selUserid).is(":focus")){
		$('#absent'+a.selUserid).focus();
		return;	
		}
	if($('#absent'+a.selUserid).is(":focus")){
		$('#cancel'+a.selUserid).focus();
		return;
		}
	if($('#cancel'+a.selUserid).is(":focus")){
		$('#present'+a.selUserid).focus();
		return;
		}
	};

a.moveSearchFocusUp = function(){
	if(a.curUserid == 0) return;
	a.highlight($('#user'+a.curUserid).prev().prev().prev().prev().attr('data-id'));
	};

a.moveSearchFocusLeft = function(){
	if(a.curUserid == 0) return;
	a.highlight($('#user'+a.curUserid).prev().attr('data-id'));
	};

a.moveSearchFocusRight = function(){
	if(a.curUserid == 0) return;
	a.highlight($('#user'+a.curUserid).next().attr('data-id'));
	};

a.moveSearchFocusDown = function(){
	if(a.curUserid == 0) return;
	a.highlight($('#user'+a.curUserid).next().next().next().next().attr('data-id'));
	};
	
a.search = function(e, force) {
	var html = "";
	var search = $('#search').val().toLowerCase().trim();
	var filter = $('#filter').val();
	
	if(search.length == 16 && search.indexOf(';') == 0){
		search = search.substring(1, 10)*1;
		}
	
	if(!force && a.lastSearchText == search) return;
	a.lastSearchText = search;
	
	a.curUserid = 0;
	for(var index in a.djs_ordered) {
		var dj = a.djs_ordered[index];
		if(search) {
			var ok = false;
			if(dj.name.toLowerCase().indexOf(search) > -1) ok = true;
			else if(dj.djname.toLowerCase().indexOf(search) > -1) ok = true;
			else if(dj.studentid == search) ok = true;
			else if(dj.id == search) ok = true;
			if(!ok) continue;
			}
		if(filter != 'nofilter'){
			var ok = false;
			if(filter == 'default' && (dj.attendance == null || dj.attendance == "")) ok = true;
			if(filter == 'present' && dj.attendance == "present") ok = true;
			if(filter == 'excused' && dj.attendance == "excused") ok = true;
			if(filter == 'absent' && dj.attendance == "absent") ok = true;
			if(!ok) continue;
			}
		if(a.curUserid == 0) a.curUserid = dj.id;
		html += "<a id='user"+dj.id+"' class='user usercell "+(dj.attendance || "default")+" "+(a.required[dj.id] ? 'required' : 'notrequired')+"' data-id='"+dj.id+"'><img src='"+dj.img50+"' /><b>"+dj.name+"</b><br />"+dj.djname+"<br />#"+dj.id+"</a>";
		}
	$('#results').html(html+"<br style='clear:both' />");
	a.highlight(a.curUserid);
	};

a.highlight = function(userid){
	if(userid) {
		a.curUserid = userid;
		$('.user.focused').removeClass('focused');
		$('#user'+userid).addClass('focused');
		}
	};

a.blur = function(userid){
	if(userid && userid == a.curUserid) {
		$('#user'+userid).removeClass('focused');
		a.curUserid = 0;
		}
	else a.curUserid = 0;
	};

a.select = function(userid) {
	var userid = userid || a.curUserid;
	if(!userid) return;
	a.selUserid = userid;
	var dj = a.djs[userid];
	if(!dj) return;
	var html = "<table><tr><td style='vertical-align: top; width: 200px;'><img src='"+dj.img192+"' style='margin: 5px;' /></td><td><dl>";
	html += "<dt>name</dt><dd>"+dj.djname+"</dd>";
	html += "<dt>dj name</dt><dd>"+dj.djname+"</dd>";
	html += "<dt>classclub</dt><dd>"+dj.classclub+"</dd>";
	html += "<dt>email</dt><dd>"+dj.email+"</dd>";
	html += "<dt>student id</dt><dd>"+dj.studentid+"</dd>";
	html += "</dl>";
	html += "<div class='address'><ul>";
	html += "<li><button onclick='a.submit(\"present\");' id='present"+userid+"' >Record "+dj.fname+" as <b style='color:#090'>Present</b></button></li>";
	html += "<li><button onclick='a.submit(\"excused\");' id='excused"+userid+"'>Record "+dj.fname+" as <b style='color:#D60'>Excused</b></button></li>";
	html += "<li><button onclick='a.submit(\"absent\");' id='absent"+userid+"'>Record "+dj.fname+" as <b style='color:#A00'>Absent</b></button></li>";
	html += "<button onclick='a.cancel();' id='cancel"+userid+"' style='color:#575757;'>Cancel</button> &nbsp; ";
	html += "</ul></div>";
	html += "</td></tr></table>";
	$('#whitescreen').slideDown();
	$('#dialogInner').html(html);
	var left = Math.round( ($(document).width() - 570) / 2 );
	var top = $(window).height();
	$('#dialog').css({top:top, left:left}).animate({top:60},400,function(){ $('#present'+userid).focus(); });
	};

a.cancel = function() {
	a.selUserid = 0;
	$('#whitescreen').slideUp();
	var top = $(window).height();
	$('#dialog').animate({top:top}, function(){ $('dialogInner').html(''); $('#search').focus(); });
	a.search();
};

a.submit = function(status) {
	if(!a.selUserid) return;
	$.getJSON("/staff/ajax/record_attendance", { "date" : $('#record-date').val(), "type" : $('#record-type').val(), "userid":a.selUserid, "status": status}, function(response){ if(response.error) alert(response.error); a.refresh(); });
	a.djs[a.selUserid].attendance = status;
	$('#search').val('');
	a.cancel();
};

a.timer = null;
a.refresh = function(){
	if(a.timer) { clearTimeout(a.timer); a.timer = null; }
	$.getJSON('/staff/ajax/refresh_attendance', { "date" : $('#record-date').val(), "type" : $('#record-type').val() }, function(response) {
		if(response.error){
			alert(response.error);
			return;
			}
		for(var userid in response.data) if(userid && a.djs[userid]) a.djs[userid].attendance = response.data[userid];
		a.search(null, true);
		a.timer = setTimeout("a.refresh()", 1000*20);
		});
	};

a.remainder = function() {
	var userids = "";
	var total = 0;
	for(var userid in a.required) {
		if(a.djs[userid].attendance == "" || a.djs[userid].attendance == null) {
			userids += userid+",";
			total++;
			}
		}
	if(!userids) alert('no action');
	else {
		if(confirm("You are about to mark "+total+" people as absent.")) {
			$.getJSON("/staff/ajax/record_attendance", { userids: userids, "date" : $('#record-date').val(), "type" : $('#record-type').val(), "status": "absent" }, function(response){
				if(response.error) alert(response.error);
				else alert(response.success);
				});
			}
		}
	};

$(document).ready(function(){
	a.djs_ordered = jQuery.map(a.djs, function(k, v) { return [k]; });
	a.djs_ordered = a.djs_ordered.sort(function(a, b){
		if ( a.lname.toLowerCase() < b.lname.toLowerCase() ) return -1;
		if ( a.lname.toLowerCase() > b.lname.toLowerCase() ) return 1;
		return 0;
		});
	
	$(document)
		.on('click', '.user', function(e){ a.select($(e.currentTarget).attr('data-id')); })
		.on('mouseover', '.user', function(e){ a.highlight($(e.currentTarget).attr('data-id')); })
		.on('mouseout', '.user', function(e){ a.blur($(e.currentTarget).attr('data-id')); });
	
	$('#search').watermark("Search...").keyup(a.search);
	$('#results').mouseleave(a.blur());
	$('#whitescreen').click(function(){a.cancel()});
	$('#filter').change(function(){a.search(null, true)});
	$(document).keydown(a.catchKey);
	a.search(null, true);
	a.refresh();
	});
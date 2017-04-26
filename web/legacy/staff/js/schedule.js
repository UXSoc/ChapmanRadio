if(typeof s == "undefined") s = new Object();
s.showsUp = function(instant) {
	if(instant){
		// $("#shows").stop().css({height:120,overflow:'auto'});
		}
	else {
		// $("#shows").stop().animate({height:120},function(){this.style.overflow='auto'}, function(){ s.tOffset = $("#t_div").offset(); });
		}
	};

s.showsDown = function() { $("#shows").stop().animate({height:400},function(){this.style.overflow='auto'})};
s.selectedUp = function() { $("#selected").stop().animate({height:120},function(){this.style.overflow='auto'})};
s.selectedDown = function() { $("#selected").stop().animate({height:400},function(){this.style.overflow='auto'})};
s.startHour = 5;
s.endHour = 28;
s.curShowid = 0;
s.cellWidth = 101;
s.cellHeight = 33;
s.selectedIsOpen = false;
s.history = [];
s.drawAvailability = function() {
	var djavailability = $('#djavailability').is(':checked');
	var scheduleavailability = $('#scheduleavailability').is(':checked');
	for(var row = s.startHour;row <= s.endHour;row++) {
		for(var col = 1;col <= 7;col+= 1) {
			cell = $('#t'+row+'-'+col);
			cell.addClass("t_cell").removeClass("t_selected t_unselected t_x1 t_x2");
			if(djavailability) {
				if(s.curShowid != 0  && s.data[s.curShowid].table[row][col] == 1) {
					cell.addClass("t_selected");
				} else {
					cell.addClass("t_unselected");
				}
			}
			if(scheduleavailability) {
				if(s.schedule[row][col][0] != 0) cell.addClass("t_x1");
				if(s.schedule[row][col][1] != 0) cell.addClass("t_x2");
			}
		}
	}
};
s.drawGenres = function() {
	var viewgenres = $('#viewgenres').is(':checked');
	for(var row = s.endHour;row >= s.startHour;row--) {
		for(var col = 7;col >= 1;col--) {
			for(var i = 0;i <= 1;i++) {
				var showid = s.schedule[row][col][i];
				var genreClass = (showid) ? s.data[showid].genre.replace(/\W/g,"") : "";
				$('#genre'+row+'-'+col+'-'+i).attr('class', (viewgenres) ? "t_genre "+genreClass : "t_genre");
			}
		}
	}
};
s.drawSchedule = function() {
	var html = "";
	if($('#viewshows').is(':checked')) {
		for(var row = s.endHour;row >= s.startHour;row--) {
			for(var col = 7;col >= 1;col--) {
				for(var shownum = 1;shownum >=0;shownum--) {
					var showid = s.schedule[row][col][shownum];
					if(!showid) continue;
					show = s.data[showid];
					if(!show) continue;
					x = (col-1)*s.cellWidth + (shownum)*s.cellWidth/2;
					y = (row-s.startHour)*s.cellHeight;
					genreClass = show.genre.replace(/\W/g,"");
					html += "<div class='s_item "+genreClass+"' style='left:"+x+"px;top:"+y+"px' onclick='s.select("+showid+")'><img src='"+show.icon+"' />"+show.showname+"</div>";
				}
			}
		}
	}
	$('#s_holder').empty().html(html);
};
s.searchTimer = null;
s.search = function() {
	if(s.searchTimer) { clearTimeout(s.searchTimer); s.searchTimer = null; }
	s.searchTimer = setTimeout("s.draw()",200);
};
s.select = function(showid) {
	s.curShowid = showid;
	s.jumpTo("selected");
	s.drawAvailability();
	var show = s.data[showid];
	$('#availabilitynotes').html(show.availabilitynotes);
	var html = "";
	html += "<div class='address'><a><b>"+show.showname+"</b></a></div>";
	html += "<table style='width:100%;' celspacing='0'><tr><td><img src='"+show.pic+"' alt='' style='float:left;' /></td><td>";
	html += "<table class='moreinfo'><tr>";
	var fields = ['genre','description','showtime'];
	for(var x in fields) {
		var val = show[fields[x]];
		html += "<td><tt>"+fields[x]+"</tt><br />"+val+"</td>";
	}
	html += "</tr></table>";
	html += "<table class='moreinfo'><tr>";
	var fields = ['seasoncount','unavailability','musictalk','turntables','explicit'];
	for(var x in fields) {
		var val = show[fields[x]];
		if(fields[x] == "explicit") val = (val == 1 ? "<b style='color:#A00'>Yes</b>":"No");
		var onclick = fields[x] == "unavailability" ? "onclick='s.jumpTo(\"schedule\")'" : "";
		html += "<td "+onclick+"><tt>"+fields[x]+"</tt><br />"+val+"</td>";
	}
	html += "</tr></table>";
	for(var x in show.djs) {
		dj = show.djs[x];
		html += "<div class='address'><a><b>"+dj.djname+"</b></a></div><table class='moreinfo'><tr>";
		html += "<td><img src='"+dj.img50+"' onclick='Shadowbox.open({\"content\":\""+dj.img310+"\",player:\"img\"})' /></td>";
		html += "<td><tt>name</tt><br />"+dj.name+"</td>";
		html += "<td><tt>type</tt><br />"+(dj.type=='staff'?"<b style='color:#090'>Staff</b>":dj.type)+"</td>";
		html += "<td><tt>seasons</tt><br />"+(dj.seasoncount)+"</td>";
		html += "<td><tt>email</tt><br />"+dj.email+"</td>";
		html += "<td><tt>facebook</tt><br />"+(dj.fbid!=0?("<a href='http://facebook.com/profile.php?id="+dj.fbid+"' target='_blank'>View profile</a>"):"Not connected")+"</td>";
		html += "</tr></table>";
	}
	html += "</table>";
	html += "<div class='address'><a>Questions</a></div><dl style='padding:6px 20px;'>";
	questions = ['differentiate','promote','timeline','giveaway','speaking','equipment','prepare','examples'];
	for(var x in questions) html += "<dt>"+questions[x]+"</dt><dd>"+show["app_"+questions[x]]+"</dd>";
	html += "</dl></div><br style='clear:both' />";
	if(!s.selectedIsOpen) $('#selectedPanel').show();
	s.selectedIsOpen = true;
	$("#selected").stop().html(html).slideDown();
	$("#schednavShows").css('display','none').html("<div style='text-align:left;'><div class='address'>"+show.showname+"</div><img src='"+show.icon+"' style='float:left;'><span class='genre'>"+show.genre+"</span><br style='clear:both;' /><dl><dt>season count</dt><dd>"+show.seasoncount+"</dd><dt>unavailability</dt><dd>"+show.unavailability+"</dd><dt>showtime</dt><dd>"+show.showtime+"</dd><dt>availabilitynotes</dt><dd>"+show.availabilitynotes+"</dd><dt>equipment</dt><dd>"+show.app_equipment+"</dd></dl></div>").slideDown();
	if(show.seasoncount > 0) $('#everyweek').prop('checked', true);
	else $('#cycle1').prop('checked', true);
	s.updateBiweekly();
	$('#djavailability').prop('checked', true);
	$('#scheduleavailability').prop('checked', true);
}

s.curPanel = "";
s.jumpTo = function(panel) {
	if(!s.selectedIsOpen && panel == "selected") panel = "shows";
	// if(s.curPanel == "shows" && panel != "shows") s.showsUp(true);
	// else s.showsUp(false);
	$.scrollTo("#"+panel+"Panel", 600);
	s.curPanel = panel;
};

s.draw = function() {
	$('#shows').empty();
	var html = "";
	var incomplete = $('#incomplete').is(":checked");
	var finalized = $('#finalized').is(":checked");
	var accepted = $('#accepted').is(":checked");
	var search = $('#search').val().toLowerCase();
	var type = $('#type').val();
	var genre = $('#genre').val();
	var newreturning = $('#newreturning').val();
	var okay = false;
	var numresults = 0;
	for(var showid in s.data) {
		show = s.data[showid];
		okay = false;
		switch(show.status) {
			case "incomplete": if(incomplete) okay = true; break;
			case "finalized": if(finalized) okay = true; break;
			case "accepted": if(accepted) okay = true; break;
		}
		if(!okay) continue;
		if(search != "") {
			if(show.showname.toLowerCase().indexOf(search) == -1) continue;
		}
		if(newreturning == "new" && show.seasoncount != 0) continue;
		if(newreturning == "returning" && show.seasoncount < 1) continue;
		if(type != "") {
			var hasStaff = false;
			var hasDJ = false;
			for(var x in show.djs) {
				var dj = show.djs[x];
				if(dj.type == "staff") hasStaff = true;
				else if(dj.type == "dj") hasDJ = true;
			}
			if(type=="staff" && !hasStaff) continue;
			if(type=="dj" && !hasDJ) continue;
		}
		if(genre != "" && show.genre != genre) continue;
		var genreClass = show.genre.replace(/\W/g,"");
		html += "<span class='show "+genreClass+"' onmouseup='s.select("+showid+")'><img src='"+show.icon+"' /><a><span class='title'>"+show.showname+"</span></a></span>";
		numresults++
	}
	if(html == "") html = "<p style='text-align:center;padding:40px;'>No results.</p>";
	$("#numresults").html(numresults+" result"+(numresults == 1 ? "" : "s"));
	$('#shows').html(html);
};

s.tOffset = null;
s.tmEnter = function(e){
	// $("#schedhover").animate({left:60});
	s.prevRow = 0;
	s.prevCol = 0;
	s.tOffset = $("#t_div").offset();
	s.updateTooltip();
	$('#tooltip').show();
};

s.tmLeave = function(e){
	$('#tooltip').hide();
	// $("#schedhover").stop().animate({left:10});
	$('#t_highlight1').hide();
	$('#t_highlight2').hide();	
};

s.prevRow = 0;
s.prevCol = 0;
s.tmMove = function(e){
	if(!s.tOffset) s.tOffset = $("#t_div").offset();
	var x = (e.pageX - s.tOffset.left);
	var y = (e.pageY - s.tOffset.top);
	var row = s.startHour+Math.floor( y / s.cellHeight);
	var col = Math.ceil( x / s.cellWidth);
	$('#tooltip').css({ left : (x+13)+"px", top : y+"px" });
	if(s.prevRow != row || s.prevCol != col) {
		s.prevRow = row;
		s.prevCol = col;
		s.updateTooltip();
	}
};

s.daysAbbr = ["","mon","tue","wed","thu","fri","sat","sun"];
s.tmClick = function() {
	if(s.curShowid) {
		var show = s.data[s.curShowid];
		if(!show) return;
		var showInfo = show.showname;
		var msg = "You are about to change the "+s.season+" schedule:\n\n";
		msg += s.seasonName + "\n";
		msg += "\t"+s.tName(s.prevRow,s.prevCol,true)+"\n";
		if(s.biweekly == 1 || s.biweekly == 3) msg += "\t\tCycle 1: "+showInfo+"\n";
		if(s.biweekly == 2 || s.biweekly == 3) msg += "\t\tCycle 2: "+showInfo+"\n";
		msg += "\n\nContinue?";
		if(confirm(msg)) {
			if(s.biweekly == 1 || s.biweekly == 3) s.schedule[s.prevRow][s.prevCol][0] = s.curShowid;
			if(s.biweekly == 2 || s.biweekly == 3) s.schedule[s.prevRow][s.prevCol][1] = s.curShowid;
			$.getJSON(s.self,{'action':'saveSchedulePlacement','showid':s.curShowid,'biweekly':s.biweekly,'hour':s.prevRow,'day':s.daysAbbr[s.prevCol],'season':s.season},function(response){ if(response.error) alert(response.error); });
			s.data[s.curShowid].status='accepted';
			$('#scheduleavailability').prop('checked', true);
			s.drawAvailability();
			s.drawGenres();
			s.drawSchedule();
		}
	} else {
		if(s.prevRow && s.prevCol) {
			var showid1 = s.schedule[s.prevRow][s.prevCol][0];
			var show1 = (showid1?s.data[showid1]:{"showname":""});
			var showid2 = s.schedule[s.prevRow][s.prevCol][1];
			var show2 = (showid2?s.data[showid2]:{"showname":""});
			if(!showid1 && !showid2) return;
			var msg = "You are about to change the "+s.season+" schedule:\n\n";
			msg += s.seasonName + "\n";
			msg += "\t"+s.tName(s.prevRow,s.prevCol,true)+"\n";
			if(show1 && (s.biweekly == 1 || s.biweekly == 3)) msg += "\t\tCycle 1: Empty (Remove "+show1.showname+")\n";
			if(show2 && (s.biweekly == 2 || s.biweekly == 3)) msg += "\t\tCycle 2: Empty (Remove "+show2.showname+")\n";
			msg += "\n\nContinue?";
			if(confirm(msg)) {
				if(s.biweekly == 1 || s.biweekly == 3) s.schedule[s.prevRow][s.prevCol][0] = 0;
				if(s.biweekly == 2 || s.biweekly == 3) s.schedule[s.prevRow][s.prevCol][1] = 0;				
				$.getJSON(s.self,{'action':'saveSchedulePlacement','showid':0,'biweekly':s.biweekly,'hour':s.prevRow,'day':s.daysAbbr[s.prevCol],'season':s.season},function(response){ if(response.error) alert(response.error); else if(response.showid) { s.data[response.showid].status = response.status; s.djs[response.showid].showtime = response.showtime; } });
				$('#scheduleavailability').prop('checked', true);
				s.drawAvailability();
				s.drawGenres();
				s.drawSchedule();
			}
		}
	}
};

s.updateTooltip = function(){
	if(!s.prevRow || !s.prevCol || !s.schedule[s.prevRow]) return
	var html = "";
	if(s.curShowid) {
		var show = s.data[s.curShowid];
		if(show) {
			html += "<div class='address'><a>Place</a></div><img src='"+show.icon+"' class='icon' style='float:left;'/> Place <b>"+show.showname+"</b> <br /> "+s.tName(s.prevRow,s.prevCol)+"<br style='clear:left;' />";
			$('#t_div').css({ cursor : 'pointer' });
			}
		}
	else $('#t_div').css({ cursor : 'default' });
	
	// highlight boxes
	var left = (s.cellWidth*(s.prevCol-1))+4;
	var top = (s.cellHeight*(s.prevRow-s.startHour)+4);
	
	if(s.curShowid && (s.biweekly == 1 || s.biweekly == 3))
		$('#t_highlight1').css({ left : left +"px", top : top + "px" }).show();
	else
		$('#t_highlight1').hide();
	
	if(s.curShowid && (s.biweekly == 2 || s.biweekly == 3))
		$('#t_highlight2').css({ left : (left+48) + "px", top : top + "px" }).show();
	else
		$('#t_highlight2').hide();
	
	// scheduled show
	var scheduleshowid1 = s.schedule[s.prevRow][s.prevCol][0];
	var scheduleshowid2 = s.schedule[s.prevRow][s.prevCol][1];
	var show1 = (scheduleshowid1) ? s.data[scheduleshowid1] : null;
	var show2 = (scheduleshowid2) ? s.data[scheduleshowid2] : null;
	var br = "<br style='clear:both' />";
	// remove
	if(s.curShowid) {
		if(scheduleshowid1 && s.biweekly == 3 && scheduleshowid1 == scheduleshowid2) {
			html += "<div class='address'><a>Remove</a></div><img src='"+show1.icon+"' />Remove <b>"+show1.showname+"</b> from <b>both cycles</b>"+br;
		} else if(scheduleshowid1 || scheduleshowid2) {
			html += "<div class='address'><a>Remove</a></div>";
			if(scheduleshowid1 && (s.biweekly == 1 || s.biweekly == 3) ) html += "<img src='"+show1.icon+"' />Remove <b>"+show1.showname+"</b> from <b>cycle 1</b>"+br;
			if(scheduleshowid2 && (s.biweekly == 2 || s.biweekly == 3) ) html += "<img src='"+show2.icon+"' />Remove <b>"+show2.showname+"</b> from <b>cycle 2</b>"+br;
		} else {
		}
	} else {
		if(scheduleshowid1 && scheduleshowid1 == scheduleshowid2) {
			html += "<div class='address'><a>Remove Current Show</a></div><img src='"+show1.icon+"' /><b>"+show1.showname+"</b> <br /> "+s.tName(s.prevRow,s.prevCol)+br;
			$('#tooltip').css({ cursor : 'pointer' });
		} else if(scheduleshowid1 || scheduleshowid2) {
			html += "<div class='address'><a>Remove Current Show"+(scheduleshowid1&&scheduleshowid2?"s":"")+"</a></div>";
			if(scheduleshowid1 && show1) html += "<img src='"+show1.icon+"' /><b>"+show1.showname+"</b> <br /> "+s.tName(s.prevRow,s.prevCol)+""+br;
			if(scheduleshowid2 && show2) html += "<img src='"+show2.icon+"' /><b>"+show2.showname+"</b> <br /> "+s.tName(s.prevRow,s.prevCol)+""+br;
			$('#tooltip').css({ cursor : 'pointer' });
		} else {
			$('#tooltip').css({ cursor : 'default' });
			html += "<div class='address'><a>Empty</a></div><p>This "+s.tName(s.prevRow,s.prevCol,true)+" is currently empty.</p>";
		}
	}
	$('#tooltip').html(html);
}

s.shortcutsDisabled = false;
s.tmKeypress = function(e) {
	if(s.shortcutsDisabled || e.ctrlKey || e.metaKey) return;
	switch(e.keyCode || e.which) {
		case 49: // 1
			$('#cycle1').prop('checked', true);
			s.updateBiweekly();
			s.updateTooltip();
			break;
		case 50: // 2
			$('#cycle2').prop('checked', true);
			s.updateBiweekly();
			s.updateTooltip();
			break;
		case 51: // 3
			$('#everyweek').prop('checked', true);
			s.updateBiweekly();
			s.updateTooltip();
			break;
		case 97: // a
			s.jumpTo("shows");
			break;
		case 115: // s
			s.jumpTo("selected");
			break;
		case 100: // d
			s.jumpTo("schedule");
			break;
		case 27: // esc
		case 102: // f
			s.curShowid = 0;
			$('#selectedPanel').slideUp(function(){ s.jumpTo("shows"); });
			$('#availabilitynotes').html("");
			$('#schednavShows').html("");
			s.drawAvailability();
			s.selectedIsOpen = false;
			s.updateTooltip();
			break;
		case 113: // q
			$('#djavailability').prop('checked', !$('#djavailability').prop('checked'));
			s.drawAvailability();
			break;
		case 119: // w
			$('#scheduleavailability').prop('checked', !$('#scheduleavailability').prop('checked'));
			s.drawAvailability();
			break;
		case 101: // e
			$('#viewgenres').prop('checked', !$('#viewgenres').prop('checked'));
			s.drawGenres();
			break;
		case 114: // r
			$('#viewshows').prop('checked', !$('#viewshows').prop('checked'));
			s.drawSchedule();
			break;
		default:
			//alert(e.keyCode || e.which);
			return;
	}
};

s.biweekly = 3;
s.updateBiweekly = function() {
	s.biweekly = 3;
	if($('#cycle1').prop('checked')) s.biweekly=1;
	if($('#cycle2').prop('checked')) s.biweekly=2;
	if($('#everyweek').prop('checked')) s.biweekly=3;
};

s.days = ["","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];
s.tName = function(row,col,ignoreBiweekly) {
	var name = "";
	if(!ignoreBiweekly) name += (s.biweekly != 3)?"every other ":"every ";
	name += s.days[col]+" at ";
	if(row < 12) name += row+"am";
	else if(row == 12) name += "noon";
	else if(row < 24) name += (row%12)+"pm";
	else if(row == 24) name += "midnight";
	else name += (row%24)+"am";
	return name;
};

s.init = function() {
	// $('#showsPanel').mouseenter(function(){s.showsDown();}).mouseleave(function(){s.showsUp();});
	// $('#selectedPanel').mouseenter(function(){s.selectedDown();}).mouseleave(function(){s.selectedUp();});
	$('#t_div').
		click(function(){s.tmClick();}).
		mousemove(function(e){s.tmMove(e)}).
		mouseleave(function(e){s.tmLeave(e);}).
		mouseenter(function(e){s.tmEnter(e);}).
		mouseup(function(){});
	$(document).keypress(function(e){s.tmKeypress(e);});
	$("input[type=text]").focus(function(){s.shortcutsDisabled = true;}).blur(function(){s.shortcutsDisabled = false;});
	setTimeout("s.draw();",300);
	setTimeout("s.drawAvailability();",600);
	setTimeout("s.drawSchedule();",900);
};

$(document).ready(function() {s.init();});
/*! chapmanradio.com/js/evals.js (C) David Tyler 2013 */

$(document).ready(function(){
	evals.init();
	$('.evals .eval-button')
		.mouseenter(function(){ $(this).stop().animate({marginLeft:10}, 200); })
		.mouseleave(function(){ $(this).stop().animate({marginLeft:0}, 200); });
	});

if(typeof evals == "undefined") evals = new Object();
evals.showid = 0; // showid
evals.interval = 10*1000;
evals.timer = null;
evals.headWidth = 240;
evals.animationDelay = 200;
evals.animationSpeed = 600;
evals.expired = false;
evals.live = 0;
evals.nominated = false;
evals.curTimestamp = 0;
evals.curDate = "";

evals.reset = function() {
	evals.showid = 0;
	evals.expired = false;
	evals.nominated = false;
	$("#loading").slideDown(function(){ $("#expiredshow").delay(400).slideUp(function(){ evals.sync(); }); });
};

evals.init = function() {
	$("#goodcomment").keydown(function(e){if(e.keyCode==13){e.preventDefault(); $('#goodcommentbutton').click(); return false;} });
	$("#badcomment").keydown(function(e){if(e.keyCode==13){e.preventDefault(); $('#badcommentbutton').click(); return false;} });
	evals.load();
};

evals.moreinfo = function(goodbad,type) {
	var html = "";
	var button = evals.categories[goodbad][type];
	var smiley = goodbad == 'good' ? "/legacy/img/icons/smileys/happy48.png" : "/legacy/img/icons/smileys/sad48.png";
	html += "<img src='"+smiley+"' style='float:left;margin:0 10px; '/>";
	html += "<div class='address'><a>"+button.label+"</a></div>";
	html += "<br style='clear:both' />";
	html += "<p><img src='"+button.icon+"' style='float:left;margin:10px; '/>"+button.description+"</p>";	
	html += "<br style='clear:both' />";
	$('#moreinfoInner').html(html);
	$("#moreinfo").slideDown();
};

evals.load = function() {
	$("#loading").stop().slideDown(function(){ setTimeout("evals.sync();", 400) });
};

evals.sync = function() {
	if(evals.timer) { clearTimeout(evals.timer); evals.timer = null; }
	$.getJSON('/dj/ajax/evals/sync', evals.response);
};

evals.response = function(response) {
	if(evals.showid && (!response || response.showid != evals.showid)) {
		evals.expired = true;
		$('#expiredshow').slideDown();
		return;
		}
	if(!response) {
		evals.showid = 0;
		evals.curTimestamp = 0;
		$("#loading").stop().slideUp( function() { $('#noshow').slideDown(function(){ $('.evals').slideUp()})});
		}
	else if(response.showid != evals.showid) {
		$('#currenteval').html('');
		// oh this is messy animation!!
		$("#noshow").slideUp(function(){
			$('.evals .list .buttons').css({display:'none'});
			$('.evals .list').css({width:0});
			$('.evals').slideDown(function(){
				$('#listgood').delay(evals.animationDelay).animate({width:evals.headWidth},evals.animationSpeed,function(){
					$('#listgood .buttons').delay(evals.animationDelay).slideDown(function(){
						$('#listbad').delay(evals.animationDelay).animate({width:evals.headWidth},evals.animationSpeed,function(){
							$('#listbad .buttons').delay(evals.animationDelay).slideDown( function(){
								$('#loading').stop().slideUp();
								});
							});
						});
					});
				});
			});
		
		evals.curTimestamp = response.timestamp;
		evals.curDate = response.date;
		evals.curShowname = response.showname;
		evals.data = response.evals;
		evals.showid = response.showid;
		var html = "<img src='"+response.icon+"' alt='' style='margin:0 10px;' />"+
			"<h3>"+response.showname+"</h3><p class='genre' style='float:right;'>"+response.genre+"</p>"+
			"<p>"+response.djs+"</p><p style='text-align:center;font-size:12px;'>"+
			"<a target='_blank' href='/show?show="+response.showid+"'>&raquo; View Full Profile</a></p>";
		$('#currentshow').html(html);
	}
	evals.timer = setTimeout("evals.sync()", evals.interval);
	evals.draw();
};

evals.submit = function(goodbad,type,value) {
	if(type == 'comment') $('#'+goodbad+'comment').val('');
	var timestamp = evals.curTimestamp;
	var showid = evals.showid;
	if(!timestamp) alert('There is currently no timestamp available. Please refresh the page, or contact the webmaster if this problem persists.');
	$.getJSON('/dj/ajax/evals/submit', { showid:showid, goodbad:goodbad, type:type, value:value, timestamp:timestamp},evals.receiveSubmit);
};

evals.receiveSubmit = function(response) {
	if(response.error) alert(response.error)
	else {
		var evalid = response.eval.evalid;
		evals.data[evalid] = response.eval;
		evals.data[evalid].id = "eval"+evalid;
		evals.draw();
		}
	};

evals.cancel = function(evalid) {
	if(confirm("Do you want to remove this from your evaluation?")) {
		$.getJSON('/dj/ajax/evals/remove', {evalid:evalid}, evals.cancelReceive);
		}
	};

evals.cancelReceive = function(response) {
	if(response.error) alert(response.error);
	else {
		evals.data[response.eval.evalid].active = 0;
		$('#eval'+response.eval.evalid).slideUp(function(){$(this).remove();});
		}
	};

evals.draw = function() {
	// is the box empty?
	if(!$('#currenteval')[0].hasChildNodes()) {
		$('#currenteval').html("<h2>Current Evaluation</h2><div class='address'><a>"+evals.curShowname+" - "+evals.curDate+"</a></div><div class='inner'></div>");
		}
	var totalChanged = 0;
	var html = "";
	var delay = 0;
	for(var x in evals.data) {
		var data = evals.data[x];
		// needs to be added to the box
		if(data.active && $('#'+data.id).length == 0) {
			html += "<div id='"+data.id+"' class='eval'><table><tr>";
			//var smiley = data.goodbad == 'good' ? "/img/icons/smileys/happyfaded48.png" : "/img/icons/smileys/sadfaded48.png";
			//html += "<td class='date' style='background-image:url("+smiley+");'>"+data.date+"</td>";
			html += "<td class='date'>"+data.date+"</td>";
			html += "<td class='img'>";
			if(data.type == 'button') {
				if(evals.categories[data.goodbad][data.value]['icon']) {
					html += "<img src='"+evals.categories[data.goodbad][data.value]['icon']+"' alt='' />";
				}
				//html += "<img src='"+eval("evals.categories."+data.goodbad+"."+data.value+".icon")+"' alt='' />";
			} else if(data.type == 'comment') {
				html += "<img src='"+evals.categories[data.goodbad][data.goodbad+data.type]['icon']+"' />";
			}
			html += "</td><td>";
			if(data.type == 'button') {
				var label = evals.categories[data.goodbad][data.value]['label'];
				if(label) {
					html += label;
					//html += evals.categories[data.goodbad][data.value]['description'];
				}
			} else if(data.type == 'comment' ) {
				html += data.value;
			}
			html += "</td><td class='undoButton'>";
			html += "<button onclick='evals.cancel("+data.evalid+")'>Undo</button>";
			html += "</td></tr></table></div>";
			totalChanged++;
		}
		if(!data.active && $('#'+data.id).length != 0) {
			$('#'+data.id).slideUp(function(){ $(this).remove(); });
		}
	}
	if(totalChanged) $("#currenteval .inner").append(html).scrollTo("max",600);
};

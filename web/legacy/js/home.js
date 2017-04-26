$(document).ready(function(){home.setup()});
home = new Object();
home.prevNum = 0;
home.setup = function() {
	home.total = 0;
	$(".feature").each(function(){this.style.display='block';this.style.left=(home.total++*420)+'px';});
	$(".featurelinks").mouseout(function(){home.mout()});
	var newWidth = 100+$("#featurelink0").width();
	if(newWidth > 290) newWidth = 290;
	$("#activebg").css({width:newWidth});
	$("#activebghover").css({opacity:.4,width:newWidth});
	home.load(0,false);
	home.loop(true);
};

home.timer = null;
home.loop = function(init) {
	if(home.status == "stopped") return;
	if(home.status != "paused" && !init) {
		var next = home.prevNum + 1;
		if(next >= home.total) next = 0;
		home.load(next, false);
	}
	if(home.timer) { clearTimeout(home.timer); home.timer = null; }
	home.timer = setTimeout("home.loop(false)",6800);
	$("#activebghover").css({top:-60});
};

home.autopilot = true;
home.status = "auto"; // auto || paused || stopped

home.load = function(num, stopAutoPilot) {
	if(stopAutoPilot) { home.status = "stopped"; if(home.timer) clearTimeout(home.timer); home.timer = null;}
	$("#features").stop().scrollTo("#feature"+num,750);
	$('featurelink'+home.prevNum).removeClass('active');
	$('featurelink'+num).addClass("active");
	home.prevNum = num;
	$("#activebg").stop().animate({top:20+38*num},300);
	return false;
};

home.mover = function(num){
	$("#activebghover").stop().animate({top:20+38*num},300);
	home.status = "paused";
};
home.mout = function(){
	$("#activebghover").stop().animate({top:20+38*home.prevNum},400);
	home.status = "auto";
};
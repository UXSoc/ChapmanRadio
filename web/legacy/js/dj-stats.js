/*
 
 js/dj-stats.js
 
 by adam borecki
 
 */
$(document).ready(function(){stats.init();});

if(typeof stats == "undefined") stats = {};

/* formatting */
stats.disp = {};
stats.disp.underground = 40;
stats.disp.foundation = 20;
stats.disp.ceiling = 20;
stats.disp.numylabels = 5;
stats.disp.height = 400;
stats.disp.width = 930;
stats.disp.leftspace = 60;
stats.disp.barInnerPadding = 2;
stats.disp.barOuterPadding = 30;
stats.disp.blockheight = stats.disp.height - stats.disp.underground - stats.disp.foundation;

stats.disp.delaySpeed = 40;

stats.clear = function() {
	$('#xaxis').html('');
	$('#yaxis').html('');
	$('#bars').html('');
	stats.barsAreDrawn = false;
	stats.prevPeak = 0;
	stats.prevLabels = null;
};

stats.data = new Array();

stats.prevPeak = 0;
stats.type = 'day';
stats.viewby = function() {
	var prevType = stats.type;
	stats.type = $('#viewbymonth').is(':checked') ? 'month' : 'day';
	stats.prevPeak = 0;
	if(stats.type != prevType) {
		stats.clear();
		if(stats.type == 'month') stats.load(stats.curMonth,true);
		else stats.load(stats.curDay,true);
	}
};

stats.init = function() {
	stats.load(stats.prevDay);
	stats.load(stats.curDay,true);
	// make arrows clickable
	$('#prev').click(function(){stats.prev();}).mouseenter(function(){this.style.opacity=1;}).mouseleave(function(){this.style.opacity=.6});
	$('#next').click(function(){stats.next();}).mouseenter(function(){this.style.opacity=1}).mouseleave(function(){this.style.opacity=.6});
	$('#prev').css({ opacity : 0.6 });
	$('#next').css({ opacity : 0.6 });
};

stats.prev = function() {
	stats.direction = 'down';
	if(stats.type == 'day') {
		stats.load(stats.prevDay,true);
	} else {
		stats.load(stats.prevMonth,true);
	}
};

stats.next = function() {
	stats.direction = 'up';
	if(stats.type == 'day') {
		stats.load(stats.nextDay,true);
	} else {
		stats.load(stats.nextMonth,true);
	}
};

// load a day or month into display
stats.direction = 'down';
stats.prevLabels = null;
stats.curRequest = "";
stats.load = function(request,loadOnReceive) {
	var loadOnReceive = loadOnReceive ? 1 : 0;
	if(!stats.data[request]) {
		if(loadOnReceive) {
			stats.clear();
			$('#loading').show();
		}
		stats.fetch(request,loadOnReceive);
	} else if(loadOnReceive) {
		var data = stats.data[request];
		$('#label').html(data.label);
		if(stats.prevPeak <= data.overallPeak) {
			stats.yaxis(data.overallPeak);
			stats.prevPeak = data.overallPeak;
		}
		if(!stats.prevLabels || !stats.prevLabels.compare(data.labels)) {
			stats.barsAreDrawn = false;
			stats.xaxis(data.labels);
			stats.prevLabels = data.labels;
		}
		if(!stats.barsAreDrawn) {
			stats.drawBars();
		}
		var peak = 0;
		var average = 0;
		var height = 0;
		var color = "";
		if(data.overallPeak <= 0) data.overallPeak = 1;
		for(var x in data.stats) {
			peak = data.stats[x][0];
			height = Math.round(stats.pxperlistener*peak) + stats.disp.foundation;
			color = calcColor(x, 0, peak, data.overallPeak);
			if(peak < 0) { height = 0; color = 'rgb(0,0,0)'; }
			$('#bar'+x+'-1').stop().delay(stats.disp.delaySpeed*x).animate({height:height,backgroundColor:color}).css('overflow','visible');
			$('#bar'+x+'-1 .label').html("Peak: "+peak);
			average = data.stats[x][1];
			height = Math.round(stats.pxperlistener*average) + stats.disp.foundation;
			color = calcColor(x+1, 0, peak, data.overallPeak);
			if(average < 0) { height = 0; color = 'rgb(0,0,0)'; }
			$('#bar'+x+'-2').stop().delay(stats.disp.delaySpeed*x).animate({height:height,backgroundColor:color}).css('overflow','visible');
			$('#bar'+x+'-2 .label').html("Average: "+average);
		}
		stats.prevDay = data.prevDay;
		stats.nextDay = data.nextDay;
		stats.prevMonth = data.prevMonth;
		stats.nextMonth = data.nextMonth;
		if(data.type =='day') {
			stats.load(data.prevDay,false);
			stats.load(data.prevDay,false);
		} else {
			stats.load(data.prevMonth,false);
			stats.load(data.nextMonth,false);
		}
		stats.type = data.type;
		stats.curRequest = request;
	}
};

// courtesy http://www.hunlock.com/blogs/Mastering_Javascript_Arrays
Array.prototype.compare = function(testArr) {
    if (this.length != testArr.length) return false;
    for (var i = 0; i < testArr.length; i++) {
        if (this[i].compare) { 
            if (!this[i].compare(testArr[i])) return false;
        }
        if (this[i] !== testArr[i]) return false;
    }
    return true;
}

// fetch data from server about requested day or month
stats.fetch = function(request,loadOnReceive) {
	var loadOnReceive = loadOnReceive ? 1 : 0;
	$.getJSON(stats.self,{'generate':'jsonstats','request':request,'direction':stats.direction,loadOnReceive:loadOnReceive},function(response){stats.receive(response)});
};

stats.receive = function(response) {
	$('#loading').hide();
	if(response.error) alert(response.error);
	else {
		if(response.type == 'day') stats.curDay = response.request;
		else stats.curMonth = response.request;
		stats.data[response.request] = response;
		if(response.loadOnReceive) stats.load(response.request,true);
	}
};

stats.type = function(request) { return request.length == 7 ? "month" : "day";  }

/* x and y axes */
stats.pxperlistener = 1;
stats.yaxis = function(peak) {
	peak = parseInt(peak);
	if(peak <= 3) peak = 10;
	else peak += 5 - (peak % 5);
	var html = "";
	var labelheight = Math.round(stats.disp.blockheight/stats.disp.numylabels);
	for(var i = 0;i <= stats.disp.numylabels;i++) {
		var bottom = stats.disp.underground + stats.disp.foundation + i*labelheight;
		html += "<div class='label' style='bottom:"+bottom+"px'>"+( peak*i/stats.disp.numylabels )+"</div>";
	}
	$('#yaxis').html(html);
	stats.pxperlistener = labelheight/(peak/stats.disp.numylabels);
};

stats.numLabels = 0;
stats.barWidth = 0;
stats.labelWidth = 0;
stats.xaxis = function(labels) {
	var html = "";
	var labelwidth = Math.floor((stats.disp.width-stats.disp.leftspace)/labels.length);
	for(var x in labels) {
		if(!parseInt(labels[x])) continue;
		var left = stats.disp.leftspace + x*labelwidth;
		html += "<div class='label' style='left:"+left+"px'>"+labels[x]+"</div>";
	}
	$('#xaxis').html(html);
	stats.numLabels = labels.length;
	stats.labelWidth = labelwidth;
	stats.barWidth = labelwidth - stats.disp.barInnerPadding - stats.disp.barOuterPadding;
	if(stats.barWidth < 10) stats.barWidth = 10;
}

/* bars */
stats.barsAreDrawn = false;
stats.drawBars = function() {
	var html = "";
	var height = stats.disp.foundation;
	var left = 0;
	for(var i = 0;i < stats.numLabels;i++) {
		left = stats.disp.leftspace + i*stats.labelWidth;
		html += "<div class='bar bar1' id='bar"+i+"-1' style='width:"+stats.barWidth+"px;left:"+left+"px;height:"+height+"px;'><span class='label'></span></div>";
		left += stats.barWidth + stats.disp.barInnerPadding;
		html += "<div class='bar bar2' id='bar"+i+"-2' style='width:"+stats.barWidth+"px;left:"+left+"px;height:"+height+"px;'><span class='label'></span></div>";
	}
	$('#bars').html(html);
	stats.barsAreDrawn = true;
};

function calcColor(index, min, num, peak) {
	// the statistics drawing utility uses this algorithm too
	var r = 0+Math.round(2.5*min);
	if(index % 2 == 0) r= 150-r;
	var g = Math.round(220*num/peak);
	var b = 175-Math.round(175*num/peak);
	return "rgb("+r+","+g+","+b+")";
}

function calcHeight(num,peak) {
	var height = (stats.height - stats.minheight) * (num/peak);
	height += stats.minheight;
	return Math.round( height );
}

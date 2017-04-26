
$(document).ready(function(){		  
	
	$(document).on('click', '.tabs a', showPane);
	
	$('.showdata').each(function(){
		var showid = this.id.replace("show","");
		if(!panes[showid]) panes[showid] = 'stats';
		var setpane = panes[showid];
		panes[showid] = '';
		$('.tabs a', this)[0].click();
	});
	
	
});

if(typeof panes === 'undefined') panes = new Array();
stats = new Object();
stats.stats = new Array();
stats.lastindex = new Array();
stats.alltimepeak = new Array();
stats.height = 220;
stats.minheight = 10;
stats.width = 360;
stats.animateduration = 400;

paneIsLoaded = new Array();

function showPane(event) {
	
	switch($(this).attr('data-pane')) {
		case 'stats':
			$('#loading').show();
			loadStats($(this).attr('data-showid'));
			break;
		case 'pic':
			break;
		default:
			$('#loading').show();
			$('#'+$(this).attr('data-toggle-target'))
				.html('Loading...')
				.load('/dj/shows', {generate: $(this).attr('data-pane'), showid: $(this).attr('data-showid') }, function(){ })
		}
	}

function loadStats(showid) {
	$.getJSON('/dj/shows',{generate:'stats',showid:showid}, function(response){
		if(!response) alert("The stats could not be loaded.");
		var showid=response.showid;
		if(typeof response.stats[0] === 'undefined') {
		  $("#show"+showid+"stats ").html("<h2>Listenership Statistics</h2><br /><p>No Data.</p>");
		  $('#loading').hide();
		  $('#show'+showid+'stats').slideDown();
		}
		else {
		  stats.stats[showid] = response.stats;
		  stats.lastindex[showid] = stats.stats[showid].length-1;
		  stats.alltimepeak[showid] = response.alltimepeak;
		  createStats(showid, stats.lastindex[showid]);
		  $('#loading').hide();
		  $('#show'+showid+'stats').slideDown();
		}
		});
	}


function createStats(showid, index) {
	$('.stats_prev').css({opacity:.6}).mouseover(function(){if(this.rel=="")$(this).css({opacity:1});}).mouseout(function(){$(this).css({opacity:.6});});
	$('.stats_next').css({opacity:.6}).mouseover(function(){if(this.rel=="")$(this).css({opacity:1});}).mouseout(function(){$(this).css({opacity:.6});});
	setListeners(showid);
	setStats(showid, index);
}

stats.prev = function(showid) {
	var index = stats.lastindex[showid];
	index--;
	if(index < 0) return;
	setStats(showid, index);
};

stats.next = function(showid) {
	var index = stats.lastindex[showid];
	index++;
	if(typeof stats.stats[showid][index] === 'undefined') {
		return;
	}
	else {
		setStats(showid, index);
	}
};

function setStats(showid,index) {
	var last = 0;
	var peak = stats.alltimepeak[showid];
	$('#show'+showid+'stats .stats_label').html(stats.stats[showid][index].label);
	$('#show'+showid+'stats .stats_peak').stop().delay(100).html("<span>Peak: "+stats.stats[showid][index].peak+"</span>").animate({bottom:calcHeight(stats.stats[showid][index].peak,peak)});
	$('#show'+showid+'stats .stats_average').stop().delay(200).html("<span>Average: "+stats.stats[showid][index].average+"</span>").animate({bottom:calcHeight(stats.stats[showid][index].average,peak)});	
	$('#show'+showid+'stats .stats_bar').each(function(){
										 
										  var min = parseInt(this.title);
										  var num = stats.stats[showid][index].data[min];
										  if(num < 0) num = 0;
										  var height = calcHeight(num, peak) ;
										  var color = calcColor(index, min, num, peak);
										  $(this).stop().delay(8*min).animate({height:height,backgroundColor:color}, {duration:stats.animateduration,complete:function(){
																			   this.style.height = height+"px";
																			   this.style.backgroundColor = color;
																			   }});
										 
										 });
	stats.lastindex[showid] = index;
}

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

function setListeners(showid) {
	var peak = stats.alltimepeak[showid];
	var pts = 1;
	if(peak <= 1) pts = 1;
	else if (peak < 8) pts = 3;
	else if (peak < 12) pts = 4;
	else pts = 5;
	for(var i = 0;i <= pts;i++) {
		var val = Math.round(peak*i/pts);
		var bottom = Math.round((stats.height-stats.minheight)*(val/peak)+stats.minheight);
		var listener = "<div class='stats_listener' style='bottom:"+bottom+"px;'>"+val+"</div>";
		$('#show'+showid+'stats .stats_listeners').append(listener);
	}
}

function setupRecordings() {
	$(".recording_data a").click(function(){
							 
							 if(this.rel=='open') {
								 this.rel = '';
								 $("#"+this.id+"-data").slideUp();
							 }
							 else {
								 this.rel = 'open';
								 $("#"+this.id+"-data").slideDown();
							 }
								 							 
							 });
}



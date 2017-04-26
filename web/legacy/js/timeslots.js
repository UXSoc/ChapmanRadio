if(typeof(t) == "undefined") t = new Object();

t.mousedown = false;
t.cellWidth = 101;
t.cellHeight = 33;
t.action = 0; // +1 is adding X's, -1 is removing X's
t.startHour = 7;
t.endHour = 28;
t.prevRow = 0;
t.prevCol = 0;

t.handleMove = function(e, workspace){
	if(!t.mousedown) return;
	var offset = $(workspace).offset();
	var row = t.startHour + Math.floor( (e.pageY - offset.top) / t.cellHeight);
	var col = Math.ceil( (e.pageX - offset.left) / t.cellWidth);
	if(t.prevRow == row && t.prevCol == col && t.action != 0) return;
	var cell = $('#t'+row+'-'+col);
	
	if(t.action == 0) t.action = cell.hasClass("t_selected") ? -1 : 1;
	
	if(t.action == 1) cell.addClass("t_selected").removeClass("t_unselected");
	else cell.removeClass("t_selected").addClass("t_unselected");
	
	t.prevRow = row;
	t.prevCol = col;
	}

t.refreshData = function(){
	var availability = "";
	for(var row = t.startHour;row <= t.endHour;row++)
		for(var col = 1;col <= 7;col+= 1)
			if($('#t'+row+'-'+col).hasClass("t_selected")) availability += row+"-"+col+",";
	$("#availability_output").val(availability);
	}
	
$(document).ready(function(){
	$("#t_div").mousedown(function(e){
		t.action=0;
		t.prevRow = -1;
		t.prevCol = -1;
		t.mousedown = true;
		t.handleMove(e, this);
		});
	
	$(document).mouseup(function(e){
		t.mousedown = false;
		t.refreshData();
		});
	
	$("#t_div").mousemove(function(e){
		t.handleMove(e, this);
		});
	});
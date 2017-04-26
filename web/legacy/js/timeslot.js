	$(document).ready(function(){

	$(document).keypress(function(event){
				 
	   if(event.which == 88 || event.which == 120){
	   timeslot.x = true;
	   if(timeslot.current) eid(timeslot.current).className = 'timeslotBusy';
	   }
	   if(event.which == 67 || event.which == 99) {
	   timeslot.c = true;
	   if(timeslot.current) eid(timeslot.current).className = 'timeslotFree';
	   }
				 
	 });

	$(document).keyup(function(){ timeslot.x = false; timeslot.c = false; });

	$(".timeslotTable a").mouseover(function(){
				  
				  if(timeslot.x) this.className = 'timeslotBusy';
				  if(timeslot.c) this.className = 'timeslotFree';
				  timeslot.current = this.id
				  
				  });
	$(".timeslotTable a").mouseout(function(){
				  
				 if(timeslot.current == this.id) timeslot.current = '';
				  
				  });

	});

	timeslot = new Object();
	timeslot.x = false;
	timeslot.c = false;
	timeslot.current = '';
	// these need to be provided by php
	// timeslot.cols = 7
	// timeslot.rows = 16


	timeslot.render = function() {
	var map = "";
	for(rows = 0;rows < timeslot.rows;rows++) {
	for(cols = 0;cols < timeslot.cols;cols++) {
	map += (document.getElementById('slot'+rows+'-'+cols).className == 'timeslotBusy' ? 1 : 0) + ",";
	}
	map += "-";
	}
	document.getElementById('availability').value = map;
	document.getElementById('inputform').submit();
	};

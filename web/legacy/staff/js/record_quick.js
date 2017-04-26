if(typeof a == 'undefined') a = {};



$(document).ready(function(){

	$('form').submit(a.handleSwipe);
	$('#search').focus();

	a.refresh();

	a.buzzerEffect = new Audio("/legacy/img/effects/buzzer4.ogg");

	});
	
a.handleSwipe = function(e){
	
	e.preventDefault();

	

	a.ready();

	

	var search = $('#search').val().toLowerCase().trim();

	

	if(search.length > 5 && search.length < 10 && !isNaN(parseFloat(search)) && isFinite(search)){

		// This is ok, typed in card #

		search = search*1;

		}

	

	// swiped card didnt work

	else if(search.length != 16 || search.indexOf(';') != 0){

		a.result(2, "INVALID CARD SWIPE");

		return;

		}

	

	// valid swipe

	else {

		search = search.substring(1, 10)*1;

		}

	

	// check we have a number

	if(isNaN(parseFloat(search)) || !isFinite(search)){

		a.result(2, "INVALID CARD ID");

		return;

		}

	
	var dj = a.getDj(search);
	
	if(dj == null){
		a.result(2, "USER NOT FOUND");
		return;
		}
	
	$("#found_dj").html("<img src='"+dj.img310+"' /><br /><strong style='font-size: 20px;'>"+dj.name+"</strong><br />"+dj.djname+" #"+dj.studentid+" (" + dj.classclub + ")");
	
	if(dj.attendance == 'present'){
		a.result(1, "ALREADY PRESENT");
		}
	else {
		a.submit(dj, 'present');
		}
	};

	
a.getDj = function(studentid){
	for(var index in a.djs) {

		var dj = a.djs[index];

		if(dj.studentid == studentid) return dj;

		}
	}



a.submit = function(dj, status) {

	$.getJSON('/staff/ajax/record_attendance', {
		"date" : $('#record-date').val(),
		"type" : $('#record-type').val(),
		"userid": dj.id,

		"status": status
		},
	function(response){
		if(response.error) alert(response.error);
		else{

			a.djs[response.userid].attendance = response.status;

			a.result(0, "PRESENT");

			}
		});

	};



a.timer = null;

a.refresh = function(){

	if(a.timer) { clearTimeout(a.timer); a.timer = null; }

	jQuery.getJSON('/staff/ajax/refresh_attendance', { "date" : $('#record-date').val(), "type" : $('#record-type').val() }, function(response) {

		if(response.error){

			alert(response.error);

			return;

			}

		for(var userid in response.data) if(userid && a.djs[userid]) a.djs[userid].attendance = response.data[userid];

		a.timer = setTimeout("a.refresh()", 10000);

		});

	};

	

a.ready = function(){

	$("#record_background").css({ opacity: 1, background: 'white' });

	$("#record_result").html("");

	$("#found_dj").html("");

	};

	

a.result = function(code, message){

	if(code == 0){

		$("#record_background").css({ background: 'green' });

		}

	else if(code == 1){

		$("#record_background").css({ background: 'orange' });

		}

	else if(code == 2){

		$("#record_background").css({ background: 'red' });

		a.buzzerEffect.play();

		}

	$("#record_result").html(message);

	a.reset();

	}

a.reset = function(){
	$('#search').focus();
	$('#search').val('');

	setTimeout(function(){

		$("#record_background").animate({ opacity: 0.4 });

		}, 750);
	};

	



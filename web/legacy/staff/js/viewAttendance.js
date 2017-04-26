
if(typeof att == 'undefined') att = {};

att.display = [];
att.display[""] = "";
att.display["absent"] = "<b style='color:#A00'>absent</b>";
att.display["present"] = "<b style='color:#090'>present</b>";
att.display["excused"] = "<b style='color:#D60'>excused</b>";
att.modify = function(attendanceid) {
	var status = $('#status'+attendanceid).val();
	var late = $('#late'+attendanceid).val();
	$.getJSON("/staff/ajax/modify_attendance.php", { status: status, late: late, attendanceid: attendanceid }, att.response);
	};

att.response = function(response){
	if(response.error){
		alert(response.error);
		}
	else {
		$('#displayStatus'+response.attendanceid).html(att.display[response.status]);
		$('#displayLate'+response.attendanceid).html(response.status == 'present' ? att.dispLate(response.late) : "");
		}
	};

att.dispLate = function(late) {
	var s = late == 1 ? "" : "s";
	if(late < 0) return "<span style='color:#090'>"+(0-late)+" minute"+s+" early</span>";
	else if(late == 0) return "<span style='color:#848484'>on time</span>";
	else if(late < 8) return "<span style='color:#A60;'>"+late+" minute"+s+" late</span>";
	else { return "<span style='color:#A00;'>"+late+" minute"+s+" late</span>"; }
	};
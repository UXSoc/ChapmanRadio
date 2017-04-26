var CR = CR || {}

$(document).ready(function(){ CR.Grades.Init(); });

CR.Grades = {
	
	Init: function(){
		$(document).on('click', '.cr-grade-edit.-editable', function(){ CR.Grades.Edit(this); });
		$(document).on('submit blur', '.cr-grade-edit.-editing form', function(v){ CR.Grades.Save(v, this); });
		},
	
	Edit: function(e){
		$(e).addClass('-editing')
			.removeClass('-editable')
			.html("<form class='cr-grade-edit-form'><input type='text' value='"+$(e).text()+"' /></form>");
		$(e).find('input').focus();
		},
	
	Save: function(v, f){
		
		v.preventDefault();
		e = $(f).parent();
		
		$(e).addClass('-locked')
			.removeClass('-editing')
			.find('input').attr('disable', true);
		$.ajax({
			type: "POST",
			url: "/staff/ajax/grade_edit",
			dataType : 'json',
			data: {
				grade_id: $(e).attr('data-cr-grade-id'),
				user_id: $(e).attr('data-cr-user-id'),
				value: $(e).find('input').val()
				},
			success: function(data){
				if(!data.result) alert("Warning: Bad Response from Server");
				else {
					if(data.error)alert("Warning: "+data.error);
					CR.Grades.Saved(e);
					}
				}
			});
		},
	
	Saved: function(e){
		$(e).addClass('-editable')
			.removeClass('-locked');
		
		var i = $(e).find('input').val();
		$(e).find('input').remove();
		$(e).text(i);
		}
	
	};
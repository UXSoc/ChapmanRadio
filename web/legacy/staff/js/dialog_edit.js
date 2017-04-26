
$(document).ready(function(){

	$(document).on('click', '.dialog-link', function(e){
	
		Shadowbox.open({
			content: 	$(e.currentTarget).attr('data-dialog'),
			player:		"iframe",
			title:      "Edit",
			//height:     550,
			//width:      700
			});
		});
	});
	
function query(table, id, field, val) {
	$('#query-result').html('<i>Loading...</i>').stop().load('/staff/ajax/query', {table:table,id:id,field:field,val:val});
	}
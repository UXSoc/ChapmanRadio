$(document).ready(function(){
	
	$(document).on('click', '#schedule-week-toggle li', function(){
		if($(this).hasClass('thisweektoggle')){
			$("#schedule .cycle.thisweek").css({ display: 'block' });
			$("#schedule .cycle.nextweek").hide();
			}
		else {
			$("#schedule .cycle.thisweek").hide();
			$("#schedule .cycle.nextweek").css({ display: 'block' });
			}
		$('#schedule-week-toggle li').removeClass('active');
		$(this).addClass('active');
		});
	});
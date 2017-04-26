<?php namespace ChapmanRadio;

define('PATH', '../');
require_once PATH."inc/global.php";

Template::SetPageTitle("Marathon Signup");
Template::SetPageSection("");
Template::SetBodyHeading("DJ Resources", "Request Marathon Slot");
Template::RequireLogin("DJ Account");

Template::JS("/legacy/js/jquery-ui-dragdrop.min.js");

Template::Bootstrap();

Template::Style("
	.cr-marathon-show { border: 1px solid black; width: 66px; height: 66px; display: inline-block; margin: 10px; }
	
	.cr-marathon-schedule th { text-align: center; }
	.cr-marathon-schedule td:first-child { width: 44px; background: #FFF; padding: 3px; border: none; }
	
	.cr-marathon-slot { margin: 2px; height: 66px; width: 66px; background: #CCC; border: 1px solid #999; border-radius: 3px; }
	.cr-marathon-slot.cr-marathon-saving { background: url('/img/loading/25_56_79C043_CCCCCC.gif') 50% 50% no-repeat #CCCCCC; }
	
	.cr-marathon-slot.cr-marathon-available.ui-state-hover { background: #EEE; border: 1px solid green; }
	.cr-marathon-slot.cr-marathon-taken.ui-state-hover { border: 1px solid red; }
	");

Template::Script("
	$(document).ready(function(){
		$('.cr-marathon-show').draggable({
			helper: 'clone'
			});
		$('.cr-marathon-slot').droppable({
			activeClass: 'ui-state-default',
			hoverClass: 'ui-state-hover',
			drop: function( event, ui ) {
				$(this)
					.addClass('cr-marathon-taken')
					.addClass('cr-marathon-saving')
					.removeClass('cr-marathon-available');
				cr_marathon_save(this, $(this).attr('data-slot'), ui.draggable.attr('data-showid'));
				}
			});
		});
	function cr_marathon_save(slot_ui, slot, show){
		// ajax save
		setTimeout(function() { 
			$.post('/dj/ajax/marathon-save', {
				slot: slot, show: show
				},function(data){
					if(data.result == 'error'){
						alert('There was a problem saving your slot: ' + data.message + '\\nPlease refresh the page and try again');
						}
					else{
						$('#cr-marathon-slot-'+slot).html('<img title=\"' + data.show.name + '\" src=\"' + data.show.img64 + '\" />');
						}
				}, 'json')
			.fail(function(){
				alert('There was a problem saving your slot. Please refresh the page and try again');
				})
			.always(function(){
				$(slot_ui).removeClass('cr-marathon-saving');
				});
			}, 500);
		}
	");

// get the user ready to go
$user = Session::getCurrentUser();
$shows = $user->GetShowModels();


Template::Add("<div style='margin: 0 auto; width: 640px;'>");
Template::Add("<div class='couju-info'>Drag a show from My Shows and drop it on any open slot</div>");


Template::Add("<h2>My Shows</h2>");

$at_least_one = false;
foreach($shows as $show){
	if($show->status != 'accepted') continue;
	$at_least_one = true;
	Template::Add("<div class='cr-marathon-show' data-showid='{$show->id}'><img title='{$show->name}' src='{$show->img64}' /></div>");
	}

if(!$at_least_one){
	Template::Add("<div class='couju-error'>You have no shows that can be scheduled. Please contact webmaster</div>");
	Template::Finalize();
	}

Template::Add("<hr class='_clear' />");

Template::Add("</div>");

Template::Add("<div style='margin: 0 auto; width: 640px;'>");
Template::Add("<h2>Marathon Schedule</h2>");

// TODO
// $base = strtotime("may 16 2015 12:00am");
// $season = "2015SM";

// TODO
// $base = strtotime("dec 14 2015 12:00am");
// $season = "2015FM";

$base = strtotime("may 16 2016 12:00am");
$season = "2016SM";
$days = 4;

$starthour = 5;
$endhour = 28;
Template::Add("<table class='cr-marathon-schedule'>");

Template::Add("<tr><th></th>");
for($day = 0; $day <= $days; $day++) Template::Add("<th>" . date('D<\b\r/>n/j', strtotime("+$day days ", $base)) . "</th>");
Template::Add("</tr>");

for($hour = $starthour; $hour <= $endhour; $hour++) {
	Template::Add("<tr><td>".Util::hourName($hour)."</td>");
	for($day = 0; $day <= $days; $day++) {
		$stamp = strtotime("+$day days $hour hours ", $base);
		$show = TimestampToShow($stamp, $season);
		$class = ($show == null) ? "cr-marathon-available" : "cr-marathon-taken";
		Template::Add("<td><div id='cr-marathon-slot-{$stamp}' class='cr-marathon-slot {$class}' data-slot='{$stamp}'>");
		if($show != null) Template::Add("<img title='{$show->name}' src='{$show->img64}' />");
		Template::Add("</div></td>");
		}
	Template::AddBodyContent("</tr>");
	}
Template::Add("</table>");

Template::Add("</div>");

Template::Add("<hr class='_clear' />");

Template::Finalize();

# --

function TimestampToShow($timestamp, $season){
	$showid = Schedule::GetShowAt($timestamp, 'none', $season); // $base + (3600*$hour) + (3600*24*$day) + (3600*24*7*$cycle));
	// echo "Render($season, $base, $hour, $day, $cycle) --> $showid<br />";
	if($showid == 0) return NULL;
	$show = ShowModel::FromId($showid, true); // uses cache
	if(!$show || $show->status == 'cancelled') return NULL;
	return $show;
	}

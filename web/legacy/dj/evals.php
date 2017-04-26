<?php namespace ChapmanRadio;

define('PATH', '../');	
require_once PATH."inc/global.php";

Template::SetPageTitle("Show Evals");
Template::RequireLogin("DJ Account");

Template::AddStaffAlert("You can see completed evals at <a href='/staff/evals'>/staff/evals</a>");

Template::css("/css/dl.css");
Template::css("/css/buttons.css");

Template::SetBodyHeading("DJ Resources", "Show Evaluations");
Template::AddBodyContent("
	<table style='margin:20px auto;font-size:13px;text-align:left;'><tr><td style='padding-right:60px;'>
	<a href='/dj/eval' class='largeButton green'>New Eval</a>
	<br />
	<p style='width:244px;color:#484848;'>Start a new evaluation for the show that's broadcasting now.</p>
	</td><td>
	<a href='/dj/myevals' class='largeButton blue'>My Evals</a>
	<br />
	<p style='width:244px;color:#484848;'>View evaluations that other DJs have completed for your show.</p>
	</td></tr></table>
	");

Template::Finalize();
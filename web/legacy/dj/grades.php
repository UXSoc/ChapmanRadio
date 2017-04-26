<?php namespace ChapmanRadio;

define('PATH', '../');
require_once PATH."inc/global.php";

Template::SetPageTitle("My Grades - DJ Resources");
Template::RequireLogin("DJ Account");Template::Bootstrap();

Template::Css("/dj/css/page-grades.css?1");

$season = Season::current();
Template::SetBodyHeading("My Grades for ".Season::Name());$user = Session::GetCurrentUser();if($user->classclub == 'club'){	Template::AddCoujuInfo("You're in the club! You don't have to worry about grades, just be sure to go to the required wednesday night meetings and your show.");	//Template::Finalize();	}
else{
	Template::AddCoujuInfo("In order to pass the Chapman Radio class, you must pass each of the categories. A passing grade is indicated with a green background, a failing category is indicated with a red background.");
	}

$grades = GradeStructureModel::ForUser($user->id);
	
// Grade table

if(empty($grades)){
	Template::AddCoujuNotice("There are no grades configured yet for this season. Please check back later or email accedemics");
	}
else{
	Template::Add("<table class='table'><thead><tr><th></th><th>My Score</th><th>Possible</th><th>Percent</th></tr></thead><tbody>");
	foreach($grades as $grade) RenderGrade($grade);
	Template::Add("<tr><td><strong>Total</strong></td><td>-</td><td>-</td><td>-</td></tr>");
	Template::Add("</tbody></table>");
	}
	
Template::Finalize();

function RenderGrade($grade, $prefix = ""){
	$scoredisp = $grade->DisplayScore();
	$scoreperc = $grade->DisplayPercent();
	
	// if($grade->name == "Midterm") return;
	
	$cat = $grade->Cat();
	
	$max = $grade->max;
	if ($grade->name == "Activity Points") $max .= " ({$grade->target} minimum)";
	
	Template::Add("<tr class='cr-grade -{$cat}'><td>{$prefix}{$grade->name}</td><td>{$scoredisp}</td><td>{$max}</td><td>{$scoreperc}</td></tr>");
	foreach($grade->children as $child) RenderGrade($child, " -- ");
	}
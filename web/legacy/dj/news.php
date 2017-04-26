<?php namespace ChapmanRadio;

define('PATH', '../');	
require_once PATH."inc/global.php";

Template::SetPageTitle("Class News");
Template::RequireLogin("DJ Account");
Template::Bootstrap();

Template::Css("/css/page-news.css");

Template::SetBodyHeading("Class News");
Template::AddStaffAlert("You can post news here at <a href='/staff/classnews'>/staff/classnews</a>");

$news = NewsModel::Paged(10);

Template::AddBodyContent("<div class='page-news'>");

if(empty($news)) Template::AddCoujuNotice("No news to display");
else foreach($news as $newsitem)
	Template::AddBodyContent("<article>
		<h2>{$newsitem->title}</h2>
		<span class='date'>Posted ".date("F jS, Y", $newsitem->posted_unix)."</span>
		<p>{$newsitem->body}</p></article>");

Template::AddBodyContent("</div>");

Template::Finalize();
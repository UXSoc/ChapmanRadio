<?php
namespace AppBundle\Controller\staff;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 3:05 PM
 */

use ChapmanRadio\DB;
use function ChapmanRadio\error;
use ChapmanRadio\Request;
use ChapmanRadio\Schedule;
use ChapmanRadio\Session;
use ChapmanRadio\Site;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class GenreContentController extends  Controller{
    /**
     * @Route("/staff/genre", name="staff_genre")
     */
    public function indexAction(ContainerInterface $container = null)
    {

        Template::SetPageTitle("Genre Content");
        Template::SetBodyHeading("Site Administration", "Genre Content");
        Template::RequireLogin("Staff Resources", "staff");

        Template::css(PATH."css/formtable.css");
        Template::style(Schedule::styleGenres());

        $season = Site::CurrentSeason();

        $genres = Site::$Genres;
        foreach($genres as $k => $v) $genres[$k] = trim($v);

        $genredata = array();
        $data = DB::GetAll("SELECT genre, COUNT(*) AS total FROM shows WHERE seasons LIKE '%$season%' GROUP BY genre");
        foreach($genres as $g) $genredata[$g] = 0;
        foreach($data as $row) $genredata[$row['genre']] = $row['total'];

        if(!isset($_REQUEST['genre'])) {
            Template::AddBodyContent("<div style='width:600px;margin:10px auto;text-align:left;'>");
            Template::AddBodyContent("<h3>Pick a Genre</h3><p>For which genre would you like to edit the content?</p>
	<form method='get' action='$_SERVER[PHP_SELF]'><table class='formtable' cellspacing='0'>
		<tr class='oddRow'><td style='text-align:center'>Pick a Genre</td></tr>
		<tr class='evenRow'><td>");
            foreach($genres as $genre) {
                $s = $genredata[$genre] == 1 ? "" : "s";
                $genreClass = preg_replace("/\\W/","", $genre);
                Template::AddBodyContent("
			<div style='float:left;margin:-4px 6px 4px 10px;padding:4px 10px;' class='$genreClass'>
			<input type='radio' name='genre' value='$genre' style='width:auto;' id='$genre' />
			</div>
			<p><label for='$genre'><a>$genre <i>({$genredata[$genre]} show$s)</i></a></label></p>
			<br style='clear:both;' />
		");
            }
            Template::AddBodyContent("</td></tr>
		<tr class='oddRow'><td style='text-align:center;'><input type='submit' value=' Edit Content ' /></td></tr>
	</table></form>");
            Template::AddBodyContent("</div>");
            Template::Finalize();
        }
        else if(!in_array(Request::Get('genre'), $genres)) {
            error("You've entered an invalid genre. please go back and try again");
        }

        $genre = Request::Get('genre');

// okay, now we have a trimmed $genre
        Template::SetPageTitle("$genre on Chapman Radio");
        $genreClass = preg_replace("/\\W/","",$genre);

// save their data
        if(isset($_POST['SAVE_CONTENT'])) {
            $content = Request::Get('genrecontent');
            $staffid = Session::GetCurrentUserId();
            $lastmodified = date("Y-m-d H:i:s");
            DB::Query("UPDATE genrecontent SET content = :content, staffid='$staffid', lastmodified='$lastmodified' WHERE genre='$genre'", array(":content" => $content));
            Template::AddBodyContent("<p style='color:#090;text-align:center;'>Your changes have been saved.</p>");
        }

// does the row in genrecontent table exist?
        $genrecontent = DB::GetFirst("SELECT * FROM genrecontent WHERE genre='$genre'");
        if(!$genrecontent) {
            DB::Insert("genrecontent", array("genre" => $genre, "content" => ""));
            $genrecontent = DB::GetFirst("SELECT * FROM genrecontent WHERE genre='$genre'");
        }

        if(!$genrecontent['content']) $genrecontent['content'] = "<p>Hi there, $genre DJs!</p><p>Let me explain why <b>$genre</b> is <i>awesome!</i></p><p>I'll keep this updated with new $genre releases.</p><p><b>NOTE:</b> You can use this editor to stylize your words, add links, and even upload images.</p>";


        Template::AddBodyContent("<form method='post' action='$_SERVER[PHP_SELF]'>");
        Template::AddBodyContent("<div style='width:750px;margin:10px auto;text-align:left;'>");
        Template::AddBodyContent("<h3 style='font-weight:normal;'>Editing <b>$genre</b> Content</h3><p>You are now editing the content for DJs with $genre shows.</p>");

// get xinha
        Template::JS("/plugins/tinymce/tinymce.min.js");
        Template::Script("tinymce.init({ selector: '.tinymce', plugins: [ 'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code insertdatetime media table contextmenu paste textcolor'], toolbar: 'insertfile undo redo | styleselect | bold italic | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image' });");

        Template::AddBodyContent("<div style='text-align:center;margin:10px auto;'><textarea class='tinymce' name='genrecontent' rows='26' cols='75' style='margin:auto;'>$genrecontent[content]</textarea></div>");

// info
        $user = Session::GetCurrentUser();
        $lastupdated = date("l, F jS, Y");
        Template::css(PATH."css/dl.css");
        Template::AddBodyContent("<div class='gloss' style='padding:0px;'>
	<div class='address'><a>$genre</a></div>
	<p class='$genreClass' style='padding:2px 0 2px 10px;height:12px;'></p>
	<table style='margin:auto;'><tr><td><img src='{$user->img64}' style='margin-right:10px;' /></td>
	<td><dl>
		<dt>Last updated</dt>
		<dd>$lastupdated</dd>
		<dt>Updated by</dd>
		<dd>{$user->name}, <i style='color:#757575'>{$user->staffposition}</i></dd>
	</dl>
	</td></tr></table>
</div>");

// submit button
        Template::AddBodyContent("<div style='margin:40px auto 40px;text-align:center;'>
	<input type='hidden' name='genre' value='$genre' />
	<input type='submit' name='SAVE_CONTENT' value=' Save $genre Content' />
</div>");

// close tags
        Template::AddBodyContent("</div></form>");

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }
}
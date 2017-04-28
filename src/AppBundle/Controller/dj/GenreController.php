<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 8:57 AM
 */

namespace AppBundle\Controller\dj;


use ChapmanRadio\DB;
use ChapmanRadio\Evals;
use ChapmanRadio\Request as ChapmanRequest;
use ChapmanRadio\Schedule;
use ChapmanRadio\Session;
use ChapmanRadio\Site;
use ChapmanRadio\Template;
use ChapmanRadio\UserModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class GenreController extends Controller
{

    /**
     * @Route("/dj/genre", name="dj_genre")
     */
    public function indexAction(Request $request)
    {
        define('PATH', '../');

        Template::SetPageTitle("My Genre");
        Template::SetBodyHeading("DJ Resources", "My Genre");
        Template::RequireLogin("/dj/genre","DJ Account");

        Template::css("/legacy/css/dl.css");

// output the header
        Template::AddBodyContent("<div style='width:600px;margin:10px auto;text-align:left;'>");

// get the user ready to go
        $userid = Session::getCurrentUserID();

// let's style the genres
        Template::style(Schedule::styleGenres());

// get all available genres

        $genres = Site::$Genres;

        foreach ($genres as $k => $v) $genres[$k] = trim($v);

        sort($genres);


// what should the default genre be?

        $genre = ChapmanRequest::Get('genre', '');

        if ($genre && !in_array($genre, $genres)) $genre = "";

        if (!$genre) {

            $show = DB::GetFirst("SELECT genre FROM shows WHERE userid1='$userid' OR userid2='$userid' OR userid3='$userid' OR userid4='$userid' OR userid5='$userid' LIMIT 0,1");

            if ($show) $genre = trim($show['genre']);

            if (!$show || !in_array($genre, $genres) || !$genre) $genre = "Alternative/Indie";

        }


        $genreClass = preg_replace("/\\W/", "", $genre);

        $genrecontent = DB::GetFirst("SELECT genrecontent.content, genrecontent.lastmodified, users.* FROM genrecontent JOIN users ON users.userid = genrecontent.staffid WHERE genre='$genre' LIMIT 0,1");


// let's output the navigation

        $path = $request->getRequestUri();
        Template::AddBodyContent("<form method='get' action='$path ' id='changegenre'><div class='address  '><a>Genre:</a> <span style='position:relative;top:2px;'><select name='genre' onchange='$(\"#changegenre\").submit();'><option value=''> - Pick a Genre - </option>");

        foreach ($genres as $g)
            Template::AddBodyContent("<option value='$g' " . ($g == $genre ? "selected='selected'" : "") . ">$g</option>");
            Template::AddBodyContent("</select> <input type='submit' value='&gt;' /></span></div></form><p class='$genreClass' style='padding:2px 0 2px 10px;height:12px;'></p><div style='padding:10px 10px 60px;text-align:left;'>");
        if (!$genrecontent || !$genrecontent['content']) {
            Template::AddBodyContent("<p style='color:#757575'>Sorry, we don't have any content for <b>$genre</b> right now.</p>");
        } else {

            Template::AddBodyContent("<div class='external-content'>" . $genrecontent['content'] . "</div>");
            $lastupdated = date("l, F jS, Y", strtotime($genrecontent['lastmodified']));
            $staffmember = UserModel::FromResult($genrecontent);


            Template::AddBodyContent("<br /><div class='gloss' style='padding:0px;'>
	<div class='address'><a>$genre</a></div>
	<p class='$genreClass' style='padding:2px 0 2px 10px;height:12px;'></p>
	<table style='margin:auto;'><tr><td><img src='{$staffmember->img64}' style='margin-right:10px;' /></td>
	<td><dl>
	<dt>Last updated</dt>
	<dd>$lastupdated</dd>
	<dt>Updated by</dt>
	<dd>{$staffmember->name}, <i style='color:#757575'>{$staffmember->staffposition}</i></dd>
	</dl>
	</td></tr></table>
	</div>");
        }


        Template::AddBodyContent("</div>");


// finish up

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize($request,"</div>"));

    }
}
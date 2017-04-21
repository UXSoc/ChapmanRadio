<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 8:57 AM
 */

namespace AppBundle\Controller\dj;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 8:08 AM
 */

namespace AppBundle\Controller\dj;


use ChapmanRadio\DB;
use ChapmanRadio\Evals;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FinalController extends Controller
{

    /**
     * @Route("/dj/final", name="dj_eval")
     */
    public function indexAction(ContainerInterface $container = null)
    {

        Template::SetPageTitle("Final Exam");
        Template::RequireLogin("Final Exam");

        Template::Bootstrap();

        if (isset($_POST['request'])) {
            $request = DB::GetFirst("SELECT * FROM finalexam WHERE exam_user = :uid AND exam_season = :season", [
                ":season" => Season::Current(),
                ":uid" => Session::GetCurrentUserId()
            ]);

            if (!$request) {
                DB::Query("INSERT INTO finalexam (exam_user, exam_mp3, exam_season) VALUES(:user, :mpid, :season)", [
                    ":user" => Session::GetCurrentUserId(),
                    ":mpid" => Request::GetInteger('mp3id'),
                    ":season" => Season::Current()
                ]);
                Template::AddBodyContent("<div class='couju-success'>Your exam has been submitted</div>");
            }
        }

        if (isset($_POST['cancel'])) {
            $request = DB::GetFirst("SELECT * FROM finalexam WHERE exam_user = :uid AND exam_season = :season", [
                ":season" => Season::Current(),
                ":uid" => Session::GetCurrentUserId()
            ]);

            if ($request) {
                DB::Query("DELETE FROM finalexam WHERE exam_user = :uid AND exam_season = :season", [
                    ":season" => Season::Current(),
                    ":uid" => Session::GetCurrentUserId()
                ]);
                Template::AddBodyContent("<div class='couju-success'>Your exam submission has been deleted</div>");
            }
        }

        $user = Session::GetCurrentUser();

        $recordings = RecordingModel::FromResults(DB::GetAll("SELECT * FROM mp3s WHERE recordedon > :start AND showid IN (
	SELECT showid FROM shows WHERE userid1 = :uid OR userid2 = :uid OR userid3 = :uid OR userid4 = :uid OR userid5 = :uid)",
            [":uid" => Session::GetCurrentUserId(), ":start" => date('Y-m-d', Season::CurrentStartUnix())]));

        if (count($recordings) == 0) Template::AddBodyContent("<div class='couju-debug'>You have no recordings eligible for your final exam. Please contact webmaster@chapmanradio.com if this is not correct.</div>");

        $request = DB::GetFirst("SELECT * FROM finalexam WHERE exam_user = :uid AND exam_season = :season", [
            ":season" => Season::Current(),
            ":uid" => Session::GetCurrentUserId()
        ]);

        if ($request) {
            Template::AddBodyContent("<div class='couju-info'>
		You have selected a recording to be used for your final exam. If you need to change your recording, you may do so below. <br />
		Email academics@chapmanradio.com with any questions.
		</div>");
        } else {
            Template::AddBodyContent("<div class='couju-info'>
		If you are in the Class, you must select a recording to be used for your final exam.<br />
		Email academics@chapmanradio.com with any questions.
		</div>");
        }

        Template::AddBodyContent("<table class='table'>");

        foreach ($recordings as $recording) {
            if (!($request == NULL || $request['exam_mp3'] == $recording->id)) continue;

            $lastmp3id = $recording->id;
            $timestamp = strtotime($recording->recordedon);
            $shortdate = date('n/j/y', $timestamp);
            $longdate = date('ga - F jS', $timestamp);
            $month = date('M', $timestamp);
            $day = date('j', $timestamp);
            $label = $recording->label ? $recording->label : "Untitled Show, " . date("F jS", $timestamp);
            $description = (trim($recording->description)) ? $description = "<p>" . str_replace("\n", "<br />", $recording->description) . "</p>" : "";

            $streamer = $recording->Exists() ? "<div class='label'>play</div>
				<div class='rec_player' onclick='rec.play($lastmp3id, \"{$recording->PubUrl("stream")}\")'>
					<a class='play'></a>
					<div class='disp'>Listen to this episode</div>
					<div class='bar'></div>
				</div>
				<br />" : "";

            $downloader = $recording->Exists() ? "<div class='label'>download</div>
			<div class='rec_dl' onclick='window.location=\"{$recording->PubUrl("download")}\"'>
				<div class='dl'></div>
				<div class='disp'>Download this episode</div>
			</div>
			<br />" : "";

            Template::AddBodyContent("

	<tr>
		<td><div class='calendaricon'><span class='month'>$month</span><span class='day'>$day</span></div></td>
		<td>$longdate</td>
		<td>$label</td>
		<td>$description</td>
		<td>
			<form class='form' method='POST'>
				
				<input type='hidden' name='mp3id' value='" . $recording->id . "' />" .
                (($request == NULL) ? "<input type='submit' name='request' value='Use this Recording' />" : "<input type='submit' name='cancel' value='Do not use this Recording' />")
                . "
			</form>
		</td>
	</tr>");

        }

        Template::AddBodyContent("</table>");
        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }
}
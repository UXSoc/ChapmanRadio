<?php
namespace AppBundle\Controller\Staff;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 3:05 PM
 */


use ChapmanRadio\DB;
use ChapmanRadio\RecordingModel;
use ChapmanRadio\Season;
use ChapmanRadio\Session;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class FinalController extends  Controller{
    /**
     * @Route("/staff/final", name="staff_final")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        Template::SetPageTitle("Staff");
        Template::SetBodyHeading("Site Administration", "Final Exams");
        //Template::RequireLogin("/staff/final","Staff Resources", "staff");
        Template::TableSorter();

        if(isset($_POST['submit'])){
            $request = DB::GetFirst("SELECT * FROM show_sitins WHERE season = :season AND showid = :id", array(":season" => Season::Current(), ":id" => Request::GetInteger('showid')));

            if(!$request){
                Template::AddBodyContent("<div class='couju-error'>That request no longer exists</div>");
            }

            else if($request['result'] != 0){
                Template::AddBodyContent("<div class='couju-error'>Someone is already assigned to that request</div>");
            }
            else {
                DB::Query("UPDATE show_sitins SET result = :staffid WHERE showid = :show AND season = :season", array(":staffid" => Session::GetCurrentUserId(), ":show" => Request::GetInteger('showid'), ":season" => Season::Current()));
                Template::AddBodyContent("<div class='couju-success'>You are now assigned to this request</div>");
            }
        }

        if(isset($_POST['cancel'])){
            $request = DB::GetFirst("SELECT * FROM show_sitins WHERE season = :season AND showid = :id", array(":season" => Season::Current(), ":id" => Request::GetInteger('showid')));

            if($request){ // && $request['result'] == Session::GetCurrentUserId()){
                DB::Query("UPDATE show_sitins SET result = 0 WHERE showid = :show AND season = :season", array(":show" => Request::GetInteger('showid'), ":season" => Season::Current()));
                Template::AddBodyContent("<div class='couju-success'>You are no longer assigned to this request</div>");
            }
            else if($request){
                Template::AddBodyContent("<div class='couju-error'>You can't be removed from a request you are not assigned to</div>");
            }
            else {
                Template::AddBodyContent("<div class='couju-error'>That request no longer exists</div>");
            }
        }

        $exams = DB::GetAll("SELECT * FROM finalexam LEFT JOIN users ON exam_user = users.userid LEFT JOIN mp3s ON exam_mp3 = mp3s.mp3id WHERE exam_season = :season",
            [":season" => Season::Current() ]);

        $user = Session::GetCurrentUser();

        Template::AddBodyContent("<table class='tablesorter' style='width: 100%; text-align: left;'>
	<thead><tr><th>User</th><th>Show Time</th><th>Recording</th><th>Download</th></tr></thead><tbody>");

        Template::js("/legacy/js/recordings.js");
        Template::css("/legacy/css/recordings.css?v2");
        Template::js("/legacy/plugins/soundmanager/script/soundmanager2-nodebug-jsmin.js");
//Template::js("/plugins/soundmanager/script/soundmanager2.js");
        Template::script("soundManager.url = '/plugins/soundmanager/swf/'; soundManager.useFlashBlock = false; soundManager.useHTML5Audio = true;");


        foreach($exams as $exam)
        {
            $recording = new RecordingModel($exam);
            $streamer = $recording->Exists() ? "
		<div id='recording{$recording->id}'><div class='rec_player' onclick='rec.play({$recording->id}, \"{$recording->PubUrl("stream")}\")'>
			<a class='play'></a>
			<div class='disp'>Listen to this episode</div>
			<div class='bar'></div>
		</div></div>" : "";

            $downloader = $recording->Exists() ? "
		<div class='rec_dl' onclick='window.location=\"{$recording->PubUrl("download")}\"'>
			<div class='dl'></div>
			<div class='disp'>Download this episode</div>
		</div>
		<br />" : "";

            Template::AddBodyContent("<tr><td>{$exam['userid']} {$exam['fname']} {$exam['lname']}</td><td>".$exam['recordedon']."</td><td>{$streamer}</td><td>{$downloader}</td></tr>");
        }

        Template::AddBodyContent("</tbody></table>");
        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize($this->container));
    }
}
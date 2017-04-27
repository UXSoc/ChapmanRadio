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
use ChapmanRadio\GradeStructureModel;
use ChapmanRadio\Request as ChapmanRadioRequest;
use ChapmanRadio\Season;
use ChapmanRadio\Session;
use ChapmanRadio\Site;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class QuizController extends Controller
{

    /**
     * @Route("/dj/quiz", name="dj_quiz")
     */
    public function indexAction(Request $request)
    {
        define('PATH', '../');

        Template::SetPageTitle("Quiz");
        Template::SetBodyHeading("Chapman Radio", "Quiz");
        Template::RequireLogin("/dj/quiz","Chapman Radio Quiz");

// settings for new quizes
        $qsPerQuiz = 10;

        Template::css("/legacy/css/formtable.css");
        Template::css("/legacy/css/subnav.css");
        Template::css("/legacy/css/ui.css");
        Template::js("/legacy/js/jquery.scrollTo.js");
        Template::js("/legacy/js/subnav.js");
        Template::js("/legacy/js/postform.js");
        Template::script("\$(document).ready(function(){ $('#subcontentContainer').css({ height : '750px' }); });");

        Template::AddBodyContent("<p>All members of Chapman Radio are required to pass the Quiz.<br />If you fail, you'll have to retake it until you get a perfect score.</p>");

        $user = Session::GetCurrentUser();

        $passed = $user->HasQuizSeason(Site::CurrentSeason());
        if($passed) Template::Error("You have already passed the quiz for this semester.");

        $action = "";
        $quizid = ChapmanRadioRequest::GetInteger('quizid', ChapmanRadioRequest::GetInteger('quiz', ChapmanRadioRequest::GetIntegerFrom($_SESSION, 'quizid')));
        if(!$quizid && !$passed) $action = "newquiz";
        if($quizid) $action = "continuequiz";

// load the quiz questions data
        $quizquestions = array();
        $result = DB::GetAll("SELECT * FROM quizquestions WHERE active=1 ORDER BY rand()");
        foreach($result as $row) $quizquestions[$row['quizquestionid']] = $row;

// should we process the quiz?
        if(isset($_POST['ProcessQuiz'])) {
            $results = "";
            $quiz = DB::GetFirst("SELECT * FROM quizes WHERE quizid='$quizid'");
            if(!$quiz) {
                unset($_SESSION['quizid']);
                Template::error("It appears that the quiz you are looking for could not be found.");
            }
            $right = 0;
            $wrong = 0;
            $total = 0;
            for($i = 1; isset($quiz["q$i"]); $i++) {
                $q = json_decode($quiz["q$i"], true);
                $quizquestion = @$quizquestions[$q['quizquestionid']];
                if(!$quizquestion) continue;
                $responses = explode("[%,%]",$quizquestion['responses']);
                $rowclass = ++$total % 2 == 0 ? 'evenRow' : 'oddRow';
                $results .= "<tr class='$rowclass'><td colspan='2' style='text-align:center;font-weight:bold;'>$quizquestion[question]</td></tr>";
                $results .= "<tr class='$rowclass'><td>";
                if(isset($_REQUEST["q$i-response"]))
                    $results .= "Your response: ".$responses[$_REQUEST["q$i-response"]]." &nbsp; ";
                else
                    $results .= "<i style='color:#757575'>No Response</i>";
                $results .= "</td><td style='padding-bottom:40px;'>";
                if(isset($_REQUEST["q$i-response"]) && @$_REQUEST["q$i-response"] == 0) {
                    $results .= "<b style='color:#090'>Correct</b>";
                    $right++;
                }
                else {
                    $results .= "<b style='color:#A00'>Incorrect</b>";
                    $wrong++;
                }
                $results .= "</td></tr>";
            }
            //Template::AddBodyContent("<pre>".print_r($_POST,true)."</pre>");
            Template::AddBodyContent("<div class='leftcontent'><h3>Quiz Results:</h3>");
            if($right == $total) {
                Template::AddBodyContent("<p>Congratulations!</p><p>You've passed the quiz. Your account is now fully activated.</p>");
                $user->AddQuizSeason(Site::CurrentSeason());
            }
            else {
                Template::AddBodyContent("<p>Sorry, but you've failed the quiz.</p><p>You need to score 100%, but you got <b>".(round(100*$right/$total))."%</b>. <a href='/quiz'>Retake the Quiz</a></p>");
            }
            Template::AddBodyContent("<table class='formtable' cellspacing='0' style='margin:10px auto;'>
		<tr class='evenRow'><td colspan='2'><h2 style='text-align:center;'><span>Your Score: ".(round(100*$right/$total))."%</span></h2></td></tr>
		$results</table>");
            Template::AddBodyContent("</div>");
            unset($_SESSION['quizid']);
            DB::Query("UPDATE quizes SET `completed`='1',`right`='$right',`wrong`='$wrong',`total`='$total' WHERE quizid='$quizid'");
            Template::Finalize();
        }

        switch($action) {
            case "newquiz":
                $totalQuizQuestions = count($quizquestions);
                $randkeys = array_rand($quizquestions, $totalQuizQuestions);

                $data = array("userid" => $user->id, "startedon" => time());
                for($i = 1; $i <= $qsPerQuiz && $i <= $totalQuizQuestions;$i++) {
                    $q = array("quizquestionid"=> $quizquestions[$randkeys[$i-1]]['quizquestionid'], "response"=>-1);
                    $data["q$i"] = json_encode($q);
                }

                $quizid = DB::Insert("quizes", $data);

            case "continuequiz":
                $_SESSION['quizid'] = $quizid;
                $quiz = DB::GetFirst("SELECT * FROM quizes WHERE quizid='$quizid'");
                if(!$quiz) {
                    unset($_SESSION['quizid']);
                    Template::error("Sorry, but the quiz you are looking for could not be found.");
                }

                $path = $request->getRequestUri();
                Template::AddBodyContent("<div class='leftcontent' style='margin:20px auto;'>
		<form method='post' action='$path' id='quizform'><table><tr><td>
		<input type='hidden' name='quizid' value='$quizid' />
		<input type='hidden' name='ProcessQuiz' value='1' />
		
		<h4 class='subnavh4'>Quiz</h4>
		<ul class='subnav'>");

                $categories = array();
                for($totalQuestions = 1; isset($quiz["q$totalQuestions"]); $totalQuestions++) $categories[] = "Question $totalQuestions";
                $qTotal = $totalQuestions;

                Template::shadowbox();
                $subcontent = "";
                foreach($categories as $qNum => $cat) {
                    $qNum++;
                    $catid = "question$qNum";
                    $active = (ChapmanRadioRequest::Get('view') == $catid) ? true : false;

                    $q = json_decode($quiz["q$qNum"],true);
                    $quizquestion = @$quizquestions[$q['quizquestionid']];
                    if(!$q) {
                        $subcontent .= "<div class='subcontent' id='$catid'><h3>$cat</h3><div class='inner'>";
                        $subcontent .= "<p>An error occurred and this particular question could not be loaded. Please move on to the next question.";
                    }
                    else {
                        $subcontent .= "<div class='subcontent' id='$catid'><h3>$quizquestion[question]</h3><div class='inner'>";
                        //$subcontent .= "<pre>".print_r($quizquestion,true)."</pre>";
                        $subcontent .= "<p>Question $qNum: $quizquestion[question]</p>";
                        if($quizquestion['pic']){
                            $subcontent .= "<p style='text-align:center'><a href='$quizquestion[full]' rel='shadowbox'><img src='$quizquestion[pic]' alt='' /></a></p>";
                        }
                        $subcontent .= "<div style='width:400px;margin:10px auto;' class='gloss'>";
                        $responses = explode("[%,%]", $quizquestion['responses']);
                        self::shuffle_with_keys($responses);
                        //$subcontent .= "<pre>".print_r($responses,true)."</pre>";
                        foreach($responses as $responseNum => $response) {
                            $id = "q$qNum-response$responseNum";
                            $subcontent .= "<p><input type='radio' name='q$qNum-response' value='$responseNum' id='$id' /> <label for='$id'>$response</label></p>";
                        }
                        $subcontent .= "</div>";
                        $qNext = $qNum+1;
                        $qPrev = $qNum-1;
                        $subcontent .= "<table style='width:400px;margin:20px auto;'><tr><td style='width:50%;'>".($qPrev?"<a class='ui_button115' style='margin:auto;' href='#question$qPrev' onclick='subnav.load(document.getElementById(\"question$qPrev\"),\"#question$qPrev\");return false;'>Prev</a>":"&nbsp;")."</td><td>".($qNext==$qTotal?"<a href='javascript:$(\"#quizform\").submit()' class='ui_button115' style='margin:auto;'>Finish</a>":"<a class='ui_button115' style='margin:auto;' href='#question$qNext' onclick='subnav.load(document.getElementById(\"question$qNext\"),\"#question$qNext\");return false;'>Next</a>")."</td></tr></table>";
                    }
                    $subcontent .= "</div></div>";
                    Template::AddBodyContent("<li ".($active?"class='active'":"")."><a href='#$catid'>$cat</a></li>");
                }

                Template::AddBodyContent("</td><td style='padding-left:10px;'><div id='subcontentContainer'>$subcontent</div></td></tr></table>");
                Template::AddBodyContent("</div></form>");
                break;

            default:
                Template::AddBodyContent("<p>Hello, you've already taken the quiz for this semester.</p>");
                break;
        }
        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }
    /* http://stackoverflow.com/questions/4102777/php-random-shuffle-array-maintaining-key-value */
    function shuffle_with_keys(&$array) {
        /* Auxiliary array to hold the new order */
        $aux = array();
        /* We work with an array of the keys */
        $keys = array_keys($array);
        /* We shuffle the keys */
        shuffle($keys);
        /* We iterate thru' the new order of the keys */
        foreach($keys as $key) {
            /* We insert the key, value pair in its new order */
            $aux[$key] = $array[$key];
            /* We remove the element from the old array to save memory */
            unset($array[$key]);
        }
        /* The auxiliary array with the new order overwrites the old variable */
        $array = $aux;
    }
}
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
use ChapmanRadio\Schedule;
use ChapmanRadio\Season;
use ChapmanRadio\Session;
use ChapmanRadio\ShowModel;
use ChapmanRadio\Site;
use ChapmanRadio\Template;
use ChapmanRadio\Util;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MyEvalsController extends Controller
{

    /**
     * @Route("/dj/myevals", name="myevals_eval")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        Template::SetPageTitle("Show Evals");
        Template::RequireLogin("/dj/myevals","DJ Account");

        Template::AddStaffAlert("You can see completed evals at <a href='/staff/evals'>/staff/evals</a>");

        Template::css("/legacy/css/dl.css");

// let's give this info to javascript
//Template::script("if(typeof evals == 'undefined') evals = {}; eval.self = '$_SERVER[PHP_SELF]';");
// we need all of the user's show info
        $shows = array();
        $userid = Session::GetCurrentUserID();

        $shows = ShowModel::FromDj($userid, "AND status='accepted'");

//Template::script("evals.shows = ".json_encode($shows).";");
// we need all of the evals categories

        $categories = Evals::categories(false);
//Template::script("evals.categories = ".json_encode($categories).";");
// we need all of the submitted eval records
// output the page
        Template::SetBodyHeading("DJ Resources", "My Evals");
        Template::AddBodyContent("<div style='width:900px;margin:10px auto;text-align:left;'>");

        Template::AddBodyContent("<h3>Completed for Me</h3>");
        Template::AddBodyContent("<table style='margin:10px auto;width:840px;' cellspacing='0'>");
        foreach($shows as $show) {
            Template::AddBodyContent("<tr><td><img src='".$show->img50."' /></td><td>");
            Template::AddBodyContent("<dl><dt>Show</dt><dd>".$show->name."</dd><dt>Genre</dt><dd>".$show->genre."</dd><dt>DJs</dt><dd>".$show->GetDjNamesCsv()."</dd></dl>");
            Template::AddBodyContent("</td>");
            Template::AddBodyContent("<td>");
            $evals = DB::GetAll("SELECT * FROM evals WHERE showid = :id", array(':id' => $show->id));
            if(empty($evals)) {
                Template::AddBodyContent("<p style='color:#848484;'>No Data.</p>");
            }
            else {
                Template::AddBodyContent("<div class='evals'>");
                Template::style(".evals {border:1px solid #CDCDCD;height:360px;overflow:auto;width:420px;margin:0 0 10px;} .evals td {padding:0 4px;} .evals .date {color:#848484;font-size:11px;} .evals tr {border-top:1px solid #CDCDCD;margin-bottom:10px;}");
                $prevTimestamp = $good = $bad = 0;
                foreach($evals as $eval){
                    if($prevTimestamp != $eval['timestamp']) {
                        if($prevTimestamp) Template::AddBodyContent("</table>".totals($good, $bad)."</div>");
                        Template::AddBodyContent("<div class='address'><a>".date("l, F jS, g:ia", $eval['timestamp'])."</a></div>");
                        Template::AddBodyContent("<div><table>");
                        $prevTimestamp = $eval['timestamp'];
                        $good = $bad = 0;
                    }
                    if($eval['goodbad'] == 'bad') $bad++;
                    else $good++;
                    $img = $eval['type'] == 'button' ? $categories[$eval['goodbad']][$eval['value']]['icon'] : $categories[$eval['goodbad']][$eval['goodbad'].'comment']['icon'];
                    Template::AddBodyContent("<tr><td class='date'>".date("g:ia",$eval['postedtimestamp'])."</td><td><img src='$img' /></td><td>".
                        ($eval['type']=='button' ? $categories[$eval['goodbad']][$eval['value']]['label'] : $eval['value'])."</td></tr>");
                }
                Template::AddBodyContent("</table>".totals($good,$bad)."</div></div>");
            }
            Template::AddBodyContent("</td></tr>");
        }

        Template::AddBodyContent("</table>");

        Template::AddBodyContent("<h3>Completed by Me</h3>");
        $userid = Session::getCurrentUserID();
        $evals = DB::GetAll("SELECT * FROM evals WHERE userid='$userid' ORDER BY timestamp");
        if(empty($evals)) {
            Template::AddBodyContent("<p style='color:#848484'>No data.</p><p>You should do a show evaluation!<p><a href='/dj/eval'>&raquo; Start an Eval</a></p>");
        }
        else {
            Template::AddBodyContent("<table style='margin:10px auto;width:840px;' cellspacing='0'>");
            $prevTimestamp = $good = $bad = 0;
            foreach($evals as $eval){
                $showid = $eval['showid'];
                if(!$showid) continue;
                if(!$eval['timestamp']) continue;
                if($prevTimestamp != $eval['timestamp']) {
                    $show = ShowModel::FromId($showid, true);
                    if(!$show) continue;
                    if($prevTimestamp) Template::AddBodyContent("</table>".self::totals($good,$bad)."</div></td>");
                    Template::AddBodyContent("<tr>");
                    Template::AddBodyContent("<td><img src='".$show->img50."' /></td>");
                    Template::AddBodyContent("<td><dl><dt>Show</dt><dd>".$show->name."</dd><dt>Genre</dt><dd>".$show->genre."</dd><dt>DJs</dt><dd>".$show->GetDjNamesCsv()."</dd></dl></td>");
                    Template::AddBodyContent("<td><div class='evals'><div class='address'><a>".date("l, F jS, g:ia",$eval['timestamp'])."</a></div><table>");
                    $prevTimestamp = $eval['timestamp'];
                    $good = $bad = 0;
                }
                if($eval['goodbad'] == 'bad') $bad++;
                else $good++;
                $img = $eval['type'] == 'button' ? $categories[$eval['goodbad']][$eval['value']]['icon'] : $categories[$eval['goodbad']][$eval['goodbad'].'comment']['icon'];
                Template::AddBodyContent("<tr><td class='date'>".date("g:ia",$eval['postedtimestamp'])."</td><td><img src='$img' /></td><td>".
                    ($eval['type']=='button'?$categories[$eval['goodbad']][$eval['value']]['label']:$eval['value'])."</td></tr>");
            }
            Template::AddBodyContent("</table>".self::totals($good,$bad)."</div></td>");
            Template::AddBodyContent("</table>");
        }
// finish up
        Template::AddBodyContent("</div>");
        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());


    }
    function totals($good,$bad) {
        return "<table style='margin:10px auto;'><tr>
		<td><img src='/img/icons/smileys/happy48.png' /></td>
		<td style='vertical-align:middle'>Total: <b>$good</b></td>
		<td style='padding-left:22px;'><img src='/img/icons/smileys/sad48.png' alt='' /></td>
		<td style='vertical-align:middle'>Total: <b>$bad</b></td>
	</tr></table>";
    }


}
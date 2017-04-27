<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 8:08 AM
 */

namespace AppBundle\Controller\staff;

use ChapmanRadio\DB;
use ChapmanRadio\Evals;
use ChapmanRadio\GradeStructureModel;
use ChapmanRadio\Log;
use ChapmanRadio\Request as ChapmanRadioRequest;
use ChapmanRadio\Schedule;
use ChapmanRadio\Season;
use ChapmanRadio\Session;
use ChapmanRadio\ShowModel;
use ChapmanRadio\Site;
use ChapmanRadio\Template;
use ChapmanRadio\UserModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class AlterationController extends Controller
{
    /**
     * @Route("/staff/alterations", name="staff_alterations")
     */
    public function indexAction(Request $request)
    {
        define('PATH', '../');

        Template::SetPageTitle("Alterations");
        Template::RequireLogin("/staff/alterations","Staff Resources", "staff");

        Template::css("/legacy/css/formtable.css");
        Template::js("/legacy/js/postform.js");
        Template::style(".alterations .time { width:220px;}");

        Template::SetBodyHeading("Staff Resources", "Alterations");

        if(isset($_REQUEST['NewAlteration'])) {
            $from = @$_REQUEST['from'] or "";
            $to = @$_REQUEST['to'] or "";
            $showid = intval(@$_REQUEST['showid']);
            $note = ChapmanRadioRequest::Get('note');
            if(!$from || !$to){
                Template::AddBodyContent("<p style='color:#A00;'>Missing from or to date.</p>");
            }
            else {
                $starttimestamp = strtotime($from);
                $endtimestamp = strtotime($to);
                if(!$starttimestamp || !$endtimestamp) {
                    Template::AddBodyContent("<p style='color:#A00;'>Invalid from or to date.</p>");
                }
                else {
                    Log::StaffEvent("Created alteration record for show $showid");
                    Schedule::alter($starttimestamp,$endtimestamp,$showid,$note);
                    Template::AddBodyContent("<p style='color:#090;'>That alteration has been created.</p>");
                }
            }
        }
        else if(isset($_REQUEST['UpdateAlteration'])) {
            $alterationid = intval(@$_REQUEST['alterationid']);
            $from = strtotime(@$_REQUEST['from']);
            $to = strtotime(@$_REQUEST['to']);
            $showid = intval(@$_REQUEST['showid']);
            $userid = Session::GetCurrentUserId();
            $note = ChapmanRadioRequest::Get('note');
            if($alterationid && $from && $to) {
                Log::StaffEvent("Modified alteration record $alterationid");
                DB::Query("UPDATE alterations SET showid='$showid', starttimestamp='$from', endtimestamp='$to',alteredby='$userid',note='$note' WHERE alterationid='$alterationid'");
                Template::AddBodyContent("<p style='color:#090'>That Alteration has been updated.</p>");
            }
            else {
                Template::AddBodyContent("<p style='color:#A00'>Invalid information. Please try again.</p>");
            }
        }

        else if(isset($_REQUEST['DeleteAlteration'])) {
            $alterationid = ChapmanRadioRequest::GetInteger('alterationid');
            if(!$alterationid) {
                Template::AddBodyContent("<p style='color:#A00'>Missing alteration id # (this is probably a programming bug)</p>");
            }
            else {
                Log::StaffEvent("Deleted alteration record $alterationid");
                DB::Query("DELETE FROM alterations WHERE alterationid=:id", array(":id" => $alterationid));
                Template::AddBodyContent("<p style='color:#D60'>That alteration has been <b>deleted</b>.</p>");
            }
        }

        $picker = "<select name='showid'><option value='0'>Automation (No Show)</option>";
        $shows = array();
        $season = Site::CurrentSeason();
        $results = DB::GetAll("SELECT showid,showname FROM shows WHERE seasons LIKE '%$season%' ORDER BY showname");

        foreach($results as $row){
            $shows[$row['showid']] = $row['showname'];
            $picker .= "<option value='$row[showid]'>$row[showname]</option>";
        }

        $picker .= "</select>";

        Template::AddBodyContent("<div style='width:840px;margin:10px auto;text-align:left;' class='alterations'>");

        $path = $request->getRequestUri();
        Template::AddBodyContent("<h3>New Alteration</h3>
<form method='post' action='$path'>
<table class='formtable' style='margin:10px auto;' cellspacing='0'>
	<tr class='oddRow'><td style='text-align:center;' colspan='2'>New Alteration</td></tr>
	<tr class='evenRow'><td>From</td><td><input class='time' type='text' name='from' value='".date("g:00a n/j/y T")."' ></td></tr>
	<tr class='oddRow'><td>To</td><td><input class='time' type='text' name='to' value='".date("g:59a n/j/y T")."' ></td></tr>
	<tr class='evenRow'><td>Show</td><td>$picker</td></tr>
	<tr class='oddRow'><td colspan='2' style='text-align:center;'>Note<br /><textarea name='note' rows='6' cols='42'></textarea></td></tr>
	<tr class='eveRow'><td style='text-align:center' colspan='2'><input type='submit' name='NewAlteration' value=' Create ' /></td></tr>
</table></form>");


        $limitview = ChapmanRadioRequest::GetInteger('limitview', 60);
        if($limitview < 1) $limitview = 1;
        if($limitview > 160) $limitview = 160;

        $fromview = strtotime(ChapmanRadioRequest::Get('fromview'));
        if(!$fromview) $fromview = strtotime("-1 month");

        $toview = strtotime(ChapmanRadioRequest::Get('toview'));
        if(!$toview) $toview = strtotime("+1 month");

        Template::AddBodyContent("<h3>Current Alterations</h3>
<form method='get' action='$path'>
<p style='margin-top:8px;'>Viewing up to <input type='text' name='limitview' value='60' style='width:20px;' maxlength='3' /> alterations from <input type='text' style='width:93px;' name='fromview' value='".date("n/j/y",$fromview)."' /> to <input type='text' name='toview' style='width:93px;' value='".date("n/j/y",$toview)."' /> <input type='submit' value=' &gt; ' />
</form><table class='formtable' style='margin:10px auto;width:750px;' cellspacing='0'>");

        Template::style(".alterationRowController{opacity:.69; cursor:pointer;}.alterationRowController:hover{opacity:1}");
        Template::script("
	$(document).on('click', '.alterationRowController', function(data){
		$(this).css({ display: 'none' });
		$('#' + $(this).attr('data-target')).css({ display: 'table-row' });
		});
	$(document).on('click', '.alterationRowCancel', function(data){
		$('#' + $(this).attr('data-source')).css({ display: 'none' });
		$('#' + $(this).attr('data-target')).css({ display: 'table-row' });
		});");

        $result = DB::GetAll("SELECT * FROM alterations WHERE starttimestamp >= '$fromview' AND endtimestamp <= '$toview' ORDER BY starttimestamp DESC LIMIT 0,$limitview");
        $count = 0;

        foreach($result as $row){
            $rowclass = ++$count % 2 == 0 ? "evenRow" : "oddRow";
            $alterationid = $row['alterationid'];
            $showid = $row['showid'];
            $show = ShowModel::FromId($showid, true);

            $from = date("g:ia n/j/y T", $row['starttimestamp']);
            $to = date("g:ia n/j/y T", $row['endtimestamp']);

            $oldshowid = Schedule::ScheduledAt($row['starttimestamp']);
            $oldshow = (!$oldshowid) ? null : ShowModel::FromId($oldshowid, true);

            $createdby = UserModel::FromId($row['alteredby'], true);
            Template::AddBodyContent("
	<tr class='$rowclass alterationRowController' id='alterationRowController$row[alterationid]' data-target='alterationRow$row[alterationid]' ><td colspan='3'>
		<div style='width:240px;float:left;'>
			<p style='color:#333;font-weight:bold;'>".date("l ga, n/j",$row['starttimestamp'])."</p>
			<p><span style='color:#AAA;font-size:11px;'>".($row['alteredby']==0?"Autocreated":"")."</span>".($createdby ? "<br />
			Altered by: ".$createdby->name : "")."<br />$row[note]</p>
		</div>
		<div style='width:240px;float:left;'>
			<b>From:</b><br />
			<img src='".($oldshow ? $oldshow->img50 : ShowModel::$default_img50)."' /><br />
			".($oldshow ? $oldshow->name : "Automation (No Show)")."
		</div>
		<div style='width:240px;float:left;'>
		<b>To:</b><br />
		<img src='".($show ? $show->img50 : ShowModel::$default_img50)."' /><br />".($show ? $show->name : "Automation (No Show)")."
		</div>
		<br style='clear:both;' />
	</td></tr>
	<form method='post' action='$path'>
		<tr class='$rowclass alterationRow' style='display:none;' id='alterationRow$row[alterationid]'>
		<td><input type='text' name='from' class='time' id='from$alterationid' value='$from' />".($row['alteredby']==0?"Autocreated":"")."</span>".($createdby ? "<br />
			Altered by: ".$createdby->name : "")."</td>
		<td><input type='text' class='time' name='to' value='$to' id='to$alterationid' /><br /><textarea name='note' style='height:39px;width:220px;'>$row[note]</textarea></td>
		<td>".self::showPicker($alterationid, $shows, $showid)."
			<input type='submit' name='UpdateAlteration' value=' &gt; Update &gt; ' style='width:auto;' />
			<input type='hidden' name='alterationid' value='$row[alterationid]' />
			<button type='button' onclick='if(confirm(\"Are you sure you want to delete this alteration? This process is irreversible.\"))postform({DeleteAlteration:true,alterationid:$row[alterationid]})'> x Delete x</button>
			<button type='button' class='alterationRowCancel' data-source='alterationRow$row[alterationid]' data-target='alterationRowController$row[alterationid]'> &lt; Cancel &lt; </button></td></tr>
		</form>");
        }

        Template::AddBodyContent("</table></div>");

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());


    }

    function showPicker($alterationid, $shows, $defaultshowid) {
        $picker = "<select name='showid' id='showid$alterationid'><option value='0'>Automation (No Show)</option>";
        foreach($shows as $showid => $showname) {
            $picker .= "<option value='$showid' ".($showid==$defaultshowid?"selected='selected'":"").">$showname</option>";
        }
        $picker .= "</select>";
        return $picker;
    }

}
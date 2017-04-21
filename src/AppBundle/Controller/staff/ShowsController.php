<?php
namespace AppBundle\Controller\staff;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 3:05 PM
 */

use ChapmanRadio\DB;
use ChapmanRadio\Request;
use ChapmanRadio\ShowModel;
use ChapmanRadio\Site;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ShowsController extends Controller
{
    /**
     * @Route("/staff/shows", name="staff_shows")
     */
    public function indexAction(ContainerInterface $container = null)
    {

        Template::SetPageTitle("Show Management");
        Template::SetBodyHeading("Site Administration", "Show Management");
        Template::RequireLogin("Staff Resources", "staff");
        Template::Bootstrap();
        Template::shadowbox();
        Template::js("/staff/js/dialog_edit.js");

        $input = Request::Get('input','');

// create show pickers
        $opts = array();
        $season = Site::CurrentSeason();
        $result = DB::GetAll("SELECT showid,showname FROM shows ORDER BY showname"); // WHERE seasons LIKE '%$season'
        foreach($result as $row) $opts[] = "<option value='$row[showid]'>$row[showname] (#{$row['showid']})</option>";

// output the default page
        Template::Add("
	<div class='cr-login-left'>
	<form class='form-horizontal' method='get' action=''>
		<h2>Search for Shows</h2>
		<div class='form-group'>
			<div class='col-sm-offset-1 col-sm-11'>
			<input type='text' placeholder='Show ID #, Show Name, or User ID #' class='form-control' name='input' />
			</div>
		</div>
		<div class='form-group'>
			<div class='col-sm-offset-1 col-sm-11'>
			<button type='submit' class='btn btn-default'>Search</button>
			</div>
		</div>
	</form>
	</div><div class='cr-login-right'>
	<form class='form-horizontal' method='get' action=''>
		<h2>Merge Shows</h2>
		<div class='form-group'>
			<label for='showidfrom' class='col-sm-2 control-label'>Delete</label>
			<div class='col-sm-10'>
			<select class='form-control' name='showidfrom'><option value=''> - Pick a Show - </option>".implode($opts)."</select>
			</div>
		</div>
		<div class='form-group'>
			<label for='showidto' class='col-sm-2 control-label'>Keep</label>
			<div class='col-sm-10'>
			<select class='form-control' name='showidto'><option value=''> - Pick a Show - </option>".implode($opts)."</select>
			</div>
		</div>
		<div class='form-group'>
			<div class='col-sm-offset-2 col-sm-10'>
			<button type='submit' name='cr_merge_shows' class='btn btn-default'>Merge Shows</button>
			</div>
		</div>
	</form>	
	</div>
	<br class='_clear' />");

// process any input
        if($input) {
            Template::css("/css/formtable.css");
            $fields = array("showid","showname");
            $shows = ShowModel::Search($input, Request::Get('season', NULL));
            if(count($shows) == 0) Template::AddBodyContent("<p>No results found.</p>");
            else foreach($shows as $show){
                Template::AddBodyContent("
			<div style='display: block; border: 1px solid #CCC; vertical-align: top; padding: 5px; margin: 5px auto; width: 695px; overflow: auto;'>
			<table class='eros dialog-link -flutter' data-dialog='/staff/dialog/show_edit?showid={$show->id}' style='width: 350px; float: left; text-align: left;'>
			<thead><tr><td colspan=2>Show #{$show->id}: {$show->name}</td></tr></thead>
			<tr class='oddRow'><td>slot</td><td>".$show->time."</td></tr>
			<tr class='oddRow'><td>showid</td><td>".$show->id."</td></tr>
			<tr class='oddRow'><td>status</td><td>".$show->status."</td></tr>
			</table>");

                Template::AddBodyContent("<table class='eros' style='width: 330px; float: right; text-align: left;'><tbody>");

                foreach($show->GetDjModels() as $showuser){
                    Template::AddBodyContent("<tr class='dialog-link -flutter' data-dialog='/staff/dialog/user_edit?userid={$showuser->id}'>
				<td>{$showuser->name} (#{$showuser->id})</td></tr>");
                }

                Template::AddBodyContent("</tbody></table></div>");
            }
        }

        if(isset($_REQUEST["cr_merge_shows"])){
            $active = true;
            $showidfrom = Request::GetInteger('showidfrom');
            $showfrom = ShowModel::FromId($showidfrom);
            $showidto = Request::GetInteger('showidto');
            $showto = ShowModel::FromId($showidto);
            $sched = DB::GetFirst("SELECT * FROM schedule WHERE mon LIKE :s OR tue LIKE :s OR wed LIKE :s OR thu LIKE :s OR fri LIKE :s OR sat LIKE :s OR sun LIKE :s", [ ":s" => "%$showidfrom" ]);
            if(!$showidfrom || !$showidto)
                Template::AddInlineError("Please pick both a user to merge from and a user to merge to.");
            else if(!$showfrom || !$showto)
                Template::AddInlineError("Invalid userids. Please try again.");
            else if($showidfrom == $showidto)
                Template::AddInlineError("You can't merge one user into itself.");
            else if($sched !== NULL)
                Template::AddInlineError("Show $showidfrom is listed on a schedule, it cannot be deleted");
            else {
                $changes = array(
                    array("alterations","showid"),
                    array("attendance","showid"),
                    array("awards","showid"),
                    array("evals","showid"),
                    array("mp3s","showid"),
                    array("show_sitins","showid"),
                    array("stats","showid"),
                    array("tags","showid")
                );
                //Log::StaffEvent("Merged show #$showidfrom ({$showfrom->name}) into Show #$showidto ({$showto->name})");
                Template::AddInlineSuccess("You are merging Show ID #$showidfrom ({$showfrom->name}) into Show ID #$showidto ({$showto->name}).");
                Template::Add("<table class='table formtable'><tr><th>Table</th><th>Field</th><th>Changes</th></tr>");
                foreach($changes as $change) {
                    list($table,$field) = $change;
                    $affected = DB::Query("UPDATE `$table` SET `$field`='$showidto' WHERE `$field`='$showidfrom'")->rowCount();
                    Template::Add("<tr><td>$table</td><td>$field</td><td>".($affected?"<b>$affected row(s)</b> affected":"0 rows affected")."</td>");
                }
                Template::Add("</table>");
                DB::Insert("show_aliases", [ "from_show_id" => $showidfrom, "to_show_id" => $showidto ]);
                DB::Query("DELETE FROM shows WHERE showid='$showidfrom'");
                Template::AddInlineNotice("Show ID #$showidfrom ($showfrom->name) has been permanently deleted.");
            }
        }

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }
}
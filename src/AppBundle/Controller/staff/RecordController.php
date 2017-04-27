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
use ChapmanRadio\UserModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class RecordController extends Controller
{
    /**
     * @Route("/staff/record", name="staff_record")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        Template::SetPageTemplate("report");
        Template::SetPageTitle("Attendance Recording Utility");
        Template::RequireLogin("/staff/record","Staff Resources", "staff");


        $type = Request::Get('type');
        $date = Request::Get('date');
        $requiredfor = Request::Get('requiredfor');

        if (!$type || !$date) Template::Finalize('Bad URL, try again');
        if ($type == 'workshop' && !$requiredfor) Template::Finalize('Bad URL, try again');

// Prepare a list of IDs that are required - all djs on all shows, respecting the classclub option
        $required = array();
        $season = Site::CurrentSeason();
        $classonly = $requiredfor == 'class' ? true : false; // class only or everyone?
        $shows = ShowModel::FromResults(DB::GetAll("SELECT * FROM shows WHERE seasons LIKE '%$season%' AND status ='accepted'"));

        $debug = Request::GetBool('debug');

        foreach ($shows as $show) {
            foreach ($show->GetDjModels() as $user) {
                if (!$user->workshoprequired) {
                    if ($debug) Template::Add("<div>{$user->name} is not required (user workshoprequired = false)</div>");
                    continue;
                }

                // if class, and user is in club, ignore
                if ($requiredfor == 'class' && $user->classclub == 'club') {
                    if ($debug) Template::Add("<div>{$user->name} is not required for class (user is club)</div>");
                    continue;
                }

                // if classnew and user is in club + returner, ignore
                if ($requiredfor == 'classnew' && $user->classclub == 'club' && $user->seasoncount > 1) {
                    if ($debug) Template::Add("<div>{$user->name} is not required for classnew (user is club + seasons > 1)</div>");
                    continue;
                }

                $required[$user->id] = 1;
                if ($debug) Template::Add("<div>{$user->name} is required</div>");
            }
        }
        Template::script("if(typeof a == 'undefined') a = {}; a.required = " . json_encode($required) . ";");

// Prepare a list of DJs that could be marked in attendance - anyone in this season or in required
        $djs = array();
        foreach ($required as $key => $val) $required[$key] = ' OR userid = ' . $key;
        $users = UserModel::FromResults(DB::GetAll("SELECT * FROM users WHERE seasons LIKE '%$season%' " . implode('', $required)));
        foreach ($users as $user) $djs[$user->id] = $user;
        Template::script("a.djs = " . json_encode($djs) . ";");

        $desc = $date;
        if ($type == "event") {
            $data = DB::GetFirst("SELECT * FROM attendance_events WHERE timestamp = :date", array(":date" => $date));
            if ($data) $desc .= " (" . $data['eventname'] . ")";
            else $desc .= " (Unknown Event)";
        }


// Generate editor
        Template::Add("<div id='controls' style='width:840px;margin:10px auto;'><input type='hidden' id='record-type' value='$type' /><input type='hidden' id='record-date' value='$date' /><div class='couju-info'>You're recording <b>$type</b> attendance for <b>$requiredfor</b> (" . count($required) . " DJs) for <b>$desc</b></div>");

// Which editor to load? Quick or Full
        if (isset($_REQUEST['quick'])) {
            Template::js("/staff/js/record_quick.js?c=20131025");
            Template::Add("<div id='record_background' style='position: fixed; bottom: 10px; top: 100px; left: 10px; right: 10px; border: 1px solid #CCC;'></div><div id='record_container' style='text-align: center; position: fixed; bottom: 10px; top: 100px; left: 10px; right: 10px; border: 1px solid #CCC;'><form><input type='hidden' id='record-type' value='" . $type . "' /><input type='hidden' id='record-date' value='" . $date . "' /><div style='margin-top: 20px;'><div><input type='text' id='search' style='padding: 10px; width: 330px; ' /></div><div id='found_dj' style='height: 350px; width: 310px; padding: 10px; border: 1px solid #666; display: inline-block; margin: 10px;'></div><div id='record_result' style='font-size: 92px; color: white; font-family: sans-serif; font-weight: bold; text-shadow: 1px 1px black;'></div></form></div></div>");
        } else {
            Template::js("/legacy/staff/js/record.js?c=20131025");
            Template::js("/legacy/js/jquery.watermark.min.js");
            Template::css("/legacy/staff/css/recordAttendance.css");
            Template::css("/legacy/css/dl.css");


            Template::AddBodyContent("<div class='address'><div style='padding:3px 20px 0 0;float:right;'><button onclick='a.remainder();'>Set Remainder Absent</button></div><form action='javascript:a.select();'><div style='padding:3px 0 0 20px;text-align:left;'><input id='search' autocomplete='off' value='' /><select name='filter' id='filter' /><option value='nofilter' selected='selected'>No Filter</option><option value='default'>Not Marked</option><option value='present'>Present</option><option value='excused'>Excused</option><option value='absent'>Absent</option></select></div></form></div><div id='results' style=min-height:240px;text-align:left;clear:both;background:url(/img/bg/panelbg.gif) #FEFEFE;margin-bottom:40px;'></div></div>");

            Template::AddBodyContent("<div style='text-align:center'><div id='whitescreen'></div><div id='dialog' style='position:fixed;bottom:-400px;width:570px;margin:auto;background:url(/img/bg/panelbg.gif) #FEFEFE;border:1px solid #575757;'><div style='background:url(/img/bg/feedh4bg.png);line-height:30px;font-size:16px;height:32px;color:#FFF;text-align:center;'>Record Attendance</div><div id='dialogInner' style='text-align:left;'></div></div>");

        }
// close editor

        Template::AddBodyContent("</div>");

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }
}
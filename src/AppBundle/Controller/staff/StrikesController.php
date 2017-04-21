<?php
namespace AppBundle\Controller\staff;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 3:05 PM
 */

use ChapmanRadio\DB;
use ChapmanRadio\Season;
use ChapmanRadio\Site;
use ChapmanRadio\Strikes;
use ChapmanRadio\Template;
use ChapmanRadio\UserModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class StrikesController extends Controller
{
    /**
     * @Route("/staff/strikes", name="staff_strikes")
     */
    public function indexAction(ContainerInterface $container = null)
    {

        $season = Season::current();
        $seasonName = Season::name($season);

        Template::SetPageTitle("Strikes");
        Template::SetBodyHeading("Staff Resources", "Strikes: $seasonName");
        Template::RequireLogin("Staff Resources", "staff");

        Template::AddBodyContent("<div class='gloss'><table style='font-size:12px;margin:auto;text-align:left;' cellspacing='2'>
	<tr><td><b>".Site::$ShowAbsencesPerStrike."</b> Show Absence</td><td>&nbsp;&nbsp;=&nbsp;&nbsp;</td><td>1 strike</td></tr>
	<tr><td><b>".Site::$TardiesPerStrike."</b> Tardies</td><td>&nbsp;&nbsp;=&nbsp;&nbsp;</td><td>1 strike</td></tr>
	<tr><td><b>".Site::$WorkshopAbsencesPerStrike."</b> Workshop Absences</td><td>&nbsp;&nbsp;=&nbsp;&nbsp;</td><td>1 strike</td></tr>
</table><br /><p><a href='/policies' target='_blank'>chapmanradio.com/policies</a></p></div><p style='margin:40px auto;'>See also: <a href='/staff/cancelledshows'>Cancelled Shows</a></p>");
        $startCalcTime = microtime(true);
        Template::tablesorter();
        Template::style(".strike {font-weight:bold;color:#333;} .noStrike {color:#AAA;} ");
        Template::AddBodyContent("<div style='margin:10px 40px;'><table class='tablesorter' style='margin:10px auto;width:100%;'>
	<thead><tr><th>&nbsp;</th><th>User</th><th>Show Absences</th><th>Show Tardies</th><th>Workshop Absences</th><th>Total Strikes</th></tr></thead>
	<tbody>");
// we need to know which users to check
        $userids = array();
        $result = DB::GetAll("SELECT userid1,userid2,userid3,userid4,userid5 FROM shows WHERE seasons LIKE '%$season%'");
        foreach($result as $row){
            for($i = 1;$i <= 5;$i++){
                if($row["userid$i"]) $userids[$row["userid$i"]] = $row["userid$i"];
            }
        }

// preload all users
        UserModel::FromIds($userids);


        foreach($userids as $userid) {
            $data = Strikes::check($userid);
            $user = UserModel::FromId($userid, true);
            if(!$user) continue;
            $djname = $user->name != $user->djname ? "<i style='color:#757575'>(".$user->djname.")</i>" : "";
            Template::AddBodyContent("<tr>
		<td><img src='".$user->img50."' alt='' /></td>
		<td>{$user->name} {$djname}<br />{$user->email}<br />".ucfirst($user->classclub)."</td>
		<td>".self::formatStrikes($data['showAbsences'], $data['showStrikes'])."</td>
		<td>".self::formatStrikes($data['workshopAbsences'], $data['workshopStrikes'])."</td>
		<td>".self::formatTotalStrikes($data['totalStrikes'])."</td>
		<td><a href='/staff/reports/user?userid=$userid' onclick='window.open(this.href,\"attendanceManager\",\"width=840,height=600,status=1,toolbars=1,scrollbars=1\");return false;'>&raquo; Manage</a>
		<td>".self::formatStrikes($data['tardies'], $data['tardyStrikes'])."</td>
		</tr>");
        }

        Template::AddBodyContent("</tbody></table>");
        $endCalcTime = microtime(true);
        $calcTime = $endCalcTime - $startCalcTime;
        $calcTime = round($calcTime*1000)/1000;
        Template::AddBodyContent("<p style='text-align:right;font-size:11px;color:#757575;'>Calculated in $calcTime seconds.</p>");
        Template::AddBodyContent("</div>");
        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }

    function formatStrikes($absences, $strikes){
        $absences = (($absences < 10)?"0":"").$absences;
        $s = $strikes == "1" ? "" : "s";
        $strikeClass = $strikes ? "strike" : "noStrike";
        return "$absences<br /><i class='$strikeClass'>$strikes strike$s</i>";
    }

    function formatTotalStrikes($strikes){
        $s = $strikes == 1 ? "" : "s";
        $strikes = (($strikes < 10)?"0":"").$strikes;

        $color = "#A00";
        if($strikes <= 0) $color = "#848484";
        if($strikes == 1) $color = "#222";
        if($strikes == 2) $color = "#D60";

        return "<strong style='color:$color;'>$strikes total strike$s</strong>";
    }

    function dispLate($late, $tardies=0) {
        $s = $late == 1 ? "" : "s";
        if($late < 0) return array("<span style='color:#090'>".(0-$late)." minute$s early</span>",$tardies);
        else if($late == 0) return array("<span style='color:#848484'>on time</span>",$tardies);
        else if($late < 8) return array("<span style='color:#A60;'>$late minute$s late</span>",$tardies);
        else {
            return array("<span style='color:#A00;'>$late minute$s late</span>",++$tardies);
        }
    }

    function picker($id, $default = "", $onchange='') {
        $onchange = $onchange ? "onchange='$onchange'" : "";
        $ret = "<select name='status' id='picker$id' $onchange><option value=''> - Pick a Status - </option>";
        foreach(array("present","absent","excused") as $option) {
            $ret .= "<option value='$option' ".($option==$default?"selected='selected'":"").">$option</option>";
        }
        $ret .= "</select>";
        return $ret;
    }
}
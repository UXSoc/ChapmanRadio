<?php
namespace AppBundle\Controller\Staff;


use ChapmanRadio\Award;
use ChapmanRadio\DB;
use function ChapmanRadio\error;
use ChapmanRadio\ShowModel;
use ChapmanRadio\Site;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AwardsController extends  Controller
{
    /**
     *@Route("/staff/awards", name="staff_awards")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        Template::SetPageTitle("Awards");
        Template::SetBodyHeading("Site Administration", "Awards");
        //Template::RequireLogin("/staff/awards","Staff Resources", "staff");

        Template::js("/legacy/staff/js/awards.js");
        Template::css("/legacy/css/formtable.css");
        Template::css("/legacy/plugins/tablesorter/blue/style.css");
        Template::js("/legacy/plugins/tablesorter/jquery.tablesorter.min.js");

        Template::AddBodyContent("<div class='leftcontent'>");

// assign an award
        Template::AddBodyContent("<h3>Assign an Award</h3>");
        if (isset($_POST['ASSIGN_AWARD'])) {
            $type = @$_REQUEST['type'] or error($this->container,"Missing <b>type</b> variable");
            $showid = @$_REQUEST['showid'] or error($this->container,"Missing <b>type</b> variable");
            $awardedon = @$_REQUEST['awardedon'] or error($this->container,"Missing <b>awardedon</b> variable");
            if (!preg_match("/^\\d{4}-\\d{2}-\\d{2}\$/", $awardedon)) error("Awarded on <b>$awardedon</b> was invalid. please enter a YYYY-MM-DD formatted date");
            DB::Insert('awards', array("type" => $type, "showid" => $showid, "awardedon" => $awardedon));
            Template::AddBodyContent("<p style='color:green'>The award <b>$type</b> has just been assigned to <a href='/show?show=$showid' target='_blank'><b>Show #$showid</b></a> ");
            self::cleanAwards();
        }

        $awardtypes = Award::$awards;

# award picker
        $awardpicker = "<select name='type'><option value=''> - Pick an Award Type - </option><optgroup label='Show of the Week'><option value='showoftheweek'>showoftheweek</option></optgroup><optgroup label='Semester Awards'>";

        foreach ($awardtypes as $type => $arr) if ($type != 'showoftheweek') $awardpicker .= "<option value='$type'>$type</option>";

        $awardpicker .= "</optgroup></select>";
# show picker
        $showpicker = "<select name='showid'><option value=''> - Pick a Show - </option>";
        $season = Site::CurrentSeason();
        $results = DB::GetAll("SELECT showid,showname FROM shows WHERE seasons LIKE '%$season%' ORDER BY showname");

        foreach ($results as $row) $showpicker .= "<option value='$row[showid]'>$row[showname]</option>";

        Template::AddBodyContent("<form method='post'><table class='formtable' cellspacing='0' cellpadding='0' style='margin:10px;text-align:center;'>
	<tr class='oddRow'><td>Award Type</td><td>$awardpicker</td></tr>
	<tr class='evenRow'><td>Show</td><td>$showpicker</select></td></tr>
	<tr class='oddRow'><td>Awarded On</td><td><input type='text' name='awardedon' value='" . date("Y-m-d") . "' /></td></tr>
	<tr class='evenRow'><td colspan='2'><input type='submit' name='ASSIGN_AWARD' value=' Assign Award ' /></td></tr>
</table></form>");


        Template::AddBodyContent("<h3>Current Awards</h3>");
        $fields = array("awardid", "type", "showid", "showname", "awardedon");

// save changes?
        if (isset($_POST['SAVE_AWARD'])) {
            $awardid = @$_REQUEST['awardid'] or error("missing awardid variable");
            $affected = 0;
            foreach ($fields as $field) {
                if (isset($_REQUEST["award-$field"])) {
                    DB::Query("UPDATE awards SET `$field` = '{$_REQUEST['award-'.$field]}' WHERE awardid='$awardid'");
                    $affected += DB::AffectedRows();
                }
            }
            Template::AddBodyContent("<p style='color:green'>Changes saved. <b>$affected</b> value(s) changed.</p>");
            self::cleanAwards(true);
        }

// delete an award?
        if (isset($_POST['DELETE_AWARD'])) {
            $awardid = @$_REQUEST['awardid'] or error("missing awardid variable");
            DB::Query("DELETE FROM awards WHERE awardid='$awardid'");
            Template::AddBodyContent("<p style='color:red;'>That award has been <b>deleted</b>.</p>");
        }

// view current awards
        Template::AddBodyContent("<div style='position:relative;height:480px;overflow:hidden;'>");
        Template::AddBodyContent("<table class='tablesorter formtable' id='awardlist' style='position:absolute;left:0;' cellspacing='0' cellpadding='0'><thead>");
        foreach ($fields as $field) Template::AddBodyContent("<th>$field</th>");
        Template::AddBodyContent("</thead><tbody style='height:400px;overflow:auto;'>");

        $result = DB::GetAll("SELECT * FROM awards,shows WHERE awards.showid = shows.showid ORDER BY awards.awardedon DESC");

        foreach ($result as $row) {
            $show = ShowModel::FromResult($row);
            $row['showhtml'] = "<div class='gloss'><img src='" . Award::icon($row['type']) . "' /> " . Award::name($row['type']) . "</div><img src='" . $show->img50 . "' style='float:left' /><h3>" . $show->name . "</h3><p>" . $show->GetDjNamesCsv() . "</p>";
            Template::script("award.data[$row[awardid]]=" . json_encode($row) . ";");
            Template::AddBodyContent("<tr onclick='award.edit($row[awardid])'>");
            foreach ($fields as $field) Template::AddBodyContent("<td>{$row[$field]}</td>");
            Template::AddBodyContent("</tr>");
        }

        Template::AddBodyContent("</tbody></table><div id='editor' style='position:absolute;left:1000px;'></div> </div>");

// all done
        Template::AddBodyContent("</div>");
        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize($this->container));

    }

    function cleanAwards($cleanall = false)
    {
        $result = DB::GetAll("SELECT * FROM awards" . ($cleanall ? "" : " WHERE season=''"));
        foreach ($result as $row) DB::Query("UPDATE awards SET season='" . Season::fromTimestamp(strtotime($row['awardedon'])) . "' WHERE awardid=$row[awardid]");
    }

}
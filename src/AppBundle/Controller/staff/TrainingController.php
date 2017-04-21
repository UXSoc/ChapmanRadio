<?php
namespace AppBundle\Controller\staff;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 3:05 PM
 */

use ChapmanRadio\DB;
use ChapmanRadio\Log;
use ChapmanRadio\Request;
use ChapmanRadio\Site;
use ChapmanRadio\Template;
use ChapmanRadio\TrainingSignupModel;
use ChapmanRadio\UserModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class TrainingController extends Controller
{

    /**
     * @Route("/staff/training", name="staff_training")
     */
    public function indexAction(ContainerInterface $container = null)
    {

        Template::SetPageTitle("Staff");
        Template::SetBodyHeading("Site Administration", "Staff Training Administration");
        Template::RequireLogin("Staff Resources", "staff");

        if(isset($_POST['cr-training-present'])){
            $suid = Request::Get('suid');
            DB::Query("UPDATE training_signups SET trainingsignup_present = '1' WHERE trainingsignup_id = :id", [ ":id" => $suid ]);
            Template::AddCoujuSuccess("Marked DJ as present");
            Log::StaffEvent("Marked training signup #$suid as present");
        }

        $season = Site::CurrentSeason();
        $data = DB::GetAll("SELECT v_training_signups.*, u.*, s.fname as s_fname, s.lname as s_lname FROM v_training_signups INNER JOIN users as u on trainingsignup_userid = u.userid INNER JOIN users as s on trainingslot_staffid = s.userid WHERE trainingslot_season = :s ORDER BY trainingslot_datetime", [ ":s" => $season ]);

        Template::AddBodyContent("<br /><table class='eros -full'><thead><tr><td>Timestamp</td><td>DJ</td><td>Staff</td><td>Actions</td></tr></thead>");
        foreach($data as $row){
            $signup = TrainingSignupModel::FromResult($row);
            $dj = UserModel::FromResult($row);
            $button = ($signup->present == "1") ? "" : "<form method='post'><input type='hidden' name='suid' value='{$signup->id}' /><input type='submit' name='cr-training-present' value='Mark Present' /></form>";
            Template::AddBodyContent("<tr><td>{$signup->datetime}</td><td>{$dj->name}</td><td>{$row['s_fname']} {$row['s_lname']}</td><td>{$button}</td></tr>");
        }
        Template::AddBodyContent("</table>");
        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
    }
}
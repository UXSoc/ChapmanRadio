<?php
namespace AppBundle\Controller\staff;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 3:05 PM
 */

use ChapmanRadio\DB;
use ChapmanRadio\GradeStructureModel;
use ChapmanRadio\Site;
use ChapmanRadio\Template;
use ChapmanRadio\UserModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class GradesController extends Controller
{
    /**
     * @Route("/staff/grades", name="staff_grades")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        Template::SetPageTitle("All Grades");
        Template::RequireLogin("/staff/grades","DJ Account");
        Template::Bootstrap();

        Template::js("/plugins/tablesorter/jquery.tablesorter.min.js");
        Template::css("/plugins/tablesorter/plain/style.css");
        Template::script("\$(document).ready(function(){\$('.tablesorter').tablesorter();});");

        Template::Css("/dj/css/page-grades.css");
        Template::Css("/staff/css/page-grades.css");
        Template::js("/staff/js/grades.js?1.4");

        Template::SetBodyHeading("All Grades for ".Season::Name());

// Get all users
        $season = Site::CurrentSeason();
        $users = UserModel::FromResults(DB::GetAll("SELECT *,(SELECT count(*) FROM shows WHERE seasons LIKE :s AND (userid1=userid OR userid2=userid OR userid3=userid OR userid4=userid OR userid5=userid)) as show_count FROM users WHERE seasons LIKE :s AND classclub='class' ORDER BY lname ASC", [ ":s" => "%{$season}%" ]));

// Lookup map
        $user_lookup = [];
        foreach($users as $user) $user_lookup[$user->id] = $user;

// Get grade structure
        $grades = GradeStructureModel::ForCurrentSeason();

// Use better lookup function
        foreach($grades as $grade) $grade->Extern('ChapmanRadio\GradeLookup');

// Get all grade values
        $known = [];
        $values = DB::GetAll("SELECT grade_values.* FROM grade_values INNER JOIN grade_structure ON grade_values.grade_id = grade_structure.grade_id AND grade_structure.grade_season = :s", [ ":s" => $season ]);
        foreach($values as $value){
            if(!isset($known[$value['user_id']])) $known[$value['user_id']] = [];
            $known[$value['user_id']][$value['grade_id']] = $value['grade_value'];
        }

// Get all eval values
        $evals_raw = DB::GetAll("SELECT userid,COUNT(DISTINCT(timestamp)) as c FROM evals WHERE season = :s GROUP BY userid", [ ":s" => $season ]);
        $evals_map = [];
        foreach($evals_raw as $eval) $evals_map[$eval['userid']] = $eval['c'];

// Get all strikes values
        $strikes_raw = DB::GetAll("SELECT userid,COUNT(*) as c FROM strikes WHERE season = :s GROUP BY userid", [ ":s" => $season ]);
        $strikes_map = [];
        foreach($strikes_raw as $strike) $strikes_map[$strike['userid']] = $strike['c'];

// User table
        Template::Add("<table id='cr-staff-grades-editor' class='table tablesorter'><thead><tr style='background: white;'><th>DJ</th>");
        foreach($grades as $item){
            Template::Add("<th>{$item->name}</th>");
            foreach($item->children as $child) Template::Add("<th>{$child->name}</th>");
        }
        Template::Add("</tr></thead><tbody>");
        foreach($users as $dj) RenderGradeRow($dj, $grades, isset($known[$dj->id])? $known[$dj->id] : []);
        Template::Add("</tbody></table>");

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());

    }
    function RenderGradeRow($dj, $grades, $known){
        if($dj->rawdata['show_count'] == 0){
            Template::Add("<tr><td><span style='color:#CCC'>{$dj->name}</span> <a href='/staff/reports/user?userid={$dj->id}' target='_blank'>&raquo;</a></td>");
        }
        else {
            Template::Add("<tr><td>{$dj->name} <a href='/staff/reports/user?userid={$dj->id}' target='_blank'>&raquo;</a></td>");
        }
        foreach($grades as $item){
            RenderGradeCol($dj, $item, $known);
            foreach($item->children as $child) RenderGradeCol($dj, $child, $known);
        }
        Template::Add("</tr>");
    }

    function RenderGradeCol($dj, $item, $known){
        $item->Load($dj->id, $known);
        $cat = $item->Cat();
        $class = $item->type == 'manual' ? "-editable" : "";
        Template::Add("<td class='cr-grade cr-grade-edit {$class} -{$cat}' data-cr-grade-id='{$item->id}' data-cr-user-id='{$dj->id}'>{$item->Score()}</td>");
    }

    function GradeLookup($grade){
        global $evals_map,$strikes_map;
        switch($grade->type){
            case 'manual':
                return $grade->value ?: 0;
            case 'evals':
                return (isset($evals_map[$grade->user_id])) ? $evals_map[$grade->user_id] : 0;
            case 'category':
                $score = 0;
                foreach($grade->children as $child) $score += $child->Score();
                return $score;
            case 'strikes':
                return (isset($strikes_map[$grade->user_id])) ? $strikes_map[$grade->user_id] : 0;
            default:
                return NULL;
        }
    }
}
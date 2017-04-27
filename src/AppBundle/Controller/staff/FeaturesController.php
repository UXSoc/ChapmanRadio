<?php
namespace AppBundle\Controller\staff;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 3:05 PM
 */

use ChapmanRadio\DB;
use ChapmanRadio\FeatureModel;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class FeaturesController extends  Controller
{
    /**
     * @Route("/staff/features", name="staff_features")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        Template::SetPageTitle("Site Administration");
        Template::SetBodyHeading("Site Administration", "Features");
        Template::RequireLogin("/staff/features","Staff Resources", "staff");

        Template::shadowbox();
        Template::js("/staff/js/dialog_edit.js");

        if(isset($_POST['new_feature'])){
            DB::Query("INSERT INTO features (feature_size) VALUES ('310')");
            $id = DB::LastInsertId();
            Template::AddBodyContent("<script>$(document).ready(function(){ setTimeout(function(){ $('#staff-feature-edit-link-{$id}').trigger('click'); }, 1000); });</script>");
        }

        Template::AddBodyContent("<form method='POST'><input style='position: absolute; right: 0; top: 10px;' name='new_feature' type='submit' value='New Feature' /></form>");

        Template::AddCoujuDebug("<div style='text-align: left;'>Features appear on the website homepage and the iPhone app. For a feature to display, it must be <b style='color:#060'>active</b>. For a feature to be active:<br />
- The 'posted' date must be in the past. This lets you schedule future features. Inactive features because of this are <u style='color:#939'>pending</u><br />
- The 'expires' date must be in the future, or 0000-00-00. This lets you schedule limited-time features. Inactive features because of this are <u style='color:#A33'>expired</u><br />
- The 'active' field must be On. This lets you temporarily disable features, regardless of their schedule. Inactive features because of this are <u style='color:#33A'>disabled</u><br /><br />
The approximate order of features is based on the <b>priority</b> property. Higher numbers will appear at the top of the homepage, lower numbers will appear towards the bottom. If there isn't a full row, then some features won't display.<br /><br />
Some features are interactive, like the show of the week, or recent tracks. These have a special <b>type</b> and update automatically. You can rearrange these using the priority property, but you cannot change what they look like.</div>");

        $features = FeatureModel::FromResults(DB::GetAll("SELECT * FROM features ORDER BY feature_priority DESC"));

        $active = array();
        $inactive = array();

        foreach($features as $feature){
            if($feature->status == 'active') $active[] = self::RenderRow($feature);
            else $inactive[] = self::RenderRow($feature);
        }

        Template::AddBodyContent("<br /><h2>Active Features</h2>");
        self::RenderTable($active);

        Template::AddBodyContent("<br /><h2>Inactive Features</h2>");
        self::RenderTable($inactive);

        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());

    }
    function RenderTable($rows){
        Template::AddBodyContent("<table style='width: 100%' class='eros'>
		<thead><tr>
		<td style='width: 5%'>Priority</td>
		<td style='width: 10%'>Status</td>
		<td style='width: 10%'>Type</td>
		<td>Title</td>
		<td style='width: 5%'>Size</td>
		<td style='width: 15%'>Posted</td>
		<td style='width: 15%'>Expires</td>
		</tr></thead>
		<tbody>".implode($rows, "")."</tbody></table>");
    }

    function RenderRow($feature){
        return "<tr class='dialog-link -flutter' id='staff-feature-edit-link-{$feature->id}' data-dialog='/staff/dialog/feature_edit?feature_id={$feature->id}'>
		<td>{$feature->priority}</td>
		<td>".self::RenderStatus($feature->status)."</td>
		<td>{$feature->type}</td>
		<td>{$feature->title}</td>
		<td>{$feature->size}</td>
		<td>".self::RenderDatetime($feature->posted)."</td>
		<td>".self::RenderDatetime($feature->expires)."</td>
		</tr>";
    }

    function RenderDatetime($time){
        if($time == '0000-00-00 00:00:00') return "N/A";
        return date("M jS, Y", strtotime($time));
    }

    function RenderStatus($status){
        switch($status){
            case 'pending': return "<span style='color:#939'>$status</span>";
            case 'active': return "<span style='color:#393'>$status</span>";
            case 'expired': return "<span style='color:#A33'>$status</span>";
            case 'disabled': return "<span style='color:#33A'>$status</span>";
        }
        return $status;
    }

}
<?php
namespace AppBundle\Controller\Staff;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 3:05 PM
 */

use ChapmanRadio\DB;
use ChapmanRadio\Request;
use ChapmanRadio\Schedule;
use ChapmanRadio\ShowModel;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class TimeStampController extends Controller
{
    /**
     * @Route("/staff/timestamp", name="staff_timestamp")
     */
    public function indexAction(ContainerInterface $container = null)
    {
        define('PATH', '../');

        if(isset($_REQUEST["make"])) {
            $t = strtotime(urldecode($_REQUEST["make"]));
            if($t === false) echo "strtotime() Could not parse input.";
            else echo "<b>$t</b><br />".date("l g:i a", $t)."<br />".date("n / j / y", $t)."<br /><i style='color:#848484;'>".date('r', $t)."</i><br /><a href='?timestamp=$t' style='font-size: 12px; color:#393939;'>Reload with $t</a>";
            die();
        }

        $dateformat = 'l, M jS, Y';

        Template::SetPageTitle("Timestamp Utility");
        Template::SetBodyHeading("Chapman Radio", "Timestamp Utility");
        //Template::RequireLogin("/staff/timestamp","Staff Resources", "staff");

// timestamp calculations
        $timestamp = Request::GetInteger('timestamp');
        if(!$timestamp) $timestamp = time();
        $dater = date('r', $timestamp);
        $usdate = date('n / j / y', $timestamp);
        $dayofweek = date('l', $timestamp);
        $timeofday = date('g:i a', $timestamp);

// mp3 calculations
        $roundedtimestamp = strtotime(date('j F Y', $timestamp));
        $mp3sTABLE = "";
        for($i = 6;$i < 30;$i++) {
            $recordedon = date("Y-m-d H:i:s",($roundedtimestamp+$i*(60*60)));
            $mp3 = DB::GetFirst("SELECT mp3id, url, showid, recordedon FROM mp3s WHERE `recordedon`='$recordedon'" );
            if(!empty($mp3)) {
                $mp3showid = $mp3['showid'];
                $show = ShowModel::FromId($mp3showid);
                $url = $mp3['url'];
                $mp3id = $mp3['mp3id'];
                $djs = $show->GetDjNamesCsv();
                $mp3timeofday = date("g:i a", strtotime($mp3['recordedon']) );
                $mp3sTABLE .= "
			<tr class='".($i%2==0?'evenRow':'oddRow')."'>
				<td><a href='/$url'>mp3 #$mp3id</td>
				<td>$mp3timeofday</td>
				<td>Show #{$show->id}, {$show->name}, $djs</td>
			</tr>";
            }
        }

        Template::style(".oddRow{background:#EEE;} .timestamp {margin:0 auto 10px;} .timestamp td {padding:0 18px 2px;}");
        Template::css(PATH."css/specs.css");
        Template::AddBodyContent("
	<div style='text-align:center;margin:10px 40px;width:auto;'>
		<table><tr><td style='width:auto;padding:10px;'>

			<h3>Timestamp: $timestamp</h3>
			<table cellspacing='0' cellpadding='0' class='timestamp'>
				<tr class='oddRow'>
					<td>Timestamp</td>
					<td>$timestamp</td>
				</tr>
				<tr>
					<td>RFC 2822</td>
					<td>$dater</td>
				</tr>
				<tr class='oddRow'>
					<td>US Date</td>
					<td>$usdate</td>
				</tr>
				<tr>
					<td>Day of Week</td>
					<td>$dayofweek</td>
				</tr>
				<tr class='oddRow'>
					<td>Time of Day</td>
					<td>$timeofday</td>
				</tr>
			</table>
			<p><br /></p>
			
			<h3>Make Timestamp</h3>
			<script type='text/javascript'>
			function makeTimestamp() {
				$.get('/staff/timestamp', { 'make' : $('#m_input').val() }, function(data){
					$('#m_output').html(data);
					$('#m_output').show();
				});
			}
			</script>
			<form method='javascript:makeTimestamp();' onsubmit='makeTimestamp();return false;'>
				<p>strtotime(<input id='m_input' />) <input type='submit' value='=' /></p>
				<div id='m_output' class='specs' style='display:none;margin:0 auto;'></div>
			</form>
			<p><br /></p>

		</td>");

// show calculations
        $schedid = Schedule::ScheduledAt($timestamp);
        $happnid = Schedule::HappenedAt($timestamp);
        $schedshow = ShowModel::FromId($schedid);
        $happnshow = ShowModel::FromId($happnid);

        Template::AddBodyContent("
		<td style='width:auto;padding:10px;'>
			<h3>Show at ".date('l, g:i a', $timestamp)."</h3>
			<table cellspacing='0' cellpadding='0' class='timestamp'>
				<tr class='oddRow'>
					<td>Show ID #</td>
					<td>".$schedid."</td>
				</tr>
				<tr>
					<td>Show Name</td>
					<td>".(($schedshow)?$schedshow->name : "N/A")."</td>
				</tr>
				<tr class='oddRow'>
					<td>DJs</td>
					<td>".(($schedshow)?$schedshow->GetDjNamesCsv() : "N/A")."</td>
				</tr>
			</table>
			<p><br /></p>
			
			<h3>Show happened at ".date('l, g:i a', $timestamp)."</h3>
			<table cellspacing='0' cellpadding='0' class='timestamp'>
				<tr class='oddRow'>
					<td>Show ID #</td>
					<td>".$happnid."</td>
				</tr>
				<tr>
					<td>Show Name</td>
					<td>".(($happnshow)?$happnshow->name : "N/A")."</td>
				</tr>
				<tr class='oddRow'>
					<td>DJs</td>
					<td>".(($happnshow)?$happnshow->GetDjNamesCsv() : "N/A")."</td>
				</tr>
			</table>
			<p><br /></p>
			
			<h3>mp3s</h3>
			<table cellspacing='0' cellpadding='0' class='timestamp'>
				$mp3sTABLE
			</table>
			<p><br /></p>
		
		</td></tr></table>
	</div>");
        return Template::Finalize($this->container);

    }
}
<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 4/20/17
 * Time: 8:57 AM
 */

namespace AppBundle\Controller\dj;


use ChapmanRadio\DB;
use ChapmanRadio\DJLive;
use ChapmanRadio\Evals;
use ChapmanRadio\GradeStructureModel;
use ChapmanRadio\Request;
use ChapmanRadio\Season;
use ChapmanRadio\Session;
use ChapmanRadio\ShowModel;
use ChapmanRadio\Site;
use ChapmanRadio\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LiveController extends Controller
{

    /**
     * @Route("/dj/live", name="dj_live")
     */
    public function indexAction(ContainerInterface $container = null)
    {

        define('PATH', '../');

        Template::SetPageTitle("DJ Live");
        Template::SetBodyHeading("Chapman Radio", "DJ Live");
        Template::RequireLogin("/dj/live","DJ Live Page");

# Template::AddBodyContent("<div class='couju-debug' style='margin: 0 10px;'>Hey DJs! During Spring Break, no one has to broadcast - but if you're more then welcome to!<br />Just select your show as usual. If you get to a page that says 'Schedule Conflict' just check the boxes and press Override.<br /><strong>YOUR SHOW WILL BE RECORDED</strong></div>");

        if (isset($_GET['djlive_override_broadcast_protection'])) $_SESSION['djlive_override_broadcast_protection'] = true;
        if (isset($_GET['djlive_override_ip_protection'])) $_SESSION['djlive_override_ip_protection'] = true;

        if (!Site::$Broadcasting && !isset($_SESSION['djlive_override_broadcast_protection'])) {
            if (Session::isStaff()) {
                Template::AddBodyContent("<div style='color:red;margin: 10px;'>You're on staff so you can preview the DJ Live page even with the site not broadcasting</div>");
            } else {
                Template::AddCoujuError("<b>Error: Not broadcasting.</b><br />Chapman Radio is not currently not broadcasting.<br />This probably means that we're on a break");
                Template::Finalize();
            }
        }
        if (!in_array(Request::ClientAddress(), Site::$StationIps) && !isset($_SESSION['djlive_override_ip_protection']) && !Session::isStaff()) {
            //Template::Finalize("<div style='width:480px;margin:10px auto;text-align:left;'><p>Welcome to the Chapman Radio Live page.</p><p>This page is <b>only active from the iMac in the station</b>, when you are doing your show.</p><br /><p>It appears that you are not on the iMac in the station.</p><p>If you are on the iMac in the station and you see this message, please contact any member of staff for help.</p>");
        }
// if the user wants to deauthorize, let them.
        if (isset($_GET['deauthorize'])) {
            DJLive::Deauthorize();
            Template::notify("Live", "<strong>Logged out of DJ Live<br /><br />(You are still logged into Chapman Radio)</strong>");
            Header("Location: /dj/live");
        }
// if the user is active on DJ Live, skip authentication. otherwise, null and we have to authenticate
        if (DJLive::isActive())
            $liveshowid = DJLive::getActive();
// the user wants to login to their show
        else if (isset($_POST['LOGIN_TO_SHOW']) || isset($_POST['OVERRIDE_SCHEDULED_SHOW']) || isset($_POST['LOGIN_WITH_BYPASS']))
            $liveshowid = DJLive::handleLogin();
// if the user didn't login correctly above, ask for login
        if (!isset($liveshowid)) {
            DJLive::LoginForm();
            return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());
        }

        /* AND NOW THE ACTUAL DJ LIVE PAGE!!!!!! */
        Template::css("/legacy/css/formtable.css");
        Template::js("/legacy/dj/js/live.js?peace");
        Template::js("/legacy/js/jquery.scrollTo.js");
        Template::js("/legacy/js/jquery.watermark.min.js");
        Template::css("/legacy/css/dj-chat.css");
        Template::css("/legacy/dj/css/live.css?3");
        Template::css("/legacy/css/ajaxresults.css");
        Template::css("/legacy/css/nowplaying.css");
        Template::css("/legacy/css/calendaricon.css");
        $liveshow = ShowModel::FromId($liveshowid);
        Template::AddBodyContent("<div style='position: absolute; top: 20px; text-align: right; display: block; width: 90%;'>");
        if ($_SESSION['DJLiveexpires'] == 0 || Session::isStaff()) Template::AddBodyContent("<a href='?deauthorize'>Logout of DJ Live ONLY</a><br />");
        Template::AddBodyContent("<a href='/logout?source=djlive'>Logout of Chapman Radio</a></div>");
        if (date('m-d') == "12-25") self::Egg("xmas");
        if (date('m-d') == "03-17") self::Egg("patty");

        $user = Session::GetCurrentUser();
        $pref = $user->rawdata['petpreference'];
        $notimportantclass = "";

        if ($pref == 'none') $pref = 'cat';

        switch ($pref) {
            case 'cat':
            case 'dog':
            case 'pet':
                $notimportantimg = "http://{$pref}oftheday.com/archive/" . date('Y/F/d') . ".jpg";
                $notimportantlink = "{$pref}oftheday.com";
                break;
            default:
                $notimportantimg = "/legacy/img/pets/{$pref}.jpg";
                $notimportantlink = "chapmanradio.com/umadbro-c";
                $notimportantclass = "internal";
                break;
        }

// start layout table
        Template::AddBodyContent("
<div id='djlivebox_container' style='margin-top: 20px;'>
<div class='djlivebox_column' id='djlivecolumn1'>
<div class='djlivebox gloss nowplaying' style='min-height: 200px'>
	<input type='hidden' id='nowplaying_liveshowid' value='$liveshowid' />
	<h2>Now Playing</h2>
	<div class='tabs' style='margin: 5px 0;' id='nowplaying_navbar' ><ul>
		<li class='music active'><a>Music</a></li>
		<li class='talk'><a>Talk</a></li>
	</li></div>
	
	<div id='musicpane'>
		<div id='nowplaying_search'>
			<span>Search for a Song</span><br />
			<input type='text' id='nowplaying_input' value='' autocomplete='off' />
			<div id='nowplaying_suggestion_box'>
				<div id='nowplaying_suggestions'></div>
				<div class='nowplaying_message ready'><p>Start typing to search for tracks</p></div>
				<div class='nowplaying_message loading'><p>Checking for more tracks...</p></div>
				<div class='nowplaying_message nocache'><p>Waiting for tracks...</p></div>
				<div class='nowplaying_message notracks'><p>Our music service appears to be overloaded or couldn't find anything for that search<br /><br />Try again in a few minutes or report to webmaster@chapmanraido.com</p></div>
				<div class='nowplaying_message error'><p>Unable to connect to database. Try searching again or report to webmaster@chapmanradio.com.</p></div>
			</div>
		</div>
		<div id='nowplaying_preview'>
			<span style='color:#848484'>Preview</span>
			<br />
			<div class='gloss nowplaying_container' style='width:300px;'>
				<img class='nowplaying_image' alt='' style='float:left;margin:0 8px;' id='image_preview' />
				<span class='nowplaying_title' id='song_preview'></span><br />
				<span style='color:#AAA'>by</span> <span class='nowplaying_artist' id='artist_preview'></span><br />
				<span class='nowplaying_notes' id='notes_preview'> &nbsp; </span><br />
			</div>
			<a id='nowplaying_reset'>Change Track</a>
			<div class='clear'></div>
			<br />
			<span>Notes <span style='color:#AAA'>(optional)</span></span>
			<br />
			<textarea class='nowplaying_input' id='nowplaying_musicnotes' value=''></textarea>
			<br />
			<input id='nowplaying_submitmusic' type='submit' value='Update' />
		</div>
	</div>
	<div id='talkpane' style='display:none;'>
		<div>
			<span>What are you talking about?</span><br />
			<textarea id='nowplaying_talknotes' name='nowplaying_talknotes'></textarea>
		</div>
		<div>
			<input id='nowplaying_submittalk' type='submit' value=' Update ' />
		</div>
	</div>
</div>
<div class='djlivebox gloss'>
	<h2>Live Stuff</h2>
		<div class='tabs' style='margin: 5px 0;' id='livestuff_navbar'><ul>
		<li class='songkick active'><a href='javascript:live.stuff.load(\"songkick\")'>Concerts</a></li>
		<li class='weather'><a href='javascript:live.stuff.load(\"weather\")'>Weather</a></li>
		<li class='traffic'><a href='javascript:live.stuff.load(\"traffic\")'>Traffic</a></li>
		<li class='phone'><a href='javascript:live.stuff.load(\"phone\")'>Phone Info</a></li>
		<li class='sweepers'><a href='javascript:live.stuff.load(\"sweepers\")'>Sweepers</a></li>
		</ul></div><br />
	<div id='livestuff'></div>
</div>
</div><div class='djlivebox_column' id='djlivecolumn2'>
<div class='djlivebox' style='text-align: right; background: #FF88FF; border: 1px solid #FF00FF; padding: 9px;'>
<h2 style='text-align: center; margin-bottom: 5px;'>Problems?</h2>
<textarea style='width: 330px; height: 80px; resize: none; padding: 2px;' id='dj-live-help'></textarea>
<input type='submit' style='vertical-align: bottom;' id='dj-live-help-submit' value='Send' />
<div id='dj-live-help-yay' style='color: white;'></div>
<script>$('#dj-live-help-submit').click(function(){ if($('#dj-live-help').val() == '') return; $('#dj-live-help-yay').html('Sending...'); $.post('/ajax/help', { message: $('#dj-live-help').val() }, function(){  $('#dj-live-help').val(''); $('#dj-live-help-yay').html('Got it! Thank you!'); }); });</script>
</div>
<div class='couju-debug' style='display: none; '>Problems? Email <a href='mailto:shortandfunny@chapmanradio.com'>shortandfunny@chapmanradio.com</a>!</div>
<div class='djlivebox chats gloss'>
	<h2>Text Messages & ListenLive Chats</h2>
	<div id='sms-root'>
		<div id='sms-nodata'>No Data Yet<br /><br />Have your listeners text<br />" . Site::$SmsNumber . "</div>
	</div>
</div>
<div id='upnext'></div>
</div><div class='djlivebox_column' id='djlivecolumn3'>
<div class='djlivebox gloss'>
	<h2>News</h2>
	<div class='tabs' style='margin: 5px 0;' id='newstuff_navbar'><ul>
		<li class='promos active'><a href='javascript:live.news.load(\"promos\")'>Promos &amp; PSAs</a></li>
		<li class='mygenre'><a href='javascript:live.news.load(\"mygenre\")'>My Genre</a></li>
	</ul></div>
	<div id='news'></div>
</div>
<div class='djlivebox gloss'>
	<h2>Stats</h2>
	<div class='tabs' style='margin: 5px 0;' id='statsstuff_navbar'><ul>
		<li class='listenership active'><a href='javascript:live.stats.load(\"listenership\")'>Listenership</a></li>
		<li class='notimportant'><a href='javascript:live.stats.load(\"notimportant\")'>Not Important</a></li>
	</ul></div>
	<div class='stats_box listenership'>
		<br />
		<div id='stats'><i>Loading...</i></div>
	</div>
	<div class='stats_box notimportant cr-dj-notimportant {$notimportantclass}' style='display:none;'>
		<br /><img style='max-height:200px' src='{$notimportantimg}' />
		<p class='ajaxdatafrom'>These statistics from <a href='http://{$notimportantlink}/' target='_blank'>$notimportantlink</a></p>
	</div>
</div>
</div> </div>"); // column // container
        Template::script("\$(document).ready(function(){ live.genre=\"{$liveshow->genre}\"; });");
        return new \Symfony\Component\HttpFoundation\Response(Template::Finalize());

    }

    function Egg($which)
    {
        Template::AddBodyContent("<script>$(document).ready(function(){ live.nowplaying.nyaeggst('davidtyler:$which!'); })</script>");
    }
}
<?php namespace ChapmanRadio;

class DJLive
{

    public static function Authorize($liveshowid, $endstamp)
    {
        $_SESSION['DJLiveexpires'] = $endstamp;
        $_SESSION['DJLiveauthid'] = $liveshowid;
        return $liveshowid;
    }

    public static function Deauthorize()
    {
        unset($_SESSION['DJLiveexpires']);
        unset($_SESSION['DJLiveauthid']);
    }

    public static function isActive()
    {
        if (isset($_SESSION['DJLiveexpires']) && ($_SESSION['DJLiveexpires'] > time() || $_SESSION['DJLiveexpires'] == 0)) return true;
        self::Deauthorize();
        return false;
    }

    public static function getActive()
    {
        if (self::isActive()) return $_SESSION['DJLiveauthid'];
        return null;
    }

    public static function handleLogin($container)
    {

        $showid = Request::GetFrom($_POST, 'showid');
        if (!$showid) error("Please go back and pick a show.");

        $show = ShowModel::FromId($showid);
        if (!$show) error("Error: Unable to locate that show in our database; please try again.");

        // We don't really care if a staff show is cancelled, or even if that staff member is on that show
        if (isset($_POST['LOGIN_WITH_BYPASS']) && Session::isStaff()) {
            Template::notify("Live", "You are now broadcasting <b>" . $show->name . "</b>.<br />Your attendance has not been recorded.<br /><br /><strong style='color:red;'>Using Bypass, your DJ Live session does not expire until you logout</strong>");
            return self::Authorize($showid, 0);
        }

        // Need a timestamp
        $now = time();

        $timestamp = Request::GetIntegerFrom($_POST, 'timestamp');
        if (!$timestamp) error("Please pick a start time for your show.");

        // Are we within 2 hours of the current time
        if ($timestamp < $now - 60 * 60 * 2 || $timestamp > $now + 60 * 60 * 2)
            error("Invalid timestamp.<br />Go back, refresh the page, then try again.");

        $scheduledshowid = Schedule::HappenedAt($timestamp);

        $userid = Session::GetCurrentUserId();

        // let's make sure the show isn't cancelled
        if ($show->status == 'cancelled') {
            Session::LoginFailed($userid, 'show_cancelled');
            Template::Error($container,"<p>Your show, <b>$show->name</b>, has been <b style='color:#A00'>cancelled</b>.</p><div style='text-align:left;'><p>This means that one or more of your DJs has accumulated 3 strikes. You cannot broadcast a show that has been cancelled.</p><p>Visit <a href='/dj/attendance'>chapmanradio.com/dj/attendance</a> for information on your strikes.</p><p>Email <a href='mailto:attendance@chapmanradio.com'>attendance@chapmanradio.com</a> if you have questions / if you think your show should not be cancelled.</p></div>");
        }

        // make sure they're a part of the show
        if (!$show->HasDj($userid)) error("Hold up there, mate!<br />You are trying to login to a show that you are not associated with. If this is unexpected then contact the webmaster for help");

        // is the user overriding the schedule?
        if (isset($_POST['OVERRIDE_SCHEDULED_SHOW'])) return self::handleOverrideLogin($showid, $timestamp, $userid);

        // is this the scheduled show?
        if ($scheduledshowid == $showid) return self::handleNormalLogin($showid, $timestamp, $userid);

        // not the correct show. ask the user if they want to override
        $scheduledshow = ShowModel::FromId($scheduledshowid);
        Template::AddBodyContent(self::WrongShowForm($show, $scheduledshow, $timestamp));
        Template::script("$('document').ready(function(){ var totalChecked = 0; $('#accept_override_form input[type=checkbox]').change(function(){ if($(this).is(':checked')) totalChecked++; else totalChecked--; $('#overridebutton').prop('disabled', (totalChecked != 5)); }); });");
        return Template::Finalize($container);
        return null;
    }

    public static function handleOverrideLogin($showid, $timestamp, $userid)
    {
        $now = time();
        // Only, so lets override the current show ONLY - from $timestamp, until the end of the timestamp hour
        $endstamp = strtotime(date("Y-m-d H:59:59", $timestamp + 59 * 60));
        Attendance::recordShow($timestamp, $showid, $userid);
        if (Schedule::HappeningNow($timestamp) != $showid) Schedule::alter($timestamp, $endstamp, $showid, "dj/live override");
        Template::notify("Live", "<strong style='color:red;'>You have overridden the schedule.</strong><br /><br/>Your show's attendance has been recorded for this hour.<br /><br />Your DJ live session will end at the end of this hour");
        return self::Authorize($showid, $endstamp);
    }

    public static function handleNormalLogin($showid, $timestamp, $userid)
    {
        $now = time();

        if ($showid == 0) die('problem - showid==0');

        // The scheduled show ($timestamp) is the current show.
        // While the current show is the next show, record attendance

        // The current show
        Attendance::recordShow($timestamp, $showid, $userid);
        $count = 1;

        // If the schedule doesnt say our show, even though it should
        if (Schedule::HappenedAt($timestamp) != $showid) Schedule::alter($timestamp, $timestamp + 3599, $showid, "djlive login");

        // While the next show is this show, login
        while (Schedule::ShouldHappenAt($timestamp + 3600) == $showid) {

            /* Record attendence (as on time) and alter schedule for whatever show starts at $timestamp */
            $count++;
            $timestamp += 3600; // next hour starttime

            Attendance::recordShow($timestamp, $showid, $userid, 0);

            // If the schedule doesnt say our show, even though it should
            if (Schedule::HappenedAt($timestamp) != $showid) Schedule::alter($timestamp, $timestamp + 3599, $showid, "djlive login");
        }

        $countformat = ($count == 1) ? "hour" : $count . " hours";

        Template::notify("Live", "<strong>You are now logged in</strong><br /><br/>Your show's attendance has been recorded for the next $countformat.<br /><br />Your DJ live session will end at the end of your show");

        return self::Authorize($showid, $timestamp + 3599); // Good until the end of the last show we recorded
    }

    public static function LoginForm()
    {
        $now = time();
        $user = Session::GetCurrentUser();
        $usershows = ShowModel::FromDj($user->id);
        Template::AddBodyContent("<div style='width:570px;margin:20px auto;text-align:left;'>
			<h3>Start Broadcasting</h3>
		
			<table style='width:320px;margin:10px auto;' ><tr><td colspan='2'>You are logged in as:</td></tr><tr><td style='width:50px;'><img src='{$user->img64}' alt='' /></td><td><b>{$user->name}</b><br /><a href='/logout'>&raquo; Log out</a></td></tr></table>
			
			<br />");

        if (empty($usershows)) {
            Template::Error("This user account <b>does not have any shows linked to it</b><br /><br />This means that although you have successfully logged on to the site, you are not listed as a DJ for any shows this season.<br /><br />Please contact the current <b>Webmaster</b>, <b>Program Manager</b>, and <b>General Manager</b> for help resolving this issue.<br /><br /><i>You will not be able to use the DJ Live page until your account has been linked up to a show.</i>");
        }

        Template::AddBodyContent("<form method='post'>
			<p>The website needs to know which show you are going to broadcast.</p>
			<p>After you <b>pick your show &amp; start time</b>, the website &amp; iPhone application will know which show to display.</p>
			<div class='gloss' style='width:360px'><h2>Show:</h2>");

        $showpicker = "<select name='showid'><option value=''> - Pick a Show - </option>"; // this is used for the staff priveledges part
        foreach ($usershows as $row) {
            Template::AddBodyContent("<div><div style='padding:10px 18px 30px;text-align:center;float:left;'><input type='radio' required name='showid' value='" . $row->id . "' id='liveshowid" . $row->id . "' /></div><label for='liveshowid" . $row->id . "'><img src='" . $row->img50 . "' alt='' style='float:left;margin:0 6px 20px;' /><b>" . $row->name . "</b><br />" . $row->genre . "<br />" . Season::name($row->seasons_csv) . "</label><br style='clear:both' /></div>");
            $showpicker .= "<option value='" . $row->id . "'>" . $row->name . "</option>";
        }
        $showpicker .= "</select>";

        Template::AddBodyContent("<br style='clear:both' /><h2>Start Time:</h2>");
        $timestamp = strtotime(date("Y-m-d H:00:00", $now));
        if (date("i", $now) < 45) {
            $late = date("i", $now) - date("i", $timestamp);
            $s = $late == 1 ? "" : "s";
            $style = $late < 8 ? "style='color:#A60;'" : "style='color:red'";
            Template::AddBodyContent("<div style='padding:10px 18px 30px;text-align:center;float:left;'><input type='radio' required name='timestamp' value='$timestamp' id='startlate' /></div><label for='startlate'>This show should have started at <b>" . date("g:ia", $timestamp) . "</b>.<br />I'm <b $style>$late minute$s late</b>.</label>");
        }
        Template::AddBodyContent("<br style='clear:both;margin-bottom:6px;' />");
        $timestamp += 60 * 60;
        if (date("i", $now) >= 14) {
            $early = 60 - date("i", $now);
            $s = $early == 1 ? "" : "s";
            Template::AddBodyContent("<div style='padding:10px 18px 30px;text-align:center;float:left;'><input type='radio' required name='timestamp' value='$timestamp' id='startearly' /></div><label for='startearly'>This show will start at <b>" . date("g:ia", $timestamp) . "</b>.<br />I'm <b style='color:green'>$early minute$s early</b>.</label>");
        }
        Template::AddBodyContent("<br style='clear:both;height:0;' /><p style='text-align:center'><input type='submit' id='loginbutton' name='LOGIN_TO_SHOW' value=' Login to Show ' /></p></div></form></div>");

        if ($user->IsStaff()) {
            Template::AddBodyContent("<div style='width:570px;margin:20px auto 60px;text-align:left;'><h3>Staff Priveledges</h3>
			<p>You are on staff, so you may view the DJ live page without recording attendance.</p>
			<form method='post'>
			<p>$showpicker <input type='submit' name='LOGIN_WITH_BYPASS' value='View without recording attendance' /></p>
			</form></div>");
        }
    }

    public static function WrongShowForm($show, $scheduledshow, $timestamp)
    {
        $now = time();
        Template::AddBodyContent("<div style='width:570px;margin:20px auto;text-align:left;'>
		<h3>Schedule Conflict</h3><br />
		<p>It appears that the show that you <b>requested</b> to login to doesn't match what's <b>scheduled</b> to be broadcasting right now.</p>
		<p style='text-align:center;margin:20px auto;color:#484848;'>");
        if ($timestamp <= $now) {
            $late = date('i', $now) - date('i', $timestamp);
            $s = $late == 1 ? "" : "s";
            $style = $late < 8 ? "style='color:#A60;'" : "style='color:red'";
            Template::AddBodyContent("It is now <b $style>$late minute$s after</b><br />");
        } else {
            $early = 60 - date('i', $now);
            $s = $early == 1 ? "" : "s";
            Template::AddBodyContent("It is now <b style='color:green'>$early minute$s before</b><br />");
        }
        Template::AddBodyContent("<span style='font-family:Courier;font-size:22px;color:#757575;'>" . date("l, g:ia", $timestamp) . "</span></p>
		</div>
		<table style='margin:10px auto;width:680px;' cellspacing='0'><tr><td style='width:50%;'>					
		<h2>You Requested:</h2><div class='gloss' style='width:auto;text-align:left;margin-right:20px;'><img src='" . $show->img50 . "' alt='' style='float:left;margin:5px;' /><b>" . $show->name . "</b><br />" . $show->genre . "<br />" . $show->GetDjNamesCsv() . "<br style='clear:both' />
		</div>
		</td><td style='width:50%;'>
		<h2>Scheduled:</h2>");
        if ($scheduledshow) {
            Template::AddBodyContent("<div class='gloss' style='width:auto;text-align:left;'><img src='" . $scheduledshow->img50 . "' alt='' style='float:left;margin:5px;' /><b>" . $scheduledshow->name . "</b><br />" . $scheduledshow->genre . "<br />" . $scheduledshow->GetDjNamesCsv() . "<br style='clear:both' /></div>");
        } else {
            Template::AddBodyContent("<div class='gloss' style='min-height:60px;width:auto;text-align:left;'>Automation<br /><i style='color:#848484'>No show is currently scheduled</i></div>");
        }
        Template::AddBodyContent("</td></tr></table>
		<form id='accept_override_form' method='post' action='/dj/live'>
		<div style='width:570px;margin:20px auto;text-align:left;'>
		<p>You are still allowed to broadcast your show; however you must read and accept the following terms to login to <i>" . $show->name . "</i>.</p>
		<blockquote>
		<p><input type='checkbox' id='ACCEPT_OVERRIDE_1' value='1' /><label for='ACCEPT_OVERRIDE_1'> I understand that <i>" . (($scheduledshow) ? $scheduledshow->name : "Automation") . "</i> would normally broadcast right now.</label></p>
		<p><input type='checkbox' id='ACCEPT_OVERRIDE_2' value='1' /><label for='ACCEPT_OVERRIDE_2'> I understand that I'm going to broadcast <i>" . $show->name . "</i> instead.</label></p>
		<p><input type='checkbox' id='ACCEPT_OVERRIDE_3' value='1' /><label for='ACCEPT_OVERRIDE_3'> I understand that my <b>attendance</b> will be recorded for <i>" . $show->name . "</i> at <i>" . date("g:ia", $timestamp) . "</i>.</label></p>
		<p><input type='checkbox' id='ACCEPT_OVERRIDE_4' value='1' /><label for='ACCEPT_OVERRIDE_4'> I understand that my show <b><i style='color:#800'>will not</i> be recorded</b> unless I have already spoken to the technical staff.</label></p>
		<p><input type='checkbox' id='ACCEPT_OVERRIDE_5' value='1' /><label for='ACCEPT_OVERRIDE_5'> I understand that my show is subject to suspension or cancellation if the Program Director deems my overriding of the scheduled show inappropriate.</label></p>
		</blockquote>
		<p style='text-align:center;'>
			<input type='hidden' name='showid' value='" . $show->id . "' />
			<input type='hidden' name='timestamp' value='$timestamp' />
			<input type='submit' name='OVERRIDE_SCHEDULED_SHOW' id='overridebutton' value=' Override ' disabled='disabled' />
		</p></div></form>");
    }

}
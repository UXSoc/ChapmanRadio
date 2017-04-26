<?php namespace ChapmanRadio;define('PATH', '../');	require_once PATH."inc/global.php";
Template::SetPageTitle("Suspended Account");
Template::SetBodyHeading("Chapman Radio", "Suspended Account");
Template::RequireLogin("DJ Resources");

Template::css("/css/formtable.css");

if(isset($_REQUEST['reactivate'])){
	$userid = Session::getCurrentUserID();
	$result = DB::Query("UPDATE users SET suspended='0' WHERE userid='$userid'");
	header("Location: /dj");
	exit;
	}
Template::AddBodyContent("<div class='gloss'><p>Your account has been temporarily suspended - most likely a result of receiving	 a second strike. If you recieved a strike, but it was later cleared, this message may still appear for up to 24 hours - just click the reactivation link below to clear it now<br /><br /><strong>You can continue to be a part of Chapman Radio, we're just making sure you know you have two strikes in our system.</strong><br/><br />As a reminder, Chapman Radio has a 3 strike <a href='/policies'>policy</a><br />If you receive a 3rd strike, all of the shows you are a part of will be <strong style='color:red;'>cancelled</strong><br /><br />For your reference, there is an overview of your strikes below. If you have questions, please email the attendance manager at attendance@chapmanradio.com<br /><br />After reading our policies, just click <a href='?reactivate'>here</a> to reactivate your account.</div>");$user = Session::GetCurrentUser();
Template::AddBodyContent(Strikes::Overview($user->id));

Template::Finalize();
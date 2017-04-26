<?php namespace ChapmanRadio;
define('PATH', '../../../');	
require_once PATH."inc/global.php";
Template::RequireLogin("DJ Account");
$evalid = Request::GetInteger('evalid');
$eval = DB::GetFirst("SELECT * FROM evals WHERE evalid='$evalid'");
if(!$eval) die(json_encode(array("error"=>"That eval (#$evalid) has already been deleted or does not exist.")));
if($eval['userid'] != Session::GetCurrentUserId()) die(json_encode(array("error"=>"That eval (#$evalid) does not belong to you")));
DB::Query("DELETE FROM evals WHERE evalid='$evalid'");
die(json_encode(array("success"=>true, "eval"=>$eval)));
break;
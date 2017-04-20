<?php namespace ChapmanRadio;

class TrainingSignupModel extends \Sinopia\BaseModel {
	
	public $slot;
	public $userid;
	public $present;
	
	public $season;
	public $datetime;
	public $staffid;
	public $max;

	public static $DataTable = "v_training_signups";
	public static $DataMap = [
		"id" => "trainingsignup_id",
		"slot" => "trainingsignup_slot",
		"userid" => "trainingsignup_userid",
		"present" => "trainingsignup_present",
		"season" => "trainingslot_season",
		"datetime" => "trainingslot_datetime",
		"staffid" => "trainingslot_staffid",
		"max" => "trainingslot_max"
		];
	}
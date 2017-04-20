<?php namespace ChapmanRadio;

class TrainingSlotModel extends \Sinopia\BaseModel {
	
	public $season;
	public $datetime;
	public $staffid;
	public $max;
	
	// view only
	public $count;
	
	public static $DataTable = "v_training_slots";
	public static $DataMap = [
		"id" => "trainingslot_id",
		"season" => "trainingslot_season",
		"datetime" => "trainingslot_datetime",
		"staffid" => "trainingslot_staffid",
		"max" => "trainingslot_max",
		"count" => "trainingslot_count",
		];
	}
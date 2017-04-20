<?php namespace ChapmanRadio;

class GradeStructureModel extends \Sinopia\BaseModel {
	
	public $name;
	public $type;
	public $parent_id;
	public $season;
	public $condition;
	public $max;
	public $target;
	public $value;
	public $user_id;
	
	public $children = [];
	
	public $extern;
	
	private $_score = NULL;

	public static $DataTable = "grade_structure";
	public static $DataMap = [
		"id" => "grade_id",
		"name" => "grade_name",
		"type" => "grade_type",
		"parent_id" => "grade_parent",
		"season" => "grade_season",
		"condition" => "grade_condition",
		"max" => "grade_max",
		"target" => "grade_target",
		"value" => "grade_value",
		"user_id" => "user_id"
		];
	
	public static function ForUser($uid){
		$results = []; $map = [];
		$rows = DB::GetAll("SELECT grade_structure.*,grade_values.grade_value FROM grade_structure LEFT JOIN grade_values ON (grade_structure.grade_id = grade_values.grade_id AND user_id = :uid) WHERE grade_season = :season", [ ":season" => Site::CurrentSeason(true), ":uid" => $uid ]);
		$data = self::FromResults($rows);
		
		// first map the data
		foreach($data as $row){
			$row->user_id = $uid;
			$map[$row->id] = $row;
			}
		
		// then itterate sort parents from children
		foreach($data as $row){
			if ($row->parent_id == NULL) $results[] = $row;
			else $map[$row->parent_id]->children[] = $row;
			}
			
		//print_r($map);
		
		return $results;
		}
	
	public static function ForCurrentSeason(){
		$results = []; $map = [];
		$rows = DB::GetAll("SELECT * FROM grade_structure WHERE grade_season = :season", [ ":season" => Site::CurrentSeason(true) ]);
		$data = self::FromResults($rows);
		
		// first map the data
		foreach($data as $row){
			$map[$row->id] = $row;
			}
		
		// then itterate sort parents from children
		foreach($data as $row){
			if ($row->parent_id == NULL) $results[] = $row;
			else $map[$row->parent_id]->children[] = $row;
			}
			
		//print_r($map);
		
		return $results;
		}
	
	public function DisplayScore(){
		if($this->Score() === NULL) return "-";
		return $this->Score();
		}
	
	public function DisplayPercent(){
		if($this->Score() === NULL) return "-";
		if($this->type === "strikes") return "-";
		if($this->condition === 'child_sum') return "-";
		return number_format(($this->Score() / $this->max)*100, 2)."%";
		}
	
	public function Load($user_id, $known){
		$this->_score = NULL;
		foreach($this->children as $child) $child->Load($user_id, $known);
		$this->value = isset($known[$this->id]) ? $known[$this->id] : NULL;
		$this->user_id = $user_id;
		}
	
	public function Extern($fn){
		$this->extern = $fn;
		foreach($this->children as $child) $child->Extern($fn);
		}
		
	public function Score(){
		if($this->_score !== NULL) return $this->_score;
		$this->_score = $this->ScoreInternal();
		if($this->_score > $this->max) $this->_score = $this->max;
		return $this->_score;
		}
	
	public function Cat(){
		if($this->target === NULL) return "neutral";
		switch($this->condition){
			case 'equal_to': return ($this->Score() == $this->target) ? "good" : "bad";
			case 'less_than': return ($this->Score() < $this->target) ? "good" : "bad";
			case 'less_than_equal': return ($this->Score() <= $this->target) ? "good" : "bad";
			case 'greater_than': return ($this->Score() > $this->target) ? "good" : "bad";
			case 'greater_than_equal': return ($this->Score() >= $this->target) ? "good" : "bad";
			}
		return "unknown";
		}
	
	private function ScoreInternal(){
		if($this->value !== NULL) return $this->value;
		if($this->extern !== NULL){ $fn = $this->extern; return $fn($this); }
		switch($this->type){
			case 'manual':
				return 0;
			case 'evals':
				$evals = DB::GetFirst("SELECT COUNT(DISTINCT(timestamp)) as c FROM evals WHERE userid = :uid AND season = :s", [ ":uid" => $this->user_id, ":s" => Site::CurrentSeason() ]);
				return $evals['c'];
			case 'category':
				$score = 0;
				foreach($this->children as $child) $score += $child->Score();
				return $score;
			case 'strikes':
				$check = Strikes::Check($this->user_id);
				return $check['totalStrikes'];
			default:
				return NULL;
			}
		}
	
	}
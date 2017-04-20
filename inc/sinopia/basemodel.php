<?php namespace Sinopia;

abstract class BaseModel {
	
	public $id;
	public $rawdata;
	public static $DataTable;
	public static $DataTableSelect;
	
	public static $DataMap;
	private $_Changes = [];
	private $_ChangesIncrements = [];
		
	public function __setup(){}
	public function __construct($db_assoc){
		if(static::$DataMap == NULL) throw new APIException("Model does not define a DataMap");
		if($db_assoc === NULL || $db_assoc === FALSE) throw new APIException("Cannot create a Model from empty data");
		$this->rawdata = $db_assoc;
		foreach(static::$DataMap as $prop => $db) if(isset($db_assoc[$db])) $this->$prop = $db_assoc[$db];
		$this->__setup();
		}
	
	public function Serialize(){
		$fields = [];
		foreach(static::$DataMap as $field => $dbfield) $fields[$field] = $this->$field;
		return $fields;
		}
	
	public function Update($field, $value){
		$this->_Changes[$field] = $value;
		$this->$field = $value;
		}
	
	public function UpdateIncrement($field, $increment){
		$this->_ChangesIncrements[$field] = $increment;
		$this->$field = $this->$field + $increment;
		}
	
	public function UpdateMany($values){
		foreach($values as $field => $value){
			//if($val === "") continue;
			if($field == "id") continue;
			if(!isset(static::$DataMap[$field])) continue;
			$this->_Changes[$field] = $value;
			$this->$field = $value;
			}
		$this->SaveChanges();
		}
	
	public function SaveChanges(){
		if(empty($this->_Changes) && empty($this->_ChangesIncrements)) return;
		if(static::$DataTable == NULL) throw new APIException("Model does not define a DataTable");
		$data = array(":id" => $this->id); $updates = array();
		foreach($this->_Changes as $field => $value){
			if($field === "id") throw new APIException("Illegal Update on Primary Key");
			if(!isset(static::$DataMap[$field])) throw new APIException("Model does not define \"$field\" property");
			$dbfield = static::$DataMap[$field];
			$updates[] = "$dbfield =  :$field";
			$data[":$field"] = $value;
			}
		foreach($this->_ChangesIncrements as $incfield => $increment){
			if($incfield === "id") throw new APIException("Illegal Update on Primary Key");
			if(!isset(static::$DataMap[$incfield])) throw new APIException("Model does not define \"$incfield\" property");
			$dbfield = static::$DataMap[$incfield];
			$updates[] = "$dbfield = $dbfield + :i$incfield";
			$data[":i$incfield"] = $increment;
			}
		$idfield = static::$DataMap["id"];
		DB::Query("UPDATE ".static::$DataTable . " SET " . implode(',', $updates) . " WHERE $idfield = :id", $data);
		$this->_Changes = [];
		$this->_ChangesIncrements = [];
		}
	
	/* Static Accessors */

	public static function FromId($id){
		if($id === 0) return NULL;
		$classname = get_called_class();
		return Cache::Handle($classname."_".$id, function() use ($id, $classname){
			return $classname::FromIdNoCache($id);
			});
		}
	
	public static function FromIdNoCache($id){
		if(static::$DataMap == NULL) throw new APIException("Model does not define a DataMap");
		if(static::$DataTable == NULL) throw new APIException("Model does not define a DataTable");
		$idfield = static::$DataMap["id"];
		$classname = get_called_class();
		return $classname::FromResult(DB::GetFirst("SELECT * FROM ".static::$DataTable." WHERE $idfield = :id", [":id" => $id]));
		}
		
	public static function FromIds($ids){
		if(static::$DataMap == NULL) throw new APIException("Model does not define a DataMap");
		if(static::$DataTable == NULL) throw new APIException("Model does not define a DataTable");
		if(empty($ids)) return array();
		$conditions = array(); $data = []; $idfield = static::$DataMap["id"];
		foreach($ids as $key => $id){ $conditions[] = "$idfield = :$key"; $data[":$key"] = $id; }
		$classname = get_called_class();
		return $classname::FromResults(DB::GetAll("SELECT * FROM ".static::$DataTable." WHERE ".implode(" OR ", $conditions)."", $data));
		}
	
	public static function Paged($per_page){
		$page = Request::GetInteger('s_page', 1) - 1;
		$start = $per_page * $page;
		$classname = get_called_class();
		return $classname::FromResults(DB::GetAll("SELECT * FROM ".static::$DataTable." LIMIT $start,$per_page"));
		}
	
	public static function All(){
		$classname = get_called_class();
		return $classname::FromResults(DB::GetAll("SELECT * FROM ".static::$DataTable));
		}
	
	public static function Where($query, $data = NULL){
		$classname = get_called_class();
		return $classname::FromResults(DB::GetAll("SELECT * FROM ".static::$DataTable." WHERE $query", $data));
		}
	
	public static function WhereFirst($query, $data = NULL){
		$classname = get_called_class();
		return $classname::FromResult(DB::GetFirst("SELECT * FROM ".static::$DataTable." WHERE $query", $data));
		}
	
	public static function FromQuery($query, $data = NULL){
		$classname = get_called_class();
		return $classname::FromResults(DB::GetAll($query, $data));
		}
	
	public static function FromQueryFirst($query, $data = NULL){
		$classname = get_called_class();
		return $classname::FromResult(DB::GetFirst($query, $data));
		}
	
	public static function FromResult($result){
		if($result === NULL || $result === FALSE) return NULL;
		$classname = get_called_class();
		return new $classname($result);
		}
	
	public static function FromResults($results){
		$ret = array();
		foreach($results as $result) $ret[] = self::FromResult($result);
		return $ret;
		}
		
	public static function Create($values){
		if(static::$DataTable == NULL) throw new APIException("Model does not define a DataTable");
		if(static::$DataMap == NULL) throw new APIException("Model does not define a DataMap");
		
		// translate php keys to db keys
		$dbvalues = self::TranslateKeysToDB($values);
		$id = DB::Insert(static::$DataTable, $dbvalues);
		
		// if this was not an auto-increment value, then use the provided value
		if($id == 0) $id = $values['id'];
		
		$dbvalues[static::$DataMap["id"]] = $id;
		
		// push row back into Model
		$classname = get_called_class();
		return new $classname($dbvalues);
		}
	
	public static function Delete($model){
		if(!$model instanceof self) throw new APIException("BaseModel::Delete requires a model that inherits BaseModel");
		if(static::$DataTable == NULL) throw new APIException("Model does not define a DataTable");
		if(static::$DataMap == NULL) throw new APIException("Model does not define a DataMap");
		DB::Query("DELETE FROM ".static::$DataTable." WHERE `".static::$DataMap["id"]."` = :id", array(":id" => $model->id));
		}
	
	public static function SerializeAll($data){
		if($data instanceof self) return $data->Serialize();
		if(is_array($data)){
			$results = array();
			foreach($data as $model) $results[] = $model->Serialize();
			return $results;
			}
		return NULL;
		}
	
	private static function TranslateKeysToDB($values){
		if(static::$DataMap == NULL) throw new APIException("Model does not define a DataMap");
		$dbvalues = [];
		foreach($values as $field => $v) if(isset(static::$DataMap[$field])) $dbvalues[static::$DataMap[$field]] = $v;
		return $dbvalues;
		}
	
	}
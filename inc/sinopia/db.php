<?php namespace Sinopia;
use PDO;
use DateTime;

abstract class DB {
	
	private static $query_count = 0;
	private static $link;
	
	private static $last;
	
	public static function Setup($link){
		self::$link = $link;
		}
	
	public static function AffectedRows(){
		return DB::$last->rowCount();
		}
	
	public static function LastInsertId(){
		return DB::$link->lastInsertId();
		}
	
	public static function Query($query, $data = NULL){
		if(self::$link === NULL) throw new \Exception("DB Link not Setup");
		self::$query_count++;
		
		// Log::ToFile("db.log", $query . "\n");
		
		if($data === NULL){
			$STH = self::$link->query($query);
			return $STH;
			}
		
		self::Clean($data);
		
		$STH = self::$link->prepare($query);  
		$STH->execute($data);
		
		self::$last = $STH;
		
		return $STH;
		}
	
	public static function QueryMany($query, $data){
		if(self::$link === NULL) throw new \Exception("DB Link not Setup");
		self::$query_count++;
		
		$STH = self::$link->prepare($query);  
		
		foreach($data as $i => $row){
			self::Clean($row);
			$STH->execute($row);
			}
		
		return $STH;
		}
	
	private static function Clean(&$row){
		foreach($row as $key => $item){
			if($item instanceof DateTime) $row[$key] = $item->format('Y-m-d H:i:s');
			}
		}
		
	public static function TableCount($table){
		$count = self::GetFirst("SELECT count(*) as count FROM $table");
		return $count['count'];
		}

	public static function GetFirst($query, $data = NULL){
		$result = self::Query($query, $data)->Fetch(PDO::FETCH_ASSOC);
		if($result === NULL || $result === FALSE) return NULL;
		return $result;
		}
		
	public static function GetAll($query, $data = NULL){
		return self::Query($query, $data)->FetchAll(PDO::FETCH_ASSOC);
		}
	
	public static function GetQueryCount(){
		return self::$query_count;
		}
		
	public static function Insert($table, $data){
		self::ValuesToFieldsParameters($data, $fields, $values, $d);
		self::Query("INSERT INTO `{$table}` (".implode(',', $fields).") VALUES(".implode(',', $values).")", $d);
		return self::LastInsertId();
		}
	
	public static function Delete($table, $data){
		self::ValuesToWheresParameters($data, $wheres, $d);
		self::Query("DELETE FROM `{$table}` WHERE (".implode(' AND ', $wheres).")", $d);
		}
	
	private static function ValuesToFieldsParameters($data, &$fields = [], &$values = [], &$d = []){
		foreach($data as $field => $value){
			$fields[] = "`$field`";
			$values[] = ":$field";
			$d[":$field"] = $value;
			}
		}
	
	private static function ValuesToWheresParameters($data, &$wheres = [], &$d = []){
		foreach($data as $field => $value){
			$wheres[] = "`$field` = :$field";
			$d[":$field"] = $value;
			}
		}
	
	}
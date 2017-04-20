<?php namespace Sinopia;

class Search {
	
	public static function FreeForm($options, &$data = []){
		$options = array_merge([ "table" => "", "query" => "", "fields" => [], "negative_terms" => [], "limit" => 10 ], $options);
		
		$query = trim(urldecode($options['query']));
		$words = explode(" ", $query);
		
		if(count($words) == 0) return NULL;
		
		$where = []; $ifs = [];
		$c = 0;
		
		// positive points for matching a query word
		foreach($words as $word){
			if($word == "" || $word == " ") continue;
			
			$where_parts = [];
			foreach($options['fields'] as $field){ $where_parts[] = "{$field} LIKE :l{$c}"; }
			$where[] = "(".implode(" OR ", $where_parts).")";
			
			foreach($options['fields'] as $field){
				$ifs[] = "if({$field} LIKE :l$c, 1, 0)";
				$ifs[] = "if({$field} = :w$c, 3, 0)";
				}
			
			$data[":w$c"] = $word;
			$data[":l$c"] = '%'.$word.'%';
			$c++;
			}
		
		// negative points for matching a negative word
		foreach($options['negative_terms'] as $word){
			foreach($options['fields'] as $field) $ifs[] = "if({$field} LIKE :l$c, -1, 0)";
			$data[":l$c"] = '%'.$word.'%';
			$c++;
			}
		
		// lots of points for matching the whole phrase
		foreach($options['fields'] as $field) $ifs[] = "if({$field} = :search, 5, 0)";
		$data[':search'] = $query;
		
		return "SELECT *, (".implode(' + ', $ifs).") AS n FROM {$options['table']} WHERE (".implode(' AND ', $where).") ORDER BY n DESC LIMIT {$options['limit']}";
		}
	
	}
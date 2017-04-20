<?php namespace Sinopia;

class ValidateException extends \Exception {}

class Validate {
	
	private $_rules = [];
	private $_problems = [];
	private $_data = [];
	private $_checked = false;
	private $_throw = false;
		
	public function AddCustomRule($field, $fn){
		$this->_rules[] = [ 'field' => $field, 'type' => 'custom', 'function' => $fn ];
		}
	
	public function AddRequiredRule($field, $title){
		$this->_rules[] = [ 'field' => $field, 'type' => 'required', 'title' => $title ];
		}
	
	public function AddEmailRule($field, $title){
		$this->_rules[] = [ 'field' => $field, 'type' => 'email', 'title' => $title ];
		}
	
	public function IsValid(){
		$this->Validate();
		return (empty($this->_problems));
		}
	
	public function SetValidationData($values){
		$this->_data = $values;
		$this->_checked = false;
		}
	
	public function Validate(){
		if($this->_checked) return;
		$this->_problems = [];
		foreach($this->_rules as $rule){
			switch($rule['type']){
				case 'custom':
					self::ValidateCustom($rule);
					break;
				case 'required':
					self::ValidateRequired($rule);
					break;
				case 'email':
					self::ValidateEmail($rule);
					break;
				default:
					throw new ValidateException("Rule type {$rule['type']} is not valid");
				}
			}
		$this->checked = true;
		}
	
	public function ValidationSummary(){
		if(empty($this->_problems)) return;
		$result = "<ul class='couju-validation-summary'>";
		foreach($this->_problems as $problem) $result .= "<li>".$problem."</li>";
		return $result."</ul>";
		}
	
	private function ValidateCustom($rule){
		if(!isset($this->_data[$rule['field']])) $this->ValidationProblem("Field {$rule['field']} does not exist");
		try{ $rule['function']($this->_data[$rule['field']]); }
		catch(\Exception $e){ $this->ValidationProblem($e->getMessage()); }
		}
	
	private function ValidateRequired($rule){
		if(!isset($this->_data[$rule['field']]) || trim($this->_data[$rule['field']]) == "")
			$this->ValidationProblem("{$rule['title']} is required");
		}
	
	private function ValidateEmail($rule){
		if(!isset($this->_data[$rule['field']]))
			$this->ValidationProblem("{$rule['title']} is required");
		if(preg_match("/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i", $this->_data[$rule['field']]) === 0)
			$this->ValidationProblem("{$rule['title']} is not a valid email address");
		}
	
	private function ValidationProblem($msg){
		$this->_problems[] = $msg;
		if($this->_throw) throw new ValidateException($msg);
		}
	
	}
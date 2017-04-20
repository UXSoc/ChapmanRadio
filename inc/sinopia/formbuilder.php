<?php namespace Sinopia;

class FormBuilder extends \Sinopia\Validate {
	
	private $_form_id;
	private $_parts = [];
	private $_fields = [];
	private $_filters = [];
	
	public static function Start($options){
		return new FormBuilder($options);
		}
	
	public function __construct($options = []){
		$options = array_merge([ "id" => "", "extras" => "" ], $options);
		$this->_form_id = $options['id'];
		$action = $_SERVER['REQUEST_URI'];
		$this->_parts[] = "<form role='form' class='dt-form' method='POST' action='{$action}' id='{$options['id']}' {$options['extras']}><input type='hidden' name='{$options['id']}-submit' value='' />";
		}
	
	public function Render(){
		return implode('', $this->_parts).'</form>';
		}
	
	private function _Register($options){	
		$this->_fields[] = $options['id'];
		if(isset($options['required']))
			$this->AddRequiredRule($options['id'], isset($options['validation-title']) ? $options['validation-title'] : $options['title']);
		}
	
	public function Field($options = []){
		$options = array_merge([ "type" => "text", "id" => "", "title" => "Input", "value" => "", "extras" => "", "class" => "" ], $options);
		$this->_Register($options);
		$id = $this->_form_id . "_" . $options['id'];
		
		if($options['value'] instanceof \DateTime) $options['value'] = $options['value']->format('Y-m-d H:i:s');
		
		$this->_parts[] = "<div class='form-group'><label for='{$id}'>{$options['title']}</label><div><input type='{$options['type']}' id='{$id}' name='{$id}' class='form-control {$options['class']}' value='{$options['value']}' {$options['extras']} /></div></div>";
		
		return $this;
		}
	
	public function Textarea($options = []){
		$options = array_merge([ "type" => "text", "id" => "", "title" => "Input", "value" => "", "extras" => "", "class" => "" ], $options);
		$this->_Register($options);
		$id = $this->_form_id . "_" . $options['id'];
		
		$this->_parts[] = "<div class='form-group'><label for='{$id}'>{$options['title']}</label><div><textarea id='{$id}' name='{$id}' class='form-control {$options['class']}' {$options['extras']}>{$options['value']}</textarea></div></div>";
		
		return $this;
		}
	
	public function Typeahead($options = []){
		$options = array_merge([ "url" => "", "value" => "" ], $options);
		$id = $this->_form_id . "_" . $options['id'];
		self::_Register($options);
		$options['id'] .= "_text";
		$options['type'] = "text";
		if(isset($options['required'])) unset($options['required']);
		self::Field($options);
		$this->_parts[] = "<input type='hidden' id='{$id}' name='{$id}' value='{$options['value']}' />";
		$this->_parts[] = "<script>$('#{$id}_text').typeahead({ remote: '{$options['url']}' }).on('typeahead:selected typeahead:autocompleted', function (object, datum) { $('#{$id}').val(datum.id); }).on('change', function(){ $('#{$id}').val(''); }); </script>";
		return $this;
		}
	
	public function Image($options = []){
		$options = array_merge([ "url" => "", "title" => "Image" ], $options);
		$this->_parts[] = "<div class='form-group'><label>{$options['title']}</label><img src='{$options['url']}' /></div>";
		return $this;
		}
	
	public function Hidden($options = []){
		$options = array_merge([ "id" => "", "value" => "" ], $options);
		$id = $this->_form_id . "_" . $options['id'];
		$this->_fields[] = $options['id'];
		$this->_parts[] = "<input type='hidden' id='{$id}' name='{$id}' value='{$options['value']}' />";
		return $this;
		}
	
	public function Text($options = []){
		$options['type'] = "text";
		self::Field($options);
		return $this;
		}
		
	public function DateTime($options = []){
		$options['type'] = "datetime";
		$this->_filters[$options['id']] = function($field, $value){ if(trim($value) === "") return NULL; return $value; };
		self::Field($options);
		return $this;
		}
	
	public function Email($options = []){
		$options['type'] = "email";
		$this->AddEmailRule($options['id'], $options['title']);
		self::Field($options);
		return $this;
		}
	
	public function Password($options = []){
		$options['type'] = "password";
		self::Field($options);
		return $this;
		}
	
	public function SubmitButton($options = []){
		$options = array_merge([ "text" => "Submit", "extras" => "", "class" => "btn-primary" ], $options);
		$this->_parts[] = "<div class='form-group'><button type='submit' class='btn {$options['class']} gp-form-submit' data-form-target='{$this->_form_id}' {$options['extras']}>{$options['text']}</button></div>";
		return $this;
		}
	
	public function ExtraSubmit($options = []){
		$options = array_merge([ "text" => "Submit", "extras" => "", "class" => "btn-primary" ], $options);
		return "<button type='button' class='btn {$options['class']} gp-form-submit' data-form-target='{$this->_form_id}' {$options['extras']}>{$options['text']}</button>";
		}
	
	public function Valid(){
		$this->SetValidationData($this->GetFields());
		return parent::IsValid();
		}
	
	public function Posted(){
		return isset($_POST[$this->_form_id . '-submit']);
		}
	
	public function GetFields(){
		$results = [];
		foreach($this->_fields as $field) $results[$field] = $_POST[$this->_form_id . "_" . $field];
		foreach($this->_filters as $field => $filter) if(isset($results[$field])) $results[$field] = $filter($field, $results[$field]);
		return $results;
		}
	
	public function Get($field){
		return isset($_POST[$this->_form_id . "_" . $field]) ? $_POST[$this->_form_id . "_" . $field] : NULL;
		}
	
	public static function CancelButton($options = []){
		$options = array_merge([ "text" => "Cancel", "class" => "btn-default" ], $options);
		return "<button type='button' class='btn {$options['class']}' data-dismiss='modal'>{$options['text']}</button>";
		}
	
	}
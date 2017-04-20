<?php namespace ChapmanRadio;
class Editor {
	
	private $model;
	private $table;
	private $field;
	
	public function __construct($model, $table){
		$this->model = $model;
		$this->table = $table;
		Template::AddBodyContent("<table>");
		}
	
	public function Each($fn){
		$otype = get_class($this->model);
		foreach($otype::$DataMap as $field => $dbfield) {
			$this->field = $field;
			$editor = $fn($field, $this);
			if($editor == null) continue;
			Template::AddBodyContent("<tr><td>$field</td><td><form method='post' action='javascript:query(\"{$this->table}\", {$this->model->id}, \"$dbfield\", $(\"#{$this->table}_{$this->model->id}_{$this->field}\").val())'><div>$editor<input type='submit' name='updatefield' value=' &gt; ' /></div></form></td></tr>");
			}
		}
	
	public function End(){
		Template::AddBodyContent("</table>");
		}
		
	public function None(){
		return NULL;
		}
	
	public function DropDown($options){
		$field = $this->field;
		$ret = "<select id='{$this->table}_{$this->model->id}_{$this->field}' style='width:300px;'>";
		foreach($options as $v => $t) $ret .= "<option value='{$v}' ".(($v == $this->model->$field)?"selected":"").">{$t}</option>";
		$ret .= "</select>";
		return $ret;
		}
		
	public function TrueFalse(){
		$field = $this->field;
		$ret = "<span style='width:300px; display: inline-block;'>
			<input type='hidden' id='{$this->table}_{$this->model->id}_{$this->field}' value='{$this->model->$field}' />
			<input type='radio' onchange='$(\"#{$this->table}_{$this->model->id}_{$this->field}\").val(1);' value='1' id='{$this->table}_{$this->model->id}_{$this->field}_1' name='{$this->table}_{$this->model->id}_{$this->field}' ".(($this->model->$field==1)?"checked":"")."/>
			<label for='{$this->table}_{$this->model->id}_{$this->field}_1'>True</label>
			<input type='radio' onchange='$(\"#{$this->table}_{$this->model->id}_{$this->field}\").val(0);' value='0' id='{$this->table}_{$this->model->id}_{$this->field}_0' name='{$this->table}_{$this->model->id}_{$this->field}' ".(($this->model->$field==0)?"checked":"")."/>
			<label for='{$this->table}_{$this->model->id}_{$this->field}_0'>False</label>
			</span>";
		return $ret;
		}
		
	public function Disabled(){
		$field = $this->field;
		return "<input disabled type='text' id='{$this->table}_{$this->model->id}_{$this->field}' style='width:300px;' value=\"".htmlentities($this->model->$field)."\" />";
		}
		
	public function Text(){
		$field = $this->field;
		return "<input type='text' id='{$this->table}_{$this->model->id}_{$this->field}' style='width:300px;' value=\"".htmlentities($this->model->$field)."\" />";
		}
		
	public function Textarea(){
		$field = $this->field;
		return "<textarea id='{$this->table}_{$this->model->id}_{$this->field}' style='resize: vertical; width:300px; height: 75px;'>".htmlentities($this->model->$field)."</textarea>";
		}
	
	}
<?php namespace ChapmanRadio;

class NewsModel extends \Sinopia\BaseModel {
	
	public $title;
	public $body;
	public $postedby;
	public $posted;
	public $expires;
	
	public $status;
	public $expires_unix;
	public $posted_unix;
	
	public static $DataTable = "v_news_active";
	public static $DataMap = [
		"id" => "news_id",
		"title" => "news_title",
		"body" => "news_body",
		"postedby" => "news_postedby",
		"posted" => "news_posted",
		"expires" => "news_expires"
		];
	
	public function __setup(){
	
		$this->posted_unix = strtotime($this->posted);
		$this->expires_unix = strtotime($this->expires);
		
		$this->status = "active";
		if($this->expires_unix !== FALSE && $this->expires_unix < time()) $this->status = "expired";
		if($this->posted_unix !== FALSE && $this->posted_unix > time()) $this->status = "pending";
		
		}
	}
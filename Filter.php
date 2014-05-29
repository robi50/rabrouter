<?php

namespace rabrouter;

class Filter{

	protected $controller = null;

	protected $denied = null;

	public static $filters = [];

	public static $filterGroups = [];

	public function __construct($controller, $denied = null){
		$this->controller = $controller;
		$this->denied = $denied;
	}

	public function hasFilterAccess($params = []){
		$check = call_user_func_array($this->controller, $params);

		if($check){
			return true;
		}else{
			if($this->denied){
				echo call_user_func_array($this->denied, $params);
				Handler::$match = true;
			}

			return false;
		}
	}

	public static function parsePattern($pattern){
		// replace group names with filters
		$pattern = preg_replace_callback('/\*([a-zA-Z0-9_-]+)/', function($matches){
			return self::$filterGroups[$matches[1]];
		}, $pattern);

		// explode pattern to filters
		return explode('|', preg_replace('/[\s]+/', '', $pattern));
	}

}
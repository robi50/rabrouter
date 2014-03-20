<?php

namespace Rab;

class Filter{

	protected $controller = null;

	protected $denied = null;

	public static $filters = [];

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

}
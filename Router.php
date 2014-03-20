<?php

namespace Rab;

require 'Request.php';
require 'Pattern.php';
require 'Filter.php';
require 'Handler.php';

class Router{

	public static function handle($pattern, $handler = null){
		if(is_array($pattern)){
			foreach($pattern as $p => $h) new Handler($p, $h);
		}else{
			return new Handler($pattern, $handler);
		}
	}

	public static function where(){
		call_user_func_array('\Rab\Pattern::define', func_get_args());
	}

	public static function filter($name, $controller, $denied = null){
		if($name[0] == '*'){
			Filter::$filterGroups[substr($name, 1)] = $controller;
		}else{
			Filter::$filters[$name] = [$controller, $denied];
		}
	}	

}
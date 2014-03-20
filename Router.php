<?php

namespace Rab;

require 'Request.php';
require 'Pattern.php';
require 'Filter.php';
require 'Handler.php';

class Router{

	/**
	 * Define router handler.
	 *
	 * @param string $pattern // router pattern
	 * @param mixin $handler // router handler
	 *
	 * @return object Handler
	 */
	public static function handle($pattern, $handler = null){
		if(is_array($pattern)){
			foreach($pattern as $p => $h) new Handler($p, $h);
		}else{
			return new Handler($pattern, $handler);
		}
	}

	/**
	 * Abstract Pattern::define method.
	 * 
	 * @return void
	 */
	public static function where(){
		call_user_func_array('\Rab\Pattern::define', func_get_args());
	}

	/**
	 * Define router filter or filter group. 	
	 *
	 * @param string $name // filter or filter group name
	 * @param string $controller // filter group pattern
	 * @param function $controller // filter controller function
	 * @param function $denied // filter denied function
	 *
	 * @return void
	 */
	public static function filter($name, $controller, $denied = null){
		// is filter group name
		if($name[0] == '*'){
			// push filter pattern
			Filter::$filterGroups[substr($name, 1)] = $controller;
		}else{
			Filter::$filters[$name] = [$controller, $denied];
		}
	}	

}
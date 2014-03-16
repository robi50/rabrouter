<?php

namespace Rab;

class Pattern{

	private $pattern = '';

	private $rules = [];

	private static $patterns = [];

	public function __construct($pattern){
		$this->pattern = trim($pattern, '/');
	}

	public function toRegex(){
		$regex = $this->pattern;

		foreach($this->rules as $n => $r){
			$regex = preg_replace('/\:'.$n.'/', '('.$r.')', $regex);
		}

		foreach(self::$patterns as $n => $r){
			$regex = preg_replace('/\:'.$n.'/', '('.$r.')', $regex);
		}

		$regex = preg_replace('/\:[a-zA-Z0-9]+\?/', '{0,}([a-zA-Z0-9_-\s]{0,})', $regex);
		$regex = preg_replace('/\:[a-zA-Z0-9]+/', '([a-zA-Z0-9_-\s]+)', $regex);
		$regex = preg_replace('/\//', '\/', $regex);

		return $regex;
	}

	public function toString(){
		return $this->pattern;
	}

	public function getParams(){
		preg_match_all('/'. $this->toRegex() .'/', Request::get(), $matches);

		$params = [];
		$t = array_slice($matches, 1);

		foreach($t as $p) $params = array_merge($params, $p);

		for($i = 0; $i < count($params); $i++) if(strlen($params[$i]) == 0) array_splice($params, $i, 1);

		return $params;
	}

	public function hasMatch(){
		return preg_match_all('/^'. $this->toRegex() .'$/', Request::get(), $matches);
	}

	public function rule($name, $pattern = ''){
		if(is_array($name)){
			foreach($name as $n => $p) $this->rules[$n] = $p;
		}else{
			$this->rules[$name] = $pattern; 
		}
	}

	public static function define($name, $pattern = ''){
		if(is_array($name)){
			foreach($name as $n => $p) self::$patterns[$n] = $p;
		}else{
			self::$patterns[$name] = $pattern;
		}
	}

}
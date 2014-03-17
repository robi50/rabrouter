<?php

namespace Rab;

class Pattern{

	private $pattern = '';

	private $regex = '';

	private $rules = [];

	private $requestParams = [

		'GET' => [],

		'POST' => []

	];

	private static $patterns = [];

	private static $flags = [

		'digit' => '[0-9]+',

		'alpha' => '[a-zA-Z]+',

		'alphanum' => '[a-zA-Z0-9]+',

		'any' => '[^/]+',

		'every' => '.*'

	];

	public function __construct($pattern){
		$this->pattern = trim($pattern, '/');
		$this->regex = $this->toRegex();
		$this->parseRequestParams();
	}

	public function toRegex(){
		$regex = $this->pattern;

		foreach(self::$flags as $n => $r){
			$regex = preg_replace('/\:'.$n.'\?/', '{0,}('.$r.'){0,}', $regex);
			$regex = preg_replace('/\:'.$n.'\b/', '('.$r.')', $regex);
		}

		foreach($this->rules as $n => $r){
			$regex = preg_replace('/\:'.$n.'\?/', '{0,}('.$r.'){0,}', $regex);
			$regex = preg_replace('/\:'.$n.'\b/', '('.$r.')', $regex);
		}

		foreach(self::$patterns as $n => $r){
			$regex = preg_replace('/\:'.$n.'\?/', '{0,}('.$r.'){0,}', $regex);
			$regex = preg_replace('/\:'.$n.'\b/', '('.$r.')', $regex);
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
		preg_match_all('/'. $this->regex .'/', Request::get(), $matches);

		$params = [];
		$t = array_slice($matches, 1);

		foreach($t as $p) $params = array_merge($params, $p);

		for($i = 0; $i < count($params); $i++) if(strlen($params[$i]) == 0) array_splice($params, $i, 1);

		return $params;
	}

	public function hasMatch(){
		return preg_match_all('/^'. $this->regex .'$/', Request::get(), $matches);
	}

	public function rule($name, $pattern = ''){
		if(is_array($name)){
			foreach($name as $n => $p) $this->rules[$n] = $p;
		}else{
			$this->rules[$name] = $pattern; 
		}
	}

	public function parseRequestParams(){
		$regex = '/\[(GET|POST)\|(.*?)\]/';

		if(preg_match_all($regex, $this->pattern, $matches)){
			for($i = 0; $i < count($matches[1]); $i++){
				parse_str($matches[2][$i], $params);

				$this->requestParams[$matches[1][$i]] = array_merge($this->requestParams[$matches[1][$i]], $params);
			}

			$this->pattern  = preg_replace($regex, '', $this->pattern);
		}
	}

	public function hasRequestParamsMatch(){
		foreach($this->requestParams['GET'] as $k => $v) if(!isset($_GET[$k]) || $_GET[$k] != $v) return false;
		foreach($this->requestParams['POST'] as $k => $v) if(!isset($_POST[$k]) || $_POST[$k] != $v) return false;

		return true;
	}

	public static function define($name, $pattern = ''){
		if(is_array($name)){
			foreach($name as $n => $p) self::$patterns[$n] = $p;
		}else{
			self::$patterns[$name] = $pattern;
		}
	}

}



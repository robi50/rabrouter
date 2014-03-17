<?php

namespace Rab;

class Pattern{

	private $pattern = '';

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
		$this->pattern = $pattern;
		$this->parseRequestParams();
		$this->pattern = trim($this->pattern, '/');
	}

	public function toRegex($pattern = null){
		$regex = $pattern ? $pattern : $this->pattern;

		$regex = $this->parseFlags($regex);

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
		foreach($this->requestParams['GET'] as $k => $v) if(!isset($_GET[$k]) || !preg_match('/^'.$this->toRegex($v).'$/', $_GET[$k])) return false;
		foreach($this->requestParams['POST'] as $k => $v) if(!isset($_POST[$k]) || !preg_match('/^'.$this->toRegex($v).'$/', $_POST[$k])) return false;

		return true;
	}

	private function parseFlags($pattern){
		foreach(self::$flags as $n => $r){
			$pattern = preg_replace('/\:'.$n.'\?/', '{0,}('.$r.'){0,}', $pattern);
			$pattern = preg_replace('/\:'.$n.'\b/', '('.$r.')', $pattern);
		}

		foreach($this->rules as $n => $r){
			$pattern = preg_replace('/\:'.$n.'\?/', '{0,}('.$r.'){0,}', $pattern);
			$pattern = preg_replace('/\:'.$n.'\b/', '('.$r.')', $pattern);
		}

		foreach(self::$patterns as $n => $r){
			$pattern = preg_replace('/\:'.$n.'\?/', '{0,}('.$r.'){0,}', $pattern);
			$pattern = preg_replace('/\:'.$n.'\b/', '('.$r.')', $pattern);
		}

		return $pattern;
	}

	public static function define($name, $pattern = ''){
		if(is_array($name)){
			foreach($name as $n => $p) self::$patterns[$n] = $p;
		}else{
			self::$patterns[$name] = $pattern;
		}
	}

}



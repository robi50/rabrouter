<?php

namespace rabrouter;

class Pattern{

	// Pattern 
	private $pattern = '';

	// Local pattern flags
	private $rules = [];

	// Pattern param buffer
	private $params = null;

	// GET, POST request params
	private $requestParams = [

		'GET' => [],

		'POST' => []

	];

	// Global flags
	private static $patterns = [];

	// Native flags
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

	/**
	 * Parse pattern to regex.
	 *
	 * @return string
	 */
	public function toRegex($pattern = null){
		$regex = $pattern ? $pattern : $this->pattern;

		$regex = $this->parseFlags($regex);

		$regex = preg_replace('/\:[a-zA-Z0-9]+\?/', '{0,}([a-zA-Z0-9_-\s]{0,})', $regex);
		$regex = preg_replace('/\:[a-zA-Z0-9]+/', '([a-zA-Z0-9_-\s]+)', $regex);
		$regex = preg_replace('/\//', '\/', $regex);

		return $regex;
	}

	/**
	 * Get pattern.
	 *
	 * @return void
	 */
	public function toString(){
		return $this->pattern;
	}

	public function getParams(){
		if(!$this->params){
			preg_match_all('/'. $this->toRegex() .'/', Request::get(), $matches);

			$params = [];
			$t = array_slice($matches, 1);

			foreach($t as $p) $params = array_merge($params, $p);

			for($i = 0; $i < count($params); $i++) if(strlen($params[$i]) == 0) array_splice($params, $i, 1);

			$this->params = $params;
		}

		return $this->params;
	}

	/**
	 * Check request and pattern are matched.
	 *
	 * @return boolean
	 */
	public function hasMatch(){
		return preg_match_all('/^'. $this->toRegex() .'$/', Request::get(), $matches);
	}

	/**
	 * Define local pattern flag.
	 *
	 * @param string $name // Flag name
	 * @param string $pattern // Flag pattern
	 *
	 * @param array $name // List of flag defines
	 *
	 * @return void
	 */
	public function rule($name, $pattern = ''){
		if(is_array($name)){
			foreach($name as $n => $p) $this->rules[$n] = $p;
		}else{
			$this->rules[$name] = $pattern; 
		}
	}

	/**
	 * Parse GET, POST request parameters.
	 *
	 * @return void 
	 */
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

	/**
	 * Parse flags like ':digit', ':alpha', ':foo' to values which is regex.
	 * 
	 * @param string $pattern // Target pattern
	 *
	 * @return void
	 */
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

	/**
	 * Define global pattern flag.
	 *
	 * @param string $name // Flag name
	 * @param string $pattern // Flag pattern
	 *
	 * @param array $name // List of flag defines
	 *
	 * @return void
	 */
	public static function define($name, $pattern = ''){
		if(is_array($name)){
			foreach($name as $n => $p) self::$patterns[$n] = $p;
		}else{
			self::$patterns[$name] = $pattern;
		}
	}

}



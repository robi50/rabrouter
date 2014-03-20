<?php

namespace Rab;

class Handler{

	private $pattern = null;

	private $handler = null;

	private $rules = [];

	private $filters = [

		'before' => [],

		'after' => []

	];

	public static $match = false;

	public function __construct($pattern, $handler){
		$this->pattern = new Pattern($pattern);
		$this->handler = $handler;
	}

	public function __destruct(){
		if($this->pattern->hasMatch() && $this->pattern->hasRequestParamsMatch() && !self::$match && $this->checkBeforeFilters()){
			$this->handle();
			self::$match = true;

			$this->checkAfterFilters();
		}
	}

	public function where(){
		call_user_func_array([$this->pattern, 'rule'], func_get_args());

		return $this;
	}

	public function filter($type, $controller, $denied = null){
		// Is filter pattern
		if(is_string($controller)){
			// Parse pattern. get filter names
			$fs = Filter::parsePattern($controller);

			// Filter name loop
			foreach($fs as $f){
				$filter = Filter::$filters[$f];
				// add filter to current handler
				$this->filters[$type][] = new Filter($filter[0], $filter[1]);
			}
		}
		// Is filter controller
		elseif(is_callable($controller)){
			$this->filters[$type][] = new Filter($controller, $denied);
		}
		// Is Filter object
		elseif($controller instanceof Filter){
			$this->filters[$type][] = $controller;
		}

		return $this;
	}

	private function checkBeforeFilters(){
		foreach($this->filters['before'] as $f){
			if(!$f->hasFilterAccess($this->pattern->getParams())) return false;
		}

		return true;
	}

	private function checkAfterFilters(){
		foreach($this->filters['after'] as $f) $f->hasFilterAccess($this->pattern->getParams());
	}

	private function handle(){
		if(is_callable($this->handler)){
			echo call_user_func_array($this->handler, $this->pattern->getParams());
		}elseif(is_string($this->handler)){
			$p = explode('@', $this->handler);

			$this->handleWithObject(new $p[0], count($p) > 1 ? $p[1] : null);
		}elseif(is_object($this->handler)){
			$this->handleWithObject($this->handler);
		}
	}

	private function handleWithObject($h, $m = null){
		$m = $m ? $m : ($_POST ? 'post' : 'get');

		if(method_exists($h, $m)) echo call_user_func_array([$h, $m], $this->pattern->getParams());
	}

}
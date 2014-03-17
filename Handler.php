<?php

namespace Rab;

class Handler{

	private $pattern = null;

	private $handler = null;

	private $rules = [];

	private static $match = false;

	public function __construct($pattern, $handler){
		$this->pattern = new Pattern($pattern);
		$this->handler = $handler;
	}

	public function __destruct(){
		if($this->pattern->hasMatch() && $this->pattern->hasRequestParamsMatch() && !self::$match){
			$this->handle();
			self::$match = true;
		}
	}

	public function where(){
		call_user_func_array([$this->pattern, 'rule'], func_get_args());

		return $this;
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
		$m = $_POST ? 'post' : 'get';

		if(method_exists($h, $m)) echo call_user_func_array([$h, $m], $this->pattern->getParams());
	}

}
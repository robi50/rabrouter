<?php

namespace Rab;

class Handler{

	private $pattern = null;

	private $handler = null;

	private $rules = [];

	public function __construct($pattern, $handler){
		$this->pattern = new Pattern($pattern);
		$this->handler = $handler;
	}

	public function __destruct(){
		if($this->pattern->hasMatch()) $this->handle();
	}

	public function where(){
		call_user_func_array([$this->pattern, 'rule'], func_get_args());

		return $this;
	}

	private function handle(){
		if(is_callable($this->handler)){
			echo call_user_func_array($this->handler, $this->pattern->getParams());
		}elseif(is_string($this->handler) && class_exists($this->handler)){
			$this->handleWithObject(new $this->handler);
		}elseif(is_object($this->handler)){
			$this->handleWithObject($this->handler);
		}
	}

	private function handleWithObject($h){
		echo call_user_func_array([$h, $_POST ? 'post' : 'get'], $this->pattern->getParams());
	}

}
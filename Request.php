<?php

namespace rabrouter;

class Request{

	public static function get(){
		return isset($_GET['request']) ? rtrim($_GET['request'], '/') : null;
	}

}
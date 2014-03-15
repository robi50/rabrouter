<?php

namespace Rab;

require 'Request.php';
require 'Pattern.php';
require 'Handler.php';

class Router{

	public static function handle($pattern, $handler){
		return new Handler($pattern, $handler);
	}

}
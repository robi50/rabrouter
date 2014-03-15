<?php

namespace Rab;

require 'Request.php';
require 'Pattern.php';
require 'Handler.php';

class Router{

	public static function handle($pattern, $handler = null){
		if(is_array($pattern)){
			foreach($pattern as $p => $h) new Handler($p, $h);
		}else{
			return new Handler($pattern, $handler);
		}
	}

	public static function where(){
		call_user_func_array('\Rab\Pattern::define', func_get_args());
	}

}
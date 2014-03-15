<?php

require '../Router.php';

use \Rab\Router;

Router::handle('hello/:name', function($name){
	return 'hello ' . $name;
});
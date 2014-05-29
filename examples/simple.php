<?php

require '../Router.php';

use \rabrouter\Router;

Router::handle('hello/:name', function($name){
	return 'hello ' . $name;
});
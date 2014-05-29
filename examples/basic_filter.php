<?php

require '../Router.php';

use \rabrouter\Router;

// parameter $id type must be number
Router::filter('NumberTypeFilter', function($id){
	return preg_match('/^[0-9]+$/', $id);
}, function($id){
	return '[NumberTypeFilter]: parameter must be number: ' . $id;
});

// parameter $id size must be between 5 and 50
Router::filter('NumberSizeFilter', function($id){
	$id = intval($id);

	return ($id > 5) && ($id < 50);
}, function($id){
	return '[NumberSizeFilter]: number must be between 5 and 50: ' . $id; 
});

// check NumberTypeFilter and NumberSizeFilter before handle
Router::handle('number/:any', function($id){
	return 'number is: ' . $id;
})->filter('before', 'NumberTypeFilter | NumberSizeFilter');
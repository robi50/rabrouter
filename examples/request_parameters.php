<?php

require '../Router.php';

use \rabrouter\Router;

Router::handle('watch[GET|v=:digit]', function(){
	return 'video id is: ' . $_GET['v'];
});

Router::handle('book[POST|key=apple]', function(){
	return 'book says: hello world!';
});

Router::handle('book', function(){
	return 'this is secret book. you must POST the key for read.<br />'.
	'<form action="" method="POST"><input type="text" name="key" value="apple" /><button>enter!</button></form>';
});
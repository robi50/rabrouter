<?php

require '../Router.php';

use \Rab\Router;

class Users{

	private $users = [
			
		'2' => [
			'username' => 'robin'
		],

		'3' => [
			'username' => 'soykan'
		],

		'8' => [
			'username' => 'artan'
		]

	];

	public function home(){
		return 'these are users: ' . implode(',', array_keys($this->users));
	}

	public function info($id){
		if(isset($this->users[$id])){
			print_r($this->users[$id]);
		}else{
			return sprintf('there is no user with this id: `%s`.', $id);
		}
	}

	public function none($req){
		return sprintf('there is no page like that: `%s`.', $req);
	}

}

Router::handle('/', function(){
	return 'this is homepage!';
});

Router::handle('users', 'Users@home');

Router::handle('users/:digit', 'Users@info');

Router::handle(':every', 'Users@none');
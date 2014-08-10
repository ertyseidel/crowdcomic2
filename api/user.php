<?php

class User {

	//dependencies
	private $api;
	private $db;

	//create
	public $username;

	//get
	public $email;
	public $pk_user_id;
	public $created_time;

	//constructor
	public function __construct($api, $db) {
		$this->api = $api;
		$this->db = $db;
	}

	//create
	//[]
	public function populate($data, $fields = []) {
		if (empty($data)) return false;
		if (empty($data->username)) return false;

		$this->username = $data->username;

		$this->email = empty($data->email) ? '' : $data->email;
		$this->pk_user_id = empty($data->pk_user_id) ? 0 : $data->pk_user_id;
		$this->created_time = empty($data->created_time) ? 0 : $data->created_time;

		return true;
	}

	//get
	public function populateByLogin($username, $password) {
		$user = $this->db->getObject(
			'SELECT pk_user_id, username, password, email, created_time FROM users
			WHERE username = :username',
			[
				'username' => $username
			]
		);
		if (!$user) return false;
		if (crypt($password, $user->password) != $user->password) return false;
		unset($user->password);
		$this->populate($user);
		return $this;
	}

	public function populateById($pk_user_id) {
		return $this->populate(
			$this->db->getObject(
				'SELECT pk_user_id, username FROM users WHERE pk_user_id = :pk_user_id',
				[
					'pk_user_id' => $pk_user_id
				]
			)
		);
	}

	//save
	public function save($new_password) {
		if (empty($this->username)) return false;
		if (empty($new_password)) return false;

		$crypt_password = crypt($new_password);
		$email = empty($this->email) ? '' : $this->email;

		return $this->db->query(
			'INSERT INTO users (username, email, password)
			VALUES (:username, :email, :password)',
			[
				'username' => $this->username,
				'email' => $email,
				'password' => $crypt_password
			]
		);
	}
}

//api/user/get/:pk_user_id
$api->registerFunction('user_get', function($args) use ($api, $db) {
	$user = new User($api, $db);
	if (!$user->populateById($args['pk_user_id'])) {
		return new Response(
			Response::ERROR,
			'Could not find user with id ' . (int)$args['pk_user_id']
		);
	}
	return new Response(
		Response::SUCCESS,
		$user
	);
}, ['pk_user_id']);

$api->registerFunction('user_login', function($args) use ($api, $db) {
	$user = new User($api, $db);
	if(!$user->populateByLogin($args['username'], $args['password'])) {
		return new Response(
			Response::ERROR,
			'Incorrect username or password'
		);
	}
	$_SESSION['user_id'] = $user->pk_user_id;
	$_SESSION['username'] = $user->username;
	return new Response(
		Response::SUCCESS,
		$user
	);
}, ['username', 'password']);

$api->registerFunction('user_register', function($args) use ($api, $db) {
	$user = new User($api, $db);
	$user->populate(Api::toObject([
		'username' => $args['username'],
		'email' => $args['email'],
	]));
	if(!$user->save($args['password'])) {
		return new Response(
			Response::ERROR,
			'Could not save user (perhaps that username is already taken?)'
		);
	}
	$new_user = new User($api, $db);
	if(!$new_user->populateByLogin($args['username'], $args['password'])){
		return new Response(
			Response::ERROR,
			'Please refresh and try logging in again'
		);
	};
	$_SESSION['user_id'] = $new_user->pk_user_id;
	return new Response(
		Response::SUCCESS,
		$new_user
	);
}, ['username', 'email', 'password']);

$api->registerFunction('user_logout', function($args) {
	unset($_SESSION['user_id']);
	session_unset('user_id');
	session_destroy();
	return new Response(
		Response::SUCCESS,
		''
	);
}, []);

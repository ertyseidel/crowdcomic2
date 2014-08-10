<?php
session_start();
date_default_timezone_set("America/New_York");
if (!file_exists('./settings.php')) {
	die('You need to create a database and a settings.php file!');
}

class API {
	private $api = [];
	private $apiArgs = [];

	public function registerFunction($name, $func, $args = []) {
		$this->apiArgs[$name] = $args;
		$this->api[$name] = $func;
	}

	public function callFunction($name, $get) {
		$args = [];
		foreach ($this->apiArgs[$name] as $arg) {
			if (!empty($get[$arg])) {
				$args[$arg] = $get[$arg];
			} else {
				$args[$arg] = false;
			}
		}
		return call_user_func($this->api[$name], $args);
	}

	static function toObject($arr) {
		return json_decode(json_encode($arr), FALSE);
	}
}

require_once('./settings.php');
require_once('./db.php');
require_once('./response.php');

$api = new API();
$db = new DBO($db_host, $db_name, $db_username, $db_password);

require_once('./vote.php');
require_once('./comic.php');
require_once('./user.php');
require_once('./post.php');
require_once('./post.question.php');
require_once('./post.suggestion.php');
require_once('./post.comment.php');

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$object = isset($_REQUEST['object']) ? $_REQUEST['object'] : '';

$result = $api->callFunction($object . '_' . $action, $_REQUEST);

if ($result instanceof Response) {
	echo $result;
} else {
	echo "not a response object";
}

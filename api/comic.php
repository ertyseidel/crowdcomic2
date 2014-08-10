<?php

class Comic{

	//dependencies
	private $api;
	private $db;

	//create
	public $user_id;
	public $text;

	//get
	public $pk_comic_id;
	public $origin_suggestion_id;
	public $unlock_time;
	public $lock_time;
	public $is_locked;
	public $suggestions;
	public $comments;
	public $questions;

	public function __construct($api, $db) {
		$this->api = $api;
		$this->db = $db;

		$this->posts = array();
	}

	//create
	//['user', 'posts', 'parts', 'origin_suggestion']
	public function populate($data, $fields = []) {
		if (empty($data->user_id)) return false;
		if (empty($data->text)) return false;

		$time = time();
		if (isset($data->unlock_time) && $data->unlock_time > $time) return false;

		$this->user_id = $data->user_id;
		$this->text = $data->text;

		$this->pk_comic_id = empty($data->pk_comic_id) ? 0 : $data->pk_comic_id;
		$this->created_time = empty($data->created_time) ? 0 : $data->created_time;
		$this->origin_suggestion_id = empty($data->origin_suggestion_id) ? 0 : $data->origin_suggestion_id;
		$this->unlock_time = empty($data->unlock_time) ? 0 : strtotime($data->unlock_time);
		$this->lock_time = empty($data->lock_time) ? 0 : strtotime($data->lock_time);

		$this->is_locked = $time < $this->unlock_time || $time > $this->lock_time;

		if (in_array('user', $fields)) {
			$this->user = $this->getUser();
		}

		$get_posts = in_array('posts', $fields);
		if ($get_posts || in_array('suggestions', $fields)) {
			$this->suggestions = $this->getPosts('suggestions');
		}
		if ($get_posts || in_array('comments', $fields)) {
			$this->comments = $this->getPosts('comments');
		}
		if ($get_posts || in_array('questions', $fields)) {
			$this->questions = $this->getPosts('questions');
		}

		if (in_array('parts', $fields)) {
			$this->parts = $this->getParts();
		}

		if (in_array('origin_suggestion', $fields)) {
			if ($this->origin_suggestion_id) {
				$this->origin_suggestion = $this->getOriginSuggestion();
				if ($this->origin_suggestion) {
					$this->origin_suggestion->is_origin_suggestion = true;
				}
			}
		}

		if (in_array('max_id', $fields)) {
			$this->max_id = $this->getMaxId();
		}

		return $this;
	}

	//get
	public function populateById($pk_comic_id) {
		return $this->populate(
			$this->db->getObject(
				'SELECT * FROM comics WHERE pk_comic_id = :pk_comic_id',
				[
					'pk_comic_id' => $pk_comic_id
				]
			),
			['user', 'posts', 'parts', 'origin_suggestion', 'max_id']
		);
	}

	public function populateWithLastComic() {
		return $this->populateById($this->getMaxId());
	}

	//lazy object accessors
	private function getUser() {
		if (!$this->user_id) return false;
		$u = new User($this->api, $this->db);
		$u->populateById($this->user_id);
		return $u;
	}

	private function getParts() {
		if (!$this->pk_comic_id) return false;
		return $this->db->getObjects(
			'SELECT * FROM parts WHERE comic_id = :comic_id',
			[
				'comic_id' => $this->pk_comic_id
			]
		);
	}

	private function getOriginSuggestion() {
		if (!$this->origin_suggestion_id) return false;
		$c = new Suggestion($this->api, $this->db);
		$c->populateById($this->origin_suggestion_id);
		return $c;
	}

	private function getPosts($type = false) {
		if (!$this->pk_comic_id) return false;
		if (empty($this->posts)) {
			$post_data = $this->db->getObjects(
				'SELECT * FROM posts WHERE comic_id = :comic_id',
				[
					'comic_id' => $this->pk_comic_id
				]
			);
			$posts = [];
			foreach ($post_data as $post) {
				switch ($post->type) {
					case 'suggestion' :
						$c = new Suggestion($this->api, $this->db);
						break;
					case 'question':
						$c = new Question($this->api, $this->db);
						break;
					case 'comment':
						$c = new Question($this->api, $this->db);
						break;
					default:
						throw new Exception('Unknown Post Type!' . htmlentities($post->type));
				}
				$c->populate($post, ['user']);
				$posts[] = $c;
			}
			$this->posts = $posts;
		}
		if ($type) {
			$typed_posts = array();
			foreach($this->posts as $post) {
				if ($post->type == $type) {
					$typed_posts[] = $post;
				}
			}
			return $typed_posts;
		}
		return $this->posts;
	}

	public function getMaxId() {
		return $this->db->getColumn(
			'SELECT MAX(pk_comic_id) FROM comics WHERE unlock_time < NOW()'
		);
	}
}

$api->registerFunction('comic_get', function($args) use ($api, $db) {
	$comic = new Comic($api, $db);
	if ($args['pk_comic_id']) {
		if (!$comic->populateById($args['pk_comic_id'])) {
			return new Response(
				Response::ERROR,
				'Could not find comic with id ' . (int)$args['pk_comic_id']
			);
		}
	} else {
		if (!$comic->populateWithLastComic()) {
			return new Response(
				Response::ERROR,
				'Could not get the last comic'
			);
		}
	}
	return new Response(
		Response::SUCCESS,
		$comic
	);
}, ['pk_comic_id']);

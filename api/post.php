<?php

class Post {

	//dependencies
	private $api;
	private $db;

	//create
	public $user_id;
	public $comic_id;
	public $text;
	public $type;

	//get
	public $pk_post_id;
	public $is_approved;
	public $is_deleted;
	public $has_viewer_vote;
	public $user;
	public $comic;

	//constructor
	public function __construct($api, $db) {
		$this->api = $api;
		$this->db = $db;
	}

	//lazy object accessors
	public function getUser() {
		if (!$this->user_id) return false;
		$u = new User($this->api, $this->db);
		$u->populateById($this->user_id);
		return $u;
	}

	public function getComic() {
		if (!$this->comic_id) return false;
		$c = new Comic($this->api, $this->db);
		$c->populateById($this->comic_id);
		return $c;
	}

	public function getHasViewerVote() {
		if (empty($_SESSION['user_id'])) return false;
		$vote = new VoteManager($this->api, $this->db);
		$vote->setPost($this);
		return $vote->getViewerVote($_SESSION['user_id']);
	}

	//create
	//['user', 'comic']
	public function populate($data, $fields = []) {
		if (empty($data)) return false;
		if (empty($data->user_id)) return false;
		if (empty($data->comic_id)) return false;
		if (empty($data->text)) return false;
		if (empty($data->type)) return false;

		$this->user_id = $data->user_id;
		$this->comic_id = $data->comic_id;
		$this->text = $data->text;
		$this->type = $data->type;

		$this->pk_post_id = empty($data->pk_post_id) ? 0 : $data->pk_post_id;
		$this->created_time = empty($data->created_time) ? 0 : $data->created_time;
		$this->is_approved = empty($data->is_approved) ? 0 : $data->is_approved;
		$this->is_deleted = empty($data->is_deleted) ? 0 : $data->is_deleted;

		if (in_array('user', $fields)) {
			$this->user = $this->getUser();
		}

		if (in_array('comic', $fields)) {
			$this->comic = $this->getComic();
		}

		$this->has_viewer_vote = $this->getHasViewerVote() || (isset($_SESSION['user_id']) && $this->user_id == $_SESSION['user_id']);

		return $this;
	}

	//get
	public function populateById($pk_post_id) {
		if (!$pk_post_id) return false;
		return $this->populate(
			$this->db->getObject(
				'SELECT * FROM posts WHERE pk_post_id = :pk_post_id',
				[
					'pk_post_id' => $pk_post_id
				]
			),
			['user', 'comic']
		);
	}

	public function populateByUserAndComic($user_id, $comic_id) {
		if (!$user_id) return false;
		if (!$comic_id) return false;
		return $this->populate(
			$this->db->getObject(
				'SELECT * FROM posts WHERE user_id = :user_id AND comic_id = :comic_id AND NOT is_deleted',
				[
					'user_id' => $user_id,
					'comic_id' => $comic_id
				]
			),
			['user']
		);
	}

	//save
	public function save() {
		if (empty($this->user_id)) return false;
		if (empty($this->comic_id)) return false;
		if (empty($this->text)) return false;

		$args = [
			'user_id' => $this->user_id,
			'comic_id' => $this->comic_id,
			'text' => $this->text,
			'created_time' => $this->created_time,
			'is_approved' => $this->is_approved,
			'is_deleted' => $this->is_deleted,
		];

		if (empty($pk_post_id)) {

			return $this->db->query(
				'INSERT INTO posts
				(user_id, comic_id, text, created_time, is_approved, is_deleted)
				values (
					:user_id,
					:comic_id,
					:text,
					IF( :created_time, :created_time, DEFAULT(created_time)),
					IF( :is_approved, :is_approved, DEFAULT(is_approved)),
					IF( :is_deleted, :is_deleted, DEFAULT(is_deleted))
				)',
				$args
			);
		} else {
			$args['pk_post_id'] = $this->pk_post_id;
			return $this->db->query(
				'UPDATE posts SET
				user_id = :user_id,
				comic_id = :comic_id,
				text = :text,
				IF( :created_time, :created_time, DEFAULT(created_time)),
				IF( :is_approved, :is_approved, DEFAULT(is_approved)),
				IF( :is_deleted, :is_deleted, DEFAULT(is_deleted))
				WHERE pk_post_id = :pk_post_id',
				$args
			);
		}
	}

	//delete
	public function delete() {
		if ($this->user_id != $_SESSION['user_id']) return false;
		if (empty($this->pk_post_id)) return false;
		return $this->db->query(
			'DELETE FROM posts
			WHERE pk_post_id = :pk_post_id',
			['pk_post_id' => $this->pk_post_id]
		);
	}
}

$api->registerFunction('post_get', function($args) use ($api, $db){
	$cmd = new Post($api, $db);
	if (!$cmd->populateById($args['pk_post_id'])) {
		return new Response(
			Response::ERROR,
			'Could not find post with ID ' . (int)$args['pk_post_id']
		);
	}
	return $cmd;
}, ['pk_post_id']);

$api->registerFunction('post_post', function($args) use ($api, $db) {
	if (!isset($_SESSION['user_id'])) return false;
	$args = Api::toObject($args);
	$args->user_id = $_SESSION['user_id'];

	$cmd = new Post($api, $db);
	$cmd->populate($args, ['comic']);

	if ($cmd->comic->is_locked) {
		return new Response(
			Response::ERROR,
			'Cannot make a suggestion after the comic is locked'
		);
	}

	$saved = $cmd->save();
	return new Response(
		$saved ? Response::SUCCESS : Response::ERROR,
		$saved ? $cmd : 'Could not save post'
	);
}, ['user_id', 'comic_id', 'text']);

$api->registerFunction('post_delete', function($args) use ($api, $db) {
	$cmd = new Post();
	$cmd->populateById($args['pk_post_id']);
	$deleted = $cmd->delete();
	return new Response(
		$deleted ? Response::SUCCESS : Response::ERROR,
		''
	);
}, ['pk_post_id']);

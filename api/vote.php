<?php

class VoteManager {

	private $api;
	private $db;

	private $cmd;

	public function __construct($api, $db) {
		$this->api = $api;
		$this->db = $db;
	}

	public function setPost($cmd) {
		$this->cmd = $cmd;
	}

	public function getViewerVote($user_id) {
		return $this->db->getColumn(
			'SELECT count(*) FROM votes WHERE post_id = :post_id AND user_id = :user_id',
			[
				'post_id' => $this->cmd->pk_post_id,
				'user_id' => $user_id
			]
		);
	}

	public function getVoteCount() {
		return $this->db->getColumn(
			'SELECT count(*) FROM votes WHERE post_id = :post_id',
			[
				'post_id' => $this->cmd->pk_post_id,
			]
		);
	}

	public function clearUserVoteFromComic($user_id) {
		return $this->db->query(
			'DELETE FROM votes
			WHERE votes.user_id = :user_id
			AND votes.post_id
			IN (SELECT pk_post_id FROM (
				SELECT pk_post_id
				FROM votes
				LEFT JOIN posts
				ON posts.pk_post_id = votes.post_id
				WHERE posts.comic_id = :comic_id
				AND votes.user_id = :user_id
			) as cid)',
			[
				'user_id' => $user_id,
				'comic_id' => $this->cmd->comic_id
			]
		);
	}

	public function togglePostVote($user_id) {
		if ($this->getViewerVote($user_id)) {
			if (!$this->db->query(
				'DELETE FROM votes WHERE post_id = :post_id',
				['post_id' => $this->cmd->pk_post_id]
			)) return false;
			return -1;
		}
		if (!$this->clearUserVoteFromComic($_SESSION['user_id'])) return false;
		if (!$this->db->query(
			'INSERT INTO votes (user_id, post_id) VALUES ( :user_id, :post_id)',
			[
				'user_id' => $user_id,
				'post_id' => $this->cmd->pk_post_id,
			]
		)) return false;
		return 1;
	}
}


$api->registerFunction('vote_post', function($args) use ($api, $db){
	if (empty($_SESSION['user_id'])) return false;
	$cmd = new Post($api, $db);
	if (!$cmd->populateById($args['pk_post_id'])) {
		return new Response(
			Response::ERROR,
			'Can\'t vote on a nonexistant post'
		);
	}
	if ($cmd->user_id == $_SESSION['user_id']) {
		return new Response(
			Response::ERROR,
			'Can\'t vote for your own comic (you do by default)'
		);
	}
	if ($cmd->comic->is_locked) {
		return new Response(
			Response::ERROR,
			'Can\'t vote on a locked comic'
		);
	}

	$user_post = new Post($api, $db);
	if ($user_post->populateByUserAndComic($_SESSION['user_id'], $cmd->comic_id)) {
		return new Response(
			Response::ERROR,
			'You can\'t vote on a comic you\'ve submitted a post for'
		);
	}

	$vm = new VoteManager($api, $db);
	$vm->setPost($cmd);
	$set_dir = $vm->togglePostVote($_SESSION['user_id']);
	if (!$set_dir) return new Response(
		Response::ERROR,
		'You found a bug! Email Erty please :)'
	);
	return new Response(
		Response::SUCCESS,
		$set_dir
	);
}, ['pk_post_id']);


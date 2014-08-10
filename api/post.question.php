<?php

class Question extends Post {

}

$api->registerFunction('question_get', function($args) use ($api, $db){
	$post = new Post($api, $db);
	if (!$post->populateById($args['pk_post_id'])) {
		return new Response(
			Response::ERROR,
			'Could not find question with ID ' . (int)$args['pk_post_id']
		);
	}
	if ($post->type != 'question') return new Response(
		Response::ERROR,
		'Found post with ID ' . (int)$args['pk_post_id'] . ' but it was not a question'
	);
	return new Response(
		Response::SUCCESS,
		$post
	);
}, ['pk_post_id']);

$api->registerFunction('question_post', function($args) use ($api, $db) {
	if (!isset($_SESSION['user_id'])) return false;
	$args = Api::toObject($args);
	$args->type = 'question';
	$args->user_id = $_SESSION['user_id'];

	$post = new Post($api, $db);
	$post->populate($args, ['comic']);

	if ($post->comic->is_locked) {
		return new Response(
			Response::ERROR,
			'Cannot make a question after the comic is locked'
		);
	}

	$saved = $post->save();
	return new Response(
		$saved ? Response::SUCCESS : Response::ERROR,
		$saved ? $post : 'Could not save question'
	);
}, ['user_id', 'comic_id', 'text']);

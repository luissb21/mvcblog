<?php

require_once(__DIR__."/../model/User.php");
require_once(__DIR__."/../model/UserMapper.php");

require_once(__DIR__."/../model/Post.php");
require_once(__DIR__."/../model/PostMapper.php");

require_once(__DIR__."/BaseRest.php");

/**
* Class PostRest
*
* It contains operations for creating, retrieving, updating, deleting and
* listing posts, as well as to create comments to posts.
*
* Methods gives responses following Restful standards. Methods of this class
* are intended to be mapped as callbacks using the URIDispatcher class.
*
*/
class PostRest extends BaseRest {
	private $postMapper;
	private $commentMapper;

	public function __construct() {
		parent::__construct();

		$this->postMapper = new PostMapper();
	}

	//Si el Usuario NO esta logeado, muestra los ultimos 12 Post, si lo esta muestra los Post likeados
	//por el usuario, si no tiene likes, muestra la home publica
	// public function getPosts() {
	// 	$posts = $this->postMapper->findAll12();

	//  	$posts_array = array();
	//  	foreach($posts as $post) {
	//  		array_push($posts_array, array(
	//  			"id" => $post->getId(),
	// 			"title" => $post->getTitle(),
	// 			"content" => $post->getContent(),
	//  			"author_id" => $post->getAuthor()->getusername()
	//  		));
	//  	}

	//  	header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
	//  	header('Content-Type: application/json');
	// 	echo(json_encode($posts_array));
	// }

	 public function getPosts() {
	  	$posts = $this->postMapper->findAll12();

	 	// json_encode Post objects.
	  	// since Post objects have private fields, the PHP json_encode will not
	  	// encode them, so we will create an intermediate array using getters and
	  	// encode it finally
  		$posts_array = array();
	  	foreach($posts as $post) {
	  		array_push($posts_array, array(
	  			"id" => $post->getId(),
	 			"title" => $post->getTitle(),
				"content" => $post->getContent(),
	  			"author" => $post->getAuthor()->getUsername(),
				"time" => $post->getTime(),
				"date" => $post->getDate(),
				"image" => $post->getImage()
	  		));
	  	}

	  	header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
	  	header('Content-Type: application/json');
	 	echo(json_encode($posts_array));
	  }

	public function createPost($data) {
		$currentUser = parent::authenticateUser();
		$post = new Post();

		if (isset($data->title) && isset($data->content)) {
			$post->setTitle($data->title);
			$post->setContent($data->content);
			$post->setAuthor($data->author);
			$post->setTime($data->time);
			$post->setDate($data->date);
			$post->setImage($data->image);
		}

		try {
			// validate Post object
			$post->checkIsValidForCreate(); // if it fails, ValidationException

			// save the Post object into the database
			$postId = $this->postMapper->save($post);

			// response OK. Also send post in content
			header($_SERVER['SERVER_PROTOCOL'].' 201 Created');
			header('Location: '.$_SERVER['REQUEST_URI']."/".$postId);
			header('Content-Type: application/json');
			echo(json_encode(array(
				"id"=>$postId,
				"title"=>$post->getTitle(),
				"content" => $post->getContent()
			)));

		} catch (ValidationException $e) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			header('Content-Type: application/json');
			echo(json_encode($e->getErrors()));
		}
	}

	public function readPost($postId) {
		// find the Post object in the database
		$post = $this->postMapper->findById($postId);
		if ($post == NULL) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("Post with id ".$postId." not found");
			return;
		}

		$post_array = array(
			"id" => $post->getId(),
			"title" => $post->getTitle(),
			"content" => $post->getContent(),
			"author" => $post->getAuthor()->getusername()

		);

		header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
		header('Content-Type: application/json');
		echo(json_encode($post_array));
	}

	public function updatePost($postId, $data) {
		$currentUser = parent::authenticateUser();

		$post = $this->postMapper->findById($postId);
		if ($post == NULL) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("Post with id ".$postId." not found");
			return;
		}

		// Check if the Post author is the currentUser (in Session)
		if ($post->getAuthor() != $currentUser) {
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			echo("you are not the author of this post");
			return;
		}
		$post->setTitle($data->title);
		$post->setContent($data->content);
		$post->setAuthor($data->author);

		try {
			// validate Post object
			$post->checkIsValidForUpdate(); // if it fails, ValidationException
			$this->postMapper->update($post);
			header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
		}catch (ValidationException $e) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			header('Content-Type: application/json');
			echo(json_encode($e->getErrors()));
		}
	}

	public function deletePost($postId) {
		$currentUser = parent::authenticateUser();
		$post = $this->postMapper->findById($postId);

		if ($post == NULL) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("Post with id ".$postId." not found");
			return;
		}
		// Check if the Post author is the currentUser (in Session)
		if ($post->getAuthor() != $currentUser) {
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			echo("you are not the author of this post");
			return;
		}

		$this->postMapper->delete($post);

		header($_SERVER['SERVER_PROTOCOL'].' 204 No Content');
	}

	
}

// URI-MAPPING for this Rest endpoint
$postRest = new PostRest();
URIDispatcher::getInstance()
->map("GET",	"/post", array($postRest,"getPosts"))
->map("GET",	"/post/$1", array($postRest,"readPost"))
->map("POST", "/post", array($postRest,"createPost"))
->map("POST", "/post/$1/comment", array($postRest,"createComment"))
->map("PUT",	"/post/$1", array($postRest,"updatePost"))
->map("DELETE", "/post/$1", array($postRest,"deletePost"));

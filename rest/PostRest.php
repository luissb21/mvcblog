<?php

require_once(__DIR__."/../model/User.php");
require_once(__DIR__."/../model/UserMapper.php");

require_once(__DIR__."/../model/Post.php");
require_once(__DIR__."/../model/PostMapper.php");

require_once(__DIR__ . "/../model/Ingredient.php");
require_once(__DIR__ . "/../model/IngredientMapper.php");

require_once(__DIR__ . "/../model/Post_ingr.php");
require_once(__DIR__ . "/../model/Post_ingrMapper.php");

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

	public function __construct() {
		parent::__construct();

		$this->postMapper = new PostMapper();
		$this->ingredientMapper = new IngredientMapper();
		$this->post_ingrMapper = new Post_ingrMapper();

	}

//function getPost(): Retorna a la vista principal (HOME) o bien los ultimos 12 post de la BD si el usuario 
//no esta logeado, o bien , si esta logeado, las recetas las cuales a marcado como favoritas.
//En caso de no tener recetas favoritas mostraría los ultimos 12 posts.

	 public function getPosts() {
		$currentUser = parent::authenticateUser();
	  	if($currentUser){	//BEGIN: Si ESTÁ logeado buscamos los likes del usuario		
			$posts = $this->postMapper->findPostLiked($currentUser->getUsername());
			$posts_array = array();
			if(!empty($posts)){	//Si el usuario TIENE likes
				foreach($posts as $post) {
					array_push($posts_array, array(
					  "id" => $post->getId(),
					  "title" => $post->getTitle(),
					  "content" => $post->getContent(),
					  "author" => $post->getAuthor(),
					  "time" => $post->getTime(),
					  "date" => $post->getDate(),
					  "image" => $post->getImage()
				));
			}
			} else {	//Si el usuario NO TIENE likes mostramos las 12 ultimas recetas
				$posts = $this->postMapper->findAll12();
  				$posts_array = array();
	 		 	foreach($posts as $post) {
	 	 			array_push($posts_array, array(
						"id" => $post->getId(),
						"title" => $post->getTitle(),
						"content" => $post->getContent(),
						"author" => $post->getAuthor(),
						"time" => $post->getTime(),
						"date" => $post->getDate(),
						"image" => $post->getImage()
	  		));
	  	}
			}

		  } else {//END: Si NO ESTÁ logeado mostramos las 12 ultimas recetas
		
		$posts = $this->postMapper->findAll12();
  		$posts_array = array();
	  	foreach($posts as $post) {
	  		array_push($posts_array, array(
	  			"id" => $post->getId(),
	 			"title" => $post->getTitle(),
				"content" => $post->getContent(),
	  			"author" => $post->getAuthor(),
				"time" => $post->getTime(),
				"date" => $post->getDate(),
				"image" => $post->getImage()
	  		));
	  	}
	}

	  	header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
	  	header('Content-Type: application/json');
	 	echo(json_encode($posts_array));
	  }



//function createPost: Crea una receta nueva en la BD
	public function createPost($data) {
		$post = new Post();
		$post_ingr = new Post_ingr();
		$ingr = new Ingredient();
		$lastId = $this->postMapper->findLastIdPlus();

		if (isset($data->title) && isset($data->content) && isset($data->author) && isset($data->time) && isset($data->date) && isset($data->image)) {
			$post->setTitle($data->title);
			$post->setContent($data->content);
			$post->setAuthor($data->author);
			$post->setTime($data->time);
			$post->setDate($data->date);
			$post->setImage($lastId . $data->image);

			file_put_contents('../res/' . $lastId . $data->image, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data->imgb64)));

		}

		try {
			// validate Post object
			$post->checkIsValidForCreate(); // if it fails, ValidationException

			// save the Post object into the database
			$postId = $this->postMapper->save($post);

			//INGREDIENTES Y CANTIDADES
			$array_ingr = array();
			$array_cant = array();
			$array_ingr = $data->ingredients;	//Guardamos el array de ingredientes (text)
			//var_dump($data->ingredients);
			$array_cant = $data->amounts;		//Guardamos el array de cantidades	(text)
			//var_dump($data->amounts);
			//var_dump(count($array_ingr));
			$i = 0;

				foreach ($array_ingr as $ingredient) {
					if (!($this->ingredientMapper->existsIngredients($ingredient))) {
						$ingr->setName($ingredient);
						$this->ingredientMapper->save($ingr);
					}
					$post_ingr->setPost_id($this->postMapper->findLastId());
					$post_ingr->setIngr_name($ingredient);
					$post_ingr->setCantidad($array_cant[$i]);
					$this->post_ingrMapper->save($post_ingr); //Guardamos un Post_like con post.id , ingredients(text), cantidad(text)
					$i++;
				}

			//INGREDIENTES Y CANTIDADES

			// response OK. Also send post in content
			header($_SERVER['SERVER_PROTOCOL'].' 201 Created');
			header('Location: '.$_SERVER['REQUEST_URI']."/".$postId);
			header('Content-Type: application/json');
			echo(json_encode(array(
				"id"=>$postId,
				"title"=>$post->getTitle(),
				"content" => $post->getContent(),
				"author" => $post->getAuthor(),
				"time" => $post->getTime(),
				"date" => $post->getDate(),
				"image" => $post->getImage()
			)));

		} catch (ValidationException $e) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			header('Content-Type: application/json');
			echo(json_encode($e->getErrors()));
		}
	}


//function readPost: Visualizacion en detalle de un Post
	public function readPost($postId) {
		// find the Post object in the database
		$post = $this->postMapper->findByIdWithIngredients($postId); //setea los Post_Ingr en el metodo
		if ($post == NULL) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("Post with id ".$postId." not found");
			return;
		}

		$ingredients = $post->getIngredients(); //Objetos Post_Ingr
		$post_ingr_array = array();
		foreach($ingredients as $ingredient){
			array_push($post_ingr_array, array(
				"ingr_name" => $ingredient->getIngr_name(),
				"cantidad" => $ingredient->getCantidad()
			));
		}

		$post_array = array(
			"id"=>$postId,
				"title"=>$post->getTitle(),
				"content" => $post->getContent(),
				"author" => $post->getAuthor(),
				"time" => $post->getTime(),
				"date" => $post->getDate(),
				"image" => $post->getImage(),
				"ingredients" => $post_ingr_array

		);

		

		//var_dump($post->getIngredients());
		//var_dump($post_array);		

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
		//var_dump($post->getAuthor());
		//var_dump($currentUser->getUsername());
		// Check if the Post author is the currentUser (in Session)
		if ($post->getAuthor() != $currentUser->getUsername()) {
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			echo("you are not the author of this post");
			return;
		}
		$post->setTitle($data->title);
		$post->setContent($data->content);
		$post->setAuthor($data->author);
		$post->setTime($data->time);
		$post->setDate($data->date);
		$post->setImage($postId . $data->image . 'edit');

		file_put_contents('../res/' . $postId . $data->image  . 'edit', base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data->imgb64)));

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

	

	public function findAllIngredients() {
		$ingredients = $this->ingredientMapper->findAllIngredients();
		//var_dump("Entra en el finAllIngrdients");
		//var_dump($ingredients);
		$ingredients_array = array();
		foreach($ingredients as $ingredient){
			array_push($ingredients_array, array(
				"name" => $ingredient->getName()
			));
		}

	  	header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
	  	header('Content-Type: application/json');
	 	echo(json_encode($ingredients_array));
		 
	  }



}

// URI-MAPPING for this Rest endpoint
$postRest = new PostRest();
URIDispatcher::getInstance()
->map("GET",	"/post", array($postRest,"getPosts"))
->map("GET",	"/post/$1", array($postRest,"readPost"))
->map("POST", "/post", array($postRest,"createPost"))
->map("GET", "/ingredients", array($postRest,"findAllIngredients"))
->map("PUT",	"/post/$1", array($postRest,"updatePost"))
->map("DELETE", "/post/$1", array($postRest,"deletePost"));

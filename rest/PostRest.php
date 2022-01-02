<?php

require_once(__DIR__ . "/../model/User.php");
require_once(__DIR__ . "/../model/UserMapper.php");

require_once(__DIR__ . "/../model/Post.php");
require_once(__DIR__ . "/../model/PostMapper.php");

require_once(__DIR__ . "/../model/Ingredient.php");
require_once(__DIR__ . "/../model/IngredientMapper.php");

require_once(__DIR__ . "/../model/Post_ingr.php");
require_once(__DIR__ . "/../model/Post_ingrMapper.php");

require_once(__DIR__ . "/../model/Post_like.php");
require_once(__DIR__ . "/../model/Post_likeMapper.php");

require_once(__DIR__ . "/BaseRest.php");

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
class PostRest extends BaseRest
{
	private $postMapper;

	public function __construct()
	{
		parent::__construct();

		$this->postMapper = new PostMapper();
		$this->ingredientMapper = new IngredientMapper();
		$this->post_ingrMapper = new Post_ingrMapper();
		$this->post_likeMapper = new Post_likeMapper();
	}

	//function getPost():Home publico

	public function getPosts(){
		$posts = $this->postMapper->findAll12();
		$posts_array = array();
		foreach ($posts as $post) {
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
	

		header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
		header('Content-Type: application/json');
		echo (json_encode($posts_array));
	}



	//function createPost: Crea una receta nueva en la BD
	public function createPost($data)
	{
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
			header($_SERVER['SERVER_PROTOCOL'] . ' 201 Created');
			header('Location: ' . $_SERVER['REQUEST_URI'] . "/" . $postId);
			header('Content-Type: application/json');
			echo (json_encode(array(
				"id" => $postId,
				"title" => $post->getTitle(),
				"content" => $post->getContent(),
				"author" => $post->getAuthor(),
				"time" => $post->getTime(),
				"date" => $post->getDate(),
				"image" => $post->getImage()
			)));
		} catch (ValidationException $e) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
			header('Content-Type: application/json');
			echo (json_encode($e->getErrors()));
		}
	}


	//function readPost: Visualizacion en detalle de un Post
	public function readPost($postId)
	{
		$currentuser = null;
		if (isset($_SERVER['PHP_AUTH_USER'])) {
			$currentuser = $_SERVER['PHP_AUTH_USER'];
		}
		// find the Post object in the database
		$post = $this->postMapper->findByIdWithIngredients($postId,$currentuser); //setea los Post_Ingr en el metodo
		if ($post == NULL) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
			echo ("Post with id " . $postId . " not found");
			return;
		}

		$ingredients = $post->getIngredients(); //Objetos Post_Ingr
		$post_ingr_array = array();
		foreach ($ingredients as $ingredient) {
			array_push($post_ingr_array, array(
				"ingr_name" => $ingredient->getIngr_name(),
				"cantidad" => $ingredient->getCantidad()
			));
		}

		$post_array = array(
			"id" => $postId,
			"title" => $post->getTitle(),
			"content" => $post->getContent(),
			"author" => $post->getAuthor(),
			"time" => $post->getTime(),
			"date" => $post->getDate(),
			"image" => $post->getImage(),
			"ingredients" => $post_ingr_array,
			"like" => $post->getLike()

		);



		//var_dump($post->getIngredients());
		//var_dump($post_array);		

		header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
		header('Content-Type: application/json');
		echo (json_encode($post_array));
	}



	//function updatePost: Modificiación de los datos de un Post
	public function updatePost($postId, $data)
	{
		$currentUser = parent::authenticateUser();
		$post = $this->postMapper->findById($postId);
		if ($post == NULL) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
			echo ("Post with id " . $postId . " not found");
			return;
		}
		//var_dump($post->getAuthor());
		//var_dump($currentUser->getUsername());
		// Check if the Post author is the currentUser (in Session)
		if ($post->getAuthor() != $currentUser->getUsername()) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
			echo ("you are not the author of this post");
			return;
		}
		$post->setTitle($data->title);
		$post->setContent($data->content);
		$post->setAuthor($data->author);
		$post->setTime($data->time);
		$post->setDate($data->date);
		$post->setImage($postId . $data->image . 'edit');

		file_put_contents('../res/' . $postId . 'edit' . $data->image, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data->imgb64)));

		try {
			// validate Post object
			$post->checkIsValidForUpdate(); // if it fails, ValidationException
			$this->postMapper->update($post);
			//INGREDIENTES

			$post_ingr = new Post_ingr();
			$ingr = new Ingredient();
			$ingrs_cant = $this->post_ingrMapper->findIngrendietsRecipes($postId);

			$array_ingr = array();
			$array_cant = array();
			$array_ingr = $data->ingredients;	//Guardamos el array de ingredientes (text)
			$array_cant = $data->amounts;		//Guardamos el array de cantidades	(text)
			$i = 0;
			$j = 0;
			$array_nuevo = array();

			foreach ($array_ingr as $ingredient) {
				array_push($array_nuevo, new Post_ingr($postId, $ingredient, $array_cant[$i])); //Creamos un array con los nuevos Post_ingr recibidos en el input
				$i++;
			}
			if (!($array_nuevo == $ingrs_cant)) {
				$this->post_ingrMapper->deleteAllIngredients($post);
				foreach ($array_ingr as $ingredient) {
					if (!($this->ingredientMapper->existsIngredients($ingredient))) {
						$ingr->setName($ingredient);
						$this->ingredientMapper->save($ingr);
					}
					$post_ingr->setPost_id($postId);
					$post_ingr->setIngr_name($ingredient);
					$post_ingr->setCantidad($array_cant[$j]);
					$this->post_ingrMapper->save($post_ingr); //Guardamos un Post_like con post.id , ingredients(text), cantidad(text)
					$j++;
				}
			}

			//INGREDIENTES

			header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
		} catch (ValidationException $e) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
			header('Content-Type: application/json');
			echo (json_encode($e->getErrors()));
		}
	}


	//function deletePost: Elimina un Post de la BD
	public function deletePost($postId)
	{
		$currentUser = parent::authenticateUser();
		$post = $this->postMapper->findById($postId);

		if ($post == NULL) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
			echo ("Post with id " . $postId . " not found");
			return;
		}
		// Check if the Post author is the currentUser (in Session)
		if ($post->getAuthor() != $currentUser->getUsername()) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
			echo ("you are not the author of this post");
			return;
		}

		$this->post_ingrMapper->deleteAllIngredients($post);
		$this->post_likeMapper->deleteLikes($post);
		$this->postMapper->delete($post);

		header($_SERVER['SERVER_PROTOCOL'] . ' 204 No Content');
	}



	public function findAllIngredients()
	{
		$ingredients = $this->ingredientMapper->findAllIngredients();
		//var_dump("Entra en el finAllIngrdients");
		//var_dump($ingredients);
		$ingredients_array = array();
		foreach ($ingredients as $ingredient) {
			array_push($ingredients_array, array(
				"name" => $ingredient->getName()
			));
		}

		header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
		header('Content-Type: application/json');
		echo (json_encode($ingredients_array));
	}

	public function findRecipeIngredients($postid)
	{
		$post_ingrs = $this->post_ingrMapper->findIngrendietsRecipes($postid);	//Tuplas post-ingrediente-cantidad
		$recipe_ingrs_array = array();

		foreach ($post_ingrs as $post_ingr) {
			array_push($recipe_ingrs_array, array(
				"ingr_name" => $post_ingr->getIngr_name(),
				"cantidad" => $post_ingr->getCantidad()
			));
		}

		//var_dump($recipe_ingrs_array);
		header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
		header('Content-Type: application/json');
		echo (json_encode($recipe_ingrs_array));
	}


	public function findMyRecipes()
	{
		$currentUser = parent::authenticateUser();
		$posts = $this->postMapper->findByAuthor($currentUser->getUsername());
		$posts_array = array();
		foreach ($posts as $post) {
			array_push($posts_array, array(
				"id" => $post->getId(),
				"title" => $post->getTitle(),
				"content" => $post->getContent(),
				"author" => $post->getAuthor(),
				"time" => $post->getTime(),
				"date" => $post->getDate(),
				"image" => $post->getImage(),
				"like" => $post->getLike()
			));
		}

		header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
		header('Content-Type: application/json');
		echo (json_encode($posts_array));
	}

	public function findAllRecipes()
	{
		$currentuser = null;
		if (isset($_SERVER['PHP_AUTH_USER'])) {
			$currentuser = $_SERVER['PHP_AUTH_USER'];
		}

		$posts = $this->postMapper->findAll($currentuser);
		$posts_array = array();
		foreach ($posts as $post) {
			array_push($posts_array, array(
				"id" => $post->getId(),
				"title" => $post->getTitle(),
				"content" => $post->getContent(),
				"author" => $post->getAuthor(),
				"time" => $post->getTime(),
				"date" => $post->getDate(),
				"image" => $post->getImage(),
				"like" => $post->getLike()
			));
		}

		//var_dump($posts_array);

		header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
		header('Content-Type: application/json');
		echo (json_encode($posts_array));
	}



	public function filters($filters)
	{
		
		$filt = explode(',', $filters);
		$posts_array = array();
		//var_dump($filt);

		if (!(empty($filt))) {
			$array_pi = array();

			foreach ($filt as $ingr_name) {
				$posts_ingr = $this->post_ingrMapper->findPostIngrByName($ingr_name); //Todas las relaciones post_ingr con ese ingrediente
				foreach ($posts_ingr as $rel) {
					array_push($array_pi, $rel);
				}
			}

			//var_dump($array_pi);	//$array_pi es un array de objetos Post_Ingr con las coincidencias solicitadas

			$array_ids = array();	//Array con todas las IDs obtenidas

			foreach ($array_pi as $pi) {
				$temp_id = $pi->getPost_id();
				if (!(in_array($temp_id, $array_ids))) {
					array_push($array_ids, $temp_id);
				}
			}

			//var_dump($array_ids);

			$id_clasifier = array();

			foreach ($array_ids as $id) {
				$id_clasifier[$id] = array();
				$ingrs_id = array();
				foreach ($array_pi as $relation) {
					if ($relation->getPost_Id() == $id) {
						array_push($ingrs_id, $relation->getIngr_name());
					}
				}
				$id_clasifier[$id] = $ingrs_id;
			}

			//var_dump($id_clasifier);

			$show = array(); //Ids de los posts a mostrar

			foreach ($array_ids as $id) {
				if ($id_clasifier[$id] == $filt) {
					array_push($show, $id);
				}
			}
			//echo("-----------show------------\n");
			//var_dump($show);

			$postFilter = array();
			foreach ($show as $s) {
				$post = $this->postMapper->findById($s);
				array_push($postFilter, $post);
			}

			//echo("-----------postFilter------------\n");
			//var_dump($postFilter);

			foreach ($postFilter as $post) {
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

		//var_dump($posts_array);

		header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
		header('Content-Type: application/json');
		echo (json_encode($posts_array));
	}

	public function findPostsLiked(){
		$posts_array = array();
		if (!isset($_SERVER['PHP_AUTH_USER'])) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
			header('Content-Type: application/json');
			echo (json_encode($posts_array));
		} else {
			$posts = $this->postMapper->findPostLiked($_SERVER['PHP_AUTH_USER']);
			
			foreach ($posts as $post) {
				array_push($posts_array, array(
					"id" => $post->getId(),
					"title" => $post->getTitle(),
					"content" => $post->getContent(),
					"author" => $post->getAuthor(),
					"time" => $post->getTime(),
					"date" => $post->getDate(),
					"image" => $post->getImage(),
					"like" => $post->getLike()
				));
			}

			//var_dump($posts_array);

			header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
			header('Content-Type: application/json');
			echo (json_encode($posts_array));
		}
	}
/*
	public function countLikes(){
		$posts = $this->postMapper->findAll();
		$countLikes = array();
			foreach ($posts as $post) {
				$c = $this->post_likeMapper->countLikesPost($post);
				$countLikes[$post->getId()] = $c;
			}

		//var_dump($countLikes);
		header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
		header('Content-Type: application/json');
		echo (json_encode($countLikes));
	}
*/






}

// URI-MAPPING for this Rest endpoint
$postRest = new PostRest();
URIDispatcher::getInstance()
	->map("GET",	"/post", array($postRest, "getPosts"))
	->map("GET",	"/likes", array($postRest, "findPostsLiked"))
	->map("GET",	"/allrecipes", array($postRest, "findAllRecipes"))
	//->map("GET",	"/count", array($postRest, "countLikes"))
	->map("GET",	"/filters/$1", array($postRest, "filters"))
	->map("GET",	"/post/$1", array($postRest, "readPost"))
	->map("POST", "/post", array($postRest, "createPost"))
	->map("GET", "/ingredients", array($postRest, "findAllIngredients"))
	->map("GET", "/myrecipes", array($postRest, "findMyRecipes"))
	->map("GET", "/recingr/$1", array($postRest, "findRecipeIngredients"))
	->map("PUT",	"/post/$1", array($postRest, "updatePost"))
	->map("DELETE", "/post/$1", array($postRest, "deletePost"));

<?php
// file: model/PostMapper.php
require_once(__DIR__ . "/../core/PDOConnection.php");

require_once(__DIR__ . "/../model/User.php");
require_once(__DIR__ . "/../model/Post.php");
//require_once(__DIR__ . "/../model/Ingredient.php");
//require_once(__DIR__ . "/../model/Post_ingr.php");
//require_once(__DIR__ . "/../model/Post_like.php");
//require_once(__DIR__ . "/../model/Post_likeMapper.php");
//require_once(__DIR__."/../model/ingredient.php");

/**
 * Class PostMapper
 *
 * Database interface for Post entities
 *
 * @author lipido <lipido@gmail.com>
 */
class PostMapper
{

	/**
	 * Reference to the PDO connection
	 * @var PDO
	 */
	private $db;

	public function __construct()
	{
		$this->db = PDOConnection::getInstance();
	}

	/**
	 * Retrieves all posts
	 *
	 * Note: ingredients are not added to the Post instances
	 *
	 * @throws PDOException if a database error occurs
	 * @return mixed Array of Post instances (without ingredients)
	 */
	public function findAll()
	{ 
		$stmt = $this->db->query("SELECT * FROM posts, users WHERE users.username = posts.author ORDER BY posts.date"); //OJO CON EL 12
		$posts_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$posts = array();

		foreach ($posts_db as $post) {
			$author = new User($post["author"]);
			array_push($posts, new Post($post["id"], $post["title"], $post["content"], $author, $post["time"], $post["date"], $post["image"]));
		}

		return $posts;
	}


	public function findAll12()
	{
		$stmt = $this->db->query("SELECT * FROM posts, users WHERE users.username = posts.author ORDER BY posts.date DESC limit 12");
		$posts_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$posts = array();

		foreach ($posts_db as $post) {
			array_push($posts, new Post($post["id"], $post["title"], $post["content"], $post["username"], $post["time"], $post["date"], $post["image"]));
		}

		return $posts;
	}

	public function findPostLiked($currentuser) {//iNDEX PRIV 
		$stmt = $this->db->query(
			"SELECT * FROM posts, users, post_like WHERE users.username ='$currentuser' 
			AND users.username = post_like.user_name AND posts.id = post_like.post_id ORDER BY posts.date DESC"
		);
		$posts_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$posts = array();

		foreach ($posts_db as $post) {
			array_push($posts, new Post($post["id"], $post["title"], $post["content"], $post["author"], $post["time"], $post["date"], $post["image"]));
		}

		return $posts;
	}


	public function findPostLikedView($currentuser) {//iNDEX PRIV
		$stmt = $this->db->query(
			"SELECT posts.id FROM posts, users, post_like WHERE users.username ='$currentuser' 
			AND users.username = post_like.user_name AND posts.id = post_like.post_id ORDER BY posts.date DESC"
		);
		$posts_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$posts = array();

		foreach ($posts_db as $post) {
			array_push($posts, $post["id"]);
		}

		return $posts;
	}

	public function findPostsIndex($currentuser) //Devuelve los Posts que deben aparecer en la PRIVADA, dependiendo de la situacion
	{
		$stmt = $this->db->query(
			"SELECT * FROM posts, users, post_like WHERE users.username ='$currentuser' 
			AND users.username = post_like.user_name AND posts.id = post_like.post_id ORDER BY posts.date DESC"
		);
		$posts_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$posts = array();

		foreach ($posts_db as $post) {
			$author = new User($post["username"]);
			array_push($posts, new Post($post["id"], $post["title"], $post["content"], $author, $post["time"], $post["date"], $post["image"]));
		}


		return $posts;
	}

	public function findByAuthor($currentuser) //Devuelve los Posts que deben aparecer en la MIS RECETAS PRIVADAS
	{
		$stmt = $this->db->query(
			"SELECT * FROM posts, users WHERE users.username ='$currentuser' 
			AND posts.author = users.username ORDER BY posts.date DESC"
		);
		$posts_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$posts = array();

		foreach ($posts_db as $post) {
			$author = new User($post["username"]);
			array_push($posts, new Post($post["id"], $post["title"], $post["content"], $author, $post["time"], $post["date"], $post["image"]));
		}
		
		return $posts;
	}



	public function findLastId() //Devuelve el id del ultimo Post
	{
		$stmt = $this->db->query(
			"SELECT posts.id FROM posts ORDER BY posts.id DESC LIMIT 1"
		);
		
		$posts_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$id = $posts_db[0]["id"];

		return $id;

		}

	/**
	 * Loads a Post from the database given its id
	 *
	 * Note: ingredients are not added to the Post
	 *
	 * @throws PDOException if a database error occurs
	 * @return Post The Post instances (without ingredients). NULL
	 * if the Post is not found
	 */
	public function findById($postid)
	{
		$stmt = $this->db->prepare("SELECT * FROM posts WHERE id=?");
		$stmt->execute(array($postid));
		$post = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($post != null) {
			return new Post(
				$post["id"],
				$post["title"],
				$post["content"],
				new User($post["author"]),
				$post["time"],
				$post["date"],
				$post["image"]
			);
		} else {
			return NULL;
		}
	}

	/**
	 * Loads a Post from the database given its id
	 *
	 * It includes all the ingredients
	 *
	 * @throws PDOException if a database error occurs
	 * @return Post The Post instances (without ingredients). NULL
	 * if the Post is not found
	 */
	// public function findByIdWithIngredients($postid)
	// {
	// 	$stmt = $this->db->prepare("SELECT
	// 		posts.id,
	// 		posts.title,
	// 		posts.content,
	// 		posts.author,
	// 		posts.time,
	// 		posts.date,
	// 		posts.image,
	// 		post_ingr.post_id,
	// 		post_ingr.ingr_name,
	// 		post_ingr.cantidad
	// 		FROM posts LEFT OUTER JOIN post_ingr
	// 		ON posts.id = post_ingr.post_id
	// 		WHERE
	// 		posts.id=? ");

	// 	$stmt->execute(array($postid));
	// 	$post_wt_ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);
	// 	//print_r($post_wt_ingredients);

	// 	if (sizeof($post_wt_ingredients) > 0) {
	// 		$post = new Post(
	// 			$post_wt_ingredients[0]["id"],
	// 			$post_wt_ingredients[0]["title"],
	// 			$post_wt_ingredients[0]["content"],
	// 			new User($post_wt_ingredients[0]["author"]),
	// 			$post_wt_ingredients[0]["time"],
	// 			$post_wt_ingredients[0]["date"],
	// 			$post_wt_ingredients[0]["image"]
	// 		);
	// 		$ingredient_array = array();
	// 		if ($post_wt_ingredients[0]["ingr_name"] != null) {
	// 			foreach ($post_wt_ingredients as $ingredient) {
	// 				$ingredient = new Post_ingr(
	// 					$ingredient["id"],
	// 					$ingredient["ingr_name"],
	// 					$ingredient["cantidad"],
	// 				);
	// 				array_push($ingredient_array, $ingredient);
	// 			}
	// 		}
	// 		$post->setingredients($ingredient_array);

	// 		return $post;
	// 	} else {
	// 		return NULL;
	// 	}
	// }

	/**
	 * Saves a Post into the database
	 *
	 * @param Post $post The post to be saved
	 * @throws PDOException if a database error occurs
	 * @return int The mew post id
	 */
	public function save(Post $post)
	{
		$stmt = $this->db->prepare("INSERT INTO posts(title, content, author, time, date, image) values (?,?,?,?,?,?)");
		$stmt->execute(array($post->getTitle(), $post->getContent(), $post->getAuthor(), $post->getTime(), $post->getDate(), $post->getImage()));
		return $this->db->lastInsertId();
	}

	/**
	 * Updates a Post in the database
	 *
	 * @param Post $post The post to be updated
	 * @throws PDOException if a database error occurs
	 * @return void
	 */
	public function update(Post $post)
	{ //tocar
		$stmt = $this->db->prepare("UPDATE posts set title=?, content=?, time=?, date=?, image=? where id=?");
		$stmt->execute(array($post->getTitle(), $post->getContent(), $post->getTime(), $post->getDate(), $post->getImage(), $post->getId()));
	}

	/**
	 * Deletes a Post into the database
	 *
	 * @param Post $post The post to be deleted
	 * @throws PDOException if a database error occurs
	 * @return void
	 */
	public function delete(Post $post)
	{
		//Eliminar Post
		$stmt = $this->db->prepare("DELETE from posts WHERE id=?");
		$stmt->execute(array($post->getId()));
	}
}

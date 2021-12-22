<?php
// file: model/PostMapper.php
require_once(__DIR__."/../core/PDOConnection.php");
require_once(__DIR__."/../model/Post_ingr.php");
require_once(__DIR__."/../model/User.php");
require_once(__DIR__."/../model/Post.php");
//require_once(__DIR__."/../model/Comment.php");

/**
* Class PostMapper
*
* Database interface for Post entities
*
* @author lipido <lipido@gmail.com>
*/
class Post_ingrMapper {

	/**
	* Reference to the PDO connection
	* @var PDO
	*/
	private $db;

	public function __construct() {
		$this->db = PDOConnection::getInstance();
	}

	/**
	* Retrieves all posts
	*
	* Note: Comments are not added to the Post instances
	*
	* @throws PDOException if a database error occurs
	* @return mixed Array of Post instances (without comments)
	*/
	public function findAll() {
		$stmt = $this->db->query("SELECT * FROM post_ingr"); 
		$post_ingrs_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$post_ingrs = array();

		foreach ($post_ingrs_db as $post_ingr) {
			array_push($post_ingrs_db, $post_ingr);
		}

		return $post_ingrs_db;
	}

	public function findIngrendietsRecipes($postid) {//EDIT
		$stmt = $this->db->query("SELECT * FROM post_ingr WHERE post_id ='$postid'"); 
		$post_ingrs_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$ingr_cant = array();

		foreach ($post_ingrs_db as $post_ingr) {
			array_push($ingr_cant, new Post_ingr($post_ingr["post_id"], $post_ingr["ingr_name"], $post_ingr["cantidad"]));
		}

		return $ingr_cant;
	}



	public function findPostIngrByName($ingr_name) {//Filtro
		
		$stmt = $this->db->query("SELECT * FROM post_ingr WHERE ingr_name = '$ingr_name'"); 
		$post_ingrs_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$post_ingrs = array();

		foreach ($post_ingrs_db as $post_ingr) {
			array_push($post_ingrs, new Post_ingr($post_ingr["post_id"], $post_ingr["ingr_name"], $post_ingr["cantidad"]));
		}

		return $post_ingrs;
		
/* 		$stmt = $this->db->prepare("SELECT * FROM post_ingr WHERE ingr_name = ?"); 
		$stmt->execute(array($ingr_name));
		$ingredients_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		return $ingredients_db; */
	}


		public function save(Post_ingr $post_ingr) {	//Guarda una variable post_ingr
			$stmt = $this->db->prepare("INSERT INTO post_ingr(post_id,ingr_name, cantidad) values (?,?,?)");
			$stmt->execute(array($post_ingr->getPost_id(),$post_ingr->getIngr_name(), $post_ingr->getCantidad()));
			return $this->db->lastInsertId();
		} 
		
		//Eliminar todos los ingredientes asignados a un post
		public function deleteAllIngredients(Post $post) {
			$stmt = $this->db->prepare("DELETE from post_ingr WHERE post_id=?");
			$stmt->execute(array($post->getId()));
		}

	}

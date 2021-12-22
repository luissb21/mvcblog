<?php
// file: model/PostMapper.php
require_once(__DIR__."/../core/PDOConnection.php");

require_once(__DIR__."/../model/Ingredient.php");
//require_once(__DIR__."/../model/Post.php");
//require_once(__DIR__."/../model/Comment.php");

/**
* Class PostMapper
*
* Database interface for Post entities
*
* @author lipido <lipido@gmail.com>
*/
class IngredientMapper {

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
	public function findAllIngredients() {
		$stmt = $this->db->query("SELECT * FROM ingredients"); 
		$ingredients_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$ingredients = array();

		foreach ($ingredients_db as $ingredient) {
			array_push($ingredients, new Ingredient($ingredient["name"]));
		}

		return $ingredients;
	}

	public function existsIngredients($ingredient) {
		$stmt = $this->db->prepare("SELECT * FROM ingredients WHERE EXISTS (SELECT * FROM ingredients WHERE ingredients.name = ?)"); 
		$stmt->execute(array($ingredient));
		$ingredients_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		return $ingredients_db;
	}

		/**
		* Saves a Post into the database
		*
		* @param Post $post The post to be saved
		* @throws PDOException if a database error occurs
		* @return int The mew post id
		*/
		public function save(Ingredient $ingredient) {
			$stmt = $this->db->prepare("INSERT INTO ingredients(name) values (?)");
			$stmt->execute(array($ingredient->getName()));
			return $this->db->lastInsertId();
		}

		public function saveByName($ingredient) {
			$stmt = $this->db->prepare("INSERT INTO ingredients(name) values (?)");
			$stmt->execute(array($ingredient));
			return $this->db->lastInsertId();
		}

		/**
		* Updates a Post in the database
		*
		* @param Post $post The post to be updated
		* @throws PDOException if a database error occurs
		* @return void
		*/
		public function update(Ingredient $ingredient) { //tocar
			$stmt = $this->db->prepare("UPDATE ingredients set name=? where name=?");
			$stmt->execute(array($ingredient->getName()));
		}

		/**
		* Deletes a Post into the database
		*
		* @param Post $post The post to be deleted
		* @throws PDOException if a database error occurs
		* @return void
		*/
		public function delete(Ingredient $ingredient) {
			$stmt = $this->db->prepare("DELETE from ingredients WHERE name=?");
			$stmt->execute(array($ingredient->getName()));
		}

	}

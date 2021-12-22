<?php
// file: model/Post.php

require_once(__DIR__."/../core/ValidationException.php");

/**
* Class Post
*
* Represents a Post in the blog. A Post was written by an
* specific User (author) and contains a list of Comments
*
* @author lipido <lipido@gmail.com>
*/
class Post_ingr {

	/**
	* The id of this post
	* @var string
	*/
	private $post_id;

	private $ingr_name;

	private $cantidad;


	/**
	* The constructor
	*
	* @param string $id The id of the post
	* @param string $title The id of the post
	* @param string $content The content of the post
	* @param User $author The author of the post
	* @param mixed $comments The list of comments
	*/
	public function __construct($post_id=NULL, $ingr_name=NULL, $cantidad=NULL) { //, ingrdienteas
		$this->post_id = $post_id;
		$this->ingr_name = $ingr_name;
		$this->cantidad = $cantidad;
	}

	/**
	* Gets the id of this post
	*
	* @return string The id of this post
	*/
	public function getPost_id() {
		return $this->post_id;
	}

	public function setPost_id($post_id) {
		$this->post_id = $post_id;
	}


	public function getIngr_name() {
		return $this->ingr_name;
	}

	public function setIngr_name($ingr_name) {
		$this->ingr_name = $ingr_name;
	}

	public function getCantidad() {
		return $this->cantidad;
	}

	public function setCantidad($cantidad) {
		$this->cantidad = $cantidad;
	}

	/**
	* Gets the title of this post
	*
	* @return string The title of this post
	*/

	public function checkIsValidForCreate() {
		$errors = array();
		if ($this->post_id == NULL ) {
			$errors["post_id"] = "post_id is mandatory";
		}
		if ($this->ingr_name == NULL ) {
			$errors["ingr_name"] = "ingr_name is mandatory";
		}
		if ($this->date == NULL ) {
			$errors["cantidad"] = "cantidad is mandatory";
		}

		if (sizeof($errors) > 0){
			throw new ValidationException($errors, "post_ingr is not valid");
		}
	}

	/**
	* Checks if the current instance is valid
	* for being updated in the database.
	*
	* @throws ValidationException if the instance is
	* not valid
	*
	* @return void
	*/
	public function checkIsValidForUpdate() {
		$errors = array();

		if (!isset($this->post_id)) {
			$errors["post_id"] = "post_id is mandatory";
		}

		try{
			$this->checkIsValidForCreate();
		}catch(ValidationException $ex) {
			foreach ($ex->getErrors() as $key=>$error) {
				$errors[$key] = $error;
			}
		}
		if (sizeof($errors) > 0) {
			throw new ValidationException($errors, "post_ingr is not valid");
		}
	}
}

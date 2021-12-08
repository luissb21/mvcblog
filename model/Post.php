<?php
// file: model/Post.php

require_once(__DIR__."/../core/ValidationException.php");

class Post {

	/**
	* The id of this post
	* @var string
	*/
	private $id;

	/**
	* The title of this post
	* @var string
	*/
	private $title;

	/**
	* The content of this post
	* @var string
	*/
	private $content;

	/**
	* The author of this post
	* @var User
	*/
	private $author;

	/**
	* The list of comments of this post
	* @var mixed
	*/
	//private $comments;

	private $time;

	private $date;

	private $ingredients;

	private $image;

	/**
	* The constructor
	*
	* @param string $id The id of the post
	* @param string $title The id of the post
	* @param string $content The content of the post
	* @param User $author The author of the post
	* @param mixed $comments The list of comments
	*/
	public function __construct($id=NULL, $title=NULL, $content=NULL, User $author=NULL, $time=NULL, $date=NULL, $image=NULL, $ingredients=NULL) {
		$this->id = $id;
		$this->title = $title;
		$this->content = $content;
		$this->author = $author;
		$this->time = $time;
		$this->date = $date;
		$this->image = $image;
		$this->ingredients = $ingredients;
	}

	/**
	* Gets the id of this post
	*
	* @return string The id of this post
	*/
	public function getId() {
		return $this->id;
	}

	/**
	* Gets the title of this post
	*
	* @return string The title of this post
	*/
	public function getTitle() {
		return $this->title;
	}

	/**
	* Sets the title of this post
	*
	* @param string $title the title of this post
	* @return void
	*/
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	* Gets the content of this post
	*
	* @return string The content of this post
	*/
	public function getContent() {
		return $this->content;
	}

	/**
	* Sets the content of this post
	*
	* @param string $content the content of this post
	* @return void
	*/
	public function setContent($content) {
		$this->content = $content;
	}


	public function getAuthor() {
		return $this->author;
	}


	public function setAuthor(User $author) {
		$this->author = $author;
	}


	public function getTime() {
		return $this->time;
	}

	public function setTime($time) {
		$this->time = $time;
	}

	public function getDate() {
		return $this->date;
	}

	public function setDate($date) {
		$this->date = $date;
	}


	public function getIngredients() {
		return $this->ingredients;
	}

	public function setIngredients(array $ingredients) {
		$this->ingredients = $ingredients;
	}

	public function getImage() {
		return $this->image;
	}

	public function setImage($image) {
		$this->image = $image;
	}

	public function checkIsValidForCreate() {
		$errors = array();
		if (strlen(trim($this->title)) == 0 ) {
			$errors["title"] = "title is mandatory";
		}
		if (strlen(trim($this->content)) == 0 ) {
			$errors["content"] = "content is mandatory";
		}
		if ($this->author == NULL ) {
			$errors["author"] = "author is mandatory";
		}
		 if ($this->time == NULL ) {
		 	$errors["time"] = "time is mandatory";
		 }
		 if ($this->date == NULL ) {
		 	$errors["date"] = "date is mandatory";
		 }

/* 		if ($this->ingredients == NULL ) {
			$errors["ingredients"] = "ingredients is mandatory";
		} */

	 	 if ($this->image == NULL ) {
		 	$errors["image"] = "image is mandatory";
		 }

		if (sizeof($errors) > 0){
			throw new ValidationException($errors, "post is not valid");
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

		if (!isset($this->id)) {
			$errors["id"] = "id is mandatory";
		}

		try{
			$this->checkIsValidForCreate();
		}catch(ValidationException $ex) {
			foreach ($ex->getErrors() as $key=>$error) {
				$errors[$key] = $error;
			}
		}
		if (sizeof($errors) > 0) {
			throw new ValidationException($errors, "post is not valid");
		}
	}
}

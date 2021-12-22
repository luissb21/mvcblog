<?php
// file: model/User.php

require_once(__DIR__."/../core/ValidationException.php");

/**
* Class User
*
* Represents a User in the blog
*
* @author lipido <lipido@gmail.com>
*/
class Ingredient {

	/**
	* The user name of the user
	* @var string
	*/
	private $name;

	public function __construct($name=NULL) {
		$this->name = $name;
	}

	/**
	* Gets the username of this user
	*
	* @return string The username of this user
	*/
	public function getName() {
		return $this->name;
	}

	/**
	* Sets the username of this user
	*
	* @param string $username The username of this user
	* @return void
	*/
	public function setName($name) {
		$this->name = $name;
	}

	public function checkIsValidForIngr() {
		$errors = array();
		if ($this->name == NULL) {
			$errors["name"] = "Ingredient name cant be null";
		}
		if (sizeof($errors)>0){
			throw new ValidationException($errors, "ingredient is not valid");
		}
	}
}

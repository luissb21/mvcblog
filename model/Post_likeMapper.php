<?php
// file: model/PostMapper.php
require_once(__DIR__ . "/../core/PDOConnection.php");
require_once(__DIR__ . "/../model/Post.php");
require_once(__DIR__ . "/../model/Post_like.php");


//require_once(__DIR__."/../model/Comment.php");

/**
 * Class PostMapper
 *
 * Database interface for Post entities
 *
 * @author lipido <lipido@gmail.com>
 */
class Post_likeMapper
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

	public function saveLike($postid, $currentuser)
	{
		$stmt = $this->db->prepare("INSERT INTO post_like(post_id, user_name) values (?,?)");
		$stmt->execute(array($postid, $currentuser));
		return $this->db->lastInsertId();
	}

	public function deletePostLike($postid, $currentuser){
		$stmt = $this->db->prepare("DELETE from post_like WHERE post_id=? and user_name=?");
		$stmt->execute(array($postid, $currentuser));
	}


	public function countLikes($postid)
	{
		$stmt = $this->db->prepare("SELECT COUNT(*) as total from post_like WHERE post_id=?");
		$stmt->execute(array($postid));
		$countLikes = $stmt->fetch(PDO::FETCH_ASSOC);

		$toret = $countLikes["total"];

		return $toret;
	}

	public function countLikesPost(Post $post)
	{
		$stmt = $this->db->prepare("SELECT COUNT(*) as total from post_like WHERE post_id=?");
		$stmt->execute(array($post->getId()));
		$countLikes = $stmt->fetch(PDO::FETCH_ASSOC);

		$toret = $countLikes["total"];

		return $toret;
	}


	//Eliminar todos los favs asignados a un post
	public function deleteLikes(Post $post)
	{
		$stmt = $this->db->prepare("DELETE from post_like WHERE post_id=?");
		$stmt->execute(array($post->getId()));
	}
}

<?php
class  DataBase{
	protected static $db = "backlog";
	protected static $user = "root";
	protected static $pass = "";
	public static function connect(){
		$conn = mysqli_connect("localhost", self::$user, self::$pass, self::$db);
		return $conn;
	}
}
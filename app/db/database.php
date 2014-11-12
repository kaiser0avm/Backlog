<?php
class  DataBase{
	//Test info
	protected static $db = "backlog";
	protected static $user = "root";
	protected static $pass = "";
	protected static $host = "localhost";
	
	/*protected static $db = "a2390031_backlog";
	protected static $user = "a2390031_kaiser";
	protected static $pass = "dragon11";
	protected static $host = "mysql5.000webhost.com";*/
	public static function connect(){
		$conn = mysqli_connect(self::$host, self::$user, self::$pass,self::$db);
		return $conn;
	}
}
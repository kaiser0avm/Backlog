<?php
class UserController{
	public static function login($u,$p){
		$result = array();
		$conn = DataBase::connect();
		try{							
			if(!mysqli_connect_errno()){
				$user = "";
				$id;
				$stmt = mysqli_prepare($conn, "SELECT id,name FROM users WHERE name = ? AND password = ?");
				mysqli_stmt_bind_param($stmt,"ss",$u,$p);
				mysqli_stmt_bind_result($stmt,$id,$user);
				mysqli_stmt_execute($stmt);
				if($stmt){
					mysqli_stmt_fetch($stmt);
					mysqli_stmt_close($stmt);
					if($user != ""){
						$result = array("id" => $id,"error" => false,"message" => "Login Successful");
					}
					else{
						throw new Exception("User doesnt exist");
					}
				}
				else{
					throw new Exception("Something went wrong with the query");
				}
			}			
			else{
				throw new Exception("Something went wrong with the db connection");
			}
		}
		catch(Exception $e){
			$result = array("error" => true,"message" => $e->getMessage());
		}
		mysqli_close($conn);
		return $result;
	}
	
	public static function editUserAttr($u,$attr,$attrValue){
		$result = array();
		$conn = DataBase::connect();
		try{
			if(!mysqli_connect_errno()){
				$stmt = mysqli_prepare($conn, "UPDATE users SET $attr = ? WHERE name = ?");
				mysqli_stmt_bind_param($stmt,"ss",$attrValue,$u);
				mysqli_stmt_execute($stmt);
				if($stmt){					
					$result = array("error" => false,"message" => "$attr changed succesfully");
				}
				else{
					throw new Exception("Something went wrong with the query");
				}
			}
			else{
				throw new Exception("Something went wrong with the db connection");
			}
		}
		catch(Exception $e){
			$result = array("error" => true,"message" => $e->getMessage());
		}
		mysqli_close($conn);
		return $result;
	}
	
	public static function newUser($u,$p){
		$result = array();
		$conn = DataBase::connect();
		try{
			if(!mysqli_connect_errno()){
				$user = "";
				$stmt = mysqli_prepare($conn, "SELECT name FROM users WHERE name = ?");
				mysqli_stmt_bind_param($stmt,"s",$u);
				mysqli_stmt_bind_result($stmt,$user);
				mysqli_stmt_execute($stmt);
				if($stmt){
					mysqli_stmt_fetch($stmt);
					mysqli_stmt_close($stmt);
					if($user != ""){
						$result = array("error" => true,"message" => "User already exists");
					}
					else{
						$stmt = mysqli_prepare($conn, "INSERT INTO users(name,password) values(?,?)");
						mysqli_stmt_bind_param($stmt,"ss",$u,$p);
						mysqli_stmt_execute($stmt);
						if($stmt){
							$result = array("error" => false,"message"=>"User created succesfully");
						}
						else{
							throw new Exception("Something went wrong inserting a new user to the database");
						}
					}
				}
				else{
					throw new Exception("Something went wrong with the query");
				}
			}
			else{
				throw new Exception("Something went wrong with the db connection");
			}
		}
		catch(Exception $e){
			$result = array("error" => true,"message" => $e->getMessage());
		}
		mysqli_close($conn);
		return $result;
	}
}
<?php
class GameController{
	public static function getGames($idUser){
		$result = array();
		$conn = DataBase::connect();
		try{
			if(!mysqli_connect_errno()){
				$id;$name;$descripcion;$order;
				$stmt = mysqli_prepare($conn, "SELECT id,name,description,backlog_order FROM games WHERE id_user = ? AND archived = 0 ORDER BY backlog_order ASC");
				mysqli_stmt_bind_param($stmt,"i",$idUser);
				mysqli_stmt_bind_result($stmt,$id,$name,$description,$order);
				mysqli_stmt_execute($stmt);
				if($stmt){
					$games = array();
					while(mysqli_stmt_fetch($stmt)){
						$game = new Game($id,$idUser,$name,$description,$order);
						$game->setTasks(TaskController::getTasks($id));
						array_push($games,$game);
					}
					mysqli_stmt_close($stmt);
					$result = array("data"=>$games,"error"=>false,"message"=>"Date retrieved successfully");
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
	
	public static function getArchivedGames($idUser){
		$result = array();
		$conn = DataBase::connect();
		try{
			if(!mysqli_connect_errno()){
				$id;$name;$descripcion;$order;
				$stmt = mysqli_prepare($conn, "SELECT id,name,description,backlog_order FROM games WHERE id_user = ? AND archived = 1 ORDER BY backlog_order ASC");
				mysqli_stmt_bind_param($stmt,"i",$idUser);
				mysqli_stmt_bind_result($stmt,$id,$name,$description,$order);
				mysqli_stmt_execute($stmt);
				if($stmt){
					$games = array();
					while(mysqli_stmt_fetch($stmt)){
						$game = new Game($id,$idUser,$name,$description,$order);
						array_push($games,$game);
					}
					mysqli_stmt_close($stmt);
					$result = array("data"=>$games,"error"=>false,"message"=>"Date retrieved successfully");
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
	
	public static function newGame($idUser,$name,$description,$order){
		$result = array();
		$conn = DataBase::connect();
		try{
			if(!mysqli_connect_errno()){
				$game = "";
				$stmt = mysqli_prepare($conn, "SELECT name FROM games WHERE id_user = ? AND name = ?");
				mysqli_stmt_bind_param($stmt,"is",$idUser,$name);
				mysqli_stmt_bind_result($stmt,$game);
				mysqli_stmt_execute($stmt);
				if($stmt){
					mysqli_stmt_fetch($stmt);
					mysqli_stmt_close($stmt);
					if($game != ""){
						$result = array("error" => true,"message" => "User already exists");
					}
					else{
						$id;$name;$descripcion;$order;
						$stmt = mysqli_prepare($conn, "INSERT INTO games(id_user,name,description,backlog_order) VALUES(?,?,?,?)");
						mysqli_stmt_bind_param($stmt,"isss",$idUser,$name,$description,$order);
						mysqli_stmt_execute($stmt);
						mysqli_stmt_close($stmt);						
						if($stmt){
							$result = array("id" => mysqli_insert_id($conn) , "error" => false,"message" => "Game created Successfully");
						}
						else{
							throw new Exception("Something went wrong with the query");
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
	
	public static function archiveGame($idGame){
		$result = array();
		$conn = DataBase::connect();
		try{
			if(!mysqli_connect_errno()){
				$id;$name;$descripcion;$order;
				$stmt = mysqli_prepare($conn, "UPDATE games SET archived = 1  WHERE id = ?");
				mysqli_stmt_bind_param($stmt,"i",$idGame);
				mysqli_stmt_execute($stmt);
				if($stmt){
					$result = array("error" => false,"message" => "Game archived Successfully");
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
	
	public static function deArchiveGame($idGame){
		$result = array();
		$conn = DataBase::connect();
		try{
			if(!mysqli_connect_errno()){
				$id;$name;$descripcion;$order;
				$stmt = mysqli_prepare($conn, "UPDATE games SET archived = 0  WHERE id = ?");
				mysqli_stmt_bind_param($stmt,"i",$idGame);
				mysqli_stmt_execute($stmt);
				if($stmt){
					$result = array("error" => false,"message" => "Game updated Successfully");
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
	
	public static function updateGame($idGame,$idUser,$name,$description,$order){
		$result = array();
		$conn = DataBase::connect();
		try{
			if(!mysqli_connect_errno()){
				$id;$name;$descripcion;$order;
				$stmt = mysqli_prepare($conn, "UPDATE games SET name = ?,description = ?,backlog_order = ? WHERE id = ?");
				mysqli_stmt_bind_param($stmt,"ssii",$name,$description,$order,$idGame);
				mysqli_stmt_execute($stmt);
				if($stmt){
					$result = array("error" => false,"message" => "Game updated Successfully");
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
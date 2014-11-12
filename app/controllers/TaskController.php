<?php
class TaskController{
	public static function getTasks($idGame){
		$result = array();
		$conn = DataBase::connect();
		try{
			if(!mysqli_connect_errno()){
				$id;$name;$complete;
				$stmt = mysqli_prepare($conn, "SELECT id,name,complete FROM tasks WHERE id_game = ?");
				mysqli_stmt_bind_param($stmt,"i",$idGame);
				mysqli_stmt_bind_result($stmt,$id,$name,$complete);
				mysqli_stmt_execute($stmt);
				if($stmt){
					$tasks = array();
					while(mysqli_stmt_fetch($stmt)){
						$task = new Task($id,$idGame,$name,$complete.'');
						array_push($tasks,$task);
					}
					mysqli_stmt_close($stmt);
					$result = array("data"=>$tasks,"error"=>false,"message"=>"Date retrieved successfully");
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
	
	public static function newTask($idGame,$name,$complete){
		$result = array();
		$conn = DataBase::connect();
		try{
			if(!mysqli_connect_errno()){
				$id;$name;$descripcion;$order;
				$stmt = mysqli_prepare($conn, "INSERT INTO tasks(id_game,name,complete) VALUES(?,?,?)");
				mysqli_stmt_bind_param($stmt,"isi",$idGame,$name,$complete);
				mysqli_stmt_execute($stmt);
				if($stmt){
					$result = array("error" => false,"message" => "Quest added Successfully");
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
	
	public static function updateTask($idTask,$complete){
		$result = array();
		$conn = DataBase::connect();
		try{
			if(!mysqli_connect_errno()){
				$id;$name;$descripcion;$order;
				$stmt = mysqli_prepare($conn, "UPDATE tasks SET complete = ?  WHERE id = ?");
				mysqli_stmt_bind_param($stmt,"ii",$complete,$idTask);
				mysqli_stmt_execute($stmt);
				if($stmt){
					$result = array("error" => false,"message" => "Quest updated Successfully");
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
	
	public static function deleteAllTasks($idGame){
		$result = array();
		$conn = DataBase::connect();
		try{
			if(!mysqli_connect_errno()){
				$id;$name;$descripcion;$order;
				$stmt = mysqli_prepare($conn, "DELETE FROM tasks WHERE id_game = ?");
				mysqli_stmt_bind_param($stmt,"i",$idGame);
				mysqli_stmt_execute($stmt);
				if($stmt){
					$result = array("error" => false,"message" => "All Quests deleted Successfully");
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
	
	public static function deleteTask($idTask){
		$result = array();
		$conn = DataBase::connect();
		try{
			if(!mysqli_connect_errno()){
				$id;$name;$descripcion;$order;
				$stmt = mysqli_prepare($conn, "DELETE FROM tasks WHERE id = ?");
				mysqli_stmt_bind_param($stmt,"i",$idTask);
				mysqli_stmt_execute($stmt);
				if($stmt){
					$result = array("error" => false,"message" => "Quest deleted Successfully");
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
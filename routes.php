<?php
include('app/db/database.php');
include('app/models/objects.php');

header('Content-type: application/json');
$result = array();
$postdata = file_get_contents("php://input");
$request = json_decode($postdata,true);
try{
	if(isset($request["op"])){
		switch($request["op"]){
			/*
			 * Game Controller operations
			 */
			case "getGames":
				if(isset($request["iu"])){
					include('app/controllers/GameController.php');
					include('app/controllers/TaskController.php');
					$result = GameController::getGames($request["iu"]);
				}
				else{
					throw new Exception("Id User was not set on this getGames operation");
				}
				break;
			case "getArchivedGames":
				if(isset($request["iu"])){
					include('app/controllers/GameController.php');
					$result = GameController::getArchivedGames($request["iu"]);
				}
				else{
					throw new Exception("Id User was not set on this getGames operation");
				}
				break;
			case "archiveGame":
				if(isset($request["ig"])){
					include('app/controllers/GameController.php');
					$result = GameController::archiveGame($request["ig"]);
				}
				else{
					throw new Exception("Id User was not set on this getGames operation");
				}
				break;
			case "deArchiveGame":
				if(isset($request["ig"])){
					include('app/controllers/GameController.php');
					$result = GameController::deArchiveGame($request["ig"]);
				}
				else{
					throw new Exception("Id User was not set on this getGames operation");
				}
				break;
			case "newGame":
				if(isset($request["iu"]) && isset($request["n"]) && isset($request["d"]) && isset($request["o"])){
					include('app/controllers/GameController.php');
					$result = GameController::newGame($request["iu"],$request["n"],$request["d"],$request["o"]);
				}
				else{
					throw new Exception("Id User,Name,Description, or Order was not set on this newGame operation");
				}
				break;
			case "updateGame":
				if(isset($request["ig"]) && isset($request["iu"]) && isset($request["n"]) && isset($request["d"]) && isset($request["o"])){
					include('app/controllers/GameController.php');
					$result = GameController::updateGame($request["ig"],$request["iu"],$request["n"],$request["d"],$request["o"]);
				}
				else{
					throw new Exception("Id Game,Id User,Name,Description, or Order was not set on this updateGame operation");
				}
				break;
			/*
			 * Task Controller operations
			 */
			case "getTasks":
				if(isset($request["ig"])){
					include('app/controllers/TaskController.php');
					$result = TaskController::getTasks($request["ig"]);
				}
				else{
					throw new Exception("Id Game was not set on this getTasks operation");
				}
				break;
			case "newTask":
				if(isset($request["ig"]) && isset($request["n"]) && isset($request["c"])){
					include('app/controllers/TaskController.php');
					$result = TaskController::newTask($request["ig"],$request["n"],$request["c"]);
				}
				else{
					throw new Exception("Id Game,Name was not set on this newTask operation");
				}
				break;
			case "updateTask":
				if(isset($request["it"]) && isset($request["c"])){
					include('app/controllers/TaskController.php');
					$result = TaskController::updateTask($request["it"],$request["c"]);
				}
				else{
					throw new Exception("Id Task,Complete was not set on this updateTask operation");
				}
				break;
			case "deleteAllTasks":
				if(isset($request["ig"])){
					include('app/controllers/TaskController.php');
					$result = TaskController::deleteAllTasks($request["ig"]);
				}
				else{
					throw new Exception("Id Task was not set on this deleteTask operation");
				}
				break;
			case "deleteTask":
				if(isset($request["it"])){
					include('app/controllers/TaskController.php');
					$result = TaskController::deleteTask($request["it"]);
				}
				else{
					throw new Exception("Id Task was not set on this deleteTask operation");
				}
				break;
			/*
			 * User Controller operations
			 */
			case "login":
				if(isset($request["u"]) && isset($request["p"])){
					include('app/controllers/UserController.php');
					$result = UserController::login($request["u"], $request["p"]);
				}
				else{
					throw new Exception("User or Password was not set on this login operation");
				}
				break;
			case "editUserAttr":
				if(isset($request["u"]) && isset($request["attr"]) && isset($request["attrValue"])){
					include('app/controllers/UserController.php');
					$result = UserController::editUserAttr($request["u"], $request["attr"], $request["attrValue"]);
				}
				else{
					throw new Exception("User or Attr or Value was not set on this edit account operation");
				}
				break;
			case "newUser":
				if(isset($request["u"]) && isset($request["p"])){
					include('app/controllers/UserController.php');
					$result = UserController::newUser($request["u"], $request["p"]);
				}
				else{
					throw new Exception("User or Password was not set on this login operation");
				}
				break;
			default:
				throw new Exception("operation parameter was not included");
				break;
		}	
	}
	else{
		throw new Exception("operation parameter was not included");
	}
}
catch(Exception $e){
	$result = array("error"=>true,"message"=>$e->getMessage());
}

echo json_encode($result);
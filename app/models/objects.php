<?php
class Game{
	public $id;
	public $id_user;
	public $name;
	public $description;
	public $order;
	public $tasks;
	public $completedTasks;
	public function __construct($id,$id_user,$name,$description,$order){
		$this->id = $id;
		$this->id_user = $id_user;
		$this->name = $name;
		$this->description = $description;
		$this->order = $order;
	}
	
	public function setTasks($tasks){
		$this->tasks = $tasks;
		$this->completedTasks = count($tasks);
	}
}

class Task{
	public $id;
	public $id_game;
	public $name;
	public $complete;
	public function __construct($id,$id_game,$name,$complete){
		$this->id = $id;
		$this->id_game = $id_game;
		$this->name = $name;
		$this->complete = $complete;
	}
}
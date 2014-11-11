var app = angular.module('backlog', ['ui.bootstrap']);
app.service("userService",function(){
	this.user = "";
	this.id = 0;
	this.setUser = function(user){
		this.user = user;
	}
	this.setId = function(id){
		this.id = id;
	}
});
app.service("messageService",function(){
	this.message = function(type,message){
		var alertMessage = 'sa';
		switch(type){
		case 0:
			alertMessage = '<div id="alertMessage" style="position:fixed;left:40%;top:0;z-index:1100;" class="alert alert-info alert-dismissable fade in">'+
			message+
   			'<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;'+
     	'</div>';
			break;
		case 1:
			alertMessage = '<div id="alertMessage" style="position:fixed;left:40%;top:0;z-index:1100;" class="alert alert-danger alert-dismissable fade in">'+
			message+
   			'<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;'+
     	'</div>';
			break;
		}
		$('body').append(alertMessage);
		setTimeout(function(){$('#alertMessage').alert('close');},1500);
	};
});
app.directive('dynamic', function ($compile) {
	  return {
	    restrict: 'A',
	    replace: true,
	    link: function (scope, ele, attrs) {
	      scope.$watch(attrs.dynamic, function(html) {
	        ele.html(html);
	        $compile(ele.contents())(scope);
	      });
	    }
	  };
});
app.filter('startFrom', function() {
	 return function(input, start) {
	 if(input) {
		 start = +start; //parse to int
		 return input.slice(start);
	 }
	 	return [];
	 };
});
//Main Controller
app.controller('mainCrtl',function($scope,$rootScope,$http,$timeout,messageService,userService){
	/*
	 * Scope Properties and Parameters
	 */
	$scope.loadingDisplay = "none";
	$scope.$on('loadDisplay',function(event,args){
		$scope.loadingDisplay = args;
	});
	$scope.$on('mainReload',function(event,args){
		$rootScope.$broadcast('reload',"r");
	});
	$scope.content = "";
	$scope.userName = "";
	$scope.userPassword = "";
	$scope.authenticated = false;
	/*
	 * Navigation functions
	 */
	$scope.selectedDir = 'news';
	$scope.navigation = function(dir){
		switch(dir){
			case "news":
				//$scope.main = '<div><center>Raids Section should be here</center></div>';
				$http.get('app/views/news.html').success(function(data){
					$scope.content=data;
				});
				$scope.selectedDir = 'news';
				break;
			case "backlog":
				$http.get('app/views/backlog.html').success(function(data){
					$scope.content=data;
				});
				$scope.selectedDir = 'backlog';
				break;
			case "account":
				$http.get('app/views/account.html').success(function(data){
					$scope.content=data;
				});
				$scope.selectedDir = 'account';
				break;
		}
	};
	/*
	 * User Login Functions
	 */
	$scope.login = function(user,pass){
		if((user == "" || user == undefined)|| (pass == "" || pass == undefined)) messageService.message(1,"Fill the form with information");
		else{
			$scope.loadingDisplay = "block";
			$http.post("routes.php",{"op":"login","u":user.trim(),"p":pass.trim()}).success(function(data){
				if(!data.error){
					$scope.authenticated = true;
					$scope.userName = user;
					userService.setUser(user);
					userService.setId(data.id);
					$scope.userPassword = pass;
					messageService.message(0,data.message);
					$("#loginModal").modal("hide");
				}
				else{
					messageService.message(1,data.message);
					$("#loginModal").modal("hide");
				}
				$scope.loadingDisplay = "none";
			});
		}
	};
	$scope.loginCancel = function(){
		$("#loginModal").modal("hide");
	};
	$scope.logout = function(){
		$scope.authenticated = false;
		$scope.userName = "";
		$scope.navigation('news');
		$scope.user = "";
		$scope.pass = "";
		
	};
	$scope.userEdit = function(attrEdit,attr){
		if(attr == "" || undefined){
			messageService.message(1,"Fill the parameter to change your account setting");
		}
		else{
			$http.post("routes.php",{"op":"editUserAttr","u":$scope.userName,"attr":attrEdit,"attrValue":attr.trim()}).success(function(data){
				if(!data.error){
					messageService.message(0,data.message);
				}
				else{
					messageService.message(1,data.message);
				}
			});
		}
	}
	$scope.newUser = function(user,pass){
		if((user == "" || user == undefined)|| (pass == "" || pass == undefined)) messageService.message(1,"Fill the form with information");
		else{
			//console.log("user: "+user + "  pass: " + pass);
			$http.post("routes.php",{op:"newUser",u: user.trim(),p: pass.trim()}).success(function(data){
				if(!data.error){
					messageService.message(0,data.message);
					$("#loginModal").modal("hide");
				}
				else{
					messageService.message(1,data.message);
				}
			});
		}
	}
	$scope.navigation('news');
});

//Backlog Controller
app.controller('backlogCrtl',function($scope,$http,$timeout,messageService,userService){
	$scope.selectedGame = {id:0,idUser:0,name:"",description:"",order:1,tasks:[]};
	$scope.$on('reload',function(event,args){
		$http.post('routes.php',{"op":"getGames","iu":userService.id}).success(function(data){
			$scope.list = data.data;
			$scope.currentPage = 1; //current page
			$scope.entryLimit = 5; //max no of items to display in a page
			$scope.filteredItems = $scope.list.length; //Initially for no filter
			$scope.totalItems = $scope.list.length;
		});
	});
	$scope.newGame = function(){
		$scope.selectedGame = {id:0,idUser:0,name:"",description:"",order:1,tasks:[]};		
		$("#backlogModal").modal('show');
	}
	$scope.gameCancel = function(){
		$("#backlogModal").modal('hide');
	}
	$scope.archiveGame = function(gameId){
		$http.post('routes.php',{'op':'archiveGame','ig':gameId}).success(function(data){
			if(!data.error){
				messageService.message(0,data.message);
				$scope.$emit('mainReload', "r");
				$scope.reload();
			}
			else{
				messageService.message(1,data.message);
			}
		});
	}
	$scope.calculateQuestProgress = function(tasks){
		var questsCompleted = 0;
		if(tasks.length == 0){
			return 0;
		}
		else{
			for(var n = 0;n<tasks.data.length;n++){
				if(tasks.data[n].complete == "1"){
					questsCompleted++;
				}
			}
			return parseInt(((questsCompleted/tasks.data.length)*100).toString());
		}
		
	}
	$scope.gameSave = function(){
		if($scope.selectedGame.name != "" && $scope.selectedGame.description != "" && $scope.selectedGame.order != ""){
			$scope.$emit('loadDisplay', "block");
			if($scope.selectedGame.id == 0){
				$http.post('routes.php',{'op':'newGame','iu':userService.id,'n':$scope.selectedGame.name,'d':$scope.selectedGame.description,'o':$scope.selectedGame.order}).success(function(data){
					if(!data.error){
						$scope.selectedGame.id = data.id;
						$scope.taskSave(0);
					}
					else{
						messageService.message(1,data.message);
					}
				});
			}
			else{
				$http.post('routes.php',{'op':'updateGame','ig':$scope.selectedGame.id,'iu':userService.id,'n':$scope.selectedGame.name,'d':$scope.selectedGame.description,'o':$scope.selectedGame.order}).success(function(data){
					if(!data.error){
						$scope.taskSave(0);
					}
					else{
						messageService.message(1,data.message);
					}
				});
			}
		}
		else{
			messageService.message(1,"Fill the form data to save the game in the backlog");
		}
	}
	$scope.taskSave = function(index){
		if($scope.selectedGame.tasks.length > 0 && index < $scope.selectedGame.tasks.length){
			if($scope.selectedGame.tasks[index].id == 0){
				$http.post('routes.php',{'op':'newTask','ig':$scope.selectedGame.id,'n':$scope.selectedGame.tasks[index].name,'c':parseInt($scope.selectedGame.tasks[index].complete)}).success(function(data){
					$scope.taskSave(index+1);
				});
			}
			else{
				$http.post('routes.php',{'op':'updateTask','it':$scope.selectedGame.tasks[index].id,'c':parseInt($scope.selectedGame.tasks[index].complete)}).success(function(data){
					$scope.taskSave(index+1);
				});
			}
		}
		else{
			messageService.message(0,"Game saved successfully");
			$scope.$emit('loadDisplay', "none");
			$scope.reload();
			$("#backlogModal").modal('hide');
		}
	}
	$scope.deleteTask = function(task){
		if(task.id==0){
			for(var n = 0;n < $scope.selectedGame.tasks.length;n++){
				if($scope.selectedGame.tasks[n] == task){
					$scope.selectedGame.tasks.splice(n,1);
				}
			}
		}
		else{
			$http.post('routes.php',{"op":"deleteTask","it":task.id}).success(function(data){
				if(!data.error){
					messageService.message(0,data.message);
					for(var n = 0;n < $scope.selectedGame.tasks.length;n++){
						if($scope.selectedGame.tasks[n].id == task.id){
							$scope.selectedGame.tasks.splice(n,1);
						}
					}
				}
				else{
					messageService.message(1,data.message);
				}
			});
		}
	}
	$scope.addTask = function(task){
		if(task == "" || task == undefined){
			messageService.message(1,"New Quest name was not defined");
		}
		else{			
			$scope.selectedGame.tasks.push({"id":0,"idGame":userService.id,"name":task,"complete":0});
			$scope.task = "";
		}
	}
	$scope.selectGame = function(game){
		$scope.selectedGame = {id:game.id,idUser:game.idUser,name:game.name,description:game.description,order:game.order,tasks:[]};
		$http.post('routes.php',{"op":"getTasks","ig":game.id}).success(function(data){
			if(!data.error){
				$scope.selectedGame.tasks = data.data;
				$("#backlogModal").modal('show');
			}
			else{
				messageService.message(1,data.message);
			}
		});
	}
	$scope.getTasks = function(idGame){
		$http.post('routes.php',{"op":"getTasks","ig":game.id}).success(function(data){
			if(!data.error){
				$scope.selectedGame.tasks = data.data;
			}
			else{
				messageService.message(1,data.message);
			}
		});
	}
	$http.post('routes.php',{"op":"getGames","iu":userService.id}).success(function(data){
		if(data.error){
			messageService.message(1,data.message);
		}
		else{
			$scope.list = data.data;
			$scope.currentPage = 1; //current page
			$scope.entryLimit = 5; //max no of items to display in a page
			$scope.filteredItems = $scope.list.length; //Initially for no filter
			$scope.totalItems = $scope.list.length;
			$scope.reload = function(){
				$http.post('routes.php',{"op":"getGames","iu":userService.id}).success(function(data){
					$scope.list = data.data;
					$scope.currentPage = 1; //current page
					$scope.entryLimit = 5; //max no of items to display in a page
					$scope.filteredItems = $scope.list.length; //Initially for no filter
					$scope.totalItems = $scope.list.length;
				});
			};			
		}
	});
	$scope.setRaid = function(idRaided){
		$http.get('raider/raids/set/' + idRaided).success(function(data){
			if(!data.error){
				messageService.message(0,data.message);
			}
			else{
				messageService.message(1,data.message);
			}
		});
	};
	$scope.setPage = function(pageNo) {
		$scope.currentPage = pageNo;
	};
	$scope.filter = function() {
		$timeout(function() {
		$scope.filteredItems = $scope.filtered.length;
		}, 10);
	};
	$scope.sort_by = function(predicate) {
		$scope.predicate = predicate;
		$scope.reverse = !$scope.reverse;
	};
});
//Archived Controller
app.controller('archivedCrtl',function($scope,$http,$timeout,messageService,userService){	
	$scope.$on('reload',function(event,args){
		$http.post('routes.php',{"op":"getArchivedGames","iu":userService.id}).success(function(data){
			$scope.list = data.data;
			$scope.currentPage = 1; //current page
			$scope.entryLimit = 5; //max no of items to display in a page
			$scope.filteredItems = $scope.list.length; //Initially for no filter
			$scope.totalItems = $scope.list.length;
		});
	});
	$http.post('routes.php',{"op":"getArchivedGames","iu":userService.id}).success(function(data){
		if(data.error){
			messageService.message(1,data.message);
		}
		else{
			$scope.list = data.data;
			$scope.currentPage = 1; //current page
			$scope.entryLimit = 5; //max no of items to display in a page
			$scope.filteredItems = $scope.list.length; //Initially for no filter
			$scope.totalItems = $scope.list.length;
			$scope.reload = function(){
				$http.post('routes.php',{"op":"getArchivedGames","iu":userService.id}).success(function(data){
					$scope.list = data.data;
					$scope.currentPage = 1; //current page
					$scope.entryLimit = 5; //max no of items to display in a page
					$scope.filteredItems = $scope.list.length; //Initially for no filter
					$scope.totalItems = $scope.list.length;
				});
			};
			$scope.$on('reloadArchive',function(event,args){
				$scope.reload();
			});
		}
	});
	$scope.deArchiveGame = function(gameId){
		$http.post('routes.php',{'op':'deArchiveGame','ig':gameId}).success(function(data){
			if(!data.error){
				messageService.message(0,data.message);
				$scope.$emit('mainReload', "r");
				$scope.reload();
			}
			else{
				messageService.message(1,data.message);
			}
		});
	}
	$scope.acceptRaidClick = function(idRaid){
		$http.get('raider/raids/' + idRaid + '/accept').success(function(data){
			if(!data.error){
				messageService.message(0,data.message);
				$scope.reload();
			}
			else{
				messageService.message(1,data.message);
			}
		});
	};
	$scope.cancelRaidClick = function(idRaid){
		$http.get('raider/raids/' + idRaid + '/cancel').success(function(data){
			if(!data.error){
				messageService.message(0,data.message);
				$scope.reload();
			}
			else{
				messageService.message(1,data.message);
			}
		});
	};
	$scope.setPage = function(pageNo) {
		$scope.currentPage = pageNo;
	};
	$scope.filter = function() {
		$timeout(function() {
		$scope.filteredItems = $scope.filtered.length;
		}, 10);
	};
	$scope.sort_by = function(predicate) {
		$scope.predicate = predicate;
		$scope.reverse = !$scope.reverse;
	};
});
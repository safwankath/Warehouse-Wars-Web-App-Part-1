<?php

	header('Content-Type: application/json');
	$dbconn = pg_connect("host=mcsdb.utm.utoronto.ca dbname=kathmuha_309 user=kathmuha password=10559");
	//pg_prepare($dbconn,"alias","INSERT INTO test(name,email,numGamesPlayed,lastLogin) values($1,$2,$3,$4);");
	//pg_execute($dbconn, "alias", array("bob", "bob@mail", 3, "yesterday"));

	//pg_query($dbconn, "INSERT INTO test(fname, lname, username, passwd, email, numgamesplayed, lastlogin) values ('Bob', 'Smith', 'bobby', 'bob', 'bob@example.com', 0, null);");
	//echo "hello";
	$method = $_SERVER['REQUEST_METHOD'];
	$reply = array();
	parse_str(file_get_contents('php://input'), $input);
    switch ($method) {
  		case 'GET':
  			getUserInfo();
  			userExists();
  			getHighscores();
  			playerRequests();
			break;
 	    case 'PUT': # new item
 	    	addScore();
 	    	register();
 	    	challenging();
			break;
  		case 'POST': # update to existing item
  			updateInfo();
			break;
  		case 'DELETE':
			 break;
	}

	return json_encode($reply);

	function getHighScores(){
		global $dbconn;
		if(isset($_REQUEST['highscores'])){
			$result = pg_query($dbconn,'SELECT t.username,t.score FROM scores t ORDER BY t.score DESC LIMIT 10;');
			$scores = array();
			$currentScore;
			while(($currentScore = pg_fetch_array($result,null)))
				array_push($scores,array($currentScore[0],$currentScore[1]));
			print json_encode($scores);
		}
	}
	function getUserInfo(){
		global $dbconn;
		if(isset($_REQUEST['user']) && isset($_REQUEST['pass'])){
			$user = $_REQUEST['user'];
			$pass = $_REQUEST['pass'];
			pg_prepare($dbconn, 'verify', 'SELECT * from appuser where username = $1 and passwd = $2 ;');
			$result = pg_execute($dbconn,'verify',array($user,$pass));
			$status = (pg_num_rows($result) == 0? 'fail':'ok');
			$reply = array();
			//querry for the user name and database
			$reply['status'] = $status;
			if($status === 'ok'){
				$info = pg_fetch_row($result);
				$reply['firstname'] = $info[0];
				$reply['lastname'] = $info[1];
				$reply['username'] = $info[2];
				$reply['email'] = $info[4];
				$reply['gamesplayed'] = $info[5];
				$reply['score'] = $info[6];
				header($_SERVER["SERVER_PROTOCOL"]." 200 OK");
			}
				else
					header($_SERVER["SERVER_PROTOCOL"]." 403 FORBIDDEN");
			exit(json_encode($reply));
		}
	}
	function updateInfo(){
		global $dbconn;
		if(isset($_REQUEST['user']) && isset($_REQUEST['fname']) && isset($_REQUEST['lname']) && isset($_REQUEST['oldpasswd']) && isset($_REQUEST['newpasswd']) && isset($_REQUEST['email'])){
			$fname = $_REQUEST['fname'];
			$lname = $_REQUEST['lname'];
			$user = $_REQUEST['user'];
			$opass = $_REQUEST['oldpasswd'];
			$npass = $_REQUEST['newpasswd'];
			$email = $_REQUEST['email'];
			pg_prepare($dbconn, 'verifyOldPass', 'SELECT * from appuser where username = $1 and passwd = $2 ;');
			$result = pg_execute($dbconn,'verifyOldPass',array($user,$opass));
			$status = (pg_num_rows($result) == 0? 'fail':'ok');
			$reply = array();
			$reply['status'] = $status;
			if($status === 'ok'){
				pg_prepare($dbconn, 'updateProf', 'UPDATE appuser set fname=$1, lname=$2, passwd=$3, email=$4 where username=$5;');
				$result = pg_execute($dbconn,'updateProf',array($fname,$lname,$npass,$email, $user));
				header($_SERVER["SERVER_PROTOCOL"]." 200 OK");
			}
			else{
				header($_SERVER["SERVER_PROTOCOL"]." 403 FORBIDDEN");
			}
			exit(json_encode($reply));	
		}
	}
	function userExists(){
		global $dbconn;
		if(isset($_REQUEST['usernameEXIST'])){
			$user = $_REQUEST['usernameEXIST'];
			pg_prepare($dbconn, 'exist', 'SELECT username from appuser where username = $1;');
			$result = pg_execute($dbconn,'exist',array($user));
			$status = (pg_num_rows($result) == 0? 'USER DOES NOT EXIST':'USER EXISTS');
			print $status;
		}

	}
	function register(){
		global $dbconn; 
		global $input;
		if(isset($input['fname'])
		    && isset($input['lname']) 
			&&isset($input['username'])
		    && isset($input['passwd']) 
			&& isset($input['email'])){
			$fname = $input['fname'];
			$lname = $input['lname'];
			$username = $input['username'];
			$passwd =$input['passwd'];
			$email = $input['email'];
			pg_prepare($dbconn,"register","INSERT INTO appuser(fname,lname,username,passwd,email,numgamesplayed,lastlogin) values($1,$2,$3,$4,$5,0,null) ;");
			$result = pg_execute($dbconn,"register",array($fname,$lname,$username,$passwd,$email));
		}
	}

	function replace(){
		global $dbconn;
		if(isset($_REQUEST['pass'])){
			
		}
	}

	function addScore(){
		global $dbconn;
		global $input;
		if(!isset($input['score']) || !isset($input['usernameSCORE']))
			return;
		$t = time();
		$score = $input['score'];
		$username = $input['usernameSCORE'];
		$dateGo = date("Y-m-d",$t);
		pg_prepare($dbconn,"addscore", "INSERT INTO scores(username,score,scoretime) values ($1,$2,$3) ;");
		pg_execute($dbconn,"addscore", array($username, $score, $dateGo));
	}
	
	function challenging(){
		global $dbconn;
		global $input;
		if(isset($input['challenger']) && isset($input['opponent'])){				
			$challenger = $input['challenger'];
			$opponent = $input['opponent'];
			pg_prepare($dbconn,"challengeVerify", "SELECT * from appuser where username=$1 ;");
			$result= pg_execute($dbconn,"challengeVerify", array($opponent));
			$status = (pg_num_rows($result) == 0? 'fail':'ok');
			$reply = array();
			$reply['status'] = $status;
			if($status === 'ok'){
			pg_prepare($dbconn,"challenge", "INSERT INTO challenges(challenger, opponent, cscore, oscore, result) values ($1,$2,null,null,null) ;");
			$result= pg_execute($dbconn,"challenge", array($challenger,$opponent));
			header($_SERVER["SERVER_PROTOCOL"]." 200 OK");
			}
			else{
				header($_SERVER["SERVER_PROTOCOL"]." 403 FORBIDDEN");
			}
			//exit(json_encode($reply));
		}
	}
	
function playerRequests(){
		global $dbconn;
		if(isset($_REQUEST['user'])){
			$result = pg_query_params($dbconn,'SELECT challenger, cscore FROM challenges where opponent=$1;', array($_REQUEST['user']));
			$scores = array();
			$currentRequest;
			while(($currentRequest = pg_fetch_array($result,null)))
				array_push($scores,array($currentRequest[0],$currentRequest[1]));
			print json_encode($scores);
		}
	}

?>

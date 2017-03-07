//setScores();
function toggleLogin(setup,start){
	$(document).ready(
    	$('#log').click(function(e){
            if (logValidate() != false){
	        	e.preventDefault();
	        	profile(setup, start);
	        	let username = $('#uname').val();
	        	let pass = $('#psw').val();
	        	fun(username,pass,setup,start);
			}
		}))	
	//FROM THE FROM FRONTPAGE TO REGISTER
   	 $(document).ready(
    	$('#reg').click(function(e){
    		showHideLog(setup,start);
        	e.preventDefault();
        	$('#login').hide()
        	$('#register').show()

    }))

}
// show/hide login at registration page
function showHideLog(setup,start){
	// go back to login (back button)
	$(document).ready(
    	$('#backLog').click(function(e){
        	e.preventDefault();
        	$('#register').hide()
        	$('#login').show()
    }))

    // go to game (next button)
    $(document).ready(
    	$('#toGame').click(function(e){
        	e.preventDefault();
            if (regValidate() != false){
	        	//new
	        	registerRequest();
	            $('#register').hide()
	            setup();
	            start();
	            $('#game').show();
	        }
    }))
}
function logValidate(){
	
	if ($('#uname').val()== ""){
		alert( "Please provide your username!" );
        $('#uname').focus();
        return false;
	}

	if (document.logForm.psw.value == ""){
		alert( "Please provide your password!" );
        document.logForm.psw.focus() ;
        return false;
	}
}
function regValidate(){

	if ($('#fnameREG').val() == ""){
		alert( "Please provide your first name!" );
        $('#fnameREG').focus() ;
        return false;
	}

	if ($('#lnameREG').val() == ""){
		alert( "Please provide your last name!" );
        $('#lname').focus() ;
        return false;
	}

	if ($('#usernameREG').val() == ""){
		alert( "Please provide your username!" );
        $('#usernameREG').focus() ;
        return false;
	}

	if ($("#passwdREG").val() == ""){
		alert( "Please provide your password!" );
        $("#passwdREG").focus() ;
        return false;
	}

	if ($("#emailREG").val() == ""){
		alert( "Please provide your email!" );
        $("#emailREG").focus() ;
        return false;
	}

}


function registerRequest(){
	var params = { 
	method: "POST", 
	url: "api/api.php", 
	data: { "fname": $("#fnameREG").val() , "lname" : $("#lnameREG").val(), "username" : $("#usernameREG").val(),
			"passwd" : $("#passwdREG").val(), "email" : $("#emailREG").val() } 
	};
	$.ajax(params);
}

var fun = function login(user,pass,setup,start){
	$.getJSON("api/api.php", {user: user,pass: pass},
	function(data){
	if(data['status'] == 'ok'){
	    $('#login').hide();
	    setup();
	    start();
	    $('#game').show();
	}
    })

}

function setScores(){
	$.getJSON("api/api.php", {highscores: null},
	function(data){
		var table = document.getElementById("highscores");
		var i;
		for (i=0; i<data.length;i++){
		    var row = table.insertRow(i+1);
		    var cell1 = row.insertCell(0);
		    var cell2 = row.insertCell(1);
		    cell1.innerHTML = data[i][0];
		    cell2.innerHTML = data[i][1];
		}
		
		$('#welcomeLog').text('Welcome, '+data[0]);
    })
}

function profile(setup,start){
	$(document).ready(
    	$('#profile').click(function(e){
        	e.preventDefault();
        	$('#game').hide()
        	$('#prof').show()
    }))

    // go back to game (back button)
	$(document).ready(
    	$('#backGame').click(function(e){
        	e.preventDefault();
        	$('#prof').hide()
        	$('#game').show()
    }))

    // go to game (next button)
    $(document).ready(
    	$('#toGame2').click(function(e){
        	e.preventDefault();
            //if (regValidate() != false){
	        	//new
	            $('#prof').hide()
	            $('#game').show();
	        //}
    }))
}
<?php
	session_start();
	include('../assets/php/db_conec.php');
	define('USER_TIME_ZONE',date('P'));
	define('FILE_PATH','../assets/logs/');
	define('FILE_EXT','.html');
	define('POST_FILE_LOCATION','assets/php/post.php');
	date_default_timezone_set(USER_TIME_ZONE);
	$_SESSION['name'] = "Site Admin";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Admin Chat Area</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
<script type="text/javascript">
	$(document).ready(function()
	{
		$("#usermsg").click(function() {
			$.post("assets/php/mark_read.php", { id: 12 }, function(data){
				
			});
		});
	});
	
	// ALLOWS THE USER TO SUBMIT A MESSAGE
	$(document).ready(function(){
		//IF THE USER SUBMITS THE FORM
		$("#submitmsg").click(function(){	
			var clientmsg = $("#usermsg").val();
			$.post("assets/php/post.php", {text: clientmsg});				
			$("#usermsg").attr("value", "");
			return false;
		});
		
		//LOAD THAT USERS FILE THAT CONATINS THE CHAT LOG
		function loadLog(){		
			var oldscrollHeight = $("#chatbox").attr("scrollHeight") - 20;
			$.ajax({
				url: "<?php echo FILE_PATH . $_GET['username'] . FILE_EXT; ?>",
				cache: false,
				success: function(html){		
					$("#chatbox").html(html); //Insert chat log into the #chatbox div				
					var newscrollHeight = $("#chatbox").attr("scrollHeight") - 20;
					if(newscrollHeight > oldscrollHeight){
						$("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal'); //Autoscroll to bottom of div
					}				
				},
			});
		}
		setInterval (loadLog, 2500);	//Reload file every 2.5 seconds
	});
	
	//SHOW NEW FUNCTION THAT WILL ALL THE SCRIPT TO SEE IF THE ADMIN HAS REPLIED 
	function show_new() {
		$(document).ready(function() {
			$.post("assets/php/status.php", { id: 12 }, function(data) {
				if(data == '1') {
					$("#wrapper").fadeTo(100, 0.1).fadeTo(200, 1.0);
					//alert('data = 1');	
				}
			});
		});
	}
	setInterval (show_new, 2500);
	
	//SHOW NEW FUNCTION THAT WILL ALL THE SCRIPT TO SEE IF THE ADMIN HAS REPLIED 
	function msg_list() {
		$(document).ready(function() {
			$.post("assets/php/msg_list.php", { id: 12 }, function(data) {
				//if(data == '1') {
					//$("#wrapper").fadeTo(100, 0.1).fadeTo(200, 1.0);
					 //$("#uder_msgs").html(data);
					 document.getElementById("user_msgs").innerHTML=data;
					//alert(data);	
				//}
			});
		});
	}
	setInterval (msg_list, 2500);
</script>

<link type="text/css" rel="stylesheet" href="../assets/css/style.css" />
</head>

<body>
    <div id="user_msgs">
    	
    </div>
    	<?php
			if($_GET['username']) {
				session_register("username");
				$_SESSION['username'] = $_GET['username'];
				//echo $_SESSION['username'];
				
		?>
    	<div id="wrapper">
            <div id="menu">
                <p class="welcome">Welcome, <b><?php echo $_SESSION['name']; ?></b></p>
                <p class="logout"><a id="exit" href="#">Exit Chat</a></p>
                <div style="clear:both"></div>
            </div>	
            <div id="chatbox"><?php
            if(file_exists(FILE_PATH . $_GET['username'] . FILE_EXT) && filesize(FILE_PATH . $_GET['username'] . FILE_EXT) > 0){
                $handle = fopen(FILE_PATH . $_GET['username'] . FILE_EXT, "r");
                $contents = fread($handle, filesize(FILE_PATH . $_GET['username'] . FILE_EXT));
                fclose($handle);
                
                echo $contents;
            }
            ?></div>
            
            <form name="message" action="">
                <input name="usermsg" type="text" id="usermsg" onclick="loadurl('assets/php/mark_read.php')" size="63" />
                <input name="submitmsg" type="submit"  id="submitmsg" value="Send" />
            </form>
        </div>
        <?php
			} else {
				echo "Select a message";	
			}
		?>
</body>
</html>
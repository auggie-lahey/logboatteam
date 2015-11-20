<?php
	session_start();
  if (!isset($_SERVER['HTTPS']) || !$_SERVER['HTTPS']) { // if request is not secure, redirect to secure url
          $url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
           header('Location: ' . $url);
            //exit;
        }
?>
<html>
<head>
	<title>DPDBP7 - Lab 9</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</head>
<body>
        <div class="container">
                <div class="row">
                        <div class="col-md-4 col-sm-4 col-xs-3"></div>
                        <div class="col-md-4 col-sm-4 col-xs-6">
                                <h2>Sign In</h2>
                                <form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
                                        <div class="row form-group">
                                                <input class='form-control' type="text" name="username" placeholder="username">
                                        </div>
                                        <div class="row form-group">
                                                <input class='form-control' type="password" name="password" placeholder="password">
                                        </div>
                                        <div class="row form-group">
                                                <input class=" btn btn-info" type="submit" name="submit" value="Login"/>
                                                <a href="register.php" class="btn btn-danger">Register</a>
                                        </div>
                                </form>
                        </div>
                 </div>
	<?php
	if(isset($_POST['submit'])) { // Was the form submitted?
	        //Connect to the MySQL Account on Azure Server
	        $hostname = "us-cdbr-azure-central-a.cloudapp.net";
	        $username = "bd822eb15a96f3";
	        $password = "0f6ed927";
	        $dbname = "cs3380-dpdbp7";
	        $link = new mysqli($hostname, $username, $password, $dbname);
	        if ($link->connect_error) {
	                die("Connection failed: " . $link->connect_error);
	        }

		//Run the prepared statement to get user data from the table if it matches
		$sql = "SELECT * FROM user WHERE username=?";
		if ($stmt = mysqli_prepare($link, $sql)) {
			$user = $_POST['username'];
			mysqli_stmt_bind_param($stmt, "s", $user) or die("bind param");
			if(mysqli_stmt_execute($stmt)){
				$result = mysqli_stmt_get_result($stmt);
				$row = mysqli_fetch_array($result);
				//Get the salt, hashed password, and user type from the DB
				$dbsalt = $row['salt'];
				$dbhashpass = $row['hashed_password'];
				$type = $row['type'];
				
				//Salt the entered password and check that it matches
				//Send to regular user page if it is a regular account
				//Send to admin page if it is an administrator account
				$saltuserpass = $dbsalt.$_POST['password'];
				if(password_verify($saltuserpass,$dbhashpass)){
					$_SESSION["username"] = $user;
					$_SESSION["type"] = $type;
					if($type == "admin"){
						header('Location: https://cs3380-dpdbp7.cloudapp.net/lab9/welcomeadmin.php');
					} else {
						header('Location: https://cs3380-dpdbp7.cloudapp.net/lab9/welcomeuser.php');
					}
				} else {
					echo "<h3>Incorrect Password!</h3>";
				}
			} else {
				echo "<h4>Failed</h4>";
			}
		} else {
			die("prepare failed");
		}
	}
	?>
	</div>
</body>
</html>

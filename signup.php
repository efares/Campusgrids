<?php
//Connect to db
include_once "connect.php";
include_once "pictureResize.php";

$valid = true; 		// Start the sign up as valid
$validEmail = true; // Same for email
$email = "";
$password = "";

// Make sure the post variables are set
if (isset($_POST["func"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["network"]) && isset($_POST["program"])) {
	$func = $_POST["func"]; // Get the function of the call (ie addUser, addClub)

	// Get variables useful for both addUser and addClub, make sure to trim
	$email = trim(strtolower($_POST["email"]));   //strtolower makes all characters in the string lower case
	$password = trim($_POST["password"]);
	$network = trim($_POST["network"]);
	$program = trim($_POST["program"]);

	$query = "SELECT * FROM grids WHERE email=?"; // Check if email already exists in DB
	if ($stmt = $mysqli->prepare($query)) {

		// Bind the email and execute the statement
		$stmt->bind_param("s", $email);
	    $stmt->execute();

		// Store the result
	    $stmt->store_result();

	    // If the number of rows is greater than 0, than the email already exists
	    if ($stmt->num_rows > 0) {
	    	$validEmail = false;
	    }

	    // close statement
	    $stmt->close();
	}

	// If adding a user (also check for valid email)
	if ($func == "addUser" && $validEmail) {
		if (isset($_POST["fName"]) && isset($_POST["lName"])) {
			// Get name, trim and convert first character to upper case (capital)
			$fName = trim(ucfirst(strip_tags($_POST["fName"])));
			$lName = trim(ucfirst(strip_tags($_POST["lName"])));
			// Check lengths of all fields
			if (strlen($fName) == 0 || strlen($lName) == 0 || strlen($email) == 0 || strlen($password) == 0 || strlen($network) == 0 || strlen($program) == 0 || strlen($fName) > 75 || strlen($lName) > 40 || strlen($email) > 100 || strlen($password) > 75 || strlen($network) > 75 || strlen($program) > 75) {
				$valid = false;
			}
			// Add extra validation stuff here later on

			// If still valid, add user to DB
			if ($valid) {
				$password = md5($password);               // Encrypt password
				$query = "INSERT INTO grids (first_name, last_name, email, password, network, program, account_type) VALUES (?,?,?,?,?,?,'U')"; // Insert query...
        		$stmt = $mysqli->prepare($query); // Prepare the query
				$stmt->bind_param("ssssss", $fName, $lName, $email, $password, $network, $program); // Bind the variables
				$stmt->execute(); // Execute the query
				$stmt->close(); // Close statement
        
        //Insert a unique confirmation code for the new user
        $confirmationCode = md5(uniqid(rand()));  // Generate a unique confirmation code
        $confirmQuery = "INSERT INTO email_confirm (email, confirmation_code) VALUES (?,?)";
        $confirmStmt = $mysqli->prepare($confirmQuery); // Prepare the query
				$confirmStmt->bind_param("ss", $email, $confirmationCode); // Bind the variables
				$confirmStmt->execute(); // Execute the query
				$confirmStmt->close(); // Close statement
			  
        //Send email confirmation to new user
        $messageStyle = '<style>
            body{
               background-color:whitesmoke;
               font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
            }
            #message{
                background-color:white;
                font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
                -webkit-border-radius: .5em; 
                -moz-border-radius: .5em;
                border-radius: .5em;
                -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.2);
                -moz-box-shadow: 0 1px 2px rgba(0,0,0,.2);
                box-shadow: 0 1px 2px rgba(0,0,0,.2);	
                padding: 0px 0px; 
            }

            #message_logo{
                -webkit-border-top-right-radius: .5em;
                -webkit-border-top-left-radius: .5em;
                padding:1% 1%;
                -moz-border-top-right-radius: .5em;
                -moz-border-top-left-radius: .5em;
                border-top-right-radius: .5em;
                border-top-left-radius: .5em;
	            width:98.2%;
            }
            #message_body{padding: 2% 20px;}
            </style>';
        $logo = 'http://campusgrids.com/Images/Campusgrids_mainLogo.png';
        list($width, $height) = getimagesize("$logo");
        list($newWidth, $newHeight) = picResize($width, $height, 200);
        $subject = "Welcome to CampusGrids";
        $message = "
		      <html>
			      <head>
			      $messageStyle
			      </head>
			
			      <body>
			      <div id='message'>
            <!-- Message logo -->
				      <div id='message_logo'>
                  <img height='$newHeight' width='$newWidth' alt='CampusGrids' src='$logo' />
                  <br />
				      </div>
            <!-- /message logo -->
				
				    <div id='message_body'>
					    <h3 style='display:inline;'>Hello $fName, thank you for signing up to CampusGrids.</h3>
			        <br />
			        <p>You can now join your friends and favourite clubs and see their events.</p>
			        <p>Please verify your account by clicking <a target='_blank' href='http://campusgrids.com/1mobileApp/verifyEmail.php?email=$email&confirm_code=$confirmationCode'>Verify me</a></p>
					    
              <!-- Share Buttons -->
			        <a href='http://api.addthis.com/oexchange/0.8/forward/facebook/offer?pco=tbx32nj-1.0&amp;url=http%3A%2F%2Fcampusgrids.com&amp;pubid=ra-5067f30759971104' target='_blank' ><img src='http://cache.addthiscdn.com/icons/v1/thumbs/32x32/facebook.png' border='0' alt='Facebook' /></a>
			        <a href='http://api.addthis.com/oexchange/0.8/forward/twitter/offer?pco=tbx32nj-1.0&amp;url=http%3A%2F%2Fcampusgrids.com&amp;pubid=ra-5067f30759971104' target='_blank' ><img src='http://cache.addthiscdn.com/icons/v1/thumbs/32x32/twitter.png' border='0' alt='Twitter' /></a>
			        <!-- /share Buttons -->
              <br />
              
					    <span id='END_SIGNATURE'><p>Thank you,<br />Campusgrids Team<br /><b>Contact us:</b> team@campusgrids.com</p></span>
				    </div>  <!--end_message_body-->
			      </div> <!--end_message-->
			      </body>
		      </html>";
				
			      $headers = "MIME-Version: 1.0" . "\r\n";
			      $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
			      $headers .= "From: CampusGrids";
			      mail($email,$subject,$message,$headers);
      }
		}
		else { // variables not set
			$valid = false;
		}
	}
	else if ($func == "addClub" && $validEmail) { // if adding a club
		if (isset($_POST["clubName"])) {
			// Get club name, trim and convert first character to upper case (capital)
			$clubName = trim(ucfirst(strip_tags($_POST["clubName"])));
			// Check lengths of all fields
			if (strlen($clubName) == 0 || strlen($email) == 0 || strlen($password) == 0 || strlen($network) == 0 || strlen($program) == 0 || strlen($clubName) > 75 || strlen($email) > 100 || strlen($password) > 75 || strlen($network) > 75 || strlen($program) > 75) {
				$valid = false;
			}
			// Add extra validation stuff here

			// If still valid, add club to DB
			if ($valid) {
				$password = md5($password);
				$query = "INSERT INTO grids (first_name, email, password, network, program, account_type) VALUES (?,?,?,?,?,'C')"; // Insert query...
				$stmt = $mysqli->prepare($query); // Prepare the query
				$stmt->bind_param("sssss", $clubName, $email, $password, $network, $program); // Bind the variables
				$stmt->execute(); // Execute the query
				$stmt->close(); // Close statement
        
        //Insert a unique confirmation code for the new club
        $confirmationCode = md5(uniqid(rand()));  // Generate a unique confirmation code
        $confirmQuery = "INSERT INTO email_confirm (email, confirmation_code) VALUES (?,?)";
        $confirmStmt = $mysqli->prepare($confirmQuery); // Prepare the query
				$confirmStmt->bind_param("ss", $email, $confirmationCode); // Bind the variables
				$confirmStmt->execute(); // Execute the query
				$confirmStmt->close(); // Close statement
			
        //Send email confirmation to new club
        $messageStyle = '<style>
            body{
               background-color:whitesmoke;
               font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
            }
            #message{
                background-color:white;
                font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
                -webkit-border-radius: .5em; 
                -moz-border-radius: .5em;
                border-radius: .5em;
                -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.2);
                -moz-box-shadow: 0 1px 2px rgba(0,0,0,.2);
                box-shadow: 0 1px 2px rgba(0,0,0,.2);	
                padding: 0px 0px; 
            }

            #message_logo{
                -webkit-border-top-right-radius: .5em;
                -webkit-border-top-left-radius: .5em;
                padding:1% 1%;
                -moz-border-top-right-radius: .5em;
                -moz-border-top-left-radius: .5em;
                border-top-right-radius: .5em;
                border-top-left-radius: .5em;
	            width:98.2%;
            }
            #message_body{padding: 2% 20px;}
            </style>';
        $logo = 'http://campusgrids.com/Images/Campusgrids_mainLogo.png';
        list($width, $height) = getimagesize("$logo");
        list($newWidth, $newHeight) = picResize($width, $height, 200);
        $subject = "Welcome to CampusGrids";
        $message = "
		      <html>
			      <head>
			      $messageStyle
			      </head>
			
			      <body>
			      <div id='message'>
            <!-- Message logo -->
				      <div id='message_logo'>
                  <img height='$newHeight' width='$newWidth' alt='CampusGrids' src='$logo' />
                  <br />
				      </div>
            <!-- /message logo -->
				
				    <div id='message_body'>
					    <h3 style='display:inline;'>Hello $clubName, thank you for signing up to CampusGrids.</h3>
			        <br />
			        <p>Students can now join your club and see all the events you post on it.</p>
			        <p>Please verify your account by clicking <a target='_blank' href='http://campusgrids.com/1mobileApp/verifyEmail.php?email=$email&confirm_code=$confirmationCode'>Verify me</a></p>
					    
              <!-- Share Buttons -->
			        <a href='http://api.addthis.com/oexchange/0.8/forward/facebook/offer?pco=tbx32nj-1.0&amp;url=http%3A%2F%2Fcampusgrids.com&amp;pubid=ra-5067f30759971104' target='_blank' ><img src='http://cache.addthiscdn.com/icons/v1/thumbs/32x32/facebook.png' border='0' alt='Facebook' /></a>
			        <a href='http://api.addthis.com/oexchange/0.8/forward/twitter/offer?pco=tbx32nj-1.0&amp;url=http%3A%2F%2Fcampusgrids.com&amp;pubid=ra-5067f30759971104' target='_blank' ><img src='http://cache.addthiscdn.com/icons/v1/thumbs/32x32/twitter.png' border='0' alt='Twitter' /></a>
			        <!-- /share Buttons -->
              <br />
              
					    <span id='END_SIGNATURE'><p>Thank you,<br />Campusgrids Team<br /><b>Contact us:</b> team@campusgrids.com</p></span>
				    </div>  <!--end_message_body-->
			      </div> <!--end_message-->
			      </body>
		      </html>";
				
			      $headers = "MIME-Version: 1.0" . "\r\n";
			      $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
			      $headers .= "From: CampusGrids";
			      mail($email,$subject,$message,$headers);
			}
		}
		else {
			$valid = false;
		}
	}
	else {
		$valid = false;
	}
}
else {
	$valid = false;
}

// If everything is valid(user/club has been added), return the grid data
if ($valid) {
	$query = "SELECT gridid, first_name, last_name, network, account_type FROM grids WHERE email=?"; // get grid data from DB
		if ($stmt = $mysqli->prepare($query)) {

		    $stmt->bind_param("s", $email);                                       // Bind email
		    $stmt->execute();                                                     // Execute statement
		    $stmt->bind_result($gridid, $first_name, $last_name, $network, $account_type);  // bind the result

		    /* fetch values */
		    while ($stmt->fetch()) {
		    	// Create array of data
		    	$arr = array('status' => 1, 'gridid' => $gridid, 'first_name' => $first_name, 'last_name' => $last_name, 'email' => $email, 'password' => $password, 'network' => $network, 'account_type' => $account_type);
		    }

		    /* close statement */
		    $stmt->close();
		}
	setcookie("gridid", $gridid, time()+3600000);
	setcookie("password", $password, time()+3600000);
}
else {
	if (!$validEmail)                 // Email already in use.
		$arr = array('status' => -1);   // create array for email already in use error
	else
		$arr = array('status' => 0);    // create array for other error
}

$mysqli->close();                   // close the connection

echo json_encode($arr);             // echo the output (JSON) which will be interpreted in javascript as an object

?>
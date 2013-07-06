<?php
//Connect to db
include_once "connect.php";
include_once "pictureResize.php";

$valid = true; // Start the sign up as valid
$validEmail = true; // Same for email
$email = "";

// Make sure the post variables are set
if (isset($_POST["email"]) && isset($_POST["fName"]) && isset($_POST["lName"])) {
	// Get variables
	$email = trim(strtolower($_POST["email"]));   //strtolower makes all characters in the string lower case
	$fName = trim(ucfirst(strip_tags($_POST["fName"])));
	$lName = trim(ucfirst(strip_tags($_POST["lName"])));

	$query = "SELECT gridid FROM grids WHERE email=? AND first_name=? AND last_name=?"; // Check if this person does exist in DB
	if ($stmt = $mysqli->prepare($query)) {
		// Bind the email and execute the statement
		$stmt->bind_param("sss", $email, $fName, $lName);
	    $stmt->execute();

		// Store the result
	    $stmt->store_result();

	    // If the number of rows is 0, than this person never signed up
	    if ($stmt->num_rows <= 0) {
	    	$validEmail = false;
	    }

	    // close statement
	    $stmt->close();
	}

	// If adding a user (also check for valid email)
	if ($validEmail) {
				$newPassword = substr(md5(rand().rand()), 0, 7);	//Generate a random set of nubers and letters.
				$encryptedPassword = md5($newPassword);
				$query = "UPDATE grids SET password=? WHERE email=? AND first_name=? ANd last_name=? "; // Insert query...
        $stmt = $mysqli->prepare($query); // Prepare the query
				$stmt->bind_param("ssss", $encryptedPassword, $email, $fName, $lName); // Bind the variables
				$stmt->execute(); // Execute the query
				$stmt->close(); // Close statement
			  
        //Send email of new password to the user
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
        $subject = "Password Reset";
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
					    <h3 style='display:inline;'>Hello $fName, your password has been reset.</h3>
			        <br />
			        <p>You can now log in using: <b>$newPassword</b>.</p>
					    
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
	else {
		$valid = false;
	}
}
else {
	$valid = false;
}

// If everything is valid(user does exist in DB), return the user's email for confirmation
if ($valid) {
		    	// Create array of data
		    	$arr = array('status' => 1, 'email' => $email);
}
else {
	if (!$validEmail)                 // Person never signed up.
		$arr = array('status' => -1);   // create array for person never signing up.
	else
		$arr = array('status' => 0);    // create array for other error
}

$mysqli->close();                   // close the connection

echo json_encode($arr);             // echo the output (JSON) which will be interpreted in javascript as an object

?>
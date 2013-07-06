<?php
//Connect to db
include_once "connect.php";
include_once "pictureResize.php";

if (isset($_GET['confirm_code']) && isset($_GET['email'])) {
	$confirm_code = $_GET['confirm_code'];  // get the confirmation code sent from the email
  $email = $_GET['email'];                // get the user's email
  
  //Set the logo size
  $logo = 'img/mainLogo.png';
  list($width, $height) = getimagesize("$logo");
  list($newWidth, $newHeight) = picResize($width, $height, 400);
    
		// Query to get the user that is trying to confirm
		$query1 = "SELECT * FROM email_confirm WHERE email=? AND confirmation_code=?";
		if ($stmt = $mysqli->prepare($query1)) {
		    $stmt->bind_param("ss", $email, $confirm_code);   // Bind parameters
		    $stmt->execute();                                 // Execute statement
		    $stmt->bind_result($email, $confirm_code);        // Bind result variables

		    // Make sure this email/confirm_code combination exist in the database
		    $count1 = 0;
		    while ($stmt->fetch()) {
		    	$count1 += 1;                // Count users selected (should be 1)
		    }
        
		    //This user has either confirmed already or hasen't signed up yet.
		    if ($count1 != 1) {
          //Query to check if this email exists in our database
		      $query2 = "SELECT email FROM grids WHERE email=?";
		      if ($stmt = $mysqli->prepare($query2)) {
		          $stmt->bind_param("s", $email);     // Bind parameters
		          $stmt->execute();                   // Execute statement
		          $stmt->bind_result($email);         // Bind result variables

		          // Make sure this email/confirm_code combination exist in the database
		          $count2 = 0;
		          while ($stmt->fetch()) {
		    	      $count2 += 1;                // Count users selected (should be 1)
		          }
              
              //This user doesn't exist in the database
              if ($count2 != 1){
                echo "
                  <center>
                      <img height='$newHeight' width='$newWidth' alt='CampusGrids' src='$logo' />
                      <h1 style='font-size: 36px;margin: 15px;text-align: center;color: #666;vertical-align:baseline;'>Find Clubs. Find Events.</h2>
                      <p style='font-size: 1.1em;vertical-align:baseline;background:transparent;'>Sorry, the email <b>$email</b> is not registered.
                      <br />Please download the CampusGrids mobile app and sign up.</p>
                  </center>
                ";
              //This user has already confirmed their email
              }else{
                echo "
                  <center>
                      <img height='$newHeight' width='$newWidth' alt='CampusGrids' src='$logo' />
                      <h1 style='font-size: 36px;margin: 15px;text-align: center;color: #666;vertical-align:baseline;'>Find Clubs. Find Events.</h1>
                      <p style='font-size: 1.1em;vertical-align:baseline;background:transparent;'>The email <b>$email</b> has already been verified.</p>
                  </center>
                ";
              }
		    }

    //This user exists and we're going to confirm them
		}else{
          $confirmQuery = "DELETE FROM email_confirm WHERE email='$email' AND confirmation_code='$confirm_code'";
          $confirmStmt = $mysqli->prepare($confirmQuery);
		      $confirmStmt->execute();                                 // Execute statement
          echo "
            <center>
                <img height='$newHeight' width='$newWidth' alt='CampusGrids' src='$logo' />
                <h1 style='font-size: 36px;margin: 15px;text-align: center;color: #666;vertical-align:baseline;'>Find Clubs. Find Events.</h1>
                <p style='font-size: 1.1em;vertical-align:baseline;background:transparent;'>Thanks! The email <b>$email</b> has been verified.</p>
            </center>
          ";
        }
  }
  	/* close statement */
	  $stmt->close();
}

$mysqli->close();       // Close connection
?>
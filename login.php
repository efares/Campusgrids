<?php
//Connect to db
include_once "connect.php";

$valid = true; // start with valid being true

// Check if email and password post variables are set
if (isset($_POST["email"]) && isset($_POST["password"])) {
	$email = trim($_POST["email"]);             // set email var and trim
	$password = md5(trim($_POST["password"]));  // set password var, trim and encrypt using MD5
  
  if (isset($_POST["link"])){
    //Save this user's gridid before logging into the other account
    $gridid1 = $_COOKIE['gridid'];
  }
  
	// Check value lengths
	if (strlen($email) != 0 && strlen($email) < 100 && strlen($password) != 0 || strlen($password) < 100) {
		// Query to get the user being logged in
		$query = "SELECT gridid, first_name, last_name, profile_picture, network, account_type FROM grids WHERE email=? AND password=?";
		if ($stmt = $mysqli->prepare($query)) {
        
		    $stmt->bind_param("ss", $email, $password);                           // Bind parameters
		    $stmt->execute();                                                     // Execute statement
		    $stmt->bind_result($gridid, $first_name, $last_name, $profile_picture, $network, $account_type);  // Bind result variables
        	
		    /* fetch values */
		    $count = 0;
		    while ($stmt->fetch()) {
		    	$count += 1; // Count users selected
		    	// Create array with user logged in's data
		    	$arr = array('status' => 1, 'gridid' => $gridid, 'first_name' => $first_name, 'last_name' => $last_name, 'email' => $email, 'password' => $password, 'profile_picture' => $profile_picture, 'network' => $network, 'account_type' => $account_type);
                setcookie("gridid", $gridid, time()+3600000);
                setcookie("password", $password, time()+3600000);
            }
        
		    // If it is not only 1 user selected from the DB, do not login
		    if ($count != 1) {
		    	$valid = false;
		    //User is linking with another account
		    }else if (isset($_POST["link"])){
                //Store the new account's gridid
                $gridid2 = $gridid;
                //Check if these two accounts are already linked
    	            $selectQuery = "SELECT * FROM links WHERE gridid1=? AND gridid2=?";   // Check if link already exists in DB
	                if ($selectStmt = $mysqli->prepare($selectQuery)) {
		                $selectStmt->bind_param("ss", $gridid1, $gridid2);                // Bind the gridid's and execute the statement
	                    $selectStmt->execute();
	                    $selectStmt->store_result();                                      // Store the result
	                    // If the number of rows is greater than 0, then the link already exists
	                    if ($selectStmt->num_rows == 0) {
                        //Insert the new link into the database
                        $linkQuery = "INSERT INTO links (gridid1, gridid2) VALUES (?,?)"; // Insert query...
                        $linkStmt = $mysqli->prepare($linkQuery);                         // Prepare the query
		                    $linkStmt->bind_param("ss", $gridid1, $gridid2);              // Bind the variables
		                    $linkStmt->execute();                                         // Execute the query
		                    $linkStmt->close();                                           // Close statement
                      }
	                    $selectStmt->close();                                             // close statement
	                }
              }
        
		    /* close statement */
		    $stmt->close();
		}
	}
	else {
		$valid = false;
	}

}
else {
	$valid = false;
}

if (!$valid) {
	$arr = array('status' => 0);  // Create error array if not valid
}

$mysqli->close();               // Close connection

echo json_encode($arr);         // echo the output (JSON) which will be interpreted in javascript as an object

?>
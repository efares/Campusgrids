<?php
//Connect to db
include_once "connect.php";

$previouslyUpdated = false;   // Starts with user updating for the first time
$arr = array('status' => 0);        // Start with no results found
$count = 0;

// Check if the function variable is set
  if (isset($_POST["func"]) && isset($_POST["gridid"]) && isset($_POST["accountType"])) {
    // Get the function of the call (ie updateInterests, retrieveInterests)
    $func = $_POST["func"];
    $gridid = $_POST["gridid"];
    $accountType = $_POST["accountType"];

    if ($accountType == 'U') {
    	$userPhone = $_POST["userPhone"];
    	$userBio = $_POST["userBio"];
    	$birthday = $_POST["birthday"];
    	$program = $_POST["program"];
    	$gender = $_POST["gender"];

	    $query = "SELECT * FROM user_info WHERE gridid=?"; // Check if user has already edited their profile before
	    if ($stmt = $mysqli->prepare($query)) {
	      $stmt->bind_param("s", $gridid);      // Bind the email and execute the statement
	      $stmt->execute();
	      $stmt->store_result();              // Store the result
	      
	      // If the number of rows is greater than 0, than the user has updated before (no need to insert new row for them)
	      if ($stmt->num_rows > 0) {
	        $previouslyUpdated = true;
	      }
	      $stmt->close();           // close statement
	    }

	    if ($func == 'retrieveGridInfo' && $previouslyUpdated == true) {
	      $arr = array('status' => 1, 'func' => 'Retrieved' );
	    } elseif ($func == 'retrieveGridInfo' && $previouslyUpdated == false) {
	    	//The user has no Profile Settings to retrieve
	    	$arr = array('status' => 1);
	    } elseif ($func == 'updateGridInfo' && $previouslyUpdated == true) {
	    	//The user has set their Profile Settings before and they want to update their entry in the DB
			$query = "UPDATE user_info SET gender=?, phone_number=?, birthdate=?, program=?, bio=? WHERE gridid=?"; // Update query
	        $stmt = $mysqli->prepare($query); // Prepare the query
			$stmt->bind_param("ssssss", $gender, $userPhone, $birthday, $program, $userBio, $gridid); // Bind the variables
			$stmt->execute(); // Execute the query
			$stmt->close(); // Close statement
	      	$arr = array('status' => 1, 'func' => 'Updated' );
	    } elseif ($previouslyUpdated == false) {
	    	//The user has set their Profile Settings for the first time so we need to insert them into the DB
			$query = "INSERT INTO user_info (gridid, gender, phone_number, birthdate, program, bio) VALUES (?,?,?,?,?,?)"; // Insert query
	        $stmt = $mysqli->prepare($query); // Prepare the query
			$stmt->bind_param("ssssss", $gridid, $gender, $userPhone, $birthday, $program, $userBio); // Bind the variables
			$stmt->execute(); // Execute the query
			$stmt->close(); // Close statement
	    	$arr = array('status' => 1);
	    }

    } elseif ($accountType == 'C') {
    	$clubUrl = $_POST["clubUrl"];
    	$clubPhone = $_POST["clubPhone"];
    	$clubEmail = $_POST["clubEmail"];
    	$clubBio = $_POST["clubBio"];

    	$query = "SELECT * FROM club_info WHERE gridid=?"; // Check if club has already edited their profile before
	    if ($stmt = $mysqli->prepare($query)) {
	      $stmt->bind_param("s", $gridid);      // Bind the email and execute the statement
	      $stmt->execute();
	      $stmt->store_result();              // Store the result
	      
	      // If the number of rows is greater than 0, than the user has updated before (no need to insert new row for them)
	      if ($stmt->num_rows > 0) {
	        $previouslyUpdated = true;
	      }
	      $stmt->close();           // close statement
	    }

	    if ($func == 'retrieveGridInfo' && $previouslyUpdated == true) {
	      $arr = array('status' => 1, 'func' => 'Retrieved' );
	    } elseif ($func == 'retrieveGridInfo' && $previouslyUpdated == false) {
	    	//The user has no Profile Settings to retrieve
	    	$arr = array('status' => 1);
	    } elseif ($func == 'updateGridInfo' && $previouslyUpdated == true) {
	    	//The club has set their Profile Settings before and they want to update their entry in the DB
			$query = "UPDATE club_info SET bio=?, phone_number=?, email=?, website=? WHERE gridid=?"; // Update query
	        $stmt = $mysqli->prepare($query); // Prepare the query
			$stmt->bind_param("sssss", $clubBio, $clubPhone, $clubEmail, $clubUrl, $gridid); // Bind the variables
			$stmt->execute(); // Execute the query
			$stmt->close(); // Close statement
	      	$arr = array('status' => 1);
	    } elseif ($previouslyUpdated == false) {
	    	//The user has set their Profile Settings for the first time so they need to be inserted into the DB
		    $query = "INSERT INTO club_info (gridid, bio, phone_number, email, website) VALUES (?,?,?,?,?)"; // Insert query
	        $stmt = $mysqli->prepare($query); // Prepare the query
			$stmt->bind_param("sssss", $gridid, $clubBio, $clubPhone, $clubEmail, $clubUrl); // Bind the variables
			$stmt->execute(); // Execute the query
			$stmt->close(); // Close statement
	    	$arr = array('status' => 1);
	    }
    }
  }
//$mysqli->close(); // Close connection

echo json_encode($arr);

?>
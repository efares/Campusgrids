<?php
//Connect to db
include_once "connect.php";
//include_once "pictureResize.php";

$valid = true; // Start the sign up as valid
$validEvent = true; // Same for email
$eventName = "";
$arr = array('status' => 0);

// Make sure the post variables are set
if (isset($_POST["eventName"]) && isset($_POST["eventDate"]) && isset($_POST["eventTime"]) && isset($_POST["gridid"])) {
	// Get variables useful for both addUser and addClub, make sure to trim
	$eventName = trim(ucfirst(strip_tags($_POST["eventName"])));   //ucfirst makes first character in the string capital
	$eventDate = trim($_POST["eventDate"]);
	$eventTime = trim($_POST["eventTime"]);
	$gridid = trim($_POST["gridid"]);
	
	//Format the date to be compatible with the DB TIMESTAMP format
	$eventDateTime = $eventDate . ' ' . $eventTime;

	//Check if this event already exists
	$query = "SELECT * FROM events WHERE gridid=? AND event_title=? AND start_date=?"; // Check if event already exists in DB
	if ($stmt = $mysqli->prepare($query)) {
		$stmt->bind_param("sss", $gridid, $eventName, $eventDateTime);			// Bind the email and execute the statement
	    $stmt->execute();
	    $stmt->store_result();							// Store the result

	    // If the number of rows is greater than 0, than the event already exists
	    if ($stmt->num_rows > 0) {
	    	$validEvent = false;
	    }
	    $stmt->close();			                // close statement
	}
	
	// if adding an event that is not already created
	if ($validEvent) {
	// Check lengths of all fields
	if (strlen($eventName) == 0 || strlen($eventDateTime) == 0 || strlen($eventName) > 50 || strlen($eventDateTime) > 100) {
				$valid = false;
			}
			// Add extra validation stuff here

			// If still valid, add event to DB
			if ($valid) {
				$query = "INSERT INTO events (gridid, event_title, start_date) VALUES (?,?,?)"; // Insert query...
				$stmt = $mysqli->prepare($query);                               // Prepare the query
				$stmt->bind_param("sss", $gridid, $eventName, $eventDateTime);  // Bind the variables
				$stmt->execute();                                               // Execute the query
				$stmt->close();                                                 // Close statement
			}
	}
	else {
		$valid = false;
	}
}
else {
	$valid = false;
}

// If everything is valid(event has been added), return the event data
if ($valid) {
	$query = "SELECT eventid, event_title FROM events WHERE gridid=? AND event_title=? AND start_date=?"; // get event data from DB
		if ($stmt = $mysqli->prepare($query)) {

		    $stmt->bind_param("sss", $gridid, $eventName, $eventDateTime);        // Bind email
		    $stmt->execute();                                                     // Execute statement
		    $stmt->bind_result($eventid, $event_name);  // bind the result

		    /* fetch values */
		    while ($stmt->fetch()) {
		    	// Create array of data
		    	$arr = array('status' => 1, 'eventid' => $eventid, 'event_name' => $event_name);
		    }

		    /* close statement */
		    $stmt->close();
		}
}
else {
	if (!$validEvent)                 // Event already created.
		$arr = array('status' => -1);   // create array for email already in use error
	else
		$arr = array('status' => 0);    // create array for other error
}

$mysqli->close();                   // close the connection

echo json_encode($arr);             // echo the output (JSON) which will be interpreted in javascript as an object

?>
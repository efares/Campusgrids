<?php
//Connect to db
include_once "connect.php";

$arr = array('status' => 0);        // Start with no user found

// Check if gridid variable is set
if (isset($_POST["gridid"])) {
    $gridid = $_POST["gridid"];     // set gridid
    // Query to get the grid info
		$query = "SELECT first_name, last_name, network, program, account_type FROM grids WHERE gridid=?";
		if ($stmt = $mysqli->prepare($query)) {
		    $stmt->bind_param("s", $gridid);                           // Bind parameters
		    $stmt->execute();                                                     // Execute statement
		    $stmt->bind_result($first_name, $last_name, $network, $program, $account_type);  // Bind result variables
        
		    /* fetch values */
		    $count = 0;
		    while ($stmt->fetch()) {
		    	$count += 1; // Count users selected
		    	// Create array with user logged in's data
		    	$arr = array('status' => 1, 'first_name' => $first_name, 'last_name' => $last_name, 'network' => $network, 'program' => $program, 'account_type' => $account_type );
            }
    }
}

$mysqli->close(); // Close connection

echo json_encode($arr); // echo the output (JSON) which will be interpreted in javascript as an object

?>
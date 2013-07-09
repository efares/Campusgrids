<?php
//Connect to db
include_once "connect.php";

$previouslyUpdated = false;   // Starts with user updating for the first time
$arr = array('status' => 0);        // Start with no results found
$count = 0;

// Check if the function variable is set
  if (isset($_POST["func"]) && isset($_POST["gridid"])) {
    // Get the function of the call (ie updateInterests, retrieveInterests)
    $func = $_POST["func"];
    $gridid = $_POST["gridid"];

    $query = "SELECT * FROM notifications WHERE gridid=?"; // Check if user has already edited interests before
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
    
    if ($func == 'retrieveNotifications' && $previouslyUpdated == true) {
      $arr = array('status' => 1, 'func' => 'Retrieved' );
    } elseif ($func == 'retrieveNotifications' && $previouslyUpdated == false) {
      $arr = array('status' => 1, 'func' => 'Nothing to retrieve' );
    } elseif ($func == 'updateNotifications' && $previouslyUpdated == true) {
      $arr = array('status' => 1, 'func' => 'Updated' );
    } elseif ($previouslyUpdated == false) {
    	$arr = array('status' => 1, 'func' => 'First time updating' );
    }
  }
$mysqli->close(); // Close connection

echo json_encode($arr);

?>
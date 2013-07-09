<?php
//Connect to db
include_once "connect.php";

$arr = array('status' => 0);        // Start with no results found
$count = 0;

// Check if the function variable is set
  if (isset($_POST["func"]) && isset($_POST["gridid"])) {
    // Get the function of the call (ie updateInterests, retrieveInterests)
    $func = $_POST["func"];
    $gridid = $_POST["gridid"];
    
    if ($func == 'retrieveAccount') {
      $arr = array('status' => 1, 'func' => 'Retrieved' );
    } elseif ($func == 'updateAccount') {
      $arr = array('status' => 1, 'func' => 'Updated' );
    }
  }
//$mysqli->close(); // Close connection

echo json_encode($arr);

?>
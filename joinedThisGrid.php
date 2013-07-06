<?php
//Connect to db
include_once "connect.php";

$numberOfMembers = 0;                                     // Start with no events found
$arr = array('status' => 0);

// Check if date variable is set
if (isset($_POST["gridid"])) {
    $gridid = $_POST["gridid"];     // set gridid
	$arr = array('status' => 1, 'numberOfMembers' => $gridid);
}

//$mysqli->close(); // Close connection

echo json_encode($arr); // echo the output (JSON) which will be interpreted in javascript as an object

?>
<?php
//Connect to db
include_once "connect.php";

$valid = true; // start with valid being true

// Check if feedbackMessage post variable are set
if (isset($_POST["feedbackMessage"])) {
	$feedbackMessage = trim($_POST["feedbackMessage"]);             // set feedback message var and trim
  $gridid = $_POST["gridid"];
  $email = $_POST["email"];
  $firstName = $_POST["firstName"];
  $lastName = $_POST["lastName"];
  
  //Send email to Feedback Team
  $subject = "Feedback from User: " . $firstName;    //user's name
  $to = "feedback@campusgrids.com";
  $message = "FROM: " . $firstName . " " . $lastName . " (GridID: " . $gridid . ")"
    ."\r\n" . "EMAIL: " . $email
    . "\r\n \r\n" . "FEEDBACK: " . $feedbackMessage;
  $headers = "From: CampusGrids App" . "\r\n" . "Feedback: ";
	
  //mail(to,subject,message,headers,parameters) 
  mail($to,$subject,$message,$headers);
  
  $arr = array('status' => 1);
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
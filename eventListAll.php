<?php
//Connect to db
include_once "connect.php";

$arr[0] = 0;        // Start with no results found
$count = 0;
// Check if the date variables are set
  if (isset($_POST["year"]) && isset($_POST["month"]) && isset($_POST["day"]) && isset($_POST["network"])) {
    $year = trim($_POST["year"]);
    $month = trim($_POST["month"]);
    $day = trim($_POST["day"]);
    $network = trim($_POST["network"]);
    $allNetworks = 'Ottawa Area';
    
    if (strlen($month) < 2)
    	$month = "0" . $month;
      
    if (strlen($day) < 2)
    	$day = "0" . $day;
      
    $date = $mysqli->real_escape_string("%" . $year . "-" . $month . "-" . $day . "%");      // Get date term

    if ($network == $allNetworks) {
      $query = "SELECT eventid, event_title, event_picture FROM events WHERE start_date LIKE ? ORDER BY event_title";
      if ($stmt = $mysqli->prepare($query)) {
          
            $stmt->bind_param("s", $date);                                // Bind parameters
            $stmt->execute();                                            // Execute statement
            $stmt->bind_result($eventid, $event_title, $event_picture);  // Bind result variables
            
            /* fetch values */
            while ($stmt->fetch()) {
              
              //Get number of attendees in this event
              $attendeeCount = 12;
              
              // Create array with grid's data
              $arr[$count] = array('eventid' => $eventid, 'event_title' => $event_title, 'event_picture' => $event_picture, 'attendeeCount' => $attendeeCount );
              
              $count += 1; // Count events selected
            }
      }
    } else {
      $query = "SELECT eventid, event_title, event_picture FROM events WHERE (start_date LIKE ? AND event_network=?) OR (start_date LIKE ? AND event_network=?) ORDER BY event_title";
      if ($stmt = $mysqli->prepare($query)) {
          
            $stmt->bind_param("ssss", $date, $network, $date, $allNetworks);                            // Bind parameters
            $stmt->execute();                                            // Execute statement
            $stmt->bind_result($eventid, $event_title, $event_picture);  // Bind result variables
            
            /* fetch values */
            while ($stmt->fetch()) {
              
              //Get number of attendees in this event
              $attendeeCount = 12;
              
              // Create array with grid's data
              $arr[$count] = array('eventid' => $eventid, 'event_title' => $event_title, 'event_picture' => $event_picture, 'attendeeCount' => $attendeeCount );
              
              $count += 1; // Count events selected
            }
      }
    }
  }
$mysqli->close(); // Close connection

echo json_encode($arr);

?>
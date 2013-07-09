<?php
//Connect to db
include_once "connect.php";

$arr[0] = 0;        // Start with no results found
$count = 0;
/*This file is reached when the user is looking at their own calendar and click on a day that has events in it.
  Now, when the user is looking at their personal list of events on this day, they should see the list of events
  they created (already in effect that is why we send the gridid to eventList.php) and they should also see 
  the list of events that have been created by grids they have joined (this is NOT in effect yet)
*/

// Check if the date variables are set
  if (isset($_POST["year"]) && isset($_POST["month"]) && isset($_POST["day"]) && isset($_POST["gridid"])) {
    $year = trim($_POST["year"]);
    $month = trim($_POST["month"]);
    $day = trim($_POST["day"]);
    $gridid =  trim($_POST["gridid"]);
    
    if (strlen($month) < 2)
    	$month = "0" . $month;
      
    if (strlen($day) < 2)
    	$day = "0" . $day;
      
    $date = $mysqli->real_escape_string("%" . $year . "-" . $month . "-" . $day . "%");      // Get date term
    $query = "SELECT eventid, event_title, event_picture FROM events WHERE start_date LIKE ? AND gridid=? ORDER BY event_title";
    if ($stmt = $mysqli->prepare($query)) {
        
		      $stmt->bind_param("ss", $date, $gridid);                     // Bind parameters
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
$mysqli->close(); // Close connection

echo json_encode($arr);

?>
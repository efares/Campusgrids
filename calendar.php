<?php
//Connect to db
include_once "connect.php";

$numberOfEvents = 0;                                     // Start with no events found
$arr[0] = 0;

// Check if date variable is set
if (isset($_POST["year"]) && isset($_POST["month"]) && isset($_POST["gridid"])) {
    $year = trim($_POST["year"]);                                // set date
    $month = trim($_POST["month"]);
    $gridid = trim($_POST["gridid"]);
    
    if (strlen($month) < 2)
    	$month = "0" . $month;
    
    for ($day = 1; $day <= 31; $day++) {
    	$dayS = $day;
    	if (strlen($day) < 2)
    		$dayS = "0" . $day;
	    $date = $mysqli->real_escape_string("%" . $year . "-" . $month . "-" . $dayS . "%");      // Get date term
	    
	    // Query to get the events on this date
	    $query = "SELECT COUNT(*) FROM events WHERE start_date LIKE ? AND gridid=? ";
		if ($stmt = $mysqli->prepare($query)) {
			$stmt->bind_param("ss", $date, $gridid);                 // Bind parameters
			$stmt->execute();                                        // Execute statement
			$stmt->bind_result($eventid);                            // Bind result variables
	        
			// fetch values
			$stmt->bind_result($count);
			while ($stmt->fetch()) {
		        $arr[$day] = $count;
		    }
	   	}
	}
}

//$mysqli->close(); // Close connection

echo json_encode($arr); // echo the output (JSON) which will be interpreted in javascript as an object

?>
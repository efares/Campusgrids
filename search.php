<?php
//Connect to db
include_once "connect.php";

$arr;         // Start with no results found
// Check if searchTerm and switchButton variables are set
	if (isset($_POST["searchTerm"]) && isset($_POST["switchButton"])) {
		$searchTerm = trim($_POST["searchTerm"]);
    	$switchButton = trim($_POST["switchButton"]);
    	$searchTerm = $mysqli->real_escape_string("%" . $searchTerm . "%");                           // Get search term
		
    //Check if the switchButton is on Grids
    if ($switchButton == 'showGrids'){
      $query = "SELECT gridid, first_name, last_name, profile_picture, account_type FROM grids WHERE CONCAT(first_name, ' ', IFNULL(last_name,'')) LIKE ? ORDER BY first_name";
		  if ($stmt = $mysqli->prepare($query)) {
        	  
		      $stmt->bind_param("s", $searchTerm);                                                    // Bind parameters
		      $stmt->execute();                                                                       // Execute statement
		      $stmt->bind_result($gridid, $first_name, $last_name, $profile_picture, $account_type);  // Bind result variables
        	  
		      /* fetch values */
		      $count = 0;
		      while ($stmt->fetch()) {
		    	$count += 1; // Count grids found
            	// Create array with grid's data
		    	$arr[$count] = array('status' => 1, 'gridid' => $gridid, 'first_name' => $first_name, 'last_name' => $last_name, 'profile_picture' => $profile_picture, 'account_type' => $account_type);
	          }
	          $mysqli->close(); // Close connection            
              
	          for($i=1; $i<=$count; $i++){
		        //Get number of members in each grid that was found
		        /*-----------------------------------------------------------------------------------------------
	            $memberQuery = "SELECT COUNT(*) AS memberCount FROM grid_members WHERE gridid=? ";
	 		        if ($memberStmt = $mysqli->prepare($memberQuery)) {
				        $memberStmt->bind_param("s", $arr[$i]['gridid']);             // Bind parameters
				        $memberStmt->execute();                                       // Execute statement
				        $memberStmt->bind_result($countOfMembers);                    // Bind result variables
		            	
				        // fetch values
				        while ($memberStmt->fetch()) {
			                $memberCount = $countOfMembers;
			            }
		   	        }
		   	    $mysqli->close(); // Close connection
				-----------------------------------------------------------------------------------------------*/
	          	$memberCount = $arr[$i]['gridid'];
	          	//Insert this memberCount into array
	        	$memberCountArr[$i] = array('memberCount' => $memberCount);
              	$arr[$i] = array_merge($arr[$i],$memberCountArr[$i]);
	          }
		  }
    //switchButton is on Events
    }elseif ($switchButton == 'showEvents') {
      $query = "SELECT eventid, event_title, event_picture FROM events WHERE event_title LIKE ? ORDER BY event_title";
		  if ($stmt = $mysqli->prepare($query)) {
        
		      $stmt->bind_param("s", $searchTerm);                         // Bind parameters
		      $stmt->execute();                                            // Execute statement
		      $stmt->bind_result($eventid, $event_title, $event_picture);  // Bind result variables
        
		      /* fetch values */
		      $count = 0;
		      while ($stmt->fetch()) {
		    	  $count += 1; // Count events found
            	  // Create array with grid's data
		    	  $arr[$count] = array('status' => 1, 'eventid' => $eventid, 'event_title' => $event_title, 'event_picture' => $event_picture);
            }
	        $mysqli->close(); // Close connection

            for($i=1; $i<=$count; $i++){
	            //Get number of attendees in this event
	            /*----------------------------------------------------------------------------------------------
	            $attendeeQuery = "SELECT COUNT(*) FROM event_members WHERE eventid = ? ";
	 		        if ($attendeeStmt = $mysqli->prepare($attendeeQuery)) {
				        $attendeeStmt->bind_param("s", $arr[$i]['eventid']);             // Bind parameters
				        $attendeeStmt->execute();                                        // Execute statement
				        $attendeeStmt->bind_result($eventid);                            // Bind result variables
		            
				        // fetch values
				        $attendeeStmt->bind_result($countOfAttendees);
				        while ($attendeeStmt->fetch()) {
			                $attendeeCount = $countOfAttendees;
			            }
		   	        }
			    ----------------------------------------------------------------------------------------------*/
		    	$attendeeCount = $arr[$i]['eventid'];
		    	//Insert this attendeeCount into array
	        	$attendeeCountArr[$i] = array('attendeeCount' => $attendeeCount );
              	$arr[$i] = array_merge($arr[$i],$attendeeCountArr[$i]);
            }
		  }
    }
	}

if ($count > 0)
	echo json_encode($arr);

?>
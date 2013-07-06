<?php
//Connect to db
include_once "connect.php";

$arr[0] = 0;        // Start with no results found
$count = 0;
$account_type = 'C';
$allNetworks = 'Ottawa Area';

// Check if the network variable is set
  if (isset($_POST["network"])) {
    $network = trim($_POST["network"]);
      
    $query = "SELECT gridid, first_name, profile_picture FROM grids WHERE account_type=? AND network=? OR network=? ORDER BY first_name";
    if ($stmt = $mysqli->prepare($query)) {
        
		      $stmt->bind_param("sss", $account_type, $network, $allNetworks);         // Bind parameters
		      $stmt->execute();                                               // Execute statement
		      $stmt->bind_result($gridid, $first_name, $profile_picture);     // Bind result variables
          
          /* fetch values */
		      while ($stmt->fetch()) {
            
            //Get number of members in this clubs
            $memberCount = 12;
            
		    	  // Create array with grid's data
		    	  $arr[$count] = array('gridid' => $gridid, 'first_name' => $first_name, 'profile_picture' => $profile_picture, 'memberCount' => $memberCount );
            
            $count += 1; // Count clubs selected
          }
    }
  }
$mysqli->close(); // Close connection

echo json_encode($arr);

?>
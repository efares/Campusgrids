<?php
//Connect to db
include_once "connect.php";

$arr = array('status' => 0);  // Start with no results found
$count = 0;

// Check if variables are set
  if (isset($_POST["func"]) && isset($_POST["gridid"])) {
    // Get the function of the call (ie updateInterests or retrieveInterests)
    $func = $_POST["func"];
    $gridid = $_POST["gridid"];

    if ($func == 'retrieveInterests') {
      //$query = "SELECT gridid, tag_name FROM tags AS t JOIN grid_tags AS g ON t.tagid = g.tagid WHERE gridid=?";

      $arr = array('status' => 1);

    } elseif ($func == 'updateInterests') {
      $athletics = trim($_POST["athletics"]);
      $arts = trim($_POST["arts"]);
      $business = trim($_POST["business"]);
      $commerce = trim($_POST["commerce"]);
      $dance = trim($_POST["dance"]);
      $engineering = trim($_POST["engineering"]);
      $journalism = trim($_POST["journalism"]);
      $compSci = trim($_POST["compSci"]);
      $law = trim($_POST["law"]);
      $math = trim($_POST["math"]);
      $music = trim($_POST["music"]);
      $politics = trim($_POST["politics"]);
      $religion = trim($_POST["religion"]);
      $science = trim($_POST["science"]);
      $social = trim($_POST["social"]);
      $technology = trim($_POST["technology"]);
      $humanities = trim($_POST["humanities"]);
      $other = trim($_POST["other"]);

      //$query = "INSERT INTO grid_tags (gridid, tagid) SELECT ?, t.tagid FROM tags AS t WHERE t.tag_name='math' ";

      $arr = array('status' => 1);
    }
  }
//$mysqli->close(); // Close connection

echo json_encode($arr);

?>
<?php
ini_set('display_errors','On');
error_reporting(E_ALL);
//Create connection to the database

//Create an error message:
$error = "Sorry there was a problem connecting to the database";

//Connect and open the database
$mysqli = new mysqli('localhost', 'partygri_campus', 'campusgrids123', 'partygri_test');

//Check connection
if (mysqli_connect_error()) {
    die('Connect Error (' . mysqli_connect_errno() . ') '
            . mysqli_connect_error());
}
?>
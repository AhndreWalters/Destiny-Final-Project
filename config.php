<?php
/* Database credentials.*/
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'stagepass');
define('DB_PASSWORD', 'KILLKINGS41!');
define('DB_NAME', 'stsgedb');
 
/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>
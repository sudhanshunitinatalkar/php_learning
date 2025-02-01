<?php
$servername = "localhost";
$username = "sudhanshu";
$password = "1";
$dbname = "note_taking";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error)
{
    die("connection failed:" . $conn->connect_error);
}
?>
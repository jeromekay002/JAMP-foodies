<?php
    $hostname = "Localhost";
    $username = "root";
    $password = "";
    $database = "jampfoodies";

    $connect = mysqli_connect($hostname, $username, $password, $database);
    if (!$connect) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
?>
<?php

    $dbhost = "localhost";
    $dbuser = "root";
    $dbpassword = "";
    $dbname = "userdb";

    $connection = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);

    if (mysqli_connect_errno()) {
        exit("Database Connection Failed" . mysqli_connect_error());
    }

?>
<?php

$host = "localhost";
$db = "webshop";
$user = "root";
$password = "Hallo123";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $error){
   die($error->getMessage());
}


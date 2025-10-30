<?php
require("../db.php");
require("../functions.php");


// HENT ALLE ORDERS_PRODUCTS
if ($_SERVER["REQUEST_METHOD"] === "GET"  && empty($_GET["id"])) {
	$stmt = $conn->prepare("SELECT * FROM orders_products");
    try {
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
        http_response_code(500);
    }
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	header("Content-Type: application/json; charset=utf-8");
	echo json_encode($result);
}


//HENT ENKELT ORDER_PRODUCTS
if ($_SERVER["REQUEST_METHOD"] === "GET" && !empty($_GET["id"]))
 {
  	$id = (int)$_GET["id"];

    $sql = "SELECT * FROM  orders_products
			where   orders_id = :id
           ";
  
	$stmt = $conn->prepare($sql);
	$stmt->bindParam(":id", $id, PDO::PARAM_INT);

    try {
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
        http_response_code(500);
    }

	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	//var_dump($result);
	header("Content-Type: application/json; charset=utf-8");
    http_response_code(200);
	echo json_encode($result);
}

//
if ($_SERVER["REQUEST_METHOD"] === "POST") {
   $products_id =  $_POST["products_id"];
   $orders_id =  $_POST["orders_id"];
   $stmt = $conn->prepare("INSERT INTO orders_products (products_id, orders_id) VALUES(:products_id, :orders_id)");
 
   $stmt->bindParam(":products_id",  $products_id);
   $stmt->bindParam(":orders_id",  $orders_id);
 
   try {
    $stmt->execute();
    http_response_code(204);
    echo "Updated successfully";
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    http_response_code(500);
}
   
}

//DELETE is in products and PATCH is meaningless


?>
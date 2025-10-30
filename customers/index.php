<?php
require("../db.php");
require("../functions.php");

// HENT ALLE 
if ($_SERVER["REQUEST_METHOD"] === "GET"  && empty($_GET["id"])) {
    $limit = isset($_GET["limit"]) ? intval($_GET["limit"]) : 10;
	$offset = isset($_GET["offset"]) ? intval($_GET["offset"]) : 0;

	$stmt = $conn->prepare("SELECT COUNT(id) FROM customers");
	$stmt->execute();
	$count = $stmt->fetch(PDO::FETCH_ASSOC);
	
	$stmt = $conn->prepare("SELECT id, name FROM customers LIMIT :limit OFFSET :offset");
	$stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
	$stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
	try {
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
        http_response_code(500);
    }
	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

	$nextOffset = $offset + $limit;
	$prevOffset = $offset - $limit;

	$next = "http://localhost/webshop/customers?offset=$nextOffset&limit=$limit";
	$prev = "http://localhost/webshop/customers?offset=$prevOffset&limit=$limit";

	// Tilføj Hypermedia Controls
	for ($i = 0; $i < count($results); $i++) {
		$results[$i]["url"] = "http://localhost/webshop/customers?id=" . $results[$i]["id"];
		
	}

	header("Content-Type: application/json; charset=utf-8");
	$output = [
		"count" => $count["COUNT(id)"],
		"next" => $nextOffset < $count["COUNT(id)"] ? $next : null,
		"prev" => $offset <= 0 ? null : $prev,
		"results" => $results
	];

    http_response_code(200);
	echo json_encode($output);

}


//HENT ENKELT 
if ($_SERVER["REQUEST_METHOD"] === "GET" && !empty($_GET["id"]))
 {
	$id = validateID("customers");

    $sql = "SELECT * FROM customers
			where  customers.id = :id
           ";
  
	$stmt = $conn->prepare($sql);
	$stmt->bindParam(":id", $id, PDO::PARAM_INT);
	try {
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
        http_response_code(500);
    }

	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	//var_dump($result);
	header("Content-Type: application/json; charset=utf-8");
    http_response_code(200);
	echo json_encode($result);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $address =  $_POST["address"];
    $phone =  $_POST["phone"];
   
   $stmt = $conn->prepare("INSERT INTO customers (name, address, phone) VALUES(:name, :address, :phone)");
 
   $stmt->bindParam(":name", $name);
   $stmt->bindParam(":address",  $address);
   $stmt->bindParam(":phone", $phone, PDO::PARAM_INT);
 
   try {
    $stmt->execute();
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    http_response_code(500);
}

   http_response_code(201);
}


if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    header("Content-Type: application/json; charset=utf-8");
    
    $id = validateID("customers");
    try {
  
        //  Delete the customer
        $stmt = $conn->prepare("DELETE FROM customers WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        http_response_code(204);
        echo json_encode([
            "status" => "success",
            "message" => "Customer deleted successfully",
            "deleted_customer_id" => $id
        ]);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Database error: " . $e->getMessage()
        ]);
    }
}


if ($_SERVER["REQUEST_METHOD"] === "PATCH") {

   
    $id = validateID("customers");

    parse_str(file_get_contents("php://input"), $body);

    if (!is_array($body)) {
        echo "Not array";
        var_dump($body);
        http_response_code(400);
        exit;
    }

    $sql1 = "";
    $params = [];
    
    if (isset($body["name"])) {
        $sql1 .= (empty($sql1) ? "" : ", ") . "name = :name";
        $params[":name"] = $body["name"];
     }
    if (isset($body["address"])) {
        $sql1 .= (empty($sql1) ? "" : ", ") . "address = :address";
        $params[":address"] = $body["address"];
     }
    if (isset($body["phone"])) {
        $sql1 .= (empty($sql1) ? "" : ", ") . "phone = :phone";
        $params[":phone"] = $body["phone"];
    }
    if (empty($sql1)) {
        http_response_code(400);
        exit;
    }
   
    $sql2 = "UPDATE customers SET " . $sql1 . " WHERE id = :id";
    $stmt = $conn->prepare($sql2);

    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }
    try {
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
        http_response_code(500);
    }
    http_response_code(204);
}


?>
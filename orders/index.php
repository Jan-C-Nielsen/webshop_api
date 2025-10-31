<?php
require("../db.php");
require("../functions.php");


// HENT ALLE ORDERS
if ($_SERVER["REQUEST_METHOD"] === "GET"  && empty($_GET["id"])) {
    $limit = isset($_GET["limit"]) ? intval($_GET["limit"]) : 10;
	$offset = isset($_GET["offset"]) ? intval($_GET["offset"]) : 0;

	$stmt = $conn->prepare("SELECT COUNT(id) FROM orders");
	$stmt->execute();
	$count = $stmt->fetch(PDO::FETCH_ASSOC);
	
	$stmt = $conn->prepare("SELECT * FROM orders LIMIT :limit OFFSET :offset");
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

	$next = "http://localhost/webshop/orders?offset=$nextOffset&limit=$limit";
	$prev = "http://localhost/webshop/orders?offset=$prevOffset&limit=$limit";

	// Tilføj Hypermedia Controls
	for ($i = 0; $i < count($results); $i++) {
		$results[$i]["url"] = "http://localhost/webshop/orders?id=" . $results[$i]["id"];
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


//HENT ENKELT ORDER
if ($_SERVER["REQUEST_METHOD"] === "GET" && !empty($_GET["id"]))
 {
	$id = validateID("orders");

    $sql = "SELECT * FROM orders
			where  orders.id = :id
           ";
  
	$stmt = $conn->prepare($sql);
	$stmt->bindParam(":id", $id, PDO::PARAM_INT);
	$stmt->execute();

	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	//var_dump($result);
	header("Content-Type: application/json; charset=utf-8");
    http_response_code(200);
	echo json_encode($result);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

   $customer_id =  $_POST["id"];
  // echo  $customer_id;
   $stmt = $conn->prepare("INSERT INTO orders (customer_id) VALUES(:customer_id)");
   $stmt->bindParam(":customer_id",  $customer_id);
   try {
    $stmt->execute();
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    http_response_code(500);
}
   http_response_code(204);
}

if ($_SERVER["REQUEST_METHOD"] === "PATCH") {
   
    $id =  validateID("orders");

    parse_str(file_get_contents("php://input"), $body);

    if (!is_array($body)) {
        echo "Not array";
        http_response_code(400);
        exit;
    }

    $sql1 = "";
    $params = [];
   
    if (isset($body["delivered"])) {
        $sql1 .=  "delivered = :delivered";
        $params[":delivered"] = $body["delivered"];
    }

    if (empty($sql1)) {
        echo "No fields to update";
        http_response_code(400);
        exit;
    }

    $sql2 = "UPDATE orders SET " . $sql1 . " WHERE id = :id";
    $stmt = $conn->prepare($sql2);

    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }

    try {
        $stmt->execute();
        http_response_code(204);
        echo "Updated successfully";
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
        http_response_code(500);
    }

}


if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    header("Content-Type: application/json; charset=utf-8");
   
    // Require the orders_id in the query string
    if (empty($_GET["orders_id"])) {
        http_response_code(400);
        echo json_encode(["message" => "Missing 'orders_id' parameter"]);
        exit;
    }

    $orders_id = $_GET["orders_id"];

    if (!is_numeric($orders_id)) {
        http_response_code(400);
        echo json_encode(["message" => "orders_id must be numeric"]);
        exit;
    }

    $orders_id = intval($orders_id, 10);

    try {
        // Check if any rows exist for this orders_id
        $stmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE id = :orders_id");
        $stmt->bindParam(":orders_id", $orders_id, PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count == 0) {
            http_response_code(404);
            echo json_encode(["message" => "Order not found"]);
            exit;
        }

        // Delete related rows (example: order_items table)
        $stmt = $conn->prepare("DELETE FROM orders_products WHERE orders_id = :orders_id");
        $stmt->bindParam(":orders_id", $orders_id, PDO::PARAM_INT);
        $stmt->execute();

        // Optionally delete the order itself
        $stmt = $conn->prepare("DELETE FROM orders WHERE id = :orders_id");
        $stmt->bindParam(":orders_id", $orders_id, PDO::PARAM_INT);
        $stmt->execute();

        http_response_code(204);
        echo json_encode([
            "status" => "success",
            "message" => "Deleted order and related rows",
            "deleted_orders_id" => $orders_id
        ]);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Database error: " . $e->getMessage()
        ]);
    }
}

?>
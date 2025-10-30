<?php
require("../db.php");
require("../functions.php");


if ($_SERVER["REQUEST_METHOD"] === "GET" && empty($_GET["id"])) 
{

	$limit = isset($_GET["limit"]) ? intval($_GET["limit"]) : 10;
	$offset = isset($_GET["offset"]) ? intval($_GET["offset"]) : 0;

	$stmt = $conn->prepare("SELECT COUNT(id) FROM products");
	try {
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
        http_response_code(500);
    }
	$count = $stmt->fetch(PDO::FETCH_ASSOC);
	
	$stmt = $conn->prepare("SELECT id, name FROM products LIMIT :limit OFFSET :offset");
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

	$next = "http://localhost/webshop/products?offset=$nextOffset&limit=$limit";
	$prev = "http://localhost/webshop/products?offset=$prevOffset&limit=$limit";

	// Tilføj Hypermedia Controls
	for ($i = 0; $i < count($results); $i++) {
		$results[$i]["url"] = "http://localhost/webshop/products?id=" . $results[$i]["id"];
		
	}

	header("Content-Type: application/json; charset=utf-8");
	$output = [
		"count" => $count["COUNT(id)"],
		"next" => $nextOffset < $count["COUNT(id)"] ? $next : null,
		"prev" => $offset <= 0 ? null : $prev,
		"results" => $results
	];
	echo json_encode($output);
	
}

//HENT ENKELT PRODUKT
if ($_SERVER["REQUEST_METHOD"] === "GET" && !empty($_GET["id"]))
 {
	//$id = validateID();
	$id = (int)$_GET["id"];

    $sql = "SELECT products.id as id, products.name as name, description, price, weight_in_grams, url FROM products 
            LEFT JOIN products_media
            ON products.id = products_media.products_id
            LEFT JOIN media
            ON products_media.media_id = media.id
			where  products.id = :id
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

	$output = [
		"id" => $result[0]["id"],
		"name" => $result[0]["name"],
		"description" => $result[0]["description"],
		"price" => $result[0]["price"],
		"weight" => $result[0]["weight_in_grams"],
		"media" => [],
	];

	//var_dump($result["url"]);


for ($i = 0; $i < count($result); $i++) {
	$output["media"][] = $result[$i]["url"];
}

	//var_dump($output);
	header("Content-Type: application/json; charset=utf-8");
	echo json_encode($output);
}



if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $description =  $_POST["description"];
    $price =  $_POST["price"];
    $weights_in_grams =  $_POST["weights_in_grams"];

	$pics = $_POST['pic'];
	//print_r($pics);

    $stmt = $conn->prepare("INSERT INTO products (name, description, price, weight_in_grams) VALUES(:name, :description, :price, :weights_in_grams)");
 
   $stmt->bindParam(":name", $name);
   $stmt->bindParam(":description",  $description);
   $stmt->bindParam(":price", $price, PDO::PARAM_INT);
   $stmt->bindParam(":weights_in_grams", $weights_in_grams, PDO::PARAM_INT);

   try {
	$stmt->execute();
	$product_id = $conn->lastInsertId();

	// Insert each pic and link to product
	if (!empty($pics) && is_array($pics)) {
		$mediaStmt = $conn->prepare("INSERT INTO media (name, filetype, url) VALUES (:name, :filetype, :url)");
		$linkStmt = $conn->prepare("INSERT INTO products_media (products_id, media_id) VALUES (:products_id, :media_id)");
        $filetype =  $_POST["filetype"];

		foreach ($pics as $pic) {
			$mediaStmt->bindParam(":name", $name);
			$mediaStmt->bindParam(":filetype", $filetype);
			$mediaStmt->bindParam(":url", $pic);
			$mediaStmt->execute();
			$media_id = $conn->lastInsertId();

			$linkStmt->bindParam(":products_id", $product_id);
			$linkStmt->bindParam(":media_id", $media_id);
			$linkStmt->execute();
		}
	}

	http_response_code(201);
	echo json_encode(["status" => "success", "product_id" => $product_id]);

} catch (PDOException $e) {
	echo "Database error: " . $e->getMessage();
	http_response_code(500);
}

}



if ($_SERVER["REQUEST_METHOD"] === "PATCH") {
    header("Content-Type: application/json; charset=utf-8");

    // Require ID
    // if (empty($_GET["id"])) {
    //     http_response_code(400);
    //     echo json_encode(["message" => "Missing 'id' parameter"]);
    //     exit;
    // }

    // $id = $_GET["id"];

    // if (!is_numeric($id)) {
    //     http_response_code(400);
    //     echo json_encode(["message" => "ID is malformed"]);
    //     exit;
    // }

    // $id = intval($id, 10);
    
	$id = validateID("products");

    parse_str(file_get_contents("php://input"), $body);

    // Check if product exists
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$existing) {
        http_response_code(404);
        echo json_encode(["message" => "Product not found"]);
        exit;
    }

    // Build dynamic SQL only for fields provided
    $fields = [];
    $params = [":id" => $id];

    if (!empty($body["name"])) {
        $fields[] = "name = :name";
        $params[":name"] = $body["name"];
    }

    if (!empty($body["description"])) {
        $fields[] = "description = :description";
        $params[":description"] = $body["description"];
    }

    if (isset($body["price"])) {
        $fields[] = "price = :price";
        $params[":price"] = $body["price"];
    }

    if (isset($body["weight_in_grams"])) {
        $fields[] = "weight_in_grams = :weight_in_grams";
        $params[":weight_in_grams"] = $body["weight_in_grams"];
    }

    if (empty($fields)) {
        http_response_code(400);
        echo json_encode(["message" => "No fields provided to update"]);
        exit;
    }

    // Build SQL dynamically
    $sql = "UPDATE products SET " . implode(", ", $fields) . " WHERE id = :id";
    $stmt = $conn->prepare($sql);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    try {
        $stmt->execute();

        // Fetch updated record
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        http_response_code(200);
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["message" => "Database error", "error" => $e->getMessage()]);
    }
}


if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    header("Content-Type: application/json; charset=utf-8");
   
    // Require product ID in query string
	$id = validateID("products");

    if (!is_numeric($id)) {
        http_response_code(400);
        echo json_encode(["message" => "ID must be numeric"]);
        exit;
    }

    $id = intval($id, 10);

    try {
        // Check if product exists
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            http_response_code(404);
            echo json_encode(["message" => "Product not found"]);
            exit;
        }

     
        // 1Get all linked media IDs
        $stmt = $conn->prepare("SELECT media_id FROM products_media WHERE products_id = :products_id");
        $stmt->bindParam(":products_id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $media_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Delete links from product_media
        $stmt = $conn->prepare("DELETE FROM products_media WHERE products_id = :products_id");
        $stmt->bindParam(":products_id", $id, PDO::PARAM_INT);
        $stmt->execute();

        // Optionally, delete media entries (only if not linked elsewhere)
        if (!empty($media_ids)) {
            $checkStmt = $conn->prepare("SELECT COUNT(*) FROM products_media WHERE media_id = :media_id");
            $deleteStmt = $conn->prepare("DELETE FROM media WHERE id = :media_id");

            foreach ($media_ids as $media_id) {
                $checkStmt->bindParam(":media_id", $media_id, PDO::PARAM_INT);
                $checkStmt->execute();
                $count = $checkStmt->fetchColumn();

                // If this media is not used by another product, delete it
                if ($count == 0) {
                    $deleteStmt->bindParam(":media_id", $media_id, PDO::PARAM_INT);
                    $deleteStmt->execute();
                }
            }
        }

        // Delete product
        $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "message" => "Product and related media deleted",
            "deleted_product_id" => $id
        ]);

    } catch (PDOException $e) {
       
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Database error",
            "error" => $e->getMessage()
        ]);
    }
}



// REDIGER ET PRODUKT (PUT)
// if ($_SERVER["REQUEST_METHOD"] === "PUT") {
//     echo "PUT";
// 	if (empty($_GET["id"])) {
// 		http_response_code(400);
// 		exit;
// 	}
    
// 	$id = $_GET["id"];
//     parse_str(file_get_contents("php://input"), $body);
	
// 	// $stmt = $conn->prepare("UPDATE products
// 	// 		SET name = :name, description = :description, price = :price, weight_in_grams = :weight WHERE id = :id");
	
// 	if (!is_numeric($id)) {
// 		header("Content-Type: application/json; charset=utf-8");
// 		http_response_code(400);
// 		echo json_encode(["message" => "ID is malformed"]);
// 		exit;
// 	}

// 	$id = intval($id, 10);

// 	$stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
// 	$stmt->bindParam(":id", $id, PDO::PARAM_INT);
// 	$stmt->execute();
// 	$result = $stmt->fetch(PDO::FETCH_ASSOC);

// 	if (!is_array($result)) {
// 		http_response_code(404);
// 		exit;
// 	}

// 	if (empty($body["name"])) {
// 		http_response_code(400);
// 		echo json_encode(["message" => "name is missing"]);
// 		exit;
// 	}
	
// 	if (empty($body["description"])) {
// 		http_response_code(400);
// 		echo json_encode(["message" => "description is missing"]);
// 		exit;
// 	}
	
// 	if (empty($body["price"])) {
// 		http_response_code(400);
// 		echo json_encode(["message" => "price is missing"]);
// 		exit;
// 	}
	
// 	if (empty($body["weight_in_grams"])) {
// 		http_response_code(400);
// 		echo json_encode(["message" => "weight_in_grams is missing"]);
// 		exit;
// 	}
	
// 	$stmt = $conn->prepare("UPDATE products
// 			SET name = :name, description = :description, price = :price, weight_in_grams = :weight WHERE id = :id");
	
// 	$stmt->bindParam(":description", $body["description"]);
// 	$stmt->bindParam(":name", $body["name"]);
// 	$stmt->bindParam(":price", $body["price"], PDO::PARAM_INT);
// 	$stmt->bindParam(":weight", $body["weight"], PDO::PARAM_INT);
// 	$stmt->bindParam(":id", $id, PDO::PARAM_INT);
// 	$stmt->execute();

// 	$stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
// 	$stmt->bindParam(":id", $id, PDO::PARAM_INT);
// 	$stmt->execute();

// 	header("Content-Type: application/json; charset=utf-8");
// 	http_response_code(200);
// 	echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
// }

// // SLET ET PRODUKT
// if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
// 	if (empty($_GET["id"])) {
// 		http_response_code(400);
// 		exit;
// 	}

// 	$id = $_GET["id"];

// 	$stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
// 	$stmt->bindParam(":id", $id, PDO::PARAM_INT);

// 	$stmt->execute();
// 	http_response_code(204);
// }

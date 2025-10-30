<?php

function validateID($tablename) {

	global $conn;

	if (empty($_GET["id"])) {
	http_response_code(400);
	exit;
}

$id = $_GET["id"];

if (!is_numeric($id)) {
	header("Content-Type: application/json; charset=utf-8");
	http_response_code(400);
	echo json_encode(["message" => "ID is malformed"]);
	exit;
}

$id = intval($id, 10);

$stmt = $conn->prepare("SELECT id FROM " . $tablename . " WHERE id = :id");
$stmt->bindParam(":id", $id, PDO::PARAM_INT);
try {
	$stmt->execute();
} catch (PDOException $e) {
	echo "Database error: " . $e->getMessage();
	http_response_code(500);
}
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!is_array($result)) {
	http_response_code(404);
	exit;
}
return $id;
}



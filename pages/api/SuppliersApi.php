<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config.php';

function getSuppliers($conn) {
  $sql = "SELECT * FROM suppliers";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  return [];
}
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $suppliers = getSuppliers($conn);
        echo json_encode($suppliers);
        exit;
}
?>
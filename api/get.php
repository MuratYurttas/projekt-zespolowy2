<?php
require __DIR__ . "/_lib.php";
$email = $_GET["email"] ?? "";
$db = load_db();
foreach($db as $row){
  if ($row["email"] === $email){ json_response(200, $row); }
}
json_response(404, error_format(404, []));

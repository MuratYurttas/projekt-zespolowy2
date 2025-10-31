<?php
require __DIR__ . "/_lib.php";
require_token();
$email = $_GET["email"] ?? "";
$db = load_db();
$found = false;
$new = [];
foreach($db as $row){
  if ($row["email"] === $email){ $found = true; continue; }
  $new[] = $row;
}
if (!$found){ json_response(404, error_format(404, [])); }
save_db($new);
http_response_code(204);

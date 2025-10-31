<?php
require __DIR__ . "/_lib.php";

$data = $_POST;
$errors = [];

if (!isset($data["name"]) || strlen(trim($data["name"])) < 3 || strlen(trim($data["name"])) > 50)
  $errors[] = field_error("name","INVALID_LENGTH","Imię: 3–50 znaków.");

if (!filter_var($data["email"] ?? "", FILTER_VALIDATE_EMAIL))
  $errors[] = field_error("email","INVALID_FORMAT","Niepoprawny email");

if (!isset($data["price"]) || !is_numeric($data["price"]) || $data["price"] <= 0)
  $errors[] = field_error("price","INVALID_VALUE","Cena musi być > 0");

if (!isset($data["birthDate"]) || ($data["birthDate"] > date("Y-m-d")))
  $errors[] = field_error("birthDate","INVALID_DATE","Data w przyszłości");

if (!isset($data["code"]) || !preg_match('/^[A-Za-z0-9-]{4,20}$/', $data["code"]))
  $errors[] = field_error("code","INVALID_FORMAT","Kod 4–20, litery/cyfry/myślnik");

if ($errors){ json_response(400, error_format(400, $errors)); }

$db = load_db();
foreach($db as $row){
  if ($row["email"] === $data["email"] || $row["code"] === $data["code"]){
    json_response(409, error_format(409, [field_error("email","DUPLICATE","Duplikat danych")]));
  }
}

$db[] = [
  "name"=>trim($data["name"]),
  "email"=>$data["email"],
  "price"=>0 + $data["price"],
  "birthDate"=>$data["birthDate"],
  "code"=>$data["code"]
];
save_db($db);

json_response(201, ["message"=>"Created"]);

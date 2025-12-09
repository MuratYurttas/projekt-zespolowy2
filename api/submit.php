<?php
require __DIR__ . "/_lib.php";

// 🔐 Token kontrolü — sadece GitHub Actions için aktif
if (!isset($_SERVER['HTTP_HOST']) || $_SERVER['HTTP_HOST'] !== 'localhost') {
    $headers = getallheaders();
    $auth = $headers['Authorization'] ?? '';

    if ($auth !== 'Bearer SECRET123') {
        json_response(401, [
            "error" => "Brak tokena lub niepoprawny token",
            "code" => "UNAUTHORIZED"
        ]);
        exit;
    }
}

$data = $_POST;
$errors = [];

// 🔎 Walidacja pól
if (!isset($data["name"]) || strlen(trim($data["name"])) < 3 || strlen(trim($data["name"])) > 50)
  $errors[] = field_error("name", "INVALID_LENGTH", "Imię: 3–50 znaków.");

if (!filter_var($data["email"] ?? "", FILTER_VALIDATE_EMAIL))
  $errors[] = field_error("email", "INVALID_FORMAT", "Niepoprawny email");

if (!isset($data["price"]) || !is_numeric($data["price"]) || $data["price"] <= 0)
  $errors[] = field_error("price", "INVALID_VALUE", "Cena musi być > 0");

if (!isset($data["birthDate"]) || ($data["birthDate"] > date("Y-m-d")))
  $errors[] = field_error("birthDate", "INVALID_DATE", "Data w przyszłości");

if (!isset($data["code"]) || !preg_match('/^[A-Za-z0-9-]{4,20}$/', $data["code"]))
  $errors[] = field_error("code", "INVALID_FORMAT", "Kod 4–20, litery/cyfry/myślnik");

// ✅ Eğer password varsa → HASH'le
if (isset($data["password"]) && strlen($data["password"]) > 0) {
  $data["password"] = password_hash($data["password"], PASSWORD_DEFAULT);
}

if ($errors){
  json_response(400, error_format(400, $errors));
  exit;
}

// 🔁 Duplikat kontrolü
$db = load_db();
foreach($db as $row){
  if ($row["email"] === $data["email"] || $row["code"] === $data["code"]){
    json_response(409, error_format(409, [
      field_error("email", "DUPLICATE", "Duplikat danych")
    ]));
    exit;
  }
}

// 💾 Kayıt
$record = [
  "name" => trim($data["name"]),
  "email" => $data["email"],
  "price" => 0 + $data["price"],
  "birthDate" => $data["birthDate"],
  "code" => $data["code"]
];

// ✅ Eğer password geldiyse DB’ye ekle
if (isset($data["password"])) {
  $record["password"] = $data["password"];
}

$db[] = $record;
save_db($db);

// ✅ Başarılı
json_response(201, ["message" => "Created"]);

<?php
function json_response($status, $payload){
  http_response_code($status);
  header('Content-Type: application/json');
  echo json_encode($payload, JSON_UNESCAPED_UNICODE);
  exit;
}
function field_error($field,$code,$msg){
  return ["field"=>$field,"code"=>$code,"message"=>$msg];
}
function error_format($status,$errors){
  return [
    "timestamp" => gmdate("Y-m-d\TH:i:s\Z"),
    "status" => $status,
    "error" => http_response_text($status),
    "fieldErrors" => $errors
  ];
}
function http_response_text($code){
  $map=[400=>"Bad Request",401=>"Unauthorized",403=>"Forbidden",404=>"Not Found",409=>"Conflict",422=>"Unprocessable Entity"];
  return $map[$code] ?? "Error";
}
function load_db(){
  $p = __DIR__ . "/../data/db.json";
  if(!file_exists($p)) file_put_contents($p,"[]");
  return json_decode(file_get_contents($p), true);
}
function save_db($arr){
  $p = __DIR__ . "/../data/db.json";
  file_put_contents($p, json_encode($arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
}
function require_token(){
  $headers = getallheaders();
  $auth = $headers['Authorization'] ?? "";
  if(!$auth){ json_response(401, error_format(401, [])); }
  // Beklenen sabit token:
  $expected = "Bearer SECRET123";
  if($auth !== $expected){ json_response(403, error_format(403, [])); }
}

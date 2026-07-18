<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$dataFile = __DIR__.'/data/overrides.json';
$dataDir  = __DIR__.'/data/';

if(!is_dir($dataDir)) mkdir($dataDir, 0755, true);

if($_SERVER['REQUEST_METHOD'] === 'GET'){
  if(file_exists($dataFile)){
    echo file_get_contents($dataFile);
  } else {
    echo '{}';
  }
  exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $body = file_get_contents('php://input');
  $data = json_decode($body, true);
  if($data === null){
    http_response_code(400);
    echo json_encode(['ok'=>false,'error'=>'JSON invalido']);exit;
  }
  // Merge with existing (don't overwrite unrelated keys)
  $existing = [];
  if(file_exists($dataFile)){
    $existing = json_decode(file_get_contents($dataFile), true) ?: [];
  }
  foreach($data as $key => $val){
    $existing[$key] = $val;
  }
  file_put_contents($dataFile, json_encode($existing, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
  echo json_encode(['ok'=>true]);
  exit;
}

http_response_code(405);
echo json_encode(['ok'=>false,'error'=>'Method not allowed']);

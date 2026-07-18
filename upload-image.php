<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$allowed = ['image/jpeg','image/png','image/webp','image/gif'];
$maxSize = 5 * 1024 * 1024; // 5MB

if($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['img'])){
  echo json_encode(['ok'=>false,'error'=>'No file']);exit;
}

$f = $_FILES['img'];
if(!in_array($f['type'], $allowed)){
  echo json_encode(['ok'=>false,'error'=>'Tipo no permitido']);exit;
}
if($f['size'] > $maxSize){
  echo json_encode(['ok'=>false,'error'=>'Archivo muy grande (max 5MB)']);exit;
}

$dir = __DIR__.'/product-images/';
if(!is_dir($dir)) mkdir($dir, 0755, true);

$ext = pathinfo($f['name'], PATHINFO_EXTENSION);
$name = preg_replace('/[^a-z0-9_-]/','', strtolower(pathinfo($f['name'],PATHINFO_FILENAME)));
$filename = $name.'_'.uniqid().'.'.$ext;
$dest = $dir.$filename;

if(!move_uploaded_file($f['tmp_name'], $dest)){
  echo json_encode(['ok'=>false,'error'=>'Error al guardar']);exit;
}

$url = (isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']==='on'?'https':'http')
  .'://'.$_SERVER['HTTP_HOST'].'/product-images/'.$filename;

echo json_encode(['ok'=>true,'url'=>$url]);

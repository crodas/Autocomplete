<?php

require __DIR__ . "/../vendor/autoload.php";

use crodas\Autocomplete\Autocomplete;
use crodas\Autocomplete\PDOConn;

$pdo     = new PDO("sqlite:data.db");
$conn    = new PDOConn($pdo);
$suggest = new Autocomplete($conn);

$words = [];
foreach ($suggest->suggest($_GET['term']) as $word) {
    $words[] = $word['word'];
}

echo json_encode($words);

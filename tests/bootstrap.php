<?php

require __DIR__ . "/../vendor/autoload.php";

use crodas\Autocomplete\PDOConn;

$pdo = new PDO('sqlite:' . __DIR__ . '/data.db');
try {
    $conn = new PDOConn($pdo);
    $conn->install();
} catch (\Exception $e) {
}

<?php

require __DIR__ . "/../vendor/autoload.php";

use crodas\Autocomplete\Autocomplete;
use crodas\Autocomplete\PDOConn;

$file = __DIR__ . '/data.db';

if (!is_file($file)) {
    $install = true;
}

// create PDO connection
$pdo = new PDO("sqlite:$file");
// Create transaction, speed up things
$pdo->beginTransaction();
    

// create Autocomplete DBInterface
$conn = new PDOConn($pdo);

// Create suggest object
$suggest = new Autocomplete($conn);

if (!empty($install)) {
    $conn->install();
}

$suggest->index("PHP", 999);
$suggest->index("Python", 93);
$suggest->index("Perl", 83);
$suggest->index("Ruby", 74);
$suggest->index("PHP Programming ", 600);
$suggest->index("Python Programming ", 500);
$suggest->index("Perl Programming ", 400);

$pdo->commit();

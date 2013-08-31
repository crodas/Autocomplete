<?php

require __DIR__ . "/../vendor/autoload.php";

use crodas\Autocomplete\DBInterface;

class DB implements DBInterface
{
    protected $pdo;
    protected $prep;

    public function __construct (PDO $pdo)
    {
        $this->pdo  = $pdo;
        $this->prep = $pdo->prepare("REPLACE INTO autocomplete VALUES(?, ?, ?)");
    }

    public function save($text, Array $ngrams, $weight)
    {
        foreach ($ngrams as $ngram) {
            $this->prep->execute([$text, $ngram, $weight]);
        }
    }

    public function get($text)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM autocomplete WHERE ngram = ? ORDER BY weight DESC LIMIT 10");
        $stmt->execute([$text]);
        return $stmt->fetchAll();
    }
}

$pdo = new PDO('sqlite:' . __DIR__ . '/data.db');
try {
    $pdo->exec("CREATE TABLE autocomplete (word varchar(50), ngram varchar(10), weight)");
    $pdo->exec("CREATE UNIQUE INDEX filter ON autocomplete (word, ngram)");
    $pdo->exec("CREATE INDEX sort ON autocomplete (ngram, weight DESC)");
} catch (\Exception $e) {
}

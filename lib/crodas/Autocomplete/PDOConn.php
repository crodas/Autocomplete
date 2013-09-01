<?php
/*
  +---------------------------------------------------------------------------------+
  | Copyright (c) 2013 César D. Rodas                                               |
  +---------------------------------------------------------------------------------+
  | Redistribution and use in source and binary forms, with or without              |
  | modification, are permitted provided that the following conditions are met:     |
  | 1. Redistributions of source code must retain the above copyright               |
  |    notice, this list of conditions and the following disclaimer.                |
  |                                                                                 |
  | 2. Redistributions in binary form must reproduce the above copyright            |
  |    notice, this list of conditions and the following disclaimer in the          |
  |    documentation and/or other materials provided with the distribution.         |
  |                                                                                 |
  | 3. All advertising materials mentioning features or use of this software        |
  |    must display the following acknowledgement:                                  |
  |    This product includes software developed by César D. Rodas.                  |
  |                                                                                 |
  | 4. Neither the name of the César D. Rodas nor the                               |
  |    names of its contributors may be used to endorse or promote products         |
  |    derived from this software without specific prior written permission.        |
  |                                                                                 |
  | THIS SOFTWARE IS PROVIDED BY CÉSAR D. RODAS ''AS IS'' AND ANY                   |
  | EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED       |
  | WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE          |
  | DISCLAIMED. IN NO EVENT SHALL CÉSAR D. RODAS BE LIABLE FOR ANY                  |
  | DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES      |
  | (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;    |
  | LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND     |
  | ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT      |
  | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS   |
  | SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE                     |
  +---------------------------------------------------------------------------------+
  | Authors: César Rodas <crodas@php.net>                                           |
  +---------------------------------------------------------------------------------+
*/
namespace crodas\Autocomplete;

class PDOConn implements DBInterface
{
    protected $pdo;
    protected $prep;

    public function __construct (\PDO $pdo)
    {
        $this->pdo  = $pdo;
        $this->prep = $pdo->prepare("REPLACE INTO autocomplete VALUES(?, ?, ?)");
    }

    public function install()
    {
        $pdo = $this->pdo;
        $pdo->exec("create table autocomplete (word varchar(50), ngram varchar(10), weight)");
        $pdo->exec("create unique index filter on autocomplete (word, ngram)");
        $pdo->exec("create index sort on autocomplete (ngram, weight desc)");
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

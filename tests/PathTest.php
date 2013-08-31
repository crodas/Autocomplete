<?php

use crodas\Autocomplete\Autocomplete;

class PathTest extends \phpunit_framework_testcase
{
    public function testLearn()
    {
        global $pdo;
        $ac = new Autocomplete(new DB($pdo));
        $pdo->beginTransaction();
        foreach(file(__DIR__ . '/data/brit-a-z.txt') as $word) {
            $ac->index($word, 100-strlen($word));
        }
        $ac->index('Something pretty large', 999);
        $pdo->commit();
    }

    /** @dependsOn testLearn */
    public function testMultiword()
    {
        global $pdo;
        $ac = new Autocomplete(new DB($pdo));
        $words = $ac->suggest('lar');
        $this->assertEquals($words[0]['word'], 'something pretty large');
    }

    /** @dependsOn testLearn */
    public function testSpeed() 
    {
        global $pdo;
        $ac = new Autocomplete(new DB($pdo));
        $time = microtime(true);
        $this->assertEquals($ac->suggest('Fath'), $ac->suggest('FATH'));
        $this->assertTrue(microtime(true)-$time < 0.01);
        $time = microtime(true);
        $this->assertEquals([], $ac->suggest('xxxx'));
        $this->assertTrue(microtime(true)-$time < 0.01);
    }

    /** @dependsOn testLearn */
    public function testCaseSearch() 
    {
        global $pdo;
        $ac = new Autocomplete(new DB($pdo));
        $this->assertEquals($ac->suggest('Fath'), $ac->suggest('FATH'));
    }
}

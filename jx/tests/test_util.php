<?php

require '../util.php';

class MiscClass {
    public $a = null;
    public $b = null;
}

class Util_Test extends PHPUnit_Framework_TestCase
{
    public function test_isAssoc()
    {
        $this->assertEquals(isAssoc(array('one' => 1, 'two' => 2)), true);
        $this->assertEquals(isAssoc(array(3, 4, 5)), false);
        $this->assertEquals(isAssoc(range('a', '~')), false);
        $this->assertEquals(isAssoc(array()), true);
        $this->assertEquals(isAssoc(array(1, 2, 'one' => 1, 'two' => 2)), true);
    }

    public function test_isScalar()
    {
        $this->assertEquals(isScalar(1), true);
        $this->assertEquals(isScalar(22), true);
        $this->assertEquals(isScalar(44.4), true);
        $this->assertEquals(isScalar("words"), true);
        $this->assertEquals(isScalar(""), true);
        $this->assertEquals(isScalar(true), true);
        $this->assertEquals(isScalar(false), true);
        $this->assertEquals(isScalar(array('one' => 1, 'two' => 2)), false);
        $this->assertEquals(isScalar(new MiscClass()), false);
    }

    public function test_properties()
    {
        $o = new MiscClass();
        $this->assertEquals(count(properties($o)), 2);
        $this->assertEquals(properties($o, true), "a, b");
    }

    public function test_generateRandomString()
    {
        $rs = generateRandomString();
        $this->assertEquals(gettype($rs), string);
        $this->assertEquals(strlen($rs), 10);

        $length = rand(1, 100);
        $r2 = generateRandomString($length);
        $this->assertEquals(gettype($r2), string);
        $this->assertEquals(strlen($r2), $length);
    }
}

?>

<?php

require_once '../util.php';

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

    public function test_properties()
    {
        $o = new MiscClass();
        $this->assertEquals(count(properties($o)), 2);
        $this->assertEquals(properties($o, true), "a, b");
    }

    public function test_startsWith()
    {
        $this->assertEquals(startsWith("a string", "a"), true);
        $this->assertEquals(startsWith("a string", "a "), true);
        $this->assertEquals(startsWith("a string", "a str"), true);
        $this->assertEquals(startsWith("a string", "a string"), true);
        $this->assertEquals(startsWith("a string", "a string and more"), false);
        $this->assertEquals(startsWith("a string", ""), true);
    }

    public function test_endsWith()
    {
        $this->assertEquals(endsWith("a string", "g"), true);
        $this->assertEquals(endsWith("a string", "ng"), true);
        $this->assertEquals(endsWith("a string", "string"), true);
        $this->assertEquals(endsWith("a string", " string"), true);
        $this->assertEquals(endsWith("a string", "a string"), true);
        $this->assertEquals(endsWith("a string", "more than a string"), false);
        $this->assertEquals(endsWith("a string", ""), true);
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

    public function test_pathparts() {
        $this->assertEquals(pathparts("/x/y/[0]"), array('x', 'y', '[0]'));
        $this->assertEquals(pathparts("x/y/[0]"), array('x', 'y', '[0]'));
        $this->assertEquals(pathparts("/x/y/[0]/a/b/c/[1]"), array('x', 'y', '[0]','a','b','c','[1]'));

    }

    public function test_makepath() {
        $this->assertEquals(makepath(array()), "/");
        $this->assertEquals(makepath(array('one')), "/one");
        $this->assertEquals(makepath(array('one', 'two')), "/one/two");
        $this->assertEquals(makepath(array('one', 'two', 'three')), "/one/two/three");
        $this->assertEquals(makepath(array('one'), true), "/one");
        $this->assertEquals(makepath(array('one', 'two', 'three'), true), "/one/two/three");
        $this->assertEquals(makepath(array('one'), false), "one");
        $this->assertEquals(makepath(array('one', 'two'), false), "one/two");
        $this->assertEquals(makepath(array('one', 'two', 'three'), false), "one/two/three");

    }
    public function test_typeof() {
        $this->assertEquals(typeof(null), null);
        $this->assertEquals(typeof(55), integer);
        $this->assertEquals(typeof(7.9), double);
        $this->assertEquals(typeof(true), boolean);
        $m = new MiscClass;
        $this->assertEquals(typeof($m), MiscClass);
    }

    public function test_say_1() {
        $this->expectOutputString("hey\n");
        say("hey");

    }

    public function test_say_2() {
        $this->expectOutputString("hey\nthere\n");
        say("hey");
        say("there");
    }

    public function test_say_3() {
        $this->expectOutputString("number: 21\nname: Joe\n");
        $v = 21;
        say("number: $v\nname: Joe");
    }
}

?>

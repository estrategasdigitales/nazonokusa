<?php

require_once __DIR__ . "/../findnode.php";

class M {
    public $y = array(3.3, 4.4, 5.5);
    public $z = array('one' => 1, 'two' => 2, 3);
}

class Findnode_Test extends PHPUnit_Framework_TestCase
{

    public function test_pure_array()
    {

        $o = array("x" => array("y" => array(11, 12, 33)),
                   "a" => 99);

        $this->assertEquals(findnode("/x/y/[0]", $o), 11);
        $this->assertEquals(findnode("/x/y/[1]", $o), 12);
        $this->assertEquals(findnode("/x/y/[2]", $o), 33);
        $this->assertEquals(findnode("/a", $o), 99);

    }

    public function test_object_access()
    {

        $m = new M;
        $o = array("x" => $m);
        $this->assertEquals(findnode("/x/y/[0]", $o), 3.3);
        $this->assertEquals(findnode("/x/y/[1]", $o), 4.4);
        $this->assertEquals(findnode("/x/y/[2]", $o), 5.5);
        $this->assertEquals(findnode("/x/z/one", $o), 1);
        $this->assertEquals(findnode("/x/z/two", $o), 2);
        $this->assertEquals(findnode("/x/z/[0]", $o), 3);

        // NB last tests, indexing into a partially associative array
        // is super-dangerous because indices don't necessarily line
        // up to human expectations
    }

    public function test_absolute_path() {

        // trival cases
        $cur = "/";
        $this->assertEquals(absolute_path($cur, ''), '/');
        $this->assertEquals(absolute_path($cur, '/'), '/');
        $this->assertEquals(absolute_path($cur, '/this/and/that'), '/this/and/that');

        // indentity cases
        $this->assertEquals(absolute_path($cur, '.'), '/');
        $this->assertEquals(absolute_path($cur, '..'), '/');

        // basic cases
        $this->assertEquals(absolute_path($cur, 'andy'), '/andy');
        $this->assertEquals(absolute_path($cur, 'andy/mandy'), '/andy/mandy');
        $this->assertEquals(absolute_path($cur, 'andy/mandy/sandy'), '/andy/mandy/sandy');

        // mixed cases
        $cur = '/andy/mandy/sandy';
        $this->assertEquals(absolute_path($cur, '.'), '/andy/mandy/sandy');
        $this->assertEquals(absolute_path($cur, '..'), '/andy/mandy');
        $this->assertEquals(absolute_path($cur, '../..'), '/andy');

        // complex cases
        $cur = '/andy/mandy/sandy';
        $this->assertEquals(absolute_path($cur, '././../handy'), '/andy/mandy/handy');
        $this->assertEquals(absolute_path($cur, '../james/ray'), '/andy/mandy/james/ray');
        $this->assertEquals(absolute_path($cur, '../james/ray/..'), '/andy/mandy/james');

    }

}

?>

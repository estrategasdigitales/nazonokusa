<?php

require __DIR__ . '/../fdom.php';
require __DIR__ . '/../util.php';



class CDATA_Test extends PHPUnit_Framework_TestCase
{
    public function test_basic()
    {
        $before = '***';
        $x = CDATA($before);
        $y = de_CDATA($x);

        $this->assertEquals($x, "<![CDATA[***]]>");
        $this->assertEquals($y, $before);
    }

    public function test_random_string()
    {

        $length = rand(10, 300);
        $before = generateRandomString($length);
        $x = CDATA($before);
        $y = de_CDATA($x);

        $this->assertEquals(strpos($x, "<![CDATA["), 0);
        $this->assertEquals(strrpos($x, "]]>"), strlen($x)-3);
        $this->assertEquals($y, $before);
    }

}

?>

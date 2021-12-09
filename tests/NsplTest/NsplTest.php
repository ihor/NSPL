<?php

namespace NsplTest;

use function \nspl\getType;

class NsplTest extends \PHPUnit\Framework\TestCase
{
    public function testGetType()
    {
        $this->assertEquals('NULL', getType(null));
        $this->assertEquals('boolean', getType(true));
        $this->assertEquals('integer', getType(1));
        $this->assertEquals('double', getType(1.0));
        $this->assertEquals('array', getType([]));
        $this->assertEquals('string', getType('hello world'));
        $this->assertEquals('stdClass', getType(new \StdClass()));
    }

}

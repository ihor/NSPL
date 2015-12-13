<?php

namespace NsplTest;

use function \nspl\all;
use function \nspl\any;

class NsplTest extends \PHPUnit_Framework_TestCase
{
    public function testAll()
    {
        $this->assertTrue(all([true, true, true]));
        $this->assertTrue(all([true, 1, 'a', [1], new \StdClass()]));
        $this->assertTrue(all([]));

        $this->assertFalse(all([true, true, false]));
        $this->assertFalse(all([true, 0, 'a', [1], new \StdClass()]));
        $this->assertFalse(all([null, true, 1, 'a', [1], new \StdClass()]));
        $this->assertFalse(all([true, 1, 'a', [], new \StdClass()]));
        $this->assertFalse(all([true, 1, '', [1], new \StdClass()]));
    }

    public function testAny()
    {
        $this->assertTrue(any([true, false, false]));
        $this->assertTrue(any([false, 1, false]));
        $this->assertTrue(any([false, false, [1]]));
        $this->assertTrue(any(['a', false, false]));
        $this->assertTrue(any([false, new \StdClass(), false]));

        $this->assertFalse(any([]));
        $this->assertFalse(any([null, false, false]));
        $this->assertFalse(any([null, [], false]));
        $this->assertFalse(any([null, false, '']));
        $this->assertFalse(any([0, false, false]));
    }

}

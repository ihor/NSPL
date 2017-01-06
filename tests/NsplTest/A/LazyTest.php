<?php

namespace NsplTest\A;

use function \nspl\a\lazy\map;
use function \nspl\a\lazy\flatMap;

use const \nspl\a\lazy\map;
use const \nspl\a\lazy\flatMap;

class LazyTest extends \PHPUnit_Framework_TestCase
{
    public function testMap()
    {
        $this->assertInstanceOf(\Generator::class, map('strtoupper', ['a', 'b', 'c']));

        $this->assertEquals(['A', 'B', 'C'], iterator_to_array(map('strtoupper', ['a', 'b', 'c'])));
        $this->assertEquals([1, 4, 9], iterator_to_array(map(function($v) { return $v * $v; }, new \ArrayIterator([1, 2, 3]))));
        $this->assertEquals(['a' => 0, 'b' => 1, 'c' => 2], iterator_to_array(map('abs', array('a' => 0, 'b' => -1, 'c' => 2))));
        $this->assertEquals([], iterator_to_array(map('strtoupper', [])));

        $range = function($min, $max) { for ($i = $min; $i <= $max; ++$i) yield $i; };
        $this->assertEquals([1, 4, 9], iterator_to_array(map(function($v) { return $v * $v; }, $range(1, 3))));

        $this->assertEquals(['A', 'B', 'C'], iterator_to_array(call_user_func(map, 'strtoupper', ['a', 'b', 'c'])));
        $this->assertEquals('\nspl\a\lazy\map', map);
    }

    public function testFlatMap()
    {
        $this->assertInstanceOf(\Generator::class, flatMap(function($v) { return [$v, $v + 1]; }, [1, 3]));

        $this->assertEquals(
            [1, 2, 3, 4],
            iterator_to_array(flatMap(function($v) { return [$v, $v + 1]; }, [1, 3]))
        );

        $this->assertEquals(
            ['hello', 'world', 'answer', 'is', '42'],
            iterator_to_array(flatMap(function($v) { return explode(' ', $v); }, ['hello world', 'answer is 42']))
        );

        $this->assertEquals(
            [1, 2, 3, 4],
            iterator_to_array(call_user_func(flatMap, function($v) { return [$v, $v + 1]; }, [1, 3]))
        );
        $this->assertEquals('\nspl\a\lazy\flatMap', flatMap);
    }

}

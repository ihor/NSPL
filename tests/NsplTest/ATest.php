<?php

use \nspl\a;
use function \nspl\a\extend;
use function \nspl\a\zip;
use function \nspl\a\flatten;
use function \nspl\a\sorted;
use function \nspl\a\take;
use function \nspl\a\first;
use function \nspl\a\drop;
use function \nspl\a\last;

class ATest extends \PHPUnit_Framework_TestCase
{
    public function testExtend()
    {
        $this->assertEquals([1, 2, 3, 4, 5, 6], extend([1, 2, 3], [4, 5, 6]));
        $this->assertEquals([1, 2, 3, 3, 4, 5], extend([1, 2, 3], [3, 4, 5]));
        $this->assertEquals([4, 5, 6], extend([], [4, 5, 6]));
        $this->assertEquals([1, 2, 3], extend([1, 2, 3], []));

        $this->assertEquals([1, 2, 3, 4, 5, 6], call_user_func(a::$extend, [1, 2, 3], [4, 5, 6]));
        $this->assertEquals([1, 2, 3, 3, 4, 5], call_user_func(a::$extend, [1, 2, 3], [3, 4, 5]));
        $this->assertEquals([4, 5, 6], call_user_func(a::$extend, [], [4, 5, 6]));
        $this->assertEquals([1, 2, 3], call_user_func(a::$extend, [1, 2, 3], []));
    }

    public function testZip()
    {
        $this->assertEquals([[1, 'a'], [2, 'b'], [3, 'c']], zip([1, 2, 3], ['a', 'b', 'c']));
        $this->assertEquals([[1, 'a'], [2, 'b']], zip([1, 2, 3], ['a', 'b']));
        $this->assertEquals([], zip([], ['a', 'b', 'c']));
        $this->assertEquals([], zip([1, 2, 3], []));

        $this->assertEquals(
            [[1, 'a', ['x']], [2, 'b', ['y']], [3, 'c', ['z']]],
            zip([1, 2, 3], ['a', 'b', 'c'], [['x'], ['y'], ['z']])
        );

        $this->assertEquals([[1, 'a'], [2, 'b'], [3, 'c']], call_user_func(a::$zip, [1, 2, 3], ['a', 'b', 'c']));
        $this->assertEquals([[1, 'a'], [2, 'b']], call_user_func(a::$zip, [1, 2, 3], ['a', 'b']));
        $this->assertEquals([], call_user_func(a::$zip, [], ['a', 'b', 'c']));
        $this->assertEquals([], call_user_func(a::$zip, [1, 2, 3], []));

        $this->assertEquals(
            [[1, 'a', ['x']], [2, 'b', ['y']], [3, 'c', ['z']]],
            call_user_func(a::$zip, [1, 2, 3], ['a', 'b', 'c'], [['x'], ['y'], ['z']])
        );
    }

    public function testFlatten()
    {
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], flatten([[1, 2, 3], [4, 5, 6], [7, 8, 9]]));
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], flatten([[1, [2, [3]]], [[[4, 5, 6]]], 7, 8, [9]]));
        $this->assertEquals([1], flatten([1]));
        $this->assertEquals([], flatten([]));

        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], call_user_func(a::$flatten, [[1, 2, 3], [4, 5, 6], [7, 8, 9]]));
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], call_user_func(a::$flatten, [[1, [2, [3]]], [[[4, 5, 6]]], 7, 8, [9]]));
        $this->assertEquals([1], call_user_func(a::$flatten, [1]));
        $this->assertEquals([], call_user_func(a::$flatten, []));
    }

    public function testSorted()
    {
        $this->assertEquals([1, 2, 3], sorted([2, 3, 1]));

        $this->assertEquals(
            array('carrot' => 'c', 'banana' => 'b', 'apple' => 'a'),
            sorted(array('carrot' => 'c', 'apple' => 'a', 'banana' => 'b'), true)
        );

        $this->assertEquals(
            ['forty two', 'answer', 'the', 'is'],
            sorted(['the', 'answer', 'is', 'forty two'], true, 'strlen')
        );

        $isFruit = function($v) { return in_array($v, ['apple', 'orange']); };
        $this->assertEquals(
            ['apple', 'orange', 'cat'],
            sorted(['orange', 'cat', 'apple'], false, null, function($v1, $v2) use ($isFruit) {
                if (!$isFruit($v1)) return 1;
                if (!$isFruit($v2)) return -1;
                return $v1 > $v2;
            })
        );

        $this->assertEquals([], sorted([]));
        $this->assertEquals([1], sorted([1]));
        $this->assertEquals(array('b' => null, 'a' => null), sorted(array('b' => null, 'a' => null)));

        $list = [3, 1, 2];
        $this->assertEquals([1, 2, 3], sorted($list));
        $this->assertEquals([3, 1, 2], $list);

        $this->assertEquals([1, 2, 3], call_user_func(a::$sorted, [2, 3, 1]));

        $this->assertEquals(
            array('carrot' => 'c', 'banana' => 'b', 'apple' => 'a'),
            call_user_func(a::$sorted, array('carrot' => 'c', 'apple' => 'a', 'banana' => 'b'), true)
        );

        $this->assertEquals(
            ['forty two', 'answer', 'the', 'is'],
            call_user_func(a::$sorted, ['the', 'answer', 'is', 'forty two'], true, 'strlen')
        );

        $isFruit = function($v) { return in_array($v, ['apple', 'orange']); };
        $this->assertEquals(
            ['apple', 'orange', 'cat'],
            call_user_func(a::$sorted, ['orange', 'cat', 'apple'], false, null, function($v1, $v2) use ($isFruit) {
                if (!$isFruit($v1)) return 1;
                if (!$isFruit($v2)) return -1;
                return $v1 > $v2;
            })
        );

        $this->assertEquals([], call_user_func(a::$sorted, []));
        $this->assertEquals([1], call_user_func(a::$sorted, [1]));
        $this->assertEquals(array('b' => null, 'a' => null), call_user_func(a::$sorted, array('b' => null, 'a' => null)));

        $list = [3, 1, 2];
        $this->assertEquals([1, 2, 3], call_user_func(a::$sorted, $list));
        $this->assertEquals([3, 1, 2], $list);
    }

    public function testTake()
    {
        $this->assertEquals([1, 2, 3], take([1, 2, 3, 4, 5, 6, 7, 8, 9], 3));
        $this->assertEquals([1, 3, 5], take([1, 2, 3, 4, 5, 6, 7, 8, 9], 3, 2));
        $this->assertEquals([1, 4, 7], take([1, 2, 3, 4, 5, 6, 7, 8, 9], 5, 3));
        $this->assertEquals([], take([1, 2, 3, 4, 5, 6, 7, 8, 9], 0));
        $this->assertEquals([], take([], 3));
        $this->assertEquals([], take([], 3, 2));

        $this->assertEquals([1, 2, 3], call_user_func(a::$take, [1, 2, 3, 4, 5, 6, 7, 8, 9], 3));
        $this->assertEquals([1, 3, 5], call_user_func(a::$take, [1, 2, 3, 4, 5, 6, 7, 8, 9], 3, 2));
        $this->assertEquals([1, 4, 7], call_user_func(a::$take, [1, 2, 3, 4, 5, 6, 7, 8, 9], 5, 3));
        $this->assertEquals([], call_user_func(a::$take, [1, 2, 3, 4, 5, 6, 7, 8, 9], 0));
        $this->assertEquals([], call_user_func(a::$take, [], 3));
        $this->assertEquals([], call_user_func(a::$take, [], 3, 2));
    }

    public function testFirst()
    {
        $this->assertEquals(1, first([1, 2, 3, 4, 5, 6, 7, 8, 9]));
        $this->assertEquals(1, first([1]));

        $this->assertEquals(1, call_user_func(a::$first, [1, 2, 3, 4, 5, 6, 7, 8, 9]));
        $this->assertEquals(1, call_user_func(a::$first, [1]));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFirstForEmptyList()
    {
        first([]);
    }

    public function testDrop()
    {
        $this->assertEquals([7, 8, 9], drop([1, 2, 3, 4, 5, 6, 7, 8, 9], 6));
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], drop([1, 2, 3, 4, 5, 6, 7, 8, 9], 0));
        $this->assertEquals([], drop([], 3));

        $this->assertEquals([7, 8, 9], call_user_func(a::$drop, [1, 2, 3, 4, 5, 6, 7, 8, 9], 6));
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], call_user_func(a::$drop, [1, 2, 3, 4, 5, 6, 7, 8, 9], 0));
        $this->assertEquals([], call_user_func(a::$drop, [], 3));
    }

    public function testLast()
    {
        $this->assertEquals(9, last([1, 2, 3, 4, 5, 6, 7, 8, 9]));

        $this->assertEquals(9, call_user_func(a::$last, [1, 2, 3, 4, 5, 6, 7, 8, 9]));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLastForEmptyList()
    {
        last([]);
    }

}

<?php

namespace NsplTest;

use function \nspl\a\all;
use function \nspl\a\any;
use function \nspl\a\getByKey;
use function \nspl\a\extend;
use function \nspl\a\zip;
use function \nspl\a\flatten;
use function \nspl\a\pairs;
use function \nspl\a\sorted;
use function \nspl\a\keySorted;
use function \nspl\a\indexed;
use function \nspl\a\take;
use function \nspl\a\first;
use function \nspl\a\drop;
use function \nspl\a\last;
use function \nspl\a\moveElement;

use const \nspl\a\all;
use const \nspl\a\any;
use const \nspl\a\getByKey;
use const \nspl\a\extend;
use const \nspl\a\zip;
use const \nspl\a\flatten;
use const \nspl\a\pairs;
use const \nspl\a\sorted;
use const \nspl\a\keySorted;
use const \nspl\a\indexed;
use const \nspl\a\take;
use const \nspl\a\first;
use const \nspl\a\drop;
use const \nspl\a\last;
use const \nspl\a\moveElement;

class ATest extends \PHPUnit_Framework_TestCase
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

        $this->assertTrue(all([19, 20, 21], function($v) { return $v > 18; }));
        $this->assertFalse(all([19, 20, 21], function($v) { return $v < 18; }));

        $this->assertTrue(call_user_func(all, [true, true, true]));
        $this->assertEquals('\nspl\a\all', all);
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

        $this->assertTrue(any([18, 19, 20], function($v) { return $v === 18; }));
        $this->assertFalse(any([19, 20, 21], function($v) { return $v === 18; }));

        $this->assertTrue(call_user_func(any, [true, false, false]));
        $this->assertEquals('\nspl\a\any', any);
    }

    public function testGetByKey()
    {
        $this->assertEquals(2, getByKey(array('a' => 1, 'b' => 2, 'c' => 3), 'b'));
        $this->assertEquals(-1, getByKey(array('a' => 1, 'b' => 2, 'c' => 3), 'd', -1));

        $this->assertEquals(2, call_user_func(getByKey, array('a' => 1, 'b' => 2, 'c' => 3), 'b'));
        $this->assertEquals('\nspl\a\getByKey', getByKey);
    }

    public function testExtend()
    {
        $this->assertEquals([1, 2, 3, 4, 5, 6], extend([1, 2, 3], [4, 5, 6]));
        $this->assertEquals([1, 2, 3, 3, 4, 5], extend([1, 2, 3], [3, 4, 5]));
        $this->assertEquals([1, 2, 3, 3, 4, 5], extend(new \ArrayIterator([1, 2, 3]), [3, 4, 5]));
        $this->assertEquals([1, 2, 3, 3, 4, 5], extend([1, 2, 3], new \ArrayIterator([3, 4, 5])));
        $this->assertEquals([4, 5, 6], extend([], [4, 5, 6]));
        $this->assertEquals([1, 2, 3], extend([1, 2, 3], []));

        $this->assertEquals([1, 2, 3, 4, 5, 6], call_user_func(extend, [1, 2, 3], [4, 5, 6]));
        $this->assertEquals('\nspl\a\extend', extend);
    }

    public function testZip()
    {
        $this->assertEquals([[1, 'a'], [2, 'b'], [3, 'c']], zip([1, 2, 3], ['a', 'b', 'c']));
        $this->assertEquals([[1, 'a'], [2, 'b'], [3, 'c']], zip(new \ArrayIterator([1, 2, 3]), ['a', 'b', 'c']));
        $this->assertEquals([[1, 'a'], [2, 'b'], [3, 'c']], zip([1, 2, 3], new \ArrayIterator(['a', 'b', 'c'])));
        $this->assertEquals([[1, 'a'], [2, 'b']], zip([1, 2, 3], ['a', 'b']));
        $this->assertEquals([], zip([], ['a', 'b', 'c']));
        $this->assertEquals([], zip([1, 2, 3], []));

        $this->assertEquals(
            [[1, 'a', ['x']], [2, 'b', ['y']], [3, 'c', ['z']]],
            zip([1, 2, 3], ['a', 'b', 'c'], [['x'], ['y'], ['z']])
        );

        $this->assertEquals([[1, 'a'], [2, 'b'], [3, 'c']], call_user_func(zip, [1, 2, 3], ['a', 'b', 'c']));
        $this->assertEquals('\nspl\a\zip', zip);
    }

    public function testFlatten()
    {
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], flatten([[1, 2, 3], [4, 5, 6], [7, 8, 9]]));
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], flatten(new \ArrayIterator([[1, 2, 3], new \ArrayIterator([4, 5, 6]), [7, 8, 9]])));
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], flatten([[1, [2, [3]]], [[[4, 5, 6]]], 7, 8, [9]]));
        $this->assertEquals([1, [2, [3]], [[4, 5, 6]], 7, 8, 9], flatten([[1, [2, [3]]], [[[4, 5, 6]]], 7, 8, [9]], 1));
        $this->assertEquals([1, 2, [3], [4, 5, 6], 7, 8, 9], flatten([[1, [2, [3]]], [[[4, 5, 6]]], 7, 8, [9]], 2));
        $this->assertEquals([1], flatten([1]));
        $this->assertEquals([], flatten([]));

        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], call_user_func(flatten, [[1, 2, 3], [4, 5, 6], [7, 8, 9]]));
        $this->assertEquals('\nspl\a\flatten', flatten);
    }

    public function testPairs()
    {
        $this->assertEquals([[0, 'a'], [1, 'b'], [2, 'c']], pairs(['a', 'b', 'c']));
        $this->assertEquals([['a', 'hello'], ['b', 'world'], ['c', 42]], pairs(array('a' => 'hello', 'b' => 'world', 'c' => 42)));
        $this->assertEquals([], pairs([]));

        $this->assertEquals([[0, 'a'], [1, 'b'], [2, 'c']], call_user_func(pairs, (['a', 'b', 'c'])));
        $this->assertEquals([['a', 'hello'], ['b', 'world'], ['c', 42]], call_user_func(pairs, (array('a' => 'hello', 'b' => 'world', 'c' => 42))));
        $this->assertEquals([], call_user_func(pairs, ([])));
    }

    public function testSorted()
    {
        $this->assertEquals([1, 2, 3], sorted([2, 3, 1]));
        $this->assertEquals([1, 2, 3], sorted(new \ArrayIterator([2, 3, 1])));

        $this->assertEquals(
            array('carrot' => 'c', 'banana' => 'b', 'apple' => 'a'),
            sorted(array('carrot' => 'c', 'apple' => 'a', 'banana' => 'b'), true)
        );

        $this->assertEquals(
            ['forty two', 'answer', 'the', 'is'],
            sorted(['the', 'answer', 'is', 'forty two'], true, 'strlen')
        );

        $this->assertEquals(
            ['is', 'the', 'answer', 'forty two'],
            sorted(['the', 'answer', 'is', 'forty two'], 'strlen')
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

        $this->assertEquals([1, 2, 3], call_user_func(sorted, [2, 3, 1]));
        $this->assertEquals('\nspl\a\sorted', sorted);
    }

    public function testKeySorted()
    {
        $this->assertEquals(array('a' => 1, 'b' => 2, 'c' => 3), keySorted(array('b' => 2, 'c' => 3, 'a' => 1)));
        $this->assertEquals(array('a' => 1, 'b' => 2, 'c' => 3), keySorted(new \ArrayIterator(array('b' => 2, 'c' => 3, 'a' => 1))));
        $this->assertEquals(array('c' => 3, 'b' => 2, 'a' => 1), keySorted(array('b' => 2, 'c' => 3, 'a' => 1), true));

        $this->assertEquals(array('a' => 1, 'b' => 2, 'c' => 3), call_user_func(keySorted, array('b' => 2, 'c' => 3, 'a' => 1)));
        $this->assertEquals('\nspl\a\keySorted', keySorted);
    }

    public function testIndexed()
    {
        $animals = [
            array('id' => 9, 'type' => 'cat', 'name' => 'Snowball'),
            array('id' => 10, 'type' => 'dog', 'name' => 'Santa\'s Little Helper'),
            array('id' => 11, 'type' => 'cat', 'name' => 'Fluffy'),
        ];

        $this->assertEquals(array(
            9 => array('id' => 9, 'type' => 'cat', 'name' => 'Snowball'),
            10 => array('id' => 10, 'type' => 'dog', 'name' => 'Santa\'s Little Helper'),
            11 => array('id' => 11, 'type' => 'cat', 'name' => 'Fluffy'),
        ), indexed($animals, 'id'));

        $this->assertEquals(array(
            9 => array('id' => 9, 'type' => 'cat', 'name' => 'Snowball'),
            10 => array('id' => 10, 'type' => 'dog', 'name' => 'Santa\'s Little Helper'),
            11 => array('id' => 11, 'type' => 'cat', 'name' => 'Fluffy'),
        ), indexed(new \ArrayIterator($animals), 'id'));

        $this->assertEquals(array(
            'cat' => array('id' => 11, 'type' => 'cat', 'name' => 'Fluffy'),
            'dog' => array('id' => 10, 'type' => 'dog', 'name' => 'Santa\'s Little Helper'),
        ), indexed($animals, 'type'));

        $this->assertEquals(array(
            'cat' => [
                array('id' => 9, 'type' => 'cat', 'name' => 'Snowball'),
                array('id' => 11, 'type' => 'cat', 'name' => 'Fluffy'),
            ],
            'dog' => [
                array('id' => 10, 'type' => 'dog', 'name' => 'Santa\'s Little Helper'),
            ],
        ), indexed($animals, 'type', false));

        $this->assertEquals(array(
            3 => [
                array('id' => 9, 'type' => 'cat', 'name' => 'Snowball'),
                array('id' => 10, 'type' => 'dog', 'name' => 'Santa\'s Little Helper'),
                array('id' => 11, 'type' => 'cat', 'name' => 'Fluffy'),
            ],
        ), indexed($animals, function($animal) { return strlen($animal['type']); }, false));

        $this->assertEquals(array(
            'Snowball' => 'cat',
            'Santa\'s Little Helper' => 'dog',
            'Fluffy' => 'cat',
        ), indexed($animals, 'name', true, function($animal) { return $animal['type']; }));

        $this->assertEquals(array(
            9 => array('id' => 9, 'type' => 'cat', 'name' => 'Snowball'),
            10 => array('id' => 10, 'type' => 'dog', 'name' => 'Santa\'s Little Helper'),
            11 => array('id' => 11, 'type' => 'cat', 'name' => 'Fluffy'),
        ), call_user_func(indexed, $animals, 'id'));

        $this->assertEquals('\nspl\a\indexed', indexed);
    }

    public function testTake()
    {
        $this->assertEquals([1, 2, 3], take([1, 2, 3, 4, 5, 6, 7, 8, 9], 3));
        $this->assertEquals([1, 2, 3], take(new \ArrayIterator([1, 2, 3, 4, 5, 6, 7, 8, 9]), 3));
        $this->assertEquals([1, 3, 5], take([1, 2, 3, 4, 5, 6, 7, 8, 9], 3, 2));
        $this->assertEquals([1, 3, 5], take(new \ArrayIterator([1, 2, 3, 4, 5, 6, 7, 8, 9]), 3, 2));
        $this->assertEquals([1, 4, 7], take([1, 2, 3, 4, 5, 6, 7, 8, 9], 5, 3));
        $this->assertEquals([], take([1, 2, 3, 4, 5, 6, 7, 8, 9], 0));
        $this->assertEquals([], take([], 3));
        $this->assertEquals([], take([], 3, 2));

        $this->assertEquals([1, 2, 3], call_user_func(take, [1, 2, 3, 4, 5, 6, 7, 8, 9], 3));
        $this->assertEquals('\nspl\a\take', take);
    }

    public function testFirst()
    {
        $this->assertEquals(1, first([1, 2, 3, 4, 5, 6, 7, 8, 9]));
        $this->assertEquals(1, first(new \ArrayIterator([1, 2, 3, 4, 5, 6, 7, 8, 9])));
        $this->assertEquals(1, first(array('hello' => 1, 'world' => 2)));
        $this->assertEquals(1, first([1]));

        $this->assertEquals(1, call_user_func(first, [1, 2, 3, 4, 5, 6, 7, 8, 9]));
        $this->assertEquals('\nspl\a\first', first);
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
        $this->assertEquals([7, 8, 9], drop(new \ArrayIterator([1, 2, 3, 4, 5, 6, 7, 8, 9]), 6));
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], drop([1, 2, 3, 4, 5, 6, 7, 8, 9], 0));
        $this->assertEquals([], drop([], 3));

        $this->assertEquals([7, 8, 9], call_user_func(drop, [1, 2, 3, 4, 5, 6, 7, 8, 9], 6));
        $this->assertEquals('\nspl\a\drop', drop);
    }

    public function testLast()
    {
        $this->assertEquals(9, last([1, 2, 3, 4, 5, 6, 7, 8, 9]));
        $this->assertEquals(9, last(new \ArrayIterator([1, 2, 3, 4, 5, 6, 7, 8, 9])));

        $this->assertEquals(9, call_user_func(last, [1, 2, 3, 4, 5, 6, 7, 8, 9]));
        $this->assertEquals('\nspl\a\last', last);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLastForEmptyList()
    {
        last([]);
    }

    public function testMoveElement()
    {
        $this->assertEquals([2, 0, 1], moveElement([0, 1, 2], 2, 0));
        $this->assertEquals([0, 2, 1], moveElement([0, 1, 2], 1, 2));
        $this->assertEquals([0, 1, 2], moveElement([0, 1, 2], 1, 1));

        $this->assertEquals([2, 0, 1], call_user_func(moveElement, [0, 1, 2], 2, 0));
        $this->assertEquals('\nspl\a\moveElement', moveElement);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMoveElementToInNotList()
    {
        moveElement(array(1 => 'a', 2 => 'b', 3 => 'c'), 1, 2);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMoveElementToInvalidPosition()
    {
        moveElement([0, 1, 2], 0, 3);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMoveElementFromInvalidPosition()
    {
        moveElement([0, 1, 2], 3, 0);
    }

}

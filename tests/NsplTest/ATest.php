<?php

namespace NsplTest;

use function \nspl\a\all;
use function \nspl\a\any;
use function \nspl\a\map;
use function \nspl\a\flatMap;
use function \nspl\a\zip;
use function \nspl\a\zipWith;
use function \nspl\a\reduce;
use function \nspl\a\filter;
use function \nspl\a\filterNot;
use function \nspl\a\partition;
use function \nspl\a\span;
use function \nspl\a\value;
use function \nspl\a\merge;
use function \nspl\a\flatten;
use function \nspl\a\pairs;
use function \nspl\a\sorted;
use function \nspl\a\keySorted;
use function \nspl\a\indexed;
use function \nspl\a\take;
use function \nspl\a\takeKeys;
use function \nspl\a\takeWhile;
use function \nspl\a\first;
use function \nspl\a\second;
use function \nspl\a\drop;
use function \nspl\a\dropWhile;
use function \nspl\a\last;
use function \nspl\a\reorder;
use function \nspl\a\isList;

use const \nspl\a\all;
use const \nspl\a\any;
use const \nspl\a\map;
use const \nspl\a\flatMap;
use const \nspl\a\zip;
use const \nspl\a\zipWith;
use const \nspl\a\reduce;
use const \nspl\a\filter;
use const \nspl\a\filterNot;
use const \nspl\a\partition;
use const \nspl\a\span;
use const \nspl\a\value;
use const \nspl\a\merge;
use const \nspl\a\flatten;
use const \nspl\a\pairs;
use const \nspl\a\sorted;
use const \nspl\a\keySorted;
use const \nspl\a\indexed;
use const \nspl\a\take;
use const \nspl\a\takeKeys;
use const \nspl\a\takeWhile;
use const \nspl\a\first;
use const \nspl\a\second;
use const \nspl\a\drop;
use const \nspl\a\dropWhile;
use const \nspl\a\last;
use const \nspl\a\reorder;
use const \nspl\a\isList;

use function \nspl\f\rpartial;
use const \nspl\op\lt;

//region deprecated
use function \nspl\a\moveElement;
use function \nspl\a\getByKey;
use function \nspl\a\extend;
use function \nspl\a\traversableToArray;

use const \nspl\a\moveElement;
use const \nspl\a\extend;
use const \nspl\a\getByKey;
//endregion

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

    public function testMap()
    {
        $this->assertEquals(['A', 'B', 'C'], map('strtoupper', ['a', 'b', 'c']));
        $this->assertEquals([1, 4, 9], map(function($v) { return $v * $v; }, new \ArrayIterator([1, 2, 3])));
        $this->assertEquals(['a' => 0, 'b' => 1, 'c' => 2], map('abs', array('a' => 0, 'b' => -1, 'c' => 2)));
        $this->assertEquals([], map('strtoupper', []));

        $range = function($min, $max) { for ($i = $min; $i <= $max; ++$i) yield $i; };
        $this->assertEquals([1, 4, 9], map(function($v) { return $v * $v; }, $range(1, 3)));

        $this->assertEquals(['A', 'B', 'C'], call_user_func(map, 'strtoupper', ['a', 'b', 'c']));
        $this->assertEquals('\nspl\a\map', map);
    }

    public function testFlatMap()
    {
        $this->assertEquals(
            [1, 2, 3, 4],
            flatMap(function($v) { return [$v, $v + 1]; }, [1, 3])
        );

        $this->assertEquals(
            ['hello', 'world', 'answer', 'is', '42'],
            flatMap(function($v) { return explode(' ', $v); }, ['hello world', 'answer is 42'])
        );

        $this->assertEquals(
            [1, 2, 3, 4],
            call_user_func(flatMap, function($v) { return [$v, $v + 1]; }, [1, 3])
        );
        $this->assertEquals('\nspl\a\flatMap', flatMap);
    }

    public function testReduce()
    {
        $this->assertEquals(6, reduce(function($a, $b) { return $a + $b; }, [1, 2, 3]));
        $this->assertEquals('abc', reduce(function($a, $b) { return $a . $b; }, new \ArrayIterator(['a', 'b', 'c']), ''));
        $this->assertEquals(64, reduce('pow', array('a' => 3, 'b' => 2, 'c' => 1), 2));
        $this->assertEquals(0, reduce(function($a, $b) { return $a * $b; }, [], 0));
        $this->assertEquals(1, reduce(function($a, $b) { return $a * $b; }, [], 1));

        $this->assertEquals(6, call_user_func(reduce, function($a, $b) { return $a + $b; }, [1, 2, 3]));
        $this->assertEquals('\nspl\a\reduce', reduce);
    }

    public function testFilter()
    {
        $this->assertEquals([1, 2, 3], filter('is_numeric', ['a', 1, 'b', 2, 'c', 3]));
        $this->assertEquals(
            array('b' => 2),
            filter(function($v) { return $v % 2 === 0; }, array('a' => 1, 'b' => 2, 'c' => 3))
        );
        $this->assertEquals([], filter('is_int', []));

        $this->assertEquals([1, 2, 3], call_user_func(filter, 'is_numeric', ['a', 1, 'b', 2, 'c', 3]));
        $this->assertEquals('\nspl\a\filter', filter);
    }

    public function testFilterNot()
    {
        $this->assertEquals(['a', 'b', 'c'], filterNot('is_numeric', ['a', 1, 'b', 2, 'c', 3]));
        $this->assertEquals(
            array('a' => 1, 'c' => 3),
            filterNot(function($v) { return $v % 2 === 0; }, array('a' => 1, 'b' => 2, 'c' => 3))
        );
        $this->assertEquals([], filterNot('is_int', []));

        $this->assertEquals(['a', 'b', 'c'], call_user_func(filterNot, 'is_numeric', ['a', 1, 'b', 2, 'c', 3]));
        $this->assertEquals('\nspl\a\filterNot', filterNot);
    }

    public function testPartition()
    {
        $this->assertEquals([[1, 2, 3], ['a', 'b', 'c']], partition('is_numeric', ['a', 1, 'b', 2, 'c', 3]));
        $this->assertEquals(
            [array('b' => 2), array('a' => 1, 'c' => 3)],
            partition(function($v) { return $v % 2 === 0; }, array('a' => 1, 'b' => 2, 'c' => 3))
        );
        $this->assertEquals([[], []], partition('is_int', []));

        $this->assertEquals([[1, 2, 3], ['a', 'b', 'c']], call_user_func(partition, 'is_numeric', ['a', 1, 'b', 2, 'c', 3]));
        $this->assertEquals('\nspl\a\partition', partition);
    }

    public function testSpan()
    {
        $this->assertEquals([[], ['a', 1, 'b', 2, 'c', 3]], span('is_numeric', ['a', 1, 'b', 2, 'c', 3]));
        $this->assertEquals([[1], ['a', 2, 'b', 3, 'c']], span('is_numeric', [1, 'a', 2, 'b', 3, 'c']));
        $this->assertEquals([[1, 2, 3], ['a', 'b', 'c']], span('is_numeric', [1, 2, 3, 'a', 'b', 'c']));
        $this->assertEquals([[], []], span('is_int', []));

        $this->assertEquals([[1], ['a', 2, 'b', 3, 'c']], call_user_func(span, 'is_numeric', [1, 'a', 2, 'b', 3, 'c']));
        $this->assertEquals('\nspl\a\span', span);
    }

    public function testValue()
    {
        $this->assertEquals(2, value(array('a' => 1, 'b' => 2, 'c' => 3), 'b'));
        $this->assertEquals(-1, value(array('a' => 1, 'b' => 2, 'c' => 3), 'd', -1));

        $this->assertEquals(2, call_user_func(value, array('a' => 1, 'b' => 2, 'c' => 3), 'b'));
        $this->assertEquals('\nspl\a\value', value);
    }

    public function testMerge()
    {
        $this->assertEquals([1, 2, 3, 4, 5, 6], merge([1, 2, 3], [4, 5, 6]));
        $this->assertEquals([1, 2, 3, 3, 4, 5], merge([1, 2, 3], [3, 4, 5]));
        $this->assertEquals([1, 2, 3, 3, 4, 5], merge(new \ArrayIterator([1, 2, 3]), [3, 4, 5]));
        $this->assertEquals([1, 2, 3, 3, 4, 5], merge([1, 2, 3], new \ArrayIterator([3, 4, 5])));
        $this->assertEquals([4, 5, 6], merge([], [4, 5, 6]));
        $this->assertEquals([1, 2, 3], merge([1, 2, 3], []));

        $this->assertEquals([1, 2, 3, 4, 5, 6], call_user_func(merge, [1, 2, 3], [4, 5, 6]));
        $this->assertEquals('\nspl\a\merge', merge);
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

    public function testZipWith()
    {
        $sum = function($x, $y) { return $x + $y; };
        $sum3 = function($x, $y, $z) { return $x + $y + $z; };

        $this->assertEquals([2, 4, 6], zipWith($sum, [1, 2, 3], [1, 2, 3]));
        $this->assertEquals([3, 6, 9], zipWith($sum3, [1, 2, 3], [1, 2, 3], [1, 2, 3]));

        $this->assertEquals([3, 6, 9], call_user_func(zipWith, $sum3, [1, 2, 3], [1, 2, 3], [1, 2, 3]));
        $this->assertEquals('\nspl\a\zipWith', zipWith);
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

    public function testTakeKeys()
    {
        $this->assertEquals(array('hello' => 1, 'world' => 2), takeKeys(array('hello' => 1, 'world' => 2, 'foo' => 3, 'bar' => 4), ['hello', 'world']));
        $this->assertEquals(array('hello' => 1), takeKeys(array('hello' => 1, 'foo' => 3, 'bar' => 4), ['hello', 'world']));
        $this->assertEquals(array(), takeKeys(array(), ['hello', 'world']));

        $this->assertEquals(array('hello' => 1, 'world' => 2), call_user_func(takeKeys, array('hello' => 1, 'world' => 2, 'foo' => 3, 'bar' => 4), ['hello', 'world']));
        $this->assertEquals('\nspl\a\takeKeys', takeKeys);
    }

    public function testTakeWhile()
    {
        $this->assertEquals([1, 2, 3], takeWhile('is_numeric', [1, 2, 3, 'a', 'b', 'c', 4, 5, 6]));
        $this->assertEquals([1, 2, 3], takeWhile(rpartial(lt, 4), [1, 2, 3, 4, 5, 6, 7, 8, 9]));
        $this->assertEquals([1, 2, 3], takeWhile(rpartial(lt, 4), new \ArrayIterator([1, 2, 3, 4, 5, 6, 7, 8, 9])));
        $this->assertEquals([], takeWhile(rpartial(lt, 4), []));

        $this->assertEquals([1, 2, 3], call_user_func(takeWhile, rpartial(lt, 4), [1, 2, 3, 4, 5, 6, 7, 8, 9]));
        $this->assertEquals('\nspl\a\takeWhile', takeWhile);
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
    public function testFirstForEmptySequence()
    {
        first([]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFirstForEmptyIterator()
    {
        first(new \ArrayIterator([]));
    }

    public function testSecond()
    {
        $this->assertEquals(2, second([1, 2, 3, 4, 5, 6, 7, 8, 9]));
        $this->assertEquals(2, second(new \ArrayIterator([1, 2, 3, 4, 5, 6, 7, 8, 9])));
        $this->assertEquals(2, second(array('hello' => 1, 'world' => 2)));

        $this->assertEquals(2, call_user_func(second, [1, 2, 3, 4, 5, 6, 7, 8, 9]));
        $this->assertEquals('\nspl\a\second', second);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFirstForSequenceWithOnlyOneItem()
    {
        second([1]);
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

    public function testDropWhile()
    {
        $this->assertEquals(['a', 'b', 'c', 4, 5, 6], dropWhile('is_numeric', [1, 2, 3, 'a', 'b', 'c', 4, 5, 6]));
        $this->assertEquals([4, 5, 6, 7, 8, 9], dropWhile(rpartial(lt, 4), [1, 2, 3, 4, 5, 6, 7, 8, 9]));
        $this->assertEquals([4, 5, 6, 7, 8, 9], dropWhile(rpartial(lt, 4), new \ArrayIterator([1, 2, 3, 4, 5, 6, 7, 8, 9])));
        $this->assertEquals([], dropWhile(rpartial(lt, 4), []));

        $this->assertEquals([4, 5, 6, 7, 8, 9], call_user_func(dropWhile, rpartial(lt, 4), [1, 2, 3, 4, 5, 6, 7, 8, 9]));
        $this->assertEquals('\nspl\a\dropWhile', dropWhile);
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

    public function testReorder()
    {
        $this->assertEquals([2, 0, 1], reorder([0, 1, 2], 2, 0));
        $this->assertEquals([0, 2, 1], reorder([0, 1, 2], 1, 2));
        $this->assertEquals([0, 1, 2], reorder([0, 1, 2], 1, 1));

        $this->assertEquals([2, 0, 1], call_user_func(reorder, [0, 1, 2], 2, 0));
        $this->assertEquals('\nspl\a\reorder', reorder);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testReorderToInNotList()
    {
        reorder(array(1 => 'a', 2 => 'b', 3 => 'c'), 1, 2);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testReorderToInvalidPosition()
    {
        reorder([0, 1, 2], 0, 3);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testReorderFromInvalidPosition()
    {
        reorder([0, 1, 2], 3, 0);
    }

    public function testIsList()
    {
        $this->assertFalse(isList(1));
        $this->assertFalse(isList(array(1 => 'a')));
        $this->assertFalse(isList(array(0 => 'a', 2 => 'c')));
        $this->assertFalse(isList(new \StdClass()));
        $this->assertTrue(isList([]));
        $this->assertTrue(isList([1]));
        $this->assertTrue(isList([1, 2, 3]));
        $this->assertTrue(isList([10, 11, 13]));

        $this->assertTrue(call_user_func(isList, [10, 11, 13]));
        $this->assertEquals('\nspl\a\isList', isList);
    }
    
    //region deprecated
    public function testTraversableToArray()
    {
        $this->assertEquals([1, 2, 3], traversableToArray([1, 2, 3]));
        $this->assertEquals([1, 2, 3], traversableToArray(new \nspl\ds\ArrayObject(1, 2, 3)));
        $this->assertEquals([1, 2, 3], traversableToArray(new \ArrayObject([1, 2, 3])));

        $range = function($min, $max)
        {
            for ($i = $min; $i <= $max; ++$i) {
                yield $i;
            }
        };
        $this->assertEquals([1, 2, 3], traversableToArray($range(1, 3)));
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
        $this->assertEquals('\nspl\a\merge', extend);
    }

    public function testGetByKey()
    {
        $this->assertEquals(2, getByKey(array('a' => 1, 'b' => 2, 'c' => 3), 'b'));
        $this->assertEquals(-1, getByKey(array('a' => 1, 'b' => 2, 'c' => 3), 'd', -1));

        $this->assertEquals(2, call_user_func(getByKey, array('a' => 1, 'b' => 2, 'c' => 3), 'b'));
        $this->assertEquals('\nspl\a\value', getByKey);
    }

    public function testMoveElement()
    {
        $this->assertEquals([2, 0, 1], moveElement([0, 1, 2], 2, 0));
        $this->assertEquals([0, 2, 1], moveElement([0, 1, 2], 1, 2));
        $this->assertEquals([0, 1, 2], moveElement([0, 1, 2], 1, 1));

        $this->assertEquals([2, 0, 1], call_user_func(moveElement, [0, 1, 2], 2, 0));
        $this->assertEquals('\nspl\a\reorder', moveElement);
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
    //endregion

}

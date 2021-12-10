<?php

namespace NsplTest\A;

use function \nspl\a\lazy\map;
use function \nspl\a\lazy\flatMap;
use function \nspl\a\lazy\zip;
use function \nspl\a\lazy\zipWith;
use function \nspl\a\lazy\filter;
use function \nspl\a\lazy\filterNot;
use function \nspl\a\lazy\take;
use function \nspl\a\lazy\takeWhile;
use function \nspl\a\lazy\drop;
use function \nspl\a\lazy\dropWhile;
use function \nspl\a\lazy\partition;
use function \nspl\a\lazy\flatten;
use function \nspl\a\lazy\pairs;
use function \nspl\a\lazy\keys;
use function \nspl\a\lazy\with;

use const \nspl\a\lazy\map;
use const \nspl\a\lazy\flatMap;
use const \nspl\a\lazy\zip;
use const \nspl\a\lazy\zipWith;
use const \nspl\a\lazy\filter;
use const \nspl\a\lazy\filterNot;
use const \nspl\a\lazy\take;
use const \nspl\a\lazy\takeWhile;
use const \nspl\a\lazy\drop;
use const \nspl\a\lazy\dropWhile;
use const \nspl\a\lazy\partition;
use const \nspl\a\lazy\flatten;
use const \nspl\a\lazy\pairs;
use const \nspl\a\lazy\keys;

use function \nspl\f\rpartial;
use const \nspl\op\lt;
use const \nspl\op\sum;


class LazyTest extends \PHPUnit\Framework\TestCase
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

    public function testZip()
    {
        $this->assertInstanceOf(\Generator::class, zip([1, 2, 3], ['a', 'b', 'c']));

        $this->assertEquals([[1, 'a'], [2, 'b'], [3, 'c']], iterator_to_array(zip([1, 2, 3], ['a', 'b', 'c'])));
        $this->assertEquals([[1, 'a'], [2, 'b'], [3, 'c']], iterator_to_array(zip(new \ArrayIterator([1, 2, 3]), ['a', 'b', 'c'])));
        $this->assertEquals([[1, 'a'], [2, 'b'], [3, 'c']], iterator_to_array(zip([1, 2, 3], new \ArrayIterator(['a', 'b', 'c']))));
        $this->assertEquals([[1, 'a'], [2, 'b']], iterator_to_array(zip([1, 2, 3], ['a', 'b'])));
        $this->assertEquals([], iterator_to_array(zip([], ['a', 'b', 'c'])));
        $this->assertEquals([], iterator_to_array((zip([1, 2, 3], []))));

        $this->assertEquals(
            [[1, 'a'], [2, 'b'], [3, 'c']],
            iterator_to_array(zip(
                (function() { foreach ([1, 2, 3] as $value) yield $value; })(),
                (function() { foreach (['a', 'b', 'c'] as $value) yield $value; })()
            ))
        );

        $numbers = (function() {
            for ($i = 0; $i < 9; ++$i) yield $i;
        })();
        $this->assertEquals(
            [[0, 1, 2], [3, 4, 5], [6, 7, 8]],
            iterator_to_array(zip($numbers, $numbers, $numbers))
        );

        $this->assertEquals(
            [[1, 'a', ['x']], [2, 'b', ['y']], [3, 'c', ['z']]],
            iterator_to_array(zip([1, 2, 3], ['a', 'b', 'c'], [['x'], ['y'], ['z']]))
        );

        $this->assertEquals([[1, 'a'], [2, 'b'], [3, 'c']], iterator_to_array(call_user_func(zip, [1, 2, 3], ['a', 'b', 'c'])));
        $this->assertEquals('\nspl\a\lazy\zip', zip);
    }

    public function testZipWith()
    {
        $sum = function($x, $y) { return $x + $y; };
        $sum3 = function($x, $y, $z) { return $x + $y + $z; };

        $this->assertInstanceOf(\Generator::class, zipWith($sum, [1, 2, 3], [1, 2, 3]));

        $this->assertEquals([2, 4, 6], iterator_to_array(zipWith($sum, [1, 2, 3], [1, 2, 3])));
        $this->assertEquals([3, 6, 9], iterator_to_array(zipWith($sum3, [1, 2, 3], [1, 2, 3], [1, 2, 3])));

        $this->assertEquals(
            [2, 4, 6],
            iterator_to_array(zipWith(
                $sum,
                (function() { foreach ([1, 2, 3] as $value) yield $value; })(),
                (function() { foreach ([1, 2, 3] as $value) yield $value; })()
            ))
        );

        $numbers = (function() {
            for ($i = 0; $i < 9; ++$i) yield $i;
        })();
        $this->assertEquals(
            [3, 12, 21],
            iterator_to_array(zipWith($sum3, $numbers, $numbers, $numbers))
        );

        $this->assertEquals([3, 6, 9], iterator_to_array(call_user_func(zipWith, $sum3, [1, 2, 3], [1, 2, 3], [1, 2, 3])));
        $this->assertEquals('\nspl\a\lazy\zipWith', zipWith);
    }

    public function testFilter()
    {
        $this->assertInstanceOf(\Generator::class, filter('is_numeric', ['a', 1, 'b', 2, 'c', 3]));

        $this->assertEqualsCanonicalizing([1, 2, 3], iterator_to_array(filter('is_numeric', ['a', 1, 'b', 2, 'c', 3])));
        $this->assertEquals(
            array('b' => 2),
            iterator_to_array(filter(function($v) { return $v % 2 === 0; }, array('a' => 1, 'b' => 2, 'c' => 3)))
        );
        $this->assertEquals([], iterator_to_array(filter('is_int', [])));

        $this->assertEqualsCanonicalizing([1, 2, 3], iterator_to_array(call_user_func(filter, 'is_numeric', ['a', 1, 'b', 2, 'c', 3])));
        $this->assertEquals('\nspl\a\lazy\filter', filter);
    }

    public function testFilterNot()
    {
        $this->assertInstanceOf(\Generator::class, filterNot('is_numeric', ['a', 1, 'b', 2, 'c', 3]));

        $this->assertEqualsCanonicalizing(['a', 'b', 'c'], iterator_to_array(filterNot('is_numeric', ['a', 1, 'b', 2, 'c', 3])));
        $this->assertEquals(
            array('a' => 1, 'c' => 3),
            iterator_to_array(filterNot(function($v) { return $v % 2 === 0; }, array('a' => 1, 'b' => 2, 'c' => 3)))
        );
        $this->assertEquals([], iterator_to_array(filterNot('is_int', [])));

        $this->assertEqualsCanonicalizing(['a', 'b', 'c'], iterator_to_array(call_user_func(filterNot, 'is_numeric', ['a', 1, 'b', 2, 'c', 3])));
        $this->assertEquals('\nspl\a\lazy\filterNot', filterNot);
    }

    public function testTake()
    {
        $this->assertInstanceOf(\Generator::class, take([1, 2, 3, 4, 5, 6, 7, 8, 9], 3));

        $this->assertEquals([1, 2, 3], iterator_to_array(take([1, 2, 3, 4, 5, 6, 7, 8, 9], 3)));
        $this->assertEquals([1, 2, 3], iterator_to_array(take(new \ArrayIterator([1, 2, 3, 4, 5, 6, 7, 8, 9]), 3)));
        $this->assertEquals([1, 3, 5], iterator_to_array(take([1, 2, 3, 4, 5, 6, 7, 8, 9], 3, 2)));
        $this->assertEquals([1, 3, 5], iterator_to_array(take(new \ArrayIterator([1, 2, 3, 4, 5, 6, 7, 8, 9]), 3, 2)));
        $this->assertEquals([1, 4, 7], iterator_to_array(take([1, 2, 3, 4, 5, 6, 7, 8, 9], 5, 3)));
        $this->assertEquals([], iterator_to_array(take([1, 2, 3, 4, 5, 6, 7, 8, 9], 0)));
        $this->assertEquals([], iterator_to_array(take([], 3)));
        $this->assertEquals([], iterator_to_array(take([], 3, 2)));

        $this->assertEquals([1, 2, 3], iterator_to_array(call_user_func(take, [1, 2, 3, 4, 5, 6, 7, 8, 9], 3)));
        $this->assertEquals('\nspl\a\lazy\take', take);
    }

    public function testTakeWhile()
    {
        $this->assertInstanceOf(\Generator::class, takeWhile('is_numeric', [1, 2, 3, 'a', 'b', 'c', 4, 5, 6]));

        $this->assertEquals([1, 2, 3], iterator_to_array(takeWhile('is_numeric', [1, 2, 3, 'a', 'b', 'c', 4, 5, 6])));
        $this->assertEquals([1, 2, 3], iterator_to_array(takeWhile(rpartial(lt, 4), [1, 2, 3, 4, 5, 6, 7, 8, 9])));
        $this->assertEquals([1, 2, 3], iterator_to_array(takeWhile(rpartial(lt, 4), new \ArrayIterator([1, 2, 3, 4, 5, 6, 7, 8, 9]))));
        $this->assertEquals([], iterator_to_array(takeWhile(rpartial(lt, 4), [])));

        $this->assertEquals([1, 2, 3], iterator_to_array(call_user_func(takeWhile, rpartial(lt, 4), [1, 2, 3, 4, 5, 6, 7, 8, 9])));
        $this->assertEquals('\nspl\a\lazy\takeWhile', takeWhile);
    }

    public function testDrop()
    {
        $this->assertInstanceOf(\Generator::class, drop([1, 2, 3, 4, 5, 6, 7, 8, 9], 6));

        $this->assertEquals([7, 8, 9], iterator_to_array(drop([1, 2, 3, 4, 5, 6, 7, 8, 9], 6)));
        $this->assertEquals([7, 8, 9], iterator_to_array(drop(new \ArrayIterator([1, 2, 3, 4, 5, 6, 7, 8, 9]), 6)));
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], iterator_to_array(drop([1, 2, 3, 4, 5, 6, 7, 8, 9], 0)));
        $this->assertEquals([], iterator_to_array(drop([], 3)));

        $this->assertEquals([7, 8, 9], iterator_to_array(call_user_func(drop, [1, 2, 3, 4, 5, 6, 7, 8, 9], 6)));
        $this->assertEquals('\nspl\a\lazy\drop', drop);
    }

    public function testDropWhile()
    {
        $this->assertInstanceOf(\Generator::class, dropWhile('is_numeric', [1, 2, 3, 'a', 'b', 'c', 4, 5, 6]));

        $this->assertEquals(['a', 'b', 'c', 4, 5, 6], iterator_to_array(dropWhile('is_numeric', [1, 2, 3, 'a', 'b', 'c', 4, 5, 6])));
        $this->assertEquals([4, 5, 6, 7, 8, 9], iterator_to_array(dropWhile(rpartial(lt, 4), [1, 2, 3, 4, 5, 6, 7, 8, 9])));
        $this->assertEquals([4, 5, 6, 7, 8, 9], iterator_to_array(dropWhile(rpartial(lt, 4), new \ArrayIterator([1, 2, 3, 4, 5, 6, 7, 8, 9]))));
        $this->assertEquals([], iterator_to_array(dropWhile(rpartial(lt, 4), [])));

        $this->assertEquals([4, 5, 6, 7, 8, 9], iterator_to_array(call_user_func(dropWhile, rpartial(lt, 4), [1, 2, 3, 4, 5, 6, 7, 8, 9])));
        $this->assertEquals('\nspl\a\lazy\dropWhile', dropWhile);
    }

    public function testPartition()
    {
        $result = partition('is_numeric', ['a', 1, 'b', 2, 'c', 3]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey(0, $result);
        $this->assertArrayHasKey(1, $result);
        $this->assertInstanceOf(\Generator::class, $result[0]);
        $this->assertInstanceOf(\Generator::class, $result[1]);
        $this->assertEquals([1, 2, 3], array_values(iterator_to_array($result[0])));
        $this->assertEquals(['a', 'b', 'c'], array_values(iterator_to_array($result[1])));

        $this->assertEquals('\nspl\a\lazy\partition', partition);
    }

    public function testFlatten()
    {
        $this->assertInstanceOf(\Generator::class, flatten([[1, 2, 3], [4, 5, 6], [7, 8, 9]]));

        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], iterator_to_array(flatten([[1, 2, 3], [4, 5, 6], [7, 8, 9]])));
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], iterator_to_array(flatten(new \ArrayIterator([[1, 2, 3], new \ArrayIterator([4, 5, 6]), [7, 8, 9]]))));
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], iterator_to_array(flatten([[1, [2, [3]]], [[[4, 5, 6]]], 7, 8, [9]])));
        $this->assertEquals([1, [2, [3]], [[4, 5, 6]], 7, 8, 9], iterator_to_array(flatten([[1, [2, [3]]], [[[4, 5, 6]]], 7, 8, [9]], 1)));
        $this->assertEquals([1, 2, [3], [4, 5, 6], 7, 8, 9], iterator_to_array(flatten([[1, [2, [3]]], [[[4, 5, 6]]], 7, 8, [9]], 2)));
        $this->assertEquals([1], iterator_to_array(flatten([1])));
        $this->assertEquals([], iterator_to_array(flatten([])));

        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], iterator_to_array(call_user_func(flatten, [[1, 2, 3], [4, 5, 6], [7, 8, 9]])));
        $this->assertEquals('\nspl\a\lazy\flatten', flatten);
    }

    public function testPairs()
    {
        $this->assertInstanceOf(\Generator::class, pairs(['a', 'b', 'c']));

        $this->assertEquals([[0, 'a'], [1, 'b'], [2, 'c']], iterator_to_array(pairs(['a', 'b', 'c'])));
        $this->assertEquals([['a', 'hello'], ['b', 'world'], ['c', 42]], iterator_to_array(pairs(array('a' => 'hello', 'b' => 'world', 'c' => 42))));
        $this->assertEquals([], iterator_to_array(pairs([])));

        $this->assertEquals([[0, 'a'], [1, 'b'], [2, 'c']], iterator_to_array(call_user_func(pairs, (['a', 'b', 'c']))));
        $this->assertEquals([['a', 'hello'], ['b', 'world'], ['c', 42]], iterator_to_array(call_user_func(pairs, (array('a' => 'hello', 'b' => 'world', 'c' => 42)))));
        $this->assertEquals([], iterator_to_array(call_user_func(pairs, ([]))));
        $this->assertEquals('\nspl\a\lazy\pairs', pairs);
    }

    public function testKeys()
    {
        $this->assertInstanceOf(\Generator::class, keys(array('a' => 1, 'b' => 2, 'c' => 3)));

        $this->assertEquals(['a', 'b', 'c'], iterator_to_array(keys(array('a' => 1, 'b' => 2, 'c' => 3))));

        $this->assertEquals(['a', 'b', 'c'], iterator_to_array(call_user_func(keys, array('a' => 1, 'b' => 2, 'c' => 3))));
        $this->assertEquals('\nspl\a\lazy\keys', keys);
    }

    public function testWith()
    {
        $square = function($n) {
            return $n * $n;
        };

        $isEven = function($n) {
            return $n % 2 === 0;
        };

        $this->assertEquals(20, with((function() {
            $current = 1;
            while (true) yield $current++;
        })())
            ->take(5)
            ->filter($isEven)
            ->map($square)
            ->reduce(sum)
        );

        $this->assertEquals([4, 16, 36],  with((function() {
            $current = 1;
            while (true) yield $current++;
        })())
            ->filter($isEven)
            ->take(3)
            ->map($square)
            ->toArray()
        );
    }

}

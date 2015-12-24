<?php

namespace NsplTest;

use \nspl\f;
use function \nspl\f\map;
use function \nspl\f\reduce;
use function \nspl\f\filter;
use function \nspl\f\apply;
use function \nspl\f\partial;
use function \nspl\f\rpartial;
use function \nspl\f\ppartial;
use function \nspl\f\compose;
use function \nspl\f\memoized;
use function \nspl\f\pipe;
use function \nspl\f\I;
use function \nspl\f\curried;
use function \nspl\f\uncurried;

class FTest extends \PHPUnit_Framework_TestCase
{
    public function testMap()
    {
        $this->assertEquals(['A', 'B', 'C'], map('strtoupper', ['a', 'b', 'c']));
        $this->assertEquals([1, 4, 9], map(function($v) { return $v * $v; }, new \ArrayIterator([1, 2, 3])));
        $this->assertEquals(['a' => 0, 'b' => 1, 'c' => 2], map('abs', array('a' => 0, 'b' => -1, 'c' => 2)));
        $this->assertEquals([], map('strtoupper', []));

        $this->assertEquals(['A', 'B', 'C'], call_user_func(f::$map, 'strtoupper', ['a', 'b', 'c']));
        $this->assertEquals([1, 4, 9], call_user_func(f::$map, function($v) { return $v * $v; }, new \ArrayIterator([1, 2, 3])));
        $this->assertEquals(['a' => 0, 'b' => 1, 'c' => 2], call_user_func(f::$map, 'abs', array('a' => 0, 'b' => -1, 'c' => 2)));
        $this->assertEquals([], call_user_func(f::$map, 'strtoupper', []));
    }

    public function testReduce()
    {
        $this->assertEquals(6, reduce(function($a, $b) { return $a + $b; }, [1, 2, 3]));
        $this->assertEquals('abc', reduce(function($a, $b) { return $a . $b; }, new \ArrayIterator(['a', 'b', 'c']), ''));
        $this->assertEquals(64, reduce('pow', array('a' => 3, 'b' => 2, 'c' => 1), 2));
        $this->assertEquals(0, reduce(function($a, $b) { return $a * $b; }, [], 0));
        $this->assertEquals(1, reduce(function($a, $b) { return $a * $b; }, [], 1));

        $this->assertEquals(6, call_user_func(f::$reduce, function($a, $b) { return $a + $b; }, [1, 2, 3]));
        $this->assertEquals('abc', call_user_func(f::$reduce, function($a, $b) { return $a . $b; }, new \ArrayIterator(['a', 'b', 'c']), ''));
        $this->assertEquals(64, call_user_func(f::$reduce, 'pow', array('a' => 3, 'b' => 2, 'c' => 1), 2));
        $this->assertEquals(0, call_user_func(f::$reduce, function($a, $b) { return $a * $b; }, [], 0));
        $this->assertEquals(1, call_user_func(f::$reduce, function($a, $b) { return $a * $b; }, [], 1));
    }

    public function testFilter()
    {
        $this->assertEquals([1, 2, 3], filter('is_numeric', ['a', 1, 'b', 2, 'c', 3]));
        $this->assertEquals(
            array('b' => 2),
            filter(function($v) { return $v % 2 === 0; }, array('a' => 1, 'b' => 2, 'c' => 3))
        );
        $this->assertEquals([], filter('is_int', []));

        $this->assertEquals([1, 2, 3], call_user_func(f::$filter, 'is_numeric', ['a', 1, 'b', 2, 'c', 3]));
        $this->assertEquals(
            array('b' => 2),
            call_user_func(f::$filter, function($v) { return $v % 2 === 0; }, array('a' => 1, 'b' => 2, 'c' => 3))
        );
        $this->assertEquals([], call_user_func(f::$filter, 'is_int', []));
    }

    public function testApply()
    {
        $this->assertEquals([1, 3, 5, 7, 9], apply('range', [1, 10, 2]));
        $this->assertEquals(time(), apply('time'), '', 0.1);

        $this->assertEquals([1, 3, 5, 7, 9], call_user_func(f::$apply, 'range', [1, 10, 2]));
        $this->assertEquals(time(), call_user_func(f::$apply, 'time'), '', 0.1);
    }

    public function testPartial()
    {
        $sqrList = partial('array_map', function($v) { return $v * $v; });
        $this->assertEquals([1, 4, 9], $sqrList([1, 2, 3]));
        $this->assertEquals([], $sqrList([]));

        $oneArgFuncPartial = partial('count', [1, 2, 3]);
        $this->assertEquals(3, $oneArgFuncPartial());

        $noArgFuncPartial = partial('time', null);
        $this->assertEquals(time(), $noArgFuncPartial(), '', 0.1);

        $sqrList = call_user_func(f::$partial, 'array_map', function($v) { return $v * $v; });
        $this->assertEquals([1, 4, 9], $sqrList([1, 2, 3]));
        $this->assertEquals([], $sqrList([]));

        $oneArgFuncPartial = call_user_func(f::$partial, 'count', [1, 2, 3]);
        $this->assertEquals(3, $oneArgFuncPartial());

        $noArgFuncPartial = call_user_func(f::$partial, 'time', null);
        $this->assertEquals(time(), $noArgFuncPartial(), '', 0.1);
    }

    public function testRpartial()
    {
        $cube = rpartial('pow', 3);
        $this->assertEquals(27, $cube(3));

        $oneArgFuncPartial = rpartial('count', [1, 2, 3]);
        $this->assertEquals(3, $oneArgFuncPartial());

        $noArgFuncPartial = rpartial('time', null);
        $this->assertEquals(time(), $noArgFuncPartial(), '', 0.1);

        $cube = call_user_func(f::$rpartial, 'pow', 3);
        $this->assertEquals(27, $cube(3));

        $oneArgFuncPartial = call_user_func(f::$rpartial, 'count', [1, 2, 3]);
        $this->assertEquals(3, $oneArgFuncPartial());

        $noArgFuncPartial = call_user_func(f::$rpartial, 'time', null);
        $this->assertEquals(time(), $noArgFuncPartial(), '', 0.1);
    }

    public function testPpartial()
    {
        $oddNumbers = ppartial('range', array(0 => 1, 2 => 2));
        $this->assertEquals([1], $oddNumbers(1));
        $this->assertEquals([1, 3, 5], $oddNumbers(6));

        $oneArgFuncPartial = ppartial('count', array(0 => [1, 2, 3]));
        $this->assertEquals(3, $oneArgFuncPartial());

        $noArgFuncPartial = ppartial('time', array(0 => null));
        $this->assertEquals(time(), $noArgFuncPartial(), '', 0.1);

        $f = function($a, $b, $c) { return $a . $b . $c; };
        $f1 = ppartial($f, array(0 => 'a'));
        $this->assertEquals('abc', call_user_func($f1, 'b', 'c'));

        $oddNumbers = call_user_func(f::$ppartial, 'range', array(0 => 1, 2 => 2));
        $this->assertEquals([1], $oddNumbers(1));
        $this->assertEquals([1, 3, 5], $oddNumbers(6));

        $oneArgFuncPartial = call_user_func(f::$ppartial, 'count', array(0 => [1, 2, 3]));
        $this->assertEquals(3, $oneArgFuncPartial());

        $noArgFuncPartial = call_user_func(f::$ppartial, 'time', array(0 => null));
        $this->assertEquals(time(), $noArgFuncPartial(), '', 0.1);
    }

    public function testMemoized()
    {
        $calculationsPerformed = 0;
        $f = function($arg1, $arg2) use (&$calculationsPerformed) {
            ++$calculationsPerformed;
            return $arg1;
        };
        $object = (object) array('name' => 'Hello wordl', 'answer' => 42);

        $memoized = memoized($f);

        $result = $memoized(1, 'a');
        $this->assertEquals(1, $result);
        $this->assertEquals(1, $calculationsPerformed);

        $result = $memoized(1, 'a');
        $this->assertEquals(1, $result);
        $this->assertEquals(1, $calculationsPerformed);

        $result = $memoized(1, ['b']);
        $result = $memoized(1, ['b']);
        $this->assertEquals(1, $result);
        $this->assertEquals(2, $calculationsPerformed);

        $result = $memoized(null, ['b']);
        $result = $memoized(null, ['b']);
        $this->assertEquals(null, $result);
        $this->assertEquals(3, $calculationsPerformed);

        $result = $memoized($object, true);
        $result = $memoized($object, true);
        $this->assertEquals($object, $result);
        $this->assertEquals(4, $calculationsPerformed);

        $calculationsPerformed = 0;
        $memoized = call_user_func(f::$memoized, $f);

        $result = $memoized(1, 'a');
        $this->assertEquals(1, $result);
        $this->assertEquals(1, $calculationsPerformed);

        $result = $memoized(1, 'a');
        $this->assertEquals(1, $result);
        $this->assertEquals(1, $calculationsPerformed);

        $result = $memoized(1, ['b']);
        $result = $memoized(1, ['b']);
        $this->assertEquals(1, $result);
        $this->assertEquals(2, $calculationsPerformed);

        $result = $memoized(null, ['b']);
        $result = $memoized(null, ['b']);
        $this->assertEquals(null, $result);
        $this->assertEquals(3, $calculationsPerformed);

        $result = $memoized($object, true);
        $result = $memoized($object, true);
        $this->assertEquals($object, $result);
        $this->assertEquals(4, $calculationsPerformed);
    }

    public function testCompose()
    {
        $countFiltered = compose('count', f::$filter);
        $this->assertEquals(3, $countFiltered('is_int', [1, 'a', 2, 'b', 3]));

        $underscoreToCamelcase = compose(
            'lcfirst',
            partial('str_replace', '_', ''),
            rpartial('ucwords', '_')
        );
        $this->assertEquals('underscoreToCamelcase', $underscoreToCamelcase('underscore_to_camelcase'));

        $countFiltered = call_user_func(f::$compose, 'count', f::$filter);
        $this->assertEquals(3, $countFiltered('is_int', [1, 'a', 2, 'b', 3]));

        $underscoreToCamelcase = call_user_func(f::$compose,
            'lcfirst',
            partial('str_replace', '_', ''),
            rpartial('ucwords', '_')
        );
        $this->assertEquals('underscoreToCamelcase', $underscoreToCamelcase('underscore_to_camelcase'));
    }

    public function testPipe()
    {
        $this->assertEquals('underscoreToCamelcase', pipe(
            'underscore_to_camelcase',
            rpartial('ucwords', '_'),
            partial('str_replace', '_', ''),
            'lcfirst'
        ));

        $this->assertEquals('underscoreToCamelcase', call_user_func(f::$pipe,
            'underscore_to_camelcase',
            rpartial('ucwords', '_'),
            partial('str_replace', '_', ''),
            'lcfirst'
        ));
    }

    public function testI()
    {
        $this->assertEquals('underscoreToCamelcase', I(
            'underscore_to_camelcase',
            rpartial('ucwords', '_'),
            partial('str_replace', '_', ''),
            'lcfirst'
        ));
    }

    public function testCurried()
    {
        $curriedStrReplace = curried('str_replace');
        $replaceUnderscores = $curriedStrReplace('_');
        $replaceUnderscoresWithSpaces = $replaceUnderscores(' ');

        $this->assertEquals('Hello world!', $replaceUnderscoresWithSpaces('Hello_world!'));

        $f = function($a, $b, $c = 'c') {
            return join('', func_get_args());
        };

        $curriedF = curried($f);
        $f1 = $curriedF('a');
        $this->assertEquals('ab', $f1('b'));

        $curriedFWithOptinalArgs = curried($f, true);
        $f1 = $curriedFWithOptinalArgs('a');
        $f2 = $f1('b');
        $this->assertEquals('abc', $f2('c'));

        $curriedFWithFourArgs = curried($f, 4);
        $f1 = $curriedFWithFourArgs('a');
        $f2 = $f1('b');
        $f3 = $f2('c');
        $this->assertEquals('abcd', $f3('d'));
    }

    public function testUncurried()
    {
        $curriedStrReplace = curried('str_replace');
        $strReplace = uncurried($curriedStrReplace);

        $this->assertEquals('Hello world!', $strReplace('_', ' ', 'Hello_world!'));
    }

}

<?php

namespace nspl\a;

use nspl\f;
use nspl\ds;
use nspl\op;

/**
 * Returns true if all elements of the $sequence satisfy the predicate are true (or if the $sequence is empty).
 * If predicate was not passed return true if all elements of the $sequence are true.
 *
 * @param array|\Traversable $sequence
 * @param callable $predicate
 * @return bool
 */
function all($sequence, callable $predicate = null)
{
    foreach ($sequence as $value) {
        if ($predicate && !call_user_func($predicate, $value) || !$predicate && !$value) {
            return false;
        }
    }

    return true;
}

/**
 * Returns true if any element of the $sequence satisfies the predicate. If predicate was not passed returns true if
 * any element of the $sequence is true. If the $sequence is empty, returns false.
 *
 * @param array|\Traversable $sequence
 * @param callable $predicate
 * @return bool
 */
function any($sequence, callable $predicate = null)
{
    foreach ($sequence as $value) {
        if ($predicate && call_user_func($predicate, $value) || !$predicate && $value) {
            return true;
        }
    }

    return false;
}

/**
 * Returns array value by key if it exists otherwise returns the default value
 *
 * @param array $array
 * @param int|string $key
 * @param mixed $default
 * @return mixed
 */
function getByKey(array $array, $key, $default = null)
{
    return isset($array[$key]) || array_key_exists($key, $array) ? $array[$key] : $default;
}

/**
 * Adds $list2 items to the end of $list1
 *
 * @param array $list1
 * @param array $list2
 * @return array
 */
function extend(array $list1, array $list2)
{
    return array_merge($list1, $list2);
}

/**
 * Zips two or more lists
 *
 * @param array $list1
 * @param array $list2
 * @return array
 */
function zip(array $list1, array $list2)
{
    $lists = func_get_args();
    $count = func_num_args();

    for ($j = 0; $j < $count; ++$j) {
        if (!ds\isList($lists[$j])) {
            $lists[$j] = array_values($lists[$j]);
        }
    }

    $i = 0;
    $result = array();
    do {
        $zipped = array();
        for ($j = 0; $j < $count; ++$j) {
            if (!isset($lists[$j][$i]) && !array_key_exists($i, $lists[$j])) {
                break 2;
            }
            $zipped[] = $lists[$j][$i];
        }
        $result[] = $zipped;
        ++$i;
    } while (true);

    return $result;
}

/**
 * Flattens multidimensional list
 *
 * @param array $list
 * @param int|null $depth
 * @return array
 */
function flatten(array $list, $depth = null)
{
    if (null === $depth) {
        $result = array();
        array_walk_recursive($list, function($a) use (&$result) { $result[] = $a; });
        return $result;
    }

    $result = array();
    foreach ($list as $value) {
        if ($depth && is_array($value)) {
            foreach (flatten($value, $depth - 1) as $subValue) {
                $result[] = $subValue;
            }
        }
        else {
            $result[] = $value;
        }
    }

    return $result;
}

/**
 * Returns list of (key, value) pairs
 * @param array|\Traversable $array
 * @param bool $valueKey If true then convert array to (value, key) pairs
 * @return array
 */
function pairs($array, $valueKey = false)
{
    if (!$array) {
        return array();
    }

    $result = array();
    foreach ($array as $key => $value) {
        $result[] = $valueKey ? array($value, $key) : array($key, $value);
    }

    return $result;
}

/**
 * Returns sorted copy of passed array
 *
 * @param array $array
 * @param bool $descending If true then sort array in descending order. If not boolean and $key was not passed then acts as a $key parameter
 * @param callable $key Function of one argument that is used to extract a comparison key from each element
 * @param callable $cmp Function of two arguments which returns a negative number, zero or positive number depending on
 *                      whether the first argument is smaller than, equal to, or larger than the second argument
 * @return array
 */
function sorted(array $array, $descending = false, callable $key = null, callable $cmp = null)
{
    if (!$cmp) {
        $cmp = function ($a, $b) { return $a > $b ? 1 : -1; };
    }

    if (!is_bool($descending) && !$key) {
        $key = $descending;
    }

    if ($key) {
        $cmp = function($a, $b) use ($key, $cmp) {
            return call_user_func_array($cmp, array($key($a), $key($b)));
        };
    }

    if (is_bool($descending) && $descending) {
        $cmp = f\compose(op::$neg, $cmp);
    }

    $isList = ds\isList($array);
    uasort($array, $cmp);

    return $isList ? array_values($array) : $array;
}

/**
 * Returns copy of passed array sorted by keys
 *
 * @param array $array
 * @param bool $reversed
 * @return array
 */
function keySorted(array $array, $reversed = false)
{
    if ($reversed) {
        krsort($array);
    }
    else {
        ksort($array);
    }

    return $array;
}

/**
 * Returns indexed list of items
 *
 * @param array $list List of arrays or objects
 * @param int|string|callable $by An array key or a function
 * @param bool $keepLast If true only the last item with the key will be returned otherwise list of items which share the same key value will be returned
 * @param callable|null $transform A function that transforms list item after indexing
 * @return array
 */
function indexed(array $list, $by, $keepLast = true, callable $transform = null)
{
    $indexIsCallable = is_callable($by);

    $result = array();
    foreach ($list as $item) {
        if ($indexIsCallable || isset($item[$by]) || array_key_exists($by, $item)) {
            $index = $indexIsCallable ? call_user_func($by, $item) : $item[$by];

            if ($keepLast) {
                $result[$index] = $transform ? call_user_func($transform, $item) : $item;;
                continue;
            }

            if (!isset($result[$index])) {
                $result[$index] = [];
            }

            $result[$index][] = $transform ? call_user_func($transform, $item) : $item;;
        }
    }

    return $result;
}

/**
 * Returns first N list items
 *
 * @param array $list
 * @param int $N
 * @param int $step
 * @return array
 */
function take(array $list, $N, $step = 1)
{
    if (1 === $step) {
        return array_values(array_slice($list, 0, $N));
    }

    $result = array();
    $length = min(count($list), $N * $step);
    for ($i = 0; $i < $length; $i += $step) {
        $result[] = $list[$i];
    }

    return $result;
}

/**
 * Returns the first list item
 *
 * @param array $list
 * @return array
 */
function first(array $list)
{
    if (!$list) {
        throw new \InvalidArgumentException('Can not return the first item of an empty list');
    }

    if (isset($list[0]) || array_key_exists(0, $list)) {
        return $list[0];
    }

    reset($list);
    return current($list);
}

/**
 * Drops first N list items
 *
 * @param array $list
 * @param int $N
 * @return array
 */
function drop(array $list, $N)
{
    return array_slice($list, $N);
}

/**
 * Returns the last list item
 *
 * @param array $list
 * @return array
 */
function last(array $list)
{
    if (!$list) {
        throw new \InvalidArgumentException('Can not return the last item of an empty list');
    }

    return $list[count($list) - 1];
}

/**
 * Moves list element to another position
 *
 * @param array $list
 * @param int $from
 * @param int $to
 * @return array
 */
function moveElement(array $list, $from, $to)
{
    if (!ds\isList($list)) {
        throw new \InvalidArgumentException('First argument should be a list');
    }

    if (!isset($list[$from]) || !isset($list[$to])) {
        throw new \InvalidArgumentException('From and to should be valid list keys');
    }

    if ($from === $to) {
        return $list;
    }

    $moving = array_splice($list, $from, 1);
    array_splice($list, $to, 0, $moving);

    return $list;
}


namespace nspl;

class a
{
    static public $all;
    static public $any;
    static public $getByKey;
    static public $extend;
    static public $zip;
    static public $flatten;
    static public $pairs;
    static public $sorted;
    static public $keySorted;
    static public $indexed;
    static public $take;
    static public $first;
    static public $drop;
    static public $last;
    static public $moveElement;

}

a::$all = function($sequence, callable $predicate = null) { return \nspl\a\all($sequence, $predicate); };
a::$any = function($sequence, callable $predicate = null) { return \nspl\a\any($sequence, $predicate); };
a::$getByKey = function(array $array, $key, $default = null) { return \nspl\a\getByKey($array, $key, $default); };
a::$extend = function(array $list1, array $list2) { return \nspl\a\extend($list1, $list2); };
a::$zip = function(array $list1, array $list2) { return call_user_func_array('\nspl\a\zip', func_get_args()); };
a::$flatten = function(array $list, $depth = null) { return \nspl\a\flatten($list, $depth); };
a::$pairs = function(array $array) { return \nspl\a\pairs($array); };
a::$sorted = function(array $array, $reversed = false, $key = null, callable $cmp = null) { return \nspl\a\sorted($array, $reversed, $key, $cmp); };
a::$keySorted = function(array $array, $reversed = false) { return \nspl\a\sorted($array, $reversed); };
a::$indexed = function(array $listOfArrays, $by, $keepLast = true, callable $transform = null) { return \nspl\a\indexed($listOfArrays, $by, $keepLast, $transform); };
a::$take = function(array $list, $N, $step = 1) { return \nspl\a\take($list, $N, $step); };
a::$first = function(array $list) { return \nspl\a\first($list); };
a::$drop = function(array $list, $N) { return \nspl\a\drop($list, $N); };
a::$last = function(array $list) { return \nspl\a\last($list); };
a::$moveElement = function(array $list, $from, $to) { return \nspl\a\moveElement($list, $from, $to); };

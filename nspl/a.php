<?php

namespace nspl\a;

use nspl\f;
use nspl\ds;
use nspl\op;
use nspl\args;

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
    args\expectsTraversable($sequence);

    foreach ($sequence as $value) {
        if ($predicate && !call_user_func($predicate, $value) || !$predicate && !$value) {
            return false;
        }
    }

    return true;
}
const all = '\nspl\a\all';

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
    args\expectsTraversable($sequence);

    foreach ($sequence as $value) {
        if ($predicate && call_user_func($predicate, $value) || !$predicate && $value) {
            return true;
        }
    }

    return false;
}
const any = '\nspl\a\any';

/**
 * Returns array value by key if it exists otherwise returns the default value
 *
 * @param array|\ArrayAccess $array
 * @param int|string $key
 * @param mixed $default
 * @return mixed
 */
function getByKey($array, $key, $default = null)
{
    args\expectsArrayAccess($array);

    return isset($array[$key]) || array_key_exists($key, $array) ? $array[$key] : $default;
}
const getByKey = '\nspl\a\getByKey';

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
const extend = '\nspl\a\extend';

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
const zip = '\nspl\a\zip';

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
            foreach ($depth > 1 ? flatten($value, $depth - 1) : $value as $subValue) {
                $result[] = $subValue;
            }
        }
        else {
            $result[] = $value;
        }
    }

    return $result;
}
const flatten = '\nspl\a\flatten';

/**
 * Returns list of (key, value) pairs
 * @param array|\Traversable $array
 * @param bool $valueKey If true then convert array to (value, key) pairs
 * @return array
 */
function pairs($array, $valueKey = false)
{
    args\expectsTraversable($array);

    if (!$array) {
        return array();
    }

    $result = array();
    foreach ($array as $key => $value) {
        $result[] = $valueKey ? array($value, $key) : array($key, $value);
    }

    return $result;
}
const pairs = '\nspl\a\pairs';

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
        $cmp = f\compose(op\neg, $cmp);
    }

    $isList = ds\isList($array);
    uasort($array, $cmp);

    return $isList ? array_values($array) : $array;
}
const sorted = '\nspl\a\sorted';

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
const keySorted = '\nspl\a\keySorted';

/**
 * Returns indexed list of items
 *
 * @param array|\Traversable $list List of arrays or objects
 * @param int|string|callable $by An array key or a function
 * @param bool $keepLast If true only the last item with the key will be returned otherwise list of items which share the same key value will be returned
 * @param callable|null $transform A function that transforms list item after indexing
 * @return array
 */
function indexed(array $list, $by, $keepLast = true, callable $transform = null)
{
    args\expectsTraversable($list);

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
const indexed = '\nspl\a\indexed';

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
const take = '\nspl\a\take';

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
const first = '\nspl\a\first';

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
const drop = '\nspl\a\drop';

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
const last = '\nspl\a\last';

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
const moveElement = '\nspl\a\moveElement';

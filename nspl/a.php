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
    args\expectsArrayKey($key);

    return isset($array[$key]) || array_key_exists($key, $array) ? $array[$key] : $default;
}
const getByKey = '\nspl\a\getByKey';

/**
 * Returns arrays containing $sequence1 items and $sequence2 items
 *
 * @param array|\Traversable $sequence1
 * @param array|\Traversable $sequence2
 * @return array
 */
function extend($sequence1, $sequence2)
{
    args\expectsTraversable($sequence1);
    args\expectsTraversable($sequence2, 2);

    return array_merge(ds\traversableToArray($sequence1), ds\traversableToArray(($sequence2)));
}
const extend = '\nspl\a\extend';

/**
 * Zips two or more sequences
 *
 * @param array|\Traversable $sequence1
 * @param array|\Traversable $sequence2
 * @return array
 */
function zip($sequence1, $sequence2)
{
    $lists = func_get_args();
    $count = func_num_args();

    for ($j = 0; $j < $count; ++$j) {
        args\expectsTraversable($lists[$j], $j + 1);
        $lists[$j] = ds\traversableToArray($lists[$j]);

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
 * @param array|\Traversable $sequence
 * @param int|null $depth
 * @return array
 */
function flatten($sequence, $depth = null)
{
    args\expectsTraversable($sequence);

    if (null === $depth) {
        $result = array();
        array_walk_recursive(ds\traversableToArray($sequence), function($item, $key) use (&$result) {
            if ($item instanceof \Traversable) {
                $result = array_merge($result, flatten(ds\traversableToArray($item)));
            }
            else {
                $result[] = $item;
            }
        });
        return $result;
    }

    $result = array();
    foreach ($sequence as $value) {
        if ($depth && (is_array($value) || $value instanceof \Traversable)) {
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
 * @param array|\Traversable $sequence
 * @param bool $valueKey If true then convert array to (value, key) pairs
 * @return array
 */
function pairs($sequence, $valueKey = false)
{
    args\expectsTraversable($sequence);
    args\expectsBool($valueKey);

    if (!$sequence) {
        return array();
    }

    $result = array();
    foreach ($sequence as $key => $value) {
        $result[] = $valueKey ? array($value, $key) : array($key, $value);
    }

    return $result;
}
const pairs = '\nspl\a\pairs';

/**
 * Returns array which contains sorted items the passed sequence
 *
 * @param array|\Traversable $array
 * @param bool $reversed If true then return reversed sorted sequence. If not boolean and $key was not passed then acts as a $key parameter
 * @param callable $key Function of one argument that is used to extract a comparison key from each element
 * @param callable $cmp Function of two arguments which returns a negative number, zero or positive number depending on
 *                      whether the first argument is smaller than, equal to, or larger than the second argument
 * @return array
 */
function sorted($array, $reversed = false, callable $key = null, callable $cmp = null)
{
    args\expectsTraversable($array);
    args\expectsBoolOrCallable($reversed);

    if (!$cmp) {
        $cmp = function ($a, $b) { return $a > $b ? 1 : -1; };
    }

    if (!is_bool($reversed) && !$key) {
        $key = $reversed;
    }

    if ($key) {
        $cmp = function($a, $b) use ($key, $cmp) {
            return call_user_func_array($cmp, array($key($a), $key($b)));
        };
    }

    if (is_bool($reversed) && $reversed) {
        $cmp = f\compose(op\neg, $cmp);
    }

    $array = ds\traversableToArray($array);
    $isList = ds\isList($array);
    uasort($array, $cmp);

    return $isList ? array_values($array) : $array;
}
const sorted = '\nspl\a\sorted';

/**
 * Returns array which contains sequence items sorted by keys
 *
 * @param array|\Traversable $array
 * @param bool $reversed
 * @return array
 */
function keySorted($array, $reversed = false)
{
    args\expectsTraversable($array);
    args\expectsBool($array);

    $array = ds\traversableToArray($array);
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
 * Returns array which contains indexed sequence items
 *
 * @param array|\Traversable $sequence List of arrays or objects
 * @param int|string|callable $by An array key or a function
 * @param bool $keepLast If true only the last item with the key will be returned otherwise list of items which share the same key value will be returned
 * @param callable|null $transform A function that transforms list item after indexing
 * @return array
 */
function indexed($sequence, $by, $keepLast = true, callable $transform = null)
{
    args\expectsTraversable($sequence);
    args\expectsArrayKeyOrCallable($by);
    args\expectsBool($keepLast);

    $indexIsCallable = is_callable($by);

    $result = array();
    foreach ($sequence as $item) {
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
 * Returns first N sequence items
 *
 * @param array|\Traversable $list
 * @param int $N
 * @param int $step
 * @return array
 */
function take($list, $N, $step = 1)
{
    args\expectsTraversable($list);
    args\expectsInt($N);
    args\expectsInt($step);

    if (is_array($list)) {
        if (1 === $step) {
            return array_values(array_slice($list, 0, $N));
        }

        $result = array();
        $length = min(count($list), $N * $step);
        for ($i = 0; $i < $length; $i += $step) {
            $result[] = $list[$i];
        }
    }
    else {
        $counter = 0;
        $result = array();
        $length = min(count($list), $N * $step);
        foreach ($list as $item) {
            if ($counter >= $length) {
                break;
            }

            if ($counter++ % $step === 0) {
                $result[] = $item;
            }
        }
    }

    return $result;
}
const take = '\nspl\a\take';

/**
 * Returns the first sequence item
 *
 * @param array|\Traversable $sequence
 * @return array
 */
function first($sequence)
{
    args\expectsTraversable($sequence);

    if (!$sequence) {
        throw new \InvalidArgumentException('Can not return the first item of an empty list');
    }

    if (is_array($sequence) && (isset($sequence[0]) || array_key_exists(0, $sequence))) {
        return $sequence[0];
    }

    foreach ($sequence as $item) break;

    return $item;
}
const first = '\nspl\a\first';

/**
 * Drops first N sequence items
 *
 * @param array|\Traversable $sequence
 * @param int $N
 * @return array
 */
function drop($sequence, $N)
{
    args\expectsTraversable($sequence);
    args\expectsInt($N);

    if (is_array($sequence)) {
        return array_slice($sequence, $N);
    }
    else {
        $counter = 0;
        $result = array();
        foreach ($sequence as $item) {
            if ($counter++ < $N) {
                continue;
            }

            $result[] = $item;
        }

        return $result;
    }
}
const drop = '\nspl\a\drop';

/**
 * Returns the last sequence item
 *
 * @param array|\Traversable $sequence
 * @return array
 */
function last($sequence)
{
    args\expectsTraversable($sequence);

    if (!$sequence) {
        throw new \InvalidArgumentException('Can not return the last item of an empty list');
    }

    if (is_array($sequence)) {
        return $sequence[count($sequence) - 1];
    }
    else {
        foreach ($sequence as $item);
        return $item;
    }
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
    args\expectsInt($from);
    args\expectsInt($to, 3);

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

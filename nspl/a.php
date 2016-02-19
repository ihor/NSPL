<?php

namespace nspl\a;

use nspl\f;
use nspl\ds;
use nspl\op;
use nspl\args;

/**
 * Returns true if all $sequence items satisfy the predicate are true (or if the $sequence is empty).
 * If predicate was not passed return true if all $sequence items are true.
 *
 * @param array|\Traversable $sequence
 * @param callable $predicate
 * @return bool
 */
function all($sequence, callable $predicate = null)
{
    args\expects(args\traversable, $sequence);

    foreach ($sequence as $value) {
        if ($predicate && !$predicate($value) || !$predicate && !$value) {
            return false;
        }
    }

    return true;
}
const all = '\nspl\a\all';

/**
 * Returns true if any $sequence item satisfies the predicate. If predicate was not passed returns true if
 * any $sequence item is true. If the $sequence is empty, returns false.
 *
 * @param array|\Traversable $sequence
 * @param callable $predicate
 * @return bool
 */
function any($sequence, callable $predicate = null)
{
    args\expects(args\traversable, $sequence);

    foreach ($sequence as $value) {
        if ($predicate && $predicate($value) || !$predicate && $value) {
            return true;
        }
    }

    return false;
}
const any = '\nspl\a\any';

/**
 * Applies function of one argument to each sequence item
 *
 * @param callable $function
 * @param array|\Traversable $sequence
 * @return array
 */
function map(callable $function, $sequence)
{
    args\expects(args\traversable, $sequence);

    $result = [];
    foreach ($sequence as $key => $item) {
        $result[$key] = $function($item);
    }

    return $result;
}
const map = '\nspl\a\map';

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
        args\expects(args\traversable, $lists[$j], $j + 1);
        if ($lists[$j] instanceof \Iterator) {
            $lists[$j] = iterator_to_array($lists[$j]);
        }

        if (!isList($lists[$j])) {
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
 * Applies function of two arguments cumulatively to the items of sequence, from left to right to reduce the sequence
 * to a single value.
 *
 * @param callable $function
 * @param array|\Traversable $sequence
 * @param mixed $initial
 * @return array
 */
function reduce(callable $function, $sequence, $initial = 0)
{
    args\expects(args\traversable, $sequence);

    foreach ($sequence as $item) {
        $initial = $function($initial, $item);
    }

    return $initial;
}
const reduce = '\nspl\a\reduce';

/**
 * Returns sequence items that satisfy the predicate
 *
 * @param callable $predicate
 * @param array|\Traversable $sequence
 * @return array
 */
function filter(callable $predicate, $sequence)
{
    args\expects(args\traversable, $sequence);

    $prevKey = -1;
    $isList = true;

    $result = [];
    foreach ($sequence as $key => $item) {
        if ($predicate($item)) {
            $result[$key] = $item;
        }

        if ($isList) {
            if ($key !== $prevKey + 1) {
                $isList = false;
            }
            ++$prevKey;
        }
    }

    return $isList ? array_values($result) : $result;
}
const filter = '\nspl\a\filter';

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
    args\expects(args\traversable, $list);
    args\expects(args\int, $N);
    args\expects(args\int, $step, 3);

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
    args\expects(args\traversable, $sequence);

    if (!$sequence) {
        throw new \InvalidArgumentException('Can not return the first item of an empty list');
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
    args\expects(args\traversable, $sequence);
    args\expects(args\int, $N);

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
    args\expects(args\traversable, $sequence);

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
 * Returns two lists, one containing values for which your predicate returned true, and the other containing
 * the items that returned false
 *
 * @param callable $predicate
 * @param array|\Traversable $sequence
 * @return array
 */
function partition(callable $predicate, $sequence)
{
    args\expects(args\traversable, $sequence);

    $isList = isList($sequence);
    $result = [[], []];
    foreach ($sequence as $k => $v) {
        if ($isList) {
            $result[(int)!$predicate($v)][] = $v;
        }
        else {
            $result[(int)!$predicate($v)][$k] = $v;
        }
    }

    return $result;
}
const partition = '\nspl\a\partition';

/**
 * Returns two lists, one containing values for which your predicate returned true until the predicate returned
 * false, and the other containing all the items that left
 *
 * @param callable $predicate
 * @param array|\Traversable $sequence
 * @return array
 */
function span(callable $predicate, $sequence)
{
    args\expects(args\traversable, $sequence);

    $isList = isList($sequence);
    $result = [[], []];

    $listIndex = 0;
    foreach ($sequence as $k => $v) {
        if (!$predicate($v)) {
            $listIndex = 1;
        }

        if ($isList) {
            $result[$listIndex][] = $v;
        }
        else {
            $result[$listIndex][$k] = $v;
        }
    }

    return $result;
}
const span = '\nspl\a\span';

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
    args\expects(args\traversable, $sequence);
    args\expects([args\arrayKey, args\callable_], $by);
    args\expects(args\bool, $keepLast);

    $indexIsCallable = is_callable($by);

    $result = array();
    foreach ($sequence as $item) {
        if ($indexIsCallable || isset($item[$by]) || array_key_exists($by, $item)) {
            $index = $indexIsCallable ? $by($item) : $item[$by];

            if ($keepLast) {
                $result[$index] = $transform ? $transform($item) : $item;;
                continue;
            }

            if (!isset($result[$index])) {
                $result[$index] = [];
            }

            $result[$index][] = $transform ? $transform($item) : $item;;
        }
    }

    return $result;
}
const indexed = '\nspl\a\indexed';

/**
 * Returns array which contains sorted items the passed sequence
 *
 * @param array|\Traversable $sequence
 * @param bool $reversed If true then return reversed sorted sequence. If not boolean and $key was not passed then acts as a $key parameter
 * @param callable $key Function of one argument that is used to extract a comparison key from each item
 * @param callable $cmp Function of two arguments which returns a negative number, zero or positive number depending on
 *                      whether the first argument is smaller than, equal to, or larger than the second argument
 * @return array
 */
function sorted($sequence, $reversed = false, callable $key = null, callable $cmp = null)
{
    args\expects(args\traversable, $sequence);
    args\expects([args\bool, args\callable_], $reversed);

    if (!$cmp) {
        $cmp = function ($a, $b) { return $a > $b ? 1 : -1; };
    }

    if (!is_bool($reversed) && !$key) {
        $key = $reversed;
    }

    if ($key) {
        $cmp = function($a, $b) use ($key, $cmp) {
            return $cmp($key($a), $key($b));
        };
    }

    if (is_bool($reversed) && $reversed) {
        $cmp = f\compose(op\neg, $cmp);
    }

    if ($sequence instanceof \Iterator) {
        $sequence = iterator_to_array($sequence);
    }

    $isList = isList($sequence);
    uasort($sequence, $cmp);

    return $isList ? array_values($sequence) : $sequence;
}
const sorted = '\nspl\a\sorted';

/**
 * Returns array which contains sequence items sorted by keys
 *
 * @param array|\Traversable $sequence
 * @param bool $reversed
 * @return array
 */
function keySorted($sequence, $reversed = false)
{
    args\expects(args\traversable, $sequence);
    args\expects(args\bool, $reversed);

    if ($sequence instanceof \Iterator) {
        $sequence = iterator_to_array($sequence);
    }

    if ($reversed) {
        krsort($sequence);
    }
    else {
        ksort($sequence);
    }

    return $sequence;
}
const keySorted = '\nspl\a\keySorted';

/**
 * Flattens multidimensional list
 *
 * @param array|\Traversable $sequence
 * @param int|null $depth
 * @return array
 */
function flatten($sequence, $depth = null)
{
    args\expects(args\traversable, $sequence);
    args\expectsOptional(args\int, $depth);

    if (null === $depth) {
        $result = array();

        if ($sequence instanceof \Traversable) {
            $sequence = iterator_to_array($sequence);
        }

        array_walk_recursive($sequence, function($item, $key) use (&$result) {
            if ($item instanceof \Traversable) {
                $result = array_merge($result, flatten(iterator_to_array($item)));
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
    args\expects(args\traversable, $sequence);
    args\expects(args\bool, $valueKey);

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
 * Returns arrays containing $sequence1 items and $sequence2 items
 *
 * @param array|\Traversable $sequence1
 * @param array|\Traversable $sequence2
 * @return array
 */
function merge($sequence1, $sequence2)
{
    args\expects(args\traversable, $sequence1);
    args\expects(args\traversable, $sequence2, 2);

    $result = $sequence1 instanceof \Iterator
        ? iterator_to_array($sequence1)
        : $sequence1;

    foreach ($sequence2 as $key => $item) {
        if (is_string($key)) {
            $result[$key] = $item;
        }
        else {
            $result[] = $item;
        }
    }

    return $result;
}
const merge = '\nspl\a\merge';

/**
 * Moves list item to another position
 *
 * @param array $list
 * @param int $from
 * @param int $to
 * @return array
 */
function reorder(array $list, $from, $to)
{
    args\expects(args\int, $from);
    args\expects(args\int, $to, 3);

    if (!isList($list)) {
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
const reorder = '\nspl\a\reorder';

/**
 * Returns array value by key if it exists otherwise returns the default value
 *
 * @param array|\ArrayAccess $array
 * @param int|string $key
 * @param mixed $default
 * @return mixed
 */
function value($array, $key, $default = null)
{
    args\expects(args\arrayAccess, $array);
    args\expects(args\arrayKey, $key);

    return isset($array[$key]) || array_key_exists($key, $array) ? $array[$key] : $default;
}
const value = '\nspl\a\value';

/**
 * Returns true if the variable is a list
 *
 * @param mixed $var
 * @return bool
 */
function isList($var)
{
    return is_array($var) && array_values($var) === $var;
}
const isList = '\nspl\a\isList';

/**
 * @deprecated
 * @param array|\Traversable $var
 * @return array
 */
function traversableToArray($var)
{
    return $var instanceof \Iterator
        ? iterator_to_array($var)
        : (array) $var;
}

//region deprecated
/**
 * @deprecated
 * @see \nspl\a\merge
 * Returns arrays containing $sequence1 items and $sequence2 items
 *
 * @param array|\Traversable $sequence1
 * @param array|\Traversable $sequence2
 * @return array
 */
function extend($sequence1, $sequence2)
{
    args\expects(args\traversable, $sequence1);
    args\expects(args\traversable, $sequence2, 2);

    return array_merge(traversableToArray($sequence1), traversableToArray(($sequence2)));
}
const extend = '\nspl\a\merge';

/**
 * @deprecated
 * @see \nspl\a\value
 * Returns array value by key if it exists otherwise returns the default value
 *
 * @param array|\ArrayAccess $array
 * @param int|string $key
 * @param mixed $default
 * @return mixed
 */
function getByKey($array, $key, $default = null)
{
    args\expects(args\arrayAccess, $array);
    args\expects(args\arrayKey, $key);

    return isset($array[$key]) || array_key_exists($key, $array) ? $array[$key] : $default;
}
const getByKey = '\nspl\a\value';

/**
 * @deprecated
 * @see \nspl\a\reorder
 * Moves list element to another position
 *
 * @param array $list
 * @param int $from
 * @param int $to
 * @return array
 */
function moveElement(array $list, $from, $to)
{
    args\expects(args\int, $from);
    args\expects(args\int, $to, 3);

    if (!isList($list)) {
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
const moveElement = '\nspl\a\reorder';
//endregion
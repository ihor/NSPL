<?php

namespace nspl\a\lazy;

use nspl\args;

/**
 * Applies function of one argument to each sequence item lazily
 *
 * @param callable $function
 * @param iterable $sequence
 * @return \Generator
 */
function map(callable $function, iterable $sequence)
{
    foreach ($sequence as $key => $item) {
        yield $key => $function($item);
    }
}
const map = '\nspl\a\lazy\map';

/**
 * Lazily applies function of one argument to each sequence item and flattens the result
 *
 * @param callable $function
 * @param iterable $sequence
 * @return \Generator
 */
function flatMap(callable $function, iterable $sequence)
{
    foreach ($sequence as $item) {
        foreach ($function($item) as $resultValue) {
            yield $resultValue;
        }
    }
}
const flatMap = '\nspl\a\lazy\flatMap';

/**
 * Zips two or more sequences lazily
 *
 * @param iterable $sequence1
 * @param iterable $sequence2
 * @return \Generator
 */
function zip(iterable $sequence1, iterable $sequence2)
{
    $sequences = func_get_args();
    $count = func_num_args();

    for ($j = 0; $j < $count; ++$j) {
        args\expects(args\iterable_, $sequences[$j], $j + 1);
        if (is_array($sequences[$j])) {
            $sequences[$j] = new \ArrayIterator($sequences[$j]);
        }
    }

    do {
        $zipped = [];
        for ($j = 0; $j < $count; ++$j) {
            $data = $sequences[$j]->current();
            if (null === $data) {
                break 2;
            }

            $sequences[$j]->next();

            $zipped[] = $data;
        }
        yield $zipped;
    } while (true);
}
const zip = '\nspl\a\lazy\zip';

/**
 * Generalises zip by zipping with the function given as the first argument, instead of an array-creating function
 *
 * @param callable $function
 * @param iterable $sequence1
 * @param iterable $sequence2
 * @return \Generator
 */
function zipWith(callable $function, iterable $sequence1, iterable $sequence2)
{
    $sequences = func_get_args();
    array_shift($sequences);
    $count = count($sequences);

    for ($j = 0; $j < $count; ++$j) {
        args\expects(args\iterable_, $sequences[$j], $j + 1);
        if (is_array($sequences[$j])) {
            $sequences[$j] = new \ArrayIterator($sequences[$j]);
        }
    }

    do {
        $zipped = [];
        for ($j = 0; $j < $count; ++$j) {
            $data = $sequences[$j]->current();
            if (null === $data) {
                break 2;
            }

            $sequences[$j]->next();

            $zipped[] = $data;
        }
        yield $count === 2
            ? $function($zipped[0], $zipped[1])
            : call_user_func_array($function, $zipped);;
    } while (true);
}
const zipWith = '\nspl\a\lazy\zipWith';

/**
 * Lazily returns sequence items that satisfy the predicate
 *
 * @param callable $predicate
 * @param iterable $sequence
 * @return \Generator
 */
function filter(callable $predicate, iterable $sequence)
{
    foreach ($sequence as $key => $item) {
        if ($predicate($item)) {
            yield $key => $item;
        }
    }
}
const filter = '\nspl\a\lazy\filter';

/**
 * Lazily returns sequence items that don't satisfy the predicate
 *
 * @param callable $predicate
 * @param iterable $sequence
 * @return \Generator
 */
function filterNot(callable $predicate, iterable $sequence)
{
    foreach ($sequence as $key => $item) {
        if (!$predicate($item)) {
            yield $key => $item;
        }
    }
}
const filterNot = '\nspl\a\lazy\filterNot';

/**
 * Returns first N sequence items with given step
 *
 * @param iterable $sequence
 * @param int $N
 * @param int $step
 * @return \Generator
 */
function take(iterable $sequence, $N, $step = 1)
{
    args\expects(args\int, $N);
    args\expects(args\int, $step, 3);

    if ($N === 0) {
        return;
    }

    $counter = 0;
    $taken = 0;
    foreach ($sequence as $item) {
        if ($counter++ % $step === 0) {
            yield $item;
            ++$taken;
        }

        if ($taken >= $N) {
            break;
        }
    }
}
const take = '\nspl\a\lazy\take';

/**
 * Returns the longest sequence prefix of all items which satisfy the predicate
 *
 * @param callable $predicate
 * @param iterable $sequence
 * @return \Generator
 */
function takeWhile(callable $predicate, iterable $sequence)
{
    foreach ($sequence as $item) {
        if ($predicate($item)) {
            yield $item;
        }
        else {
            break;
        }
    }
}
const takeWhile = '\nspl\a\lazy\takeWhile';

/**
 * Drops first N sequence items
 *
 * @param iterable $sequence
 * @param int $N
 * @return \Generator
 */
function drop(iterable $sequence, $N)
{
    args\expects(args\int, $N);

    $counter = 0;
    foreach ($sequence as $item) {
        if ($counter++ < $N) {
            continue;
        }

        yield $item;
    }
}
const drop = '\nspl\a\lazy\drop';

/**
 * Drops the longest sequence prefix of all items which satisfy the predicate
 *
 * @param callable $predicate
 * @param iterable $sequence
 * @return \Generator
 */
function dropWhile(callable $predicate, iterable $sequence)
{
    $drop = true;
    foreach ($sequence as $item) {
        if ($drop) {
            if (!$predicate($item)) {
                $drop = false;
                yield $item;
            }
        }
        else {
            yield $item;
        }
    }
}
const dropWhile = '\nspl\a\lazy\dropWhile';

/**
 * Returns two generators, one yielding values for which the predicate returned true, and the other one
 * the items that returned false
 *
 * @param callable $predicate
 * @param iterable $sequence
 * @return \Generator[]
 */
function partition(callable $predicate, iterable $sequence)
{
    $checked = array();

    $first = function() use ($sequence, $predicate, &$checked) {
        foreach ($sequence as $k => $v) {
            if (!isset($checked[$k])) {
                $checked[$k] = $predicate($v);
            }

            if ($checked[$k]) {
                yield $k => $v;
            }
        }
    };

    $second = function() use ($sequence, $predicate, &$checked) {
        foreach ($sequence as $k => $v) {
            if (!isset($checked[$k])) {
                $checked[$k] = $predicate($v);
            }

            if (!$checked[$k]) {
                yield $k => $v;
            }
        }
    };

    return [$first(), $second()];
}
const partition = '\nspl\a\lazy\partition';

/**
 * Flattens multidimensional sequence
 *
 * @param iterable $sequence
 * @param int|null $depth
 * @return \Generator
 */
function flatten(iterable $sequence, $depth = null)
{
    args\expectsOptional(args\int, $depth);

    foreach ($sequence as $value) {
        if (null == $depth && (is_array($value) || $value instanceof \Traversable)) {
            foreach (flatten($value) as $subValue) {
                yield $subValue;
            }
        }
        else if ($depth && (is_array($value) || $value instanceof \Traversable)) {
            foreach ($depth > 1 ? flatten($value, $depth - 1) : $value as $subValue) {
                yield $subValue;
            }
        }
        else {
            yield $value;
        }
    }
}
const flatten = '\nspl\a\lazy\flatten';

/**
 * Returns list of (key, value) pairs
 * @param iterable $sequence
 * @param bool $valueKey If true then convert array to (value, key) pairs
 * @return \Generator
 */
function pairs(iterable $sequence, $valueKey = false)
{
    args\expects(args\bool, $valueKey);

    foreach ($sequence as $key => $value) {
        yield ($valueKey ? [$value, $key] : [$key, $value]);
    }
}
const pairs = '\nspl\a\lazy\pairs';

/**
 * Returns list of the sequence keys
 * @param iterable $sequence
 * @return \Generator
 */
function keys(iterable $sequence)
{
    foreach ($sequence as $key => $_) {
        yield $key;
    }
}
const keys = '\nspl\a\lazy\keys';

/**
 * Creates a lazy chainable sequence
 *
 * @param array|\Iterator|\IteratorAggregate $sequence
 * @return LazyChainableSequence
 */
function with(iterable $sequence)
{
    return new LazyChainableSequence($sequence);
}

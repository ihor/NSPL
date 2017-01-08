<?php

namespace nspl\a\lazy;

use nspl\args;

/**
 * Applies function of one argument to each sequence item lazily
 *
 * @param callable $function
 * @param array|\Traversable $sequence
 * @return \Generator
 */
function map(callable $function, $sequence)
{
    args\expects(args\traversable, $sequence);

    foreach ($sequence as $key => $item) {
        yield $key => $function($item);
    }
}
const map = '\nspl\a\lazy\map';

/**
 * Lazily applies function of one argument to each sequence item and flattens the result
 *
 * @param callable $function
 * @param array|\Traversable $sequence
 * @return \Generator
 */
function flatMap(callable $function, $sequence)
{
    args\expects(args\traversable, $sequence);

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
 * @param array|\Traversable $sequence1
 * @param array|\Traversable $sequence2
 * @return \Generator
 */
function zip($sequence1, $sequence2)
{
    $sequences = func_get_args();
    $count = func_num_args();

    for ($j = 0; $j < $count; ++$j) {
        args\expects(args\traversable, $sequences[$j], $j + 1);
    }

    do {
        $zipped = [];
        for ($j = 0; $j < $count; ++$j) {
            if (!$data = each($sequences[$j])) {
                return;
            }
            $zipped[] = $data['value'];
        }
        yield $zipped;
    } while (true);
}
const zip = '\nspl\a\lazy\zip';

/**
 * Generalises zip by zipping with the function given as the first argument, instead of an array-creating function
 *
 * @param callable $function
 * @param array|\Traversable $sequence1
 * @param array|\Traversable $sequence2
 * @return \Generator
 */
function zipWith(callable $function, $sequence1, $sequence2)
{
    $sequences = func_get_args();
    array_shift($sequences);
    $count = count($sequences);

    for ($j = 0; $j < $count; ++$j) {
        args\expects(args\traversable, $sequences[$j], $j + 1);
    }

    do {
        $zipped = [];
        for ($j = 0; $j < $count; ++$j) {
            if (!$data = each($sequences[$j])) {
                return;
            }
            $zipped[] = $data['value'];
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
 * @param array|\Traversable $sequence
 * @return \Generator
 */
function filter(callable $predicate, $sequence)
{
    args\expects(args\traversable, $sequence);

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
 * @param array|\Traversable $sequence
 * @return \Generator
 */
function filterNot(callable $predicate, $sequence)
{
    args\expects(args\traversable, $sequence);

    foreach ($sequence as $key => $item) {
        if (!$predicate($item)) {
            yield $key => $item;
        }
    }
}
const filterNot = '\nspl\a\lazy\filterNot';
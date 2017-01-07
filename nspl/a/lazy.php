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
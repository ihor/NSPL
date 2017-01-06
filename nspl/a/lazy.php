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

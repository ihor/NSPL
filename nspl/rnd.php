<?php

namespace nspl\rnd;

use \nspl\a;
use \nspl\args;

/**
 * Returns a k length list of unique items chosen from the population sequence
 *
 * @param array|\Traversable $population
 * @param int $length
 * @param bool $preserveKeys
 * @return array
 */
function sample($population, $length, $preserveKeys = false)
{
    args\expects(args\traversable, $population);
    args\expects(args\int, $length);
    args\expects(args\bool, $preserveKeys);

    if (!$length) {
        return array();
    }

    if ($population instanceof \Iterator) {
        $population = iterator_to_array($population);
    }

    if ($length > count($population)) {
        throw new \InvalidArgumentException('Sample is larger than population');
    }

    $keys = (array) array_rand($population, $length);
    $result = array();
    foreach ($keys as $key) {
        $result[$key] = $population[$key];
    }

    return $preserveKeys ? $result : array_values($result);
}

/**
 * Returns a random item from a non-empty sequence
 *
 * @param array|\Traversable $sequence
 * @return mixed
 */
function choice($sequence)
{
    if (!$sequence) {
        throw new \InvalidArgumentException('Sequence is empty');
    }

    args\expects(args\traversable, $sequence);

    if ($sequence instanceof \Iterator) {
        $sequence = iterator_to_array($sequence);
    }

    return $sequence[array_rand($sequence)];
}

/**
 * Returns a random item from a non-empty sequence of items with associated weights.
 * Weights can have up to 6 numbers after decimal point.
 *
 * @param array $weightPairs List of pairs [[item, weight], ...]
 * @return mixed
 */
function weightedChoice(array $weightPairs)
{
    if (!$weightPairs) {
        throw new \InvalidArgumentException('Weight pairs are empty');
    }

    $multiplier = 1000000;

    $total = array_reduce($weightPairs, function($sum, $v) { return $sum + $v[1]; });
    $r = mt_rand(1, $total * $multiplier);

    $acc = 0;
    foreach ($weightPairs as $pair) {
        $acc += $pair[1] * $multiplier;
        if ($acc >= $r) {
            return $pair[0];
        }
    }

    return $pair[0];
}

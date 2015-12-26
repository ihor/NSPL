<?php

namespace nspl\rnd;

use function \nspl\ds\isList;

/**
 * Returns a k length list of unique elements chosen from the population sequence
 *
 * @param array $population
 * @param int $length
 * @param bool $preserveKeys
 * @return array
 */
function sample(array $population, $length, $preserveKeys = false)
{
    if (!$length) {
        return array();
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
 * Returns a random element from a non-empty sequence
 *
 * @param array $sequence
 * @return mixed
 */
function choice(array $sequence)
{
    if (!$sequence) {
        throw new \InvalidArgumentException('Sequence is empty');
    }

    return $sequence[array_rand($sequence)];
}

/**
 * Returns a random element from a non-empty sequence of items with associated weights.
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

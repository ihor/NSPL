<?php

namespace nspl\rnd;

use \nspl\args;

const ALPHA_NUM = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

/**
 * Returns random string of the given length
 *
 * @param $length
 * @param string $source Alpha-numeric by default
 * @return bool|string
 */
function randomString($length, $source = ALPHA_NUM)
{
    return substr(str_shuffle(str_repeat($source, ceil($length / strlen($source)) )), 0, $length);
}

/**
 * Returns a k length list of unique items chosen from the population sequence
 *
 * @param iterable $population
 * @param int $length
 * @param bool $preserveKeys
 * @return array
 */
function sample(iterable $population, $length, $preserveKeys = false)
{
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
 * @param iterable $sequence
 * @return mixed
 */
function choice(iterable $sequence)
{
    if (!$sequence) {
        throw new \InvalidArgumentException('Sequence is empty');
    }

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

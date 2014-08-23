<?php

namespace rnd;

/**
 * @param array $array
 * @param int $length
 * @return array
 */
function sample(array $array, $length)
{
    if (!$array || !$length) {
        return array();
    }

    $keys = (array) array_rand($array, $length);
    $result = array();
    foreach ($keys as $key) {
        $result[$key] = $array[$key];
    }

    return $result;
}

/**
 * @param array $array
 * @return mixed
 */
function choice(array $array)
{
    return current(sample($array, 1));
}

/**
 * @param array $weights
 * @param int $length
 * @return mixed
 */
function weightedSample(array $weights, $length)
{
    if (!$weights || !$length) {
        return array();
    }

    $result = array();

    $count = 0;
    $total = array_sum($weights);
    while ($length >= $count) {
        $r = mt_rand(1, $total);

        reset($weights);
        $acc = current($weights);
        while ($acc < $r && next($weights)) {
            $acc += current($weights);
        }

        $result[] = key($weights);
        ++$count;
    }

    return $result;
}

/**
 * @param array $array
 * @return mixed
 */
function weightedChoice(array $array)
{
    return current(weightedSample($array, 1));
}

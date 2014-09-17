<?php

namespace nspl\a;

use nspl\f;
use nspl\ds;
use nspl\op;

/**
 * Adds $list2 values to the end of $list1
 *
 * @param array $list1
 * @param array $list2
 * @return array
 */
function extend(array $list1, array $list2)
{
    do {
        $list1[] = current($list2);
    } while (next($list2));

    return $list1;
}

/**
 * Zips passed lists
 *
 * @param array $list1
 * @param array $list2
 * @return array
 */
function zip(array $list1, array $list2)
{
    $arrays = func_get_args();
    $arraysNum = count($arrays);

    $result = array();
    $finished = false;
    do {
        $zipped = array();
        for ($i = 0; $i < $arraysNum; ++$i) {
            $zipped[] = current($arrays[$i]);
            if (!next($arrays[$i])) {
                $finished = true;
            }
        }
        $result[] = $zipped;
    } while (!$finished);

    return $result;
}

/**
 * Flattens multidimensional list
 *
 * @param array $multidimensionalList
 * @return array
 */
function flatten(array $multidimensionalList)
{
    return call_user_func_array('array_merge', array_map('array_values', $multidimensionalList));
}

/**
 * Returns sorted copy of passed sequence
 *
 * @param array $sequence
 * @param bool $reversed
 * @param callable $cmp Custom comparison function of two arguments which should return a negative, zero or positive number depending on whether the first argument is considered smaller than, equal to, or larger than the second argument
 * @param callable $key Function of one argument that is used to extract a comparison key from each element
 * @return array
 */
function sorted($sequence, $reversed = false, $cmp = null, $key = null)
{
    if (!$cmp) {
        $cmp = function ($a, $b) { return $a > $b ? 1 : -1; };
    }

    if ($key) {
        $cmp = function($a, $b) use ($key, $cmp) {
            return call_user_func_array($cmp, array($key($a), $key($b)));
        };
    }

    if ($reversed) {
        $cmp = f\compose(op::$neg, $cmp);
    }

    $array = (array) $sequence;
    uasort($array, $cmp);

    return $array;
}

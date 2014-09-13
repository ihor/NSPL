<?php

namespace nspl\a;

use nspl\f;

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

function flatten(array $multidimensionalList)
{
    // @todo
}

//region Aliases
/**
 * @param array $list
 * @return array
 */
function reversed($list)
{
    return array_reverse($list);
}
//endregion
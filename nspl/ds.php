<?php

namespace nspl\ds;

/**
 * @param mixed $notArray
 * @return array
 */
function toArray($notArray)
{
    if ($notArray instanceof \Iterator) {
        return iterator_to_array($notArray);
    }

    return (array) $notArray;
}

/**
 * @param mixed $var
 * @return string
 */
function getType($var)
{
    return is_object($var) ? get_class($var) : gettype($var);
}

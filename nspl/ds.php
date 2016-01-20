<?php

namespace nspl\ds;

use \nspl\args;

/**
 * Returns the variable type or its class name if it is an object
 *
 * @param mixed $var
 * @return string
 */
function getType($var)
{
    return is_object($var) ? get_class($var) : \gettype($var);
}

/**
 * Returns true if the variable is a list
 *
 * @param mixed $var
 * @return bool
 */
function isList($var)
{
    return is_array($var) && array_values($var) === $var;
}

/**
 * @param array|\Traversable $var
 * @return array
 */
function traversableToArray($var)
{
    args\expectsTraversable($var);
    return $var instanceof \Iterator
        ? iterator_to_array($var)
        : (array) $var;
}

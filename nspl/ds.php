<?php

namespace nspl\ds;

/**
 * Returns variable type or its class name if it is an object
 *
 * @param mixed $var
 * @return string
 */
function getType($var)
{
    return is_object($var) ? get_class($var) : \gettype($var);
}

/**
 * @param mixed $var
 * @return bool
 */
function isList($var)
{
    return is_array($var) && array_values($var) === $var;
}

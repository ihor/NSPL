<?php

namespace nspl\ds;

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

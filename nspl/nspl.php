<?php

namespace nspl;

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
const getType = '\nspl\getType';

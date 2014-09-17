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
    return is_object($var) ? get_class($var) : gettype($var);
}

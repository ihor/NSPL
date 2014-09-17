<?php

namespace nspl\ds;

/**
 * @param mixed $var
 * @return string
 */
function getType($var)
{
    return is_object($var) ? get_class($var) : gettype($var);
}

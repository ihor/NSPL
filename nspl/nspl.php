<?php

namespace nspl;

/**
 * Returns true if all elements of the $sequence are true (or if the $sequence is empty)
 *
 * @param array $sequence
 * @return bool
 */
function all(array $sequence)
{
    foreach ($sequence as $value) {
        if (!$value) {
            return false;
        }
    }

    return true;
}

/**
 * Returns true if any element of the $sequence is true. If the $sequence is empty, returns false.
 *
 * @param array $sequence
 * @return bool
 */
function any(array $sequence)
{
    foreach ($sequence as $value) {
        if ($value) {
            return true;
        }
    }

    return false;
}

<?php

namespace nspl\f;

use nspl\a;
use nspl\ds;

/**
 * @param callable $function
 * @param array|\Iterator $sequence
 * @return array
 */
function map($function, $sequence)
{
    return array_map($function, ds\toArray($sequence));
}

/**
 * @param callable $function
 * @param array|\Iterator $sequence
 * @param mixed $initial
 * @return array
 */
function reduce($function, $sequence, $initial = 0)
{
    return array_reduce(ds\toArray($sequence), $function, $initial);
}

/**
 * @param callable $function
 * @param array|\Iterator $sequence
 * @return array
 */
function filter($function, $sequence)
{
    return array_filter(ds\toArray($sequence), $function);
}

/**
 * @param callable $function
 * @param array $args
 * @return mixed
 */
function apply($function, array $args = array())
{
    return call_user_func_array($function, $args);
}

/**
 * @param callable $function
 * @param mixed $param1
 * @param mixed $param2
 * @param mixed ...
 * @return callable
 */
function partial($function)
{
    $args = array_slice(func_get_args(), 1);
    return function() use ($function, $args) {
        return apply($function, a\extend(func_get_args(), $args));
    };
}

/**
 * @param callable $function
 * @param mixed $param1
 * @param mixed $param2
 * @param mixed ...
 * @return callable
 */
function lpartial($function)
{
    $args = array_slice(func_get_args(), 1);
    return function() use ($function, $args) {
        return apply($function, a\extend($args, func_get_args()));
    };
}

function ppartial($function)
{
    // @todo
}

/**
 * Returns memoized version of given function
 * @param callable $function
 * @return callable
 */
function memoize($function)
{
    return function() use ($function) {
        static $memory = array();
        $args = func_get_args();
        $key = serialize($args);
        if (!isset($memory[$key]) && !array_key_exists($key, $memory)) {
            $memory[$key] = call_user_func_array($function, $args);
        }

        return $memory[$key];
    };
}

/**
 * @param mixed $args
 * @param callable[] $functions
 * @return mixed
 */
function pipe($args, $functions)
{
    $functionsCount = count($functions);
    for ($i = 0; $i < $functionsCount; ++$i) {
        $args = array(apply($functions[$i], $args));
    }

    return current($args);
}


namespace nspl;

class f
{
    static public $map;
    static public $reduce;
    static public $filter;
    static public $apply;
    static public $partial;
    static public $lpartial;
    static public $rpartial;
    static public $memoize;

}

f::$map = function($function, $sequence) { return f\map($function, $sequence); };
f::$reduce = function($function, $sequence, $initial = 0) { return f\reduce($function, $sequence, $initial); };
f::$filter = function($function, $sequence) { return f\filter($function, $sequence); };
f::$apply = function($function, array $args = array()) { return f\apply($function, $args); };
f::$partial = function($function) { return f\apply('f\partial', array_slice(func_get_args(), 1)); };
f::$lpartial = function($function) { return f\apply('f\lpartial', array_slice(func_get_args(), 1)); };
f::$rpartial = function($function) { return f\apply('f\ppartial', array_slice(func_get_args(), 1)); };
f::$memoize = function($function) { return f\memoize($function); };

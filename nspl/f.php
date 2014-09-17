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
 * Applies a function to arguments and returns the result
 *
 * @param callable $function
 * @param array $args
 * @return mixed
 */
function apply($function, $args = array())
{
    return call_user_func_array($function, $args);
}

/**
 * Returns composition of the last function in arguments list with functions that take one argument
 * compose(f, g, h) is the same as f(g(h(x)))
 *
 * @param callable $f
 * @param callable $g
 * @return callable
 */
function compose($f, $g)
{
    $functions = func_get_args();
    return function() use ($functions) {
        $args = func_get_args();
        foreach (array_reverse($functions) as $function) {
            $args = array(call_user_func_array($function, $args));
        }

        return current($args);
    };
}

/**
 * Returns new partial function which will behave like $function with
 * predefined left arguments passed to partial
 *
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
        return call_user_func_array($function, a\extend($args, func_get_args()));
    };
}

/**
 * Returns new partial function which will behave like $function with
 * predefined right arguments passed to rpartial
 *
 * @param callable $function
 * @param mixed $param1
 * @param mixed $param2
 * @param mixed ...
 * @return callable
 */
function rpartial($function)
{
    $args = array_slice(func_get_args(), 1);
    return function() use ($function, $args) {
        return call_user_func_array($function, a\extend(func_get_args(), $args));
    };
}

/**
 * Returns new partial function which will behave like $function with
 * predefined positional arguments passed to ppartial
 *
 * @param callable $function
 * @param array $args Predefined positional args (position => value)
 * @return callable
 */
function ppartial($function, array $args)
{
    return function() use ($function, $args) {
        $_args = func_get_args();
        $position = 0;
        do {
            if (!isset($args[$position]) && !array_key_exists($position, $args)) {
                $args[$position] = array_pop($_args);
            }
            ++$position;
        } while($_args);
        ksort($args);
        return call_user_func_array($function, $args);
    };
}

/**
 * Returns you a curried version of function
 * If you are going to curry a function which read args with func_get_args() then pass number of args as the 2nd argument.
 *
 * @see partial()
 * 
 * @param $function
 * @param bool $withOptionalArgs If true then curry function with optional args otherwise curry it only with required args. Or you can pass the exact number of args you want to curry.
 * @return callable
 */
function curried($function, $withOptionalArgs = false)
{
    if (is_bool($withOptionalArgs)) {
        $reflection = new \ReflectionFunction($function);
        $numOfArgs = $withOptionalArgs
            ? $reflection->getNumberOfParameters()
            : $reflection->getNumberOfRequiredParameters();
    }
    else {
        $numOfArgs = $withOptionalArgs;
    }

    return function($arg) use ($function, $numOfArgs) {
        if (1 === $numOfArgs) {
            return call_user_func_array($function, array($arg));
        }

        return curried(function() use ($arg, $function) {
            return call_user_func_array($function, array_merge(array($arg), func_get_args()));
        }, $numOfArgs - 1);
    };
}

/**
 * Returns uncurried version of curried function
 *
 * @param callable $function Curried function
 * @return callable
 */
function uncurried($function)
{
    return function() use ($function) {
        foreach (func_get_args() as $arg) {
            $function = call_user_func($function, $arg);
        }

        return $function;
    };
}

/**
 * Returns memoized $function which returns the cached result when the same inputs occur again
 *
 * @param callable $function
 * @return callable
 */
function memoized($function)
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
 * Passes args to composition of functions (functions have to be in the reversed order)
 *
 * @param mixed $args
 * @param callable[] $functions
 * @return mixed
 */
function pipe($args, array $functions)
{
    if (!is_array($args)) {
        $args = (array) $args;
    }

    return call_user_func_array(
        call_user_func_array(
            \nspl\f::$compose,
            array_reverse($functions)
        ),
        $args
    );
}

/**
 * Alias for @see pipe()
 *
 * @param mixed $args
 * @param callable[] $functions
 * @return mixed
 */
function I($args, array $functions)
{
    return pipe($args, $functions);
}


namespace nspl;

class f
{
    static public $map;
    static public $reduce;
    static public $filter;
    static public $apply;
    static public $partial;
    static public $rpartial;
    static public $ppartial;
    static public $memoize;
    static public $compose;
    static public $pipe;
    static public $curried;
    static public $uncurried;

}

f::$map = function($function, $sequence) { return f\map($function, $sequence); };
f::$reduce = function($function, $sequence, $initial = 0) { return f\reduce($function, $sequence, $initial); };
f::$filter = function($function, $sequence) { return f\filter($function, $sequence); };
f::$apply = function($function, array $args = array()) { return call_user_func_array($function, $args); };
f::$partial = function($function) { return call_user_func_array('\nspl\f\partial', array_slice(func_get_args(), 1)); };
f::$rpartial = function($function) { return call_user_func_array('\nspl\f\lpartial', array_slice(func_get_args(), 1)); };
f::$ppartial = function($function) { return call_user_func_array('\nspl\f\ppartial', array_slice(func_get_args(), 1)); };
f::$memoize = function($function) { return f\memoized($function); };
f::$compose = function($f, $g) { return call_user_func_array('\nspl\f\compose', func_get_args()); };
f::$pipe = function($args, array $functions) { return f\pipe($args, $functions); };
f::$curried = function($function, $withOptionalArgs = false) { return f\curried($function, $withOptionalArgs); };
f::$uncurried = function($function) { return f\uncurried($function); };

<?php

namespace nspl\f;

use nspl\a;
use nspl\ds;

/**
 * Applies function of one argument to each sequence item
 *
 * @param callable $function
 * @param array|\Traversable $sequence
 * @return array
 */
function map($function, $sequence)
{
    return array_map($function, (array) $sequence);
}

/**
 * Applies function of two arguments cumulatively to the items of sequence, from left to right to reduce the sequence
 * to a single value.
 *
 * @param callable $function
 * @param array|\Traversable $sequence
 * @param mixed $initial
 * @return array
 */
function reduce($function, $sequence, $initial = 0)
{
    return array_reduce((array) $sequence, $function, $initial);
}

/**
 * Returns sequence items that satisfy the predicate
 *
 * @param callable $predicate
 * @param array|\Traversable $sequence
 * @return array
 */
function filter($predicate, $sequence)
{
    $isList = ds\isList($sequence);
    $filtered = array_filter((array) $sequence, $predicate);
    return $isList ? array_values($filtered) : $filtered;
}

/**
 * Applies function to arguments and returns the result
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
 * Returns new function which will behave like $function with
 * predefined left arguments passed to partial
 *
 * @param callable $function
 * @param mixed $arg1
 * @param mixed $arg2
 * @param mixed ...
 * @return callable
 */
function partial($function, $arg1)
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
 * @param mixed $arg1
 * @param mixed $arg2
 * @param mixed ...
 * @return callable
 */
function rpartial($function, $arg1)
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
 * Returns new function which applies each given function to the result of another from right to left
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
 * Passes args to composition of functions (functions have to be in the reversed order)
 *
 * @param mixed $input
 * @param callable $function1
 * @param callable $function2
 * @return mixed
 */
function pipe($input, $function1, $function2)
{
    $functions = func_get_args();
    unset($functions[0]);

    return call_user_func(
        call_user_func_array(\nspl\f::$compose, array_reverse($functions)),
        $input
    );
}

/**
 * Alias for @see pipe()
 *
 * @param mixed $input
 * @param callable $function1
 * @param callable $function2
 * @return mixed
 */
function I($input, $function1, $function2)
{
    return call_user_func_array('\nspl\f\pipe', func_get_args());
}

/**
 * Returns you a curried version of the function
 * If you are going to curry a function which reads args with func_get_args() then pass a number of args as the 2nd argument.
 *
 * @param callable $function
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
    static public $memoized;
    static public $compose;
    static public $pipe;
    static public $curried;
    static public $uncurried;

}

f::$map = function($function, $sequence) { return f\map($function, $sequence); };
f::$reduce = function($function, $sequence, $initial = 0) { return f\reduce($function, $sequence, $initial); };
f::$filter = function($function, $sequence) { return f\filter($function, $sequence); };
f::$apply = function($function, array $args = array()) { return f\apply($function, $args); };
f::$partial = function($function) { return call_user_func_array('\nspl\f\partial', func_get_args()); };
f::$rpartial = function($function) { return call_user_func_array('\nspl\f\rpartial', func_get_args()); };
f::$ppartial = function($function) { return call_user_func_array('\nspl\f\ppartial', func_get_args()); };
f::$memoized = function($function) { return f\memoized($function); };
f::$compose = function($f, $g) { return call_user_func_array('\nspl\f\compose', func_get_args()); };
f::$pipe = function($input, $function1, $function2) { return call_user_func_array('\nspl\f\pipe', func_get_args()); };
f::$curried = function($function, $withOptionalArgs = false) { return f\curried($function, $withOptionalArgs); };
f::$uncurried = function($function) { return f\uncurried($function); };

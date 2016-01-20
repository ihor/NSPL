<?php

namespace nspl\f;

use nspl\a;
use nspl\ds;
use nspl\args;

/**
 * Applies function of one argument to each sequence item
 *
 * @param callable $function
 * @param array|\Traversable $sequence
 * @return array
 */
function map(callable $function, $sequence)
{
    args\expectsTraversable($sequence);
    return array_map($function, ds\traversableToArray($sequence));
}
const map = '\nspl\f\map';

/**
 * Applies function of two arguments cumulatively to the items of sequence, from left to right to reduce the sequence
 * to a single value.
 *
 * @param callable $function
 * @param array|\Traversable $sequence
 * @param mixed $initial
 * @return array
 */
function reduce(callable $function, $sequence, $initial = 0)
{
    args\expectsTraversable($sequence);
    return array_reduce(ds\traversableToArray($sequence), $function, $initial);
}
const reduce = '\nspl\f\reduce';

/**
 * Returns sequence items that satisfy the predicate
 *
 * @param callable $predicate
 * @param array|\Traversable $sequence
 * @return array
 */
function filter(callable $predicate, $sequence)
{
    args\expectsTraversable($sequence);

    $sequence = ds\traversableToArray($sequence);
    $isList = ds\isList($sequence);
    $filtered = array_filter($sequence, $predicate);
    return $isList ? array_values($filtered) : $filtered;
}
const filter = '\nspl\f\filter';

/**
 * Applies function to arguments and returns the result
 *
 * @param callable $function
 * @param array $args
 * @return mixed
 */
function apply(callable $function, array $args = array())
{
    return call_user_func_array($function, $args);
}
const apply = '\nspl\f\apply';

/**
 * Returns function which accepts arguments in the reversed order
 *
 * @param callable $function
 * @return callable
 */
function flipped(callable $function) {
    return function() use ($function) {
        return call_user_func_array($function, array_reverse(func_get_args()));
    };
}
const flipped = '\nspl\f\flipped';

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
function partial(callable $function, $arg1)
{
    $args = array_slice(func_get_args(), 1);
    return function() use ($function, $args) {
        return call_user_func_array($function, a\extend($args, func_get_args()));
    };
}
const partial = '\nspl\f\partial';

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
function rpartial(callable $function, $arg1)
{
    $args = array_slice(func_get_args(), 1);
    return function() use ($function, $args) {
        return call_user_func_array($function, a\extend(func_get_args(), $args));
    };
}
const rpartial = '\nspl\f\rpartial';

/**
 * Returns new partial function which will behave like $function with
 * predefined positional arguments passed to ppartial
 *
 * @param callable $function
 * @param array $args Predefined positional args (position => value)
 * @return callable
 */
function ppartial(callable $function, array $args)
{
    return function() use ($function, $args) {
        $_args = func_get_args();
        $position = 0;
        do {
            if (!isset($args[$position]) && !array_key_exists($position, $args)) {
                $args[$position] = array_shift($_args);
            }
            ++$position;
        } while($_args);
        ksort($args);
        return call_user_func_array($function, $args);
    };
}
const ppartial = '\nspl\f\ppartial';

/**
 * Returns memoized $function which returns the cached result when the same inputs occur again
 *
 * @param callable $function
 * @return callable
 */
function memoized(callable $function)
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
const memoized = '\nspl\f\memoized';

/**
 * Returns new function which applies each given function to the result of another from right to left
 * compose(f, g, h) is the same as f(g(h(x)))
 *
 * @param callable $f
 * @param callable $g
 * @return callable
 */
function compose(callable $f, callable $g)
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
const compose = '\nspl\f\compose';

/**
 * Passes args to composition of functions (functions have to be in the reversed order)
 *
 * @param mixed $input
 * @param callable $function1
 * @param callable $function2
 * @return mixed
 */
function pipe($input, callable $function1, callable $function2)
{
    $functions = func_get_args();
    unset($functions[0]);

    return call_user_func(
        call_user_func_array(compose, array_reverse($functions)),
        $input
    );
}
const pipe = '\nspl\f\pipe';

/**
 * Alias for @see pipe()
 *
 * @param mixed $input
 * @param callable $function1
 * @param callable $function2
 * @return mixed
 */
function I($input, callable $function1, callable $function2)
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
function curried(callable $function, $withOptionalArgs = false)
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
const curried = '\nspl\f\curried';

/**
 * Returns uncurried version of curried function
 *
 * @param callable $function Curried function
 * @return callable
 */
function uncurried(callable $function)
{
    return function() use ($function) {
        foreach (func_get_args() as $arg) {
            $function = call_user_func($function, $arg);
        }

        return $function;
    };
}
const uncurried = '\nspl\f\uncurried';

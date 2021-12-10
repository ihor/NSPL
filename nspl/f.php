<?php

namespace nspl\f;

use nspl\a;
use nspl\args;

/**
 * Identity function. Returns passed value.
 *
 * @param mixed $value
 * @return mixed
 */
function id($value)
{
    return $value;
}
const id = '\nspl\f\id';

/**
 * Applies function to arguments and returns the result
 *
 * @param callable $function
 * @param array $args
 * @return mixed
 */
function apply(callable $function, array $args = array())
{
    switch (count($args)) {
        case 0: return $function();
        case 1: return $function($args[0]);
        case 2: return $function($args[0], $args[1]);
        case 3: return $function($args[0], $args[1], $args[2]);
        default: return call_user_func_array($function, $args);
    }
}
const apply = '\nspl\f\apply';

/**
 * Returns new function which will behave like $function with
 * predefined left arguments passed to partial
 *
 * @param callable $function
 * @param mixed[] ...$args
 * @return callable
 */
function partial(callable $function, ...$args)
{
    return function(...$extraArgs) use ($function, $args) {
        return call_user_func_array($function, array_merge($args, $extraArgs));
    };
}
const partial = '\nspl\f\partial';

/**
 * Returns new partial function which will behave like $function with
 * predefined right arguments passed to rpartial
 *
 * @param callable $function
 * @param mixed[] ...$args
 * @return callable
 */
function rpartial(callable $function, ...$args)
{
    return function(...$extraArgs) use ($function, $args) {
        return call_user_func_array($function, array_merge($extraArgs, $args));
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
    return function(...$extraArgs) use ($function, $args) {
        $position = 0;
        while ($extraArgs) {
            if (!isset($args[$position]) && !array_key_exists($position, $args)) {
                $args[$position] = array_shift($extraArgs);
            }
            ++$position;
        };
        ksort($args);
        return call_user_func_array($function, $args);
    };
}
const ppartial = '\nspl\f\ppartial';

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
            return $function($arg);
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
            $function = $function($arg);
        }

        return $function;
    };
}
const uncurried = '\nspl\f\uncurried';

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
 * Returns throttled version of the passed function, that, when invoked repeatedly, will only
 * actually call the original function at most once per every wait milliseconds.
 *
 * @param callable $function
 * @param int $wait
 * @return callable
 */
function throttled(callable $function, $wait)
{
    return function () use ($function, $wait) {
        $args = func_get_args();
        static $invokedAt = 0;
        $now = microtime(true);
        if ($now - $invokedAt >= $wait / 1000) {
            $invokedAt = $now;
            call_user_func_array($function, $args);
        }
    };
}
const throttled = '\nspl\f\throttled';

//region deprecated
/**
 * @deprecated
 * @see \nspl\a\map
 *
 * Applies function of one argument to each sequence item
 *
 * @param callable $function
 * @param array|\Traversable $sequence
 * @return array
 */
function map(callable $function, $sequence)
{
    args\expects(args\traversable, $sequence);
    return array_map($function, a\traversableToArray($sequence));
}
const map = '\nspl\a\map';

/**
 * @deprecated
 * @see \nspl\a\reduce
 *
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
    args\expects(args\traversable, $sequence);
    return array_reduce(a\traversableToArray($sequence), $function, $initial);
}
const reduce = '\nspl\a\reduce';

/**
 * @deprecated
 * @see \nspl\a\filter
 *
 * Returns sequence items that satisfy the predicate
 *
 * @param callable $predicate
 * @param array|\Traversable $sequence
 * @return array
 */
function filter(callable $predicate, $sequence)
{
    args\expects(args\traversable, $sequence);

    $sequence = a\traversableToArray($sequence);
    $filtered = array_filter($sequence, $predicate);
    return a\isList($sequence) ? array_values($filtered) : $filtered;
}
const filter = '\nspl\a\filter';

/**
 * @deprecated
 * @see \nspl\a\partition
 *
 * Returns two lists, one containing values for which your predicate returned true, and the other containing
 * the elements that returned false
 *
 * @param callable $predicate
 * @param array|\Traversable $sequence
 * @return array
 */
function partition(callable $predicate, $sequence)
{
    args\expects(args\traversable, $sequence);

    $isList = a\isList($sequence);
    $result = [[], []];
    foreach ($sequence as $k => $v) {
        if ($isList) {
            $result[(int)!$predicate($v)][] = $v;
        }
        else {
            $result[(int)!$predicate($v)][$k] = $v;
        }
    }

    return $result;
}
const partition = '\nspl\a\partition';

/**
 * @deprecated
 * @see \nspl\a\span
 *
 * Returns two lists, one containing values for which your predicate returned true until the predicate returned
 * false, and the other containing all the elements that left
 *
 * @param callable $predicate
 * @param array|\Traversable $sequence
 * @return array
 */
function span(callable $predicate, $sequence)
{
    args\expects(args\traversable, $sequence);

    $isList = a\isList($sequence);
    $result = [[], []];

    $listIndex = 0;
    foreach ($sequence as $k => $v) {
        if (!$predicate($v)) {
            $listIndex = 1;
        }

        if ($isList) {
            $result[$listIndex][] = $v;
        }
        else {
            $result[$listIndex][$k] = $v;
        }
    }

    return $result;
}
const span = '\nspl\a\span';
//endregion

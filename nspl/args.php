<?php

namespace nspl\args;

use function nspl\ds\getType;
use function nspl\a\drop;

/**
 * Checks that argument is boolean otherwise throws the corresponding exception
 * @param mixed $arg
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsBool($arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_bool($arg)) {
        _throwExpectsException($arg, 'be a boolean', $atPosition, $otherwiseThrow);
    }
}

/**
 * Checks that argument is an integer otherwise throws the corresponding exception
 * @param mixed $arg
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsInt($arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_int($arg)) {
        _throwExpectsException($arg, 'be an integer', $atPosition, $otherwiseThrow);
    }
}

/**
 * Checks that argument is a float otherwise throws the corresponding exception
 * @param mixed $arg
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsFloat($arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_float($arg)) {
        _throwExpectsException($arg, 'be a float', $atPosition, $otherwiseThrow);
    }
}

/**
 * Checks that argument is numeric otherwise throws the corresponding exception
 * @param mixed $arg
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsNumeric($arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_numeric($arg)) {
        _throwExpectsException($arg, 'be numeric', $atPosition, $otherwiseThrow);
    }
}

/**
 * Checks that argument is a string otherwise throws the corresponding exception
 * @param mixed $arg
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsString($arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_string($arg)) {
        _throwExpectsException($arg, 'be a string', $atPosition, $otherwiseThrow);
    }
}

/**
 * Checks that argument can be an array key otherwise throws the corresponding exception
 * @param mixed $arg
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsArrayKey($arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_int($arg) && !is_string($arg)) {
        _throwExpectsException($arg, 'be an integer or a string', $atPosition, $otherwiseThrow);
    }
}

/**
 * Checks that argument is an array or traversable otherwise throws the corresponding exception
 * @param mixed $arg
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsTraversable($arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_array($arg) && !$arg instanceof \Traversable) {
        _throwExpectsException($arg, 'be an array or traversable', $atPosition, $otherwiseThrow);
    }
}

/**
 * Checks that argument is an array or implements array access otherwise throws the corresponding exception
 * @param mixed $arg
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsArrayAccess($arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_array($arg) && !$arg instanceof \ArrayAccess) {
        _throwExpectsException($arg, 'be an array or implement array access', $atPosition, $otherwiseThrow);
    }
}

/**
 * Checks that argument implements array access or is a string otherwise throws the corresponding exception
 * @param mixed $arg
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsArrayAccessOrString($arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_array($arg) && !$arg instanceof \ArrayAccess && !is_string($arg)) {
        _throwExpectsException($arg, 'be a string, an array or implement array access', $atPosition, $otherwiseThrow);
    }
}

/**
 * Checks that argument is callable otherwise throws the corresponding exception
 * @param mixed $arg
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsCallable($arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_callable($arg)) {
        _throwExpectsException($arg, 'be a callable', $atPosition, $otherwiseThrow);
    }
}

/**
 * Checks that argument can be an array key or is callable otherwise throws the corresponding exception
 * @param mixed $arg
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsArrayKeyOrCallable($arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_int($arg) && !is_string($arg) && !is_callable($arg)) {
        _throwExpectsException($arg, 'be an integer or a string or a callable', $atPosition, $otherwiseThrow);
    }
}

/**
 * Checks that object has the required method. Is useful when you use duck-typing instead of interfaces
 * @param object $object
 * @param string $method
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsWithMethod($object, $method, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_object($object) || !method_exists($object, $method)) {
        _throwExpectsException($object, 'be an object with public method "' . $method . '"', $atPosition, $otherwiseThrow);
    }
}

/**
 * Checks that object has the required methods. Is useful when you use duck-typing instead of interfaces
 * @param object $object
 * @param string[] $methods
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsWithMethods($object, array $methods, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    $passed = is_object($object);
    if ($passed) {
        foreach ($methods as $method) {
            if (!method_exists($object, $method)) {
                $passed = false;
                break;
            }
        }
    }

    if (!$passed) {
        _throwExpectsException($object, 'be an object with public methods "' . implode('", "', $methods) . '"', $atPosition, $otherwiseThrow);
    }
}

/**
 * Checks that array has the required keys
 * @param mixed $array
 * @param string[] $keys
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsWithKeys(array $array, array $keys, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    $passed = is_array($array);
    if ($passed) {
        foreach ($keys as $key) {
            if (!isset($array[$key]) && !array_key_exists($key, $array)) {
                $passed = false;
                break;
            }
        }
    }

    if (!$passed) {
        _throwExpectsException($array, 'to be an array with keys "' . implode('", "', $keys) . '"', $atPosition, $otherwiseThrow, true);
    }
}

/**
 * Checks that argument satisfies requirements otherwise throws the corresponding exception
 * @param mixed $arg
 * @param string $hasTo Message which tells what the argument is expected to be
 * @param callable $satisfy
 * @param int $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expects($arg, $hasTo, callable $satisfy, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!call_user_func($satisfy, $arg)) {
        _throwExpectsException($arg, $hasTo, $atPosition, $otherwiseThrow, true);
    }
}

/**
 * @param mixed $arg
 * @param string $hadTo
 * @param int $atPosition
 * @param string|\Throwable $exception
 * @param bool $fromExpects
 * @throws \Throwable
 * @throws string
 */
function _throwExpectsException($arg, $hadTo, $atPosition = null, $exception = '\InvalidArgumentException', $fromExpects = false)
{
    list($function, $position, $file, $line) = _getErrorSource($arg);

    if (!is_object($exception) || !($exception instanceof \Exception)) {
        // @todo instanceof \Throwable since PHP 7
        $position = $atPosition !== null ? $atPosition : $position + 1;

        /** @var \InvalidArgumentException $_exception */
        $exception = new $exception(sprintf(
            'Argument %s passed to %s() %s %s%s',
            $position,
            $function,
            $fromExpects ? 'has' : 'must',
            $hadTo,
            $fromExpects ? '' : (', ' . getType($arg) . ' given')
        ));
    }

    $setter = function($property, $arg) { $this->{$property} = $arg; };

    $exceptionSetter = $setter->bindTo($exception, $exception);
    $exceptionSetter('file', $file);
    $exceptionSetter('line', $line);

    $baseExceptionSetter = $setter->bindTo($exception, 'Exception');
    $baseExceptionSetter('trace', drop($exception->getTrace(), 2));

    throw $exception;
}

/**
 * Returns array containing data about the error source
 * @param mixed $arg
 * @return array
 */
function _getErrorSource($arg)
{
    $call = array('function' => 'unknown', 'file' => 'unknown', 'line' => 'unknown');
    foreach (debug_backtrace() as $call) {
        if (substr($call['function'], 0, strlen(__NAMESPACE__)) !== __NAMESPACE__) {
            break;
        }
    }

    $position = 0;
    foreach ($call['args'] as $position => $_arg) {
        if ($arg === $_arg) {
            break;
        }
    }

    $result = array(
        'function' => $call['function'],
        'position' => $position,
        'file' => $call['file'],
        'line' => $call['line'],
    );

    return array_merge(array_values($result), $result);
}

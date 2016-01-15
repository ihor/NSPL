<?php

namespace nspl\args;

use function nspl\ds\getType;

/**
 * Checks that value is boolean otherwise throws the corresponding exception
 * @param mixed $value
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsBool($value, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_bool($value)) {
        _throwExpectsException($value, 'be a boolean', $atPosition, $otherwiseThrow);
    }
}

/**
 * Checks that value is an integer otherwise throws the corresponding exception
 * @param mixed $value
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsInt($value, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_int($value)) {
        _throwExpectsException($value, 'be an integer', $atPosition, $otherwiseThrow);
    }
}

/**
 * Checks that value is a float otherwise throws the corresponding exception
 * @param mixed $value
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsFloat($value, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_float($value)) {
        _throwExpectsException($value, 'be a float', $atPosition, $otherwiseThrow);
    }
}

/**
 * Checks that value is numeric otherwise throws the corresponding exception
 * @param mixed $value
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsNumeric($value, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_numeric($value)) {
        _throwExpectsException($value, 'be numeric', $atPosition, $otherwiseThrow);
    }
}

/**
 * Checks that value is a string otherwise throws the corresponding exception
 * @param mixed $value
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsString($value, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_string($value)) {
        _throwExpectsException($value, 'be a string', $atPosition, $otherwiseThrow);
    }
}

/**
 * Checks that value can be an array key otherwise throws the corresponding exception
 * @param mixed $value
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsArrayKey($value, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_int($value) && !is_string($value)) {
        _throwExpectsException($value, 'be an integer or a string', $atPosition, $otherwiseThrow);
    }
}

/**
 * Checks that value is traversable otherwise throws the corresponding exception
 * @param mixed $value
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsTraversable($value, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_array($value) && !$value instanceof \Traversable) {
        _throwExpectsException($value, 'be an array or traversable', $atPosition, $otherwiseThrow);
    }
}

/**
 * Checks that value implements array access otherwise throws the corresponding exception
 * @param mixed $value
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsArrayAccess($value, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_array($value) && !$value instanceof \ArrayAccess) {
        _throwExpectsException($value, 'be an array or implement array access', $atPosition, $otherwiseThrow);
    }
}

/**
 * Checks that value is callable otherwise throws the corresponding exception
 * @param mixed $value
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsCallable($value, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_callable($value)) {
        _throwExpectsException($value, 'be a callable', $atPosition, $otherwiseThrow);
    }
}

/**
 * Checks that value can be an array key or is callable otherwise throws the corresponding exception
 * @param mixed $value
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsArrayKeyOrCallable($value, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_int($value) && !is_string($value) && !is_callable($value)) {
        _throwExpectsException($value, 'be an integer or a string or a callable', $atPosition, $otherwiseThrow);
    }
}

/**
 * Checks that passed object has the required method. Is useful when you use duck-typing instead of interfaces
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
 * Checks that passed object has the required methods. Is useful when you use duck-typing instead of interfaces
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
 * Checks that passed array has the required keys
 * @param mixed $array
 * @param string[] $keys
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsWithKeys($array, array $keys, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
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
 * Checks that value satisfies requirements otherwise throws the corresponding exception
 * @param mixed $value
 * @param string $hasTo Message which tells what the value is expected to be
 * @param callable $satisfy
 * @param int $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expects($value, $hasTo, callable $satisfy, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!call_user_func($satisfy, $value)) {
        _throwExpectsException($value, $hasTo, $atPosition, $otherwiseThrow, true);
    }
}

/**
 * @param mixed $value
 * @param string $hadTo
 * @param int $atPosition
 * @param string|\Throwable $exception
 * @param bool $fromExpects
 * @throws \Throwable
 * @throws string
 */
function _throwExpectsException($value, $hadTo, $atPosition = null, $exception = '\InvalidArgumentException', $fromExpects = false)
{
    list($function, $position, $file, $line) = _getErrorSource($value);

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
            $fromExpects ? '' : (', ' . getType($value) . ' given')
        ));
    }

    $setter = function($property, $value) { $this->{$property} = $value; };

    $exceptionSetter = $setter->bindTo($exception, $exception);
    $exceptionSetter('file', $file);
    $exceptionSetter('line', $line);

    throw $exception;
}

/**
 * Returns array containing data about the error source
 * @param mixed $value
 * @return array
 */
function _getErrorSource($value)
{
    $call = array('function' => 'unknown', 'file' => 'unknown', 'line' => 'unknown');
    foreach (debug_backtrace() as $call) {
        if (substr($call['function'], 0, strlen(__NAMESPACE__)) !== __NAMESPACE__) {
            break;
        }
    }

    $position = 0;
    foreach ($call['args'] as $position => $arg) {
        if ($arg === $value) {
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

<?php

namespace nspl\args;

const notEmpty = '\nspl\args\_p\isNotEmpty';
const bool = 'is_bool';
const int = 'is_int';
const float = 'is_float';
const numeric = 'is_numeric';
const string = 'is_string';
const callable_ = 'is_callable';
const arrayKey = '\nspl\args\_p\isArrayKey';
const traversable = '\nspl\args\_p\isTraversable';
const arrayAccess = '\nspl\args\_p\isArrayAccess';

/**
 * Returns functions which returns true when passed object has the method
 * @param string $method
 * @return _p\HasMethods
 */
function withMethod($method)
{
    return new _p\HasMethods(func_get_args());
}

/**
 * Returns functions which returns true when passed object has the methods
 * @param string $method1
 * @param string $method2
 * @return _p\HasMethods
 */
function withMethods($method1, $method2 /*, ... */)
{
    return new _p\HasMethods(func_get_args());
}

/**
 * Returns functions which returns true when passed array has the key
 * @param string $key
 * @return _p\HasKeys
 */
function withKey($key)
{
    return new _p\HasKeys(func_get_args());
}

/**
 * Returns functions which returns true when passed array has the keys
 * @param string $key1
 * @param string $key2
 * @return _p\HasKeys
 */
function withKeys($key1, $key2 /*, ... */)
{
    return new _p\HasKeys(func_get_args());
}

/**
 * Checks that argument has the required type (or types) otherwise throws the corresponding exception
 *
 * @param callable|callable[] $type Class name(s) or callable(s) which checks that the argument has the corresponding type(s)
 * @param mixed $arg
 * @param int|null $atPosition If null then calculated automatically
 * @param string $otherwiseThrow Exception class or exception object
 * @throws \Throwable
 */
function expects($type, $arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    // Backward compatibility
    if (null !== $atPosition && is_callable($atPosition)) {
        expectsToBe($type, $arg, $atPosition, is_int($otherwiseThrow) ? $otherwiseThrow : null, func_num_args() > 4 ? func_get_arg(4) : '\InvalidArgumentException');
        return;
    }

    if (is_array($type)) {
        $passed = false;
        foreach ($type as $_type) {
            if (
                (is_callable($_type) && call_user_func($_type, $arg)) ||
                (is_string($_type) && class_exists($_type) && $arg instanceof $_type)
            ) {
                $passed = true;
                break;
            }
        }

        if (!$passed) {
            $message = implode(' or ', array_map('\nspl\args\_p\getErrorMessage', $type));
            _p\throwExpectsException($arg, $message, $atPosition, $otherwiseThrow);
        }
    }
    else if (!call_user_func($type, $arg)) {
        _p\throwExpectsException($arg, _p\getErrorMessage($type), $atPosition, $otherwiseThrow);
    }
}

/**
 * Checks that arguments have the required type (or types) otherwise throws the corresponding exception
 *
 * @param callable|callable[] $type Class name(s) or callable(s) which checks that the argument has the corresponding type(s)
 * @param array $args
 * @param array $atPositions If empty then calculated automatically
 * @param string $otherwiseThrow Exception class or exception object
 * @throws \Throwable
 */
function expectsAll($type, array $args, array $atPositions = [], $otherwiseThrow = '\InvalidArgumentException')
{
    foreach ($args as $k => $arg) {
        expects($type, $arg, isset($atPositions[$k]) ? $atPositions[$k] : null, $otherwiseThrow);
    }
}

/**
 * Checks that argument has the required type (or types) or is null otherwise throws the corresponding exception
 *
 * @param callable|callable[] $type Class name(s) or callable(s) which checks that the argument has the corresponding type(s)
 * @param mixed $arg
 * @param int|null $atPosition If null then calculated automatically
 * @param string $otherwiseThrow
 * @throws \Throwable
 * @throws string
 */
function expectsOptional($type, $arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (null !== $arg && !call_user_func($type, $arg)) {
        _p\throwExpectsException($arg, _p\getErrorMessage($type), $atPosition, $otherwiseThrow);
    }
}

/**
 * Checks that argument satisfies the requirements otherwise throws the corresponding exception
 * @todo Find a better function name
 *
 * @param mixed $arg
 * @param string $toBe Message which tells what the argument is expected to be
 * @param callable $satisfy
 * @param int $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsToBe($arg, $toBe, callable $satisfy, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!call_user_func($satisfy, $arg)) {
        _p\throwExpectsException($arg, 'to be ' . $toBe, $atPosition, $otherwiseThrow, true);
    }
}

#region deprecated
/**
 * @deprecated
 * Checks that argument is not empty otherwise throws the corresponding exception
 * @param mixed $arg
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsNotEmpty($arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!$arg) {
        _p\throwExpectsException($arg, 'not be empty', $atPosition, $otherwiseThrow);
    }
}

/**
 * @deprecated
 * Checks that argument is boolean otherwise throws the corresponding exception
 * @param mixed $arg
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsBool($arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_bool($arg)) {
        _p\throwExpectsException($arg, 'be a boolean', $atPosition, $otherwiseThrow);
    }
}

/**
 * @deprecated
 * Checks that argument is an integer otherwise throws the corresponding exception
 * @param mixed $arg
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsInt($arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_int($arg)) {
        _p\throwExpectsException($arg, 'be an integer', $atPosition, $otherwiseThrow);
    }
}

/**
 * @deprecated
 * Checks that argument is a float otherwise throws the corresponding exception
 * @param mixed $arg
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsFloat($arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_float($arg)) {
        _p\throwExpectsException($arg, 'be a float', $atPosition, $otherwiseThrow);
    }
}

/**
 * @deprecated
 * Checks that argument is numeric otherwise throws the corresponding exception
 * @param mixed $arg
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsNumeric($arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_numeric($arg)) {
        _p\throwExpectsException($arg, 'be numeric', $atPosition, $otherwiseThrow);
    }
}

/**
 * @deprecated
 * Checks that argument is a string otherwise throws the corresponding exception
 * @param mixed $arg
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsString($arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_string($arg)) {
        _p\throwExpectsException($arg, 'be a string', $atPosition, $otherwiseThrow);
    }
}

/**
 * @deprecated
 * Checks that argument can be an array key otherwise throws the corresponding exception
 * @param mixed $arg
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsArrayKey($arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_int($arg) && !is_string($arg)) {
        _p\throwExpectsException($arg, 'be an integer or a string', $atPosition, $otherwiseThrow);
    }
}

/**
 * @deprecated
 * Checks that argument is an array or traversable otherwise throws the corresponding exception
 * @param mixed $arg
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsTraversable($arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_array($arg) && !$arg instanceof \Traversable) {
        _p\throwExpectsException($arg, 'be an array or traversable', $atPosition, $otherwiseThrow);
    }
}

/**
 * @deprecated
 * Checks that argument is an array or implements array access otherwise throws the corresponding exception
 * @param mixed $arg
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsArrayAccess($arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_array($arg) && !$arg instanceof \ArrayAccess) {
        _p\throwExpectsException($arg, 'be an array or implement array access', $atPosition, $otherwiseThrow);
    }
}

/**
 * @deprecated
 * Checks that argument implements array access or is a string otherwise throws the corresponding exception
 * @param mixed $arg
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsArrayAccessOrString($arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_array($arg) && !$arg instanceof \ArrayAccess && !is_string($arg)) {
        _p\throwExpectsException($arg, 'be a string, an array or implement array access', $atPosition, $otherwiseThrow);
    }
}

/**
 * @deprecated
 * Checks that argument can be an array key or is callable otherwise throws the corresponding exception
 * @param mixed $arg
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsArrayKeyOrCallable($arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_int($arg) && !is_string($arg) && !is_callable($arg)) {
        _p\throwExpectsException($arg, 'be an integer or a string or a callable', $atPosition, $otherwiseThrow);
    }
}

/**
 * @deprecated
 * Checks that argument is boolean or is callable otherwise throws the corresponding exception
 * @param mixed $arg
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsBoolOrCallable($arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_bool($arg) && !is_callable($arg)) {
        _p\throwExpectsException($arg, 'be boolean or callable', $atPosition, $otherwiseThrow);
    }
}

/**
 * @deprecated
 * Checks that object has the required method. Is useful when you use duck-typing instead of interfaces
 * @param object $object
 * @param string $method
 * @param int|null $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsWithMethod($object, $method, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!is_object($object) || !method_exists($object, $method)) {
        _p\throwExpectsException($object, 'be an object with public method "' . $method . '"', $atPosition, $otherwiseThrow);
    }
}

/**
 * @deprecated
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
        _p\throwExpectsException($object, 'be an object with public methods "' . implode('", "', $methods) . '"', $atPosition, $otherwiseThrow);
    }
}

/**
 * @deprecated
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
        _p\throwExpectsException($array, 'be an array with keys "' . implode('", "', $keys) . '"', $atPosition, $otherwiseThrow);
    }
}
#endregion

namespace nspl\args\_p;

use nspl\a;
use nspl\ds;

/**
 * @param mixed $value
 * @return bool
 */
function isNotEmpty($value)
{
    return (bool) $value;
}

/**
 * @param mixed $value
 * @return bool
 */
function isArrayKey($value)
{
    return is_int($value) || is_string($value);
}

/**
 * @param mixed $value
 * @return bool
 */
function isTraversable($value)
{
    return is_array($value) || $value instanceof \Traversable;
}

/**
 * @param mixed $value
 * @return bool
 */
function isArrayAccess($value)
{
    return is_array($value) || $value instanceof \ArrayAccess;
}


interface Requirement
{
    /**
     * @return string
     */
    function getErrorMessage();
}


class HasMethods implements Requirement
{
    /**
     * @var string[]
     */
    private $methods;

    /**
     * @param string[] $methods
     */
    public function __construct(array $methods)
    {
        $this->methods = $methods;
    }

    /**
     * @param object $object
     * @return bool
     */
    public function __invoke($object)
    {
        if (!is_object($object)) {
            return false;
        }

        foreach ($this->methods as $method) {
            if (!method_exists($object, $method)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    function getErrorMessage()
    {
        return 'be an object with public method(s) "' . implode('", "', $this->methods) . '"';
    }

}


class HasKeys implements Requirement
{
    /**
     * @var string[]
     */
    private $keys;

    /**
     * @param string[] $keys
     */
    public function __construct(array $keys)
    {
        $this->keys = $keys;
    }

    /**
     * @param object $array
     * @return bool
     */
    public function __invoke($array)
    {
        if (!is_array($array) && !($array instanceof \ArrayAccess)) {
            return false;
        }

        foreach ($this->keys as $key) {
            if (!isset($array[$key]) || !array_key_exists($key, $array)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    function getErrorMessage()
    {
        return 'be an array with key(s) "' . implode('", "', $this->keys) . '"';
    }

}


/**
 * @param string|object $type
 * @return string
 */
function getErrorMessage($type)
{
    if ($type instanceof Requirement) {
        return $type->getErrorMessage();
    }

    return a\getByKey(array(
        \nspl\args\notEmpty => 'not be empty',
        \nspl\args\bool => 'be a boolean',
        \nspl\args\int => 'be an integer',
        \nspl\args\float => 'be a float',
        \nspl\args\numeric => 'be numeric',
        \nspl\args\string => 'be a string',
        \nspl\args\arrayKey => 'be an integer or a string',
        \nspl\args\callable_ => 'be callable',
        \nspl\args\traversable => 'be an array or traversable',
        \nspl\args\arrayAccess => 'be an array or implement array access',
    ), $type, 'be ' . $type);
}

/**
 * @param mixed $arg
 * @param string $hadTo
 * @param int $atPosition
 * @param string|\Throwable $exception
 * @param bool $reportValue
 * @param bool $has
 * @throws \Throwable
 * @throws string
 */
function throwExpectsException($arg, $hadTo, $atPosition = null, $exception = '\InvalidArgumentException', $has = false)
{
    list($function, $position, $file, $line) = getErrorSource($arg);

    if (!is_object($exception) || !($exception instanceof \Exception)) {
        // @todo instanceof \Throwable since PHP 7
        $position = $atPosition !== null ? $atPosition : $position + 1;

        /** @var \InvalidArgumentException $_exception */
        $exception = new $exception(sprintf(
            'Argument %s passed to %s() %s %s%s',
            $position,
            $function,
            $has ? 'has' : 'must',
            $hadTo,
            is_scalar($arg) ? (', ' . ds\getType($arg) . ' ' . var_export($arg, true) . ' given') : (', ' . ds\getType($arg) . ' given')
        ));
    }

    $setter = function($property, $arg) { $this->{$property} = $arg; };

    $exceptionSetter = $setter->bindTo($exception, $exception);
    $exceptionSetter('file', $file);
    $exceptionSetter('line', $line);

    $baseExceptionSetter = $setter->bindTo($exception, 'Exception');
    $baseExceptionSetter('trace', a\drop($exception->getTrace(), 2));

    throw $exception;
}

/**
 * Returns array containing data about the error source
 * @param mixed $arg
 * @return array
 */
function getErrorSource($arg)
{
    $call = array('function' => 'unknown', 'file' => 'unknown', 'line' => 'unknown');
    foreach (debug_backtrace() as $call) {
        if (substr($call['function'], 0, 9) !== 'nspl\args') {
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

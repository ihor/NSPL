<?php

namespace nspl\args;

use \nspl\f;

/**
 * Checks that argument satisfies the required constraints otherwise throws the corresponding exception
 *
 * @param callable|callable[]|string|string[] $constraints Callable(s) which return(s) true if the argument satisfies the requirements or it also might contain the required class name(s)
 * @param mixed $arg
 * @param int|null $atPosition If null then calculated automatically
 * @param string $otherwiseThrow Exception class or exception object
 * @throws \Throwable
 */
function expects($constraints, $arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    // Backward compatibility
    if (null !== $atPosition && is_callable($atPosition)) {
        expectsToBe($constraints, $arg, $atPosition, is_int($otherwiseThrow) ? $otherwiseThrow : null, func_num_args() > 4 ? func_get_arg(4) : '\InvalidArgumentException');
        return;
    }

    if ((array) $constraints === $constraints) {
        $passedAnd = true;
        $passedOr = false;
        foreach ($constraints as $constraint) {
            if (is_callable($constraint)) {
                if (isset(_p\Checker::$isOr[$constraint])) {
                    if (!$passedOr && $constraint($arg)) {
                        $passedOr = true;
                    }
                }
                else if (!$constraint($arg)) {
                    $passedAnd = false;
                    break;
                }
            }
            else if (!$passedOr && class_exists($constraint) && $arg instanceof $constraint) {
                $passedOr = true;
            }
        }

        if (!$passedAnd || !$passedOr) {
            _p\throwExpectsException($arg, _p\ErrorMessage::getFor($constraints), $atPosition, $otherwiseThrow);
        }
    }
    else if (!$constraints($arg)) {
        _p\throwExpectsException($arg, _p\ErrorMessage::getFor($constraints), $atPosition, $otherwiseThrow);
    }
}

/**
 * Checks that all specified arguments satisfy the required constraints otherwise throws the corresponding exception
 *
 * @param callable|callable[]|string|string[] $constraints Callable(s) which return(s) true if the argument satisfies the requirements or it also might contain the required class name(s)
 * @param array $args
 * @param array $atPositions If empty then calculated automatically
 * @param string $otherwiseThrow Exception class or exception object
 * @throws \Throwable
 */
function expectsAll($constraints, array $args, array $atPositions = [], $otherwiseThrow = '\InvalidArgumentException')
{
    foreach ($args as $k => $arg) {
        expects($constraints, $arg, isset($atPositions[$k]) ? $atPositions[$k] : null, $otherwiseThrow);
    }
}

/**
 * Checks that argument is null or satisfies the required constraints otherwise throws the corresponding exception
 *
 * @param callable|callable[] $constraints Callable(s) which return(s) true if the argument satisfies the requirements or it also might contain the required class name(s)
 * @param mixed $arg
 * @param int|null $atPosition If empty then calculated automatically
 * @param string $otherwiseThrow Exception class or exception object
 * @throws \Throwable
 */
function expectsOptional($constraints, $arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (null !== $arg) {
        expects($constraints, $arg, $atPosition, $otherwiseThrow);
    }
}

// Or-constraints
const bool = 'is_bool';
const int = 'is_int';
const float = 'is_float';
const numeric = 'is_numeric';
const string = 'is_string';
const callable_ = 'is_callable';
const arrayKey = '\nspl\args\_p\isArrayKey';
const traversable = '\nspl\args\_p\isTraversable';
const arrayAccess = '\nspl\args\_p\isArrayAccess';

// And-constraints
const nonEmpty = '\nspl\args\_p\isNotEmpty';
const positive = '\nspl\args\_p\isPositive';
const nonNegative = '\nspl\args\_p\isNotNegative';
const nonZero = '\nspl\args\_p\isNotZero';

/**
 * Returns a function which returns true when any of given constraints is satisfied
 * @param callable $constraints
 * @return _p\Any
 */
function any(callable $constraints)
{
    return new _p\Any(func_get_args());
}

/**
 * Returns a function which returns true when none of given constraints are satisfied
 * @param callable $constraints
 * @return _p\Not
 */
function not(callable $constraints)
{
    return new _p\Not(func_get_args());
}

/**
 * Returns a function which returns true when the argument is one of the given values
 * @param mixed $value1
 * @param mixed $value2
 * @return _p\Values
 */
function values($value1, $value2 /*, ... */)
{
    return new _p\Values(func_get_args());
}

/**
 * Returns a function which returns true is given string is shorter than $threshold
 * @param int $threshold
 * @return _p\LessThan
 */
function shorterThan($threshold)
{
    return new _p\LessThan('strlen', $threshold, 'shorter');
}

/**
 * Returns a function which returns true is given string is longer than $threshold
 * @param int $threshold
 * @return _p\LessThan
 */
function longerThan($threshold)
{
    return new _p\MoreThan('strlen', $threshold, 'longer');
}

/**
 * Returns a function which returns true is given number is smaller than $threshold
 * @param int $threshold
 * @return _p\LessThan
 */
function smallerThan($threshold)
{
    return new _p\LessThan(f\id, $threshold, 'smaller');
}

/**
 * Returns a function which returns true is given number is bigger than $threshold
 * @param int $threshold
 * @return _p\LessThan
 */
function biggerThan($threshold)
{
    return new _p\MoreThan(f\id, $threshold, 'bigger');
}

/**
 * Returns a function which returns true when given array has the key
 * @param string $key
 * @return _p\HasKeys
 */
function withKey($key)
{
    return new _p\HasKeys(func_get_args());
}

/**
 * Returns a function which returns true when given array has the keys
 * @param string $key1
 * @param string $key2
 * @return _p\HasKeys
 */
function withKeys($key1, $key2 /*, ... */)
{
    return new _p\HasKeys(func_get_args());
}

/**
 * Returns a function which returns true when given object has the method
 * @param string $method
 * @return _p\HasMethods
 */
function withMethod($method)
{
    return new _p\HasMethods(func_get_args());
}

/**
 * Returns a function which returns true when given object has the methods
 * @param string $method1
 * @param string $method2
 * @return _p\HasMethods
 */
function withMethods($method1, $method2 /*, ... */)
{
    return new _p\HasMethods(func_get_args());
}

interface Constraint
{
    /**
     * Returns message which will be used in the exception when value doesn't satisfy the constraint
     * The message must contain text which goes after "must" in the exception message
     *
     * @return string
     */
    function __toString();

    /**
     * Returns true if the value satisfies the constraint
     *
     * @param mixed $value
     * @return bool
     */
    function __invoke($value);

}

#region deprecated
/**
 * @deprecated
 * Checks that argument satisfies the required constraints otherwise throws the corresponding exception
 *
 * @param mixed $arg
 * @param string $toBe Message which tells what the argument is expected to be
 * @param callable $constraints Callable which returns true if argument satisfies the constraints
 * @param int $atPosition If null then calculated automatically
 * @param string|\Throwable $otherwiseThrow Exception class or exception object
 */
function expectsToBe($arg, $toBe, callable $constraints, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')
{
    if (!$constraints($arg)) {
        _p\throwExpectsException($arg, 'to be ' . $toBe, $atPosition, $otherwiseThrow, true);
    }
}

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
use nspl\f;
use nspl\ds;
use nspl\args\Constraint;

function isNotEmpty($value) { return (bool) $value; }
function isArrayKey($value) { return is_int($value) || is_string($value); }
function isTraversable($value) { return is_array($value) || $value instanceof \Traversable; }
function isArrayAccess($value) { return is_array($value) || $value instanceof \ArrayAccess; }
function isPositive($value) { return $value > 0; }
function isNotNegative($value) { return $value >= 0; }
function isNotZero($value) { return $value !== 0; }

/**
 * @param mixed $arg
 * @param string $hadTo
 * @param int $atPosition
 * @param string|\Throwable $exception
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
            is_scalar($arg)
                ? (', ' . \nspl\getType($arg) . ' ' . var_export($arg, true) . ' given')
                : (', ' . \nspl\getType($arg) . (is_array($arg) ? (' ' . json_encode($arg)): '') . ' given')
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
        'function' => (isset($call['class']) ? $call['class'] . '::' : '') . $call['function'],
        'position' => $position,
        'file' => $call['file'],
        'line' => $call['line'],
    );

    return array_merge(array_values($result), $result);
}

class Checker
{
    public static $isOr = array(
        \nspl\args\bool => true,
        \nspl\args\int => true,
        \nspl\args\float => true,
        \nspl\args\numeric => true,
        \nspl\args\string => true,
        \nspl\args\arrayKey => true,
        \nspl\args\callable_ => true,
        \nspl\args\traversable => true,
        \nspl\args\arrayAccess => true,
    );

}

class ErrorMessage
{
    public static $messages = array(
        \nspl\args\bool => 'be a boolean',
        \nspl\args\int => 'be an integer',
        \nspl\args\float => 'be a float',
        \nspl\args\numeric => 'be numeric',
        \nspl\args\string => 'be a string',
        \nspl\args\arrayKey => 'be an integer or a string',
        \nspl\args\callable_ => 'be callable',
        \nspl\args\traversable => 'be an array or traversable',
        \nspl\args\arrayAccess => 'be an array or implement array access',

        \nspl\args\nonEmpty => 'not be empty',
        \nspl\args\positive => 'be positive',
        \nspl\args\nonNegative => 'be non-negative',
        \nspl\args\nonZero => 'be non-zero',
    );

    /**
     * @param string|object|array $type
     * @param bool $onlyOr
     * @return string
     */
    public static function getFor($type, $onlyOr = false)
    {
        if (is_array($type)) {
            $messagesFor = f\partial(a\map, ['\nspl\args\_p\ErrorMessage', 'getFor']);
            if ($onlyOr) {
                return implode(' or ', $messagesFor($type));
            }
            else {
                $isOr = function ($t) { return isset(Checker::$isOr[$t]) || class_exists($t); };
                list($orTypes, $andTypes) = a\partition($isOr, $type);

                return implode(' and ', array_filter([
                    implode(' or ', $messagesFor($orTypes)),
                    implode(' and ', $messagesFor($andTypes))
                ]));
            }
        }

        if ($type instanceof Constraint) {
            return $type->__toString();
        }

        $default = class_exists($type)
            ? $type
            : implode(' ', array_map('strtolower', preg_split('/(?=[A-Z])/', end(explode('\\', $type)))));

        return a\value(self::$messages, $type, 'be ' . $default);
    }

}


class HasMethods implements Constraint
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
    function __toString()
    {
        return 'be an object with public method(s) \'' . implode("', '", $this->methods) . "'";
    }

}

class HasKeys implements Constraint
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
     * @param array $array
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
    function __toString()
    {
        return 'be an array with key(s) ' . implode(', ', array_map(function($v) {
            return var_export($v, true);
        }, $this->keys));
    }

}

class LessThan implements Constraint
{
    /**
     * @var callable
     */
    private $function;

    /**
     * @var callable
     */
    private $threshold;

    /**
     * @var string
     */
    private $message;

    /**
     * @param callable $function
     * @param int $threshold
     * @param string $message
     */
    public function __construct($function, $threshold, $message)
    {
        $this->function = $function;
        $this->threshold = $threshold;
        $this->message = $message;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function __invoke($value)
    {
        $f = $this->function;
        return $f($value) < $this->threshold;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return 'be ' . $this->message . ' than ' . $this->threshold;
    }

}

class MoreThan implements Constraint
{
    /**
     * @var callable
     */
    private $function;

    /**
     * @var callable
     */
    private $threshold;

    /**
     * @var string
     */
    private $message;

    /**
     * @param callable $function
     * @param int $threshold
     * @param string $message
     */
    public function __construct($function, $threshold, $message)
    {
        $this->function = $function;
        $this->threshold = $threshold;
        $this->message = $message;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function __invoke($value)
    {
        $f = $this->function;
        return $f($value) > $this->threshold;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return 'be ' . $this->message . ' than ' . $this->threshold;
    }

}

class Not implements Constraint
{
    /**
     * @var callable[]
     */
    private $constraints;

    /**
     * @param callable[] $constraints
     */
    public function __construct(array $constraints)
    {
        $this->constraints = $constraints;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function __invoke($value)
    {
        foreach ($this->constraints as $expectation) {
            if ($expectation($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return 'not be ' . ErrorMessage::getFor($this->constraints, true);
    }

}

class Any implements Constraint
{
    /**
     * @var callable[]
     */
    private $constraints;

    /**
     * @param callable[] $constraints
     */
    public function __construct(array $constraints)
    {
        $this->constraints = $constraints;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function __invoke($value)
    {
        foreach ($this->constraints as $expectation) {
            if ($expectation($value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return 'be ' . ErrorMessage::getFor($this->constraints, true);
    }

}

class Values implements Constraint
{
    /**
     * @var array
     */
    private $values;

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function __invoke($value)
    {
        return in_array($value, $this->values);
    }

    /**
     * @return string
     */
    function __toString()
    {
        return 'be one of the following values ' . implode(', ', array_map(function($v) {
            return var_export($v, true);
        }, $this->values));
    }

}
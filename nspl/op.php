<?php

namespace nspl\op;

use \nspl\args;

function sum($a, $b) { return $a + $b; };
const sum = '\nspl\op\sum';

function sub($a, $b) { return $a - $b; };
const sub = '\nspl\op\sub';

function mul($a, $b) { return $a * $b; };
const mul = '\nspl\op\mul';

function div($a, $b) { return $a / $b; };
const div = '\nspl\op\div';

function mod($a, $b) { return $a % $b; };
const mod = '\nspl\op\mod';

function inc($a) { return ++$a; };
const inc = '\nspl\op\inc';

function dec($a) { return --$a; };
const dec = '\nspl\op\dec';

function neg($a) { return - $a; };
const neg = '\nspl\op\neg';

function band($a, $b) { return $a & $b; };
const band = '\nspl\op\band';

function bxor($a, $b) { return $a ^ $b; };
const bxor = '\nspl\op\bxor';

function bor($a, $b) { return $a | $b; };
const bor = '\nspl\op\bor';

function bnot($a) { return ~ $a; };
const bnot = '\nspl\op\bnot';

function lshift($a, $b) { return $a << $b; };
const lshift = '\nspl\op\lshift';

function rshift($a, $b) { return $a >> $b; };
const rshift = '\nspl\op\rshift';

function lt($a, $b) { return $a < $b; };
const lt = '\nspl\op\lt';

function le($a, $b) { return $a <= $b; };
const le = '\nspl\op\le';

function eq($a, $b) { return $a == $b; };
const eq = '\nspl\op\eq';


function idnt($a, $b) { return $a === $b; };
const idnt = '\nspl\op\idnt';

function ne($a, $b) { return $a != $b; };
const ne = '\nspl\op\ne';

function nidnt($a, $b) { return $a !== $b; };
const nidnt = '\nspl\op\nidnt';

function ge($a, $b) { return $a >= $b; };
const ge = '\nspl\op\ge';

function gt($a, $b) { return $a > $b; };
const gt = '\nspl\op\gt';


function and_($a, $b) { return $a && $b; };
const and_ = '\nspl\op\and_';

function or_($a, $b) { return $a || $b; };
const or_ = '\nspl\op\or_';

function xor_($a, $b) { return $a xor $b; };
const xor_ = '\nspl\op\xor_';

function not($a) { return !$a; };
const not = '\nspl\op\not';


function concat($a, $b) { return $a . $b; };
const concat = '\nspl\op\concat';


function int($a) { return (int) $a; };
const int = '\nspl\op\int';

function bool($a) { return (bool) $a; };
const bool = '\nspl\op\bool';

function float($a) { return (float) $a; };
const float = '\nspl\op\float';

function str($a) { return (string) $a; };
const str = '\nspl\op\str';

function array_($a) { return (array) $a; };
const array_ = '\nspl\op\array_';

function object($a) { return (object) $a; };
const object = '\nspl\op\object';

/**
 * Returns a function that returns key value for a given array
 * @param string $key Array key. Optionally it takes several keys as arguments and returns list of values
 * @return callable
 */
function itemGetter($key)
{
    args\expects(args\arrayKey, $key);

    if (func_num_args() > 1) {
        $keys = func_get_args();
        return function($array) use ($keys) {
            return array_map(function($k) use ($array) { return $array[$k]; }, $keys);
        };
    }

    return function($array) use ($key) {
        return $array[$key];
    };
}

/**
 * Returns a function that returns property value for a given object
 * @param string $property Object property
 * @return callable
 */
function propertyGetter($property)
{
    args\expects(args\string, $property);

    if (func_num_args() > 1) {
        $properties = func_get_args();
        return function($object) use ($properties) {
            $result = array();
            foreach ($properties as $property) {
                $result[$property] = $object->{$property};
            }

            return $result;
        };
    }

    return function($object) use ($property) {
        return $object->{$property};
    };
}

/**
 * Returns a function that returns method result for a given object on predefined arguments
 * @param string $method Object method
 * @param array $args
 * @return callable
 */
function methodCaller($method, array $args = array())
{
    args\expects(args\string, $method);

    return function($object) use ($method, $args) {
        return call_user_func_array(array($object, $method), $args);
    };
}

/**
 * Returns a function that returns a new instance of a predefined class, passing its parameters to the constructor
 * @param string $class Class name
 * @return callable
 */
function instanceCreator($class)
{
    args\expects(args\string, $class);

    $reflectionClass = new \ReflectionClass($class);

    return function() use ($class, $reflectionClass) {
        return call_user_func_array(array($reflectionClass, 'newInstance'), func_get_args());
    };
}

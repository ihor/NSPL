<?php

namespace nspl\op;

function sum($a, $b) { return $a + $b; };
function sub($a, $b) { return $a - $b; };
function mul($a, $b) { return $a * $b; };
function div($a, $b) { return $a / $b; };
function mod($a, $b) { return $a % $b; };
function inc($a) { return ++$a; };
function dec($a) { return --$a; };
function neg($a) { return - $a; };

function band($a, $b) { return $a & $b; };
function bxor($a, $b) { return $a ^ $b; };
function bor($a, $b) { return $a | $b; };
function bnot($a) { return ~ $a; };
function lshift($a, $b) { return $a << $b; };
function rshift($a, $b) { return $a >> $b; };

function lt($a, $b) { return $a < $b; };
function le($a, $b) { return $a <= $b; };
function eq($a, $b) { return $a == $b; };
function idnt($a, $b) { return $a === $b; };
function ne($a, $b) { return $a != $b; };
function nidnt($a, $b) { return $a !== $b; };
function ge($a, $b) { return $a >= $b; };
function gt($a, $b) { return $a > $b; };

function and_($a, $b) { return $a && $b; };
function mand($a, $b) {
    if ($a) {
        return (bool) $b;
    }

    return !$b;
};
function or_($a, $b) { return $a || $b; };
function xor_($a, $b) { return $a xor $b; };
function not($a) { return !$a; };

function concat($a, $b) { return $a . $b; };

function int($a) { return (int) $a; };
function bool($a) { return (bool) $a; };
function float($a) { return (float) $a; };
function str($a) { return (string) $a; };
function array_($a) { return (array) $a; };
function object($a) { return (object) $a; };

/**
 * Returns a function that returns key value for a given array
 * @param string $key Array key. Optionally it takes several keys as arguments and returns list of values
 * @return callable
 */
function itemGetter($key)
{
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
    return function($object) use ($method, $args) {
        return call_user_func_array(array($object, $method), $args);
    };
}

namespace nspl;

class op
{
    const sum = '\nspl\op\sum';
    const sub = '\nspl\op\sub';
    const mul = '\nspl\op\mul';
    const div = '\nspl\op\div';
    const mod = '\nspl\op\mod';
    const inc = '\nspl\op\inc';
    const dec = '\nspl\op\dec';
    const neg = '\nspl\op\neg';

    const band = '\nspl\op\band';
    const bxor = '\nspl\op\bxor';
    const bor = '\nspl\op\bor';
    const bnot = '\nspl\op\bnot';
    const lshift = '\nspl\op\lshift';
    const rshift = '\nspl\op\rshift';

    const lt = '\nspl\op\lt';
    const le = '\nspl\op\le';
    const eq = '\nspl\op\eq';
    const idnt = '\nspl\op\idnt';
    const ne = '\nspl\op\ne';
    const nidnt = '\nspl\op\nidnt';
    const ge = '\nspl\op\ge';
    const gt = '\nspl\op\gt';

    const and_ = '\nspl\op\and_';
    const mand = '\nspl\op\mand';
    const or_ = '\nspl\op\or_';
    const xor_ = '\nspl\op\xor_';
    const not = '\nspl\op\not';

    const concat = '\nspl\op\concat';

    const int = '\nspl\op\int';
    const bool = '\nspl\op\bool';
    const float = '\nspl\op\float';
    const str = '\nspl\op\str';
    const array_ = '\nspl\op\array_';
    const object = '\nspl\op\object';

}

<?php

namespace nspl;

class op
{
    static public $sum;
    static public $sub;
    static public $mul;
    static public $div;
    static public $mod;
    static public $inc;
    static public $dec;
    static public $neg;

    static public $band;
    static public $bxor;
    static public $bor;
    static public $bnot;
    static public $lshift;
    static public $rshift;

    static public $lt;
    static public $le;
    static public $eq;
    static public $idnt;
    static public $ne;
    static public $nidnt;
    static public $ge;
    static public $gt;

    static public $and;
    static public $mand;
    static public $or;
    static public $xor;
    static public $not;

    static public $concat;

    /**
     * Returns a function that returns key value for a given array
     * @param string $key Array key. Optionally it takes several keys as arguments and returns list of values
     * @return callable
     */
    static public function itemGetter($key)
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
    static public function propertyGetter($property)
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
    static public function methodCaller($method, array $args = array())
    {
        return function($object) use ($method, $args) {
            return call_user_func_array(array($object, $method), $args);
        };
    }
}

op::$sum = function($a, $b) { return $a + $b; };
op::$sub = function($a, $b) { return $a - $b; };
op::$mul = function($a, $b) { return $a * $b; };
op::$div = function($a, $b) { return $a / $b; };
op::$mod = function($a, $b) { return $a % $b; };
op::$inc = function($a) { return ++$a; };
op::$dec = function($a) { return --$a; };
op::$neg = function($a) { return - $a; };

op::$band = function($a, $b) { return $a & $b; };
op::$bxor = function($a, $b) { return $a ^ $b; };
op::$bor = function($a, $b) { return $a | $b; };
op::$bnot = function($a) { return ~ $a; };
op::$lshift = function($a, $b) { return $a << $b; };
op::$rshift = function($a, $b) { return $a >> $b; };

op::$lt = function($a, $b) { return $a < $b; };
op::$le = function($a, $b) { return $a <= $b; };
op::$eq = function($a, $b) { return $a == $b; };
op::$idnt = function($a, $b) { return $a === $b; };
op::$ne = function($a, $b) { return $a != $b; };
op::$nidnt = function($a, $b) { return $a !== $b; };
op::$ge = function($a, $b) { return $a >= $b; };
op::$gt = function($a, $b) { return $a > $b; };

op::$and = function($a, $b) { return $a && $b; };
op::$mand = function($a, $b) {
    if ($a) {
        return (bool) $b;
    }

    return !$b;
};
op::$or = function($a, $b) { return $a || $b; };
op::$xor = function($a, $b) { return $a xor $b; };
op::$not = function($a) { return !$a; };

op::$concat = function($a, $b) { return $a . $b; };

namespace nspl\op;

function itemGetter($key) { return call_user_func_array(['\nspl\op', 'itemGetter'], func_get_args()); }
function propertyGetter($property) { return call_user_func_array(['\nspl\op', 'propertyGetter'], func_get_args()); }
function methodCaller($method, array $args = array()) { return \nspl\op::methodCaller($method, $args); }

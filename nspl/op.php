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

    static public $and_;
    static public $xor_;
    static public $or_;
    static public $not_;
    static public $lshift;
    static public $rshift;

    static public $lt;
    static public $le;
    static public $eq;
    static public $idnt;
    static public $ne;
    static public $nis;
    static public $ge;
    static public $gt;

    static public $and;
    static public $or;
    static public $xor;
    static public $not;

    static public $concat;

    /**
     * Returns a function that returns key value for a given array
     * @param string $key Array key
     * @return callable
     */
    static public function itemGetter($key)
    {
        return function($array) use ($key) {
            return isset($array[$key]) || array_key_exists($key, $array)
                ? $array[$key]
                : null;
        };
    }

    /**
     * Returns a function that returns property value for a given object
     * @param string $property Object property
     * @return callable
     */
    static public function propertyGetter($property)
    {
        return function($object) use ($property) {
            if (!property_exists($object, $property)) {
                throw new \InvalidArgumentException(sprintf(
                    'Object "%s" does not have public property "%s"',
                    get_class($object),
                    $property
                ));
            }
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
            if (!method_exists($object, $method)) {
                throw new \InvalidArgumentException(sprintf(
                    'Object "%s" does not have public method "%s"',
                    get_class($object),
                    $method
                ));
            }
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

op::$and_ = function($a, $b) { return $a & $b; };
op::$xor_ = function($a, $b) { return $a ^ $b; };
op::$or_ = function($a, $b) { return $a | $b; };
op::$not_ = function($a) { return ~ $a; };
op::$lshift = function($a, $b) { return $a << $b; };
op::$rshift = function($a, $b) { return $a >> $b; };

op::$lt = function($a, $b) { return $a < $b; };
op::$le = function($a, $b) { return $a <= $b; };
op::$eq = function($a, $b) { return $a == $b; };
op::$idnt = function($a, $b) { return $a === $b; };
op::$ne = function($a, $b) { return $a != $b; };
op::$nis = function($a, $b) { return $a !== $b; };
op::$ge = function($a, $b) { return $a >= $b; };
op::$gt = function($a, $b) { return $a > $b; };

op::$and = function($a, $b) { return $a && $b; };
op::$or = function($a, $b) { return $a || $b; };
op::$xor = function($a, $b) { return $a xor $b; };
op::$not = function($a) { return !$a; };

op::$concat = function($a, $b) { return $a . $b; };

function itemGetter($key) { return op::itemGetter($key); }
function propertyGetter($property) { return op::propertyGetter($property); }
function methodCaller($method, array $args = array()) { return op::methodCaller($method, $args); }

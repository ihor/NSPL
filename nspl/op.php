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

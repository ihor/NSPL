<?php

require_once __DIR__ . '/../autoload.php';

use const \nspl\args\int;
use const \nspl\args\numeric;
use const \nspl\args\string;
use const \nspl\args\nonEmpty;
use const \nspl\args\arrayAccess;
use function \nspl\args\withKeys;
use function \nspl\args\withMethod;
use function \nspl\args\expects;
use function \nspl\args\expectsAll;
use function \nspl\args\expectsOptional;
use function \nspl\args\expectsToBe;

function callAndCatch($function, $arg)
{
    try {
        $args = func_get_args();
        array_shift($args);
        call_user_func_array($function, $args);
    }
    catch (\InvalidArgumentException $e) {
        echo $e->getMessage() . "\n";
    }
}

// 1. Specify scalar parameter type
function sqr($x)
{
    expects(numeric, $x);
    return $x * $x;
}

callAndCatch('sqr', 'hello world');

// 2. Specify several types
function first($sequence)
{
    expects([nonEmpty, arrayAccess, string], $sequence);
    return $sequence[0];
}

callAndCatch('first', 12);

// 3. Specify several parameters of the same type
function concat($str1, $str2)
{
    expectsAll(string, [$str1, $str2]);
    return $str1 . $str2;
}

callAndCatch('concat', 1, 2);

// 4. Specify type for optional parameter
function splitBy($string, $separator = ' ', $limit = null)
{
    expectsAll(string, [$string, $separator]);
    expectsOptional(int, $string);

    return explode($separator, $string, $limit);
}

callAndCatch('splitBy', 'hello world', ' ', 'world');


// 5. Specify user-defined type
class Integer {}

function sum($x, $y)
{
    expectsAll([int, Integer::class], [$x, $y]);
}

callAndCatch('sum', 'hello', 'world');

// 6. Specify array with keys
function getUserFullName($data)
{
    expects(withKeys('first_name', 'last_name'), $data);
    return $data['first_name'] . ' ' . $data['last_name'];
}

callAndCatch('getUserFullName', array('hello' => 'world'));

// 7. Specify object with methods
function pet($duck)
{
    expects(withMethod('quack'), $duck);
}

callAndCatch('pet', new Integer());


// 8. Specify with custom checking function
function validYear($year)
{
    return is_int($year) && $year > 1900 && $year <= (int) date('Y');
}
const validYear = 'validYear';

function calculateAge($yearOfBirth)
{
    expects(validYear, $yearOfBirth);

    return (int) date('Y') - $yearOfBirth;
}

callAndCatch('calculateAge', 1800);

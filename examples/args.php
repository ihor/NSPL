<?php

require_once __DIR__ . '/../autoload.php';

use const \nspl\args\int;
use const \nspl\args\numeric;
use const \nspl\args\string;
use const \nspl\args\notEmpty;
use const \nspl\args\arrayAccess;
use function \nspl\args\withKeys;
use function \nspl\args\withMethod;
use function \nspl\args\expects;
use function \nspl\args\expectsAll;
use function \nspl\args\expectsOptional;
use function \nspl\args\expectsToBe;


// 1. Specify scalar parameter type
function sqr($x)
{
    expects(numeric, $x);
    return $x * $x;
}

try {
    sqr('hello world');
}
catch (\InvalidArgumentException $e) {
    echo $e->getMessage() . "\n";
}

// 2. Specify several types
function first($sequence)
{
    expects([notEmpty, arrayAccess, string], $sequence);
    return $sequence[0];
}

try {
    first(12);
}
catch (\InvalidArgumentException $e) {
    echo $e->getMessage() . "\n";
}

// 3. Specify several parameters of the same type
function concat($str1, $str2)
{
    expectsAll(string, [$str1, $str2]);
    return $str1 . $str2;
}

try {
    concat(1, 2);
}
catch (\InvalidArgumentException $e) {
    echo $e->getMessage() . "\n";
}

// 4. Specify type for optional parameter
function splitBy($string, $separator = ' ', $limit = null)
{
    expectsAll(string, [$string, $separator]);
    expectsOptional(int, $string);

    return explode($separator, $string, $limit);
}

try {
    splitBy('hello world', ' ', 'world');
}
catch (\InvalidArgumentException $e) {
    echo $e->getMessage() . "\n";
}

// 5. Specify user-defined type
class Integer {}

function sum($x, $y)
{
    expectsAll([int, Integer::class], [$x, $y]);
}

try {
    sum('hello', 'world');
}
catch (\InvalidArgumentException $e) {
    echo $e->getMessage() . "\n";
}

// 6. Specify array with keys
function getUserFullName($data)
{
    expects(withKeys('first_name', 'last_name'), $data);
    return $data['first_name'] . ' ' . $data['last_name'];
}

try {
    getUserFullName(array('hello' => 'world'));
}
catch (\InvalidArgumentException $e) {
    echo $e->getMessage() . "\n";
}

// 7. Specify object with methods
function pet($duck)
{
    expects(withMethod('quack'), $duck);
}

try {
    pet(new Integer());
}
catch (\InvalidArgumentException $e) {
    echo $e->getMessage() . "\n";
}

// 8. Specify with custom checking function
function calculateAge($yearOfBirth)
{
    expectsToBe($yearOfBirth, 'to be an integer > 1900 and <= current year', function($arg) {
        return is_int($arg) && $arg > 1900 && $arg <= (int) date('Y');
    });

    return (int) date('Y') - $yearOfBirth;
}

try {
    $age = calculateAge(1800);
}
catch (\InvalidArgumentException $e) {
    echo $e->getMessage() . "\n";
}

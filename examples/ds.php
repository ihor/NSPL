<?php

require_once __DIR__ . '/../autoload.php';

use function \nspl\ds\defaultarray;
use function \nspl\ds\dstring;

use function \nspl\f\map;
use function \nspl\op\methodCaller;


// 1. Cast default array to regular PHP array
$wordCounter = defaultarray(0);
++$wordCounter['hello'];
++$wordCounter['hello'];
++$wordCounter['hello'];
++$wordCounter['world'];

echo "Word counter:\n";
print_r($wordCounter->toArray());


// 2. Multidimensional default array
// Note that we create nested default array with an anonymous function.
// Otherwise, default array object will be shared across all parent array fields.
$matrix = defaultarray(function() { return defaultarray(0); });
for ($i = 0; $i < 3; ++$i) {
    for ($j = 0; $j < 3; ++$j) {
        ++$matrix[$i][$j];
    }
}

echo "Matrix 3x3:\n";
print_r(map(methodCaller('toArray'), $matrix->toArray())); // casting default array with all nested default arrays to PHP array


// 3. Dynamic email subject
$subject = dstring()
    ->addStr('Something bad happened on ')
    ->addConstant('APPLICATION_ENV')
    ->addStr(' at ')
    ->addFunction('date', ['Y-m-d H:i:s']);

define('APPLICATION_ENV', 'staging');
echo $subject . "\n";

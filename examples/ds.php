<?php

require_once __DIR__ . '/../autoload.php';

use \nspl\ds\Set;
use function \nspl\ds\set;
use function \nspl\ds\defaultarray;

use function \nspl\a\map;
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

// 3. Set example
$set = set(1, 2);

$set->add('hello');
$set[] = 'world';
$set->update([3, 4], ['answer', 42]);

echo "Set:\n";
print_r($set->toArray());

foreach (['hello', 3, 4, 'answer', 42] as $element) {
    $set->delete($element);
}

echo "Set:\n";
print_r($set->toArray());

$array = [1, 2, 3];
$intersection = $set->intersection($array);

echo "Intersection:\n";
print_r($intersection->toArray());

$anotherSet = Set::fromArray([1, 2, 3]);
$difference = $set->difference($anotherSet);

echo "Difference:\n";
print_r($difference->toArray());

$iterator = new \ArrayIterator([1, 2, 3]);
$union = $set->union($iterator);

echo "Union:\n";
print_r($union->toArray());

$isSubset = $set->isSubset([1, 2, 'hello', 'world']);

$isSuperset = $set->isSuperset([1, 2]);

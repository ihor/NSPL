<?php

require_once __DIR__ . '/../autoload.php';

use const nspl\a\lazy\{take, map, filter};
use function nspl\a\lazy\with;
use function nspl\f\{pipe, partial, rpartial};

// Calls generator function and logs the yielded values
function logged(callable $generatorFunction)
{
    static $count = 1;
    return function(...$args) use ($generatorFunction, &$count) {
        foreach ($generatorFunction(...$args) as $value) {
            echo $count++ . '. ' .  (string) $generatorFunction . ' -> ' . $value . "\n";
            yield $value;
        };
    };
}

// Returns list of natural numbers starting 1 (never terminates)
function naturalNumbers()
{
    $current = 1;
    while (true) yield $current++;
}
const naturalNumbers = 'naturalNumbers';

// Returns square of a number
function square($n)
{
    return $n * $n;
}
const square = 'square';

// Checks if a number is even
function isEven($n)
{
    return $n % 2 === 0;
}
const isEven = 'isEven';

echo "Using f\pipe() function:\n\n";

$result = pipe(
    logged(naturalNumbers)(), // from all natural numbers
    partial(logged(filter), isEven), // filter only even numbers
    rpartial(logged(take), 3), // take only first 3 even numbers
    partial(logged(map), square) // and calculate their squares
);

foreach ($result as $value) {
    echo "\nNext value is $value \n\n";
}

echo "The same solution with chaining:\n\n";

$result = with(logged(naturalNumbers)())
    ->filter(isEven)
    ->take(3)
    ->map(square);

foreach ($result as $value) {
    echo "\nNext value is $value \n\n";
}

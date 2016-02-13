<?php

require_once __DIR__ . '/../autoload.php';

use nspl\f;
use function nspl\f\partial;
use function nspl\f\rpartial;
use function nspl\f\flipped;
use function nspl\f\compose;
use function nspl\f\memoized;

use const nspl\op\object;
use const nspl\op\gt;
use const nspl\op\mul;
use function nspl\op\propertyGetter;

use const \nspl\a\getByKey;
use function \nspl\a\map;
use function \nspl\a\reduce;
use function \nspl\a\filter;

$users = map(object, [
    array('id' => 1, 'name' => 'John', 'age' => 15),
    array('id' => 2, 'name' => 'Jack', 'age' => 35),
    array('id' => 3, 'name' => 'Sarah', 'age' => 25),
    array('id' => 4, 'name' => 'Norah', 'age' => 20),
    array('id' => 5, 'name' => 'Michael', 'age' => 30),
    array('id' => 6, 'name' => 'Bob', 'age' => 30),
]);

// 1. Get user name from which can be stored as username, user_name or name in data array
$data = array('id' => 1337, 'name' => 'John', 'gender' => 'male');
$name = reduce(flipped(partial(getByKey, $data)), ['username', 'user_name', 'name'], '');

echo sprintf("User name is %s\n", $name);


// 2. Get users older than 25
$isOlderThan25 = compose(rpartial(gt, 25), propertyGetter('age'));
$olderThan25 = filter($isOlderThan25, $users);

echo "These users are older than 25:\n";
foreach ($olderThan25 as $user) {
    echo sprintf("    %s - %s y.o.\n", $user->name, $user->age);
}


// 3. Memoizing heavy calculations
$factorial = function($n) {
    echo "Calculating $n!\n";
    return reduce(mul, range(1, $n), 1);
};

$memoizedFactorial = memoized($factorial);

foreach ([3, 3, 5, 5, 5] as $n) {
    echo sprintf("%s! = %s\n", $n, $memoizedFactorial($n));
}

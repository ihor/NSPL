<?php

require_once __DIR__ . '/../autoload.php';

use nspl\f;
use function nspl\f\map;
use function nspl\f\reduce;
use function nspl\f\filter;
use function nspl\f\rpartial;
use function nspl\f\compose;
use function nspl\f\memoized;

use nspl\op;
use function nspl\op\propertyGetter;


$users = map(op::$object, [
    array('id' => 1, 'name' => 'John', 'age' => 15),
    array('id' => 2, 'name' => 'Jack', 'age' => 35),
    array('id' => 3, 'name' => 'Sarah', 'age' => 25),
    array('id' => 4, 'name' => 'Norah', 'age' => 20),
    array('id' => 5, 'name' => 'Michael', 'age' => 30),
    array('id' => 6, 'name' => 'Bob', 'age' => 30),
]);

// 1. Get user ids
$userIds = map(propertyGetter('id'), $users);

echo sprintf("User ids are: %s\n", implode(', ', $userIds));


// 2. Count users younger than 25
$youngerThan25Count = reduce(function($count, $user) { return $count + (int) ($user->age < 25); }, $users);

echo sprintf("%s users are younger than 25\n", $youngerThan25Count);


// 3. Get users younger than 25
$youngerThan25 = filter(function($user) { return $user->age < 25; }, $users);

echo "These users are younger than 25:\n";
foreach ($youngerThan25 as $user) {
    echo sprintf("    %s - %s y.o.\n", $user->name, $user->age);
}


// 4. Get users older than 25
$isOlderThan25 = compose(rpartial(op::$gt, 25), propertyGetter('age'));
$olderThan25 = filter($isOlderThan25, $users);

echo "These users are older than 25:\n";
foreach ($olderThan25 as $user) {
    echo sprintf("    %s - %s y.o.\n", $user->name, $user->age);
}


// 5. Memoizing heavy calculations
$factorial = function($n) {
    echo "Calculating $n!\n";
    return reduce(op::$mul, range(1, $n), 1);
};

$memoizedFactorial = memoized($factorial);

foreach ([3, 3, 5, 5, 5] as $n) {
    echo sprintf("%s! = %s\n", $n, $memoizedFactorial($n));
}

<?php

require_once __DIR__ . '/../autoload.php';

use nspl\a;
use function nspl\a\all;
use function nspl\a\any;
use function nspl\a\getByKey;
use function nspl\a\sorted;
use function nspl\a\indexed;
use function nspl\a\take;
use function nspl\a\moveElement;

use nspl\op;
use function nspl\op\itemGetter;

use function nspl\f\map;
use function nspl\f\reduce;
use function nspl\f\flipped;
use function nspl\f\partial;


// 1. Check all statuses are "ready"
$statuses = ['ready', 'ready', 'not-ready'];
$ready = all($statuses, partial(op::$eq, 'ready'));

echo $ready ? "Everybody is ready\n" : "Someone is not ready\n";


// 2. Check at least someone is "ready"
$someoneIsReady = any($statuses, partial(op::$eq, 'ready'));

echo $someoneIsReady ? "Someone is ready\n" : "Everybody is not ready\n";


// 3. Get user name from which can be stored as username, user_name or name in data array
$data = array('id' => 1337, 'name' => 'John', 'gender' => 'male');
$name = reduce(flipped(partial(a::$getByKey, $data)), ['username', 'user_name', 'name'], '');

echo sprintf("User name is %s\n", $name);


// 4. Sort list of user objects by their name
$users = map(op::$object, [
    array('id' => 1, 'name' => 'John', 'age' => 15),
    array('id' => 2, 'name' => 'Jack', 'age' => 35),
    array('id' => 3, 'name' => 'Sarah', 'age' => 25),
    array('id' => 4, 'name' => 'Norah', 'age' => 20),
    array('id' => 5, 'name' => 'Michael', 'age' => 30),
]);

$usersSortedByName = sorted($users, false, op::propertyGetter('name'));
echo "Users sorted by name:\n";
foreach ($usersSortedByName as $user) {
    echo sprintf("    %s\n", $user->name);
}


// 5. Index users by ids
$usersIndexedByIds = indexed($users, op::propertyGetter('id'));

echo "Users indexed by id:\n";
foreach ($usersIndexedByIds as $id => $user) {
    echo sprintf("    %s. %s\n", $id, $user->name);
}


// 6. Create a map (name => age) from users data
$usersAgeByName = indexed($users, op::propertyGetter('name'), true, op::propertyGetter('age'));

echo "Users age:\n";
foreach ($usersAgeByName as $name => $age) {
    echo sprintf("    %s is %s y.o.\n", $name, $age);
}


// 7. Get all numbers less than 20 which are divisible by 3
$numbers = take(range(3, 20), 20, 3);

echo sprintf("Numbers less than 20 which are divisible by 3: %s\n", implode(', ', $numbers));


// 8. Re-order pets rating
$petsRating = moveElement(['dog', 'hamster', 'cat'], 2, 1);

echo "New pets rating:\n";
foreach ($petsRating as $pet) {
    echo sprintf("    %s\n", $pet);
}

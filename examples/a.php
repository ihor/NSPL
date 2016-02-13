<?php

require_once __DIR__ . '/../autoload.php';

use const nspl\a\getByKey;
use function nspl\a\all;
use function nspl\a\any;
use function nspl\a\map;
use function nspl\a\reduce;
use function nspl\a\filter;
use function nspl\a\getByKey;
use function nspl\a\sorted;
use function nspl\a\keySorted;
use function nspl\a\indexed;
use function nspl\a\take;
use function nspl\a\moveElement;

use const nspl\op\eq;
use const nspl\op\object;
use function nspl\op\itemGetter;
use function nspl\op\propertyGetter;

use function nspl\f\partial;

$users = map(object, [
    array('id' => 1, 'name' => 'John', 'age' => 15),
    array('id' => 2, 'name' => 'Jack', 'age' => 35),
    array('id' => 3, 'name' => 'Sarah', 'age' => 25),
    array('id' => 4, 'name' => 'Norah', 'age' => 20),
    array('id' => 5, 'name' => 'Michael', 'age' => 30),
    array('id' => 6, 'name' => 'Bob', 'age' => 30),
]);

// 1. Check all statuses are "ready"
$statuses = ['ready', 'ready', 'not-ready'];
$ready = all($statuses, partial(eq, 'ready'));

echo $ready ? "Everybody is ready\n" : "Someone is not ready\n";


// 2. Check at least someone is "ready"
$someoneIsReady = any($statuses, partial(eq, 'ready'));

echo $someoneIsReady ? "Someone is ready\n" : "Everybody is not ready\n";


// 3. Get user ids
$userIds = map(propertyGetter('id'), $users);

echo sprintf("User ids are: %s\n", implode(', ', $userIds));


// 4. Count users younger than 25
$youngerThan25Count = reduce(function($count, $user) { return $count + (int) ($user->age < 25); }, $users);

echo sprintf("%s users are younger than 25\n", $youngerThan25Count);


// 5. Get users younger than 25
$youngerThan25 = filter(function($user) { return $user->age < 25; }, $users);

echo "These users are younger than 25:\n";
foreach ($youngerThan25 as $user) {
    echo sprintf("    %s - %s y.o.\n", $user->name, $user->age);
}

// 6. Sort list of user objects by their name
$usersSortedByName = sorted($users, false, propertyGetter('name'));
echo "Users sorted by name:\n";
foreach ($usersSortedByName as $user) {
    echo sprintf("    %s\n", $user->name);
}


// 7. Index users by ids
$usersIndexedByIds = indexed($users, propertyGetter('id'));
// In case of array it would be indexed($users, 'id')

echo "Users indexed by id:\n";
foreach ($usersIndexedByIds as $id => $user) {
    echo sprintf("    %s. %s\n", $id, $user->name);
}


// 8. Create a map (name => age) from users data
$usersAgeByName = indexed($users, propertyGetter('name'), true, propertyGetter('age'));

echo "Users age:\n";
foreach ($usersAgeByName as $name => $age) {
    echo sprintf("    %s is %s y.o.\n", $name, $age);
}


// 9. Get users with unique age (unique values in multidimensional array)
$usersWithUniqueAge = array_values(indexed($users, propertyGetter('age')));

echo "Users with unique age:\n";
foreach ($usersWithUniqueAge as $user) {
    echo sprintf("    %s is %s y.o.\n", $user->name, $user->age);
}


// 10. Group users by age range
$usersByAgeRange = keySorted(indexed($users, function($user) { return floor($user->age / 10) * 10; }, false));

echo "Users by age range:\n";
foreach ($usersByAgeRange as $age => $usersGroup) {
    echo sprintf("    %s-%s: %s\n", $age, $age + 9, implode(', ', map(propertyGetter('name'), $usersGroup)));
}


// 11. Get all numbers less than 20 which are divisible by 3
$numbers = take(range(3, 20), 20, 3);

echo sprintf("Numbers less than 20 which are divisible by 3: %s\n", implode(', ', $numbers));


// 12. Re-order pets rating
$petsRating = moveElement(['dog', 'hamster', 'cat'], 2, 1);

echo "New pets rating:\n";
foreach ($petsRating as $pet) {
    echo sprintf("    %s\n", $pet);
}

<?php

require_once __DIR__ . '/../autoload.php';

use const nspl\f\apply;
use const nspl\op\gt;
use const nspl\op\object;
use function nspl\a\all;
use function nspl\a\map;
use function nspl\a\sorted;
use function nspl\a\zip;
use function nspl\f\partial;
use function nspl\f\rpartial;
use function nspl\op\instanceCreator;
use function nspl\op\itemGetter;


class User {

	private $name;
	private $age;

	public function __construct($name, $age=0) {
		$this->name = $name;
		$this->age = $age;
	}
}

$users = [
    array('name' => 'John', 'age' => 15),
    array('name' => 'Jack', 'age' => 35),
    array('name' => 'Sarah', 'age' => 25),
    array('name' => 'Norah', 'age' => 20),
    array('name' => 'Michael', 'age' => 30),
];


// 1. Get user names from list of users presented with array data
$names = map(itemGetter('name'), $users);
$ages = map(itemGetter('age'), $users);

echo sprintf("User names are: %s (users were presented with array data)\n", implode(', ', $names));


// 2. Convert list of user presented with array data to list of objects
$objects = map(object, $users);

echo sprintf("List of users converted to objects consists of types: %s\n", implode(', ', map(\nspl\getType, $objects)));

$userClassObjectsWithName = map(instanceCreator(User::class), $names);

echo sprintf("List of user class instances with name consists of types: %s\n", implode(', ', map(\nspl\getType, $userClassObjectsWithName)));

$userClassObjectsWithNameAndAge = map(partial(apply, instanceCreator(User::class)), zip($names, $ages));

echo sprintf("List of user class instances name and age consists of types: %s\n", implode(', ', map(\nspl\getType, $userClassObjectsWithNameAndAge)));

// 3. Sort users by age
$sorted = sorted($users, false, itemGetter('age'));

echo "Users sorted by age:\n";
foreach ($sorted as $user) {
    echo sprintf("    %s - %s y.o.\n", $user['name'], $user['age']);
}


// 4. Check if all numbers are positive
$allPositive = all([1, 2, 3, 4, 5], rpartial(gt, 0));

echo $allPositive ? "All numbers are positive\n" : "At least one number was not positive\n";

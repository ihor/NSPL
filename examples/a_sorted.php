<?php

require_once __DIR__ . '/../autoload.php';

use function nspl\a\sorted;

use function nspl\op\itemGetter;
use function nspl\op\propertyGetter;
use function nspl\op\methodCaller;


// 1. Sort array
$sorted = sorted([3, 1, 2]);
display('1. Sorted array', $sorted);

// 2. Sort array in descending order
$sortedDesc = sorted([3, 1, 2], true);
display('2. Sorted in descending order', $sortedDesc);

// 3. Sort array by the result of a given function (order words by length)
$sortedByLength = sorted(['bc', 'a', 'abc'], 'strlen');
display('3.1. Sorted by length', $sortedByLength);

$sortedByLengthDesc = sorted(['bc', 'a', 'abc'], true, 'strlen');
display('3.2. Sorted by length in descending order', $sortedByLengthDesc);

// 4. Sort array by the result of user-defined function (order word by the 1st character)
$sortedByTheFirstCharacter = sorted(['bc', 'a', 'abc'], function($v) { return $v[0]; });
display('4. Sorted by the 1st character', $sortedByTheFirstCharacter);

// 5. Which is the same as
$sortedByTheFirstCharacter = sorted(['bc', 'a', 'abc'], itemGetter(0));
display('5.1. Sorted by the 1st character with nspl\op\itemGetter', $sortedByTheFirstCharacter);

$sortedByTheFirstCharacterDesc = sorted(['bc', 'a', 'abc'], true, itemGetter(0));
display('5.2. Sorted by the 1st character with nspl\op\itemGetter in descending order', $sortedByTheFirstCharacterDesc);

// 6. Sort array with comparison function (order word by the 1st character)
$sortedByTheFirstCharacter = sorted(['bc', 'a', 'abc'], false, null, function($v1, $v2) {
    return chr($v1[0]) - chr($v2[0]);
});
display('6. Sorted by the 1st character with a comparison function', $sortedByTheFirstCharacter);

// 7. Sort list of strings lexicographically
$sortedLexicographically = sorted(['bc', 'a', 'abc'], false, null, 'strcmp');
display('7. Sorted lexicographically', $sortedLexicographically);

// 8. Sort multidimensional array (sort list of users by their names)
$users = [
    array('name' => 'Robert', 'age' => 20),
    array('name' => 'Alex', 'age' => 30),
    array('name' => 'Jack', 'age' => 25),
];
$sortedByName = sorted($users, itemGetter('name'));
display('8.1. Users sorted by name', $sortedByName);

$sortedByNameDesc = sorted($users, true, itemGetter('name'));
display('8.2. Users sorted by name in descending order', $sortedByNameDesc);

// 9. Sort list of objects by property value (sort list of users by their name)
$users = [
    new User('Robert', 20),
    new User('Alex', 30),
    new User('Jack', 25),
];
$sortedByName = sorted($users, propertyGetter('name'));
display('9.1. Users presented as list of objects sorted by name', $sortedByName);

$sortedByNameDesc = sorted($users, true, propertyGetter('name'));
display('9.2. Users presented as list of objects sorted by name in descending order', $sortedByNameDesc);

// 10. Sort list of objects by method result (sort list of users by their age)
$sortedByAge = sorted($users, methodCaller('getAge'));
display('10.1. Users presented as list of objects sorted by age', $sortedByAge);

$sortedByAgeDesc = sorted($users, true, methodCaller('getAge'));
display('10.2. Users presented as list of objects sorted by age in descending order', $sortedByAgeDesc);


class User
{
    public $name;
    public $age;

    public function __construct($name, $age)
    {
        $this->name = $name;
        $this->age = $age;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAge()
    {
        return $this->age;
    }

    public function __toString()
    {
        return $this->name . ', ' . $this->age . 'y.o.';
    }

}

function display($title, array $array)
{
    echo $title . ":\n";
    foreach ($array as $item) {
        if (is_array($item)) {
            $item = json_encode($item);
        }

        echo ' - ' . $item . "\n";
    }
    echo "\n";
}

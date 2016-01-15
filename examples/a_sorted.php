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

// 3. Sort array with a given function (order words by length)
$sortedByLength = sorted(['bc', 'a', 'abc'], false, 'strlen');
display('3. Sorted by length', $sortedByLength);

// 4. Sort array with user-defined function (order word by the 1st character)
$sortedByTheFirstCharacter = sorted(['bc', 'a', 'abc'], false, null, function($v1, $v2) {
    return chr($v1[0]) - chr($v2[0]);
});
display('4. Sorted by the 1st character', $sortedByTheFirstCharacter);

// 5. Which is the same as
$sortedByTheFirstCharacter = sorted(['bc', 'a', 'abc'], false, itemGetter(0));
display('5. Sorted by the 1st character with nspl\op\itemGetter', $sortedByTheFirstCharacter);

// 6. Sort multidimensional array (sort list of users by their names)
$users = [
    array('name' => 'Robert', 'age' => 20),
    array('name' => 'Alex', 'age' => 30),
    array('name' => 'Jack', 'age' => 25),
];
$sortedByName = sorted($users, false, itemGetter('name'));
display('6. Users sorted by name', $sortedByName);

// 7. Sort list of objects by property value (sort list of users by their name)
$users = [
    new User('Robert', 20),
    new User('Alex', 30),
    new User('Jack', 25),
];
$sortedByName = sorted($users, false, propertyGetter('name'));
display('7. Users presented as list of objects sorted by name', $sortedByName);

// 8. Sort list of objects by property value (sort list of users by their name)
$sortedByAge = sorted($users, false, methodCaller('getAge'));
display('8. Users presented as list of objects sorted by age', $sortedByAge);

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

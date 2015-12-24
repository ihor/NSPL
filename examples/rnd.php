<?php

require_once __DIR__ . '/../autoload.php';

use function \nspl\rnd\choice;
use function \nspl\rnd\weightedChoice;
use function \nspl\rnd\sample;

use function \nspl\a\pairs;


// 1. Get random array element
$random = choice([1, 2, 3]);

echo sprintf("Random number is %s\n", $random);


// 2. Get 3 random numbers between 1 and 1000
$numbers = sample(range(1, 1000), 3);

echo sprintf("Three random numbers between 1 and 1000 are: %s\n", implode(', ', $numbers));


// 3. Get lottery winner, changes are proportional to number of tickets bought
// When data is presented in pairs [user_name, tickets_number]
$winner = weightedChoice([
    ['Jack', 1],
    ['John', 3],
    ['Tom', 2],
]);

echo sprintf("Lottery winner is %s (data was presented in pairs)\n", $winner);

// When data is presented in a dictionary array(user_name => tickets_number)
$winner = weightedChoice(pairs(array(
    'Jack' => 1,
    'John' => 3,
    'Tom' => 2,
)));

echo sprintf("Lottery winner is %s (data was presented as a dictionary)\n", $winner);

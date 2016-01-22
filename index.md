Non-standard PHP library (NSPL)
===============================
Non-standard PHP Library (NSPL) is a collection of modules that are meant to solve common day to day routine problems:

 - [nspl\f](#nsplf) - provides the most popular higher-order functions: functions that act on or return other functions. Helps to write code with functional programming paradigm
 - [nspl\op](#nsplop) - provides functions that perform standard PHP operations and can be passed as callbacks to higher-order functions. Mimics Python's [operator](https://docs.python.org/2/library/operator.html) module 
 - [nspl\a](#nspla) - provides missing array functions
 - [nspl\ds](#nsplds) - provides non-standard data structures and methods to work with them
 - [nspl\rnd](#nsplrnd) - helps to pick random elements from sequences of data
 - [nspl\args](#nsplargs) - provides possibility to validate function arguments including types, mixed types, combining primitive types with user-defined types and custom validation rules

NSPL aims to provide compact but clear syntax to make functional PHP code look less verbose. Fast and simple, it is created to be used every day instead of being another functional programming playground for geeks. Look at the following code written with NSPL:
```php
// get user ids
$userIds = map(propertyGetter('id'), $users);

// or sort them by age
$sortedByAge = sorted($users, methodCaller('getAge'));

// or check if they all are online
$online = all($users, methodCaller('isOnline'));

// or define new function as composition of the existing ones
$flatMap = compose(rpartial(flatten, 1), map);
```

In pure PHP it would look like this:
```php
// get user ids
$userIds = array_map(function($user) { return $user->id; }, $users);

// sort them by age, note that the following code modifies the original users array
usort($users, function($user1, $user2) {
    return $user1->getAge() - $user2->getAge();
});

// check if they all are online
$online = true;
foreach ($users as $user) {
    if (!$user->isOnline()) {
        $online = false;
        break;
    }
}

// define new function as composition of the existing ones
$flatMap = function($function, $list) {
    // note the inconsistency in array_map and array_reduce parameters
    return array_reduce(array_map($function, $list), 'array_merge', []);
};
```
You can see more examples in the [library reference](#reference) below or [here](https://github.com/ihor/Nspl/blob/master/examples).

Installation
------------
#### Using [composer](https://getcomposer.org)
Define the following requirement in your composer.json file:
```
"require": {
    "ihor/nspl": "~1.0"
}
```
or simply execute the following in the command line:
```
composer require ihor/nspl
```

#### Manually
Checkout the code and include ```autoload.php```:
```php
include 'path/to/nspl/autoload.php';
```

Reference
=========
Here I assume that described functions are imported with [use function](http://php.net/manual/en/language.namespaces.importing.php):
```php
use function nspl\a\zip;
$pairs = zip([1, 2, 3], ['a', 'b', 'c']);
```
If your PHP version is less than 5.6 you should import parent namespace and use functions with the namespace prefix:
```php
use nspl\a;
$pairs = a\zip([1, 2, 3], ['a', 'b', 'c']);
```

## nspl\f

Provides the most popular higher-order functions: functions that act on or return other functions. Helps to write code in functional programming paradigm.


##### map($function, $sequence)

Applies function of one argument to each sequence item.
```php
assert(['A', 'B', 'C'] === map('strtoupper', ['a', 'b', 'c']));
```

##### reduce($function, $sequence, $initial = 0)

Applies function of two arguments cumulatively to the items of sequence, from left to right to reduce the sequence to a single value.
```php
assert(6 === reduce(function($a, $b) { return $a + $b; }, [1, 2, 3]));
```

##### filter($function, $sequence)

Returns list items that satisfy the predicate
```php
assert([1, 2, 3] === filter('is_numeric', ['a', 1, 'b', 2, 'c', 3]));
```

##### apply($function, array $args = [])

Applies given function to arguments and returns the result
```php
assert([1, 3, 5, 7, 9] === apply('range', [1, 10, 2]));
```

##### flipped($function)

Returns function which accepts arguments in the reversed order

##### partial($function, $arg1)

Returns new partial function which will behave like ```$function``` with predefined *left* arguments passed to partial
```php
$sum = function($a, $b) { return $a + $b; };
$inc = partial($sum, 1);
```

##### rpartial($function, $arg1)

Returns new partial function which will behave like ```$function``` with predefined *right* arguments passed to rpartial
```php
$cube = rpartial('pow', 3);
```

##### ppartial($function, array $args)

Returns new partial function which will behave like ```$function``` with predefined *positional* arguments passed to ppartial
```php
$oddNumbers = ppartial('range', array(0 => 1, 2 => 2));
```

##### memoized($function)

Returns memoized ```$function``` which returns the cached result when the same inputs occur again
```php
$f = function($arg) {
    echo sprintf("Performing heavy calculations with '%s'\n", $arg);
    return $arg;
};

$memoized = memoized($f);
echo $memoized('Hello world!') . "\n";
echo $memoized('Hello world!') . "\n";
```
which outputs
```
Performing heavy calculations with 'Hello world!'
Hello world!
Hello world!
```

##### compose($f, $g)

Returns new function which applies each given function to the result of another from right to left
```compose(f, g, h)``` is the same as ```f(g(h(x)))```
```php
use const \nspl\a\flatten;
use const \nspl\f\map;
use function \nspl\f\compose;
use function \nspl\f\partial;
use function \nspl\f\rpartial;

$flatMap = compose(rpartial(flatten, 1), map);
assert(['hello', 'world', 'foo', 'bar'] === $flatMap(partial('explode', ' '), ['hello world', 'foo bar']));
```

##### pipe($input, $function1, $function2)

Passes ```$input``` to composition of functions (functions have to be in the reversed order)
```php
use const \nspl\op\sum;
use const \nspl\f\filter;
use const \nspl\f\map;
use const \nspl\f\reduce;
use function \nspl\f\partial;

// sum of squares of all even numbers less than 20
$sum = pipe(
    range(1, 20),
    partial(filter, function($x) { return $x % 2 === 0; }),
    partial(map, function($x) { return $x * $x; }),
    partial(reduce, sum)
);
```

##### I($input, $function1, $function2)

Alias for the pipe

##### curried($function, $withOptionalArgs = false)

Returns a [curried](https://en.wikipedia.org/wiki/Currying) version of the function. If you are going to curry a function which reads args with ```func_get_args()``` then pass a number of args as the 2nd argument.

If the second argument is true then curry function with optional args otherwise curry it only with required args. Or you can pass the exact number of args you want to curry.

##### uncurried($function)

Returns normal (uncurried) version of a [curried function](https://en.wikipedia.org/wiki/Currying)

##### Callbacks

```nspl\f``` provides all these functions as callbacks in its constants which have the same names as the functions.
```php
use const \nspl\f\map;
use const \nspl\f\filter;

$incListItems = partial(map, function($v) { return $v + 1; });
$filterNumbers = partial(filter, 'is_numeric');
```

Check ```\nspl\f``` examples [here](https://github.com/ihor/Nspl/blob/master/examples/f.php).


## nspl\op

Class ```nspl\op``` provides functions that perform standard PHP operations and can be passed as callbacks to higher-order functions. Mimics Python's [operator](https://docs.python.org/2/library/operator.html) module. For example:


```php
use const nspl\op\sum;
use function nspl\f\reduce;

assert(6 === reduce(sum, [1, 2, 3]));
```

Function | Operation
---------|-----------------------------------------------
sum      | +
sub      | -
mul      | *
div      | /
mod      | %
inc      | ++
dec      | --
neg      | -
band     | &
bxor     | ^
bor      | &#124;
bnot     | ~
lshift   | <<
rshift   | >>
lt       | <
le       | <=
eq       | ==
idnt     | ===
ne       | !=
nidnt    | !==
ge       | >
gt       | >=
and_     | &&
or_      | &#124;&#124;
xor_     | xor
not      | !
concat   | .
int      | (int)
bool     | (bool)
float    | (float)
str      | (string)
array_   | (array)
object   | (object)

##### itemGetter($key)
Returns a function that returns key value for a given array

```php
use function nspl\op\itemGetter;
use function nspl\f\map;

assert([2, 5, 8] === map(itemGetter(1), [[1, 2, 3], [4, 5, 6], [7, 8, 9]]));
```

##### propertyGetter($property)
Returns a function that returns property value for a given object

```php
$userIds = map(propertyGetter('id'), $users);
```

##### methodCaller($method, array $args = array())
Returns a function that returns method result for a given object on predefined arguments

```php
$userIds = map(methodCaller('getId'), $users);
```

Check ```\nspl\op``` examples [here](https://github.com/ihor/Nspl/blob/master/examples/op.php).

## nspl\a

Provides missing array functions

##### all($sequence, $predicate)

Returns true if all elements of the ```$sequence``` satisfy the predicate are true (or if the ```$sequence``` is empty). If predicate was not passed return true if all elements of the ```$sequence``` are true.

```php
assert(true === all([true, true, true]));
```

##### any($sequence, $predicate)

Returns true if any element of the ```$sequence``` satisfies the predicate. If predicate was not passed returns true if any element of the ```$sequence``` is true. If the ```$sequence``` is empty, returns false.

```php
assert(true === any([true, false, false]));
```

##### getByKey($array, $key, $default = null)

Returns array value by key if it exists otherwise returns the default value
```php
$data = array('a' => 1, 'b' => 2, 'c' => 3);
assert(2 === getByKey($data, 'b', -1));
assert(-1 === getByKey($data, 'd', -1));
```

##### extend(array $list1, array $list2)

Adds ```$list2``` items to the end of ```$list1```
```php
assert([1, 2, 3, 4, 5, 6] === extend([1, 2, 3], [4, 5, 6]));
```

##### zip(array $list1, array $list2)

Zips two or more lists
```php
assert([[1, 'a'], [2, 'b'], [3, 'c']] === zip([1, 2, 3], ['a', 'b', 'c']));
```

##### flatten(array $list, $depth = null)

Flattens multidimensional list
```php
assert([1, 2, 3, 4, 5, 6, 7, 8, 9] === flatten([[1, [2, [3]]], [[[4, 5, 6]]], 7, 8, [9]]));
assert([1, 2, [3], [4, 5, 6], 7, 8, 9] === flatten([[1, [2, [3]]], [[[4, 5, 6]]], 7, 8, [9]], 2));
```

##### sorted(array $array, $descending = false, $key = null, $cmp = null)

Returns sorted copy of the passed array

```$descending``` if true then sort array in descending order. If not boolean and ```$key``` was not passed then acts as a ```$key``` parameter  
```$key``` is a function of one argument that is used to extract a comparison key from each element  
```$cmp``` is a function of two arguments which returns a negative number, zero or positive number depending on whether the first argument is smaller than, equal to, or larger than the second argument
```php
assert([1, 2, 3] === sorted([2, 3, 1]));
assert(['a', 'b', 'c'] === sorted(['c', 'a', 'b'], true));

$usersSortedByName = sorted($users, function($u) { return $u->getName(); });
// Which is the same as
use function nspl\op\methodCaller;
$usersSortedByName = sorted($users, methodCaller('getName'));
```

Check more ```\nspl\a\sorted``` examples [here](https://github.com/ihor/Nspl/blob/master/examples/a_sorted.php).

##### keySorted(array $array, $reversed = false)

Returns copy of passed array sorted by keys
```php
assert(array('a' => 1, 'b' => 2, 'c' => 3) === keySorted(array('b' => 2, 'c' => 3, 'a' => 1));
```

##### indexed(array $list, $by, $keepLast = true, $transform = null)

Returns indexed list of items

```$by``` is an array key or a function  
If ```$keepLast``` is true only the last item with the key will be returned otherwise list of items which share the same key value will be returned  
```$transform``` is a function that transforms list item after indexing

```php
$indexedById = indexed([
    array('id' => 1, 'name' => 'John'),
    array('id' => 2, 'name' => 'Kate'),
    array('id' => 3, 'name' => 'Robert'),
], 'id');
```

##### pairs(array $array, $valueKey = false)

Returns list of (key, value) pairs. If ```$valueKey``` is true then convert array to (value, key) pairs.
```php
assert([['a', 'hello'], ['b', 'world'], ['c', 42]] === pairs(array('a' => 'hello', 'b' => 'world', 'c' => 42)));
```

##### take(array $list, $N, $step = 1)

Returns first N list items
```php
assert([1, 3, 5] === take([1, 2, 3, 4, 5, 6, 7, 8, 9], 3, 2));
```

##### first(array $list)

Returns the first list item
```php
assert(1 === first([1, 2, 3, 4, 5, 6, 7, 8, 9]));
```

##### drop(array $list, $N)

Drops first N list items
```php
assert([7, 8, 9] === drop([1, 2, 3, 4, 5, 6, 7, 8, 9], 6));
```

##### last(array $list)

Returns the last list item
```php
assert(9 === last([1, 2, 3, 4, 5, 6, 7, 8, 9]));
```

##### moveElement(array $list, $from, $to)

Moves list element to another position
```php
assert([2, 0, 1] === moveElement([0, 1, 2], 2, 0)); // move element from the 2nd position to the begining of the list
```

##### Callbacks

```nspl\a``` provides all these functions as callbacks in its constants which have the same names as the functions.
```php
use const \nspl\a\first;
assert([1, 2, 3] === map(first, [[1, 'a'], [2, 'b'], [3, 'c']]));
```

Check ```\nspl\a``` examples [here](https://github.com/ihor/Nspl/blob/master/examples/a.php).


## nspl\ds

Provides non-standard data structures and methods to work with them


##### getType($var)

Returns the variable type or its class name if it is an object

##### isList($var)

Returns true if the variable is a list

##### traversableToArray($var)

Takes array or traversable and returns an array

##### ArrayObject

Alternative ArrayObject implementation

##### arrayobject()

Returns new ArrayObject

##### DefaultArray

Array with a default value for missing keys. If you pass a function as default value it will be called without arguments to provide a default value for the given key, this value will be inserted in the array for the key, and returned.
Using DefaultArray turns this code:
```php
$a = array();
foreach([1, 2, 1, 1, 3, 3, 3] as $v) {
    if (!isset($a[$v])) {
        $a[$v] = 0;
    }
    ++$a[$v];
}
```
into this:
```php
$a = new DefaultArray(0);
foreach([1, 2, 1, 1, 3, 3, 3] as $v) {
    ++$a[$v];
}
```

##### defaultarray($default, $data = array())

Returns new DefaultArray

Check ```\nspl\ds``` examples [here](https://github.com/ihor/Nspl/blob/master/examples/ds.php).

## nspl\rnd

Helps to pick random elements from sequences of data


##### choice(array $sequence)

Returns a random element from a non-empty sequence

##### weightedChoice(array $weightPairs)

Returns a random element from a non-empty sequence of items with associated weights presented as pairs (item, weight)

```php
use function \nspl\rnd\weightedChoice;
use function \nspl\a\pairs;

$nextPet = weightedChoice([['cat', 20], ['hamster', 30], ['dog', 50]]);
$nextFavouriteColor = weightedChoice(pairs(array(
    'red' => 0.2,
    'green' => 0.3,
    'blue' => 0.5,
)));
```

##### sample(array $population, $length, $preserveKeys = false)

Returns a k length list of unique elements chosen from the population sequence

Check ```\nspl\rnd``` examples [here](https://github.com/ihor/Nspl/blob/master/examples/rnd.php).

## nspl\args

Provides possibility to validate function arguments including types, mixed types, combining primitive types with user-defined types and custom validation rules

```nspl\args``` contains the following functions which take the same parameters:  
##### expects*($arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')

Checks that value is boolean otherwise throws the corresponding exception  

If ```$atPosition``` is null then position is calculated automatically comparing given argument to the actual arguments passed to the function  
```$otherwiseThrow``` defines exception which will be thrown if given argument is invalid, it can be exception class or exception object  

- ```expectsNotEmpty()``` - checks that argument is not empty otherwise throws the corresponding exception
- ```expectsBool()``` - checks that argument is boolean otherwise throws the corresponding exception
- ```expectsInt()``` - checks that argument is an integer otherwise throws the corresponding exception
- ```expectsFloat()``` - checks that argument is a float otherwise throws the corresponding exception
- ```expectsNumeric()``` - checks that argument is numeric otherwise throws the corresponding exception
- ```expectsString()``` - checks that argument is a string otherwise throws the corresponding exception
- ```expectsArrayKey()``` - checks that argument can be an array key otherwise throws the corresponding exception
- ```expectsTraversable()``` - checks that argument is an array or traversable otherwise throws the corresponding exception
- ```expectsArrayAccess()``` - checks that argument is an array or implements array access otherwise throws the corresponding exception
- ```expectsArrayAccessOrString()``` - checks that argument implements array access or is a string otherwise throws the corresponding exception
- ```expectsCallable()``` - checks that argument is callable otherwise throws the corresponding exception
- ```expectsArrayKeyOrCallable()``` - checks that argument can be an array key or is callable otherwise throws the corresponding exception

```php
use function \nspl\args\expectsNumeric;

function sqr($x)
{
    expectsNumeric($x);
    return $x * $x;
}

sqr('hello world');
```

Outputs:
```
InvalidArgumentException: Argument 1 passed to sqr() must be numeric, string given in /path/to/example.php on line 17

Call Stack:
    0.0002     230304   1. {main}() /path/to/example.php:0
    0.0023     556800   2. sqr() /path/to/example.php:17
```

```nspl\args``` also contains the following functions which have similar signature:

##### expectsWithMethod($object, $method, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')

Checks that object has the required method. Is useful when you use duck-typing instead of interfaces

##### expectsWithMethods($object, array $methods, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')

Checks that object has the required methods. Is useful when you use duck-typing instead of interfaces

##### expectsWithKeys(array $array, array $keys, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')

Checks that array has the required keys

```php
use function \nspl\args\expectsWithMethods;

class Service
{
    // ...
    public function setCache($cache)
    {
        expectsWithMethods($cache, ['set', 'get']);
        $this->cache = $cache;
    }
    // ....
}
```

##### expects($arg, $hasTo, callable $satisfy, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')

Checks that argument satisfies requirements otherwise throws the corresponding exception  

```$hasTo``` is a message which tells what the argument is expected to be. It will be used in the exceptions if the argument is invalid and also provides description for other developers  
```$satisfy``` is a function which returns true if argument satisfies the requirements otherwise it returns false  
```php
use function \nspl\args\expects;

function calculateAge($yearOfBirth)
{
    expects($yearOfBirth, 'to be an integer > 1900 and < current year', function($arg) {
        return is_int($arg) && $arg > 1900 && $arg < (int) date('Y');
    });

    return (int) date('Y') - $yearOfBirth;
}

$age = calculateAge(1800);
```

Outputs:
```
InvalidArgumentException: Argument 1 passed to calculateAge() has to be an integer > 1900 and < current year, 1800 given in /path/to/example.php on line 35

Call Stack:
    0.0002     230704   1. {main}() /path/to/example.php:0
    0.0025     561328   2. calculateAge() /path/to/example.php:35
```

Roadmap
=======

- Create a roadmap

Feedback
========

There are no mailing lists or discussion groups yet. Please use GitHub issues and pull request or follow me on Twitter [@IhorBurlachenko](https://twitter.com/IhorBurlachenko)
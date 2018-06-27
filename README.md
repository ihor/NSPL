Non-standard PHP library (NSPL)
===============================
Non-standard PHP Library (NSPL) is a collection of modules that are meant to solve common day to day routine problems:

 - [nspl\f](#nsplf) - provides functions that act on other functions. Helps to write code in functional programming paradigm
 - [nspl\op](#nsplop) - provides functions that perform standard PHP operations and can be passed as callbacks to higher-order functions. Mimics Python's [operator](https://docs.python.org/2/library/operator.html) module
 - [nspl\a](#nspla) - provides missing array functions which also can be applied to traversable sequences
 - [nspl\a\lazy](#nsplalazy) - provides lazy versions of functions from ```\nspl\a```
 - [nspl\args](#nsplargs) - helps to validate function arguments
 - [nspl\ds](#nsplds) - provides non-standard data structures and methods to work with them
 - [nspl\rnd](#nsplrnd) - helps to pick random items from sequences of data

NSPL aims to make code compact and less verbose but still clear and readable. Look at the following example:
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
    "ihor/nspl": "~1.2"
}
```
or simply execute the following in the command line:
```
composer require ihor/nspl
```
For the latest changes require version ```1.3.*-dev```

#### Manually
Checkout [the code](https://github.com/ihor/Nspl) and include ```autoload.php```:
```php
include 'path/to/nspl/autoload.php';
```

Reference
=========
This is documentation for the dev version ```1.3.*-dev``` which contains the latest changes. For the version ```1.2``` (last stable version) documentation click [here](https://github.com/ihor/Nspl/tree/1.2#reference).

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

## Table of contents

* [nspl\f](#nsplf)
    * [id](#idvalue)
    * [apply](#applyfunction-array-args--)
    * [partial](#partialfunction-arg1)
    * [rpartial](#rpartialfunction-arg1)
    * [ppartial](#ppartialfunction-array-args)
    * [flipped](#flippedfunction)
    * [compose](#composef-g)
    * [pipe](#pipeinput-function1-function2)
    * [curried](#curriedfunction-withoptionalargs--false)
    * [uncurried](#uncurriedfunction)
    * [memoized](#memoizedfunction)
    * [throttled](#throttledfunction-wait))
    * [Callbacks](#callbacks)
* [nspl\op](#nsplop)
    * [Callbacks](#callbacks-1)
    * [itemGetter](#itemgetterkey)
    * [propertyGetter](#propertygetterproperty)
    * [methodCaller](#methodcallermethod-array-args--array)
    * [instanceCreator](#instancecreatorclass)
* [nspl\a](#nspla)
    * [all](#allsequence-predicate)
    * [any](#anysequence-predicate)
    * [map](#mapfunction-sequence)
    * [flatMap](#flatmapfunction-sequence)
    * [zip](#zipsequence1-sequence2)
    * [zipWith](#zipwithfunction-sequence1-sequence2)
    * [reduce](#reducefunction-sequence-initial--0)
    * [filter](#filterpredicate-sequence)
    * [filterNot](#filternotpredicate-sequence)
    * [take](#takesequence-n-step--1)
    * [takeKeys](#takekeyssequence-array-keys)
    * [takeWhile](#takewhilepredicate-sequence)
    * [first](#firstsequence)
    * [second](#secondsequence)
    * [drop](#dropsequence-n)
    * [dropKeys](#dropkeyssequence-array-keys)
    * [dropWhile](#dropwhilepredicate-sequence)
    * [last](#lastsequence)
    * [partition](#partitionpredicate-sequence)
    * [span](#spanpredicate-sequence)
    * [indexed](#indexedsequence-by-keeplast--true-transform--null)
    * [sorted](#sortedsequence-reversed--false-key--null-cmp--null)
    * [keySorted](#keysortedsequence-reversed--false)
    * [flatten](#flattensequence-depth--null)
    * [pairs](#pairssequence-valuekey--false)
    * [merge](#mergesequence1-sequence2)
    * [reorder](#reorderarray-list-from-to)
    * [value](#valuearray-key-default--null)
    * [keys](#keyssequence)
    * [in](#initem-array-array)
    * [isList](#islistvar)
    * [Callbacks](#callbacks-2)
* [nspl\a\lazy](#nsplalazy)
* [nspl\args](#nsplargs)
    * [expects](#expectsconstraints-arg-atposition--null-otherwisethrow--invalidargumentexception)
    * [expectsAll](#expectsallconstraints-array-args-array-atpositions---otherwisethrow--invalidargumentexception)
    * [expectsOptional](#expectsoptionalconstraints-arg-atposition--null-otherwisethrow--invalidargumentexception)
    * [Predefined constraints](#predefined-constraints)
    * [Custom constraints](#custom-constraints)
* [nspl\ds](#nsplds)
    * [ArrayObject](#arrayobject)
    * [DefaultArray](#defaultarray)
    * [Set](#set)
* [nspl\rnd](#nsplrnd)
    * [randomString](#length)
    * [choice](#choicesequence)
    * [weightedChoice](#weightedchoiceweightpairs)
    * [sample](#samplepopulation-length-preservekeys--false)
* [nspl](#nspl)
    * [getType](#gettypevar)

## nspl\f

Provides functions that act on other functions. Helps to write code in functional programming paradigm.

##### id($value)

Identity function. Returns passed value.

```php
assert(1 === id(1));
```

##### apply($function, array $args = [])

Applies given function to arguments and returns the result
```php
assert([1, 3, 5, 7, 9] === apply('range', [1, 10, 2]));
```

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

##### flipped($function)

Returns function which accepts arguments in the reversed order

##### compose($f, $g)

Returns new function which applies each given function to the result of another from right to left
```compose(f, g, h)``` is the same as ```f(g(h(x)))```
```php
use const \nspl\a\flatten;
use const \nspl\a\map;
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
use const \nspl\a\filter;
use const \nspl\a\map;
use const \nspl\a\reduce;
use function \nspl\f\partial;

$isEven = function($x) { return $x % 2 === 0; };
$square = function($x) { return $x * $x; };

// sum of squares of all even numbers less than 20
$sum = pipe(
    range(1, 20),
    partial(filter, $isEven),
    partial(map, $square),
    partial(reduce, sum)
);
```

> **Tip**
>
> To make your code compact you can use short function aliases. For example:
>
> ```php
> use function \nspl\f\partial as p;
>
> $sum = pipe(
>    range(1, 20),
>    p(filter, $isEven),
>    p(map, $square),
>    p(reduce, sum)
> );
> ```
> Note, while sometimes it can improve readability by removing extra characters it also may confuse your team members

##### curried($function, $withOptionalArgs = false)

Returns a [curried](https://en.wikipedia.org/wiki/Currying) version of the function. If you are going to curry a function which reads args with ```func_get_args()``` then pass a number of args as the 2nd argument.

If the second argument is true then curry function with optional args otherwise curry it only with required args. Or you can pass the exact number of args you want to curry.

##### uncurried($function)

Returns normal (uncurried) version of a [curried function](https://en.wikipedia.org/wiki/Currying)

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

##### throttled($function, $wait)

Returns throttled version of the passed function, that, when invoked repeatedly, will only actually call the original function at most once per every wait milliseconds.
```php
$f = function() {
    echo "Invoked\n";
};

$throttled = throttled($f, 10);

$startedAt = microtime(true);
do {
    $throttled();
} while((microtime(true) - $startedAt) * 1000 < 30); // 30ms
```
which outputs
```
Invoked
Invoked
Invoked
```

##### Callbacks

```nspl\f``` provides all these functions as callbacks in its constants which have the same names as the functions.
```php
use const \nspl\a\map;
use const \nspl\a\filter;

$incListItems = partial(map, function($v) { return $v + 1; });
$filterNumbers = partial(filter, 'is_numeric');
```

Check more ```\nspl\f``` examples [here](https://github.com/ihor/Nspl/blob/master/examples/f.php).


## nspl\op

Class ```nspl\op``` provides functions that perform standard PHP operations and can be passed as callbacks to higher-order functions. Mimics Python's [operator](https://docs.python.org/2/library/operator.html) module. For example:

```php
use const nspl\op\sum;
use function nspl\a\reduce;

assert(6 === reduce(sum, [1, 2, 3]));
```

##### Callbacks

The module provides the following operations both as functions and callbacks. See an example below.

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
use function nspl\a\map;

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

##### instanceCreator($class)
Returns a function that returns a new instance of a predefined class, passing its parameters to the constructor

```php
$users = map(instanceCreator(User::class), $usersData);
```

Check more ```\nspl\op``` examples [here](https://github.com/ihor/Nspl/blob/master/examples/op.php).

## nspl\a

Provides missing array functions which also can be applied to traversable sequences

##### all($sequence, $predicate)

Returns true if all ```$sequence``` items satisfy the predicate are true (or if the ```$sequence``` is empty). If predicate was not passed return true if all ```$sequence``` items are true.

```php
assert(true === all([true, true, true]));
```

##### any($sequence, $predicate)

Returns true if any ```$sequence``` items satisfies the predicate. If predicate was not passed returns true if any ```$sequence``` item is true. If the ```$sequence``` is empty, returns false.

```php
assert(true === any([true, false, false]));
```

##### map($function, $sequence)

Applies function of one argument to each sequence item
```php
assert(['A', 'B', 'C'] === map('strtoupper', ['a', 'b', 'c']));
```

##### flatMap($function, $sequence)

Applies function of one argument to each sequence item and flattens the result
```php
$duplicate = function($v) { return [$v, $v]; }
assert(['hello', 'hello', 'world', 'world'] === flatMap($duplicate, ['hello', 'world']));
```

##### zip($sequence1, $sequence2)

Zips two or more sequences
```php
assert([[1, 'a'], [2, 'b'], [3, 'c']] === zip([1, 2, 3], ['a', 'b', 'c']));
```

##### zipWith($function, $sequence1, $sequence2)

Generalises zip by zipping with the function given as the first argument, instead of an array-creating function
```php
use const \nspl\op\sum;

assert([101, 1002, 10003] === zipWith(sum, [1, 2, 3], [100, 1000, 10000]));
```

##### reduce($function, $sequence, $initial = 0)

Applies function of two arguments cumulatively to the sequence items, from left to right to reduce the sequence to a single value
```php
assert(6 === reduce(function($a, $b) { return $a + $b; }, [1, 2, 3]));

// Which is the same as
use const \nspl\op\sum;
assert(6 === reduce(sum, [1, 2, 3]));

```

##### filter($predicate, $sequence)

Returns sequence items that satisfy the predicate
```php
assert([1, 2, 3] === filter('is_numeric', ['a', 1, 'b', 2, 'c', 3]));
```

##### filterNot($predicate, $sequence)

Returns sequence items that don't satisfy the predicate
```php
assert(['a', 'b', 'c'] === filterNot('is_numeric', ['a', 1, 'b', 2, 'c', 3]));
```

##### take($sequence, $N, $step = 1)

Returns first N sequence items with given step
```php
assert([1, 3, 5] === take([1, 2, 3, 4, 5, 6, 7, 8, 9], 3, 2));
```

##### takeKeys($sequence, array $keys)

Returns array containing only given sequence keys
```php
assert(array('hello' => 1, 'world' => 2) === takeKeys(array('hello' => 1, 'world' => 2, 'foo' => 3), ['hello', 'world']));
```

##### takeWhile($predicate, $sequence)

Returns the longest sequence prefix of all items which satisfy the predicate
```php
assert([1, 2, 3] === takeWhile('is_numeric', [1, 2, 3, 'a', 'b', 'c', 4, 5, 6]));
```

##### first($sequence)

Returns the first sequence item
```php
assert(1 === first([1, 2, 3, 4, 5, 6, 7, 8, 9]));
```

##### second($sequence)

Returns the second sequence item
```php
assert(2 === second([1, 2, 3, 4, 5, 6, 7, 8, 9]));
```

##### drop($sequence, $N)

Drops first N sequence items
```php
assert([7, 8, 9] === drop([1, 2, 3, 4, 5, 6, 7, 8, 9], 6));
```

##### dropKeys($sequence, array $keys)

Returns array containing all keys except the given ones
```php
assert(array('hello' => 1, 'world' => 2) === dropKeys(array('hello' => 1, 'world' => 2, 'foo' => 3), ['foo']));
```

##### dropWhile($predicate, $sequence)

Drops the longest sequence prefix of all items which satisfy the predicate
```php
assert(['a', 'b', 'c', 4, 5, 6] === dropWhile('is_numeric', [1, 2, 3, 'a', 'b', 'c', 4, 5, 6]));
```

##### last($sequence)

Returns the last sequence item
```php
assert(9 === last([1, 2, 3, 4, 5, 6, 7, 8, 9]));
```

##### partition($predicate, $sequence)

Returns two lists, one containing values for which the predicate returned true, and the other containing the items that returned false
```php
assert([[1, 2, 3], ['a', 'b', 'c']] === partition('is_numeric', ['a', 1, 'b', 2, 'c', 3]));
```

##### span($predicate, $sequence)

Returns two lists, one containing values for which your predicate returned true until the predicate returned false, and the other containing all the items that left
```php
assert([[1], ['a', 2, 'b', 3, 'c']] === span('is_numeric', [1, 'a', 2, 'b', 3, 'c']));
```

##### indexed($sequence, $by, $keepLast = true, $transform = null)

Returns array which contains indexed sequence items

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

##### sorted($sequence, $reversed = false, $key = null, $cmp = null)

Returns array which contains sorted items from the passed sequence

If ```$reversed``` is true then return reversed sorted sequence. If ```$reversed``` is not boolean and ```$key``` was not passed then acts as a ```$key``` parameter
```$key``` is a function of one argument that is used to extract a comparison key from each item
```$cmp``` is a function of two arguments which returns a negative number, zero or positive number depending on whether the first argument is smaller than, equal to, or larger than the second argument
```php
assert([1, 2, 3] === sorted([2, 3, 1]));
assert(['c', 'b', 'a'] === sorted(['c', 'a', 'b'], true));

$usersSortedByName = sorted($users, function($u) { return $u->getName(); });

// Which is the same as
use function \nspl\op\methodCaller;
$usersSortedByName = sorted($users, methodCaller('getName'));
```

Check more ```\nspl\a\sorted``` examples [here](https://github.com/ihor/Nspl/blob/master/examples/a_sorted.php).

##### keySorted($sequence, $reversed = false)

Returns array which contains sequence items sorted by keys
```php
assert(array('a' => 1, 'b' => 2, 'c' => 3) === keySorted(array('b' => 2, 'c' => 3, 'a' => 1));
```

##### flatten($sequence, $depth = null)

Flattens multidimensional sequence
```php
assert([1, 2, 3, 4, 5, 6, 7, 8, 9] === flatten([[1, [2, [3]]], [[[4, 5, 6]]], 7, 8, [9]]));
assert([1, 2, [3], [4, 5, 6], 7, 8, 9] === flatten([[1, [2, [3]]], [[[4, 5, 6]]], 7, 8, [9]], 2));
```

##### pairs($sequence, $valueKey = false)

Returns list of (key, value) pairs. If ```$valueKey``` is true then convert array to (value, key) pairs.
```php
assert([['a', 'hello'], ['b', 'world'], ['c', 42]] === pairs(array('a' => 'hello', 'b' => 'world', 'c' => 42)));
```

##### merge($sequence1, $sequence2)

Returns arrays containing ```$sequence1``` items and ```$sequence2``` items
```php
assert([1, 2, 3, 4, 5, 6] === merge([1, 2, 3], [4, 5, 6]));
```

##### reorder(array $list, $from, $to)

Moves list item to another position
```php
assert([2, 0, 1] === reorder([0, 1, 2], 2, 0)); // move item from the 2nd position to the begining of the list
```

##### value($array, $key, $default = null)

Returns array value by key if it exists otherwise returns the default value
```php
$data = array('a' => 1, 'b' => 2, 'c' => 3);
assert(2 === value($data, 'b', -1));
assert(-1 === value($data, 'd', -1));
```

##### keys($sequence)

Returns list of the sequence keys
```php
assert(['a', 'b', 'c'] === keys(array('a' => 1, 'b' => 2, 'c' => 3)));
```
##### in($item, array $array)

Returns array value by key if it exists otherwise returns the default value
```php
assert(true === in(1, [1, 2, 3]);
```

##### isList($var)

Returns true if the variable is a list

##### Callbacks

```nspl\a``` provides all these functions as callbacks in its constants which have the same names as the functions.
```php
use const \nspl\a\first;
assert([1, 2, 3] === map(first, [[1, 'a'], [2, 'b'], [3, 'c']]));
```

Check more ```\nspl\a``` examples [here](https://github.com/ihor/Nspl/blob/master/examples/a.php).

## nspl\a\lazy
Provides lazy versions of functions from [nspl\a](#nspla)

This module might be useful when you don't need to process all the values from an array or any other traversable sequence. To understand how these lazy functions work let's have a look at the following example.

Let's define a function which wraps a generator function and logs all the values it yields. It will help up us to see the order of function calls:
```php
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
```

To have some data to operate on, let's define a function which returns all natural numbers. Since it returns all the natural numbers it never terminates:
```php
function naturalNumbers()
{
    $current = 1;
    while (true) yield $current++;
}
const naturalNumbers = 'naturalNumbers';
```

And let's define the operations we want to perform on those numbers:
```php
// Returns square of a number
function sqr($n)
{
    return $n * $n;
}
const sqr = 'sqr';

// Checks if a number is even
function isEven($n)
{
    return $n % 2 === 0;
}
const isEven = 'isEven';
```

Now let's assume we want to take the first three even natural numbers and calculate their squares:
```php
use const nspl\a\lazy\{take, map, filter};

$map = logged(map);
$take = logged(take);
$filter = logged(filter);
$numbers = logged(naturalNumbers)();

$evenNumbers = $filter(isEven, $numbers); // filter only even numbers
$firstThreeEvenNumbers = $take($evenNumbers, 3); // take only first 3 even numbers
$result = $map(sqr, $firstThreeEvenNumbers); // and calculate their squares

foreach ($result as $value) {
    echo "\nNext value is $value \n\n";
}
```

When we run this example we'll see the following output:
```
1. naturalNumbers -> 1
2. naturalNumbers -> 2
3. \nspl\a\lazy\filter -> 2
4. \nspl\a\lazy\take -> 2
5. \nspl\a\lazy\map -> 4

Next value is 4

6. naturalNumbers -> 3
7. naturalNumbers -> 4
8. \nspl\a\lazy\filter -> 4
9. \nspl\a\lazy\take -> 4
10. \nspl\a\lazy\map -> 16

Next value is 16

11. naturalNumbers -> 5
12. naturalNumbers -> 6
13. \nspl\a\lazy\filter -> 6
14. \nspl\a\lazy\take -> 6
15. \nspl\a\lazy\map -> 36

Next value is 36
```

If we used regular non-lazy versions of these functions we would generate all the natural numbers, then filtered only even numbers, then took only the first three of them and then calculated their squares. Instead of that you see that functions were called one by one passing the result to the next function until we completed the full cycle:
 1. We took first natural number – 1. It wasn't even so we skipped it
 2. We took the next one – 2, it was even
 3. And passed the ```filter``` function
 4. It was the first number we took, so it passed through the ```take``` function as well
 5. And then we calculated its square and printed the result

The same repeated on steps 6-10 and 11-15. On step 14 the ```take``` function took the last third number. So after step 15,  when ```map``` requested the next value ```take``` didn't yield anything and the whole iteration was finished.

Check this example [here](https://github.com/ihor/Nspl/blob/master/examples/a_lazy.php).

> **Tip**
>
> Note that while functions from ```\nspl\a\lazy``` allow you to avoid redundant computations, in case when you need to process all sequence values, functions from ```\nspl\a``` will do the job faster.

## nspl\args

Helps to validate function arguments

##### expects($constraints, $arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')

Checks that argument satisfies the required constraints otherwise throws the corresponding exception

```$constraints``` are callable(s) which return(s) true if the argument satisfies the requirements or it also might contain the required class name(s)
If ```$atPosition``` is null then position is calculated automatically comparing given argument to the actual arguments passed to the function
```$otherwiseThrow``` defines exception which will be thrown if given argument is invalid, it can be the exception class or exception object

```php
use const \nspl\args\int;
use const \nspl\args\string;
use const \nspl\args\arrayAccess;
use function \nspl\args\expects;

function nth($sequence, $n)
{
    expects([arrayAccess, string], $sequence);
    expects(int, $n);

    return $sequence[$n];
}

nth('hello world', 'blah');
```

Outputs:
```
InvalidArgumentException: Argument 2 passed to nth() must be integer, string 'blah' given in /path/to/example.php on line 17

Call Stack:
    0.0002     230304   1. {main}() /path/to/example.php:0
    0.0023     556800   2. sqr() /path/to/example.php:17
```

##### expectsAll($constraints, array $args, array $atPositions = [], $otherwiseThrow = '\InvalidArgumentException')

Checks that all specified arguments satisfy the required constraints otherwise throws the corresponding exception

```php
use const \nspl\args\numeric;
use function \nspl\args\expects;

function sum($x, $y)
{
    expectsAll(numeric, [$x, $y]);

    return $x + $y;
}
```

##### expectsOptional($constraints, $arg, $atPosition = null, $otherwiseThrow = '\InvalidArgumentException')

Checks that argument is null or satisfies the required constraints otherwise throws the corresponding exception

```php
function splitBy($string, $separator = ' ', $limit = null)
{
    expectsAll(string, [$string, $separator]);
    expectsOptional(int, $limit);

    return explode($separator, $string, $limit);
}
```

##### Predefined constraints

The module provides predefined constraints. Which can be one of the two types:
- OR-constraints which are evaluated with ```or``` operator (e.g. ```expects([int, string], $arg)``` evaluates as ```$arg``` has to be an ```int``` or a ```string```)
- AND-constraints which are evaluated with ```and``` operator (e.g. ```expects([string, longerThan(3), shorterThan(10)], $arg)``` evaluates as ```$arg``` has to be a string longer than 3 characters and shorter than 10 characters). If you want to evaluate several AND-constraints as they were OR-constraints you can use ```any``` constraint. If you want to evaluate several OR-constraints as they were AND-constraints you can use ```all``` constraint

Callback                            | Explanation                                                            | Type
------------------------------------|------------------------------------------------------------------------|----------
bool                                | Checks that argument is a bool                                         | OR
int                                 | Checks that argument is an int                                         | OR
float                               | Checks that argument is a float                                        | OR
numeric                             | Checks that argument is numeric                                        | OR
string                              | Checks that argument is a string                                       | OR
array_                              | Checks that argument is an array                                          | OR
object                              | Checks that argument is an object                                         | OR
callable_                           | Checks that argument is callable                                       | OR
arrayKey                            | Checks that argument can be an array key                               | OR
traversable                         | Checks that argument can be traversed with foreach                     | OR
arrayAccess                         | Checks that argument supports array index access                       | OR
nonEmpty                            | Checks that argument is not empty                                      | AND
positive                            | Checks that argument is positive (> 0)                                 | AND
nonNegative                         | Checks that argument is not negative (>= 0)                            | AND
nonZero                             | Checks that argument is not zero (!== 0)                               | AND
any(constraint1, ..., constraintN)  | Checks constraints as if they were OR-constraints                      | AND
all(constraint1, ..., constraintN)  | Checks constraints as if they were AND-constraints                     | AND
not(constraint1, ..., constraintN)  | Checks that argument does't satisfy all listed constraints             | AND
values(value1, ..., valueN)         | Checks that argument is one of the specified values                    | AND
longerThan($threshold)              | Checks that string argument is longer than given threshold             | AND
shorterThan($threshold)             | Checks that string argument is shorter than given threshold            | AND
biggerThan($threshold)              | Checks that number is bigger than given threshold                      | AND
smallerThan($threshold)             | Checks that number is smaller than given threshold                     | AND
hasKey($key)                        | Checks that argument supports array index access and has given key     | AND
hasKeys($key1, ..., $keyN)          | Checks that argument supports array index access and has given keys    | AND
hasMethod($method)                  | Checks that argument is an object and has given method                 | AND
hasMethods($method1, ..., $methodN) | Checks that argument is an object and has given methods                | AND


```php
function setUsername($username)
{
    expects([string, longerThan(3), shorterThan(10)], $username);
    // ...
}

function setState($state)
{
    expects(values('running', 'idle', 'stopped'), $state);
    // ...
}
```

Duck-typing example:
```php
class Service
{
    // ...
    public function setCache($cache)
    {
        expects(withMethods('set', 'get'), $cache);
        $this->cache = $cache;
    }
    // ....
}
```

##### Custom constraints

It is possible to use custom constraints. Just define a new function which returns true when argument satisfies the constraint:
```php
function even($value)
{
    return is_int($value) && $value %2 === 0;
}

function half($number)
{
    expects('even', $number);
    return $number / 2;
}
```
or we can make it more convenient to use introducing a constant:
```php
const even = 'even';

function half($number)
{
    expects(even, $number);
    return $number / 2;
}

half('pie');
```
Outputs:
```
InvalidArgumentException: Argument 1 passed to half() must be even, string 'pie' given in /path/to/example.php on line 25

Call Stack:
    0.0009     253640   1. {main}() /path/to/example.php:0
    0.0123     673984   2. half() /path/to/example.php:25
```

If you need to create a constraint which takes arguments you must create a callable object which implements ```\nspl\args\Constraint``` interface. It contains two methods:
- ```__invoke($value)``` - returns true if the value satisfies the constraint
- ```__toString()``` - returns text which will be used in the exception when value doesn't satisfy the constraint. The text must contain message which goes after "must" in the exception message.


## nspl\ds

Provides non-standard data structures and methods to work with them

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
$a = defaultarray(0);
foreach([1, 2, 1, 1, 3, 3, 3] as $v) {
    ++$a[$v];
}
```

##### defaultarray($default, $data = array())

Returns new DefaultArray

##### Set

Array-like collection that contains no duplicate elements. It supports basic set operations which take other sets, arrays and traversable objects as arguments

```php
$set = set(1, 2);

$set->add('hello');
$set[] = 'world';

$set->delete('hello');

$array = [1, 2, 3];
$intersection = $set->intersection($array);

$anotherSet = Set::fromArray([1, 2, 3]);
$difference = $set->difference($anotherSet);

$iterator = new \ArrayIterator([1, 2, 3]);
$union = $set->union($iterator);

$isSubset = $set->isSubset([1, 2, 'hello', 'world']);

$isSuperset = $set->isSuperset([1, 2]);
```

##### set

Returns new Set

Check more ```\nspl\ds``` examples [here](https://github.com/ihor/Nspl/blob/master/examples/ds.php).

## nspl\rnd

##### randomString($length)

Returns random alpha-numeric string of the given length

##### choice($sequence)

Returns a random item from a non-empty sequence

##### weightedChoice($weightPairs)

Returns a random item from a non-empty sequence of items with associated weights presented as pairs (item, weight)

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

##### sample($population, $length, $preserveKeys = false)

Returns a k length list of unique items chosen from the population sequence

Check more ```\nspl\rnd``` examples [here](https://github.com/ihor/Nspl/blob/master/examples/rnd.php).

## nspl

##### getType($var)

Returns the variable type or its class name if it is an object


Roadmap
=======

- Add laziness in version 1.3

Contributing
============

This project uses [semantic versioning](http://semver.org/) to tag releases. Please submit your pull requests to the latest release branch where the issue was introduced.

Feedback
========

There are no mailing lists or discussion groups yet. Please use GitHub issues and pull request or follow me on Twitter [@IhorBurlachenko](https://twitter.com/IhorBurlachenko)

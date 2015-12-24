Non-standard PHP library
========================
Nspl is inspired by Python and makes solving day to day routine tasks easier.


Installation
------------
Define the following requirement in your composer.json file:
```
"require": {
    "ihor/nspl": "1.0"
}
```
or simply execute the following the command line:
```
composer require ihor/nspl
```

Usage
=====

Nspl contains the following modules:
- [nspl\f](#nsplf) - provides the most popular higher-order functions: functions that act on or return other functions
- [nspl\op](#nsplop) - provides lambda-functions that perform standard PHP operations and can be passed as callbacks to higher-order functions. Mimics Python's [operator](https://docs.python.org/2/library/operator.html) module 
- [nspl\a](#nspla) - provides missing array functions
- [nspl\ds](#nsplds) - provides non-standard data structures and methods to work with them
- [nspl\rnd](#nsplrnd) - provides useful pseudo-random number generators


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

Provides the most popular higher-order functions: functions that act on or return other functions.


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

##### partial($function, $arg1)

Returns new partial function which will behave like $function with predefined *left* arguments passed to partial
```php
$sum = function($a, $b) { return $a + $b; };
$inc = partial($sum, 1);
```

##### rpartial($function, $arg1)

Returns new partial function which will behave like $function with predefined *right* arguments passed to rpartial
```php
$cube = rpartial('pow', 3);
```

##### ppartial($function, array $args)

Returns new partial function which will behave like $function with predefined *positional* arguments passed to ppartial
```php
$oddNumbers = ppartial('range', array(0 => 1, 2 => 2));
```

##### memoized($function)

Returns memoized $function which returns the cached result when the same inputs occur again
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
compose(f, g, h) is the same as f(g(h(x)))
```php
$underscoreToCamelcase = compose(
    'lcfirst',
    partial('str_replace', '_', ''),
    rpartial('ucwords', '_')
);
```

##### pipe($input, $function1, $function2)

Passes $input to composition of functions (functions have to be in the reversed order)
```php
assert('underscoreToCamelcase' === pipe(
    'underscore_to_camelcase', 
    rpartial('ucwords', '_'),
    partial('str_replace', '_', ''),
    'lcfirst'
))
```

##### I($input, $function1, $function2)

Alias for the pipe

*The following two functions were added for fun and don't have much practical usage in PHP.*

##### curried($function, $withOptionalArgs = false)

Returns you a curried version of the function. If you are going to curry a function which reads args with func_get_args() then pass a number of args as the 2nd argument.

If the second argument is true then curry function with optional args otherwise curry it only with required args. Or you can pass the exact number of args you want to curry.
```php
$curriedStrReplace = curried('str_replace');
$replaceUnderscores = $curriedStrReplace('_');
$replaceUnderscoresWithSpaces = $replaceUnderscores(' ');
assert('Hello world!' === $replaceUnderscoresWithSpaces('Hello_world!'));
```

##### uncurried($function)

Returns uncurried version of curried function
```php
$curriedStrReplace = curried('str_replace');
$strReplace = uncurried($curriedStrReplace);
```

##### Lambdas

Class *nspl\f* provides all these functions as lambdas in its static properties which have the same names as the functions.
```php
$incListItems = partial(f::$map, function($v) { return $v + 1; });
$filterNumbers = partial(f::$filter, 'is_numeric');
```


## nspl\op

Class *nspl\op* provides lambda-functions that perform standard PHP operations and can be passed as callbacks to higher-order functions. Mimics Python's [operator](https://docs.python.org/2/library/operator.html) module. For example:


```php
use nspl\op;
use function nspl\f\reduce;

assert(6 === reduce(op::$sum, [1, 2, 3]));
```

Function    | Operation
------------|-----------------------------------------------
op::$sum    | +
op::$sub    | -
op::$mul    | *
op::$div    | /
op::$mod    | %
op::$inc    | ++
op::$dec    | --
op::$neg    | -
op::$band   | &
op::$bxor   | ^
op::$bor    | &#124;
op::$bnot   | ~
op::$lshift | <<
op::$rshift | >>
op::$lt     | <
op::$le     | <=
op::$eq     | ==
op::$idnt   | ===
op::$ne     | !=
op::$nidnt  | !==
op::$ge     | >
op::$gt     | >=
op::$and    | &&
op::$mand   | The same as && except: false && false = true
op::$or     | &#124;&#124;
op::$xor    | xor
op::$not    | !
op::$concat | .
op::$int    | (int)
op::$bool   | (bool)
op::$float  | (float)
op::$str    | (string)
op::$array  | (array)
op::$object | (object)

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


## nspl\a

Provides missing array functions

##### all($sequence, $predicate)

Returns true if all elements of the $sequence satisfy the predicate are true (or if the $sequence is empty). If predicate was not passed return true if all elements of the $sequence are true.

```php
assert(true === all([true, true, true]);
```

##### any($sequence, $predicate)

Returns true if any element of the $sequence satisfies the predicate. If predicate was not passed returns true if any element of the $sequence is true. If the $sequence is empty, returns false.

```php
assert(true === any([true, false, false]);
```

##### extend(array $list1, array $list2)

Adds $list2 items to the end of $list1
```php
assert([1, 2, 3, 4, 5, 6] === extend([1, 2, 3], [4, 5, 6]));
```

##### zip(array $list1, array $list2)

Zips two or more lists
```php
assert([[1, 'a'], [2, 'b'], [3, 'c']] === zip([1, 2, 3], ['a', 'b', 'c']));
```

##### flatten(array $list)

Flattens multidimensional list
```php
assert([1, 2, 3, 4, 5, 6, 7, 8, 9] === flatten([[1, 2, 3], [4, 5, 6], [7, 8, 9]]));
```

##### sorted(array $array, $reversed = false, $key = null, $cmp = null)

Returns sorted copy of the passed array
$key is a function of one argument that is used to extract a comparison key from each element
$cmp is a function of two arguments which returns a negative number, zero or positive number depending on whether the first argument is smaller than, equal to, or larger than the second argument
```php
assert([1, 2, 3] === sorted([2, 3, 1]));
assert(['a', 'b', 'c'] === sorted(['c', 'a', 'b'], true));

$usersSortedByName = sorted($users, false, function($u1, $u2) { return $u1->getName() - $u2->getName(); });
// Which is the same as
use function nspl\op\methodCaller;
$usersSortedByName = sorted($users, false, methodCaller('getName'));
```

##### indexed(array $list, $by, $keepLast = true, $transform = null)

Returns indexed list of items

$by is an array key or a function
If $keepLast is true only the last item with the key will be returned otherwise list of items which share the same key value will be returned
$transform is a function that transforms list item after indexing

```php
$indexedById = indexed([
    array('id' => 1, 'name' => 'John'),
    array('id' => 2, 'name' => 'Kate'),
    array('id' => 3, 'name' => 'Robert'),
], 'id');
```

##### pairs(array $array, $valueKey = false)

Returns list of (key, value) pairs. If $valueKey is true then convert array to (value, key) pairs.
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

##### Lambdas

Class *nspl\a* provides all these functions as lambdas in its static properties which have the same names as the functions.
```php
assert([1, 2, 3] === map(a::$first, [[1, 'a'], [2, 'b'], [3, 'c']]));
```


## nspl\ds

Provides non-standard data structures and methods to work with them


##### getType($var)

Returns the variable type or its class name if it is an object

##### isList($var)

Returns true if the variable is a list

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


## nspl\rnd

Provides useful pseudo-random number generators


##### choice(array $sequence)

Returns a random element from a non-empty sequence

##### weightedChoice(array $weightPairs)

Returns a random element from a non-empty sequence of items with associated weights presented as pairs (item, weight)

##### sample(array $population, $length, $preserveKeys = false)

Returns a k length list of unique elements chosen from the population sequence

Roadmap
=======

TODO

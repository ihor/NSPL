Non-standard PHP library
========================
Sometimes I'm not happy with PHP API and write my own implementations or port something from other languages (like Python) to PHP. I decided to start collecting those implementations in Non-standard PHP library.

Installation
------------
Define the following requirement in your composer.json file:
```
"require": {
    "ihor/nspl": "1.0"
}
```

Usage
-----

Here I assume that all functions are imported with *use function*.

#### nspl/f

Provides functions needed to work with functions

**map($function, $sequence)**
```php
map('strtoupper', ['a', 'b', 'c']);
```

**reduce($function, $sequence, $initial = 0)**
```php
reduce(function($a, $b) { return $a + $b; }, [1, 2, 3]);
```

**filter($function, $sequence)**
```php
filter('is_numeric', ['a', 1, 'b', 2, 'c', 3]);
```

**apply($function, array $args = [])**

Applies a function to arguments and returns the result
```php
apply('range', [1, 10, 2]);
```

**compose($f, $g)**

Returns composition of the last function in arguments list with functions that take one argument
compose(f, g, h) is the same as f(g(h(x)))
```php
$underscoreToCamelcase = compose(
    'lcfirst',
    partial('str_replace', ' ', ''),
    'ucwords',
    partial('str_replace', '_', ' ')
);
```

**partial($function)**

Returns new partial function which will behave like $function with predefined *left* arguments passed to partial
```php
$sum = function($a, $b) { return $a + $b; };
$inc = partial($sum, 1);
```

**rpartial($function)**

Returns new partial function which will behave like $function with predefined *right* arguments passed to rpartial
```php
$cube = rpartial('pow', 3);
```

**ppartial($function)**

Returns new partial function which will behave like $function with predefined *positional* arguments passed to ppartial
```php
$concatThreeStrings = function($s1, $s2, $s3) { return $s1 . $s2 . $s3; };
$greet = ppartial($concatThreeStrings, array(0 => 'Hello ', 2 => '!'));
assert('Hello world!' === $greet('world'));
```

**curried($function, $withOptionalArgs = false)**

Returns you a curried version of function. If you are going to curry a function which read args with func_get_args() then pass number of args as the 2nd argument.

If the second argument is true then curry function with optional args otherwise curry it only with required args. Or you can pass the exact number of args you want to curry.
```php
$curriedStrReplace = curried('str_replace');
$replaceUnderscores = $curriedStrReplace('_');
$replaceUnderscoresWithSpaces = $replaceUnderscores(' ');
echo $replaceUnderscoresWithSpaces('Hello_world!');
```

**memoized($function)**

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
```
Performing heavy calculations with 'Hello world!'
Hello world!
Hello world!
```

**pipe($args, array $functions)**

Passes args to composition of functions (functions have to be in the reversed order)
```php
pipe('underscore_to_camelcase', [
    partial('str_replace', '_', ' '),
    'ucwords',
    partial('str_replace', ' ', ''),
    'lcfirst'
])
```

**I($args, array $functions)**

Alias for pipe

#### nspl/op

Provides lambda-functions that perform standard PHP operations and can be passed as callbacks to higher-order functions. For example:
```php
use nspl\op;
use function nspl\f\reduce;

reduce(op::$sum, [1, 2, 3]);
```
which is shorter and nicer than:
```php
reduce(function($a, $b) { return $a + $b; }, [1, 2, 3]);
```
I'm not going to list standard PHP operators, you can easily find any of them with autocompletion in your favourite IDE. I'm listing only non-standard ones. All functions are presented as static variables of class *op* except cases when we need to pass some arguments to receive the desired function (like itemGetter). In these cases they are presented as static functions of class *op* and have aliases as functions in namespace *op*.

**itemGetter($key)**
Returns a function that returns key value for a given array

```php
use nspl\op\itemGetter;
use function nspl\f\map;

assert([2, 5, 8] === map(itemGetter(1), [[1, 2, 3], [4, 5, 6], [7, 8, 9]]));
```

**propertyGetter($property)**
Returns a function that returns property value for a given object

```php
$userIds = map(propertyGetter('id'), $users);
```

**methodCaller($method, array $args = array())**
Returns a function that returns method result for a given object on predefined arguments

```php
$userIds = map(methodCaller('getId'), $users);
```

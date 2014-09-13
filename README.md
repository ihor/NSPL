Non-standard PHP library
========================
Sometimes when you notice that you are not happy with PHP API you write an implementation that you like. Then you forget about it and never come back to that code again because it is not practical to use it. I decided to start collecting those implementations in Non-standard PHP library. Some of them are inspired (copied) from Python.

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

Here I suppose that all functions are imported with *use function*.

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
```php
apply('range', [1, 10, 2]);
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

**compose($f, $g)**

Returns composition of the last function in arguments with any functions that take one argument
```php
$underscoreToCamelcase = compose(
    'lcfirst',
    partial('str_replace', ' ', ''),
    'ucwords',
    partial('str_replace', '_', ' ')
);
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

Alias to pipe

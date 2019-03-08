<?php

require_once __DIR__ . '/nspl/nspl.php';
require_once __DIR__ . '/nspl/a.php';
require_once __DIR__ . '/nspl/a/ChainableSequence.php';
require_once __DIR__ . '/nspl/a/ChainableArray.php';
require_once __DIR__ . '/nspl/a/lazy/LazyChainableSequence.php';

if ((PHP_MAJOR_VERSION >= 7) || (PHP_MAJOR_VERSION >= 5 && PHP_MINOR_VERSION >= 4)) {
    require_once __DIR__ . '/nspl/a/lazy.php';
}
require_once __DIR__ . '/nspl/args.php';
require_once __DIR__ . '/nspl/f.php';
require_once __DIR__ . '/nspl/ds.php';
require_once __DIR__ . '/nspl/rnd.php';
require_once __DIR__ . '/nspl/op.php';

require_once __DIR__ . '/nspl/ds/Collection.php';
require_once __DIR__ . '/nspl/ds/ArrayObject.php';
require_once __DIR__ . '/nspl/ds/DefaultArray.php';
require_once __DIR__ . '/nspl/ds/Set.php';

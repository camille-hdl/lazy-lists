# camille-hdl/lazy-lists

[![Source Code][badge-source]][source]
[![Latest Version][badge-release]][packagist]
[![Software License][badge-license]][license]
[![PHP Version][badge-php]][php]
[![Build Status][badge-build]][build]
[![Coverage Status][badge-coverage]][coverage]
[![Total Downloads][badge-downloads]][downloads]

LazyLists is a toolbox for iterating over a `Traversable` and transforming it. This is your typical `map`, `filter`, `pipe` library, but with a twist: you only ever iterate at most once.

Using `\LazyLists\pipe()`, you can compose `filter`, `map`, `reduce`, `each`, `until`(...) into a single function that will iterate over your input only once (even less than once if you use `take` or `until`), thus, "Lazy".  

For example, let's say we want to compute something about the 50 first products ordered in the Technology category of an online store:  
```php
$getUsefulInsight = pipe(
    map($getProductsInOrder),
    flatten(1),
    filter($isTechnologyRelated),
    take(50),
    reduce($computeInsight, $initialValue)
);
$insight = $getUsefulInsight($orders);
```
Even if `$orders` is very large, `$getUsefulInsight` will only step through it until `$isTechnologyRelated` has let 50 items through (or `$orders` runs out before that), then stop the iteration early and return the final result of `$computeInsight`.  
`$getProductsInOrder` and `$isTechnologyRelated` will be called only as long as they are needed.

This is particularly useful if the cost of iteration is high.


Alternatively, you can just use the functions directly: `$output = \LazyLists\map($transformation, $input)`.

You can use these features on arrays or `Traversable`s such as iterators.

See the examples below.

## Installation

The preferred method of installation is via [Composer][]. Run the following
command to install the package and add it as a requirement to your project's
`composer.json`:

```bash
composer require camille-hdl/lazy-lists
```


## Usage

You can use the functions directly on arrays or `Traversable`s

```php
use LazyLists\map;
use LazyLists\filter;
use LazyLists\reduce;
use LazyLists\flatten;
use LazyLists\take;

$result = map($fn, $input);
$result = filter($fn, $input);
$result = reduce($fn, [], $input);
$result = flatten(1, $input);
$result = take(10, $input);
```

but the most interesting way of using LazyLists is to use the composition functions : `pipe` or `iterate`.  
Steps in the pipeline are executed sequentially *for each element* in the collection *in a single iteration*, unlike `array_map`, `array_filter` or other similar libraries.

```php
use LazyLists\pipe;
use LazyLists\iterate;
use LazyLists\map;
use LazyLists\filter;
use LazyLists\reduce;
use LazyLists\each;
use LazyLists\flatten;
use LazyLists\take;
use LazyLists\until;

/**
 * Compose steps together into a single function
 */
$computeFinalResult = pipe(
    flatten(1),
    filter($myPredicate),
    map($myTransformation),
    each($mySideEffect),
    take(10),
    reduce($myAggregator, 0)
);
// returns an array
$result = $computeFinalResult($myArrayOrIterator);

// returns an iterator
$filterIterator = iterate(
    filter($myPredicate),
    until($myCondition)
);
foreach ($filterIterator($myArrayOrIterator) as $key => $value) {
    echo "$key : $value";
}

// you can iterate over a reduction
$reduceIterator = iterate(
    reduce(function ($acc, $v) { return $acc + $v; }, 0),
    until(function ($sum) { return $sum > 10; })
);
foreach ($reduceIterator([1, 5, 10]) as $reduction) {
    echo $reduction;
}
// 1, 5
```

### Gotchas

* When iterating over arrays, keys are *never* kept, by design.
* The return value of `pipe(...)($input)` depends on the last function in the composition, according to the following heuristic, which is intuitive to me but maybe not to you:
    * if the last function is one that returns a single value, such as `reduce()`, the return value will be this value;
    * if the last function is one that transforms an input set into an output set (such as `map` or `flatten`), the return value will be the output set (as a flat array);
    * if the last function is one that restricts the output set, such as `filter`, `take` or `until`, the return value will be the output set (as a flat array);
* `iterate(...)($input)` behaves in the same way, but returns an iterator instead of an array.

### Extending LazyLists

* You can create your own function usable with `pipe()` and `iterate()` by having it return a `\Lazy\Transducer\TransducerInterface`
* You can create your own composition function (to use instead of `pipe()` or `iterate()`). It should be relatively easy to implement something that can take a stream as input for example. Look-up the source code of `pipe()` (its very short!) and `\Lazy\LazyWorker` (its less short!).

## Performance considerations

LazyLists is optimized for minimizing the number of iterations while (hopefully) allowing for API elegance when transforming an input set.

Thus, the lower the cost of iteration, the less incentive there is to use this library.

If your program is only iterating over fast, in-memory data structures, performance will almost always be worse than using the built-in `array_*` functions. That said, if you use `\LazyLists\pipe()` to compose your functions, the performance gap reduces as the number of iterations increases.

You can see for yourself by running `composer run phpbench`.

However, using `\LazyLists\pipe()` will probably be beneficial in terms of performance if you either:

* use I/O during iteration, or
* use `\LazyLists\take()` and `\LazyLists\until()` to restrict your output set.

## Inspiration

This library attempts a subset of what transducers can do. There are transducer libraries out there, but I hope to bring a simpler API.

* [Watch a talk on transducers](https://www.youtube.com/watch?v=6mTbuzafcII)
* https://github.com/mtdowling/transducers.php

## Contributing

This project adheres to a [Contributor Code of Conduct][conduct]. By
participating in this project and its community, you are expected to uphold this
code.

Contributions are welcome! Please read [CONTRIBUTING][] for details.


## Copyright and License

The camille-hdl/lazy-lists library is copyright © [Camille Hodoul](https://camillehdl.dev)
and licensed for use under the MIT License (MIT). Please see [LICENSE][] for
more information.


[conduct]: https://github.com/camille-hdl/lazy-lists/blob/master/.github/CODE_OF_CONDUCT.md
[composer]: http://getcomposer.org/
[documentation]: https://camille-hdl.github.io/lazy-lists/
[contributing]: https://github.com/camille-hdl/lazy-lists/blob/master/.github/CONTRIBUTING.md

[badge-source]: http://img.shields.io/badge/source-camille--hdl/lazy--lists-blue.svg?style=flat-square
[badge-release]: https://img.shields.io/packagist/v/camille-hdl/lazy-lists.svg?style=flat-square&label=release
[badge-license]: https://img.shields.io/packagist/l/camille-hdl/lazy-lists.svg?style=flat-square
[badge-php]: https://img.shields.io/packagist/php-v/camille-hdl/lazy-lists.svg?style=flat-square
[badge-build]: https://img.shields.io/travis/camille-hdl/lazy-lists/master.svg?style=flat-square
[badge-coverage]: https://img.shields.io/coveralls/github/camille-hdl/lazy-lists/master.svg?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/camille-hdl/lazy-lists.svg?style=flat-square&colorB=mediumvioletred

[source]: https://github.com/camille-hdl/lazy-lists
[packagist]: https://packagist.org/packages/camille-hdl/lazy-lists
[license]: https://github.com/camille-hdl/lazy-lists/blob/master/LICENSE
[php]: https://php.net
[build]: https://travis-ci.org/camille-hdl/lazy-lists
[coverage]: https://coveralls.io/r/camille-hdl/lazy-lists?branch=master
[downloads]: https://packagist.org/packages/camille-hdl/lazy-lists

# camille-hdl/lazy-lists

[![Source Code][badge-source]][source]
[![Latest Version][badge-release]][packagist]
[![Software License][badge-license]][license]
[![PHP Version][badge-php]][php]
[![Build Status][badge-build]][build]
[![Coverage Status][badge-coverage]][coverage]
[![Total Downloads][badge-downloads]][downloads]

Lazy list processing functions.

This project adheres to a [Contributor Code of Conduct][conduct]. By
participating in this project and its community, you are expected to uphold this
code.


## Installation

The preferred method of installation is via [Composer][]. Run the following
command to install the package and add it as a requirement to your project's
`composer.json`:

```bash
composer require camille-hdl/lazy-lists
```


## Documentation

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

$pipeline = pipe(
    flatten(1),
    filter($myPredicate),
    map($myTransformation),
    each($mySideEffect),
    take(10),
    reduce($myAggregator, 0)
);
// returns an array
$result = $pipeline($myArrayOrIterator);

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

## Contributing

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

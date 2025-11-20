<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php83\Rector\BooleanAnd\JsonValidateRector;
use Rector\Php83\Rector\ClassConst\AddTypeToConstRector;
use Rector\Php84\Rector\Foreach_\ForeachToArrayAllRector;
use Rector\Php84\Rector\Foreach_\ForeachToArrayAnyRector;
use Rector\Php84\Rector\Foreach_\ForeachToArrayFindKeyRector;
use Rector\Php84\Rector\Foreach_\ForeachToArrayFindRector;
use Rector\Php84\Rector\MethodCall\NewMethodCallWithoutParenthesesRector;

return RectorConfig::configure()
    ->withSkip([
        RemoveExtraParametersRector::class,
        AddTypeToConstRector::class, // PHP 8.3 only
        JsonValidateRector::class, // PHP 8.3 only
        ForeachToArrayAnyRector::class, // PHP 8.4 only
        ClassPropertyAssignToConstructorPromotionRector::class,
        NewMethodCallWithoutParenthesesRector::class, // PHP 8.4 only
        ForeachToArrayFindKeyRector::class, // PHP 8.4 only
        ForeachToArrayAllRector::class, // PHP 8.4 only
        ForeachToArrayFindRector::class, // PHP 8.4 only
    ])
    ->withPhpSets(
        php84: true
    )
    ->withPhpVersion(\Rector\ValueObject\PhpVersion::PHP_84);

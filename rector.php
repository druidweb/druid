<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Config\RectorConfig;
use RectorLaravel\Rector\FuncCall\UnlinkFuncCallToFileFacadeDeleteRector;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
  ->withSets([
    LaravelSetList::LARAVEL_ARRAYACCESS_TO_METHOD_CALL,
    LaravelSetList::LARAVEL_ARRAY_STR_FUNCTION_TO_STATIC_CALL,
    LaravelSetList::LARAVEL_CODE_QUALITY,
    LaravelSetList::LARAVEL_COLLECTION,
    LaravelSetList::LARAVEL_CONTAINER_STRING_TO_FULLY_QUALIFIED_NAME,
    LaravelSetList::LARAVEL_ELOQUENT_MAGIC_METHOD_TO_QUERY_BUILDER,
    LaravelSetList::LARAVEL_FACADE_ALIASES_TO_FULL_NAMES,
    LaravelSetList::LARAVEL_FACTORIES,
    LaravelSetList::LARAVEL_IF_HELPERS,
    LaravelSetList::LARAVEL_LEGACY_FACTORIES_TO_CLASSES,
  ])
  ->withImportNames(
    removeUnusedImports: true,
  )
  ->withComposerBased(laravel: true)
  ->withCache(
    cacheDirectory: '/tmp/rector',
    cacheClass: FileCacheStorage::class,
  )
  ->withPaths([
    __DIR__.'/app',
    __DIR__.'/bootstrap/app.php',
    __DIR__.'/config',
    __DIR__.'/database',
    __DIR__.'/public',
    __DIR__.'/routes',
    __DIR__.'/tests',
  ])
  ->withSkip([
    // Pest.php's shutdown handler runs after the container is flushed, so the File facade can't resolve there.
    UnlinkFuncCallToFileFacadeDeleteRector::class => [__DIR__.'/tests/Pest.php'],
  ])
  ->withPreparedSets(
    deadCode: true,
    codeQuality: true,
    typeDeclarations: true,
    privatization: true,
  )
  ->withPhpSets(php84: true);

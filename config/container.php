<?php

use DI\ContainerBuilder;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\PsrCachedReader;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;

return static function(): ContainerInterface {
    $cacheDir = __DIR__. '/var/mapper-cache';
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir);
    }

    $cache = new PhpFilesAdapter('mapper.common-cache', 0, $cacheDir, true);

    $yamlConfCacheItem = $cache->getItem('config');
    if (!$yamlConfCacheItem->isHit()) {
        throw new RuntimeException('Load config from .yaml with cli first');
    }

    $yamlConf = $yamlConfCacheItem->get();

    /** @var ContainerBuilder $definitions */
    $definitions = require (__DIR__ . '/../config/definitions.php')(
        new PsrCachedReader(new AnnotationReader(), $cache, $yamlConf['mapper']['common']['debug']),
        $cache,
        require (__DIR__ . '/../config/clickhouse-connection.php')($yamlConf)
    );

    return $definitions->build();
};
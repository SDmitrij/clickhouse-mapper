<?php

/**
 * @noinspection PhpUnusedParameterInspection
 * @noinspection PhpMultipleClassDeclarationsInspection
 */
namespace Mapper;

use ClickHouseDB\Client;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\PsrCachedReader;
use Exception;
use Mapper\Connection\Interaction\ClickHouseInteraction;
use Mapper\Connection\Interaction\ClickHouseInteractionInterface;
use Mapper\Entity\EntityManager;
use Mapper\Entity\EntityManagerInterface;
use Mapper\Evaluation\Evaluator;
use Mapper\Evaluation\EvaluatorInterface;
use Mapper\Metadata\MetadataManager;
use Mapper\Metadata\MetadataManagerInterface;
use Mapper\Validation\Validator;
use Mapper\Validation\ValidatorInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Container\ContainerInterface;
use RuntimeException;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;
use Symfony\Component\Cache\Exception\CacheException;

class ContainerBuilder
{
    /**
     * @throws CacheException
     * @throws Exception|InvalidArgumentException
     */
    public static function build(): ContainerInterface
    {
        $cacheDir = dirname(__DIR__).'/mapper-cache';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir);
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        $cache = new PhpFilesAdapter('mapper.common-cache', 0, $cacheDir, true);

        $yamlConfCacheItem = $cache->getItem('config');
        if (!$yamlConfCacheItem->isHit()) {
            throw new RuntimeException('Load config from .yaml with cli first');
        }

        $yamlConf = $yamlConfCacheItem->get();

        $reader = new PsrCachedReader(new AnnotationReader(), $cache, $yamlConf['mapper']['common']['debug']);
        $clickHouseClient = new Client(
            [
                'host' => $yamlConf['mapper']['connection']['host'],
                'port' => $yamlConf['mapper']['connection']['port'],
                'username' => $yamlConf['mapper']['connection']['username'],
                'password' => $yamlConf['mapper']['connection']['password'],
            ],
            ['database' => $yamlConf['mapper']['connection']['database']]
        );

        $builder = new \DI\ContainerBuilder();

        $builder->addDefinitions([
            ClickHouseInteractionInterface::class => function () use ($clickHouseClient) {
                return new ClickHouseInteraction($clickHouseClient);
            },
            EvaluatorInterface::class => function () {
                return new Evaluator();
            },
            ValidatorInterface::class => function (ContainerInterface $container) use ($cache) {
                return new Validator($container->get(ClickHouseInteractionInterface::class), $cache);
            },
            MetadataManagerInterface::class => function () use ($reader, $cache) {
                return new MetadataManager($reader, $cache);
            },
            EntityManagerInterface::class => function (ContainerInterface $container) {
                return new EntityManager(
                    $container->get(ClickHouseInteractionInterface::class),
                    $container->get(ValidatorInterface::class),
                    $container->get(MetadataManagerInterface::class),
                    $container->get(EvaluatorInterface::class)
                );
            },
        ]);

        return $builder->build();
    }
}

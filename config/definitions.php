<?php

use ClickHouseDB\Client;
use DI\ContainerBuilder;
use Doctrine\Common\Annotations\Reader;
use Mapper\Commands\LoadConfigCommand;
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
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;

return static function(Reader $reader, CacheItemPoolInterface $cacheItemPool, Client $client): ContainerBuilder {
    $builder = new ContainerBuilder();

    $builder->addDefinitions([
        ClickHouseInteractionInterface::class => function () use ($client) {
            return new ClickHouseInteraction($client);
        },
        EvaluatorInterface::class => function () {
            return new Evaluator();
        },
        ValidatorInterface::class => function (ContainerInterface $container) use ($cacheItemPool) {
            return new Validator($container->get(ClickHouseInteractionInterface::class), $cacheItemPool);
        },
        MetadataManagerInterface::class => function () use ($reader, $cacheItemPool) {
            return new MetadataManager($reader, $cacheItemPool);
        },
        EntityManagerInterface::class => function (ContainerInterface $container) {
            return new EntityManager(
                $container->get(ClickHouseInteractionInterface::class),
                $container->get(ValidatorInterface::class),
                $container->get(MetadataManagerInterface::class),
                $container->get(EvaluatorInterface::class)
            );
        },
        LoadConfigCommand::class => function () use ($cacheItemPool) {
            return new LoadConfigCommand($cacheItemPool);
        },
        'console' => [
            'commands' => [
                LoadConfigCommand::class
            ]
        ]
    ]);

    return $builder;
};
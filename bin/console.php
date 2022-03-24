<?php

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

/** @var ContainerInterface $container */
$container = (require __DIR__.'/../config/container.php')();

$cli = new Application('mapper-console');

foreach ($container->get('console')['commands'] as $command) {
    $cli->add($container->get($command));
}

$cli->run();

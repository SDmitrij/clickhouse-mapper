<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mapper\Test;

use Mapper\ContainerBuilder;
use Psr\Container\ContainerInterface;

trait WithContainerTrait
{
    private ContainerInterface $container;

    public function getCertain(string $class)
    {
        return $this->container->get($class);
    }

    /**
     * @before
     */
    public function buildContainer(): void
    {
        $this->container = ContainerBuilder::build();
    }
}

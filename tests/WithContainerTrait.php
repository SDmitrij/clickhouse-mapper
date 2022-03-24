<?php

namespace Mapper\Test;

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
    public function container(): void
    {
        $this->container = (require __DIR__.'/../config/container.php')();
    }
}

<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mapper\Test;

use Mapper\ContainerBuilder;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Cache\Exception\CacheException;

trait WithContainerTrait
{
    private ContainerInterface $container;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getCertain(string $class)
    {
        return $this->container->get($class);
    }

    /**
     * @before
     * @throws CacheException
     */
    public function buildContainer(): void
    {
        $this->container = ContainerBuilder::build();
    }
}
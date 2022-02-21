<?php

namespace Mapper\Test\Unit;

use Mapper\Entity\EntityManagerInterface;
use Mapper\Mapper;
use PHPUnit\Framework\TestCase;

class MapperEntryTest extends TestCase
{
    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function testGettingEntityManagerSuccess(): void
    {
        $this->assertInstanceOf(EntityManagerInterface::class, Mapper::getEntityManager());
    }
}
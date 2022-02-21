<?php

namespace Mapper\Test\Unit;

use DateTime;
use Entity\Order;
use Exception;
use Mapper\Entity\EntityManagerInterface;
use Mapper\Entity\Exception\EntityAlreadyAttachedException;
use Mapper\Test\Builder\OrderBuilder;
use Mapper\Test\Builder\StatusBuilder;
use Mapper\Test\WithContainerTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class EntityManagerTest extends TestCase
{
    use WithContainerTrait;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testAttachSuccess(): void
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->getCertain(EntityManagerInterface::class);

        foreach ((new OrderBuilder())->build(1000) as $order) {
            $entityManager->attach($order);
        }

        $buffer = $entityManager->getBuffer();

        $this->assertArrayHasKey(Order::class, $buffer);
        $this->assertCount(1000, $buffer[Order::class]['values']);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testEntityAlreadyAttachedException(): void
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->getCertain(EntityManagerInterface::class);
        $entityManager->attach(
            new Order(
                new DateTime('-5 days'),
                new DateTime(),
                10,
                20,
                30,
                40,
                (new StatusBuilder())->build()
            )
        );
        $this->expectException(EntityAlreadyAttachedException::class);
        $entityManager->attach(
            new Order(
                new DateTime('-5 days'),
                new DateTime(),
                10,
                20,
                30,
                40,
                (new StatusBuilder())->build()
            )
        );
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testReleaseSuccess(): void
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->getCertain(EntityManagerInterface::class);

        foreach ((new OrderBuilder())->build(1000) as $order) {
            $entityManager->attach($order);
        }

        $entityManager->release();
        $buffer = $entityManager->getBuffer();

        foreach ($buffer as $entity) {
            $this->assertEmpty($entity['values']);
        }
    }
}

<?php

namespace Mapper\Test\Unit;

use Entity\Order;
use Mapper\Metadata\MetadataManagerInterface;
use Mapper\Test\WithContainerTrait;
use Mapper\Validation\ValidatorInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Mapper\Validation\Validator
 */
class ValidatorTest extends TestCase
{
    use WithContainerTrait;

    public function testIsValidSuccess(): void
    {
        /** @var MetadataManagerInterface $metadataManager */
        $metadataManager = $this->getCertain(MetadataManagerInterface::class);
        $metadataManager->loadFor(Order::class);
        /** @var ValidatorInterface $validator */
        $validator = $this->getCertain(ValidatorInterface::class);

        $this->assertTrue($validator->isValid(Order::class, $metadataManager->get()));
    }
}

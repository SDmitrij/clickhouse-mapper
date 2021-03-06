<?php

namespace Mapper\Test\Unit;

use Entity\Order;
use InvalidArgumentException;
use Mapper\Metadata\Exception\InvalidAnnotationsCombination;
use Mapper\Metadata\Exception\InvalidBaseEntityAnnotationException;
use Mapper\Metadata\Exception\InvalidEmbeddedEntityAnnotationException;
use Mapper\Metadata\Exception\NullableAnnotationException;
use Mapper\Metadata\MetadataManagerInterface;
use Mapper\Metadata\Visitor\CommonInfoPlain;
use Mapper\Metadata\Visitor\CommonInfoVisitor;
use Mapper\Metadata\Visitor\PropertyInfoPlain;
use Mapper\Metadata\Visitor\PropertyInfoVisitor;
use Mapper\Test\Entity\EntityWithNullablePropertyWithoutDefaultValue;
use Mapper\Test\Entity\EntityWithPropertyMarkedWithInvalidEvaluationRuleAnnotation;
use Mapper\Test\Entity\InvalidEntityThatReferenceToInvalidEmbedded;
use Mapper\Test\Entity\InvalidEntityWithCommonAndEmbeddedAnnotation;
use Mapper\Test\Entity\InvalidEntityWithoutAnnotations;
use Mapper\Test\Entity\InvalidEntityWithPropertyThatHasPropertyColumnAndEmbeddedPropertyAnnotation;
use Mapper\Test\WithContainerTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Mapper\Metadata\MetadataManager
 */
class MetadataManagerTest extends TestCase
{
    use WithContainerTrait;

    public function testParseMetadataSuccess(): void
    {
        /** @var MetadataManagerInterface $metadataManager */
        $metadataManager = $this->getCertain(MetadataManagerInterface::class);
        $metadataManager->loadFor(Order::class);
        $metadata = $metadataManager->get();

        $this->assertArrayHasKey('common', $metadata);
        $this->assertArrayHasKey('properties', $metadata);

        $this->assertArrayHasKey('embedded', $metadata['common']);
        $this->assertArrayHasKey('insert_columns', $metadata['common']);
        $this->assertNotEmpty($metadata['common']['insert_columns']);

        $this->assertNotEmpty($metadata['common']);
        $this->assertNotEmpty($metadata['common']['embedded']);
        $this->assertNotEmpty($metadata['properties']);
    }

    public function testInvalidBaseEntityAnnotationException(): void
    {
        /** @var MetadataManagerInterface $metadataManager */
        $metadataManager = $this->getCertain(MetadataManagerInterface::class);

        $class = InvalidEntityWithCommonAndEmbeddedAnnotation::class;

        $this->expectException(InvalidBaseEntityAnnotationException::class);

        $metadataManager->loadFor($class);
    }

    public function testInvalidAnnotationsCombinationException(): void
    {
        /** @var MetadataManagerInterface $metadataManager */
        $metadataManager = $this->getCertain(MetadataManagerInterface::class);

        $class = InvalidEntityWithPropertyThatHasPropertyColumnAndEmbeddedPropertyAnnotation::class;

        $this->expectException(InvalidAnnotationsCombination::class);

        $metadataManager->loadFor($class);
    }

    public function testExceptionOnParseMetadataOfEntityWithoutCommonInfoAnnotation(): void
    {
        /** @var MetadataManagerInterface $metadataManager */
        $metadataManager = $this->getCertain(MetadataManagerInterface::class);

        $class = InvalidEntityWithoutAnnotations::class;

        $this->expectException(InvalidBaseEntityAnnotationException::class);

        $metadataManager->loadFor($class);
    }

    public function testExceptionOnLoadMetadataForUnExistedClass(): void
    {
        /** @var MetadataManagerInterface $metadataManager */
        $metadataManager = $this->getCertain(MetadataManagerInterface::class);

        $this->expectException(InvalidArgumentException::class);
        $metadataManager->loadFor('Entity\History');
    }

    public function testInvalidEmbeddedEntityAnnotationException(): void
    {
        /** @var MetadataManagerInterface $metadataManager */
        $metadataManager = $this->getCertain(MetadataManagerInterface::class);

        $this->expectException(InvalidEmbeddedEntityAnnotationException::class);
        $metadataManager->loadFor(InvalidEntityThatReferenceToInvalidEmbedded::class);
    }

    public function testExceptionOnPropertyWithInvalidEvaluationRule(): void
    {
        /** @var MetadataManagerInterface $metadataManager */
        $metadataManager = $this->getCertain(MetadataManagerInterface::class);

        $this->expectException(InvalidArgumentException::class);
        $metadataManager->loadFor(EntityWithPropertyMarkedWithInvalidEvaluationRuleAnnotation::class);
    }

    public function testNullableAnnotationException(): void
    {
        /** @var MetadataManagerInterface $metadataManager */
        $metadataManager = $this->getCertain(MetadataManagerInterface::class);

        $this->expectException(NullableAnnotationException::class);
        $metadataManager->loadFor(EntityWithNullablePropertyWithoutDefaultValue::class);
    }

    public function testMetadataVisitorPropertyInfoPlain(): void
    {
        /** @var MetadataManagerInterface $metadataManager */
        $metadataManager = $this->getCertain(MetadataManagerInterface::class);
        $metadataManager->loadFor(Order::class);

        $propertyInfoPlain = $metadataManager->accept(new PropertyInfoVisitor('webmasterLanding'))->get();

        $this->assertInstanceOf(PropertyInfoPlain::class, $propertyInfoPlain);
    }

    public function testMetadataVisitorCommonInfoPlain(): void
    {
        /** @var MetadataManagerInterface $metadataManager */
        $metadataManager = $this->getCertain(MetadataManagerInterface::class);
        $metadataManager->loadFor(Order::class);

        $commonInfoPlain = $metadataManager->accept(new CommonInfoVisitor())->get();

        $this->assertInstanceOf(CommonInfoPlain::class, $commonInfoPlain);
    }
}

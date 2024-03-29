<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Validation\Unit\Mapping;

use Chubbyphp\Validation\Mapping\ValidationMappingProviderInterface;
use Chubbyphp\Validation\Mapping\ValidationMappingProviderRegistry;
use Chubbyphp\Validation\ValidatorLogicException;
use Doctrine\Persistence\Proxy;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Validation\Mapping\ValidationMappingProviderRegistry
 *
 * @internal
 */
final class ValidationMappingProviderRegistryTest extends TestCase
{
    public function testGetObjectMapping(): void
    {
        $object = $this->getObject();

        $registry = new ValidationMappingProviderRegistry([
            $this->getValidationObjectMapping(),
        ]);

        $mapping = $registry->provideMapping($object::class);

        self::assertInstanceOf(ValidationMappingProviderInterface::class, $mapping);
    }

    public function testGetMissingObjectMapping(): void
    {
        self::expectException(ValidatorLogicException::class);
        self::expectExceptionMessage('There is no mapping for class: "stdClass"');

        $registry = new ValidationMappingProviderRegistry([]);

        $registry->provideMapping((new \stdClass())::class);
    }

    public function testGetObjectMappingFromDoctrineProxy(): void
    {
        $object = $this->getProxyObject();

        $registry = new ValidationMappingProviderRegistry([
            $this->getValidationProxyObjectMapping(),
        ]);

        $mapping = $registry->provideMapping($object::class);

        self::assertInstanceOf(ValidationMappingProviderInterface::class, $mapping);
    }

    private function getValidationObjectMapping(): ValidationMappingProviderInterface
    {
        /** @var MockObject|ValidationMappingProviderInterface $objectMapping */
        $objectMapping = $this->getMockBuilder(ValidationMappingProviderInterface::class)
            ->getMockForAbstractClass()
        ;

        $object = $this->getObject();

        $objectMapping->expects(self::any())->method('getClass')->willReturnCallback(
            static fn () => $object::class
        );

        return $objectMapping;
    }

    private function getValidationProxyObjectMapping(): ValidationMappingProviderInterface
    {
        /** @var MockObject|ValidationMappingProviderInterface $objectMapping */
        $objectMapping = $this->getMockBuilder(ValidationMappingProviderInterface::class)
            ->getMockForAbstractClass()
        ;

        $object = $this->getProxyObject();

        $objectMapping->expects(self::any())->method('getClass')->willReturnCallback(
            static fn () => AbstractManyModel::class
        );

        return $objectMapping;
    }

    /**
     * @return object
     */
    private function getObject()
    {
        return new class() {
            private ?string $name = null;

            /**
             * @return null|string
             */
            public function getName()
            {
                return $this->name;
            }

            public function setName(string $name): self
            {
                $this->name = $name;

                return $this;
            }
        };
    }

    /**
     * @return object
     */
    private function getProxyObject()
    {
        return new class() extends AbstractManyModel implements Proxy {
            /**
             * Initializes this proxy if its not yet initialized.
             *
             * Acts as a no-op if already initialized.
             */
            public function __load(): void {}

            /**
             * Returns whether this proxy is initialized or not.
             *
             * @return bool
             */
            public function __isInitialized() {}
        };
    }
}

abstract class AbstractManyModel
{
    protected ?string $name = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Validation\Unit\Mapping;

use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Validation\Mapping\LazyValidationMappingProvider;
use Chubbyphp\Validation\Mapping\ValidationClassMappingInterface;
use Chubbyphp\Validation\Mapping\ValidationMappingProviderInterface;
use Chubbyphp\Validation\Mapping\ValidationPropertyMappingInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Validation\Mapping\LazyValidationMappingProvider
 *
 * @internal
 */
final class LazyValidationMappingProviderTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        $denormalizationClassMapping = $this->getValidationClassMapping();
        $denormalizationPropertyMappings = [$this->getValidationPropertyMapping()];

        $container = $this->getContainer([
            'service' => $this->getValidationObjectMapping(
                $denormalizationClassMapping,
                $denormalizationPropertyMappings
            ),
        ]);

        $objectMapping = new LazyValidationMappingProvider($container, 'service', \stdClass::class);

        self::assertEquals(\stdClass::class, $objectMapping->getClass());
        self::assertSame($denormalizationClassMapping, $objectMapping->getValidationClassMapping('path'));
        self::assertSame($denormalizationPropertyMappings, $objectMapping->getValidationPropertyMappings('path'));
    }

    private function getContainer(array $services): ContainerInterface
    {
        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockBuilder(ContainerInterface::class)->getMockForAbstractClass();

        $container
            ->expects(self::any())
            ->method('get')
            ->willReturnCallback(static fn (string $id) => $services[$id])
        ;

        return $container;
    }

    /**
     * @param ValidationPropertyMappingInterface[] $denormalizationPropertyMappings
     */
    private function getValidationObjectMapping(
        ?ValidationClassMappingInterface $denormalizationClassMapping,
        array $denormalizationPropertyMappings
    ): ValidationMappingProviderInterface {
        // @var ValidationMappingProviderInterface|MockObject $mapping
        return $this->getMockByCalls(ValidationMappingProviderInterface::class, [
            Call::create('getValidationClassMapping')->with('path')->willReturn($denormalizationClassMapping),
            Call::create('getValidationPropertyMappings')->with('path')->willReturn($denormalizationPropertyMappings),
        ]);
    }

    /**
     * @return MockObject|ValidationClassMappingInterface
     */
    private function getValidationClassMapping(): ValidationClassMappingInterface
    {
        return $this->getMockByCalls(ValidationClassMappingInterface::class);
    }

    /**
     * @return MockObject|ValidationPropertyMappingInterface
     */
    private function getValidationPropertyMapping(): ValidationPropertyMappingInterface
    {
        return $this->getMockByCalls(ValidationPropertyMappingInterface::class);
    }
}

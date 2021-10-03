<?php

declare(strict_types=1);

namespace Chubbyphp\Validation\Mapping;

use Psr\Container\ContainerInterface;

final class LazyValidationMappingProvider implements ValidationMappingProviderInterface
{
    private ContainerInterface $container;

    private string $serviceId;

    private string $class;

    /**
     * @param string $serviceId
     */
    public function __construct(ContainerInterface $container, $serviceId, string $class)
    {
        $this->container = $container;
        $this->serviceId = $serviceId;
        $this->class = $class;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return null|ValidationClassMappingInterface
     */
    public function getValidationClassMapping(string $path)
    {
        return $this->container->get($this->serviceId)->getValidationClassMapping($path);
    }

    /**
     * @return array<int, ValidationPropertyMappingInterface>
     */
    public function getValidationPropertyMappings(string $path): array
    {
        return $this->container->get($this->serviceId)->getValidationPropertyMappings($path);
    }
}

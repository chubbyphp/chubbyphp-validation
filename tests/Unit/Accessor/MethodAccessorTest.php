<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Validation\Unit\Accessor;

use Chubbyphp\Validation\Accessor\MethodAccessor;
use Chubbyphp\Validation\ValidatorLogicException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Validation\Accessor\MethodAccessor
 *
 * @internal
 */
final class MethodAccessorTest extends TestCase
{
    public function testGetValue(): void
    {
        $object = new class() {
            private ?string $name = null;

            public function getName(): string
            {
                return $this->name;
            }

            public function setName(string $name): void
            {
                $this->name = $name;
            }
        };

        $object->setName('Name');

        $accessor = new MethodAccessor('name');

        self::assertSame('Name', $accessor->getValue($object));
    }

    public function testHasValue(): void
    {
        $object = new class() {
            private ?string $name = null;

            public function hasName(): bool
            {
                return (bool) $this->name;
            }

            public function setName(string $name): void
            {
                $this->name = $name;
            }
        };

        $object->setName('Name');

        $accessor = new MethodAccessor('name');

        self::assertTrue($accessor->getValue($object));
    }

    public function testIsValue(): void
    {
        $object = new class() {
            private ?string $name = null;

            public function isName(): bool
            {
                return (bool) $this->name;
            }

            public function setName(string $name): void
            {
                $this->name = $name;
            }
        };

        $object->setName('Name');

        $accessor = new MethodAccessor('name');

        self::assertTrue($accessor->getValue($object));
    }

    public function testMissingGet(): void
    {
        self::expectException(ValidatorLogicException::class);
        self::expectExceptionMessageMatches(
            '/There are no accessible method\(s\) "getName", "hasName", "isName", within class\: "[^"]+"/'
        );

        $object = new class() {};

        $accessor = new MethodAccessor('name');
        $accessor->getValue($object);
    }
}

<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Validation\Unit\Error;

use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Validation\Error\ErrorInterface;
use Chubbyphp\Validation\Error\NestedErrorMessages;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Validation\Error\NestedErrorMessages
 *
 * @internal
 */
final class NestedErrorMessagesTest extends TestCase
{
    use MockByCallsTrait;

    public function testWithoutMessages(): void
    {
        $errorMessages = new NestedErrorMessages([], static fn (string $key, array $arguments) => $key);

        self::assertEquals([], $errorMessages->getMessages());
    }

    public function testWithMessages(): void
    {
        $errors = [
            $this->getError('collection[_all]', 'constraint.collection.all'),
            $this->getError('collection[0].field1', 'constraint.collection0.constraint1'),
            $this->getError('collection[0].field1', 'constraint.collection0.constraint2'),
            $this->getError('collection[1].field1', 'constraint.collection1.constraint1'),
            $this->getError('collection[1].field1', 'constraint.collection1.constraint2'),
        ];

        $errorMessages = new NestedErrorMessages(
            $errors,
            static fn (string $key, array $arguments) => $key
        );

        self::assertEquals([
            'collection' => [
                '_all' => [
                    'constraint.collection.all',
                ],
                0 => [
                    'field1' => [
                        'constraint.collection0.constraint1',
                        'constraint.collection0.constraint2',
                    ],
                ],
                1 => [
                    'field1' => [
                        'constraint.collection1.constraint1',
                        'constraint.collection1.constraint2',
                    ],
                ],
            ],
        ], $errorMessages->getMessages());
    }

    private function getError(string $path, string $key, array $arguments = []): ErrorInterface
    {
        // @var ErrorInterface|MockObject $error
        return $this->getMockByCalls(ErrorInterface::class, [
            Call::create('getPath')->with()->willReturn($path),
            Call::create('getKey')->with()->willReturn($key),
            Call::create('getArguments')->with()->willReturn($arguments),
        ]);
    }
}

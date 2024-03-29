<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Validation\Unit\Constraint;

use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Validation\Constraint\ChoiceConstraint;
use Chubbyphp\Validation\Error\Error;
use Chubbyphp\Validation\ValidatorContextInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Validation\Constraint\ChoiceConstraint
 *
 * @internal
 */
final class ChoiceConstraintTest extends TestCase
{
    use MockByCallsTrait;

    public function testWithNullValue(): void
    {
        $constraint = new ChoiceConstraint(['active', 'inactive']);

        self::assertEquals([], $constraint->validate('choice', null, $this->getContext()));
    }

    /**
     * @dataProvider provideWithChoiceCases
     *
     * @param mixed $choice
     */
    public function testWithChoice(array $choices, $choice): void
    {
        $constraint = new ChoiceConstraint($choices);

        self::assertEquals([], $constraint->validate('choice', $choice, $this->getContext()));
    }

    public static function provideWithChoiceCases(): iterable
    {
        return [
            ['choices' => ['active', 'inactive'], 'choice' => 'active'],
            ['choices' => [true, false], 'choice' => true],
            ['choices' => [1.0, 2.0, 3.0], 'choice' => 1.0],
            ['choices' => [1, 2, 3], 'choice' => 1],
        ];
    }

    /**
     * @dataProvider provideWithInvalidChoiceCases
     *
     * @param mixed $choice
     */
    public function testWithInvalidChoice(array $choices, $choice): void
    {
        $constraint = new ChoiceConstraint($choices);

        $error = new Error(
            'choice',
            'constraint.choice.invalidvalue',
            ['value' => $choice, 'choices' => $this->implode($choices)]
        );

        self::assertEquals([$error], $constraint->validate('choice', $choice, $this->getContext()));
    }

    public static function provideWithInvalidChoiceCases(): iterable
    {
        return [
            ['choices' => ['active', 'inactive'], 'choice' => 'test'],
            ['choices' => [true], 'choice' => false],
            ['choices' => [1.0, 2.0, 3.0], 'choice' => 4.0],
            ['choices' => [1, 2, 3], 'choice' => 4],
        ];
    }

    private function implode(array $choices): string
    {
        return implode(', ', $choices);
    }

    private function getContext(): ValidatorContextInterface
    {
        // @var ValidatorContextInterface|MockObject $context
        return $this->getMockByCalls(ValidatorContextInterface::class);
    }
}

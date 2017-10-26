<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Validation\Constraint;

use Chubbyphp\Validation\Constraint\NotNullConstraint;
use Chubbyphp\Validation\Error\Error;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Validation\Constraint\NotNullConstraint
 */
final class NotNullConstraintTest extends TestCase
{
    public function testWithNullValue()
    {
        $constraint = new NotNullConstraint();

        $error = new Error('notnull', 'constraint.notnull.null');

        self::assertEquals([$error], $constraint->validate('notnull', null));
    }

    public function testWithBool()
    {
        $constraint = new NotNullConstraint();

        self::assertEquals([], $constraint->validate('notnull', false));
    }

    public function testWithInteger()
    {
        $constraint = new NotNullConstraint();

        self::assertEquals([], $constraint->validate('notnull', 5));
    }

    public function testWithFloat()
    {
        $constraint = new NotNullConstraint();

        self::assertEquals([], $constraint->validate('notnull', 5.5));
    }

    public function testWithString()
    {
        $constraint = new NotNullConstraint();

        self::assertEquals([], $constraint->validate('notnull', ''));
    }

    public function testWithArray()
    {
        $constraint = new NotNullConstraint();

        self::assertEquals([], $constraint->validate('notnull', []));
    }

    public function testWithStdClass()
    {
        $constraint = new NotNullConstraint();

        self::assertEquals([], $constraint->validate('notnull', new \stdClass()));
    }
}

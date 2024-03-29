<?php

declare(strict_types=1);

namespace Chubbyphp\Validation\Constraint;

use Chubbyphp\Validation\Error\Error;
use Chubbyphp\Validation\Error\ErrorInterface;
use Chubbyphp\Validation\ValidatorContextInterface;
use Chubbyphp\Validation\ValidatorInterface;

final class EmailConstraint implements ConstraintInterface
{
    /**
     * @param mixed $value
     *
     * @return array<ErrorInterface>
     */
    public function validate(
        string $path,
        $value,
        ValidatorContextInterface $context,
        ?ValidatorInterface $validator = null
    ) {
        if (null === $value || '' === $value) {
            return [];
        }

        if (!\is_scalar($value) && !(\is_object($value) && method_exists($value, '__toString'))) {
            return [new Error(
                $path,
                'constraint.email.invalidtype',
                ['type' => get_debug_type($value)]
            )];
        }

        $value = (string) $value;

        if (false === filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return [new Error($path, 'constraint.email.invalidformat', ['value' => $value])];
        }

        return [];
    }
}

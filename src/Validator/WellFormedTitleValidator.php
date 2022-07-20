<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class WellFormedTitleValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var WellFormedTitle $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new \Exception('WellFormedTitle constraints applies only to string data type.');
        }

        if ($value !== ucfirst($value)) {
            $this->context->buildViolation($constraint->ucfirstMessage)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }

        if ('.' !== substr($value, -1)) {
            $this->context->buildViolation($constraint->dotEndingMessage)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}

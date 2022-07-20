<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class WellFormedTitle extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $ucfirstMessage = 'The value "{{ value }}" should start upper cased.';
    public $dotEndingMessage = 'The value "{{ value }}" should end with a dot.';
}

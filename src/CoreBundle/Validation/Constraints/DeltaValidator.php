<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/13/17
 * Time: 12:44 PM
 */

namespace CoreBundle\Validation\Constraints;


use DBlackborough\Quill\Parser\Html;
use DBlackborough\Quill\Render;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DeltaValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {

        try{
            $parser = new Html();
            $parser->load($value);
            if(!$parser->parse())
            {
                $this->context->buildViolation('Invalid Delta')
                    ->addViolation();
            }
        } catch ( \Exception $e){
            $this->context->buildViolation($e->getMessage())
                ->addViolation();
        }

    }
}
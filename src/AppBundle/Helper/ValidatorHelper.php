<?php

namespace AppBundle\Helper;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidatorHelper
{
    /**
     * @param ConstraintViolationListInterface $constraintViolationList
     * @return array
     */
    public function constraintViolationListToArray(ConstraintViolationListInterface $constraintViolationList)
    {
        $errors = [];

        foreach ($constraintViolationList AS $error) {
            $errors[$error->getPropertyPath()] = $error->getMessage();
        }

        return $errors;
    }
}

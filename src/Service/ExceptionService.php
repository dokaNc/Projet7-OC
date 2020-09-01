<?php


namespace App\Service;


use App\Exception\ResourceValidationException;
use Symfony\Component\Validator\ConstraintViolationList;


class ExceptionService
{
     /**
      * @param ConstraintViolationList $violations
      * @throws ResourceValidationException
      */
    public function invalidJson(ConstraintViolationList $violations)
    {
        if (count($violations)) {
            $message = 'The JSON sent contains invalid data: ';
            foreach ($violations as $violation) {
                $message .= sprintf(
                    "Field '%s': %s ",
                    $violation->getPropertyPath(),
                    $violation->getMessage());
            }

            throw new ResourceValidationException($message);
        }
    }
}
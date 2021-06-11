<?php

namespace GislerCMS\Validator;

use Laminas\Validator\AbstractValidator;

/**
 * Class ValidJson
 * @package GislerCMS\Validator
 */
class ValidJson extends AbstractValidator
{
    /**
     * Returns true if the json is valid
     *
     * @param  string $value
     * @return bool
     */
    public function isValid($value): bool
    {
        json_decode($value);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}

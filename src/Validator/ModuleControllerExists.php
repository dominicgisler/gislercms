<?php

namespace GislerCMS\Validator;

use GislerCMS\Controller\Module\AbstractModuleController;
use Zend\Validator\AbstractValidator;

/**
 * Class ModuleControllerExists
 * @package GislerCMS\Validator
 */
class ModuleControllerExists extends AbstractValidator
{
    /**
     * Returns true if the module controller exists
     *
     * @param  string $value
     * @return bool
     */
    public function isValid($value)
    {
        $this->setValue($value);

        if (!in_array($value, self::getModuleControllers())) {
            $this->error('module controller does not exist');
        }

        if ($this->getMessages()) {
            return false;
        }

        return true;
    }

    public static function getModuleControllers(): array
    {
        $conts = [];
        foreach (glob(__DIR__ . '/../Controller/Module/*.php') as $file) {
            $class = basename($file, '.php');
            $cont = '\\GislerCMS\\Controller\\Module\\' . $class;
            if (is_subclass_of($cont, AbstractModuleController::class)) {
                $conts[] = $class;
            }
        }
        return $conts;
    }
}

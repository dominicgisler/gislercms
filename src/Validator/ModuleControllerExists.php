<?php

namespace GislerCMS\Validator;

use GislerCMS\Controller\Module\AbstractModuleController;
use Laminas\Validator\AbstractValidator;

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

        if (!array_key_exists($value, self::getModuleControllers())) {
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
            /** @var AbstractModuleController $cont */
            $cont = '\\GislerCMS\\Controller\\Module\\' . $class;
            if (is_subclass_of($cont, AbstractModuleController::class)) {
                $conts[$class] = json_encode($cont::getExampleConfig(), JSON_PRETTY_PRINT);
            }
        }
        return $conts;
    }
}

<?php

namespace GislerCMS\Entity;

use Zend\Session\Container;

/**
 * Class SessionHelper
 * @package GislerCMS\Entity
 */
class SessionHelper
{
    const CNT_KEY = 'gislercms';

    /**
     * @var Container
     */
    private static $container;

    public static function getContainer(): Container
    {
        if (is_null(self::$container)) {
            self::$container = new Container(self::CNT_KEY);
        }

        return self::$container;
    }
}

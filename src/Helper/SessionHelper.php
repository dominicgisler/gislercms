<?php

namespace GislerCMS\Helper;

use Laminas\Session\Container;

/**
 * Class SessionHelper
 * @package GislerCMS\Helper
 */
class SessionHelper
{
    const CNT_KEY = 'gislercms';

    /**
     * @var ?Container
     */
    private static $container;

    /**
     * @return Container
     */
    public static function getContainer(): Container
    {
        if (is_null(self::$container)) {
            self::$container = new Container(self::CNT_KEY);
        }

        return self::$container;
    }
}

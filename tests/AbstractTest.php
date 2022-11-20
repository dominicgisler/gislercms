<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\Panther\Client;

abstract class AbstractTest extends TestCase
{
    protected string $url = 'http://gislercms';

    /**
     * @return Client
     */
    protected function getClient(): Client
    {
        return Client::createChromeClient(null, ['--no-sandbox', '--headless', '--disable-dev-shm-usage']);
    }
}

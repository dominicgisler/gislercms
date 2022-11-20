<?php

use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeoutException;
use Facebook\WebDriver\WebDriverBy;

final class SetupTest extends AbstractTest
{
    /**
     * @throws NoSuchElementException
     * @throws TimeoutException
     */
    public function testCanSetup(): void
    {
        $client = $this->getClient();
        $crawler = $client->request('GET', $this->url . '/admin/setup');

        $this->assertEquals('Setup - GislerCMS', $client->getTitle());

        $crawler->findElement(WebDriverBy::cssSelector('input[name=db_host]'))->clear()->sendKeys('mariadb-test');
        $crawler->findElement(WebDriverBy::cssSelector('input[name=db_database]'))->clear()->sendKeys('gcms_data');
        $crawler->findElement(WebDriverBy::cssSelector('input[name=db_user]'))->clear()->sendKeys('gcms_user');
        $crawler->findElement(WebDriverBy::cssSelector('input[name=db_password]'))->clear()->sendKeys('gcms_pass');

        $crawler->findElement(WebDriverBy::className('btn-primary'))->click();
        $client->waitForVisibility('button[type=submit]');

        $crawler->findElement(WebDriverBy::cssSelector('input[name=user_username]'))->clear()->sendKeys('admin');
        $crawler->findElement(WebDriverBy::cssSelector('input[name=user_firstname]'))->clear()->sendKeys('Admin');
        $crawler->findElement(WebDriverBy::cssSelector('input[name=user_lastname]'))->clear()->sendKeys('User');
        $crawler->findElement(WebDriverBy::cssSelector('input[name=user_email]'))->clear()->sendKeys('info@example.com');
        $crawler->findElement(WebDriverBy::cssSelector('input[name=user_password]'))->clear()->sendKeys('admin');

        $crawler->findElement(WebDriverBy::cssSelector('button[type=submit]'))->click();

        sleep(3);
        $client->takeScreenshot('test.png');
    }
}

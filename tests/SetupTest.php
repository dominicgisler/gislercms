<?php

use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeoutException;
use Facebook\WebDriver\WebDriverBy;
use Symfony\Component\Panther\Client;

final class SetupTest extends AbstractTest
{
    /**
     * @return Client
     * @throws NoSuchElementException
     * @throws TimeoutException
     */
    public function testCanSetup(): Client
    {
        $client = $this->getClient();
        $crawler = $client->request('GET', $this->url . '/admin/setup');

        $this->assertEquals('Setup - GislerCMS', $client->getTitle());

        $crawler->findElement(WebDriverBy::cssSelector('input[name=db_host]'))->clear()->sendKeys('mariadb-test');
        $crawler->findElement(WebDriverBy::cssSelector('input[name=db_database]'))->clear()->sendKeys('gcms_data');
        $crawler->findElement(WebDriverBy::cssSelector('input[name=db_user]'))->clear()->sendKeys('gcms_user');
        $crawler->findElement(WebDriverBy::cssSelector('input[name=db_password]'))->clear()->sendKeys('gcms_pass');

        $crawler->findElement(WebDriverBy::className('btn-primary'))->click();
        $crawler = $client->waitForVisibility('button[type=submit]');

        $crawler->findElement(WebDriverBy::cssSelector('input[name=user_username]'))->clear()->sendKeys('admin');
        $crawler->findElement(WebDriverBy::cssSelector('input[name=user_firstname]'))->clear()->sendKeys('Admin');
        $crawler->findElement(WebDriverBy::cssSelector('input[name=user_lastname]'))->clear()->sendKeys('User');
        $crawler->findElement(WebDriverBy::cssSelector('input[name=user_email]'))->clear()->sendKeys('info@example.com');
        $crawler->findElement(WebDriverBy::cssSelector('input[name=user_password]'))->clear()->sendKeys('admin');

        $crawler->findElement(WebDriverBy::cssSelector('button[type=submit]'))->click();

        $crawler = $client->waitForVisibility('div.alert');
        $this->assertStringContainsString('Setup successful', $crawler->findElement(WebDriverBy::cssSelector('div.alert'))->getText());

        return $client;
    }

    /**
     * @depends testCanSetup
     * @param Client $client
     * @return void
     * @throws NoSuchElementException
     * @throws TimeoutException
     */
    public function testCanReadPage(Client $client): void
    {
        $client->request('GET', $this->url);

        $crawler = $client->waitForVisibility('div.container');
        $this->assertEquals('Home', $client->getTitle());
        $this->assertEquals('Welcome to your Website!', $crawler->findElement(WebDriverBy::cssSelector('main h1'))->getText());
        $this->assertStringContainsString('Lorem ipsum dolor sit amet', $crawler->findElement(WebDriverBy::cssSelector('main'))->getText());
    }

    /**
     * @depends testCanSetup
     * @param Client $client
     * @return void
     * @throws NoSuchElementException
     * @throws TimeoutException
     */
    public function testCanLogin(Client $client): void
    {
        $client->request('GET', $this->url . '/admin');

        $crawler = $client->waitForVisibility('form.form-signin');
        $this->assertEquals('Login - GislerCMS', $client->getTitle());

        $crawler->findElement(WebDriverBy::cssSelector('input[name=username]'))->clear()->sendKeys('admin');
        $crawler->findElement(WebDriverBy::cssSelector('input[name=password]'))->clear()->sendKeys('admin');
        $crawler->findElement(WebDriverBy::cssSelector('button[type=submit]'))->click();

        $crawler = $client->waitForVisibility('.container-fluid');
        $this->assertEquals('Dashboard - GislerCMS', $client->getTitle());
        $this->assertEquals('Dashboard', $crawler->findElement(WebDriverBy::cssSelector('main h1'))->getText());
    }
}

<?php

namespace GislerCMS\Controller;

use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use GislerCMS\Helper\SessionHelper;
use GislerCMS\Model\Client;
use GislerCMS\Model\DbModel;
use GislerCMS\Model\Module;
use GislerCMS\Model\PageTranslation;
use GislerCMS\Model\Session;
use GislerCMS\Model\Visit;
use GislerCMS\Model\Widget;
use GislerCMS\Model\WidgetTranslation;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Route;

/**
 * Class AbstractController
 * @package GislerCMS\Controller
 */
abstract class AbstractController
{
    /**
     * @var \Slim\Container|\Psr\Container\ContainerInterface
     */
    private $container;

    /**
     * @param \Slim\Container|\Psr\Container\ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container = $container;
        DbModel::init($this->get('pdo'));
    }

    /**
     * @param string $var
     * @return mixed
     */
    protected function get($var)
    {
        return $this->container->get($var);
    }

    /**
     * @param Request $request
     * @param ResponseInterface $response
     * @param string $template
     * @param array $data
     * @return Response
     */
    protected function render(Request $request, ResponseInterface $response, string $template, array $data = []): Response
    {
        /** @var Route $route */
        $route = $request->getAttribute('route');
        $arr = [
            'route' => $route->getName(),
            'widget' => new Widget(),
            'widget_translation' => new WidgetTranslation(),
            'module' => new Module()
        ];
        return $this->get('view')->render($response, $template, array_merge($arr, $data));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param PageTranslation $pTrans
     * @return ResponseInterface
     * @throws \Exception
     */
    protected function trackPage(Request $request, Response $response, PageTranslation $pTrans): ResponseInterface
    {
        $cUuid = FigRequestCookies::get($request, 'client', $this->uuidv4())->getValue();
        $sUuid = SessionHelper::getContainer()->offsetGet('session_uuid') ?: $this->uuidv4();

        $client = Client::getClient($cUuid);
        $client->setUuid($cUuid);
        $response = FigResponseCookies::set($response, SetCookie::create('client')->withValue($cUuid)->withExpires(strtotime('+1 year')));

        $session = Session::getSession($sUuid);
        $session->setUuid($sUuid);
        SessionHelper::getContainer()->offsetSet('session_uuid', $sUuid);

        $browserData = parse_user_agent();
        $session->setPlatform($browserData['platform'] ?: '');
        $session->setBrowser($browserData['browser'] ?: '');

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $ip = preg_replace('/[0-9]+\z/', '0', $ip);
        $session->setIp($ip);

        $client = $client->save();
        if ($client->getClientId() > 0) {
            $session->setClient($client);
            $session = $session->save();
        }

        $visit = new Visit();
        $visit->setPageTranslation($pTrans);
        $visit->setSession($session);
        $visit->save();

        return $response;
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function uuidv4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}

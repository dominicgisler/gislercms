<?php

namespace GislerCMS\Controller;

use Dflydev\FigCookies\FigRequestCookies;
use GislerCMS\Model\Client;
use GislerCMS\Model\DbModel;
use GislerCMS\Model\PageTranslation;
use GislerCMS\Model\Widget;
use GislerCMS\Model\WidgetTranslation;
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
     * @param Response $response
     * @param string $template
     * @param array $data
     * @return Response
     */
    protected function render(Request $request, Response $response, string $template, array $data = []): Response
    {
        /** @var Route $route */
        $route = $request->getAttribute('route');
        $arr = [
            'route' => $route->getName(),
            'widget' => new Widget(),
            'widget_translation' => new WidgetTranslation()
        ];
        return $this->get('view')->render($response, $template, array_merge($arr, $data));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param PageTranslation $pTrans
     * @return Response
     * @throws \Exception
     */
    protected function trackPage(Request $request, Response $response, PageTranslation $pTrans): Response
    {
        $cUuid = FigRequestCookies::get($request, 'client', $this->uuidv4())->getValue();

        $browserData = parse_user_agent();

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $ip = preg_replace('/[0-9]+\z/', '0', $ip);

        $client = new Client(0, $cUuid);

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

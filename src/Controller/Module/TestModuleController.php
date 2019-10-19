<?php

namespace GislerCMS\Controller\Module;

class TestModuleController extends AbstractModuleController {
    public function onGet($request)
    {
        return $this->view->fetch('module/test/get.twig');
    }

    public function onPost($request)
    {
        return $this->view->fetch('module/test/post.twig', ['data' => $request->getParsedBody()]);
    }
}
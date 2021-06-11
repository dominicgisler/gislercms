<?php

namespace GislerCMS\Controller\Admin\Module\Manage;

use Exception;
use GislerCMS\Model\GuestbookEntry;
use GislerCMS\Model\Module;
use GislerCMS\Validator\ValidJson;
use Slim\Http\Request;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilterInterface;

/**
 * Class GuestbookController
 * @package GislerCMS\Admin\Module\Manage
 */
class GuestbookController extends AbstractController
{
    /**
     * @param Module $mod
     * @param Request $request
     * @return string
     * @throws Exception
     */
    public function manage(Module $mod, Request $request): string
    {
        $errors = [];
        $msg = false;

        if ($request->isPost()) {
            if (is_null($request->getParsedBodyParam('delete')) && is_null($request->getParsedBodyParam('delete_entry'))) {
                $data = $request->getParsedBody();
                $filter = $this->getInputFilter();
                $filter->setData($data);
                if (!$filter->isValid()) {
                    $errors = array_merge($errors, array_keys($filter->getMessages()));
                }
                $data = $filter->getValues();
                $mod->setConfig($data['config']);

                if (sizeof($errors) == 0) {
                    $res = $mod->save();
                    if (!is_null($res)) {
                        $msg = 'save_success';
                    } else {
                        $msg = 'save_error';
                    }
                } else {
                    $msg = 'invalid_input';
                }
            } else if (!is_null($request->getParsedBodyParam('delete_entry'))) {
                $entryId = intval($request->getParsedBodyParam('delete_entry'));
                $entry = GuestbookEntry::get($entryId);
                if ($entry->getGuestbookEntryId() > 0) {
                    $entry->delete();
                }
            }
        }

        $config = json_decode($mod->getConfig(), true);
        $entries = [];
        if (!empty($config['identifier'])) {
            $entries = GuestbookEntry::getGuestbookEntries($config['identifier']);
        }

        return $this->view->fetch('admin/module/manage/guestbook.twig', [
            'module' => $mod,
            'errors' => $errors,
            'message' => $msg,
            'entries' => $entries
        ]);
    }

    /**
     * @return InputFilterInterface
     */
    private function getInputFilter(): InputFilterInterface
    {
        $factory = new Factory();
        return $factory->createInputFilter([
            [
                'name' => 'config',
                'required' => true,
                'filters' => [],
                'validators' => [
                    new ValidJson()
                ]
            ]
        ]);
    }
}

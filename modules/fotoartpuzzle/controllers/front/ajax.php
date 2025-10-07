<?php

class FotoartpuzzleAjaxModuleFrontController extends ModuleFrontController
{
    /**
     * @var FAPPuzzleRepository
     */
    protected $repository;

    public function __construct()
    {
        parent::__construct();
        $this->ajax = true;
        $this->repository = new FAPPuzzleRepository();
    }

    public function initContent()
    {
        parent::initContent();
        $action = Tools::getValue('action');
        switch ($action) {
            case 'config':
                $this->sendJsonResponse([
                    'success' => true,
                    'config' => FAPConfiguration::getFrontConfig(),
                ]);
                break;
            case 'getPuzzles':
                $this->sendJsonResponse([
                    'success' => true,
                    'puzzles' => $this->repository->getFormats(true),
                ]);
                break;
            case 'getBoxes':
                $this->sendJsonResponse([
                    'success' => true,
                    'boxes' => $this->repository->getBoxes(true),
                ]);
                break;
            default:
                $this->sendJsonResponse([
                    'success' => false,
                    'message' => $this->module->l('Unknown action'),
                ]);
        }
    }

    /**
     * Output JSON payload and terminate execution.
     *
     * @param array $payload
     */
    protected function sendJsonResponse(array $payload)
    {
        header('Content-Type: application/json; charset=utf-8');
        $this->ajaxDie(json_encode($payload));
    }
}

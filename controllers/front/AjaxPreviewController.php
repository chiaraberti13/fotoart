<?php
require_once _PS_MODULE_DIR_ . 'art_puzzle/classes/ArtPuzzleAjaxErrorHandler.php';

/**
 * Controller: art_puzzle/controllers/front/AjaxPreviewController.php
 * Gestisce le richieste AJAX per generare anteprime
 */

class ArtPuzzleAjaxPreviewModuleFrontController extends ModuleFrontController
{
    public $ajax = true;
    public $display_header = false;
    public $display_footer = false;

    public function init()
    {
        parent::init();

        ArtPuzzleAjaxErrorHandler::register(function (\Throwable $throwable) {
            $this->handleAjaxThrowable($throwable);
        });

        // Verifica token di sicurezza
        if (!Tools::getIsset('token') || Tools::getValue('token') !== Tools::getToken(false)) {
            $this->ajaxError('Token di sicurezza non valido');
        }
    }

    public function postProcess()
    {
        try {
            $action = Tools::getValue('action');

            if (!$action) {
                $this->ajaxError('Nessuna azione specificata');
            }

            switch ($action) {
                case 'generateBoxPreview':
                    $this->generateBoxPreview();
                    break;

                case 'generatePuzzlePreview':
                    $this->generatePuzzlePreview();
                    break;

                case 'generateSummaryPreview':
                    $this->generateSummaryPreview();
                    break;

                default:
                    $this->ajaxError('Azione non riconosciuta: ' . $action);
            }
        } catch (\Throwable $throwable) {
            $this->handleAjaxThrowable($throwable);
        }
    }

    private function generateBoxPreview()
    {
        try {
            $result = [
                'success' => true,
                'preview_url' => _MODULE_DIR_ . 'art_puzzle/views/img/anteprima_scatola.png'
            ];
            $this->ajaxDie(json_encode($result));
        } catch (\Throwable $throwable) {
            $this->handleAjaxThrowable($throwable);
        }
    }

    private function generatePuzzlePreview()
    {
        try {
            $uploaded_image = $this->context->cookie->__get('art_puzzle_uploaded_image');

            if (!$uploaded_image || !file_exists(_PS_MODULE_DIR_ . 'art_puzzle/upload/' . $uploaded_image)) {
                $this->ajaxError('Immagine puzzle non disponibile');
            }

            $result = [
                'success' => true,
                'preview_url' => _MODULE_DIR_ . 'art_puzzle/upload/' . $uploaded_image
            ];
            $this->ajaxDie(json_encode($result));
        } catch (\Throwable $throwable) {
            $this->handleAjaxThrowable($throwable);
        }
    }

    private function generateSummaryPreview()
    {
        try {
            $result = [
                'success' => true,
                'summary_html' => '<div class="summary-box">Preview riepilogo generata.</div>'
            ];
            $this->ajaxDie(json_encode($result));
        } catch (\Throwable $throwable) {
            $this->handleAjaxThrowable($throwable);
        }
    }

    private function ajaxError($message)
    {
        if (class_exists('ArtPuzzleLogger')) {
            ArtPuzzleLogger::log('[AJAX PREVIEW] ' . $message);
        }
        $this->ajaxDie(json_encode([
            'success' => false,
            'message' => $message
        ]));
    }

    private function handleAjaxThrowable(\Throwable $throwable)
    {
        $this->ajaxError($throwable->getMessage());
    }
}

<?php
/**
 * Controller: art_puzzle/controllers/front/AjaxPreviewController.php
 * Gestisce le richieste AJAX per generare anteprime
 */

class ArtPuzzlePreviewModuleFrontController extends ModuleFrontController
{
    public $ajax = true;
    public $display_header = false;
    public $display_footer = false;

    public function init()
    {
        parent::init();

        // Verifica token di sicurezza
        if (!Tools::getIsset('token') || Tools::getValue('token') !== Tools::getToken(false)) {
            $this->ajaxError('Token di sicurezza non valido');
        }
    }

    public function postProcess()
    {
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
    }

    private function generateBoxPreview()
    {
        // Simulazione: in produzione generare immagine reale
        $result = [
            'success' => true,
            'preview_url' => _MODULE_DIR_ . 'art_puzzle/views/img/anteprima_scatola.png'
        ];
        $this->ajaxDie(json_encode($result));
    }

    private function generatePuzzlePreview()
    {
        $uploaded_image = $this->context->cookie->__get('art_puzzle_uploaded_image');

        if (!$uploaded_image || !file_exists(_PS_MODULE_DIR_ . 'art_puzzle/upload/' . $uploaded_image)) {
            $this->ajaxError('Immagine puzzle non disponibile');
        }

        $result = [
            'success' => true,
            'preview_url' => _MODULE_DIR_ . 'art_puzzle/upload/' . $uploaded_image
        ];
        $this->ajaxDie(json_encode($result));
    }

    private function generateSummaryPreview()
    {
        // Esempio statico, in futuro generare immagine composita con box+foto
        $result = [
            'success' => true,
            'summary_html' => '<div class="summary-box">Preview riepilogo generata.</div>'
        ];
        $this->ajaxDie(json_encode($result));
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
}

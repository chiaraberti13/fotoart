<?php
/**
 * Controller: art_puzzle/controllers/front/SummaryController.php
 * Mostra il riepilogo finale e genera l'anteprima per il PDF
 */

class ArtPuzzleSummaryModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        if (!Tools::isSubmit('confirm_summary')) {
            $this->logAndError('Devi confermare la personalizzazione prima di procedere.');
            return;
        }

        $image = $this->context->cookie->__get('art_puzzle_uploaded_image');
        $format = $this->context->cookie->__get('art_puzzle_selected_format');
        $box_text = $this->context->cookie->__get('art_puzzle_box_text');

        if (!$image || !$format) {
            $this->logAndError('Dati di personalizzazione incompleti.');
            return;
        }

        require_once _PS_MODULE_DIR_ . 'art_puzzle/classes/PDFGeneratorPuzzle.php';
        $pdfGenerator = new PDFGeneratorPuzzle();

        $pdfPath = $pdfGenerator->generateCustomerPreview($image, $format, $box_text);

        if (!$pdfPath || !file_exists($pdfPath)) {
            $this->logAndError('Errore nella generazione del PDF.');
            return;
        }

        // Salva in cookie il nome del PDF
        $this->context->cookie->__set('art_puzzle_summary_pdf', basename($pdfPath));

        Tools::redirect('index.php?controller=cart');
    }

    public function initContent()
    {
        parent::initContent();

        $image = $this->context->cookie->__get('art_puzzle_uploaded_image');
        $format = $this->context->cookie->__get('art_puzzle_selected_format');
        $box_text = $this->context->cookie->__get('art_puzzle_box_text');

        $this->context->smarty->assign([
            'summary_image' => $image,
            'summary_format' => $format,
            'summary_box_text' => $box_text,
            'confirm_url' => $this->context->link->getModuleLink('art_puzzle', 'summary')
        ]);

        $this->setTemplate('module:art_puzzle/views/templates/front/summary.tpl');
    }

    private function logAndError($message)
    {
        $this->errors[] = $this->module->l($message);
        if (class_exists('ArtPuzzleLogger')) {
            ArtPuzzleLogger::log('[SUMMARY] ' . $message);
        }
    }
}

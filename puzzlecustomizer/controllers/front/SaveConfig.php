<?php
/**
 * Salva configurazione del puzzle nella sessione/carrello.
 */

class PuzzlecustomizerSaveConfigModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();
        header('Content-Type: application/json');

        try {
            $this->processSave();
        } catch (Exception $e) {
            $this->ajaxDie(json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]));
        }
    }

    protected function processSave()
    {
        $payload = json_decode(Tools::file_get_contents('php://input'), true);
        if (!$payload || !isset($payload['token'])) {
            throw new Exception($this->module->l('Dati non validi.'));
        }

        $token = pSQL($payload['token']);
        $filename = isset($payload['file']) ? basename($payload['file']) : null;
        $tempPath = _PS_MODULE_DIR_ . $this->module->name . '/uploads/temp/' . $filename;
        $finalDir = _PS_MODULE_DIR_ . $this->module->name . '/uploads/customizations/';
        $finalPath = $finalDir . $filename;

        if (!is_file($tempPath)) {
            throw new Exception($this->module->l('File temporaneo non trovato.'));
        }

        $processor = new ImageProcessor();
        $processor->moveToCustomizationDirectory($tempPath, $finalPath);

        $customization = new PuzzleCustomization();
        $customization->id_cart = (int) $this->context->cart->id;
        $customization->token = $token;
        $customization->configuration = json_encode($payload);
        $customization->image_path = $filename;
        $customization->status = 'saved';
        $customization->created_at = date('Y-m-d H:i:s');
        $customization->updated_at = date('Y-m-d H:i:s');
        $customization->save();

        $this->ajaxDie(json_encode([
            'success' => true,
            'id' => (int) $customization->id,
        ]));
    }
}

<?php
/**
 * Controller: art_puzzle/controllers/front/CartController.php
 * Aggiunge i dati della personalizzazione al carrello PrestaShop
 */

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Art_PuzzleCartModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        $id_product = (int) Tools::getValue('id_product');

        if (!$id_product || !Validate::isUnsignedId($id_product)) {
            $this->logAndError('ID prodotto non valido.');
            return;
        }

        $custom_data = [
            'image' => $this->context->cookie->__get('art_puzzle_uploaded_image'),
            'format' => $this->context->cookie->__get('art_puzzle_selected_format'),
            'box_text' => $this->context->cookie->__get('art_puzzle_box_text'),
            'pdf' => $this->context->cookie->__get('art_puzzle_summary_pdf')
        ];

        // Validazione file
        if (!file_exists(_PS_MODULE_DIR_ . 'art_puzzle/upload/' . $custom_data['image']) ||
            !file_exists(_PS_MODULE_DIR_ . 'art_puzzle/upload/' . $custom_data['pdf'])) {
            $this->logAndError('File immagine o PDF mancante.');
            return;
        }

        // Invia email se richiesto da configurazione
        $this->sendPuzzleEmails($custom_data);

        $customization_text = json_encode($custom_data);
        $cart = $this->context->cart;

        // Verifica e imposta prodotto come personalizzabile
        $product = new Product($id_product, false, $this->context->language->id);
        if (!$product->customizable) {
            $product->customizable = 1;
            $product->save();
        }

        // Aggiunge il prodotto al carrello
        $cart->updateQty(1, $id_product);

        // Aggiunge la personalizzazione
        $id_customization = $cart->addTextFieldToProduct($id_product, Product::CUSTOMIZE_TEXTFIELD, 'art_puzzle_data', $this->context->language->id);
        if ($id_customization) {
            $cart->addCustomization($id_product, $id_customization, Product::CUSTOMIZE_TEXTFIELD, 0, $customization_text);
        } else {
            $this->logAndError('Errore nella creazione della personalizzazione.');
        }

        Tools::redirect('index.php?controller=cart');
    }

    private function sendPuzzleEmails($custom_data)
    {
        $customer = $this->context->customer;
        $lang_id = (int)$this->context->language->id;

        $pdfPath = _PS_MODULE_DIR_ . 'art_puzzle/upload/' . $custom_data['pdf'];
        $imagePath = _PS_MODULE_DIR_ . 'art_puzzle/upload/' . $custom_data['image'];

        $template_vars = [
            '{product_name}' => '', // da completare con nome prodotto se necessario
            '{box_text}' => $custom_data['box_text']
        ];

        // Invia al cliente
        if (Configuration::get('ART_PUZZLE_SEND_PREVIEW_USER_EMAIL')) {
            Mail::Send(
                $lang_id,
                'art_puzzle_user',
                $this->module->l('La tua personalizzazione del puzzle'),
                $template_vars,
                $customer->email,
                $customer->firstname . ' ' . $customer->lastname,
                null, null,
                [$pdfPath, $imagePath],
                null, _PS_MODULE_DIR_ . 'art_puzzle/mails/'
            );
        }

        // Invia all'amministratore
        if (Configuration::get('ART_PUZZLE_SEND_PREVIEW_ADMIN_EMAIL')) {
            Mail::Send(
                $lang_id,
                'art_puzzle_admin',
                $this->module->l('Nuova personalizzazione ricevuta'),
                $template_vars,
                Configuration::get('PS_SHOP_EMAIL'),
                'Admin ArtPuzzle',
                null, null,
                [$pdfPath, $imagePath],
                null, _PS_MODULE_DIR_ . 'art_puzzle/mails/'
            );
        }
    }

    private function logAndError($message)
    {
        $this->errors[] = $this->module->l($message);
        if (class_exists('ArtPuzzleLogger')) {
            ArtPuzzleLogger::log('[CART] ' . $message);
        }
    }
}

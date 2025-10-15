<?php
/**
 * Salva configurazione del puzzle nella sessione/carrello.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__DIR__, 2) . '/classes/PuzzleCustomization.php';
require_once dirname(__DIR__, 2) . '/classes/ImageProcessor.php';
require_once dirname(__DIR__, 2) . '/classes/PuzzleOption.php';
require_once dirname(__DIR__, 2) . '/classes/PuzzleBoxColor.php';
require_once dirname(__DIR__, 2) . '/classes/PuzzleTextColor.php';
require_once dirname(__DIR__, 2) . '/classes/PuzzleFont.php';

class PuzzlecustomizerSaveConfigModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();
        header('Content-Type: application/json');

        try {
            $this->processSave();
        } catch (PuzzleImageProcessorException $e) {
            PrestaShopLogger::addLog(
                'Puzzle Customizer Save Error: ' . $e->getMessage(),
                3,
                null,
                'PuzzleCustomizer',
                null,
                true
            );

            $this->ajaxDie(json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]));
        } catch (Exception $e) {
            PrestaShopLogger::addLog(
                'Puzzle Customizer Save Unexpected Error: ' . $e->getMessage() . ' | ' . $e->getTraceAsString(),
                3,
                null,
                'PuzzleCustomizer',
                null,
                true
            );

            $this->ajaxDie(json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]));
        }
    }

    protected function processSave()
    {
        $payload = json_decode(Tools::file_get_contents('php://input'), true);

        if (!$payload || !is_array($payload)) {
            throw new Exception($this->module->l('Invalid data format.'));
        }

        foreach (['token', 'file'] as $field) {
            if (!isset($payload[$field]) || empty($payload[$field])) {
                throw new Exception(sprintf($this->module->l('Missing required field: %s'), $field));
            }
        }

        if (!isset($payload['csrf_token']) || $payload['csrf_token'] !== Tools::getToken(false)) {
            throw new Exception($this->module->l('Security token validation failed.'));
        }

        if (!preg_match('/^[a-zA-Z0-9]{32,64}$/', $payload['token'])) {
            throw new Exception($this->module->l('Invalid token format.'));
        }

        $filename = basename($payload['file']);
        if (!$filename || !preg_match('/^[a-zA-Z0-9_\-\.]+$/', $filename)) {
            throw new Exception($this->module->l('Invalid filename format.'));
        }

        $filename = str_replace(['..', '/', '\\'], '', $filename);

        $tempDir = _PS_MODULE_DIR_ . $this->module->name . '/uploads/temp/';
        $tempPath = $tempDir . $filename;
        $allowedDir = realpath($tempDir);
        $realPath = realpath($tempPath);

        if ($realPath === false || strpos($realPath, $allowedDir) !== 0) {
            throw new Exception($this->module->l('Invalid file path.'));
        }

        if (!is_file($realPath)) {
            throw new Exception($this->module->l('File temporaneo non trovato.'));
        }

        if (!$this->context->cart || !$this->context->cart->id) {
            throw new Exception($this->module->l('No active cart found.'));
        }

        if ($this->context->cart->id_customer != $this->context->customer->id) {
            throw new Exception($this->module->l('Cart does not belong to current customer.'));
        }

        if (isset($payload['option_id'])) {
            $optionId = (int) $payload['option_id'];
            if ($optionId) {
                $option = new PuzzleOption($optionId);
                if (!Validate::isLoadedObject($option) || !$option->active) {
                    throw new Exception($this->module->l('Invalid puzzle option selected.'));
                }
            }
        }

        if (isset($payload['box_color_id'])) {
            $colorId = (int) $payload['box_color_id'];
            if ($colorId) {
                $color = new PuzzleBoxColor($colorId);
                if (!Validate::isLoadedObject($color) || !$color->active) {
                    throw new Exception($this->module->l('Invalid box color selected.'));
                }
            }
        }

        if (isset($payload['text_color_id'])) {
            $textColorId = (int) $payload['text_color_id'];
            if ($textColorId) {
                $textColor = new PuzzleTextColor($textColorId);
                if (!Validate::isLoadedObject($textColor) || !$textColor->active) {
                    throw new Exception($this->module->l('Invalid text color selected.'));
                }
            }
        }

        if (isset($payload['font_id'])) {
            $fontId = (int) $payload['font_id'];
            if ($fontId) {
                $font = new PuzzleFont($fontId);
                if (!Validate::isLoadedObject($font) || !$font->active) {
                    throw new Exception($this->module->l('Invalid font selected.'));
                }
            }
        }

        if (isset($payload['text_content'])) {
            if (Tools::strlen($payload['text_content']) > 500) {
                throw new Exception($this->module->l('Text content too long (max 500 characters).'));
            }

            $payload['text_content'] = strip_tags($payload['text_content']);
        }

        $sanitizedPayload = $this->sanitizePayload($payload);
        $finalDir = _PS_MODULE_DIR_ . $this->module->name . '/uploads/customizations/';
        $finalPath = $finalDir . $filename;

        $db = Db::getInstance();
        $transactionStarted = false;

        if (method_exists($db, 'beginTransaction')) {
            $transactionStarted = $db->beginTransaction();
        } else {
            $transactionStarted = $db->execute('START TRANSACTION');
        }

        try {
            $processor = new ImageProcessor();
            $processor->moveToCustomizationDirectory($realPath, $finalPath);

            $existingCustomization = $this->getExistingCustomization(
                (int) $this->context->cart->id,
                isset($payload['id_product']) ? (int) $payload['id_product'] : null,
                $payload['token']
            );

            if ($existingCustomization) {
                $customization = $existingCustomization;
            } else {
                $customization = new PuzzleCustomization();
                $customization->created_at = date('Y-m-d H:i:s');
            }

            $customization->id_cart = (int) $this->context->cart->id;
            $customization->token = pSQL($payload['token']);
            $customization->configuration = json_encode($sanitizedPayload, JSON_UNESCAPED_UNICODE);
            $customization->image_path = $filename;
            $customization->status = 'saved';
            $customization->updated_at = date('Y-m-d H:i:s');

            if (!$customization->save()) {
                throw new Exception($this->module->l('Failed to save customization.'));
            }

            if ($transactionStarted) {
                if (method_exists($db, 'commit')) {
                    $db->commit();
                } else {
                    $db->execute('COMMIT');
                }
            }

            $response = [
                'success' => true,
                'id' => (int) $customization->id,
            ];

            $idProduct = isset($payload['id_product']) ? (int) $payload['id_product'] : 0;

            if ($idProduct) {
                require_once dirname(__DIR__, 2) . '/classes/PuzzleCartManager.php';

                $addedToCart = PuzzleCartManager::addToCart(
                    $idProduct,
                    (int) $customization->id,
                    1,
                    $this->context
                );

                $response['added_to_cart'] = (bool) $addedToCart;
                $response['cart_url'] = $this->context->link->getPageLink('cart');
            }

            $this->ajaxDie(json_encode($response));
        } catch (Exception $e) {
            if ($transactionStarted) {
                if (method_exists($db, 'rollback')) {
                    $db->rollback();
                } else {
                    $db->execute('ROLLBACK');
                }
            }

            if (file_exists($finalPath)) {
                @unlink($finalPath);
            }

            throw $e;
        }
    }

    /**
     * Retrieve existing customization for cart/token.
     *
     * @param int $idCart
     * @param int|null $idProduct
     * @param string|null $token
     *
     * @return PuzzleCustomization|null
     */
    protected function getExistingCustomization($idCart, $idProduct = null, $token = null)
    {
        $conditions = ['id_cart = ' . (int) $idCart];

        if ($token) {
            $conditions[] = 'token = "' . pSQL($token) . '"';
        }

        $sql = 'SELECT id_puzzle_customization, configuration FROM ' . _DB_PREFIX_ . 'puzzle_customization'
            . ' WHERE ' . implode(' AND ', $conditions)
            . ' ORDER BY id_puzzle_customization DESC';

        $rows = Db::getInstance()->executeS($sql);

        if (!empty($rows)) {
            foreach ($rows as $row) {
                if ($idProduct) {
                    $config = json_decode($row['configuration'], true);
                    if ($config && isset($config['id_product']) && (int) $config['id_product'] !== (int) $idProduct) {
                        continue;
                    }
                }

                return new PuzzleCustomization((int) $row['id_puzzle_customization']);
            }
        }

        return null;
    }

    /**
     * Sanitize payload data before storing.
     *
     * @param array $payload
     *
     * @return array
     */
    protected function sanitizePayload(array $payload)
    {
        $sanitized = [
            'token' => pSQL($payload['token']),
            'file' => pSQL(basename($payload['file'])),
            'option_id' => isset($payload['option_id']) ? (int) $payload['option_id'] : null,
            'box_color_id' => isset($payload['box_color_id']) ? (int) $payload['box_color_id'] : null,
            'text_color_id' => isset($payload['text_color_id']) ? (int) $payload['text_color_id'] : null,
            'font_id' => isset($payload['font_id']) ? (int) $payload['font_id'] : null,
            'dimension' => isset($payload['dimension']) ? (int) $payload['dimension'] : null,
            'pieces' => isset($payload['pieces']) ? (int) $payload['pieces'] : null,
            'box_color' => isset($payload['box_color']) ? pSQL($payload['box_color']) : null,
            'text_color' => isset($payload['text_color']) ? pSQL($payload['text_color']) : null,
            'font' => isset($payload['font']) ? pSQL($payload['font']) : null,
            'id_product' => isset($payload['id_product']) ? (int) $payload['id_product'] : null,
        ];

        if (isset($payload['text_content'])) {
            $sanitized['text_content'] = pSQL(strip_tags($payload['text_content']));
        }

        if (isset($payload['edited_image']) && Tools::strlen($payload['edited_image']) < 10485760) { // 10MB limit
            $sanitized['edited_image'] = preg_replace('/[^A-Za-z0-9\+\/=]/', '', $payload['edited_image']);
        }

        return $sanitized;
    }
}

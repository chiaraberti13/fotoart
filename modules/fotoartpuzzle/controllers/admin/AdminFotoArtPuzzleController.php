<?php

class AdminFotoArtPuzzleController extends ModuleAdminController
{
    /**
     * @var array
     */
    protected $productionStatuses = [];

    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->list_no_link = true;

        $this->productionStatuses = [
            'pending' => $this->l('Pending'),
            'in_production' => $this->l('In production'),
            'quality_check' => $this->l('Quality check'),
            'completed' => $this->l('Completed'),
            'shipped' => $this->l('Shipped'),
        ];
    }

    public function postProcess()
    {
        if (Tools::isSubmit('fapUpdateStatus')) {
            $this->processStatusUpdate();
        }

        if (Tools::isSubmit('fapDownloadAssets')) {
            $this->processBatchDownload();
        }

        parent::postProcess();
    }

    public function initContent()
    {
        parent::initContent();

        $filters = $this->getFilters();
        $orders = $this->getProductionOrders($filters);

        $this->context->smarty->assign([
            'orders' => $orders,
            'production_statuses' => $this->productionStatuses,
            'filters' => $filters,
            'token' => $this->token,
            'controller_link' => $this->context->link->getAdminLink($this->controller_name),
            'errors' => $this->errors,
            'confirmations' => $this->confirmations,
        ]);

        $this->content = $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/production.tpl');
    }

    /**
     * Handle production status update
     */
    protected function processStatusUpdate()
    {
        if ($this->token !== Tools::getValue('token')) {
            $this->errors[] = $this->l('Invalid security token.');

            return;
        }

        $idOrder = (int) Tools::getValue('id_order');
        $status = (string) Tools::getValue('production_status');

        if (!$idOrder) {
            $this->errors[] = $this->l('Missing order identifier.');

            return;
        }

        if (!isset($this->productionStatuses[$status])) {
            $this->errors[] = $this->l('Invalid production status selected.');

            return;
        }

        $now = date('Y-m-d H:i:s');
        Db::getInstance()->insert(
            'fap_production_order',
            [
                'id_order' => (int) $idOrder,
                'status' => pSQL($status),
                'date_add' => pSQL($now),
                'date_upd' => pSQL($now),
            ],
            false,
            true,
            Db::REPLACE
        );

        $this->confirmations[] = $this->l('Production status updated successfully.');
    }

    /**
     * Handle asset download
     */
    protected function processBatchDownload()
    {
        if ($this->token !== Tools::getValue('token')) {
            $this->errors[] = $this->l('Invalid security token.');

            return;
        }

        $selected = Tools::getValue('fap_orders');
        if (!is_array($selected)) {
            $this->errors[] = $this->l('Select at least one order to download assets.');

            return;
        }

        $orderIds = array_unique(array_map('intval', $selected));
        $orderIds = array_values(array_filter($orderIds));
        if (!$orderIds) {
            $this->errors[] = $this->l('Select at least one order to download assets.');

            return;
        }

        $archivePath = $this->createBatchArchive($orderIds);
        if (!$archivePath) {
            $this->errors[] = $this->l('No assets available for the selected orders.');

            return;
        }

        $this->streamArchive($archivePath);
    }

    /**
     * Retrieve filters from request
     *
     * @return array
     */
    protected function getFilters()
    {
        return [
            'date_from' => Tools::getValue('fap_date_from', ''),
            'date_to' => Tools::getValue('fap_date_to', ''),
            'status' => Tools::getValue('fap_status', 'all'),
        ];
    }

    /**
     * Build list of orders for dashboard
     *
     * @param array $filters
     *
     * @return array
     */
    protected function getProductionOrders(array $filters)
    {
        $idLang = (int) $this->context->language->id;

        $query = new DbQuery();
        $query->select('o.id_order, o.reference, o.date_add, o.current_state,');
        $query->select('CONCAT(c.firstname, " ", c.lastname) AS customer_name');
        $query->select('IFNULL(fpo.status, "pending") AS production_status');
        $query->select('fpo.date_upd AS status_updated');
        $query->select('osl.name AS order_state');
        $query->from('orders', 'o');
        $query->innerJoin('order_detail', 'od', 'od.id_order = o.id_order AND od.id_customization > 0');
        $query->innerJoin('customized_data', 'cd', 'cd.id_customization = od.id_customization AND cd.type = ' . (int) Product::CUSTOMIZE_TEXTFIELD . ' AND cd.`index` = 1');
        $query->leftJoin('customer', 'c', 'c.id_customer = o.id_customer');
        $query->leftJoin('fap_production_order', 'fpo', 'fpo.id_order = o.id_order');
        $query->leftJoin('order_state_lang', 'osl', 'osl.id_order_state = o.current_state AND osl.id_lang = ' . $idLang);

        if (!empty($filters['date_from'])) {
            $query->where("o.date_add >= '" . pSQL($filters['date_from']) . " 00:00:00'");
        }

        if (!empty($filters['date_to'])) {
            $query->where("o.date_add <= '" . pSQL($filters['date_to']) . " 23:59:59'");
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            if ($filters['status'] === 'pending') {
                $query->where("(fpo.status IS NULL OR fpo.status = 'pending')");
            } else {
                $query->where("fpo.status = '" . pSQL($filters['status']) . "'");
            }
        }

        $query->groupBy('o.id_order');
        $query->orderBy('o.date_add DESC');

        $rows = Db::getInstance()->executeS($query) ?: [];

        $orders = [];
        foreach ($rows as $row) {
            $idOrder = (int) $row['id_order'];
            $status = $row['production_status'] ?: 'pending';

            $orders[] = [
                'id_order' => $idOrder,
                'reference' => $row['reference'],
                'date_add' => Tools::displayDate($row['date_add'], (int) $this->context->language->id, true),
                'customer_name' => $row['customer_name'] ?: $this->l('Guest'),
                'order_state' => $row['order_state'] ?: $this->l('Unknown'),
                'production_status' => $status,
                'production_status_label' => isset($this->productionStatuses[$status]) ? $this->productionStatuses[$status] : $status,
                'status_updated' => $row['status_updated'] ? Tools::displayDate($row['status_updated'], (int) $this->context->language->id, true) : null,
                'customizations' => $this->formatCustomizations($idOrder),
                'order_link' => $this->context->link->getAdminLink('AdminOrders', true, [], [
                    'vieworder' => 1,
                    'id_order' => $idOrder,
                ]),
            ];
        }

        return $orders;
    }

    /**
     * Format customization details for display
     *
     * @param int $idOrder
     *
     * @return array
     */
    protected function formatCustomizations($idOrder)
    {
        $customizations = FAPCustomizationService::getOrderCustomizations((int) $idOrder);
        $formatted = [];

        foreach ($customizations as $customization) {
            $metadata = is_array($customization['metadata']) ? $customization['metadata'] : [];
            $displayMetadata = $metadata;
            unset(
                $displayMetadata['preview_path'],
                $displayMetadata['asset_map'],
                $displayMetadata['timestamps']
            );

            $assetDownloads = [];
            if (!empty($metadata['asset_map']) && is_array($metadata['asset_map'])) {
                foreach ($metadata['asset_map'] as $key => $asset) {
                    if (!is_array($asset) || empty($asset['path'])) {
                        continue;
                    }

                    $assetDownloads[$key] = $this->module->getDownloadLink(
                        $asset['path'],
                        'admin',
                        ['ttl' => 86400, 'id_order' => $idOrder]
                    );
                }
            }

            $formatted[] = [
                'id_customization' => $customization['id_customization'],
                'text' => $customization['text'],
                'metadata' => $displayMetadata,
                'preview_link' => !empty($metadata['preview_path'])
                    ? $this->module->getDownloadLink($metadata['preview_path'], 'admin', ['ttl' => 86400, 'id_order' => $idOrder])
                    : null,
                'image_link' => !empty($customization['file'])
                    ? $this->module->getDownloadLink($customization['file'], 'admin', ['ttl' => 86400, 'id_order' => $idOrder])
                    : null,
                'asset_downloads' => $assetDownloads,
            ];
        }

        return $formatted;
    }

    /**
     * Create temporary archive with assets
     *
     * @param array $orderIds
     *
     * @return string|null
     */
    protected function createBatchArchive(array $orderIds)
    {
        if (!class_exists('ZipArchive')) {
            $this->errors[] = $this->l('Zip extension is not available on the server.');

            return null;
        }

        $tempDir = FAPPathBuilder::getTempPath();
        if (!is_dir($tempDir)) {
            FAPPathBuilder::ensureFilesystem();
        }

        $filename = 'fotoart_assets_' . date('Ymd_His') . '_' . Tools::passwdGen(6) . '.zip';
        $archivePath = rtrim($tempDir, '/\\') . '/' . $filename;

        $zip = new ZipArchive();
        if ($zip->open($archivePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->errors[] = $this->l('Unable to create archive.');

            return null;
        }

        $filesAdded = 0;
        foreach ($orderIds as $idOrder) {
            $assets = FAPCustomizationService::getOrderAssets((int) $idOrder);
            if (!$assets) {
                continue;
            }

            foreach ($assets as $asset) {
                if (empty($asset['path']) || !file_exists($asset['path'])) {
                    continue;
                }

                $entryName = 'order_' . (int) $idOrder . '/' . $asset['filename'];
                if ($zip->addFile($asset['path'], $entryName)) {
                    ++$filesAdded;
                }
            }
        }

        $zip->close();

        if (!$filesAdded) {
            @unlink($archivePath);

            return null;
        }

        return $archivePath;
    }

    /**
     * Output archive to browser
     *
     * @param string $path
     */
    protected function streamArchive($path)
    {
        if (!file_exists($path)) {
            $this->errors[] = $this->l('Archive not found.');

            return;
        }

        header('Content-Type: application/zip');
        header('Content-Length: ' . (int) filesize($path));
        header('Content-Disposition: attachment; filename="' . basename($path) . '"');

        readfile($path);
        @unlink($path);
        exit;
    }
}

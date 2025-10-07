<?php

class FAPPdfGenerator
{
    /**
     * @var FotoArtPuzzle
     */
    private $module;

    public function __construct(FotoArtPuzzle $module)
    {
        $this->module = $module;
    }

    /**
     * Generate a PDF summary for the given order customizations.
     *
     * @param Order $order
     * @param array $customizations
     * @param array $options
     *
     * @return array|null
     */
    public function generate(Order $order, array $customizations, array $options = [])
    {
        if (empty($customizations)) {
            return null;
        }

        $scope = isset($options['scope']) ? (string) $options['scope'] : 'user';
        $idLang = isset($options['id_lang']) ? (int) $options['id_lang'] : (int) $order->id_lang;
        if ($idLang <= 0) {
            $idLang = (int) Configuration::get('PS_LANG_DEFAULT');
        }

        $orderPath = FAPPathBuilder::getOrderPath((int) $order->id);
        if (!is_dir($orderPath)) {
            @mkdir($orderPath, 0750, true);
        }

        if (!class_exists('TCPDF')) {
            $tcpdfPath = _PS_TOOL_DIR_ . 'tcpdf/tcpdf.php';
            if (file_exists($tcpdfPath)) {
                require_once $tcpdfPath;
            } else {
                $vendorPath = _PS_ROOT_DIR_ . '/vendor/tecnickcom/tcpdf/tcpdf.php';
                if (file_exists($vendorPath)) {
                    require_once $vendorPath;
                }
            }
        }

        if (!class_exists('TCPDF')) {
            throw new Exception('TCPDF library is not available');
        }

        $pdf = new TCPDF();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(15, 20, 15);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->SetCreator('FotoArt Puzzle');
        $pdf->SetAuthor(Configuration::get('PS_SHOP_NAME'));
        $pdf->SetTitle($this->trans('Puzzle customization summary', $idLang));
        $pdf->SetSubject($this->trans('Order reference', $idLang) . ': ' . $order->reference);
        $pdf->SetFont('helvetica', '', 10);

        $shopName = Configuration::get('PS_SHOP_NAME');
        $orderDate = Tools::displayDate($order->date_add, $idLang, true);
        $customerName = $this->resolveCustomerName($order);

        $index = 0;
        foreach ($customizations as $customization) {
            $pdf->AddPage();
            ++$index;

            $html = $this->renderHeaderHtml($shopName, $order, $orderDate, $customerName, $index, $scope, $idLang);
            $html .= $this->renderCustomizationHtml($customization, $scope, $idLang);

            $pdf->writeHTML($html, true, false, true, false, '');

            $previewPath = $this->resolvePreviewPath($customization);
            if ($previewPath) {
                try {
                    $pdf->Ln(4);
                    $pdf->Image($previewPath, '', '', 150, 0, '', '', 'T', false, 300, '', false, false, 1, false, false, false);
                } catch (Exception $e) {
                    // Ignore preview rendering issues but keep going.
                }
            }
        }

        $reference = $order->reference ? preg_replace('/[^A-Za-z0-9_-]/', '', $order->reference) : (string) (int) $order->id;
        $filename = sprintf('%s_puzzle_%s.pdf', $reference, $scope);
        $destination = rtrim($orderPath, '/\\') . '/' . $filename;
        $pdf->Output($destination, 'F');

        return [
            'path' => $destination,
            'filename' => $filename,
            'scope' => $scope,
        ];
    }

    /**
     * Render document header.
     *
     * @param string $shopName
     * @param Order $order
     * @param string $orderDate
     * @param string $customerName
     * @param int $index
     * @param string $scope
     * @param int $idLang
     *
     * @return string
     */
    private function renderHeaderHtml($shopName, Order $order, $orderDate, $customerName, $index, $scope, $idLang)
    {
        $title = $this->trans('Puzzle customization summary', $idLang);
        $customizationLabel = sprintf('%s #%d', $this->trans('Customization', $idLang), $index);

        $rows = [
            [$this->trans('Shop', $idLang), $shopName],
            [$this->trans('Order reference', $idLang), $order->reference],
            [$this->trans('Order date', $idLang), $orderDate],
            [$this->trans('Customer', $idLang), $customerName],
            [$this->trans('Document scope', $idLang), Tools::ucfirst($scope)],
        ];

        $html = '<h1 style="font-size:18px;">' . $this->escape($title) . '</h1>';
        $html .= '<table border="0" cellpadding="4" cellspacing="0" style="font-size:10px;">';
        foreach ($rows as $row) {
            $html .= '<tr>';
            $html .= '<th align="left" width="30%"><strong>' . $this->escape($row[0]) . '</strong></th>';
            $html .= '<td width="70%">' . $this->escape($row[1]) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
        $html .= '<h2 style="font-size:14px; margin-top:12px;">' . $this->escape($customizationLabel) . '</h2>';

        return $html;
    }

    /**
     * Render customization details section.
     *
     * @param array $customization
     * @param string $scope
     * @param int $idLang
     *
     * @return string
     */
    private function renderCustomizationHtml(array $customization, $scope, $idLang)
    {
        $metadata = isset($customization['metadata']) && is_array($customization['metadata'])
            ? $customization['metadata']
            : [];

        $displayData = $this->module->getCustomizationDisplayData([$customization], [
            'include_links' => false,
            'scope' => $scope,
        ]);

        $display = isset($displayData[0]) ? $displayData[0] : ['metadata' => []];
        $entries = isset($display['metadata']) && is_array($display['metadata'])
            ? $display['metadata']
            : [];

        $html = '<table border="0" cellpadding="4" cellspacing="0" style="font-size:10px;">';
        $html .= '<tr>';
        $html .= '<th align="left" width="30%"><strong>' . $this->trans('Box text', $idLang) . '</strong></th>';
        $html .= '<td width="70%">' . $this->escape($this->extractText($customization)) . '</td>';
        $html .= '</tr>';

        foreach ($entries as $label => $value) {
            $html .= '<tr>';
            $html .= '<th align="left" width="30%"><strong>' . $this->escape($label) . '</strong></th>';
            $html .= '<td width="70%">' . $this->escape($value) . '</td>';
            $html .= '</tr>';
        }

        $cropSummary = $this->summarizeCrop($metadata, $idLang);
        if ($cropSummary) {
            foreach ($cropSummary as $row) {
                $html .= '<tr>';
                $html .= '<th align="left" width="30%"><strong>' . $this->escape($row[0]) . '</strong></th>';
                $html .= '<td width="70%">' . $this->escape($row[1]) . '</td>';
                $html .= '</tr>';
            }
        }

        $notes = $this->summarizeNotes($metadata);
        if ($notes) {
            foreach ($notes as $row) {
                $html .= '<tr>';
                $html .= '<th align="left" width="30%"><strong>' . $this->escape($row[0]) . '</strong></th>';
                $html .= '<td width="70%">' . $this->escape($row[1]) . '</td>';
                $html .= '</tr>';
            }
        }

        $html .= '</table>';

        return $html;
    }

    /**
     * Summarize crop information.
     *
     * @param array $metadata
     * @param int $idLang
     *
     * @return array
     */
    private function summarizeCrop(array $metadata, $idLang)
    {
        $rows = [];
        $crop = isset($metadata['crop']) && is_array($metadata['crop']) ? $metadata['crop'] : [];
        $coordinates = isset($metadata['coordinates']) && is_array($metadata['coordinates']) ? $metadata['coordinates'] : [];

        if (!empty($crop)) {
            $summary = [];
            if (isset($crop['width'])) {
                $summary[] = $this->trans('Width', $idLang) . ': ' . (int) $crop['width'];
            }
            if (isset($crop['height'])) {
                $summary[] = $this->trans('Height', $idLang) . ': ' . (int) $crop['height'];
            }
            if (isset($crop['x'])) {
                $summary[] = 'x: ' . (int) $crop['x'];
            }
            if (isset($crop['y'])) {
                $summary[] = 'y: ' . (int) $crop['y'];
            }
            if ($summary) {
                $rows[] = [$this->trans('Crop area', $idLang), implode(', ', $summary)];
            }
        }

        if (!empty($coordinates)) {
            $coordSummary = [];
            foreach ($coordinates as $axis => $value) {
                $coordSummary[] = $axis . ': ' . (is_numeric($value) ? (float) $value : $value);
            }
            if ($coordSummary) {
                $rows[] = [$this->trans('Coordinates', $idLang), implode(', ', $coordSummary)];
            }
        }

        return $rows;
    }

    /**
     * Summarize notes section.
     *
     * @param array $metadata
     *
     * @return array
     */
    private function summarizeNotes(array $metadata)
    {
        $rows = [];
        if (!empty($metadata['notes']) && is_array($metadata['notes'])) {
            foreach ($metadata['notes'] as $key => $value) {
                if ($value === '') {
                    continue;
                }
                $label = Tools::ucfirst(str_replace('_', ' ', $key));
                $rows[] = [$label, (string) $value];
            }
        }

        return $rows;
    }

    /**
     * Resolve the preview path for embedding in PDF.
     *
     * @param array $customization
     *
     * @return string|null
     */
    private function resolvePreviewPath(array $customization)
    {
        $metadata = isset($customization['metadata']) && is_array($customization['metadata'])
            ? $customization['metadata']
            : [];

        if (!empty($metadata['asset_map']['preview']['path']) && file_exists($metadata['asset_map']['preview']['path'])) {
            return $metadata['asset_map']['preview']['path'];
        }

        if (!empty($metadata['preview_path']) && file_exists($metadata['preview_path'])) {
            return $metadata['preview_path'];
        }

        if (!empty($customization['file']) && file_exists($customization['file'])) {
            return $customization['file'];
        }

        return null;
    }

    /**
     * Resolve customer display name.
     *
     * @param Order $order
     *
     * @return string
     */
    private function resolveCustomerName(Order $order)
    {
        if (empty($order->id_customer)) {
            return $this->trans('Guest', (int) $order->id_lang ?: (int) Configuration::get('PS_LANG_DEFAULT'));
        }

        $customer = new Customer((int) $order->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            return $this->trans('Guest', (int) $order->id_lang ?: (int) Configuration::get('PS_LANG_DEFAULT'));
        }

        return trim($customer->firstname . ' ' . $customer->lastname);
    }

    /**
     * Extract customization text content.
     *
     * @param array $customization
     *
     * @return string
     */
    private function extractText(array $customization)
    {
        if (!empty($customization['text'])) {
            return (string) $customization['text'];
        }

        $metadata = isset($customization['metadata']) && is_array($customization['metadata'])
            ? $customization['metadata']
            : [];

        if (!empty($metadata['box_info']['text'])) {
            return (string) $metadata['box_info']['text'];
        }

        return '';
    }

    /**
     * Escape HTML output.
     *
     * @param string $value
     *
     * @return string
     */
    private function escape($value)
    {
        return Tools::htmlentitiesUTF8((string) $value);
    }

    /**
     * Translate a label using the module.
     *
     * @param string $text
     * @param int $idLang
     *
     * @return string
     */
    private function trans($text, $idLang)
    {
        return $this->module->l($text, 'fappdfgenerator', $idLang);
    }
}

<?php

class FotoartpuzzleDownloadModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        $token = Tools::getValue('token');
        $path = Tools::getValue('path');
        $scope = Tools::getValue('scope', 'front') === 'admin' ? 'admin' : 'front';
        $disposition = Tools::getValue('disposition', 'attachment') === 'inline' ? 'inline' : 'attachment';
        $expires = Tools::getValue('expires');
        $idOrder = Tools::getValue('id_order');

        try {
            $canonicalPath = FAPPathValidator::assertReadablePath($path);
        } catch (Exception $exception) {
            header('HTTP/1.1 404 Not Found');
            exit;
        }

        if (!$token || !$this->module->validateDownloadToken($token, $canonicalPath, $scope, $expires, $idOrder, $disposition)) {
            header('HTTP/1.1 403 Forbidden');
            exit;
        }

        $mime = function_exists('mime_content_type') ? @mime_content_type($canonicalPath) : false;
        header('Content-Type: ' . ($mime ?: 'application/octet-stream'));
        header('Content-Disposition: ' . ($disposition === 'inline' ? 'inline' : 'attachment') . '; filename="' . basename($canonicalPath) . '"');
        header('Content-Length: ' . filesize($canonicalPath));

        FAPLogger::create()->info('Download delivered', [
            'path' => $canonicalPath,
            'scope' => $scope,
            'expires' => $expires,
            'order' => $idOrder,
        ]);

        readfile($canonicalPath);
        exit;
    }
}

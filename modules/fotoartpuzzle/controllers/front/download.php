<?php

class FotoartpuzzleDownloadModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        $token = Tools::getValue('token');
        $path = Tools::getValue('path');
        $scope = Tools::getValue('scope', 'front');
        $disposition = Tools::getValue('disposition', 'attachment');
        $expires = Tools::getValue('expires');
        $idOrder = Tools::getValue('id_order');

        if (!$token || !$this->module->validateDownloadToken($token, $path, $scope, $expires, $idOrder)) {
            header('HTTP/1.1 403 Forbidden');
            exit;
        }

        if (!$path || !file_exists($path) || !$this->module->isAllowedDownloadPath($path)) {
            header('HTTP/1.1 404 Not Found');
            exit;
        }

        $mime = function_exists('mime_content_type') ? @mime_content_type($path) : false;
        header('Content-Type: ' . ($mime ?: 'application/octet-stream'));
        header('Content-Disposition: ' . ($disposition === 'inline' ? 'inline' : 'attachment') . '; filename="' . basename($path) . '"');
        header('Content-Length: ' . filesize($path));

        FAPLogger::create()->info('Download delivered', [
            'path' => $path,
            'scope' => $scope,
            'expires' => $expires,
            'order' => $idOrder,
        ]);

        readfile($path);
        exit;
    }
}

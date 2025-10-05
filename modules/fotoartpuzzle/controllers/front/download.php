<?php

class FotoartpuzzleDownloadModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        $token = Tools::getValue('token');
        $path = Tools::getValue('path');
        $scope = Tools::getValue('scope', 'front');
        $disposition = Tools::getValue('disposition', 'attachment');

        if (!$token || !$this->module->validateDownloadToken($token, $path, $scope)) {
            header('HTTP/1.1 403 Forbidden');
            exit;
        }

        if (!$path || !file_exists($path)) {
            header('HTTP/1.1 404 Not Found');
            exit;
        }

        $mime = function_exists('mime_content_type') ? @mime_content_type($path) : false;
        header('Content-Type: ' . ($mime ?: 'application/octet-stream'));
        header('Content-Disposition: ' . ($disposition === 'inline' ? 'inline' : 'attachment') . '; filename="' . basename($path) . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }
}

<?php

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/TestCase.php';

class TokenServiceTest extends FAPTestCase
{
    /** @var FotoArtPuzzle */
    private $module;

    public function __construct()
    {
        $this->module = new FotoArtPuzzle();
        $this->module->context = Context::getContext();
        $this->module->context->customer->id = 99;
        $this->module->context->customer->secure_key = 'secure-99';
        $this->module->context->cookie->id_guest = 555;
        $this->module->context->cookie->id_employee = 0;
        $this->module->context->cart->id = 321;
        Configuration::updateValue(FAPConfiguration::SECURITY_SECRET, 'tests-secret-key');
        Configuration::updateValue(FAPConfiguration::ADMIN_DOWNLOAD_SECRET, 'tests-admin-secret-key');
    }

    public function run()
    {
        $this->testFrontTokenRoundTrip();
        $this->testDownloadTokenTampering();
        $this->testDownloadTokenScopeMismatch();
        $this->testAdminDownloadTokenRequiresEmployeeCookie();
        $this->testAdminDownloadTokenWithEmployeeCookie();
        $this->testTokenServiceExpiration();
    }

    private function testFrontTokenRoundTrip()
    {
        $token = $this->module->getFrontToken('upload');
        $this->assertNotEmpty($token, 'Front token should not be empty');
        $this->assertTrue($this->module->validateFrontToken($token, 'upload'), 'Front token must validate for upload scope');
    }

    private function testDownloadTokenTampering()
    {
        $tempDir = FAPPathBuilder::getTempPath();
        @mkdir($tempDir, 0755, true);
        $file = tempnam($tempDir, 'fap');
        file_put_contents($file, 'dummy');

        $link = $this->module->getDownloadLink($file, 'front', ['ttl' => 600]);
        $this->assertNotEmpty($link, 'Download link should be generated');

        $parts = parse_url($link);
        parse_str(isset($parts['query']) ? $parts['query'] : '', $params);
        $this->assertTrue($this->module->validateDownloadToken($params['token'], $params['path'], $params['scope'], $params['expires'], null, $params['disposition']), 'Original token must be valid');

        $tamperedPath = FAPPathBuilder::getTempPath() . '/other.file';
        file_put_contents($tamperedPath, 'fake');
        $this->assertFalse($this->module->validateDownloadToken($params['token'], $tamperedPath, $params['scope'], $params['expires'], null, $params['disposition']), 'Tampered path should invalidate token');
    }

    private function testDownloadTokenScopeMismatch()
    {
        $tempDir = FAPPathBuilder::getTempPath();
        $file = tempnam($tempDir, 'fap');
        file_put_contents($file, 'dummy');

        $frontLink = $this->module->getDownloadLink($file, 'front', ['ttl' => 600]);
        $parts = parse_url($frontLink);
        parse_str($parts['query'], $params);

        $this->assertFalse($this->module->validateDownloadToken($params['token'], $params['path'], 'admin', $params['expires'], null, $params['disposition']), 'Using front token in admin scope must fail');
    }

    private function parseDownloadLink($link)
    {
        $parts = parse_url($link);
        parse_str(isset($parts['query']) ? $parts['query'] : '', $params);

        return $params;
    }

    private function generateDownloadParams($scope)
    {
        $tempDir = FAPPathBuilder::getTempPath();
        @mkdir($tempDir, 0755, true);
        $file = tempnam($tempDir, 'fap');
        file_put_contents($file, 'dummy');

        $link = $this->module->getDownloadLink($file, $scope, ['ttl' => 600]);
        $this->assertNotEmpty($link, 'Download link should be generated');

        return [$this->parseDownloadLink($link), $file];
    }

    private function testAdminDownloadTokenRequiresEmployeeCookie()
    {
        $previousEmployee = $this->module->context->employee;
        $previousEmployeeId = $this->module->context->cookie->id_employee;
        $previousOverride = Tools::$adminTokenLiteOverride;
        $this->module->context->employee = null;
        $this->module->context->cookie->id_employee = 0;
        Tools::$adminTokenLiteOverride = '';

        try {
            list($params) = $this->generateDownloadParams('admin');

            $this->assertFalse($this->module->validateDownloadToken($params['token'], $params['path'], 'admin', $params['expires'], null, $params['disposition']), 'Admin token must be rejected without employee cookie');
        } finally {
            Tools::$adminTokenLiteOverride = $previousOverride;
            $this->module->context->cookie->id_employee = $previousEmployeeId;
            $this->module->context->employee = $previousEmployee;
        }
    }

    private function testAdminDownloadTokenWithEmployeeCookie()
    {
        $previousEmployee = $this->module->context->employee;
        $previousEmployeeId = $this->module->context->cookie->id_employee;

        $employee = new Employee();
        $employee->id = 777;
        $this->module->context->employee = $employee;
        $this->module->context->cookie->id_employee = 777;

        try {
            list($params) = $this->generateDownloadParams('admin');

            $this->assertTrue($this->module->validateDownloadToken($params['token'], $params['path'], 'admin', $params['expires'], null, $params['disposition']), 'Admin token must validate with employee cookie');
            $this->assertFalse($this->module->validateDownloadToken($params['token'] . 'tampered', $params['path'], 'admin', $params['expires'], null, $params['disposition']), 'Tampered admin token should be rejected');
            $this->assertFalse($this->module->validateDownloadToken($params['token'], $params['path'], 'admin', $params['expires'] + 1, null, $params['disposition']), 'Admin token with mismatched expiration must fail');
        } finally {
            $this->module->context->cookie->id_employee = $previousEmployeeId;
            $this->module->context->employee = $previousEmployee;
        }
    }

    private function testTokenServiceExpiration()
    {
        $service = new FAPSecurityTokenService('secret');
        $issued = $service->issue(['scope' => 'test'], 1);
        $this->assertTrue($service->isValid($issued['token'], ['scope' => 'test']), 'Token should be initially valid');
        sleep(2);
        $this->assertFalse($service->isValid($issued['token'], ['scope' => 'test']), 'Token should expire after TTL');
    }
}

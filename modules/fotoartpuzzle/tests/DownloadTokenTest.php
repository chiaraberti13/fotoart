<?php

require_once __DIR__ . '/bootstrap.php';

class FotoArtPuzzleDownloadTokenTest
{
    /** @var FotoArtPuzzle */
    private $module;

    public function __construct()
    {
        $this->module = new FotoArtPuzzle();
        $this->module->context = Context::getContext();
    }

    public function run()
    {
        $this->testAdminTokenValidWithoutEmployee();
        $this->testAdminTokenRejectedWhenTampered();
        $this->testAdminTokenRejectedWhenExpired();
        $this->testFrontTokenValidForOrderOwner();
        $this->testFrontTokenRejectedForDifferentCustomer();

        echo "All fotoartpuzzle download token tests passed\n";
    }

    private function testAdminTokenValidWithoutEmployee()
    {
        $context = Context::getContext();
        $context->employee = null;
        unset($context->cookie->id_employee);

        $path = '/tmp/admin-file.pdf';
        $expires = time() + 3600;
        $token = $this->generateSignature($path, 'admin', $expires, 0);

        $this->assertTrue(
            $this->module->validateDownloadToken($token, $path, 'admin', $expires, 0),
            'Admin token should be valid without employee context'
        );
    }

    private function testAdminTokenRejectedWhenTampered()
    {
        $path = '/tmp/admin-file.pdf';
        $expires = time() + 3600;
        $token = $this->generateSignature($path, 'admin', $expires, 0);

        $this->assertFalse(
            $this->module->validateDownloadToken($token, '/tmp/other.pdf', 'admin', $expires, 0),
            'Tampered admin token should be rejected'
        );
    }

    private function testAdminTokenRejectedWhenExpired()
    {
        $path = '/tmp/admin-file.pdf';
        $expires = time() - 10;
        $token = $this->generateSignature($path, 'admin', $expires, 0);

        $this->assertFalse(
            $this->module->validateDownloadToken($token, $path, 'admin', $expires, 0),
            'Expired admin token should be rejected'
        );
    }

    private function testFrontTokenValidForOrderOwner()
    {
        $context = Context::getContext();
        $context->customer->id = 99;
        $context->customer->secure_key = 'secure-99';
        $context->cookie->fap_session_secret = 'front-session-secret';

        $orderId = 501;
        Order::seed($orderId, 99);

        $path = '/tmp/front-file.pdf';
        $expires = time() + 3600;
        $token = $this->generateSignature($path, 'front', $expires, $orderId);

        $this->assertTrue(
            $this->module->validateDownloadToken($token, $path, 'front', $expires, $orderId),
            'Front token should be valid for logged customer owning the order'
        );
    }

    private function testFrontTokenRejectedForDifferentCustomer()
    {
        $context = Context::getContext();
        $context->customer->id = 10;
        $context->customer->secure_key = 'secure-10';
        $context->cookie->fap_session_secret = 'front-session-secret';

        $orderId = 777;
        Order::seed($orderId, 42);

        $path = '/tmp/front-file.pdf';
        $expires = time() + 3600;
        $token = $this->generateSignature($path, 'front', $expires, $orderId);

        $this->assertFalse(
            $this->module->validateDownloadToken($token, $path, 'front', $expires, $orderId),
            'Front token should be rejected when customer does not own the order'
        );
    }

    private function generateSignature($path, $scope, $expires, $idOrder)
    {
        $secret = $this->getScopeSecret($scope);

        $reflection = new ReflectionClass($this->module);
        $method = $reflection->getMethod('signDownloadPath');
        $method->setAccessible(true);

        return $method->invoke($this->module, $path, $scope, (int) $expires, (int) $idOrder, $secret);
    }

    private function getScopeSecret($scope)
    {
        $reflection = new ReflectionClass($this->module);
        $method = $reflection->getMethod('getScopeSecret');
        $method->setAccessible(true);

        return $method->invoke($this->module, $scope);
    }

    private function assertTrue($condition, $message)
    {
        if (!$condition) {
            throw new Exception($message);
        }
    }

    private function assertFalse($condition, $message)
    {
        $this->assertTrue(!$condition, $message);
    }
}

$test = new FotoArtPuzzleDownloadTokenTest();
$test->run();

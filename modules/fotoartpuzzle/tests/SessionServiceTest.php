<?php

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/TestCase.php';

class SessionServiceTest extends FAPTestCase
{
    public function run()
    {
        $this->testSessionLifecycle();
        $this->testSessionExpiration();
    }

    private function testSessionLifecycle()
    {
        $service = new FAPSessionService();
        $result = $service->manage(['cart_id' => 10, 'data' => ['step' => 'upload']]);
        $this->assertNotEmpty($result['session_id'], 'Session id should be generated');

        $restored = $service->restore($result['session_id']);
        $this->assertEquals('upload', $restored['data']['step'], 'Session data should be restored');

        $updated = $service->update($result['session_id'], ['data' => ['step' => 'summary']]);
        $this->assertEquals('summary', $updated['data']['step'], 'Session update should be persisted');
    }

    private function testSessionExpiration()
    {
        $service = new FAPSessionService();
        $session = $service->manage(['data' => ['value' => 1], 'ttl' => 1]);
        $this->assertNotEmpty($session['session_id']);
        sleep(2);
        $restored = $service->restore($session['session_id']);
        $this->assertTrue(empty($restored), 'Expired session should be pruned');
    }
}

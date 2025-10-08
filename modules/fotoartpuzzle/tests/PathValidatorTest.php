<?php

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/TestCase.php';

class PathValidatorTest extends FAPTestCase
{
    public function run()
    {
        $this->testAcceptsModuleVarPath();
        $this->testRejectsTraversalOutsideModule();
    }

    private function testAcceptsModuleVarPath()
    {
        $base = FAPPathBuilder::getBasePath();
        @mkdir($base, 0755, true);
        $file = $base . '/validator-test.txt';
        file_put_contents($file, 'ok');

        $resolved = FAPPathValidator::assertReadablePath($file);
        $this->assertEquals(realpath($file), $resolved, 'Resolved path should match realpath inside module var');
        $this->assertTrue(FAPPathValidator::isAllowed($file), 'Validator should accept files inside module base path');

        @unlink($file);
    }

    private function testRejectsTraversalOutsideModule()
    {
        $outside = sys_get_temp_dir() . '/fap_outside.txt';
        file_put_contents($outside, 'outside');
        $this->assertFalse(FAPPathValidator::isAllowed($outside), 'Validator must reject paths outside allowed roots');
        try {
            FAPPathValidator::assertReadablePath($outside);
            $this->assertTrue(false, 'Expected exception for outside path');
        } catch (Exception $exception) {
            $this->assertTrue(true);
        }
        @unlink($outside);
    }
}

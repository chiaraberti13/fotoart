<?php

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/TestCase.php';

class PathValidatorTest extends FAPTestCase
{
    public function run()
    {
        $this->testAcceptsModuleVarPath();
        $this->testRejectsTraversalOutsideModule();
        $this->testRejectsPathsWithCommonPrefix();
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

    private function testRejectsPathsWithCommonPrefix()
    {
        $base = FAPPathBuilder::getBasePath();
        $sibling = $base . 'ious';
        @mkdir($sibling, 0755, true);
        $fake = $sibling . '/fake.txt';
        file_put_contents($fake, 'fake');

        $this->assertFalse(
            FAPPathValidator::isAllowed($fake),
            'Validator must reject paths that only share a prefix with allowed roots'
        );

        try {
            FAPPathValidator::assertReadablePath($fake);
            $this->assertTrue(false, 'Expected exception for path with common prefix');
        } catch (Exception $exception) {
            $this->assertTrue(true);
        }

        @unlink($fake);
        @rmdir($sibling);
    }
}

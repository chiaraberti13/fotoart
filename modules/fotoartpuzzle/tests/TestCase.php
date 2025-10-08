<?php

abstract class FAPTestCase
{
    /**
     * @var int
     */
    private $assertions = 0;

    /**
     * Execute test suite.
     */
    abstract public function run();

    protected function assertTrue($condition, $message = 'Failed asserting that value is true')
    {
        $this->assertions++;
        if (!$condition) {
            throw new Exception($message);
        }
    }

    protected function assertFalse($condition, $message = 'Failed asserting that value is false')
    {
        $this->assertTrue(!$condition, $message);
    }

    protected function assertEquals($expected, $actual, $message = '')
    {
        $this->assertions++;
        if ($expected != $actual) {
            throw new Exception($message ?: sprintf('Failed asserting that %s matches expected %s', var_export($actual, true), var_export($expected, true)));
        }
    }

    protected function assertNotEmpty($value, $message = 'Failed asserting that value is not empty')
    {
        $this->assertions++;
        if (empty($value)) {
            throw new Exception($message);
        }
    }

    public function getAssertionCount()
    {
        return $this->assertions;
    }
}

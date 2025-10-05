<?php

class FAPLogger
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * @var string
     */
    private $level;

    /**
     * @param string $level
     */
    public function __construct($level)
    {
        $this->level = $level;
        $this->filePath = FAPPathBuilder::getLogPath() . '/module.log';
    }

    /**
     * Create instance from configuration
     *
     * @return self
     */
    public static function create()
    {
        return new self(Configuration::get(FAPConfiguration::LOG_LEVEL));
    }

    /**
     * Log message
     *
     * @param string $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = [])
    {
        $levels = ['ERROR' => 0, 'WARNING' => 1, 'INFO' => 2, 'DEBUG' => 3];
        $configuredLevel = strtoupper((string) $this->level);
        if (!isset($levels[$configuredLevel]) || !isset($levels[$level]) || $levels[$level] > $levels[$configuredLevel]) {
            return;
        }

        $line = sprintf(
            "[%s] %s: %s %s\n",
            date('Y-m-d H:i:s'),
            $level,
            $message,
            $context ? json_encode($context) : ''
        );

        @file_put_contents($this->filePath, $line, FILE_APPEND);
    }

    public function error($message, array $context = [])
    {
        $this->log('ERROR', $message, $context);
    }

    public function warning($message, array $context = [])
    {
        $this->log('WARNING', $message, $context);
    }

    public function info($message, array $context = [])
    {
        $this->log('INFO', $message, $context);
    }

    public function debug($message, array $context = [])
    {
        $this->log('DEBUG', $message, $context);
    }
}

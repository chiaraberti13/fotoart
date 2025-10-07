<?php

class FAPCleanupService
{
    /**
     * @var FotoArtPuzzle
     */
    private $module;

    /**
     * @var FAPLogger
     */
    private $logger;

    /**
     * @param FotoArtPuzzle $module
     */
    public function __construct($module)
    {
        $this->module = $module;
        $this->logger = FAPLogger::create();
    }

    /**
     * Helper builder
     *
     * @param FotoArtPuzzle $module
     *
     * @return self
     */
    public static function fromModule(FotoArtPuzzle $module)
    {
        return new self($module);
    }

    /**
     * Remove temporary files older than TTL
     */
    public function cleanupTemporary()
    {
        $ttl = (int) Configuration::get(FAPConfiguration::TEMP_TTL_HOURS);
        if ($ttl <= 0) {
            return;
        }
        $threshold = time() - ($ttl * 3600);
        $tempPath = FAPPathBuilder::getTempPath();
        if (!is_dir($tempPath)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($tempPath, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                if (!@rmdir($file->getPathname())) {
                    continue;
                }
            } else {
                if ($file->getMTime() >= $threshold) {
                    continue;
                }

                if (!@unlink($file->getPathname())) {
                    $this->logger->warning('Unable to remove temporary file', ['file' => $file->getPathname()]);
                }
            }
        }
    }

    /**
     * Cleanup stale session payloads.
     */
    public function cleanupSessions()
    {
        $service = new FAPSessionService();
        $service->cleanup();
    }

    /**
     * Execute full housekeeping routine.
     */
    public function runHousekeeping()
    {
        $this->cleanupTemporary();
        $this->cleanupSessions();
    }
}

<?php

class FAPPathValidator
{
    /**
     * Resolve and ensure that the path is within an allowed root and readable.
     *
     * @param string $path
     *
     * @return string
     */
    public static function assertReadablePath($path)
    {
        $canonical = self::resolvePath($path);
        if (!file_exists($canonical)) {
            throw new RuntimeException('The requested path does not exist.');
        }

        self::assertWithinAllowedRoots($canonical);

        if (!is_readable($canonical)) {
            throw new RuntimeException('The requested path is not readable.');
        }

        return $canonical;
    }

    /**
     * Ensure the provided destination is within the allowed roots and writable.
     *
     * @param string $path
     *
     * @return string
     */
    public static function assertWritableDestination($path)
    {
        $path = (string) $path;
        if ($path === '') {
            throw new InvalidArgumentException('Destination path cannot be empty.');
        }

        $directory = dirname($path);
        $canonicalDirectory = self::resolvePath($directory);
        self::assertWithinAllowedRoots($canonicalDirectory);

        if (!is_dir($canonicalDirectory)) {
            throw new RuntimeException('Destination directory does not exist.');
        }

        if (!is_writable($canonicalDirectory)) {
            throw new RuntimeException('Destination directory is not writable.');
        }

        return rtrim($canonicalDirectory, '/\\') . DIRECTORY_SEPARATOR . basename($path);
    }

    /**
     * Check whether a path would resolve inside an allowed root.
     *
     * @param string $path
     *
     * @return bool
     */
    public static function isAllowed($path)
    {
        try {
            $canonical = self::resolvePath($path);
            self::assertWithinAllowedRoots($canonical);

            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * Resolve real path for the provided location.
     *
     * @param string $path
     *
     * @return string
     */
    private static function resolvePath($path)
    {
        $path = trim((string) $path);
        if ($path === '') {
            throw new InvalidArgumentException('Path cannot be empty.');
        }

        $realPath = realpath($path);
        if (false === $realPath) {
            throw new RuntimeException('Unable to resolve the requested path.');
        }

        return $realPath;
    }

    /**
     * Assert that the canonical path is contained in the allowed roots.
     *
     * @param string $canonicalPath
     */
    private static function assertWithinAllowedRoots($canonicalPath)
    {
        $allowedRoots = self::getAllowedRoots();
        $canonicalPath = rtrim($canonicalPath, '/\\');
        $canonicalPathWithSeparator = $canonicalPath . DIRECTORY_SEPARATOR;

        foreach ($allowedRoots as $root) {
            $normalisedRoot = rtrim($root, '/\\');
            $rootWithSeparator = $normalisedRoot . DIRECTORY_SEPARATOR;

            if ($canonicalPath === $normalisedRoot || strpos($canonicalPathWithSeparator, $rootWithSeparator) === 0) {
                return;
            }
        }

        throw new RuntimeException('Resolved path is outside of the authorized directories.');
    }

    /**
     * Collect allowed root directories for file operations.
     *
     * @return array
     */
    private static function getAllowedRoots()
    {
        $roots = [
            FAPPathBuilder::getBasePath(),
            FAPPathBuilder::getTempPath(),
            FAPPathBuilder::getPreviewPath(),
            FAPPathBuilder::getOrdersPath(),
            FAPPathBuilder::getCropsPath(),
            FAPPathBuilder::getBoxesPath(),
        ];

        if (defined('_PS_DOWNLOAD_DIR_')) {
            $roots[] = _PS_DOWNLOAD_DIR_;
        }

        if (defined('_PS_UPLOAD_DIR_')) {
            $roots[] = _PS_UPLOAD_DIR_;
        }

        $canonicalRoots = [];
        foreach ($roots as $root) {
            $resolved = realpath($root);
            if (false !== $resolved) {
                $canonicalRoots[] = rtrim($resolved, '/\\');
            }
        }

        return $canonicalRoots;
    }
}

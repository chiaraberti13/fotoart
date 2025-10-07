<?php

class FAPImageProcessor
{
    /**
     * @var bool
     */
    private $stripExif = true;

    /**
     * @var bool
     */
    private $forceReencode = true;

    /**
     * Maximum preview width in pixels
     */
    private const PREVIEW_MAX_WIDTH = 600;

    /**
     * Maximum preview height in pixels
     */
    private const PREVIEW_MAX_HEIGHT = 600;

    /**
     * Maximum thumbnail width in pixels
     */
    private const THUMB_MAX_WIDTH = 160;

    /**
     * Maximum thumbnail height in pixels
     */
    private const THUMB_MAX_HEIGHT = 130;

    public function __construct()
    {
    }

    /**
     * Process an uploaded image and return destination path
     *
     * @param string $sourcePath
     * @param string $destinationPath Path without extension
     * @param array $options
     *
     * @return array
     */
    public function process($sourcePath, $destinationPath, array $options = [])
    {
        $info = getimagesize($sourcePath);
        if (!$info) {
            throw new Exception('Unable to read image size.');
        }

        $mime = $info['mime'];
        $image = $this->createImageResource($sourcePath, $mime);

        if (!$image) {
            throw new Exception('Unable to create image resource.');
        }

        $width = imagesx($image);
        $height = imagesy($image);

        if ($this->stripExif && function_exists('exif_read_data')) {
            // no-op: re-encoding strips EXIF automatically
        }

        $destinationExt = $mime === 'image/png' ? 'png' : 'jpg';
        $destination = $destinationPath . '.' . $destinationExt;

        $this->writeImage($image, $destination, $destinationExt);

        $previewPath = null;
        $thumbPath = null;

        if (!empty($options['preview_path'])) {
            $previewExt = $destinationExt === 'png' ? 'png' : 'jpg';
            $previewPath = $options['preview_path'] . '.' . $previewExt;
            $this->createResizedCopy(
                $image,
                $previewPath,
                self::PREVIEW_MAX_WIDTH,
                self::PREVIEW_MAX_HEIGHT,
                $previewExt
            );
        }

        if (!empty($options['thumb_path'])) {
            $thumbExt = $destinationExt === 'png' ? 'png' : 'jpg';
            $thumbPath = $options['thumb_path'] . '.' . $thumbExt;
            $this->createResizedCopy(
                $image,
                $thumbPath,
                self::THUMB_MAX_WIDTH,
                self::THUMB_MAX_HEIGHT,
                $thumbExt
            );
        }

        imagedestroy($image);

        return [
            'path' => $destination,
            'width' => $width,
            'height' => $height,
            'mime' => $mime,
            'orientation' => $width >= $height ? 'landscape' : 'portrait',
            'preview_path' => $previewPath,
            'thumb_path' => $thumbPath,
        ];
    }

    /**
     * Create image resource from source path
     *
     * @param string $sourcePath
     * @param string $mime
     *
     * @return resource
     */
    private function createImageResource($sourcePath, $mime)
    {
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                return imagecreatefromjpeg($sourcePath);
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                if ($image) {
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                }
                return $image;
            default:
                throw new Exception('Unsupported image type.');
        }
    }

    /**
     * Persist an image resource to disk
     *
     * @param resource $image
     * @param string $path
     * @param string $extension
     */
    private function writeImage($image, $path, $extension)
    {
        if ($extension === 'jpg' || $extension === 'jpeg') {
            imagejpeg($image, $path, 95);
            return;
        }

        if ($extension === 'png') {
            imagepng($image, $path, 6);
            return;
        }

        throw new Exception('Unsupported output format.');
    }

    /**
     * Create a resized copy of an image
     *
     * @param resource $source
     * @param string $destination
     * @param int $maxWidth
     * @param int $maxHeight
     * @param string $extension
     */
    private function createResizedCopy($source, $destination, $maxWidth, $maxHeight, $extension)
    {
        $width = imagesx($source);
        $height = imagesy($source);

        list($targetWidth, $targetHeight) = $this->calculateDimensions($width, $height, $maxWidth, $maxHeight);

        if ($targetWidth === $width && $targetHeight === $height) {
            $this->writeImage($source, $destination, $extension);
            return;
        }

        $resampled = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($resampled, false);
        imagesavealpha($resampled, true);

        imagecopyresampled(
            $resampled,
            $source,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $width,
            $height
        );

        $this->writeImage($resampled, $destination, $extension);

        imagedestroy($resampled);
    }

    /**
     * Calculate scaled dimensions while preserving aspect ratio
     *
     * @param int $width
     * @param int $height
     * @param int $maxWidth
     * @param int $maxHeight
     *
     * @return array
     */
    private function calculateDimensions($width, $height, $maxWidth, $maxHeight)
    {
        if ($width <= 0 || $height <= 0) {
            return [0, 0];
        }

        $ratio = min($maxWidth / $width, $maxHeight / $height, 1);

        $targetWidth = (int) round($width * $ratio);
        $targetHeight = (int) round($height * $ratio);

        return [$targetWidth, $targetHeight];
    }
}

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

    public function __construct()
    {
    }

    /**
     * Process an uploaded image and return destination path
     *
     * @param string $sourcePath
     * @param string $destinationPath
     *
     * @return array
     */
    public function process($sourcePath, $destinationPath)
    {
        $info = getimagesize($sourcePath);
        if (!$info) {
            throw new Exception('Unable to read image size.');
        }

        $mime = $info['mime'];
        $image = null;
        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                break;
            default:
                throw new Exception('Unsupported image type.');
        }

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

        if ($destinationExt === 'jpg') {
            imagejpeg($image, $destination, 95);
        } else {
            imagepng($image, $destination, 6);
        }

        imagedestroy($image);

        return [
            'path' => $destination,
            'width' => $width,
            'height' => $height,
            'mime' => $mime,
        ];
    }
}

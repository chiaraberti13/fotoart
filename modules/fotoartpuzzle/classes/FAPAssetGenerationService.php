<?php

class FAPAssetGenerationService
{
    /**
     * Generate derivative assets for the given metadata payload.
     *
     * @param array $metadata
     *
     * @return array
     */
    public function generate(array $metadata)
    {
        $metadata = $this->normaliseMetadata($metadata);
        $source = $this->resolveSourcePath($metadata);
        if (!$source || !file_exists($source)) {
            return $metadata;
        }

        try {
            $source = FAPPathValidator::assertReadablePath($source);
        } catch (Exception $exception) {
            return $metadata;
        }

        $fingerprint = sha1($source . '|' . json_encode(isset($metadata['crop']) ? $metadata['crop'] : []));
        $basePath = rtrim(FAPPathBuilder::getCropsPath(), '/\\') . '/' . $fingerprint;
        if (!is_dir($basePath)) {
            @mkdir($basePath, 0750, true);
        }

        try {
            $basePath = FAPPathValidator::assertWritableDestination($basePath . '/.keep');
            $basePath = dirname($basePath);
        } catch (Exception $exception) {
            return $metadata;
        }

        $metadata['asset_map']['original']['path'] = $source;
        $metadata['asset_map']['original']['filename'] = basename($source);

        $cropResult = $this->generateCrop($source, $basePath, $metadata);
        if ($cropResult) {
            $metadata['asset_map']['cropped'] = $cropResult;
        }

        $previewResult = $this->generatePreview($cropResult ? $cropResult['path'] : $source, $basePath);
        if ($previewResult) {
            $metadata['asset_map']['preview'] = $previewResult;
        }

        $boxResult = $this->generateBoxMockup($metadata, $previewResult ? $previewResult['path'] : ($cropResult ? $cropResult['path'] : $source), $basePath);
        if ($boxResult) {
            $metadata['asset_map']['box_layout'] = $boxResult;
        }

        return $metadata;
    }

    /**
     * Resolve the source image path from metadata.
     *
     * @param array $metadata
     *
     * @return string|null
     */
    private function resolveSourcePath(array $metadata)
    {
        $candidates = [
            ['image', 'path'],
            ['asset_map', 'original', 'path'],
            ['asset_map', 'upload', 'path'],
            ['image_path'],
        ];

        foreach ($candidates as $path) {
            $value = $this->traverse($metadata, $path);
            if ($value && is_string($value)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Lightweight metadata normalisation that mirrors the structure
     * expected by the legacy payload without relying on the core service.
     *
     * @param array $metadata
     *
     * @return array
     */
    private function normaliseMetadata(array $metadata)
    {
        if (!isset($metadata['asset_map']) || !is_array($metadata['asset_map'])) {
            $metadata['asset_map'] = [];
        }

        if (!isset($metadata['box']) || !is_array($metadata['box'])) {
            $metadata['box'] = [];
        }

        if (!isset($metadata['format']) || !is_array($metadata['format'])) {
            $metadata['format'] = [];
        }

        if (!isset($metadata['coordinates']) || !is_array($metadata['coordinates'])) {
            $metadata['coordinates'] = [];
        }

        return $metadata;
    }

    /**
     * Generate cropped asset from metadata payload.
     *
     * @param string $source
     * @param string $basePath
     * @param array $metadata
     *
     * @return array|null
     */
    private function generateCrop($source, $basePath, array $metadata)
    {
        $crop = $this->extractCropData($metadata);
        if (!$crop) {
            return null;
        }

        $mime = null;
        $image = $this->createResource($source, $mime);
        if (!$image) {
            return null;
        }

        $cropWidth = (int) $crop['width'];
        $cropHeight = (int) $crop['height'];
        $cropped = imagecreatetruecolor($cropWidth, $cropHeight);
        imagealphablending($cropped, false);
        imagesavealpha($cropped, true);
        imagecopyresampled(
            $cropped,
            $image,
            0,
            0,
            (int) $crop['x'],
            (int) $crop['y'],
            $cropWidth,
            $cropHeight,
            $cropWidth,
            $cropHeight
        );

        $extension = $mime === 'image/png' ? 'png' : 'jpg';
        $destination = $basePath . '/crop.' . $extension;
        $this->writeResource($cropped, $destination, $extension);

        imagedestroy($cropped);
        imagedestroy($image);

        $destination = FAPPathValidator::assertReadablePath($destination);

        return [
            'path' => $destination,
            'filename' => basename($destination),
            'width' => $cropWidth,
            'height' => $cropHeight,
        ];
    }

    /**
     * Generate preview asset from cropped or original source.
     *
     * @param string $source
     * @param string $basePath
     *
     * @return array|null
     */
    private function generatePreview($source, $basePath)
    {
        $mime = null;
        $image = $this->createResource($source, $mime);
        if (!$image) {
            return null;
        }

        $width = imagesx($image);
        $height = imagesy($image);

        $maxWidth = 900;
        $maxHeight = 900;
        $scale = min($maxWidth / $width, $maxHeight / $height, 1);
        $targetWidth = (int) round($width * $scale);
        $targetHeight = (int) round($height * $scale);

        $resampled = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($resampled, false);
        imagesavealpha($resampled, true);
        imagecopyresampled($resampled, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        $extension = $mime === 'image/png' ? 'png' : 'jpg';
        $destination = $basePath . '/preview.' . $extension;
        $this->writeResource($resampled, $destination, $extension);

        imagedestroy($resampled);
        imagedestroy($image);

        $destination = FAPPathValidator::assertReadablePath($destination);

        return [
            'path' => $destination,
            'filename' => basename($destination),
            'width' => $targetWidth,
            'height' => $targetHeight,
        ];
    }

    /**
     * Generate box layout asset using renderer.
     *
     * @param array $metadata
     * @param string $source
     * @param string $basePath
     *
     * @return array|null
     */
    private function generateBoxMockup(array $metadata, $source, $basePath)
    {
        if (!class_exists('FAPBoxRenderer')) {
            return null;
        }

        $boxInfo = [];
        if (isset($metadata['box']) && is_array($metadata['box']) && $metadata['box']) {
            $boxInfo = $metadata['box'];
        } elseif (isset($metadata['box_info']) && is_array($metadata['box_info']) && $metadata['box_info']) {
            $boxInfo = $metadata['box_info'];
        } elseif (isset($metadata['box_payload']) && is_array($metadata['box_payload']) && $metadata['box_payload']) {
            $boxInfo = $metadata['box_payload'];
        }

        if (!$boxInfo) {
            $boxInfo = [];
        }

        if (!isset($boxInfo['color']) && isset($metadata['color'])) {
            $boxInfo['color'] = $metadata['color'];
        }

        if (!isset($boxInfo['font']) && isset($metadata['font'])) {
            $boxInfo['font'] = $metadata['font'];
        }

        if (!$boxInfo) {
            return null;
        }

        $renderer = new FAPBoxRenderer();
        $destination = $basePath . '/box.png';
        try {
            $renderer->renderFromImage($source, $destination, [
                'text' => isset($boxInfo['text']) ? $boxInfo['text'] : '',
                'color' => isset($boxInfo['color']) ? $boxInfo['color'] : null,
                'font' => isset($boxInfo['font']) ? $boxInfo['font'] : null,
                'template' => isset($boxInfo['template']) ? $boxInfo['template'] : null,
            ]);
        } catch (Exception $exception) {
            return null;
        }

        if (!file_exists($destination)) {
            return null;
        }

        try {
            $destination = FAPPathValidator::assertReadablePath($destination);
        } catch (Exception $exception) {
            return null;
        }

        $size = @getimagesize($destination);

        return [
            'path' => $destination,
            'filename' => basename($destination),
            'width' => is_array($size) && isset($size[0]) ? (int) $size[0] : null,
            'height' => is_array($size) && isset($size[1]) ? (int) $size[1] : null,
        ];
    }

    /**
     * Extract crop information from metadata payload.
     *
     * @param array $metadata
     *
     * @return array|null
     */
    private function extractCropData(array $metadata)
    {
        $candidates = [
            ['crop'],
            ['format', 'crop'],
            ['coordinates', 'selection'],
            ['format', 'coordinates', 'selection'],
        ];

        foreach ($candidates as $candidate) {
            $value = $this->traverse($metadata, $candidate);
            if (is_array($value) && isset($value['width'], $value['height'])) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Create image resource from file path.
     *
     * @param string $path
     * @param string $mime
     *
     * @return resource|false
     */
    private function createResource($path, &$mime)
    {
        $info = @getimagesize($path);
        if (!$info) {
            return false;
        }

        $mime = isset($info['mime']) ? $info['mime'] : 'image/jpeg';
        switch ($mime) {
            case 'image/png':
                $resource = imagecreatefrompng($path);
                if ($resource) {
                    imagealphablending($resource, false);
                    imagesavealpha($resource, true);
                }
                return $resource;
            case 'image/gif':
                return imagecreatefromgif($path);
            case 'image/jpeg':
            case 'image/jpg':
            default:
                return imagecreatefromjpeg($path);
        }
    }

    /**
     * Persist GD resource to disk.
     *
     * @param resource $resource
     * @param string $destination
     * @param string $extension
     */
    private function writeResource($resource, $destination, $extension)
    {
        $destination = FAPPathValidator::assertWritableDestination($destination);

        switch ($extension) {
            case 'png':
                imagepng($resource, $destination, 6);
                break;
            case 'gif':
                imagegif($resource, $destination);
                break;
            case 'jpg':
            case 'jpeg':
            default:
                imagejpeg($resource, $destination, 95);
        }
    }

    /**
     * Traverse metadata array.
     *
     * @param array $metadata
     * @param array $path
     *
     * @return mixed|null
     */
    private function traverse(array $metadata, array $path)
    {
        $cursor = $metadata;
        foreach ($path as $segment) {
            if (!is_array($cursor) || !array_key_exists($segment, $cursor)) {
                return null;
            }

            $cursor = $cursor[$segment];
        }

        return $cursor;
    }
}


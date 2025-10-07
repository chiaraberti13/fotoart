<?php

class FAPPuzzleRepository
{
    /**
     * Retrieve all puzzle formats ordered by pieces and name.
     *
     * @param bool $onlyActive
     *
     * @return array
     */
    public function getFormats($onlyActive = true)
    {
        $query = new DbQuery();
        $query->select('*')
            ->from('fap_puzzle_format')
            ->orderBy('position ASC, pieces ASC, name ASC');

        if ($onlyActive) {
            $query->where('active = 1');
        }

        $rows = Db::getInstance()->executeS($query);
        if (!is_array($rows)) {
            return [];
        }

        return array_map(function (array $row) {
            return [
                'id' => (int) $row['id_fap_puzzle_format'],
                'reference' => (string) $row['reference'],
                'name' => (string) $row['name'],
                'pieces' => (int) $row['pieces'],
                'width' => (float) $row['width_cm'],
                'height' => (float) $row['height_cm'],
                'width_cm' => (float) $row['width_cm'],
                'height_cm' => (float) $row['height_cm'],
                'shape' => $row['shape'] !== '' ? (string) $row['shape'] : null,
                'price' => $row['price'] !== null ? (float) $row['price'] : null,
                'image' => $row['image'] !== '' ? (string) $row['image'] : null,
                'position' => isset($row['position']) ? (int) $row['position'] : 0,
                'payload' => $row['payload'] ? $this->decodeJson($row['payload']) : [],
            ];
        }, $rows);
    }

    /**
     * Retrieve all box templates ordered by position and name.
     *
     * @param bool $onlyActive
     *
     * @return array
     */
    public function getBoxes($onlyActive = true)
    {
        $query = new DbQuery();
        $query->select('*')
            ->from('fap_puzzle_box')
            ->orderBy('position ASC, name ASC');

        if ($onlyActive) {
            $query->where('active = 1');
        }

        $rows = Db::getInstance()->executeS($query);
        if (!is_array($rows)) {
            return [];
        }

        return array_map(function (array $row) {
            return [
                'id' => (int) $row['id_fap_puzzle_box'],
                'reference' => (string) $row['reference'],
                'name' => (string) $row['name'],
                'template' => $row['template'] !== '' ? (string) $row['template'] : null,
                'preview' => $row['preview'] !== '' ? (string) $row['preview'] : null,
                'color' => $row['color'] !== '' ? strtoupper((string) $row['color']) : null,
                'textColor' => $row['text_color'] !== '' ? strtoupper((string) $row['text_color']) : null,
                'position' => isset($row['position']) ? (int) $row['position'] : 0,
                'payload' => $row['payload'] ? $this->decodeJson($row['payload']) : [],
            ];
        }, $rows);
    }

    /**
     * Retrieve a single format by identifier.
     *
     * @param int $idFormat
     *
     * @return array|null
     */
    public function getFormatById($idFormat)
    {
        $query = new DbQuery();
        $query->select('*')
            ->from('fap_puzzle_format')
            ->where('id_fap_puzzle_format = ' . (int) $idFormat)
            ->limit(1);

        $row = Db::getInstance()->getRow($query);
        if (!$row) {
            return null;
        }

        $formats = $this->getFormats(false);
        foreach ($formats as $format) {
            if ((int) $format['id'] === (int) $idFormat) {
                return $format;
            }
        }

        return null;
    }

    /**
     * Retrieve a single box template by identifier.
     *
     * @param int $idBox
     *
     * @return array|null
     */
    public function getBoxById($idBox)
    {
        $query = new DbQuery();
        $query->select('*')
            ->from('fap_puzzle_box')
            ->where('id_fap_puzzle_box = ' . (int) $idBox)
            ->limit(1);

        $row = Db::getInstance()->getRow($query);
        if (!$row) {
            return null;
        }

        $boxes = $this->getBoxes(false);
        foreach ($boxes as $box) {
            if ((int) $box['id'] === (int) $idBox) {
                return $box;
            }
        }

        return null;
    }

    /**
     * Decode a JSON string and ensure an array is always returned.
     *
     * @param string $json
     *
     * @return array
     */
    private function decodeJson($json)
    {
        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : [];
    }
}

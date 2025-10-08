<?php

class FAPSessionService
{
    /**
     * Maximum session lifetime in seconds (48h)
     */
    private const DEFAULT_TTL = 172800;

    /**
     * Maximum length allowed for a session identifier.
     */
    private const SESSION_ID_MAX_LENGTH = 64;

    /**
     * Create or update a session payload.
     *
     * @param array $payload
     *
     * @return array
     */
    public function manage(array $payload)
    {
        $sessionId = '';
        if (isset($payload['session_id'])) {
            try {
                $sessionId = $this->sanitizeSessionId($payload['session_id']);
            } catch (Exception $exception) {
                $sessionId = '';
            }
        }

        if ($sessionId === '') {
            $sessionId = $this->generateSessionId();
        }

        $data = $this->load($sessionId);
        $data = array_merge($data, $payload);
        $data['session_id'] = $sessionId;
        $data['updated_at'] = time();
        $data['ttl'] = isset($payload['ttl']) ? (int) $payload['ttl'] : self::DEFAULT_TTL;

        $this->persist($sessionId, $data);

        return [
            'session_id' => $sessionId,
            'ttl' => $data['ttl'],
            'updated_at' => $data['updated_at'],
        ];
    }

    /**
     * Update an existing session with a diff payload.
     *
     * @param string $sessionId
     * @param array $diff
     *
     * @return array
     */
    public function update($sessionId, array $diff)
    {
        $sessionId = $this->sanitizeSessionId($sessionId);

        $data = $this->load($sessionId);
        if (!$data) {
            throw new Exception('Session not found');
        }

        $data = array_merge($data, $diff);
        $data['updated_at'] = time();
        $this->persist($sessionId, $data);

        return $data;
    }

    /**
     * Retrieve session payload for restoration.
     *
     * @param string $sessionId
     *
     * @return array
     */
    public function restore($sessionId)
    {
        $sessionId = $this->sanitizeSessionId($sessionId);
        $data = $this->load($sessionId);
        if (!$data) {
            return [];
        }

        $ttl = isset($data['ttl']) ? (int) $data['ttl'] : self::DEFAULT_TTL;
        $updatedAt = isset($data['updated_at']) ? (int) $data['updated_at'] : 0;
        if ($ttl > 0 && $updatedAt > 0 && ($updatedAt + $ttl) < time()) {
            $this->delete($sessionId);
            return [];
        }

        return $data;
    }

    /**
     * Remove stale sessions from disk.
     */
    public function cleanup()
    {
        $path = FAPPathBuilder::getSessionsPath();
        if (!is_dir($path)) {
            return;
        }

        $now = time();
        foreach (glob($path . '/*.json') as $file) {
            $payload = json_decode((string) @file_get_contents($file), true);
            if (!is_array($payload)) {
                @unlink($file);
                continue;
            }

            $ttl = isset($payload['ttl']) ? (int) $payload['ttl'] : self::DEFAULT_TTL;
            $updatedAt = isset($payload['updated_at']) ? (int) $payload['updated_at'] : 0;
            if ($ttl > 0 && $updatedAt > 0 && ($updatedAt + $ttl) < $now) {
                @unlink($file);
            }
        }
    }

    /**
     * Load session payload from disk.
     *
     * @param string $sessionId
     *
     * @return array
     */
    private function load($sessionId)
    {
        $file = $this->getPath($sessionId);
        if (!is_file($file)) {
            return [];
        }

        $raw = (string) @file_get_contents($file);
        $data = json_decode($raw, true);

        return is_array($data) ? $data : [];
    }

    /**
     * Persist payload to disk.
     *
     * @param string $sessionId
     * @param array $data
     */
    private function persist($sessionId, array $data)
    {
        $file = $this->getPath($sessionId);
        if (!is_dir(dirname($file))) {
            @mkdir(dirname($file), 0750, true);
        }

        $encoded = json_encode($data);
        if (false === file_put_contents($file, $encoded)) {
            throw new Exception('Unable to persist session data');
        }
    }

    /**
     * Delete session from disk.
     *
     * @param string $sessionId
     */
    private function delete($sessionId)
    {
        $file = $this->getPath($sessionId);
        if (is_file($file)) {
            @unlink($file);
        }
    }

    /**
     * Generate session identifier.
     *
     * @return string
     */
    private function generateSessionId()
    {
        return sha1(uniqid('fap_session', true));
    }

    /**
     * Build session file path.
     *
     * @param string $sessionId
     *
     * @return string
     */
    private function getPath($sessionId)
    {
        $sessionId = $this->sanitizeSessionId($sessionId);

        $basePath = rtrim(FAPPathBuilder::getSessionsPath(), '/\\');

        return $basePath . '/' . $sessionId . '.json';
    }

    /**
     * Ensure the session identifier is safe to use on disk.
     *
     * @param mixed $sessionId
     *
     * @return string
     */
    private function sanitizeSessionId($sessionId)
    {
        $sessionId = trim((string) $sessionId);
        if ($sessionId === '') {
            throw new Exception('Missing session identifier');
        }

        if (!preg_match('/^[A-Za-z0-9_-]+$/', $sessionId)) {
            throw new Exception('Invalid session identifier');
        }

        if (strlen($sessionId) > self::SESSION_ID_MAX_LENGTH) {
            throw new Exception('Invalid session identifier');
        }

        return $sessionId;
    }
}


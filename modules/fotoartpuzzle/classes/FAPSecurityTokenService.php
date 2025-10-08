<?php

class FAPSecurityTokenService
{
    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $algorithm;

    /**
     * @param string $secret
     * @param string $algorithm
     */
    public function __construct($secret, $algorithm = 'sha256')
    {
        $secret = (string) $secret;
        if ($secret === '') {
            throw new InvalidArgumentException('Token secret cannot be empty');
        }

        $algorithm = (string) $algorithm;
        if (!in_array($algorithm, hash_hmac_algos(), true)) {
            throw new InvalidArgumentException('Unsupported hashing algorithm for token service');
        }

        $this->secret = $secret;
        $this->algorithm = $algorithm;
    }

    /**
     * Issue a signed token with a TTL.
     *
     * @param array $claims
     * @param int $ttl
     *
     * @return array{token: string, payload: array}
     */
    public function issue(array $claims, $ttl)
    {
        $ttl = (int) $ttl;
        if ($ttl <= 0) {
            throw new InvalidArgumentException('TTL must be a positive integer');
        }

        $now = time();
        $payload = [
            'iat' => $now,
            'exp' => $now + $ttl,
            'nonce' => bin2hex(random_bytes(16)),
            'claims' => $claims,
        ];

        $encodedPayload = $this->encode($payload);
        $signature = $this->sign($encodedPayload);

        return [
            'token' => 'v1.' . $encodedPayload . '.' . $signature,
            'payload' => $payload,
        ];
    }

    /**
     * Validate token and return payload when successful.
     *
     * @param string $token
     * @param array $expectedClaims
     *
     * @return array
     */
    public function validate($token, array $expectedClaims = [])
    {
        $parts = explode('.', (string) $token);
        if (count($parts) !== 3 || $parts[0] !== 'v1') {
            throw new RuntimeException('Malformed token received');
        }

        list(, $encodedPayload, $encodedSignature) = $parts;
        $payload = $this->decode($encodedPayload);

        if (!is_array($payload) || !isset($payload['exp'], $payload['claims'])) {
            throw new RuntimeException('Token payload missing required fields');
        }

        if ((int) $payload['exp'] < time()) {
            throw new RuntimeException('Token has expired');
        }

        $expectedSignature = $this->sign($encodedPayload);
        if (!hash_equals($expectedSignature, $encodedSignature)) {
            throw new RuntimeException('Token signature mismatch');
        }

        if (!is_array($payload['claims'])) {
            throw new RuntimeException('Token claims payload is invalid');
        }

        foreach ($expectedClaims as $key => $value) {
            if (!array_key_exists($key, $payload['claims']) || $payload['claims'][$key] !== $value) {
                throw new RuntimeException('Token claims do not match the expected values');
            }
        }

        return $payload;
    }

    /**
     * Check if the provided token matches expected claims.
     *
     * @param string $token
     * @param array $expectedClaims
     *
     * @return bool
     */
    public function isValid($token, array $expectedClaims = [])
    {
        try {
            $this->validate($token, $expectedClaims);
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * Create HMAC signature for payload.
     *
     * @param string $encodedPayload
     *
     * @return string
     */
    private function sign($encodedPayload)
    {
        $signature = hash_hmac($this->algorithm, $encodedPayload, $this->secret, true);

        return $this->base64UrlEncode($signature);
    }

    /**
     * Encode payload using JSON and Base64 URL safe encoding.
     *
     * @param array $payload
     *
     * @return string
     */
    private function encode(array $payload)
    {
        $json = json_encode($payload);
        if (false === $json) {
            throw new RuntimeException('Unable to encode token payload');
        }

        return $this->base64UrlEncode($json);
    }

    /**
     * Decode payload from Base64 URL safe encoding.
     *
     * @param string $encoded
     *
     * @return array
     */
    private function decode($encoded)
    {
        $decoded = base64_decode($this->base64UrlDecode($encoded), true);
        if (false === $decoded) {
            throw new RuntimeException('Unable to decode token payload');
        }

        $payload = json_decode($decoded, true);
        if (!is_array($payload)) {
            throw new RuntimeException('Token payload is not valid JSON');
        }

        return $payload;
    }

    /**
     * Base64 URL safe encode helper.
     *
     * @param string $data
     *
     * @return string
     */
    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64 URL safe decode helper.
     *
     * @param string $data
     *
     * @return string
     */
    private function base64UrlDecode($data)
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return strtr($data, '-_', '+/');
    }
}

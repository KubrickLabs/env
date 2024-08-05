<?php

namespace kubricklabs\base\env;

use Exception;

final class Env
{
    private $env_path;
    private $variables = [];

    public function __construct($path = null)
    {
        $this->env_path = $path ?? __DIR__ . '/../../.env';

        try {
            if ($this->env_path) {
                $this->loadEnv();
            }
        } catch (Exception $e) {
            // Log the error message instead of echoing it
            error_log('Error loading .env file: ' . $e->getMessage());
        }
    }

    protected function loadEnv()
    {
        if (!file_exists($this->env_path)) {
            throw new Exception('The .env file does not exist at the specified path.');
        }

        $envContent = file_get_contents($this->env_path);
        $lines = explode("\n", $envContent);

        foreach ($lines as $line) {
            // Trim whitespace and skip empty lines and comments
            $line = trim($line);
            if ($line === '' || strpos($line, '#') === 0) {
                continue;
            }

            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                // Ensure the key is a valid identifier
                if (preg_match('/^[A-Z0-9_]+$/', $key)) {
                    $this->variables[$key] = $value;
                }
            }
        }
    }

    public function get($key, $default = null)
    {
        // Use array_key_exists to avoid issues with null values in $_ENV
        return array_key_exists($key, $this->variables) ? $this->variables[$key] : $default;
    }

    public function __destruct()
    {
        // Optional cleanup or logging
    }
}

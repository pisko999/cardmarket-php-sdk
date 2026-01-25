<?php

declare(strict_types=1);

/**
 * E2E Tests Bootstrap.
 *
 * Loads environment variables and creates the Cardmarket client.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Pisko\CardMarket\Cardmarket;
use Pisko\CardMarket\HttpClient\HttpClientCreator;

// Load .env file (skip validation if running PHPStan)
$isPhpStan = defined('__PHPSTAN_RUNNING__') || (PHP_SAPI === 'cli' && str_contains(implode(' ', $_SERVER['argv'] ?? []), 'phpstan'));
$envFile = __DIR__ . '/.env';

if (!file_exists($envFile)) {
    if ($isPhpStan) {
        // Create dummy values for static analysis
        $_ENV['CARDMARKET_APP_TOKEN'] = 'phpstan_dummy';
        $_ENV['CARDMARKET_APP_SECRET'] = 'phpstan_dummy';
        $_ENV['CARDMARKET_ACCESS_TOKEN'] = 'phpstan_dummy';
        $_ENV['CARDMARKET_ACCESS_SECRET'] = 'phpstan_dummy';
    } else {
        echo "\033[31mError: .env file not found!\033[0m\n";
        echo "Copy .env.example to .env and fill in your Cardmarket API credentials.\n";
        echo "  cp e2e/.env.example e2e/.env\n";
        exit(1);
    }
}

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines !== false) {
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }
            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }
    }
}

// Validate required credentials (skip for PHPStan)
if (!$isPhpStan) {
    $required = [
        'CARDMARKET_APP_TOKEN',
        'CARDMARKET_APP_SECRET',
        'CARDMARKET_ACCESS_TOKEN',
        'CARDMARKET_ACCESS_SECRET',
    ];

    foreach ($required as $key) {
        if (empty($_ENV[$key])) {
            echo "\033[31mError: Missing required environment variable: {$key}\033[0m\n";
            exit(1);
        }
    }
}

/**
 * Create and return a configured Cardmarket client.
 */
function createCardmarketClient(): Cardmarket
{
    $httpCreator = new HttpClientCreator();
    $httpCreator
        ->setApplicationToken($_ENV['CARDMARKET_APP_TOKEN'])
        ->setApplicationSecret($_ENV['CARDMARKET_APP_SECRET'])
        ->setAccessToken($_ENV['CARDMARKET_ACCESS_TOKEN'])
        ->setAccessSecret($_ENV['CARDMARKET_ACCESS_SECRET']);

    return new Cardmarket($httpCreator);
}

/**
 * Get test configuration value.
 */
function getTestConfig(string $key, mixed $default = null): mixed
{
    return $_ENV[$key] ?? $default;
}

/**
 * Output colored message.
 */
function output(string $message, string $color = 'white'): void
{
    $colors = [
        'red' => "\033[31m",
        'green' => "\033[32m",
        'yellow' => "\033[33m",
        'blue' => "\033[34m",
        'white' => "\033[37m",
        'reset' => "\033[0m",
    ];

    echo ($colors[$color] ?? '') . $message . $colors['reset'] . "\n";
}

/**
 * Output success message.
 */
function success(string $message): void
{
    output("✓ {$message}", 'green');
}

/**
 * Output error message.
 */
function error(string $message): void
{
    output("✗ {$message}", 'red');
}

/**
 * Output info message.
 */
function info(string $message): void
{
    output("ℹ {$message}", 'blue');
}

/**
 * Output warning message.
 */
function warning(string $message): void
{
    output("⚠ {$message}", 'yellow');
}

/**
 * Save API response to file for debugging.
 *
 * @param string $name Name/identifier for the response (will be sanitized for filename)
 * @param mixed $response The response data to save
 * @param string|null $testClass Optional test class name for better organization
 *
 * @return string The path to the saved file
 */
function saveResponse(string $name, mixed $response, ?string $testClass = null): string
{
    $responsesDir = __DIR__ . '/responses';

    // Create subdirectory for test class if provided
    if ($testClass !== null) {
        $responsesDir .= '/' . preg_replace('/[^a-zA-Z0-9_-]/', '', $testClass);
    }

    if (!is_dir($responsesDir)) {
        mkdir($responsesDir, 0755, true);
    }

    // Sanitize filename
    $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
    $filename = preg_replace('/_+/', '_', $filename);
    $filename = trim($filename, '_');

    // Add timestamp for uniqueness
    $timestamp = date('Ymd_His');
    $filepath = "{$responsesDir}/{$filename}_{$timestamp}.json";

    // Prepare content
    $content = [
        'name' => $name,
        'timestamp' => date('c'),
        'response' => $response,
    ];

    file_put_contents($filepath, json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    if (getenv('E2E_DEBUG') === 'true') {
        info("Response saved to: {$filepath}");
    }

    return $filepath;
}

/**
 * Check if response logging is enabled.
 */
function isResponseLoggingEnabled(): bool
{
    return getenv('E2E_LOG_RESPONSES') === 'true' || getenv('E2E_DEBUG') === 'true';
}


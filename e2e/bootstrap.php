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

// Load .env file
$envFile = __DIR__ . '/.env';
if (!file_exists($envFile)) {
    echo "\033[31mError: .env file not found!\033[0m\n";
    echo "Copy .env.example to .env and fill in your Cardmarket API credentials.\n";
    echo "  cp e2e/.env.example e2e/.env\n";
    exit(1);
}

$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    if (str_starts_with(trim($line), '#')) {
        continue;
    }
    if (str_contains($line, '=')) {
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

// Validate required credentials
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

    // Use sandbox if configured
    if (($_ENV['CARDMARKET_SANDBOX'] ?? 'false') === 'true') {
        $httpCreator->setSandbox();
    }

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

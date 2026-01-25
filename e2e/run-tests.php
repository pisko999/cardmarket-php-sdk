#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * E2E Test Runner.
 *
 * Usage:
 *   php e2e/run-tests.php                    # Run all tests
 *   php e2e/run-tests.php --suite=games      # Run specific suite
 *   php e2e/run-tests.php --test=GamesTest   # Run specific test class
 *   php e2e/run-tests.php --list             # List available tests
 *   php e2e/run-tests.php --clean            # Delete all saved JSON responses
 *   E2E_DEBUG=true php e2e/run-tests.php     # Run with debug output
 */

// Parse command line arguments first (before bootstrap)
$options = getopt('', ['suite::', 'test::', 'list', 'clean', 'help']);

if (isset($options['help'])) {
    echo <<<HELP
Cardmarket SDK E2E Test Runner

Usage:
  php e2e/run-tests.php [options]

Options:
  --suite=NAME    Run specific test suite (games, products, stock, orders, wantslists, account, cart)
  --test=NAME     Run specific test class (e.g., GamesTest)
  --list          List available test suites and classes
  --clean         Delete all saved JSON responses from responses/ directory
  --help          Show this help message

Environment:
  E2E_DEBUG=true  Enable debug output

Examples:
  php e2e/run-tests.php                    # Run all tests
  php e2e/run-tests.php --suite=games      # Run games tests only
  php e2e/run-tests.php --test=GamesTest   # Run GamesTest class
  php e2e/run-tests.php --clean            # Clean up response files
  E2E_DEBUG=true php e2e/run-tests.php     # With debug output

HELP;
    exit(0);
}

// Clean responses directory
if (isset($options['clean'])) {
    $responsesDir = __DIR__ . '/responses';
    $deleted = 0;
    
    if (is_dir($responsesDir)) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($responsesDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'json') {
                unlink($file->getPathname());
                $deleted++;
            }
        }
    }
    
    echo "\033[32m✓ Deleted {$deleted} JSON response files\033[0m\n";
    exit(0);
}

// Test suites configuration
$suites = [
    'games' => ['GamesTest', 'ExpansionsTest'],
    'products' => ['ProductsTest', 'ArticlesTest', 'MetaproductsTest', 'PricesTest'],
    'stock' => ['StockTest'],
    'orders' => ['OrdersTest'],
    'wantslists' => ['WantslistsTest'],
    'account' => ['AccountTest', 'MessagesTest'],
    'cart' => ['CartTest'],
    'users' => ['UsersTest'],
];

// List available tests (no bootstrap required)
if (isset($options['list'])) {
    echo "\n\033[34mAvailable test suites:\033[0m\n";
    foreach ($suites as $name => $tests) {
        echo "  \033[37m{$name}:\033[0m\n";
        foreach ($tests as $test) {
            $file = __DIR__ . "/Tests/{$test}.php";
            if (file_exists($file)) {
                echo "    \033[32m✓ {$test}\033[0m\n";
            } else {
                echo "    \033[31m✗ {$test}\033[0m\n";
            }
        }
    }
    echo "\n";
    exit(0);
}

// Load bootstrap and TestCase for actual test execution
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/TestCase.php';

// Determine which tests to run
$testsToRun = [];

if (isset($options['test'])) {
    $testsToRun[] = $options['test'];
} elseif (isset($options['suite'])) {
    $suite = $options['suite'];
    if (!isset($suites[$suite])) {
        error("Unknown suite: {$suite}");
        output("Available suites: " . implode(', ', array_keys($suites)), 'white');
        exit(1);
    }
    $testsToRun = $suites[$suite];
} else {
    // Run all tests
    foreach ($suites as $tests) {
        $testsToRun = array_merge($testsToRun, $tests);
    }
}

// Load and run tests
output("\n" . str_repeat('=', 60), 'blue');
output('Cardmarket SDK E2E Tests', 'blue');
output(str_repeat('=', 60), 'blue');
output('Date: ' . date('Y-m-d H:i:s'), 'white');

$client = createCardmarketClient();

$totalPassed = 0;
$totalFailed = 0;
$totalSkipped = 0;
$testResults = [];

foreach ($testsToRun as $testName) {
    $testFile = __DIR__ . "/Tests/{$testName}.php";

    if (!file_exists($testFile)) {
        warning("Test file not found: {$testFile}");
        continue;
    }

    require_once $testFile;

    $className = "CardmarketE2E\\Tests\\{$testName}";

    if (!class_exists($className)) {
        error("Test class not found: {$className}");
        continue;
    }

    $test = new $className($client);
    $results = $test->run();

    $totalPassed += $results['passed'];
    $totalFailed += $results['failed'];
    $totalSkipped += $results['skipped'];
    $testResults[$testName] = $results;
}

// Final summary
output("\n" . str_repeat('=', 60), 'blue');
output('FINAL SUMMARY', 'blue');
output(str_repeat('=', 60), 'blue');

foreach ($testResults as $testName => $results) {
    $status = $results['failed'] > 0 ? '✗' : '✓';
    $color = $results['failed'] > 0 ? 'red' : 'green';
    output(sprintf(
        "%s %s: %d passed, %d failed, %d skipped",
        $status,
        $testName,
        $results['passed'],
        $results['failed'],
        $results['skipped'],
    ), $color);
}

output(str_repeat('-', 60), 'white');
$totalColor = $totalFailed > 0 ? 'red' : 'green';
output(sprintf(
    "TOTAL: %d passed, %d failed, %d skipped",
    $totalPassed,
    $totalFailed,
    $totalSkipped,
), $totalColor);

exit($totalFailed > 0 ? 1 : 0);

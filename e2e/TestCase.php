<?php

declare(strict_types=1);

namespace CardmarketE2E;

/**
 * Base class for E2E tests.
 */
abstract class TestCase
{
    protected \Pisko\CardMarket\Cardmarket $client;

    protected array $results = [];

    protected int $passed = 0;

    protected int $failed = 0;

    protected int $skipped = 0;

    public function __construct(\Pisko\CardMarket\Cardmarket $client)
    {
        $this->client = $client;
    }

    /**
     * Run all test methods.
     */
    public function run(): array
    {
        $reflection = new \ReflectionClass($this);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        output("\n" . str_repeat('=', 60), 'blue');
        output('Running: ' . $reflection->getShortName(), 'blue');
        output(str_repeat('=', 60), 'blue');

        // Separate cleanup tests from regular tests
        $regularTests = [];
        $cleanupTests = [];

        foreach ($methods as $method) {
            $name = $method->getName();
            if (str_starts_with($name, 'test')) {
                if (str_contains(strtolower($name), 'cleanup')) {
                    $cleanupTests[] = $name;
                } else {
                    $regularTests[] = $name;
                }
            }
        }

        // Run regular tests first
        foreach ($regularTests as $testName) {
            $this->runTest($testName);
        }

        // Always run cleanup tests at the end, even if other tests failed
        if (!empty($cleanupTests)) {
            output("\n" . str_repeat('-', 40), 'yellow');
            output('Running cleanup...', 'yellow');
            foreach ($cleanupTests as $testName) {
                $this->runTest($testName);
            }
        }

        $this->printSummary();

        return [
            'passed' => $this->passed,
            'failed' => $this->failed,
            'skipped' => $this->skipped,
        ];
    }

    /**
     * Run a single test method.
     */
    protected function runTest(string $methodName): void
    {
        $testName = $this->formatTestName($methodName);
        output("\n▶ {$testName}", 'white');

        try {
            $this->{$methodName}();
            success("PASSED");
            $this->passed++;
            $this->results[$methodName] = 'passed';
        } catch (SkipTestException $e) {
            warning("SKIPPED: {$e->getMessage()}");
            $this->skipped++;
            $this->results[$methodName] = 'skipped';
        } catch (\Throwable $e) {
            error("FAILED: {$e->getMessage()}");
            if (getenv('E2E_DEBUG') === 'true') {
                output("  " . $e->getTraceAsString(), 'red');
            }
            $this->failed++;
            $this->results[$methodName] = 'failed';
        }
    }

    /**
     * Format test method name for display.
     */
    protected function formatTestName(string $methodName): string
    {
        $name = preg_replace('/^test/', '', $methodName);
        $name = preg_replace('/([A-Z])/', ' $1', $name);

        return trim($name);
    }

    /**
     * Print test summary.
     */
    protected function printSummary(): void
    {
        output("\n" . str_repeat('-', 40), 'white');
        output(sprintf(
            'Results: %d passed, %d failed, %d skipped',
            $this->passed,
            $this->failed,
            $this->skipped,
        ), $this->failed > 0 ? 'red' : 'green');
    }

    /**
     * Assert that a condition is true.
     */
    protected function assertTrue(bool $condition, string $message = ''): void
    {
        if (!$condition) {
            throw new \RuntimeException($message ?: 'Assertion failed: expected true');
        }
    }

    /**
     * Assert that a condition is false.
     */
    protected function assertFalse(bool $condition, string $message = ''): void
    {
        if ($condition) {
            throw new \RuntimeException($message ?: 'Assertion failed: expected false');
        }
    }

    /**
     * Assert that two values are equal.
     */
    protected function assertEquals(mixed $expected, mixed $actual, string $message = ''): void
    {
        if ($expected !== $actual) {
            throw new \RuntimeException(
                $message ?: sprintf('Assertion failed: expected %s, got %s', var_export($expected, true), var_export($actual, true)),
            );
        }
    }

    /**
     * Assert that a value is not empty.
     */
    protected function assertNotEmpty(mixed $value, string $message = ''): void
    {
        if (empty($value)) {
            throw new \RuntimeException($message ?: 'Assertion failed: expected non-empty value');
        }
    }

    /**
     * Assert that an array has a specific key.
     */
    protected function assertArrayHasKey(string|int $key, array $array, string $message = ''): void
    {
        if (!array_key_exists($key, $array)) {
            throw new \RuntimeException($message ?: "Assertion failed: array does not have key '{$key}'");
        }
    }

    /**
     * Assert that a value is an array.
     */
    protected function assertIsArray(mixed $value, string $message = ''): void
    {
        if (!is_array($value)) {
            throw new \RuntimeException($message ?: 'Assertion failed: expected array');
        }
    }

    /**
     * Assert that an array has a minimum count.
     */
    protected function assertCountGreaterThan(int $minCount, array $array, string $message = ''): void
    {
        if (count($array) <= $minCount) {
            throw new \RuntimeException(
                $message ?: sprintf('Assertion failed: expected count > %d, got %d', $minCount, count($array)),
            );
        }
    }

    /**
     * Skip the current test.
     */
    protected function skip(string $reason): void
    {
        throw new SkipTestException($reason);
    }

    /**
     * Assert that a callable throws an exception.
     *
     * @param callable $callable The callable that should throw
     * @param string|null $expectedExceptionClass Expected exception class (null = any)
     * @param string|null $expectedMessageContains Expected message substring (null = any)
     */
    protected function assertThrows(
        callable $callable,
        ?string $expectedExceptionClass = null,
        ?string $expectedMessageContains = null,
        string $message = '',
    ): void {
        try {
            $callable();
            throw new \RuntimeException(
                $message ?: 'Expected exception was not thrown',
            );
        } catch (\Throwable $e) {
            // Re-throw if this is our own "no exception" error
            if ($e->getMessage() === ($message ?: 'Expected exception was not thrown')) {
                throw $e;
            }

            // Check exception class if specified
            if ($expectedExceptionClass !== null && !($e instanceof $expectedExceptionClass)) {
                throw new \RuntimeException(
                    $message ?: sprintf(
                        'Expected exception of type %s, got %s: %s',
                        $expectedExceptionClass,
                        $e::class,
                        $e->getMessage(),
                    ),
                );
            }

            // Check message contains if specified
            if ($expectedMessageContains !== null && !str_contains($e->getMessage(), $expectedMessageContains)) {
                throw new \RuntimeException(
                    $message ?: sprintf(
                        'Expected exception message to contain "%s", got: %s',
                        $expectedMessageContains,
                        $e->getMessage(),
                    ),
                );
            }

            // Exception was thrown as expected
            info(sprintf('Caught expected exception: %s', $e->getMessage()));
        }
    }

    /**
     * Assert that a value is an instance of a class.
     */
    protected function assertInstanceOf(string $className, mixed $value, string $message = ''): void
    {
        if (!($value instanceof $className)) {
            throw new \RuntimeException(
                $message ?: sprintf('Expected instance of %s, got %s', $className, is_object($value) ? $value::class : gettype($value)),
            );
        }
    }

    /**
     * Log debug information.
     */
    protected function debug(string $message, mixed $data = null): void
    {
        if (getenv('E2E_DEBUG') === 'true') {
            info("[DEBUG] {$message}");
            if ($data !== null) {
                print_r($data);
            }
        }
    }
}

/**
 * Exception for skipped tests.
 */
class SkipTestException extends \Exception
{
}

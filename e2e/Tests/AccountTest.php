<?php

declare(strict_types=1);

namespace CardmarketE2E\Tests;

use CardmarketE2E\TestCase;

/**
 * E2E Tests for Account API.
 */
class AccountTest extends TestCase
{
    /**
     * Test getting account information.
     */
    public function testGetAccountInformation(): void
    {
        $result = $this->client->account()->getAccountInformation();
        $this->logResponse('getAccountInformation', $result);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('account', $result);

        $account = $result['account'];
        $this->assertArrayHasKey('idUser', $account);
        $this->assertArrayHasKey('username', $account);

        info(sprintf(
            'Account: %s (ID: %d)',
            $account['username'],
            $account['idUser'],
        ));

        // Store user ID for other tests
        if (empty(getTestConfig('TEST_USER_ID'))) {
            warning('Tip: Set TEST_USER_ID=' . $account['idUser'] . ' in .env');
        }
    }

    /**
     * Test account information is consistent.
     */
    public function testAccountInformationConsistency(): void
    {
        $result1 = $this->client->account()->getAccountInformation();
        $this->logResponse('getAccountInformation_1', $result1);
        $result2 = $this->client->account()->getAccountInformation();
        $this->logResponse('getAccountInformation_2', $result2);

        $this->assertEquals(
            $result1['account']['idUser'],
            $result2['account']['idUser'],
            'Account ID should be consistent',
        );

        $this->assertEquals(
            $result1['account']['username'],
            $result2['account']['username'],
            'Username should be consistent',
        );

        info('Account information consistency verified');
    }

    /**
     * Test vacation status.
     */
    public function testVacationStatus(): void
    {
        $result = $this->client->account()->getAccountInformation();
        $this->logResponse('getAccountInformation', $result);
        $account = $result['account'];

        $this->assertTrue(
            array_key_exists('onVacation', $account),
            'Account should have onVacation field',
        );

        $onVacation = $account['onVacation'];
        $this->assertTrue(
            is_bool($onVacation),
            'onVacation should be a boolean',
        );

        info(sprintf('Vacation status: %s', $onVacation ? 'ON' : 'OFF'));
    }

    /**
     * Test toggling vacation status.
     *
     * WARNING: This will temporarily change your vacation status!
     */
    public function testToggleVacation(): void
    {
        // Only run if explicitly enabled (destructive operation)
        if (($_ENV['ENABLE_VACATION_TESTS'] ?? 'false') !== 'true') {
            $this->skip('Vacation toggle requires ENABLE_VACATION_TESTS=true');
        }

        // Get current status
        $result = $this->client->account()->getAccountInformation();
        $this->logResponse('getAccountInformation_before', $result);
        $originalStatus = $result['account']['onVacation'] ?? false;

        info(sprintf('Original vacation status: %s', $originalStatus ? 'ON' : 'OFF'));

        try {
            // Toggle vacation ON
            $this->client->account()->setOnVacation(true, []);
            info('Vacation set to ON');

            // Verify it changed
            $result = $this->client->account()->getAccountInformation();
            $this->logResponse('getAccountInformation_after_on', $result);
            $this->assertTrue($result['account']['onVacation'], 'Vacation should be ON');

            // Toggle back OFF
            $this->client->account()->setOnVacation(false, ['relistItems' => false]);
            info('Vacation set to OFF');

            // Verify it changed back
            $result = $this->client->account()->getAccountInformation();
            $this->logResponse('getAccountInformation_after_off', $result);
            $this->assertFalse($result['account']['onVacation'], 'Vacation should be OFF');

            info('Vacation toggle test completed successfully');
        } catch (\Throwable $e) {
            // Try to restore original status
            try {
                $this->client->account()->setOnVacation($originalStatus, ['relistItems' => false]);
            } catch (\Throwable) {
                // Ignore restoration errors
            }
            throw $e;
        }
    }

    /**
     * Test account has expected fields.
     */
    public function testAccountHasExpectedFields(): void
    {
        $result = $this->client->account()->getAccountInformation();
        $this->logResponse('getAccountInformation', $result);
        $account = $result['account'];

        // Check for expected fields
        $expectedFields = ['idUser', 'username', 'country', 'isCommercial', 'onVacation'];

        foreach ($expectedFields as $field) {
            $this->assertTrue(
                array_key_exists($field, $account),
                "Account should have field: {$field}",
            );
        }

        info(sprintf(
            'Account fields verified: %s, Country: %s, Commercial: %s',
            $account['username'],
            $account['country'] ?? 'N/A',
            ($account['isCommercial'] ?? false) ? 'Yes' : 'No',
        ));
    }
}

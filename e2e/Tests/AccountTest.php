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
        $result2 = $this->client->account()->getAccountInformation();

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
        $account = $result['account'];

        $onVacation = $account['onVacation'] ?? false;
        info(sprintf('Vacation status: %s', $onVacation ? 'ON' : 'OFF'));

        // We don't toggle vacation as it could affect real orders
        success('Vacation status check completed');
    }

    /**
     * Test account has expected fields.
     */
    public function testAccountHasExpectedFields(): void
    {
        $result = $this->client->account()->getAccountInformation();
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

<?php

declare(strict_types=1);

namespace CardmarketE2E\Tests;

use CardmarketE2E\TestCase;

/**
 * E2E Tests for Users API.
 */
class UsersTest extends TestCase
{
    /**
     * Test getting user details.
     */
    public function testGetUserDetails(): void
    {
        // First get our own user ID
        $account = $this->client->account()->getAccountInformation();
        $userId = $account['account']['idUser'];

        $result = $this->client->users()->getUserDetails($userId);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('user', $result);

        $user = $result['user'];
        $this->assertEquals($userId, $user['idUser']);

        info(sprintf('User: %s (ID: %d)', $user['username'], $user['idUser']));
    }

    /**
     * Test finding users.
     */
    public function testFindUsers(): void
    {
        // Search for a common username pattern
        $result = $this->client->users()->findUsers('cardmarket');

        $this->assertIsArray($result);

        if (isset($result['user'])) {
            info(sprintf('Found %d users matching "cardmarket"', count($result['user'])));
        } else {
            info('No users found');
        }
    }
}

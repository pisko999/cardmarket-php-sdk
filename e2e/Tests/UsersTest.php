<?php

declare(strict_types=1);

namespace CardmarketE2E\Tests;

use CardmarketE2E\TestCase;
use Pisko\CardMarket\Exception\HttpClientException;

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
        $this->logResponse('getAccountInformation', $account);
        $userId = $account['account']['idUser'];

        $result = $this->client->users()->getUserDetails($userId);
        $this->logResponse('getUserDetails', $result);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('user', $result);

        $user = $result['user'];
        $this->assertEquals($userId, $user['idUser']);

        info(sprintf('User: %s (ID: %d)', $user['username'], $user['idUser']));
    }

    /**
     * Test getting non-existent user fails.
     */
    public function testGetNonExistentUserFails(): void
    {
        $this->assertThrows(
            fn () => $this->client->users()->getUserDetails(999999999),
            HttpClientException::class,
        );
    }

    /**
     * Test finding users.
     */
    public function testFindUsers(): void
    {
        $searchUsername = getTestConfig('TEST_SEARCH_USERNAME');
        
        if (empty($searchUsername)) {
            $this->skip('TEST_SEARCH_USERNAME not configured');
        }

        $result = $this->client->users()->findUsers($searchUsername);
        $this->logResponse('findUsers', $result);

        $this->assertIsArray($result);

        // API returns 'users' (plural), not 'user'
        $users = $result['users'] ?? $result['user'] ?? [];
        if (!empty($users)) {
            if (!isset($users[0])) {
                $users = [$users];
            }
            info(sprintf('Found %d users matching "%s"', count($users), $searchUsername));
        } else {
            info(sprintf('No users found matching "%s"', $searchUsername));
        }
    }

    /**
     * Test finding users with no results.
     */
    public function testFindUsersNoResults(): void
    {
        // Search for unlikely username
        $result = $this->client->users()->findUsers('xyznonexistent12345username67890abc');
        $this->logResponse('findUsers_nonexistent', $result);

        $this->assertIsArray($result);
        $users = $result['users'] ?? $result['user'] ?? [];
        $count = is_array($users) ? count($users) : 0;

        info(sprintf('Found %d users for unlikely search (expected 0)', $count));
    }

    /**
     * Test getting user articles.
     */
    public function testGetUserArticles(): void
    {
        $userId = getTestConfig('TEST_OTHER_USER_ID');

        if (empty($userId)) {
            // Use our own user ID
            $account = $this->client->account()->getAccountInformation();
            $userId = $account['account']['idUser'];
        }

        $result = $this->client->articles()->getArticlesByUser((int) $userId);
        $this->logResponse('getArticlesByUser', $result);

        $this->assertIsArray($result);
        $count = count($result['article'] ?? []);

        info(sprintf('User %s has %d articles', $userId, $count));
    }
}

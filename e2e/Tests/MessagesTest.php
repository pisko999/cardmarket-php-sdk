<?php

declare(strict_types=1);

namespace CardmarketE2E\Tests;

use CardmarketE2E\TestCase;
use Pisko\CardMarket\Entities\MessageEntity;
use Pisko\CardMarket\Exception\HttpClientException;

/**
 * E2E Tests for Messages API.
 */
class MessagesTest extends TestCase
{
    private const TEST_PREFIX = '[E2E Test]';

    /**
     * Test getting messages thread.
     */
    public function testGetMessagesThread(): void
    {
        $result = $this->client->messages()->getMessagesThread();

        $this->assertIsArray($result);

        if (isset($result['thread'])) {
            info(sprintf('Found %d message threads', count($result['thread'])));
        } else {
            info('No message threads found');
        }
    }

    /**
     * Test finding messages.
     */
    public function testFindMessages(): void
    {
        // Get unread messages
        $result = $this->client->messages()->findMessages(true);

        $this->assertIsArray($result);

        if (isset($result['thread'])) {
            info(sprintf('Found %d unread message threads', count($result['thread'])));
        } else {
            info('No unread messages');
        }
    }

    /**
     * Test getting messages with a specific user.
     */
    public function testGetMessagesByUser(): void
    {
        // First get threads to find a user to test with
        $threads = $this->client->messages()->getMessagesThread();

        if (empty($threads['thread'])) {
            $this->skip('No message threads to test with');
        }

        $userId = $threads['thread'][0]['partner']['idUser'];
        $result = $this->client->messages()->getMessagesThreadByUser($userId);

        $this->assertIsArray($result);

        info(sprintf('Retrieved message thread with user %d', $userId));
    }

    /**
     * Test getting messages with non-existent user.
     */
    public function testGetMessagesByNonExistentUserFails(): void
    {
        $this->assertThrows(
            fn () => $this->client->messages()->getMessagesThreadByUser(999999999),
            HttpClientException::class,
        );
    }

    /**
     * Test sending a message.
     *
     * WARNING: This actually sends a message!
     */
    public function testSendMessage(): void
    {
        $userId = getTestConfig('TEST_OTHER_USER_ID');

        if (empty($userId)) {
            $this->skip('TEST_OTHER_USER_ID not configured');
        }

        // Only send if in sandbox mode or explicitly enabled
        if (($_ENV['CARDMARKET_SANDBOX'] ?? 'false') !== 'true' && ($_ENV['ENABLE_MESSAGE_TESTS'] ?? 'false') !== 'true') {
            $this->skip('Message sending only enabled in sandbox mode or with ENABLE_MESSAGE_TESTS=true');
        }

        $message = new MessageEntity([
            'idOtherUser' => (int) $userId,
            'message' => self::TEST_PREFIX . ' Automated test message - ' . date('Y-m-d H:i:s'),
        ]);

        $result = $this->client->messages()->sendMessage($message);

        $this->assertIsArray($result);

        info(sprintf('Sent test message to user %d', (int) $userId));
    }

    /**
     * Test sending message to non-existent user fails.
     */
    public function testSendMessageToNonExistentUserFails(): void
    {
        $message = new MessageEntity([
            'idOtherUser' => 999999999,
            'message' => self::TEST_PREFIX . ' This should fail',
        ]);

        $this->assertThrows(
            fn () => $this->client->messages()->sendMessage($message),
            HttpClientException::class,
        );
    }

    /**
     * Test sending empty message fails.
     */
    public function testSendEmptyMessageFails(): void
    {
        $userId = getTestConfig('TEST_OTHER_USER_ID');

        if (empty($userId)) {
            $this->skip('TEST_OTHER_USER_ID not configured');
        }

        $message = new MessageEntity([
            'idOtherUser' => (int) $userId,
            'message' => '', // Empty message
        ]);

        $this->assertThrows(
            fn () => $this->client->messages()->sendMessage($message),
            HttpClientException::class,
        );
    }

    /**
     * Test deleting message thread.
     */
    public function testDeleteMessageThread(): void
    {
        // First get threads to find one to delete
        $threads = $this->client->messages()->getMessagesThread();

        if (empty($threads['thread'])) {
            $this->skip('No message threads to test deletion');
        }

        // Find an E2E test thread if possible, or skip
        $threadToDelete = null;
        foreach ($threads['thread'] as $thread) {
            if (isset($thread['message']) && str_contains($thread['message'], self::TEST_PREFIX)) {
                $threadToDelete = $thread;
                break;
            }
        }

        if ($threadToDelete === null) {
            $this->skip('No E2E test message thread found to delete');
        }

        $userId = $threadToDelete['partner']['idUser'];
        $result = $this->client->messages()->deleteMessagesByUser($userId);

        $this->assertIsArray($result);
        info(sprintf('Deleted message thread with user %d', $userId));
    }

    /**
     * Cleanup any E2E test message threads.
     */
    public function testCleanupTestMessages(): void
    {
        $threads = $this->client->messages()->getMessagesThread();

        if (empty($threads['thread'])) {
            info('No message threads to cleanup');

            return;
        }

        $deleted = 0;
        foreach ($threads['thread'] as $thread) {
            // Check if last message contains our test prefix
            if (isset($thread['message']) && str_contains($thread['message'], self::TEST_PREFIX)) {
                try {
                    $this->client->messages()->deleteMessagesByUser($thread['partner']['idUser']);
                    $deleted++;
                } catch (\Throwable $e) {
                    warning(sprintf('Could not delete thread with user %d: %s', $thread['partner']['idUser'], $e->getMessage()));
                }
            }
        }

        info(sprintf('Cleaned up %d E2E test message threads', $deleted));
    }
}

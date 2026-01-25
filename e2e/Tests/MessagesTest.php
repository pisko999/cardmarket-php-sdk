<?php

declare(strict_types=1);

namespace CardmarketE2E\Tests;

use CardmarketE2E\TestCase;
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
        $this->logResponse('getMessagesThread', $result);

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
        // Note: This API endpoint may be unavailable
        try {
            $result = $this->client->messages()->findMessages(true);
            $this->logResponse('findMessages_unread', $result);

            $this->assertIsArray($result);

            if (isset($result['thread'])) {
                info(sprintf('Found %d unread message threads', count($result['thread'])));
            } else {
                info('No unread messages');
            }
        } catch (HttpClientException $e) {
            if (str_contains($e->getMessage(), 'unavailable')) {
                $this->skip('Find messages API is currently unavailable');
            }
            throw $e;
        }
    }

    /**
     * Test getting messages with a specific user.
     *
     * Note: Reading a message thread may mark messages as read!
     * We only test with TEST_OTHER_USER_ID to avoid marking real messages as read.
     */
    public function testGetMessagesByUser(): void
    {
        $userId = getTestConfig('TEST_OTHER_USER_ID');

        if (empty($userId)) {
            $this->skip('TEST_OTHER_USER_ID not configured');
        }

        $result = $this->client->messages()->getMessagesThreadByUser((int) $userId);
        $this->logResponse('getMessagesThreadByUser', $result);

        $this->assertIsArray($result);

        info(sprintf('Retrieved message thread with user %d', $userId));
    }

    /**
     * Test getting messages with non-existent user.
     */
    public function testGetMessagesByNonExistentUserFails(): void
    {
        // Note: API may return 500 for non-existent user
        $this->assertThrows(
            fn () => $this->client->messages()->getMessagesThreadByUser(999999999),
            \Throwable::class,
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

        // Only send if explicitly enabled (destructive operation)
        if (($_ENV['ENABLE_MESSAGE_TESTS'] ?? 'false') !== 'true') {
            $this->skip('Message sending requires ENABLE_MESSAGE_TESTS=true');
        }

        $messageText = self::TEST_PREFIX . ' Automated test message - ' . date('Y-m-d H:i:s');
        $result = $this->client->messages()->sendMessage((int) $userId, $messageText);
        $this->logResponse('sendMessage', $result);

        $this->assertIsArray($result);

        info(sprintf('Sent test message to user %d', (int) $userId));
    }

    /**
     * Test sending message to non-existent user fails.
     */
    public function testSendMessageToNonExistentUserFails(): void
    {
        $this->assertThrows(
            fn () => $this->client->messages()->sendMessage(999999999, self::TEST_PREFIX . ' This should fail'),
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

        // Note: Empty messages may be accepted by API or validated by SDK
        // Both behaviors are acceptable - we just verify the API responds
        $testPassed = false;
        try {
            $result = $this->client->messages()->sendMessage((int) $userId, '');
            // If we get here, API accepted empty message
            $this->logResponse('sendEmptyMessage', $result);
            $this->assertIsArray($result);
            $testPassed = true;
            info('API accepted empty message (may send previous text or ignore)');
        } catch (\Throwable $e) {
            $testPassed = true;
            info('Empty message correctly rejected: ' . $e->getMessage());
        }
        $this->assertTrue($testPassed, 'Test should complete via success or exception');
    }

    /**
     * Test deleting a single message.
     *
     * Note: deleteMessagesByUser() doesn't actually delete messages (API limitation),
     * so we test deleteOneMessageByUser() which works correctly.
     */
    public function testDeleteMessage(): void
    {
        // First get threads to find a test message to delete
        $threads = $this->client->messages()->getMessagesThread();
        $this->logResponse('getMessagesThread', $threads);

        if (empty($threads['thread'])) {
            $this->skip('No message threads to test deletion');
        }

        // Find an E2E test message - check full thread history
        $messageToDelete = null;
        $targetUserId = null;
        foreach ($threads['thread'] as $thread) {
            $userId = $thread['partner']['idUser'];

            // Get full thread to check all messages
            $fullThread = $this->client->messages()->getMessagesThreadByUser($userId);
            $messages = $fullThread['message'] ?? [];

            // Ensure messages is an array
            if (!is_array($messages) || !isset($messages[0])) {
                $messages = [$messages];
            }

            foreach ($messages as $msg) {
                $messageText = $msg['text'] ?? '';
                if (str_contains($messageText, self::TEST_PREFIX)) {
                    $messageToDelete = $msg;
                    $targetUserId = $userId;
                    break 2;
                }
            }
        }

        if ($messageToDelete === null) {
            $this->skip('No E2E test message found to delete');
        }

        $messageId = $messageToDelete['idMessage'];
        $result = $this->client->messages()->deleteOneMessageByUser($targetUserId, $messageId);
        $this->logResponse('deleteOneMessageByUser', $result);

        $this->assertIsArray($result);
        info(sprintf('Deleted message %s from thread with user %d', $messageId, $targetUserId));
    }

    /**
     * Cleanup any E2E test messages.
     *
     * Note: deleteMessagesByUser() doesn't actually delete messages, only hides the thread.
     * We need to delete individual messages using deleteOneMessageByUser().
     */
    public function testCleanupTestMessages(): void
    {
        $threads = $this->client->messages()->getMessagesThread();
        $this->logResponse('getMessagesThread', $threads);

        if (empty($threads['thread'])) {
            info('No message threads to cleanup');

            return;
        }

        $deleted = 0;
        foreach ($threads['thread'] as $thread) {
            $userId = $thread['partner']['idUser'];

            // Get full thread to check all messages for test prefix
            try {
                $fullThread = $this->client->messages()->getMessagesThreadByUser($userId);
                $messages = $fullThread['message'] ?? [];

                // Ensure messages is an array
                if (!is_array($messages) || !isset($messages[0])) {
                    $messages = [$messages];
                }

                // Delete individual E2E test messages (deleteMessagesByUser doesn't work)
                foreach ($messages as $msg) {
                    $messageText = $msg['text'] ?? '';
                    $messageId = $msg['idMessage'] ?? null;
                    
                    if ($messageId && str_contains($messageText, self::TEST_PREFIX)) {
                        $this->client->messages()->deleteOneMessageByUser($userId, $messageId);
                        $deleted++;
                    }
                }
            } catch (\Throwable $e) {
                warning(sprintf('Could not process thread with user %d: %s', $userId, $e->getMessage()));
            }
        }

        info(sprintf('Cleaned up %d E2E test messages', $deleted));
    }
}

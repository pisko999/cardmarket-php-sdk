<?php

declare(strict_types=1);

namespace CardmarketE2E\Tests;

use CardmarketE2E\TestCase;

/**
 * E2E Tests for Messages API.
 */
class MessagesTest extends TestCase
{
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

        // Only send if in sandbox mode
        if (($_ENV['CARDMARKET_SANDBOX'] ?? 'false') !== 'true') {
            $this->skip('Message sending only enabled in sandbox mode');
        }

        $message = new \Pisko\CardMarket\Entities\MessageEntity([
            'idOtherUser' => (int) $userId,
            'message' => 'E2E Test message - ' . date('Y-m-d H:i:s'),
        ]);

        $result = $this->client->messages()->sendMessage($message);

        $this->assertIsArray($result);

        info(sprintf('Sent test message to user %d', $userId));
    }
}

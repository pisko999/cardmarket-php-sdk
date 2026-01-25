<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Tests\Resources\AccountManagement;

use Pisko\CardMarket\Resources\AccountManagement\MessagesResource;
use Pisko\CardMarket\Tests\ResourceTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class MessagesResourceTest extends ResourceTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testGetMessagesThread()
    {
        $mockResponse = new MockResponse(json_encode([
            'thread' => [
                ['idUser' => 12345, 'username' => 'user1', 'unreadMessages' => 2],
                ['idUser' => 67890, 'username' => 'user2', 'unreadMessages' => 0],
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 5,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $messagesResource = new MessagesResource($this->httpClientCreatorMock);

        $response = $messagesResource->getMessagesThread();

        $this->assertArrayHasKey('thread', $response);
        $this->assertArrayHasKey('api', $response);
    }

    public function testGetMessagesThreadByUser()
    {
        $mockResponse = new MockResponse(json_encode([
            'message' => [
                ['idMessage' => 'msg1', 'text' => 'Hello', 'date' => '2026-01-20T10:00:00+0100'],
                ['idMessage' => 'msg2', 'text' => 'Hi there', 'date' => '2026-01-20T10:05:00+0100'],
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 6,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $messagesResource = new MessagesResource($this->httpClientCreatorMock);

        $response = $messagesResource->getMessagesThreadByUser(12345);

        $this->assertArrayHasKey('message', $response);
        $this->assertIsArray($response['message']);
    }

    public function testGetMessageByUser()
    {
        $mockResponse = new MockResponse(json_encode([
            'message' => [
                'idMessage' => 'msg123',
                'text' => 'Test message',
                'date' => '2026-01-20T10:00:00+0100',
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 7,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $messagesResource = new MessagesResource($this->httpClientCreatorMock);

        $response = $messagesResource->getMessageByUser(12345, 'msg123');

        $this->assertArrayHasKey('message', $response);
        $this->assertArrayHasKey('idMessage', $response['message']);
    }

    public function testSendMessage()
    {
        $mockResponse = new MockResponse(json_encode([
            'message' => [
                'idMessage' => 'msg456',
                'text' => 'Test message',
                'date' => '2026-01-25T10:00:00+0100',
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 8,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $messagesResource = new MessagesResource($this->httpClientCreatorMock);

        $response = $messagesResource->sendMessage(12345, 'Test message');

        $this->assertArrayHasKey('message', $response);
        $this->assertSame('Test message', $response['message']['text']);
    }

    public function testDeleteMessageThread()
    {
        $mockResponse = new MockResponse(json_encode(['deleted' => true]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 9,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $messagesResource = new MessagesResource($this->httpClientCreatorMock);

        $response = $messagesResource->deleteMessagesByUser(12345);

        $this->assertArrayHasKey('deleted', $response);
        $this->assertTrue($response['deleted']);
    }

    public function testDeleteMessage()
    {
        $mockResponse = new MockResponse(json_encode(['deleted' => true]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 10,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $messagesResource = new MessagesResource($this->httpClientCreatorMock);

        $response = $messagesResource->deleteOneMessageByUser(12345, 'msg123');

        $this->assertArrayHasKey('deleted', $response);
        $this->assertTrue($response['deleted']);
    }

    public function testFindMessagesUnread(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'message' => [
                ['idMessage' => 'msg1', 'text' => 'Unread message 1', 'date' => '2026-01-20T10:00:00+0100', 'unread' => true],
                ['idMessage' => 'msg2', 'text' => 'Unread message 2', 'date' => '2026-01-20T11:00:00+0100', 'unread' => true],
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 11,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $messagesResource = new MessagesResource($this->httpClientCreatorMock);

        $response = $messagesResource->findMessages(true);

        $this->assertArrayHasKey('message', $response);
        $this->assertIsArray($response['message']);
    }

    public function testFindMessagesWithDateRange(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'message' => [
                ['idMessage' => 'msg1', 'text' => 'Message from Jan', 'date' => '2026-01-15T10:00:00+0100'],
                ['idMessage' => 'msg2', 'text' => 'Message from Jan', 'date' => '2026-01-25T10:00:00+0100'],
            ],
        ]), [
            'response_headers' => [
                'X-Request-Limit-Max' => 5000,
                'X-Request-Limit-Count' => 12,
            ],
        ]);

        $this->setupHttpClientCreatorMock([$mockResponse]);
        $messagesResource = new MessagesResource($this->httpClientCreatorMock);

        $response = $messagesResource->findMessages(
            false,
            new \DateTime('2026-01-01'),
            new \DateTime('2026-01-31'),
        );

        $this->assertArrayHasKey('message', $response);
        $this->assertIsArray($response['message']);
    }

    protected function getMockResponses(): array
    {
        $threadList = json_encode([
            'thread' => [
                ['idUser' => 12345, 'username' => 'user1', 'unreadMessages' => 2],
                ['idUser' => 67890, 'username' => 'user2', 'unreadMessages' => 0],
            ],
        ]);

        $threadByUser = json_encode([
            'message' => [
                ['idMessage' => 'msg1', 'text' => 'Hello', 'date' => '2026-01-20T10:00:00+0100'],
                ['idMessage' => 'msg2', 'text' => 'Hi there', 'date' => '2026-01-20T10:05:00+0100'],
            ],
        ]);

        $singleMessage = json_encode([
            'message' => [
                'idMessage' => 'msg123',
                'text' => 'Test message',
                'date' => '2026-01-20T10:00:00+0100',
            ],
        ]);

        $sentMessage = json_encode([
            'message' => [
                'idMessage' => 'msg456',
                'text' => 'Test message',
                'date' => '2026-01-25T10:00:00+0100',
            ],
        ]);

        $deleteResponse = json_encode(['deleted' => true]);

        return [
            new MockResponse($threadList, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 5,
                ],
            ]),
            new MockResponse($threadByUser, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 6,
                ],
            ]),
            new MockResponse($singleMessage, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 7,
                ],
            ]),
            new MockResponse($sentMessage, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 8,
                ],
            ]),
            new MockResponse($deleteResponse, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 9,
                ],
            ]),
            new MockResponse($deleteResponse, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 10,
                ],
            ]),
        ];
    }
}

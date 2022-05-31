<?php
declare(strict_types=1);

namespace Pisko\CardMarket\Resources;

use Pisko\CardMarket\Authentication\AuthenticationHeaderBuilder;
use Pisko\CardMarket\Exception\HttpClientException;
use Pisko\CardMarket\Exception\HttpServerException;
use Pisko\CardMarket\Exception\UnknownErrorException;
use Pisko\CardMarket\HttpClient\HttpClientCreator;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Class HttpCaller
 *
 * @package Pisko\CardMarket\Resources
 *
 * @author Nicolas Perussel <nicolas.perussel@gmail.com>
 */
abstract class HttpCaller
{
    /**
     * @var HttpClientCreator
     */
    protected $httpClientCreator;

    /**
     * @var HttpClientInterface
     */
    protected $httpClient;

    public function __construct(HttpClientCreator $httpClientCreator)
    {
        $this->httpClientCreator = $httpClientCreator;
        $this->httpClient = $httpClientCreator->createHttpClient();
    }

    /**
     * Perform GET request.
     *
     * @param string $uri
     *
     * @return array
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    protected function get(string $uri): array
    {
        $url = $this->httpClientCreator->getUrl() . $uri;

        try {
            $response = $this->httpClient->request('GET', $url, [
              'headers' => self::getAuthorizationHeader($url, 'GET'),
            ]);

            return self::processJsonResponse($response);
        } catch (UnknownErrorException | DecodingExceptionInterface | HttpExceptionInterface | TransportExceptionInterface $exception) {
            throw $exception;
        }
    }

    /**
     * Perform PUT request.
     *
     * @param string $uri
     * @param array $content
     *
     * @return array
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    protected function put(string $uri, array $content): array
    {
        $url = $this->httpClientCreator->getUrl() . $uri;

        try {
            $response = $this->httpClient->request('PUT', $url, [
              'headers' => self::getAuthorizationHeader($url, 'PUT'),
              'body' => json_encode($content),
            ]);

            return self::processJsonResponse($response);
        } catch (UnknownErrorException | DecodingExceptionInterface | HttpExceptionInterface | TransportExceptionInterface $exception) {
            throw $exception;
        }
    }

    /**
     * Create the Authorisation header.
     *
     * @param string $url
     * @param string $method
     *
     * @return array
     */
    protected function getAuthorizationHeader(string $url, string $method): array
    {
        $headerBuilder = new AuthenticationHeaderBuilder($this->httpClientCreator, $url, $method);

        return [
          'Authorization' => $headerBuilder->getAuthorisationHeaderValue(),
        ];
    }


    /**
     * @param \Symfony\Contracts\HttpClient\ResponseInterface $response
     *
     * @return array
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    protected function processJsonResponse(ResponseInterface $response): array
    {
        if ($response->getStatusCode() !== 200
            && $response->getStatusCode() !== 201
            && $response->getStatusCode() !== 206
        ) {
            $this->handleErrors($response);
        }

        try {
            $decodedContent = $response->toArray();

            return array_merge($decodedContent, $this->getApiLimitFromResponseHeaders($response->getHeaders(false)));
        } catch (TransportExceptionInterface | HttpExceptionInterface | HttpServerException | DecodingExceptionInterface $exception) {
            throw $exception;
        }
    }

    /**
     * Get Cardmarket  API requests calls number.
     *
     * @param array $headers
     *
     * @return array
     */
    private function getApiLimitFromResponseHeaders(array $headers): array
    {
        return [
          'api' => [
              'request-limit-max' => $headers['x-request-limit-max'][0],
              'request-limit-count' => $headers['x-request-limit-count'][0],
            ]
        ];
    }

    /**
     * @param \Symfony\Contracts\HttpClient\ResponseInterface $response
     *
     * @throws \Pisko\CardMarket\Exception\UnknownErrorException
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    protected function handleErrors(ResponseInterface $response): void
    {
        $statusCode = $response->getStatusCode();

        switch ($statusCode) {
          case 204:
            throw HttpClientException::noContent($response);
          case 400:
            throw HttpClientException::badRequest($response);
          case 401:
            throw HttpClientException::unauthorized($response);
          case 403:
            throw HttpClientException::forbidden($response);
          case 404:
            throw HttpClientException::notFound($response);
          case 429:
            throw HttpClientException::tooManyRequests($response);
          case 500 <= $statusCode:
            throw new HttpServerException($statusCode);
          default:
            throw new UnknownErrorException();
        }
    }
}

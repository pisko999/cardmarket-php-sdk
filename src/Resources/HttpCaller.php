<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Resources;

use Pisko\CardMarket\Authentication\AuthenticationHeaderBuilder;
use Pisko\CardMarket\Entities\BaseEntity;
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
 * Class HttpCaller.
 *
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
     * @throws UnknownErrorException
     * @throws HttpClientException
     * @throws DecodingExceptionInterface
     * @throws HttpExceptionInterface
     * @throws TransportExceptionInterface
     *
     * @return array
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
     * Perform DELETE request.
     *
     * @param string $uri
     * @param BaseEntity $content
     *
     * @throws TransportExceptionInterface
     *
     * @return array
     */
    protected function delete(string $uri, ?BaseEntity $content = null): array
    {
        $url = $this->httpClientCreator->getUrl() . $uri;

        try {
            $options = [
                'headers' => self::getAuthorizationHeader($url, 'DELETE'),
            ];
            if ($content !== null) {
                $options['body'] = $content->getXML();
            }
            $response = $this->httpClient->request('DELETE', $url, $options);

            return self::processJsonResponse($response);
        } catch (TransportExceptionInterface $exception) {
            throw $exception;
        }
    }

    /**
     * Perform POST request.
     *
     * @param string $uri
     * @param BaseEntity $content
     *
     * @throws UnknownErrorException
     * @throws HttpClientException
     * @throws DecodingExceptionInterface
     * @throws HttpExceptionInterface
     * @throws TransportExceptionInterface
     *
     * @return array
     */
    protected function post(string $uri, ?BaseEntity $content = null): array
    {
        $url = $this->httpClientCreator->getUrl() . $uri;

        try {
            $options = [
                'headers' => self::getAuthorizationHeader($url, 'POST'),
            ];
            if ($content !== null) {
                $options['body'] = $content->getXML();
            }
            $response = $this->httpClient->request('POST', $url, $options);

            return self::processJsonResponse($response);
        } catch (UnknownErrorException | DecodingExceptionInterface | HttpExceptionInterface | TransportExceptionInterface $exception) {
            throw $exception;
        }
    }

    /**
     * Perform PUT request.
     *
     * @param string $uri
     * @param BaseEntity $content
     *
     * @throws UnknownErrorException
     * @throws HttpClientException
     * @throws DecodingExceptionInterface
     * @throws HttpExceptionInterface
     * @throws TransportExceptionInterface
     *
     * @return array
     */
    protected function put(string $uri, ?BaseEntity $content = null): array
    {
        $url = $this->httpClientCreator->getUrl() . $uri;

        try {
            $options = [
                'headers' => self::getAuthorizationHeader($url, 'PUT'),
            ];
            if ($content !== null) {
                $options['body'] = $content->getXML();
            }
            $response = $this->httpClient->request('PUT', $url, $options);

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
     * @param ResponseInterface $response
     *
     * @throws UnknownErrorException
     * @throws HttpClientException
     * @throws DecodingExceptionInterface
     * @throws HttpExceptionInterface
     * @throws TransportExceptionInterface
     *
     * @return array
     */
    protected function processJsonResponse(ResponseInterface $response): array
    {
        if ($response->getStatusCode() !== 200
            && $response->getStatusCode() !== 201
            && $response->getStatusCode() !== 202
            && $response->getStatusCode() !== 206
        ) {
            $this->handleErrors($response);
        }

        try {
            $decodedContent = empty($response->getContent()) ? [] : $response->toArray();

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
              'request-limit-max' => $headers['x-request-limit-max'][0] ?? null,
              'request-limit-count' => $headers['x-request-limit-count'][0] ?? null,
            ],
        ];
    }

    /**
     * @param ResponseInterface $response
     *
     * @throws UnknownErrorException
     * @throws HttpClientException
     * @throws TransportExceptionInterface
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
            default:
                if ($statusCode >= 500) {
                    throw new HttpServerException($statusCode);
                }

                throw new UnknownErrorException(json_encode($response->toArray()));
        }
    }

    /**
     * Set up required and optional parameters.
     *
     * @param array $data
     * @param array $optional
     *
     * @return array
     */
    protected function setUpOptionalParameters(array $data, array $optional): array
    {
        $output = [];
        foreach ($optional as $key => $value) {
            if (isset($data[$key])) {
                $output[$key] = $this->setDataKey($data[$key], $value);
            }
        }

        return $output;
    }

    /**
     * Set one dataKey.
     *
     * @param mixed $data
     * @param string $type
     *
     * @return mixed
     */
    private function setDataKey(mixed $data, string $type): mixed
    {
        if ($type === 'bool') {
            return $data ? 'true' : 'false';
        } else {
            return $data;
        }
    }
}

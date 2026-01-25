<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Authentication;

use Pisko\CardMarket\HttpClient\HttpClientCreator;

/**
 * Class AuthenticationHeaderBuilder.
 *
 * Build the Authentication header string base on the Cardmarket documentation
 * https://api.cardmarket.com/ws/documentation/API:Auth_OAuthHeader
 */
final class AuthenticationHeaderBuilder
{
    private string $nonce;

    private int $timestamp;

    private string $signatureMethod;

    private string $version;

    private HttpClientCreator $httpClientCreator;

    private array $parsedURL;

    private string $method;

    private array $parameters;

    private array $credentials;

    public function __construct(HttpClientCreator $httpClientCreator, string $url, string $method = 'GET')
    {
        if (!is_array(parse_url($url))) {
            throw new \LogicException(sprintf("String \"%s\" is malformed and can't be parsed.", $url));
        }

        $this->nonce = uniqid();
        $this->timestamp = time();
        $this->signatureMethod = 'HMAC-SHA1';
        $this->version = '1.0';
        $this->httpClientCreator = $httpClientCreator;
        $this->parsedURL = parse_url($url);
        $this->method = $method;
        $this->credentials = $this->httpClientCreator->retrieveAppCredentials();
    }

    /**
     * Build and return the Authorisation header correctly formatted.
     *
     * @return string
     */
    public function getAuthorisationHeaderValue(): string
    {
        $this->parameters = self::computeParameters();
        $this->parameters['oauth_signature'] = self::createOAuthSignature();

        $header = 'OAuth ';
        $headerParams = [];
        foreach ($this->parameters as $key => $value) {
            // Only include OAuth parameters in the header, not query parameters
            if ($key === 'realm' || str_starts_with($key, 'oauth_')) {
                $headerParams[] = $key . '="' . $value . '"';
            }
        }
        $header .= implode(', ', $headerParams);

        return $header;
    }

    /**
     * Create the OAuth signature based on HMAC-SHA1 algorithm.
     *
     * @return string
     */
    private function createOAuthSignature(): string
    {
        $finalUrl = strtoupper($this->method) . '&' . rawurlencode(self::getUrlCall()) . '&';

        // Use RFC3986 encoding for proper %20 handling, then rawurlencode the entire string
        $paramsString = rawurlencode(http_build_query($this->sortParameters(), '', '&', PHP_QUERY_RFC3986));
        $finalUrl .= $paramsString;

        $signatureKey = rawurlencode($this->credentials['application_secret']) . '&' . rawurlencode($this->credentials['access_secret']);
        $rawSignature = hash_hmac('sha1', $finalUrl, $signatureKey, true);

        return base64_encode($rawSignature);
    }

    /**
     * Sort parameters for OAuth signature (without pre-encoding).
     *
     * @return array
     */
    private function sortParameters(): array
    {
        $params = [];

        foreach ($this->parameters as $key => $value) {
            if ('realm' !== $key) {
                $params[(string) $key] = (string) $value;
            }
        }

        ksort($params);

        return $params;
    }

    /**
     * Merge the needed headers params with query string params.
     *
     * @return array
     */
    private function computeParameters(): array
    {
        $params = [
          'realm' => self::getUrlCall(),
          'oauth_consumer_key' => $this->credentials['application_token'],
          'oauth_token' => $this->credentials['access_token'],
          'oauth_nonce' => $this->nonce,
          'oauth_timestamp' => $this->timestamp,
          'oauth_signature_method' => $this->signatureMethod,
          'oauth_version' => $this->version,
        ];

        $params = array_merge($params, self::extractQueryParams());

        return $params;
    }

    /**
     * Simple helper to create base query URL without query params.
     *
     * @return string
     */
    private function getUrlCall(): string
    {
        return sprintf('%s://%s%s', $this->parsedURL['scheme'], $this->parsedURL['host'], $this->parsedURL['path']);
    }

    /**
     * Retrieve only query params from given URL.
     *
     * @return array
     */
    private function extractQueryParams(): array
    {
        if (!isset($this->parsedURL['query'])) {
            return [];
        }

        parse_str($this->parsedURL['query'], $queryParams);

        return $queryParams;
    }
}

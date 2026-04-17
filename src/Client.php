<?php

namespace BMLConnect;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;

class Client
{
    public const BML_SANDBOX_ENDPOINT = 'https://api.uat.merchants.bankofmaldives.com.mv/public/';
    public const BML_PRODUCTION_ENDPOINT = 'https://api.merchants.bankofmaldives.com.mv/public/';

    /**
     * @var GuzzleClient
     */
    private GuzzleClient $httpClient;

    /**
     * @var string
     */
    private string $apiKey;

    /**
     * @var array<array-key, mixed>
     */
    private array $clientOptions;

    /**
     * @var string
     */
    private string $baseUrl;

    /**
     * @var Transactions
     */
    public Transactions $transactions;


    /**
     * Client constructor.
     * @param string $apiKey
     * @param string $_appId
     * @param string $mode
     * @param array<array-key, mixed> $clientOptions
     */
    public function __construct(string $apiKey, string $_appId, string $mode = 'production', array $clientOptions = [])
    {
        $this->apiKey = $apiKey;
        $this->clientOptions = [
            'headers' => [
                'X-App-Id' => $_appId,
            ],
        ];
        $this->baseUrl = ($mode === 'production' ? self::BML_PRODUCTION_ENDPOINT : self::BML_SANDBOX_ENDPOINT);
        $this->clientOptions = array_replace_recursive($this->clientOptions, $clientOptions);

        $this->initiateHttpClient();

        $this->transactions = new Transactions($this);
    }

    /**
     * @param GuzzleClient $client
     */
    public function setClient(GuzzleClient $client): void
    {
        $this->httpClient = $client;
    }

    /**
     * Initiates the HttpClient with required headers
     */
    private function initiateHttpClient(): void
    {
        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $this->apiKey,
            ]
        ];

        $this->httpClient = new GuzzleClient(array_replace_recursive($this->clientOptions, $options));
    }

    private function buildBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @param string $body
     * @return mixed
     */
    private function handleResponse(string $body): mixed
    {
        if ($body === '') {
            return [];
        }

        $decoded = json_decode($body, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return $body;
    }

    /**
     * @param string $endpoint
     * @param array<array-key, mixed> $json
     * @return mixed
     * @throws GuzzleException
     */
    public function post(string $endpoint, array $json): mixed
    {
        $response = $this->httpClient->request('POST', $this->buildBaseUrl() . $endpoint, ['json' => $json]);
        return $this->handleResponse((string) $response->getBody());
    }

    /**
     * @param string $endpoint
     * @param array<array-key, mixed> $json
     * @return mixed
     * @throws GuzzleException
     */
    public function patch(string $endpoint, array $json): mixed
    {
        $response = $this->httpClient->request('PATCH', $this->buildBaseUrl() . $endpoint, ['json' => $json]);
        return $this->handleResponse((string) $response->getBody());
    }

    /**
     * @param string $endpoint
     * @return mixed
     */
    public function get(string $endpoint): mixed
    {
        $response = $this->httpClient->request('GET', $this->buildBaseUrl() . $endpoint);
        return $this->handleResponse((string) $response->getBody());
    }

    /**
     * @param string $endpoint
     * @param array<array-key, mixed> $json
     * @return mixed
     * @throws GuzzleException
     */
    public function delete(string $endpoint, array $json): mixed
    {
        $response = $this->httpClient->request('DELETE', $this->buildBaseUrl() . $endpoint, ['json' => $json]);
        return $this->handleResponse((string) $response->getBody());
    }
}

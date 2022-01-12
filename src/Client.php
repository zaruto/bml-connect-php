<?php

namespace BMLConnect;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;

class Client
{
    const BML_API_VERSION = '2.0';
    const BML_APP_VERSION = 'bml-connect-php';
    const BML_SIGN_METHOD = 'sha1';
    const BML_SANDBOX_ENDPOINT = 'https://api.uat.merchants.bankofmaldives.com.mv/public/';
    const BML_PRODUCTION_ENDPOINT = 'https://api.merchants.bankofmaldives.com.mv/public/';

    /**
     * @var GuzzleClient
     */
    private GuzzleClient $httpClient;

    /**
     * @var string
     */
    private string $apiKey;

    /**
     * @var string
     */
    private string $appId;

    /**
     * @var string
     */
    private string $mode;

    /**
     * @var array
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
     * @param string $appId
     * @param string $mode
     * @param array $clientOptions
     */
    public function __construct(string $apiKey, string $appId, string $mode = 'production', array $clientOptions = [])
    {
        $this->apiKey = $apiKey;
        $this->appId = $appId;
        $this->mode = $mode;
        $this->baseUrl = ($mode === 'production' ? self::BML_PRODUCTION_ENDPOINT : self::BML_SANDBOX_ENDPOINT);
        $this->clientOptions = $clientOptions;

        $this->initiateHttpClient();

        $this->transactions = new Transactions($this);
    }

    /**
     * @param GuzzleClient $client
     */
    public function setClient(GuzzleClient $client)
    {
        $this->httpClient = $client;
    }

    /**
     * Initiates the HttpClient with required headers
     */
    private function initiateHttpClient()
    {
        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' =>  $this->apiKey,
            ]
        ];

        $this->httpClient = new GuzzleClient(array_replace_recursive($this->clientOptions, $options));
    }

    private function buildBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @param Response $response
     * @return mixed
     */
    private function handleResponse(Response $response): mixed
    {
        $stream = Utils::streamFor($response->getBody());
        return json_decode($stream);
    }

    /**
     * @param string $endpoint
     * @param array $json
     * @return mixed
     * @throws GuzzleException
     */
    public function post(string $endpoint, array $json): mixed
    {
        $json['apiVersion'] = self::BML_API_VERSION;
        $json['appVersion'] = self::BML_APP_VERSION;
        $json['signMethod'] = self::BML_SIGN_METHOD;

        $response = $this->httpClient->request('POST', $this->buildBaseUrl().$endpoint, ['json' => $json]);
        return $this->handleResponse($response);
    }

    /**
     * @param string $endpoint
     * @param array $pagination
     * @return mixed
     */
    public function get(string $endpoint, array $pagination = []): mixed
    {
        $response = $this->httpClient->request(
            'GET',
            $this->applyPagination($this->buildBaseUrl().$endpoint, $pagination)
        );

        return $this->handleResponse($response);
    }

    /**
     * @param string $url
     * @param array $pagination
     * @return string
     */
    private function applyPagination(string $url, array $pagination): string
    {
        if (count($pagination)) {
            return $url.'?'.http_build_query($this->cleanPagination($pagination));
        }

        return $url;
    }

    /**
     * @param array $pagination
     * @return array
     */
    private function cleanPagination(array $pagination): array
    {
        $allowed = [
            'page',
        ];

        return array_intersect_key($pagination, array_flip($allowed));
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }
}

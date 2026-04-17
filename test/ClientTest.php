<?php

use BMLConnect\Client as BmlClient;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testClientEndpoint(): void
    {
        $client = new BmlClient('foo', 'bar');
        $baseUrl = $this->readPrivateProperty($client, 'baseUrl');
        $this->assertSame(BmlClient::BML_PRODUCTION_ENDPOINT, $baseUrl);

        $sandboxClient = new BmlClient('foo', 'bar', 'sandbox');
        $sandboxUrl = $this->readPrivateProperty($sandboxClient, 'baseUrl');
        $this->assertSame(BmlClient::BML_SANDBOX_ENDPOINT, $sandboxUrl);
    }

    public function testCreateTransactionUsesV2EndpointAndPayloadPassthrough(): void
    {
        $mock = new MockHandler([new Response(201, [], '{"url":"https://pay.example/abc","extra":"ok"}')]);
        $container = [];
        $history = Middleware::history($container);
        $stack = HandlerStack::create($mock);
        $stack->push($history);

        $httpClient = new Client(['handler' => $stack]);
        $client = new BmlClient('foo', 'bar');
        $client->setClient($httpClient);

        $payload = [
            'redirectUrl' => 'https://merchant.example/orders/1',
            'localId' => 'INV-1',
            'order' => [
                'shopId' => 'shop-id',
                'products' => [
                    ['productId' => 'product-id', 'numberOfItems' => 2],
                ],
            ],
        ];

        $response = $client->transactions->create($payload);

        $this->assertSame('https://api.merchants.bankofmaldives.com.mv/public/v2/transactions', (string) $container[0]['request']->getUri());
        $this->assertSame('POST', $container[0]['request']->getMethod());
        $this->assertSame($payload, json_decode((string) $container[0]['request']->getBody(), true));
        $this->assertSame('https://pay.example/abc', $response['url']);
        $this->assertSame('ok', $response['extra']);
        $this->assertArrayNotHasKey('signature', json_decode((string) $container[0]['request']->getBody(), true));
        $this->assertArrayNotHasKey('apiVersion', json_decode((string) $container[0]['request']->getBody(), true));
        $this->assertArrayNotHasKey('signMethod', json_decode((string) $container[0]['request']->getBody(), true));
    }

    private function readPrivateProperty(object $object, string $property): mixed
    {
        $reflection = new ReflectionObject($object);
        $baseUrlProperty = $reflection->getProperty($property);
        $baseUrlProperty->setAccessible(true);
        return $baseUrlProperty->getValue($object);
    }
}

<?php

namespace BMLConnect;

class Transactions
{
    private const ENDPOINT = 'transactions';
    private const CREATE_ENDPOINT = 'v2/transactions';

    /**
     * @var Client
     */
    private Client $client;

    /**
     * Payments constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param array<string, mixed> $payload
     * @return mixed
     */
    public function create(array $payload): mixed
    {
        return $this->client->post(self::CREATE_ENDPOINT, $payload);
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function get(string $id): mixed
    {
        return $this->client->get(self::ENDPOINT . '/' . $id);
    }

    /**
     * @param string $id
     * @param array<string, mixed> $payload
     * @return mixed
     */
    public function update(string $id, array $payload): mixed
    {
        return $this->client->patch(self::ENDPOINT . '/' . $id, $payload);
    }

    /**
     * @param string $id
     * @param string $mobile
     * @return mixed
     */
    public function sendSms(string $id, string $mobile): mixed
    {
        return $this->client->post(self::ENDPOINT . '/' . $id . '/send-sms', ['mobile' => $mobile]);
    }

    /**
     * @param string $id
     * @param string|array<int, string> $emails
     * @return mixed
     */
    public function sendEmail(string $id, string|array $emails): mixed
    {
        return $this->client->post(self::ENDPOINT . '/' . $id . '/send-email', ['emails' => $emails]);
    }
}

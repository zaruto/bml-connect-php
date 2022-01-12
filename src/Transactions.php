<?php

namespace BMLConnect;

use BMLConnect\Crypt\Signature;
use BMLConnect\Model\Transaction;

class Transactions
{
    const ENDPOINT = 'transactions';

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
     * @param array $json
     * @return mixed
     */
    public function create(array $json): mixed
    {
        $transaction = (new Transaction())->fromArray($json);
        $json['signature'] = (new Signature($transaction, $this->client->getApiKey()))->sign();
        return $this->client->post(self::ENDPOINT, $json);
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function get(string $id): mixed
    {
        return $this->client->get(self::ENDPOINT.'/'.$id);
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function list(array $params): mixed
    {
        return $this->client->get(self::ENDPOINT, $params);
    }
}

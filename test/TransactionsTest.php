<?php

use BMLConnect\Client;
use BMLConnect\Transactions;
use PHPUnit\Framework\TestCase;

class TransactionsTest extends TestCase
{
    public function testGetUsesTransactionEndpoint(): void
    {
        $stub = $this->createMock(Client::class);
        $stub->expects($this->once())
            ->method('get')
            ->with('transactions/tx-123')
            ->willReturn(['id' => 'tx-123']);

        $transactions = new Transactions($stub);
        $this->assertSame(['id' => 'tx-123'], $transactions->get('tx-123'));
    }

    public function testUpdateUsesPatchOnTransactionEndpoint(): void
    {
        $payload = ['customerReference' => 'Basket 392'];

        $stub = $this->createMock(Client::class);
        $stub->expects($this->once())
            ->method('patch')
            ->with('transactions/tx-123', $payload)
            ->willReturn(['id' => 'tx-123', 'customerReference' => 'Basket 392']);

        $transactions = new Transactions($stub);
        $response = $transactions->update('tx-123', $payload);

        $this->assertSame('Basket 392', $response['customerReference']);
    }

    public function testSendSmsUsesSendSmsEndpoint(): void
    {
        $stub = $this->createMock(Client::class);
        $stub->expects($this->once())
            ->method('post')
            ->with('transactions/tx-123/send-sms', ['mobile' => '9607770000'])
            ->willReturn(['id' => 'tx-123']);

        $transactions = new Transactions($stub);
        $this->assertSame(['id' => 'tx-123'], $transactions->sendSms('tx-123', '9607770000'));
    }

    public function testSendEmailAcceptsArrayAndSingleString(): void
    {
        $stub = $this->createMock(Client::class);
        $requests = [];

        $stub->expects($this->exactly(2))
            ->method('post')
            ->willReturnCallback(function (string $endpoint, array $payload) use (&$requests): array {
                $requests[] = [$endpoint, $payload];
                return ['ok' => true];
            });

        $transactions = new Transactions($stub);
        $this->assertSame(['ok' => true], $transactions->sendEmail('tx-123', ['a@example.com', 'b@example.com']));
        $this->assertSame(['ok' => true], $transactions->sendEmail('tx-123', 'single@example.com'));

        $this->assertSame(
            [
                ['transactions/tx-123/send-email', ['emails' => ['a@example.com', 'b@example.com']]],
                ['transactions/tx-123/send-email', ['emails' => 'single@example.com']],
            ],
            $requests
        );
    }
}

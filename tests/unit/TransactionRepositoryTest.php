<?php

namespace Tests\unit;

use Authorizer\entities\Transaction;
use Authorizer\repositories\TransactionRepositoryInMemory;
use PHPUnit\Framework\TestCase;

class TransactionRepositoryTest extends TestCase
{
    public function test_should_create_a_transaction_and_save_in_repository(): void
    {
        // Arrange
        $repository = new TransactionRepositoryInMemory();
        $transaction = new Transaction();
        $transaction->merchant = "BK";
        $transaction->amount = "100";
        $transaction->time = "2020-12-01T11:07:00.000Z";
        $actual[] = $transaction;

        // Act
        $repository->createTransaction($transaction);
        $expected = $repository->getTransactions();

        // Assert
        self::assertEquals($expected, $actual);
    }

    public function test_should_create_multiple_transactions_and_save_in_repository(): void
    {
        // Arrange
        $repository = new TransactionRepositoryInMemory();
        $transaction = new Transaction();
        $transaction->merchant = "BK";
        $transaction->amount = "100";
        $transaction->time = "2020-12-01T11:07:00.000Z";
        $actual[] = $transaction;

        $newTransaction = new Transaction();
        $newTransaction->merchant = "MC";
        $newTransaction->amount = "200";
        $newTransaction->time = "2020-12-01T11:05:00.000Z";
        $actual[] = $newTransaction;

        // Act
        $repository->createTransaction($transaction);
        $repository->createTransaction($newTransaction);
        $expected = $repository->getTransactions();

        // Assert
        self::assertEquals($expected, $actual);
        self::assertEquals($expected[0], $actual[0]);
        self::assertEquals($expected[1], $actual[1]);
        self::assertNotEquals($expected[0], $actual[1]);
        self::assertNotEquals($expected[1], $actual[0]);
    }
}
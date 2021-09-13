<?php

namespace Tests\unit;

use Authorizer\entities\Transaction;
use Authorizer\repositories\TransactionRepositoryInMemory;
use Authorizer\services\transaction_rules\HighFrequencySmallIntervalRule;
use Authorizer\services\TransactionAuthorizer;
use PHPUnit\Framework\TestCase;

class HighFrequencySmallIntervalRuleTest extends TestCase
{
    private string $actual;
    private TransactionRepositoryInMemory $repository;
    private HighFrequencySmallIntervalRule $rule;
    private TransactionAuthorizer $authorizer;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new TransactionRepositoryInMemory();
        $this->rule = new HighFrequencySmallIntervalRule();
        $this->authorizer = new TransactionAuthorizer($this->rule, $this->repository);
    }

    public function test_should_validate_and_return_one_error(): void
    {
        // Arrange
        $expected = "high-frequency-small-interval";
        $scenario = [
            ["transaction" => ["merchant" => "Burger King", "amount" => 20, "time" => "2019-02-13T11:00:00.000Z"]],
            ["transaction" => ["merchant" => "Habbib's", "amount" => 20, "time" => "2019-02-13T11:00:01.000Z"]],
            ["transaction" => ["merchant" => "McDonald's", "amount" => 20, "time" => "2019-02-13T11:01:01.000Z"]],
            ["transaction" => ["merchant" => "Subway", "amount" => 20, "time" => "2019-02-13T11:01:31.000Z"]]
        ];

        // Act
        foreach ($scenario as $transactionScenario) {
            $transaction = new Transaction();
            $arrayValue = reset($transactionScenario);
            $transaction->merchant = $arrayValue["merchant"];
            $transaction->amount = $arrayValue["amount"];
            $transaction->time = $arrayValue["time"];
            $this->repository->createTransaction($transaction);
            $this->actual = $this->authorizer->authorize($transaction);
        }

        // Assert
        self::assertEquals($expected, $this->actual);
    }

    public function test_should_validate_and_return_no_errors(): void
    {
        // Arrange
        $expected = "";
        $scenario = [
            ["transaction" => ["merchant" => "Burger King", "amount" => 20, "time" => "2019-02-13T11:00:00.000Z"]],
            ["transaction" => ["merchant" => "Habbib's", "amount" => 20, "time" => "2019-02-13T11:00:01.000Z"]],
            ["transaction" => ["merchant" => "McDonald's", "amount" => 20, "time" => "2019-02-13T11:01:01.000Z"]],
            ["transaction" => ["merchant" => "Burger King", "amount" => 10, "time" => "2019-02-13T12:00:00.000Z"]]
        ];

        // Act
        foreach ($scenario as $transactionScenario) {
            $transaction = new Transaction();
            $arrayValue = reset($transactionScenario);
            $transaction->merchant = $arrayValue["merchant"];
            $transaction->amount = $arrayValue["amount"];
            $transaction->time = $arrayValue["time"];
            $this->repository->createTransaction($transaction);
            $this->actual = $this->authorizer->authorize($transaction);
        }

        // Assert
        self::assertEquals($expected, $this->actual);
    }
}
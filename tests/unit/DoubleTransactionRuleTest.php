<?php

namespace Tests\unit;

use Authorizer\entities\Transaction;
use Authorizer\repositories\TransactionRepositoryInMemory;
use Authorizer\services\transaction_rules\DoubleTransactionRule;
use Authorizer\services\TransactionAuthorizer;
use PHPUnit\Framework\TestCase;

class DoubleTransactionRuleTest extends TestCase
{
    private string $actual;
    private TransactionRepositoryInMemory $repository;
    private DoubleTransactionRule $rule;
    private TransactionAuthorizer $authorizer;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new TransactionRepositoryInMemory();
        $this->rule = new DoubleTransactionRule();
        $this->authorizer = new TransactionAuthorizer($this->rule, $this->repository);
    }

//    public function test_should_validate_and_return_one_error(): void
//    {
//        // Arrange
//        $expected = "double-transaction";
//        $scenarios = [
//            ["transaction" => ["merchant" => "Burger King", "amount" => 20, "time" => "2019-02-13T11:00:00.000Z"]],
//            ["transaction" => ["merchant" => "McDonald's", "amount" => 10, "time" => "2019-02-13T11:00:01.000Z"]],
//            ["transaction" => ["merchant" => "Burger King", "amount" => 20, "time" => "2019-02-13T11:00:02.000Z"]]
//
//        ];
//        // Act
//        foreach ($scenarios as $scenario) {
//            $transaction = new Transaction();
//            $arrayValue = reset($scenario);
//            $transaction->merchant = $arrayValue["merchant"];
//            $transaction->amount = $arrayValue["amount"];
//            $transaction->time = $arrayValue["time"];
//            $this->repository->createTransaction($transaction);
//            $this->actual = $this->authorizer->authorize($transaction);
//        }
//
//        // Assert
//        self::assertEquals($expected, $this->actual);
//    }

    public function test_should_validate_and_return_no_errors(): void
    {
        // Arrange
        $expected = "";
        $scenarios = [
            ["transaction" => ["merchant" => "Burger King", "amount" => 20, "time" => "2019-02-13T11:00:00.000Z"]],
            ["transaction" => ["merchant" => "McDonald's", "amount" => 10, "time" => "2019-02-13T11:00:01.000Z"]],
            ["transaction" => ["merchant" => "Burger King", "amount" => 15, "time" => "2019-02-13T11:00:03.000Z"]]

        ];

        // Act
        foreach ($scenarios as $scenario) {
            $transaction = new Transaction();
            $arrayValue = reset($scenario);
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
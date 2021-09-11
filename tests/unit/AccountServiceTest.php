<?php

namespace Tests\unit;

use Authorizer\repositories\AccountRepositoryInMemory;
use Authorizer\services\AccountService;
use PHPUnit\Framework\TestCase;

class AccountServiceTest extends TestCase
{
    public function test_should_process_and_return_account()
    {
        // Arrange
        $accountRepository = new AccountRepositoryInMemory();
        $accountService = new AccountService($accountRepository);
        $request = ["active-card" => true, "available-limit" => 100];
        $expected = ["account" => ["active-card" => true, "available-limit" => 100], "violations" => []];

        // Act
        $actual = $accountService->createAccount($request);

        // Assert
        self::assertEquals($expected, $actual);
    }

    public function test_should_return_account_already_initialized_violation()
    {
        // Arrange
        $accountRepository = new AccountRepositoryInMemory();
        $accountService = new AccountService($accountRepository);
        $request = ["active-card" => true, "available-limit" => 100];
        $expected = ["account" => ["active-card" => true, "available-limit" => 100], "violations" => ["account-already-initialized"]];

        // Act
        $accountService->createAccount($request);
        $actual = $accountService->createAccount($request);

        // Assert
        self::assertEquals($expected, $actual);
    }
}
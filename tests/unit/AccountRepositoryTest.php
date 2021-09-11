<?php

namespace Tests\unit;

use Authorizer\entities\Account;
use Authorizer\repositories\AccountRepositoryInMemory;
use PHPUnit\Framework\TestCase;

class AccountRepositoryTest extends TestCase
{
    public function test_should_create_an_account_and_save_in_repository()
    {
        // Arrange
        $repository = new AccountRepositoryInMemory();
        $account = new Account();
        $account->activeCard = true;
        $account->availableLimit = 100;

        $actual = new Account();
        $actual->activeCard = true;
        $actual->availableLimit = 100;

        // Act
        $repository->createAccount($account);
        $expected = $repository->getAccount();

        // Assert
        self::assertEquals($expected, $actual);
    }

    public function test_should_update_an_existing_account()
    {
        // Arrange
        $repository = new AccountRepositoryInMemory();

        $account = new Account();
        $account->activeCard = true;
        $account->availableLimit = 200;

        $updatedAccount = new Account();
        $updatedAccount->activeCard = true;
        $updatedAccount->availableLimit = 100;

        $expected = new Account();
        $expected->activeCard = true;
        $expected->availableLimit = 100;

        // Act
        $repository->createAccount($account);
        $repository->updateAccount($updatedAccount);

        // Assert
        self::assertEquals($expected, $updatedAccount);
        self::assertNotEquals($account, $updatedAccount);
    }
}
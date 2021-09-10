<?php

namespace Authorizer\repositories;

use authorizer\entities\Account;

class AccountRepositoryInMemory implements AccountRepository
{
    protected array $accounts;

    public function __construct()
    {
        $this->accounts = [];
    }

    public function getAccount(): ?Account
    {
        return $this->accounts[0] ?? null;
    }

    public function createAccount(Account $account): void
    {
        $this->accounts[] = $account;
    }

    public function updateAccount(Account $account): void
    {
        $this->accounts[0] = $account;
    }
}
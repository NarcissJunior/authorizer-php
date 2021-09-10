<?php

namespace Authorizer\repositories;

use authorizer\entities\Transaction;

class TransactionRepositoryInMemory implements TransactionRepository
{
    protected array $accounts;

    public function __construct()
    {
        $this->accounts = [];
    }
}
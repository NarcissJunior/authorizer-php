<?php

namespace Authorizer\repositories;

use authorizer\entities\Transaction;

class TransactionRepositoryInMemory implements TransactionRepository
{
    protected array $transactions;

    public function __construct()
    {
        $this->transactions = [];
    }

    public function getTransactions(): array
    {
        return $this->transactions;
    }

    public function createTransaction(Transaction $transaction): void
    {
        $this->transactions[] = $transaction;
    }
}
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

    public function getTransactions(): ?array
    {
        return $this->transactions ?? null;
    }

    public function createTransaction(Transaction $transaction): void
    {
        $dateTime = date("Y-m-d H:i:s", strtotime($transaction->time));
        var_dump(strtotime($transaction->time));
        var_dump($dateTime);
        var_dump("oi");
        die;
        //$transaction->time = $dateTime;
        $this->transactions[] = $transaction;
    }

}
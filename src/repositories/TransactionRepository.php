<?php

namespace Authorizer\repositories;

use Authorizer\entities\Transaction;

interface TransactionRepository
{
    public function getTransactions(): ?array;
    public function createTransaction(Transaction $transaction): void;
}
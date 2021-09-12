<?php

namespace Authorizer\services\transaction_rules;

use Authorizer\entities\Transaction;
use Authorizer\repositories\TransactionRepository;

interface TransactionRule
{
    public function authorize(Transaction $transaction, TransactionRepository $repository): string;
}
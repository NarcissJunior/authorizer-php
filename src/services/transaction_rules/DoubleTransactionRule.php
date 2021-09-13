<?php

namespace Authorizer\services\transaction_rules;

use Authorizer\entities\Transaction;
use Authorizer\repositories\TransactionRepository;

class DoubleTransactionRule implements TransactionRule
{
    public function authorize(Transaction $newTransaction, TransactionRepository $repository): string
    {
        $transactions = $repository->getTransactions();

        foreach ($transactions as $transaction) {
            if ($newTransaction->merchant == $transaction->merchant && $newTransaction->amount == $transaction->amount) {
                $diff = (-1) * (strtotime($transaction->time) - strtotime($newTransaction->time));
                if ($diff <= 120) {
                    return "double-transaction";
                }
            }
        }
        return "";
    }
}
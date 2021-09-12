<?php

namespace Authorizer\services\transaction_rules;

use Authorizer\entities\Transaction;
use Authorizer\repositories\TransactionRepository;

class HighFrequencySmallIntervalRule implements TransactionRule
{
    public function authorize(Transaction $transaction, TransactionRepository $repository): string
    {
        $transactions = $repository->getTransactions();
        $sizeList = count($transactions);

	    if ($sizeList >= 3) {
            $diff = (-1) * (strtotime($transactions[$sizeList-3]->time) - strtotime($transaction->time));
            if ($diff <= 120) {
                return "high-frequency-small-interval";
            }
        }
        return "";
    }
}
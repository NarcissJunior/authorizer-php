<?php

namespace Authorizer\services\transaction_rules;

use Authorizer\repositories\TransactionRepositoryInMemory;

class HighFrequencySmallIntervalRule implements TransactionRule
{
    public function authorize(): string
    {
        $transactionRepository = new TransactionRepositoryInMemory();
        $transactions = $transactionRepository->getTransactions();

        $sizeList = count($transactions);
        $timeLimit = 2 * time()->minute;

	    if ($sizeList >= 3) {
            timeDiff := transaction.Time.Sub($transactions[3]->time);

            if timeDiff < timeLimit {
                return "high-frequency-small-interval";
            }
        }

        return "";
    }
}
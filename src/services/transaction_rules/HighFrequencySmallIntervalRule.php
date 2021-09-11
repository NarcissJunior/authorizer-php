<?php

namespace Authorizer\services\transaction_rules;

use Authorizer\repositories\TransactionRepositoryInMemory;

class HighFrequencySmallIntervalRule implements TransactionRule
{
    public function authorize(): string
    {
        $transactionRepository = new TransactionRepositoryInMemory();
        $transactions = $transactionRepository->getTransactions();

        if ($transactions[0] === null)
        {
            return "";
        }

        $myCount = 0;

        for ($i = 0; $i <= count($transactions); $i++) {
            if ($transactions[$i]->time === $transactions[$i-1]) {
                $myCount++;
            }
            if ($myCount == 3) {
                return "high-frequency-small-interval";
            }
        }

        return "";
    }
}
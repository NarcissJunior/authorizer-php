<?php

namespace Authorizer\services\transaction_rules;

use Authorizer\repositories\TransactionRepositoryInMemory;

class DoubleTransactionRule implements TransactionRule
{
    public function authorize(): string
    {
        $transactionRepository = new TransactionRepositoryInMemory();
        $transactions = $transactionRepository->getTransactions();
//
//        $timeLimit = 2 * time()->minute;
//
//        foreach ($transactions as $transaction) {
//            if ($transaction->merchant === $transactionQueVouReceber) {
//                $duration = $transaction->time . Sub($transactionQueVouReceber->time);
//                if ($duration < $timeLimit) {
//                    return "double-transaction";
//                }
//            }
//        }

        return "";
    }
}
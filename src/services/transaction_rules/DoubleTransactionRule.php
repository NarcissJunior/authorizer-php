<?php

namespace Authorizer\services\transaction_rules;

use Authorizer\entities\Transaction;
use Authorizer\repositories\TransactionRepository;
use Authorizer\repositories\TransactionRepositoryInMemory;

class DoubleTransactionRule implements TransactionRule
{
    public function authorize(Transaction $transaction, TransactionRepository $repository): string
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
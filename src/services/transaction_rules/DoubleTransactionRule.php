<?php

namespace Authorizer\services\transaction_rules;

use Authorizer\repositories\TransactionRepositoryInMemory;

class DoubleTransactionRule implements TransactionRule
{
    public function authorize(): string
    {
        $transactionRepository = new TransactionRepositoryInMemory();
        $transactions = $transactionRepository->getTransactions();



        //$diff = transaction.Time.Sub(trx[sizeList-3].Time)
        //$timeLimit = 2 * time()->min;

        if (count($transactions) >= 3) {
            for ($i = 0; $i <= count($transactions); $i++) {
                if ($diff < $timeLimit) {
                    return "double-transaction";
                }
            }
        }
        return "";

}
<?php

namespace Authorizer\services\transaction_rules;

use Authorizer\entities\Transaction;
use Authorizer\repositories\TransactionRepository;

class DoubleTransactionRule implements TransactionRule
{
    public function authorize(Transaction $transaction, TransactionRepository $repository): string
    {
        $transactions = $repository->getTransactions();


        if (count($transactions) >= 2) {
            foreach ($transactions as $arrayTransactions) {


                if ($transaction->merchant === $arrayTransactions->merchant && $transaction->amount === $arrayTransactions->amount) {

                    $diff = (-1) * (strtotime($arrayTransactions->time) - strtotime($transaction->time));

                    if ($diff <= 120) {

                        return "double-transaction";
                    }
                }
            }
        }

        return "";
    }
}
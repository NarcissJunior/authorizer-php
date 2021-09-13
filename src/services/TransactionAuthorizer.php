<?php

namespace Authorizer\services;

use Authorizer\entities\Transaction;
use Authorizer\repositories\TransactionRepository;
use Authorizer\services\transaction_rules\TransactionRule;

class TransactionAuthorizer
{
    public TransactionRule $rule;
    private TransactionRepository $repository;

    /**
     * @param TransactionRule $rule
     * @param TransactionRepository $repository
     */
    public function __construct(TransactionRule $rule, TransactionRepository $repository)
    {
        $this->rule = $rule;
        $this->repository = $repository;
    }

    public function authorize(Transaction $transaction): ?string
    {
        return $this->rule->authorize($transaction, $this->repository);
    }
}
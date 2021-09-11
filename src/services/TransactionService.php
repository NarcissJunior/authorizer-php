<?php

namespace Authorizer\services;

use Authorizer\repositories\AccountRepository;
use Authorizer\repositories\TransactionRepository;

class TransactionService
{
    /**
     * @var AccountRepository
     */
    private AccountRepository $accountRepository;

    /**
     * @var TransactionRepository
     */
    private TransactionRepository $transactionRepository;

    private $rules;

    public function __construct(AccountRepository $accountRepository, TransactionRepository $transactionRepository, $rules)
    {
        $this->accountRepository = $accountRepository;
        $this->transactionRepository = $transactionRepository;
        $this->rules = $rules;
    }

    public function processTransaction(array $transaction): array
    {
        $account =  $this->accountRepository->getAccount();

        $response["account"] = $account;
        $response["violations"] = [];

        if (!$account) {
            $response["violations"][] = "account-notinitialized";
        }

        if (!$account->activeCard) {
            $response["violations"] = "card-not-active";
        }

        if ($account->availableLimit < $transaction["amount"]) {
            $response["violations"] = " insufficient-limit";
        }

        foreach ($this->rules as $rule) {
            $validator = new TransactionAuthorizer(new $rule);
            $response["violations"] = $validator->authorize();
        }

        $account->availableLimit = $account->availableLimit - $transaction["amount"];
        $this->accountRepository->updateAccount($account);

        return $response;
    }
}
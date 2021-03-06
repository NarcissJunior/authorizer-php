<?php

namespace Authorizer\services;

use Authorizer\entities\Transaction;
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

    public function processTransaction(array $transactionFields): array
    {
        $account =  $this->accountRepository->getAccount();

        $response["account"] = [];
        $response["violations"] = [];

        if (!$account) {
            $response["violations"][] = "account-not-initialized";
            return $response;
        }

        if (!$account->activeCard) {
            $response["violations"][] = "card-not-active";
        }

        if ($account->availableLimit < $transactionFields["amount"]) {
            $response["violations"][] = "insufficient-limit";
        }

        $transaction = new Transaction();
        $transaction->merchant = $transactionFields['merchant'];
        $transaction->amount = $transactionFields['amount'];
        $transaction->time = $transactionFields['time'];

        foreach ($this->rules as $rule) {
            $validator = new TransactionAuthorizer(new $rule, $this->transactionRepository);
            if ($validator->authorize($transaction) != "") {
                $response["violations"][] = $validator->authorize($transaction);
            }
        }

        $this->transactionRepository->createTransaction($transaction);

        if (!empty($response["violations"])) {
            $response["account"]["active-card"] = $account->activeCard;
            $response["account"]["available-limit"] = $account->availableLimit;
            return $response;
        }

        $account->availableLimit = $account->availableLimit - $transactionFields["amount"];
        $this->accountRepository->updateAccount($account);

        $response["account"]["active-card"] = $account->activeCard;
        $response["account"]["available-limit"] = $account->availableLimit;

        return $response;
    }
}
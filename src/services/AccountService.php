<?php

namespace Authorizer\services;

use Authorizer\entities\Account;
use Authorizer\repositories\AccountRepository;

class AccountService
{
    /**
     * @var AccountRepository
     */
    private AccountRepository $accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function createAccount(array $account): array
    {
        $response["account"] = $account;
        $response["violations"] = [];

        if ($this->accountRepository->getAccount() !== null){
            $response["violations"][] = "account-already-initialized";
            return $response;
        }

        $currentAccount = new Account();
        $currentAccount->activeCard = $account["active-card"];
        $currentAccount->availableLimit = $account["available-limit"];
        $this->accountRepository->createAccount($currentAccount);

        return $response;
    }
}
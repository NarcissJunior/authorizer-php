<?php

namespace Authorizer;

use authorizer\services\AccountService;
use authorizer\services\TransactionService;

class Processor
{
    private AccountService          $accountService;
    private TransactionService      $transactionService;

    protected string $accountOperation =    "account";
    protected string $accountTransaction =  "transaction";

    public function __construct(AccountService $accountService, TransactionService $transactionService)
    {
        $this->accountService = $accountService;
        $this->transactionService = $transactionService;
    }

    public function process(string $request): array
    {
        $inputArray = json_decode($request, true);
        $index = array_key_first($inputArray);

        switch ($index) {
            case $this->accountOperation:
                $arrayValue = reset($inputArray);
                return $this->accountService->createAccount($arrayValue);
            case $this->accountTransaction:
                $arrayValue = reset($inputArray);

                $dateTime = date("Y-m-d H:i:s", strtotime($arrayValue['time']));
                var_dump(strtotime($arrayValue['time']));
                var_dump($dateTime);
                var_dump("oi");

                $this->transactionService->processTransaction($arrayValue);
                break;
            case "undefined":
                echo "deu ruim";
                break;
        }
        return [];
    }

}
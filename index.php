<?php

use Authorizer\Processor;
use Authorizer\repositories\AccountRepositoryInMemory;
use Authorizer\services\AccountService;

use Authorizer\services\TransactionService;
use Authorizer\repositories\TransactionRepositoryInMemory;

require __DIR__ . '/vendor/autoload.php';

$transactionRules = [
    \Authorizer\services\transaction_rules\DoubleTransactionRule::class,
    \Authorizer\services\transaction_rules\HighFrequencySmallIntervalRule::class
];

$accountRepository = new AccountRepositoryInMemory();
$accountService = new AccountService($accountRepository);

$transactionrepository = new TransactionRepositoryInMemory();
$transactionService = new TransactionService($accountRepository, $transactionrepository, $transactionRules);

$processor = new Processor($accountService, $transactionService);

$stdin = fopen('php://stdin', 'r');
$stdout = fopen('php://stdout', 'w');

while (($input = fgets($stdin)) !== false) {
    $response = $processor->process($input);
    fwrite($stdout, (json_encode($response). PHP_EOL));
}

fclose($stdin);
fclose($stdout);
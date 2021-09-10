<?php

namespace Authorizer\repositories;

use Authorizer\entities\Account;

interface AccountRepository
{
    public function getAccount(): ?Account;
    public function createAccount(Account $account): void;
    public function updateAccount(Account $account): void;

}
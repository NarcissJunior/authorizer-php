<?php

namespace Authorizer\services;

use Authorizer\services\transaction_rules\TransactionRule;

class TransactionAuthorizer
{
    public TransactionRule $rule;

    public function __construct(TransactionRule $rule)
    {
        $this->rule = $rule;
    }

    public function authorize()
    {
        return $this->rule->authorize();
    }
}
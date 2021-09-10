<?php

namespace Authorizer\services\transaction_rules;

interface TransactionRule
{
    public function authorize();
}
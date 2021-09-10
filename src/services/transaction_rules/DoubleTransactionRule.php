<?php

namespace Authorizer\services\transaction_rules;

class DoubleTransactionRule implements TransactionRule
{
    public function authorize()
    {
        return "double-transaction";
    }

}
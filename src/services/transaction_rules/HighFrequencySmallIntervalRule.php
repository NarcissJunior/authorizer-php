<?php

namespace Authorizer\services\transaction_rules;

class HighFrequencySmallIntervalRule implements TransactionRule
{
    public function authorize()
    {
        if(true){
            return "high-frequency-small-interval";
        }
        return null;
    }
}
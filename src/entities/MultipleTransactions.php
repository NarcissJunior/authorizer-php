<?php

namespace Authorizer\entities;

class MultipleTransactions extends Transaction
{
    protected $trxs = Transaction::class;
}
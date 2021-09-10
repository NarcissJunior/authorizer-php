<?php

namespace Authorizer\entities;

use Cassandra\Date;

class Transaction
{
    public string $merchant;
    public int $amount;
    public Timezone $time;
}
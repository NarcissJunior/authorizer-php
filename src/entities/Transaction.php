<?php

namespace Authorizer\entities;

class Transaction
{
    public string $merchant;
    public int $amount;
    public string $time;
}
<?php

namespace Tests;

use Authorizer\Processor;
use PHPUnit\Framework\TestCase;

class ProcessorTest extends TestCase
{
    public function test_processor_returns_jesus()
    {

    }

    public function test_operation_returns_account()
    {
        // Arrange
        $expected = "account";
        $processor = new Processor();

        // Act
        $accountOperation = $processor->getOperation("account");

        // Assert
        $this->assertEquals($expected, $accountOperation);
    }

    public function test_operation_returns_transaction()
    {
        // Arrange
        $expected = "transaction";
        $processor = new Processor();

        // Act
        $accountOperation = $processor->getOperation("transaction");

        // Assert
        $this->assertEquals($expected, $accountOperation);
    }
}
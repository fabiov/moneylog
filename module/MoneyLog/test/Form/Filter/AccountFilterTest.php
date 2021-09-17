<?php

declare(strict_types=1);

namespace MoneyLogTest\Form\Filter;

use MoneyLog\Form\Filter\AccountFilter;
use PHPUnit\Framework\TestCase;

class AccountFilterTest extends TestCase
{
    public static function testFilter(): void
    {
        $filters = new AccountFilter();

        self::assertCount(2, $filters->getInputs());
    }
}

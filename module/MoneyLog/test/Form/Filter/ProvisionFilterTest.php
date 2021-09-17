<?php

declare(strict_types=1);

namespace MoneyLogTest\Form\Filter;

use MoneyLog\Form\Filter\ProvisionFilter;
use PHPUnit\Framework\TestCase;

class ProvisionFilterTest extends TestCase
{
    public static function testFilter(): void
    {
        $filters = new ProvisionFilter();

        self::assertCount(3, $filters->getInputs());
    }
}

<?php

declare(strict_types=1);

namespace MoneyLogTest\Form\Filter;

use MoneyLog\Form\Filter\MovementFilter;
use PHPUnit\Framework\TestCase;

class MovementFilterTest extends TestCase
{
    public static function testFilter(): void
    {
        $filters = new MovementFilter();

        self::assertCount(5, $filters->getInputs());
    }
}

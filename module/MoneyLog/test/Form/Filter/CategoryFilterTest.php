<?php

declare(strict_types=1);

namespace MoneyLogTest\Form\Filter;

use MoneyLog\Form\Filter\CategoryFilter;
use PHPUnit\Framework\TestCase;

class CategoryFilterTest extends TestCase
{
    public static function testFilter(): void
    {
        $filters = new CategoryFilter();

        self::assertCount(2, $filters->getInputs());
    }
}

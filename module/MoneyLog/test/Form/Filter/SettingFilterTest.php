<?php

declare(strict_types=1);

namespace MoneyLogTest\Form\Filter;

use MoneyLog\Form\Filter\SettingFilter;
use PHPUnit\Framework\TestCase;

class SettingFilterTest extends TestCase
{
    public static function testFilter(): void
    {
        $filters = new SettingFilter();

        self::assertCount(3, $filters->getInputs());
    }
}

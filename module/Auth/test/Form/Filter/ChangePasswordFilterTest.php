<?php

declare(strict_types=1);

namespace AuthTest\Form\Filter;

use Auth\Form\Filter\ChangePasswordFilter;
use PHPUnit\Framework\TestCase;

class ChangePasswordFilterTest extends TestCase
{
    public static function testFilter(): void
    {
        $filters = new ChangePasswordFilter();

        self::assertCount(3, $filters->getInputs());
    }
}

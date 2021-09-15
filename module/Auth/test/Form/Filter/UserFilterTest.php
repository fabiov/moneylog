<?php

declare(strict_types=1);

namespace AuthTest\Form\Filter;

use Auth\Form\Filter\UserFilter;
use PHPUnit\Framework\TestCase;

class UserFilterTest extends TestCase
{
    public static function testFilter(): void
    {
        $filters = new UserFilter();

        self::assertCount(2, $filters->getInputs());
    }
}

<?php

declare(strict_types=1);

namespace AuthTest\Form\Filter;

use Auth\Form\Filter\LoginFilter;
use PHPStan\Testing\TestCase;

class LoginFilterTest extends TestCase
{
    public static function testFilter(): void
    {
        $filters = new LoginFilter();

        self::assertCount(2, $filters->getInputs());
    }
}

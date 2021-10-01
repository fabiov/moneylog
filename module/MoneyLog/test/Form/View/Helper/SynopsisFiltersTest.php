<?php

declare(strict_types=1);

namespace MoneyLogTest\Form\View\Helper;

use MoneyLog\View\Helper\SynopsisFilters;
use PHPUnit\Framework\TestCase;

class SynopsisFiltersTest extends TestCase
{
    public static function test(): void
    {
        $synopsisFilters = new SynopsisFilters();

        $filters = [
            'account' => '',
            'amountMax' => '',
            'amountMin' => '',
            'category' => '',
            'dateMax' => '',
            'dateMin' => '',
            'description' => '',
        ];
        self::assertSame('<strong>Motra tutto</strong>', ($synopsisFilters)($filters, [], []));
    }
}

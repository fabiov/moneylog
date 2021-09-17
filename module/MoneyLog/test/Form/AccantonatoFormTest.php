<?php

declare(strict_types=1);

namespace MoneyLogTest\Form;

use MoneyLog\Form\AccantonatoForm;
use PHPUnit\Framework\TestCase;

class AccantonatoFormTest extends TestCase
{
    public static function testElements(): void
    {
        $form = new AccantonatoForm();

        self::assertCount(3, $form->getElements());
    }
}

<?php

declare(strict_types=1);

namespace MoneyLogTest\Form;

use MoneyLog\Form\AccountForm;
use PHPUnit\Framework\TestCase;

class AccountFormTest extends TestCase
{
    public static function testElements(): void
    {
        $form = new AccountForm();

        self::assertCount(2, $form->getElements());
    }
}

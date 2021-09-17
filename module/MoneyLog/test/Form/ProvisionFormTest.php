<?php

declare(strict_types=1);

namespace MoneyLogTest\Form;

use MoneyLog\Form\ProvisionForm;
use PHPUnit\Framework\TestCase;

class ProvisionFormTest extends TestCase
{
    public static function testElements(): void
    {
        $form = new ProvisionForm();

        self::assertCount(3, $form->getElements());
    }
}

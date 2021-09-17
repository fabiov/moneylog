<?php

declare(strict_types=1);

namespace MoneyLogTest\Form;

use MoneyLog\Form\SettingForm;
use PHPUnit\Framework\TestCase;

class SettingFormTest extends TestCase
{
    public function testElements(): void
    {
        $form = new SettingForm();

        self::assertCount(3, $form->getElements());
    }
}

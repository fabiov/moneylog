<?php

declare(strict_types=1);

namespace AuthTest\Form;

use Auth\Form\ChangePasswordForm;
use PHPUnit\Framework\TestCase;

class ChangePasswordFormTest extends TestCase
{
    public static function testElements(): void
    {
        $form = new ChangePasswordForm();

        self::assertCount(3, $form->getElements());
    }
}

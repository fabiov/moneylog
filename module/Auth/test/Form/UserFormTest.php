<?php

declare(strict_types=1);

namespace AuthTest\Form;

use Auth\Form\UserForm;
use PHPUnit\Framework\TestCase;

class UserFormTest extends TestCase
{
    public static function testElements(): void
    {
        $form = new UserForm();

        self::assertCount(2, $form->getElements());
    }
}

<?php

declare(strict_types=1);

namespace AuthTest\Form;

use Auth\Form\RegistrationForm;
use PHPUnit\Framework\TestCase;

class RegistrationFormTest extends TestCase
{
    public function testElements()
    {
        $form = new RegistrationForm();

        self::assertCount(6, $form->getElements());
    }
}

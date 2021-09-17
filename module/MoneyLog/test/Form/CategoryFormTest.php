<?php

declare(strict_types=1);

namespace MoneyLogTest\Form;

use MoneyLog\Form\CategoryForm;
use PHPUnit\Framework\TestCase;

class CategoryFormTest extends TestCase
{
    public static function testElements(): void
    {
        $form = new CategoryForm();

        self::assertCount(4, $form->getElements());
    }
}

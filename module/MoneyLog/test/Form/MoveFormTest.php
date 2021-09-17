<?php

declare(strict_types=1);

namespace MoneyLogTest\Form;

use MoneyLog\Form\MoveForm;
use function PHPUnit\Framework\assertSame;
use PHPUnit\Framework\TestCase;

class MoveFormTest extends TestCase
{
    public static function testElements(): void
    {
        $options = ['', 'Uno', 'Due'];
        $form = new MoveForm();
        $form->setAccountOptions($options);

        /** @var array<string, mixed> $elements */
        $elements = $form->getElements();
        self::assertCount(4, $elements);

        /** @var \Laminas\Form\Element\Select $accountElement */
        $accountElement = $elements['targetAccountId'];
        assertSame($options, $accountElement->getValueOptions());
    }
}

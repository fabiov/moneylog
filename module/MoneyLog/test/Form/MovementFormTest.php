<?php

declare(strict_types=1);

namespace MoneyLogTest\Form;

use Doctrine\ORM\EntityManagerInterface;
use MoneyLog\Form\MovementForm;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class MovementFormTest extends TestCase
{
    use ProphecyTrait;

    public function testElements(): void
    {
        /** @var EntityManagerInterface $em */
        $em = $this->prophesize(EntityManagerInterface::class)->reveal();
        $form = new MovementForm('movement', $em, 1);

        self::assertCount(6, $form->getElements());
    }
}

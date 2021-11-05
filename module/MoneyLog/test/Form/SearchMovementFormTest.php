<?php

declare(strict_types=1);

namespace MoneyLogTest\Form;

use Application\Entity\Account;
use MoneyLog\Form\SearchMovementForm;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class SearchMovementFormTest extends TestCase
{
    use ProphecyTrait;

    public function testWhitOpenAndClosed(): void
    {
        $bpmAccountProphecy = $this->prophesize(Account::class);
        $bpmAccountProphecy->getId()->willReturn(1);
        $bpmAccountProphecy->getName()->willReturn('Banco Popolare di Milano');
        $bpmAccountProphecy->getStatus()->willReturn(Account::STATUS_CLOSED);
        /** @var Account $bpmAccount */
        $bpmAccount = $bpmAccountProphecy->reveal();

        $widibaAccountProphecy = $this->prophesize(Account::class);
        $widibaAccountProphecy->getId()->willReturn(2);
        $widibaAccountProphecy->getName()->willReturn('Widiba');
        $widibaAccountProphecy->getStatus()->willReturn(Account::STATUS_OPEN);
        /** @var Account $widibaAccount */
        $widibaAccount = $widibaAccountProphecy->reveal();

        $form = new SearchMovementForm([$bpmAccount, $widibaAccount]);

        /** @var \Laminas\Form\Element\Select $selectAccount */
        $selectAccount = $form->get('account');

        self::assertCount(1, $form->getElements());
        self::assertSame([
            0 => 'Tutti',
            'opened' => [
                'label' => 'Aperti',
                'options' => [2 => 'Widiba']
            ],
            'closed' => [
                'label' => 'Chiusi',
                'options' => [1 => 'Banco Popolare di Milano']
            ]
        ], $selectAccount->getValueOptions());
    }

    public function testWhitOpen(): void
    {
        $bpmAccountProphecy = $this->prophesize(Account::class);
        $bpmAccountProphecy->getId()->willReturn(1);
        $bpmAccountProphecy->getName()->willReturn('Banco Popolare di Milano');
        $bpmAccountProphecy->getStatus()->willReturn(Account::STATUS_OPEN);
        /** @var Account $bpmAccount */
        $bpmAccount = $bpmAccountProphecy->reveal();

        $widibaAccountProphecy = $this->prophesize(Account::class);
        $widibaAccountProphecy->getId()->willReturn(2);
        $widibaAccountProphecy->getName()->willReturn('Widiba');
        $widibaAccountProphecy->getStatus()->willReturn(Account::STATUS_OPEN);
        /** @var Account $widibaAccount */
        $widibaAccount = $widibaAccountProphecy->reveal();

        $form = new SearchMovementForm([$bpmAccount, $widibaAccount]);

        /** @var \Laminas\Form\Element\Select $selectAccount */
        $selectAccount = $form->get('account');

        self::assertCount(1, $form->getElements());
        self::assertSame(['Tutti', 'Banco Popolare di Milano', 'Widiba'], $selectAccount->getValueOptions());
    }
}

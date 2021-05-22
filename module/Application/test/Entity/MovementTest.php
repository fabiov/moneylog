<?php

namespace ApplicationTest\Entity;

use Application\Entity\Account;
use Application\Entity\Category;
use Application\Entity\Movement;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterInterface;
use PHPUnit\Framework\TestCase;

class MovementTest extends TestCase
{
    public function testSettersAndGetters(): void
    {
        $movement = new Movement();

        $description = 'Description';
        $movement->setDescription($description);
        self:: assertSame($description, $movement->getDescription());

        $account = new Account();
        $movement->setAccount($account);
        self:: assertSame($account, $movement->getAccount());

        $amount = 12.03;
        $movement->setAmount($amount);
        self:: assertSame($amount, $movement->getAmount());

        $category = new Category();
        $movement->setCategory($category);
        self:: assertSame($category, $movement->getCategory());

        $date = new \DateTime();
        $movement->setDate($date);
        self:: assertSame($date, $movement->getDate());

        self::assertInstanceOf(InputFilterInterface::class, $movement->getInputFilter());

        self::expectException(\Exception::class);
        $movement->setInputFilter(new InputFilter());
    }

    public function testArrayExchangeAndCopy(): void
    {
        $movement = new Movement();

//        $account = new Account();
        $category = new Category();
//        $date = new \DateTime();
        $amount = 78.54;

        $movement->exchangeArray([
            //            'account' => $account,
            'category' => $category,
            //            'date' => $date,
            'amount' => $amount,
        ]);

        $copy = $movement->getArrayCopy();

//        self::assertSame($copy['account'], $movement->getAccount());
        self::assertSame($copy['category'], $movement->getCategory());
//        self::assertSame($copy['date'], $movement->getDate());
        self::assertSame($copy['amount'], $movement->getAmount());
    }
}

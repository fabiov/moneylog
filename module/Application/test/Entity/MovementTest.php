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
    public function testGettersWithoutSetter(): void
    {
        $id = 1;
        $movement = new Movement();
        $reflectionClass = new \ReflectionClass($movement);
        $reflectedProperty = $reflectionClass->getProperty('id');
        $reflectedProperty->setAccessible(true);

        $reflectedProperty->setValue($movement, $id);

        self::assertSame($id, $movement->getId());
    }

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

        $accountId = 78;
        $amount = 78.54;
        $category = new Category();
        $date = '2021-06-16';
        $description = 'Description';

        $movement->exchangeArray([
            'accountId' => $accountId,
            'amount' => $amount,
            'category' => $category,
            'date' => $date,
            'description' => $description,
        ]);

        $copy = $movement->getArrayCopy();

        self::assertSame($copy['amount'], $movement->getAmount());
        self::assertSame($copy['category'], $movement->getCategory());
        self::assertSame($copy['date'], $movement->getDate());
        self::assertSame($copy['description'], $movement->getDescription());
    }
}

<?php

namespace ApplicationTest\Entity;

use Application\Entity\Account;
use Application\Entity\Category;
use Application\Entity\Movement;
use PHPUnit\Framework\TestCase;

class MovementTest extends TestCase
{
    public function testGettersWithoutSetter(): void
    {
        $id = 1;
        $movement = new Movement(new Account(), 0, new \DateTime(), '');
        $reflectionClass = new \ReflectionClass($movement);
        $reflectedProperty = $reflectionClass->getProperty('id');
        $reflectedProperty->setAccessible(true);

        $reflectedProperty->setValue($movement, $id);

        self::assertSame($id, $movement->getId());
    }

    public function testSettersAndGetters(): void
    {
        $movement = new Movement(new Account(), 0, new \DateTime(), '');

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
    }

    public function testArrayCopy(): void
    {
        $account = new Account();
        $category = new Category();
        $date = new \DateTime();
        $amount = 10.23;
        $description = 'Description';

        $movement = new Movement($account, $amount, $date, $description, $category);

        $copy = $movement->getArrayCopy();

        self:: assertSame($account, $copy['account']);
        self:: assertSame($category, $copy['category']);
        self:: assertSame($date, $copy['date']);
        self:: assertSame($amount, $copy['amount']);
        self:: assertSame($description, $copy['description']);
    }
}

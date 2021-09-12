<?php

namespace ApplicationTest\Entity;

use Application\Entity\Provision;
use Application\Entity\User;
use PHPUnit\Framework\TestCase;

class ProvisionTest extends TestCase
{
    public function testGettersWithoutSetter(): void
    {
        $provision = new Provision();
        $reflectionClass = new \ReflectionClass($provision);

        $id = 1;
        $reflectedProperty = $reflectionClass->getProperty('id');
        $reflectedProperty->setAccessible(true);
        $reflectedProperty->setValue($provision, $id);
        self::assertSame($id, $provision->getId());
    }

    public function testSettersWithoutGetter(): void
    {
        $provision = new Provision();
        $reflectionClass = new \ReflectionClass($provision);

        $user = new User();
        $provision->setUser($user);
        $reflectedProperty = $reflectionClass->getProperty('user');
        $reflectedProperty->setAccessible(true);
        self::assertSame($user, $reflectedProperty->getValue($provision));
    }

    public function testGettersAndSetters(): void
    {
        $provision = new Provision();

        $amount = 67.65;
        $provision->setAmount($amount);
        self::assertSame($amount, $provision->getAmount());

        $date = new \DateTime();
        $provision->setDate($date);
        self::assertSame($date, $provision->getDate());

        $description = 'Description';
        $provision->setDescription($description);
        self::assertSame($description, $provision->getDescription());
    }

    public function testArrayCopy(): void
    {
        $provision = new Provision();

        $amount = 23.34;
        $date = new \DateTime();
        $description = 'Description';
        $user = new User();

        $provision->setAmount($amount);
        $provision->setDate($date);
        $provision->setDescription($description);
        $provision->setUser($user);

        $copy = $provision->getArrayCopy();

        self::assertSame($amount, $copy['amount']);
        self::assertSame($date, $copy['date']);
        self::assertSame($description, $copy['description']);
        self::assertSame($user, $copy['user']);
    }
}

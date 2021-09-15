<?php

namespace ApplicationTest\Entity;

use Application\Entity\Provision;
use Application\Entity\User;
use PHPUnit\Framework\TestCase;

class ProvisionTest extends TestCase
{
    public function testGettersWithoutSetter(): void
    {
        $provision = new Provision(new User('', '', '', '', '', User::STATUS_CONFIRMED, '', ''), new \DateTime(), 23.9, '');
        $reflectionClass = new \ReflectionClass($provision);

        $id = 1;
        $reflectedProperty = $reflectionClass->getProperty('id');
        $reflectedProperty->setAccessible(true);
        $reflectedProperty->setValue($provision, $id);
        self::assertSame($id, $provision->getId());
    }

    public function testGettersAndSetters(): void
    {
        $user = new User('', '', '', '', '', User::STATUS_CONFIRMED, '', '');
        $date = new \DateTime();
        $amount = 67.65;
        $description = 'Description';

        $provision = new Provision($user, $date, $amount, $description);

        self::assertSame($user, $provision->getUser());
        self::assertSame($amount, $provision->getAmount());
        self::assertSame($date, $provision->getDate());
        self::assertSame($description, $provision->getDescription());

        $date = new \DateTime('2021-09-12');
        $amount = 67.67;
        $description = 'Description 2';

        $provision->setAmount($amount);
        $provision->setDate($date);
        $provision->setDescription($description);

        self::assertSame($amount, $provision->getAmount());
        self::assertSame($date, $provision->getDate());
        self::assertSame($description, $provision->getDescription());
    }
}

<?php

namespace ApplicationTest\Entity;

use Application\Entity\Provision;
use Application\Entity\User;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterInterface;
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

        self::assertInstanceOf(InputFilterInterface::class, $provision->getInputFilter());

        self::expectException(\Exception::class);
        $provision->setInputFilter(new InputFilter());
    }

    public function testArrayExchangeAndCopy(): void
    {
        $provision = new Provision();

        $amount = 23.34;
        $date = '2021-04-13';
        $description = 'Description';
        $user = 1;

        $provision->exchangeArray([
            'amount' => $amount,
            'date' => $date,
            'description' => $description,
            'user' => $user,
        ]);

        $copy = $provision->getArrayCopy();

        self::assertSame($amount, $copy['amount']);
        self::assertSame($date, $copy['date']->format('Y-m-d'));
        self::assertSame($description, $copy['description']);
        self::assertSame($user, $copy['user']);
    }
}

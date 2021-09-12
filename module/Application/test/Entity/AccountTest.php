<?php

namespace ApplicationTest\Entity;

use Application\Entity\Account;
use Application\Entity\User;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    public function testGettersWithoutSetter(): void
    {
        $id = 1;
        $account = new Account();
        $reflectionClass = new \ReflectionClass($account);
        $reflectedProperty = $reflectionClass->getProperty('id');
        $reflectedProperty->setAccessible(true);

        $reflectedProperty->setValue($account, $id);

        self::assertSame($id, $account->getId());
    }

    public function testSetterAndGetter(): void
    {
        $account = new Account();

        $user = new User();
        $account->setUser($user);
        self::assertSame($user, $account->getUser());

        $name = 'test';
        $account->setName($name);
        self::assertSame($name, $account->getName());

        $recap = 1;
        $account->setRecap($recap);
        self::assertSame($recap, $account->getRecap());

        $closed = false;
        $account->setClosed($closed);
        self::assertSame($closed, $account->isClosed());
    }

    public function testArrayExchangeAndCopy(): void
    {
        $user = new User();
        $name = 'test';
        $recap = 1;
        $closed = false;

        $account = new Account();

        $account->setUser($user);
        $account->setName($name);
        $account->setRecap($recap);
        $account->setClosed($closed);

        $copy = $account->getArrayCopy();

        self::assertSame($copy['user'], $user);
        self::assertSame($copy['name'], $name);
        self::assertSame($copy['recap'], $recap);
        self::assertSame($copy['closed'], $closed);
    }
}

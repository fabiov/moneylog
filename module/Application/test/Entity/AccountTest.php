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
        $account = new Account(new User(), '');
        $reflectionClass = new \ReflectionClass($account);
        $reflectedProperty = $reflectionClass->getProperty('id');
        $reflectedProperty->setAccessible(true);

        $reflectedProperty->setValue($account, $id);

        self::assertSame($id, $account->getId());
    }

    public function testSetterAndGetter(): void
    {
        $user = new User();
        $name = 'test';
        $account = new Account($user, $name);

        self::assertSame($user, $account->getUser());
        self::assertSame($name, $account->getName());
        self::assertSame(0, $account->getRecap());
        self::assertSame(false, $account->isClosed());

        $name = 'test2';
        $account->setName($name);
        self::assertSame($name, $account->getName());

        $recap = 1;
        $account->setRecap($recap);
        self::assertSame($recap, $account->getRecap());

        $closed = true;
        $account->setClosed($closed);
        self::assertSame($closed, $account->isClosed());
    }
}

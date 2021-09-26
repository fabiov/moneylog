<?php

namespace ApplicationTest\Entity;

use Application\Entity\Account;
use Application\Entity\User;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    public function testGettersWithoutSetter(): void
    {
        $id = 1;
        $account = new Account(new User('', '', '', '', '', User::STATUS_CONFIRMED, '', ''), '');
        $reflectionClass = new \ReflectionClass($account);
        $reflectedProperty = $reflectionClass->getProperty('id');
        $reflectedProperty->setAccessible(true);

        $reflectedProperty->setValue($account, $id);

        self::assertSame($id, $account->getId());
    }

    public function testSetterAndGetter(): void
    {
        $user = new User('', '', '', '', '', User::STATUS_CONFIRMED, '', '');
        $name = 'test';
        $account = new Account($user, $name);

        self::assertSame($user, $account->getUser());
        self::assertSame($name, $account->getName());
        self::assertSame(Account::STATUS_OPEN, $account->getStatus());

        $name = 'test2';
        $account->setName($name);
        self::assertSame($name, $account->getName());

        $status = Account::STATUS_CLOSED;
        $account->setStatus($status);
        self::assertSame($status, $account->getStatus());

        self::assertInstanceOf(Collection::class, $account->getMovements());
    }
}

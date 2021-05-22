<?php

namespace ApplicationTest\Entity;

use Application\Entity\Account;
use Application\Entity\User;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterInterface;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    public function testSetter(): void
    {
        $user = new User();
        $account = new Account();

        $account->setUser($user);
        self::assertSame($user, $account->getUser());

        self::assertInstanceOf(InputFilterInterface::class, $account->getInputFilter());

        $this->expectException(\Exception::class);
        $account->setInputFilter(new InputFilter());
    }

    public function testGetters(): void
    {
        $user = new User();
        $name = 'test';
        $closed = true;

        $account = new Account();

        $account->exchangeArray([
            'user' => $user,
            'name' => $name,
            'closed' => $closed,
        ]);

        self::assertSame($account->getUser(), $user);
        self::assertSame($account->getName(), $name);
        self::assertSame($account->isClosed(), $closed);
    }

    public function testArrayExchangeAndCopy(): void
    {
        $user = new User();
        $name = 'test';
        $recap = true;
        $closed = false;

        $account = new Account();

        $account->exchangeArray([
            'user' => $user,
            'name' => $name,
            'recap' => $recap,
            'closed' => $closed,
        ]);

        $copy = $account->getArrayCopy();

        self::assertSame($copy['user'], $user);
        self::assertSame($copy['name'], $name);
        self::assertSame($copy['recap'], $recap);
        self::assertSame($copy['closed'], $closed);
    }
}

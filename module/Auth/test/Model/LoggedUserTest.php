<?php

namespace AuthTest\Model;

use Auth\Model\LoggedUser;
use Auth\Model\LoggedUserSettings;
use PHPUnit\Framework\TestCase;

class LoggedUserTest extends TestCase
{
    public function testGetters(): void
    {
        $id = 1;
        $name = 'Mario';
        $surname = 'Draghi';
        $email = 'mario.draghi@mail.it';
        $role = 'user';
        $settings = new LoggedUserSettings(10, 12, true);

        $loggedUser = new LoggedUser($id, $name, $surname, $email, $role, $settings);

        self::assertSame($id, $loggedUser->getId());
        self::assertSame($name, $loggedUser->getName());
        self::assertSame($surname, $loggedUser->getSurname());
        self::assertSame($email, $loggedUser->getEmail());
        self::assertSame($role, $loggedUser->getRole());
        self::assertSame($settings, $loggedUser->getSettings());
    }
}

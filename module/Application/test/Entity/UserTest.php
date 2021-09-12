<?php

namespace ApplicationTest\Entity;

use Application\Entity\Setting;
use Application\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGettersWithoutSetter(): void
    {
        $id = 1;
        $user = new User();
        $reflectionClass = new \ReflectionClass($user);
        $reflectedProperty = $reflectionClass->getProperty('id');
        $reflectedProperty->setAccessible(true);

        $reflectedProperty->setValue($user, $id);

        self::assertSame($id, $user->getId());
    }

    public function testGettersAndSetters(): void
    {
        $user = new User();

        $status = User::STATUS_CONFIRMED;
        $user->setStatus($status);
        self::assertSame($status, $user->getStatus());

        $password = 'password';
        $user->setPassword($password);
        self::assertSame($password, $user->getPassword());

        $email = 'email';
        $user->setEmail($email);
        self::assertSame($email, $user->getEmail());

        $lastLogin = new \DateTime();
        $user->setLastLogin($lastLogin);
        self::assertSame($lastLogin, $user->getLastLogin());

        $name = 'name';
        $user->setName($name);
        self::assertSame($name, $user->getName());

        $role = 'role';
        $user->setRole($role);
        self::assertSame($role, $user->getRole());

        $salt = 'salt';
        $user->setSalt($salt);
        self::assertSame($salt, $user->getSalt());

        $setting = new Setting($user);
        $user->setSetting($setting);
        self::assertSame($setting, $user->getSetting());

        $surname = 'surname';
        $user->setSurname($surname);
        self::assertSame($surname, $user->getSurname());
    }

    public function testArrayCopy(): void
    {
        $email = 'email';
        $name = 'name';
        $password = 'password';
        $registrationToken = 'registrationToken';
        $role = 'role';
        $salt = 'salt';
        $status = User::STATUS_NOT_CONFIRMED;
        $surname = 'surname';

        $user = new User();
        $user->setEmail($email);
        $user->setName($name);
        $user->setPassword($password);
        $user->setRegistrationToken($registrationToken);
        $user->setRole($role);
        $user->setSalt($salt);
        $user->setStatus($status);
        $user->setSurname($surname);

        $copy = $user->getArrayCopy();

        self::assertSame($email, $copy['email']);
        self::assertSame($name, $copy['name']);
        self::assertSame($password, $copy['password']);
        self::assertSame($registrationToken, $copy['registrationToken']);
        self::assertSame($registrationToken, $user->getRegistrationToken());
        self::assertSame($role, $copy['role']);
        self::assertSame($salt, $copy['salt']);
        self::assertSame($status, $copy['status']);
        self::assertSame($surname, $copy['surname']);
    }

    public function testSetStatusException(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $user = new User();
        $user->setStatus(2);
    }
}

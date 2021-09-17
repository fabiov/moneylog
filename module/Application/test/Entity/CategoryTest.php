<?php

namespace ApplicationTest\Entity;

use Application\Entity\Category;
use Application\Entity\User;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    public function testGettersWithoutSetter(): void
    {
        $id = 1;
        $category = new Category(new User('', '', '', '', '', User::STATUS_CONFIRMED, '', ''), '');
        $reflectionClass = new \ReflectionClass($category);
        $reflectedProperty = $reflectionClass->getProperty('id');
        $reflectedProperty->setAccessible(true);

        $reflectedProperty->setValue($category, $id);

        self::assertSame($id, $category->getId());
    }

    public function testSetterAndGetters(): void
    {
        $user = new User('', '', '', '', '', User::STATUS_CONFIRMED, '', '');
        $description = 'Description';
        $active = true;

        $category = new Category($user, $description, $active);

        self::assertSame($user, $category->getUser());
        self::assertSame($description, $category->getDescription());
        self::assertSame($active, $category->isActive());

        $description = 'Description 2';
        $active = false;

        $category->setDescription($description);
        $category->setActive($active);
        self::assertSame($description, $category->getDescription());
        self::assertSame($active, $category->isActive());
    }
}

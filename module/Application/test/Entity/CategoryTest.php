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
        $category = new Category(new User('', '', '', '', '', User::STATUS_CONFIRMED, '', ''), '', Category::STATUS_ACTIVE);
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
        $status = Category::STATUS_ACTIVE;

        $category = new Category($user, $description, $status);

        self::assertSame($user, $category->getUser());
        self::assertSame($description, $category->getDescription());
        self::assertSame($status, $category->getStatus());

        $description = 'Description 2';
        $status = Category::STATUS_INACTIVE;

        $category->setDescription($description);
        $category->setStatus($status);
        self::assertSame($description, $category->getDescription());
        self::assertSame($status, $category->getStatus());

        self::expectException(\Exception::class);
        $category->setStatus(2);
    }
}

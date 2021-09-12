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
        $category = new Category();
        $reflectionClass = new \ReflectionClass($category);
        $reflectedProperty = $reflectionClass->getProperty('id');
        $reflectedProperty->setAccessible(true);

        $reflectedProperty->setValue($category, $id);

        self::assertSame($id, $category->getId());
    }

    public function testSetterAndGetters(): void
    {
        $category = new Category();

        $user = new User();
        $category->setUser($user);
        self::assertSame($user, $category->getUser());

        $description = 'Description';
        $category->setDescription($description);
        self::assertSame($description, $category->getDescription());

        $status = Category::STATUS_ACTIVE;
        $category->setStatus($status);
        self::assertSame($status, $category->getStatus());
    }

    public function testStatusException(): void
    {
        $category = new Category();
        self::expectException(\Exception::class);
        $category->setStatus(2);
    }

    public function testArrayCopy(): void
    {
        $category = new Category();

        $user = new User();
        $description = 'description';
        $status = Category::STATUS_INACTIVE;

        $category->setUser($user);
        $category->setDescription($description);
        $category->setStatus($status);

        $copy = $category->getArrayCopy();

        self::assertSame($copy['user'], $user);
        self::assertSame($copy['description'], $description);
        self::assertSame($copy['status'], $status);
    }
}

<?php

declare(strict_types=1);

namespace ApplicationTest\ViewHelper;

use Application\ViewHelper\UserData;
use Laminas\View\Renderer\PhpRenderer;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class UserDataTest extends TestCase
{
    use ProphecyTrait;

    public function testUserData(): void
    {
        $viewProphesize = $this->prophesize(PhpRenderer::class);
        $viewProphesize->identity()->willReturn(null);
        /** @var \Laminas\View\Renderer\RendererInterface $view */
        $view = $viewProphesize->reveal();

        $userData = new UserData();
        $userData->setView($view);

        self::assertSame($userData, $userData());
        self::assertNull($userData->getFullName());
        self::assertNull($userData->hasStored());
    }
}

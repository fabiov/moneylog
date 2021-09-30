<?php

declare(strict_types=1);

namespace ApplicationTest\ViewHelper;

use Application\ViewHelper\SortLink;
use Laminas\View\Renderer\PhpRenderer;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class SortLinkTest extends TestCase
{
    use ProphecyTrait;

    public function testSameFieldAsc(): void
    {
        $field = 'date';
        $text = 'Data';
        $routeName = 'routeName';

        $viewProphesize = $this->prophesize(PhpRenderer::class);
        $viewProphesize
            ->escapeHtml($text)
            ->willReturn($text);
        $viewProphesize
            ->url($routeName, [], ["query" => ["orderField" => "date", "orderType" => "DESC"]])
            ->willReturn("/?orderField=$field&orderType=DESC");

        $sortLink = new SortLink();

        /** @var \Laminas\View\Renderer\RendererInterface */
        $view = $viewProphesize->reveal();
        $sortLink->setView($view);

        $url = ($sortLink)('Data', 'date', $routeName, ['orderField' => 'date', 'orderType' => 'ASC']);

        self::assertSame('<a href="/?orderField=date&orderType=DESC">Data <span class="glyphicon glyphicon-triangle-top"></span></a>', $url);
    }

    public function testSameFieldDesc(): void
    {
        $viewProphesize = $this->prophesize(PhpRenderer::class);
        $sortLink = new SortLink();

        /** @var \Laminas\View\Renderer\RendererInterface */
        $view = $viewProphesize->reveal();
        $sortLink->setView($view);

        $url = ($sortLink)('Data', 'date', 'routeName', ['orderField' => 'date', 'orderType' => 'DESC']);

        self::assertSame('<a href=""> <span class="glyphicon glyphicon-triangle-bottom"></span></a>', $url);
    }

    public function testDifferentField(): void
    {
        $viewProphesize = $this->prophesize(PhpRenderer::class);
        $sortLink = new SortLink();

        /** @var \Laminas\View\Renderer\RendererInterface */
        $view = $viewProphesize->reveal();
        $sortLink->setView($view);

        $url = ($sortLink)('Data', 'date', 'routeName', ['orderField' => 'amount', 'orderType' => 'DESC']);

        self::assertSame('<a href=""></a>', $url);
    }
}

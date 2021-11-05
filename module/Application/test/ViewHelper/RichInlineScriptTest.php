<?php

namespace ApplicationTest\ViewHelper;

use Application\ViewHelper\RichInlineScript;
use Laminas\View\Helper\InlineScript;
use Laminas\View\Renderer\PhpRenderer;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class RichInlineScriptTest extends TestCase
{
    use ProphecyTrait;

    public function testAddOnDocumentReady(): void
    {
        $js = 'alert(\'Hello\');';
        $expected = "$(document).ready(function(){\n    $js\n\n});\n";

        $inlineScriptProphecy = $this->prophesize(InlineScript::class);
        $inlineScriptProphecy->appendScript($expected)->willReturn(null);
        $inlineScriptProphecy->__toString()->willReturn($expected);

        $viewProphecy = $this->prophesize(PhpRenderer::class);
        $viewProphecy->inlineScript()->willReturn($inlineScriptProphecy->reveal());
        /** @var \Laminas\View\Renderer\RendererInterface $view */
        $view = $viewProphecy->reveal();

        $richInlineScript = new RichInlineScript();
        $richInlineScript->setView($view);

        $richInlineScript->addOnDocumentReady($js);
        self::assertSame($expected, (string) $richInlineScript);
    }

    public function testAddGeneric(): void
    {
        $js = 'alert(\'Hello\');';

        $inlineScriptProphecy = $this->prophesize(InlineScript::class);
        $inlineScriptProphecy->appendScript("$js\n")->willReturn(null);
        $inlineScriptProphecy->__toString()->willReturn($js);

        $viewProphecy = $this->prophesize(PhpRenderer::class);
        $viewProphecy->inlineScript()->willReturn($inlineScriptProphecy->reveal());
        /** @var \Laminas\View\Renderer\RendererInterface $view */
        $view = $viewProphecy->reveal();

        $richInlineScript = new RichInlineScript();
        $richInlineScript->setView($view);

        $richInlineScript->addGeneric($js);
        self::assertSame($js, (string) $richInlineScript);
    }
}

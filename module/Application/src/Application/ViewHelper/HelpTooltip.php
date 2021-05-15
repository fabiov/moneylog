<?php

namespace Application\ViewHelper;

use Laminas\Escaper\Escaper;
use Laminas\View\Helper\AbstractHelper;

/**
 * @property \Laminas\View\Renderer\RendererInterface $view
 */
class HelpTooltip extends AbstractHelper
{
    /**
     * @param string $text
     * @param string $place
     * @return string
     */
    public function __invoke(string $text, string $place = 'top'): string
    {
        $escaper = new Escaper('utf-8');
        $this->view->richInlineScript()->addGeneric('$(\'[data-toggle="tooltip"]\').tooltip(); ' . PHP_EOL);
        return '<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="' . $place . '" title="' . $escaper->escapeHtmlAttr($text) . '"></i>';
    }
}

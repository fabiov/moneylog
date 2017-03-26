<?php
namespace Application\ViewHelper;

use Zend\Escaper\Escaper;
use Zend\View\Helper\AbstractHelper;

class HelpTooltip extends AbstractHelper
{
    /**
     * @param $text
     * @param string $place
     * @return string
     */
    public function __invoke($text, $place = 'top')
    {
        $escaper = new Escaper('utf-8');
        $this->view->richInlineScript()->addGeneric('$(\'[data-toggle="tooltip"]\').tooltip(); ' . PHP_EOL);
        return '<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="' . $place . '" title="' . $escaper->escapeHtmlAttr($text) . '"></i>';
    }
}
<?php
namespace Application\ViewHelper;

use Zend\Debug\Debug;
use Zend\Escaper\Escaper;
use Zend\Form\Element;
use Zend\View\Helper\AbstractHelper;

class SbaFormRow extends AbstractHelper
{
    /**
     * @param Element $element
     * @param string $help
     * @return string
     */
    public function __invoke(Element $element, $help = '')
    {

        if ($help) {
            $escaper = new Escaper('utf-8');

//            $helpPopHover = ' <i class="fa fa-question-circle" data-toggle="popover" data-placement="right" data-content="' . $escaper->escapeHtmlAttr($help) . '" style="cursor:pointer;"></i>';
//            $this->view->richInlineScript()->addGeneric('$("[data-toggle=popover]").popover()' . PHP_EOL);

            $helpPopHover = ' <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" title="' . $escaper->escapeHtmlAttr($help) . '"></i>';
            $this->view->richInlineScript()->addGeneric('$(".form-group").tooltip({selector:"[data-toggle=tooltip]",container:"body"});' . PHP_EOL);
        } else {
            $helpPopHover = '';
        }

        $element->setAttribute('class', 'form-control');
        $input = $element instanceof Element\Select ? $this->view->formSelect($element)
                                                    : $this->view->formInput($element);
        $errors = $this->view->formElementErrors($element);
        return '<div class="' . ($errors ? 'form-group has-error' : 'form-group') . '">'
            . $this->view->formLabel($element->setLabelAttributes(['class' => 'control-label'])) . $helpPopHover
            . $input
            . $errors
            . '</div>';
    }
}
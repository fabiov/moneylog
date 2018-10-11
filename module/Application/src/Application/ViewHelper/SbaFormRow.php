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

            $helpPopHover = ' <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" title="' . $escaper->escapeHtmlAttr($help) . '"></i><br>';
            $this->view->richInlineScript()->addGeneric('$(".form-group").tooltip({selector:"[data-toggle=tooltip]",container:"body"});' . PHP_EOL);
        } else {
            $helpPopHover = '';
        }


        if ($element instanceof Element\Select) {
            $element->setAttribute('class', self::addFormControllCssClass($element));
            $input = $this->view->formSelect($element);
        } elseif ($element instanceof Element\Checkbox) {
            $input = $this->view->formCheckbox($element);
        } else {
            $element->setAttribute('class', self::addFormControllCssClass($element));
            $input = $this->view->formInput($element);
        }

        $errors = $this->view->formElementErrors($element);
        return '<div class="' . ($errors ? 'form-group has-error' : 'form-group') . '">'
            . $this->view->formLabel($element->setLabelAttributes(['class' => 'control-label'])) . $helpPopHover
            . $input
            . $errors
            . '</div>';
    }

    /**
     * @param Element $element
     * @return string
     */
    private static function addFormControllCssClass(Element $element)
    {
        $str = $element->getAttribute('class');
        return preg_match('/(^| )form-control( |$)/', $str) ? $str : "$str form-control";
    }
}

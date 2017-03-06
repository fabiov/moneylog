<?php
namespace Application\ViewHelper;

use Zend\Debug\Debug;
use Zend\Form\Element;
use Zend\View\Helper\AbstractHelper;

class SbaFormRow extends AbstractHelper
{
    /**
     * @param $element
     * @return string
     */
    public function __invoke(Element $element)
    {
        $element->setAttribute('class', 'form-control');
        $input = $element instanceof Element\Select ? $this->view->formSelect($element)
                                                    : $this->view->formInput($element);
        $errors = $this->view->formElementErrors($element);
        return '<div class="' . ($errors ? 'form-group has-error' : 'form-group') . '">'
            . $this->view->formLabel($element->setLabelAttributes(['class' => 'control-label']))
            . $input
            . $errors
            . '</div>';
    }
}
<?php
namespace Auth\Form\Filter;

use Zend\InputFilter\InputFilter;

/**
 * Class UserFilter
 * @package Auth\Form\Filter
 */
class UserFilter extends InputFilter
{
    /**
     * UserFilter constructor.
     */
    function __construct()
    {
        $this
            ->add(array(
                'name' => 'name',
                'required' => true,
                'filters' => array(array('name' => 'StringTrim'))
            ))
            ->add(array(
                'name' => 'surname',
                'required' => true,
                'filters' => array(array('name' => 'StringTrim'))
            ));
    }
}
<?php

/**
 *
 * @author fabio.ventura
 */
namespace Accantona\Model;

// Add these import statements
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Variabile implements InputFilterAwareInterface
{

    public $segno;
    public $nome;
    public $valore;

    protected $inputFilter;

    // Add content to these methods:
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
       throw new \Exception('Not used');
    }

    public function exchangeArray($data)
    {
        $this->segno  = empty($data['segno']) ? null : $data['segno'];
        $this->nome   = empty($data['nome']) ? null : $data['nome'];
        $this->valore = empty($data['valore']) ? null : $data['valore'];
    }

    // Add the following method:
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name'     => 'segno',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'nome',
                'required' => true,
                'filters'  => array(
                     array('name' => 'StringTrim'),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'valore',
                'required' => true,
                'filters'  => array(
                     array('name' => 'StringTrim'),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}

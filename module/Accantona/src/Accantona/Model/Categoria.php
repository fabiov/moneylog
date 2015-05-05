<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Anagrafica
 *
 * @author fabio.ventura
 */
namespace Accantona\Model;

// Add these import statements
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Categoria implements InputFilterAwareInterface
{

    public $id;
    public $descrizione;

    protected $inputFilter;

    // Add content to these methods:
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
       throw new \Exception('Not used');
    }

    public function exchangeArray($data)
    {
        $this->id           = empty($data['id'])            ? null : $data['id'];
        $this->descrizione  = empty($data['descrizione'])   ? null : $data['descrizione'];
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
                'name'     => 'id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'descrizione',
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

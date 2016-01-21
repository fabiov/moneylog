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

class Spesa implements InputFilterAwareInterface
{

    public $id;
    public $id_categoria;
    public $valuta;
    public $importo;
    public $descrizione;
    public $categoryDescription;

    protected $inputFilter;

    // Add content to these methods:
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
       throw new \Exception('Not used');
    }

    public function exchangeArray($data)
    {
        $this->id                  = empty($data['id'])                  ? null : $data['id'];
        $this->valuta              = empty($data['valuta'])              ? null : $data['valuta'];
        $this->id_categoria        = empty($data['id_categoria'])        ? null : $data['id_categoria'];
        $this->importo             = empty($data['importo'])             ? null : $data['importo'];
        $this->descrizione         = empty($data['descrizione'])         ? null : $data['descrizione'];
        $this->categoryDescription = empty($data['categoryDescription']) ? null : $data['categoryDescription'];
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
                'name'     => 'id_categoria',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'valuta',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'Date',
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'importo',
                'required' => true,
                'validators' => array(
                    array(
                        'name'    => 'Regex',
                        'options' => array(
                            'pattern' => '/^[1-9]\d*(\.\d{1,2})?$/',
                        ),
                    ),
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

<?php

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

class Accantonato implements InputFilterAwareInterface
{

    public $id;
    public $id_categoria;
    public $valuta;
    public $importo;
    public $descrizione;
    public $categoryDescription;
    public $userId;

    protected $inputFilter;

    // Add content to these methods:
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
       throw new \Exception('Not used');
    }

    public function exchangeArray($data)
    {
        $this->id          = empty($data['id'])          ? null : $data['id'];
        $this->userId      = empty($data['userId'])      ? null : $data['userId'];
        $this->valuta      = empty($data['valuta'])      ? null : $data['valuta'];
        $this->importo     = empty($data['importo'])     ? null : $data['importo'];
        $this->descrizione = empty($data['descrizione']) ? null : $data['descrizione'];
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
                'name'     => 'valuta',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'Date',
                    ),
                ),
            ));

            $inputFilter->add(array('name' => 'importo', 'required' => true));

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

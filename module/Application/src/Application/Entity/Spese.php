<?php
namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * Setting.
 *
 * @ORM\Entity(repositoryClass="Application\Repository\SpeseRepository")
 * @ORM\Table(name="spese")
 * @property int $id
 * @property int $userId
 * @property DateTime $valuta
 * @property int $id_categoria
 * @property float $importo
 * @property string $descrizione
 * @property Category $category
 */
class Spese implements InputFilterAwareInterface
{

    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true});
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="userId", type="integer", options={"unsigned"=true})
     */
    protected $userId;

    /**
     * @ORM\Column(name="valuta", type="date")
     */
    protected $valuta;

    /**
     * @ORM\OneToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="id_categoria", referencedColumnName="id")
     */
    protected $category;

    /**
     * @ORM\Column(name="importo", type="float")
     */
    protected $importo;

    /**
     * @ORM\Column(name="descrizione", type="string")
     */
    protected $descrizione;

    /**
     * Magic getter to expose protected properties.
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->$property;
    }

    /**
     * Magic setter to save protected properties.
     *
     * @param string $property
     * @param mixed $value
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * Populate from an array.
     *
     * @param array $data
     * @return Setting
     */
    public function exchangeArray($data = array())
    {
        if (isset($data['id'])) {
            $this->id = $data['id'];
        }
        if (isset($data['userId'])) {
            $this->userId = $data['userId'];
        }
        $this->valuta       = isset($data['valuta'])       ? new \DateTime($data['valuta']) : null;
        $this->importo      = isset($data['importo'])      ? $data['importo']               : null;
        $this->descrizione  = isset($data['descrizione'])  ? $data['descrizione']           : null;
    }

    /**
     * @param InputFilterInterface $inputFilter
     * @return void|InputFilterAwareInterface
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /**
     * @return InputFilter
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $this->inputFilter = new InputFilter();

            $this->inputFilter->add(array(
                'name'     => 'accountId',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));

            $this->inputFilter->add(array(
                'name'     => 'valuta',
                'required' => true,
                'filters'  => array(),
            ));

            $this->inputFilter->add(array(
                'name'     => 'id_categoria',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));

            $this->inputFilter->add(array(
                'name'     => 'importo',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
            ));

            $this->inputFilter->add(array(
                'name'     => 'descrizione',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
            ));
        }
        return $this->inputFilter;
    }

}
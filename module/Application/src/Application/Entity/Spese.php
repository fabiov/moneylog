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
 * @ORM\Entity
 * @ORM\Table(name="spese")
 * @property int $id
 * @property int $userId
 * @property DateTime $valuta
 * @property int $id_categoria
 * @property float $importo
 * @property string $descrizione
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
     * @ORM\Column(name="id_categoria", type="integer", options={"unsigned"=true})
     */
    protected $id_categoria;

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
        $this->id_categoria = isset($data['id_categoria']) ? $data['id_categoria']          : null;
        $this->importo      = isset($data['importo'])      ? $data['importo']               : null;
        $this->descrizione  = isset($data['descrizione'])  ? $data['descrizione']           : null;
    }

    /**
     * Set input filter
     *
     * @param  InputFilterInterface $inputFilter
     * @return InputFilterAwareInterface
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
            $inputFilter = new InputFilter();

//            $inputFilter->add(array(
//                'name'     => 'id',
//                'required' => true,
//                'filters'  => array(
//                    array('name' => 'Int'),
//                ),
//            ));

//            $inputFilter->add(array(
//                'name'     => 'userId',
//                'required' => true,
//                'filters'  => array(
//                    array('name' => 'Int'),
//                ),
//            ));

            $inputFilter->add(array(
                'name'     => 'valuta',
                'required' => true,
                'filters'  => array(),
            ));

            $inputFilter->add(array(
                'name'     => 'id_categoria',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'importo',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StringTrim'),
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
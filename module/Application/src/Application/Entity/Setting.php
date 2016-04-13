<?php
namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * Setting.
 *
 * @ORM\Entity
 * @ORM\Table(name="Setting")
 * @property int $id
 * @property string $name
 * @property string $options
 */
class Setting implements InputFilterAwareInterface
{

    protected $decodedOptions;
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @ORM\Column(type="string")
     */
    protected $options;

    /**
     * Magic getter to expose protected properties.
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        if ($property == 'options') {
            if ($this->decodedOptions === null) {
                $this->decodedOptions = json_decode($this->options);
            }
            return $this->decodedOptions;
        }

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
     */
    public function exchangeArray($data = array())
    {
        $this->id = $data['id'];
        $this->artist = $data['artist'];
        $this->title = $data['title'];
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

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $inputFilter->add(array(
                'name' => 'id',
                'required' => true,
                'filters' => array(array('name' => 'Int'))
            ));
            $inputFilter->add(array(
                'name'     => 'name',
                'required' => true,
                'filters'  => array(array('name' => 'StringTrim')),
                'validators' => array(array('name' => 'StringLength', 'options' => array('encoding' => 'UTF-8'))),
            ));
            $inputFilter->add(array(
                'name'     => 'options',
                'required' => true,
                'filters'  => array(array('name' => 'StringTrim')),
                'validators' => array(array('name' => 'StringLength', 'options' => array('encoding' => 'UTF-8'))),
            ));

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

}
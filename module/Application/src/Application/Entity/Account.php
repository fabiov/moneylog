<?php
namespace Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilter;

/**
 * Setting.
 *
 * @ORM\Entity
 * @ORM\Table(name="Account")
 * @property int $id
 * @property int $userId
 * @property string $name
 * @property string $created
 * @property string $updated
 */
class Account implements InputFilterAwareInterface
{

    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true});
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    protected $userId;

    /**
     * @ORM\Column(type="string", length=255, unique=false, nullable=false)
     */
    protected $name;

    /**
     * @ORM\Column(name="created", type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(name="updated", type="datetime")
     */
    protected $updated;

    /**
     * @ORM\OneToMany(targetEntity="Moviment", mappedBy="account")
     */
    protected $moviments;

    public function __construct()
    {
        $this->moviments = new ArrayCollection();
    }

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

//    /**
//     * Magic setter to save protected properties.
//     *
//     * @param string $property
//     * @param mixed $value
//     */
//    public function __set($property, $value)
//    {
//        $this->$property = $value;
//    }

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
     * @return $this
     */
    public function exchangeArray(array $data = array())
    {
        if (isset($data['userId'])) {
            $this->userId =  $data['userId'];
        }
        $this->name = isset($data['name']) ? $data['name'] : null;
        return $this;
    }

    /**
     * Not used
     *
     * @param  InputFilterInterface $inputFilter
     * @throws \Exception
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
//            $inputFilter->add(array(
//                'name' => 'id',
//                'required' => true,
//                'filters' => array(array('name' => 'Int'))
//            ));
//            $inputFilter->add(array(
//                'name'     => 'userId',
//                'required' => true,
//                'filters' => array(array('name' => 'Int'))
//            ));
//            $inputFilter->add(array(
//                'name'     => 'settingId',
//                'required' => true,
//                'filters' => array(array('name' => 'Int'))
//            ));
//            $inputFilter->add(array(
//                'name'     => 'value',
//                'required' => true,
//                'filters'  => array(array('name' => 'StringTrim')),
//                'validators' => array(array('name' => 'StringLength', 'options' => array('encoding' => 'UTF-8'))),
//            ));
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

}
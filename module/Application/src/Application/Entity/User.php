<?php
namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * Setting.
 *
 * @ORM\Entity
 * @ORM\Table(name="UserSetting")
 * @property int $id
 * @property int $userId
 * @property int $settingId
 * @property string $value
 */
class User implements InputFilterAwareInterface
{

    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

//    /**
//     * @ORM\Column(type="integer")
//     */
//    protected $userId;
//
//    /**
//     * @ORM\Column(type="integer")
//     */
//    protected $settingId;
//
//    /**
//     * @ORM\Column(type="string")
//     */
//    protected $value;
//
//    /**
//     * @ORM\ManyToOne(targetEntity="Setting")
//     * @ORM\JoinColumn(name="settingId", referencedColumnName="id")
//     */
//    private $settings;
//
//    /**
//     * Magic getter to expose protected properties.
//     *
//     * @param string $property
//     * @return mixed
//     */
//    public function __get($property)
//    {
//        return $this->$property;
//    }
//
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
//
//    /**
//     * Convert the object to an array.
//     *
//     * @return array
//     */
//    public function getArrayCopy()
//    {
//        return get_object_vars($this);
//    }
//
//    /**
//     * Populate from an array.
//     *
//     * @param array $data
//     */
//    public function exchangeArray($data = array())
//    {
//        $this->id = $data['id'];
//        $this->artist = $data['artist'];
//        $this->title = $data['title'];
//    }

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
<?php
namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * User.
 *
 * @ORM\Entity
 * @ORM\Table(name="User")
 * @property int $id
 * @property string $email
 * @property string $name
 * @property string $surname
 * @property string $password
 * @property string $salt
 * @property int $status
 * @property string $role
 * @property string $registrationToken
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

    /**
     * @ORM\Column(type="string")
     */
    protected $email;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     */
    protected $surname;

    /**
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string")
     */
    private $salt;

    /**
     * @ORM\Column(type="integer")
     */
    private $status = 0;

    /**
     * @ORM\Column(type="string")
     */
    private $role;

    /**
     * @ORM\Column(type="string")
     */
    private $registrationToken;

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
     */
    public function exchangeArray(array $data = array())
    {
        $this->id                = isset($data['id'])                ? $data['id']                : null;
        $this->email             = isset($data['email'])             ? $data['email']             : null;
        $this->name              = isset($data['name'])              ? $data['name']              : null;
        $this->surname           = isset($data['name'])              ? $data['name']              : null;
        $this->password          = isset($data['password'])          ? $data['password']          : null;
        $this->salt              = isset($data['salt'])              ? $data['salt']              : null;
        $this->status            = isset($data['status'])            ? $data['status']            : null;
        $this->role              = isset($data['role'])              ? $data['role']              : null;
        $this->registrationToken = isset($data['registrationToken']) ? $data['registrationToken'] : null;
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

            $inputFilter->add(array(
                'name'     => 'email',
                'required' => true,
                'filters' => array(array('name' => 'StringTrim'))
            ));
            $inputFilter->add(array(
                'name' => 'name',
                'required' => true,
                'filters' => array(array('name' => 'StringTrim'))
            ));
            $inputFilter->add(array(
                'name' => 'surname',
                'required' => true,
                'filters' => array(array('name' => 'StringTrim'))
            ));

            $inputFilter->add(array(
                'name'     => 'password',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array('encoding' => 'UTF-8', 'min' => 1),
                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

}
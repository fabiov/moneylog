<?php
namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilter;
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
     * @ORM\Column(name="lastLogin", type="datetime", nullable=true)
     * @var \DateTime
     */
    private $lastLogin;

    /**
     * @ORM\Column(name="created", type="datetime", nullable=true)
     * @var \DateTime
     */
    private $created;

    /**
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     * @var \DateTime
     */
    private $updated;

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
        $this->id      = isset($data['id'])      ? $data['id']      : null;
        if (array_key_exists('email', $data)) {
            $this->email = $data['email'];
        }
        $this->name    = isset($data['name'])    ? $data['name']    : null;
        $this->surname = isset($data['surname']) ? $data['surname'] : null;
        if (array_key_exists('password', $data)) {
            $this->password = $data['password'];
        }
        if (array_key_exists('salt', $data)) {
            $this->salt = $data['salt'];
        }
        if (array_key_exists('status', $data)) {
            $this->status = $data['status'];
        }
        if (array_key_exists('role', $data)) {
            $this->role = $data['role'];
        }
        if (array_key_exists('registrationToken', $data)) {
            $this->registrationToken = $data['registrationToken'];
        }
    }

    /**
     * ATTENZIONE: filtri e forn devono avere esattamente gli stessi campi
     *
     * @param InputFilterInterface $inputFilter
     * @return $this
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        $this->inputFilter = $inputFilter;
        return $this;
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

    /**
     * @param \DateTime $date
     * @return $this
     */
    public function setLastLogin(\DateTime $date)
    {
        $this->lastLogin = $date;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }
}
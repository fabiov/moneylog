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
 * @property Setting $setting
 */
class User implements InputFilterAwareInterface
{
    const STATUS_NOT_CONFIRMED = 0;
    const STATUS_CONFIRMED = 1;

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
     * One user has One settings.
     *
     * @ORM\OneToOne(targetEntity="Setting")
     * @ORM\JoinColumn(name="id", referencedColumnName="userId")
     */
    private $setting;

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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return User
     */
    public function setId(int $id): User
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return User
     */
    public function setName(string $name): User
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getSurname(): string
    {
        return $this->surname;
    }

    /**
     * @param string $surname
     * @return User
     */
    public function setSurname(string $surname): User
    {
        $this->surname = $surname;
        return $this;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string $role
     * @return User
     */
    public function setRole(string $role): User
    {
        $this->role = $role;
        return $this;
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

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getSalt(): string
    {
        return $this->salt;
    }

    /**
     * @param string $salt
     * @return User
     */
    public function setSalt(string $salt): User
    {
        $this->salt = $salt;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return User
     */
    public function setStatus(int $status): User
    {
        $this->status = $status;
        return $this;
    }
}
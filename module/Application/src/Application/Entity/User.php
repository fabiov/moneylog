<?php

declare(strict_types=1);

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name = "user", uniqueConstraints = {@ORM\UniqueConstraint(name="email_idx", columns={"email"})})
 */
class User implements InputFilterAwareInterface
{
    public const STATUS_NOT_CONFIRMED = 0;
    public const STATUS_CONFIRMED = 1;

    /**
     * @var ?InputFilterInterface
     */
    private $inputFilter;

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true});
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @var string
     */
    private $surname;

    /**
     * @ORM\Column(type="string", nullable=false, length=32, options={"fixed" = true})
     * @var string
     */
    private $password;

    /**
     * @ORM\Column(type="string", nullable=false, length=4, options={"fixed" = true})
     * @var string
     */
    private $salt;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"unsigned"=true})
     * @var int
     */
    private $status = 0;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @var string
     */
    private $role;

    /**
     * @ORM\Column(type="string", nullable=false, length=8, options={"fixed" = true})
     * @var string
     */
    private $registrationToken;

    /**
     * @ORM\Column(name="lastLogin", nullable=true, type="datetime", nullable=true)
     * @var \DateTime
     */
    private $lastLogin;

    /**
     * One user has One settings.
     * @var Setting
     * @ORM\OneToOne(targetEntity="Setting", mappedBy="user")
     */
    private $setting;

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function getArrayCopy(): array
    {
        return get_object_vars($this);
    }

    /**
     * Populate from an array.
     *
     * @param array $data
     */
    public function exchangeArray(array $data = []): void
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->surname = $data['surname'] ?? null;

        if (array_key_exists('email', $data)) {
            $this->email = $data['email'];
        }
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
     * ATTENZIONE: filtri e form devono avere esattamente gli stessi campi
     *
     * @param InputFilterInterface $inputFilter
     * @return $this
     */
    public function setInputFilter(InputFilterInterface $inputFilter): self
    {
        $this->inputFilter = $inputFilter;
        return $this;
    }

    public function getInputFilter(): InputFilterInterface
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name'     => 'email',
                'required' => true,
                'filters' => [['name' => 'StringTrim']]
            ]);
            $inputFilter->add([
                'name' => 'name',
                'required' => true,
                'filters' => [['name' => 'StringTrim']]
            ]);
            $inputFilter->add([
                'name' => 'surname',
                'required' => true,
                'filters' => [['name' => 'StringTrim']]
            ]);
            $inputFilter->add([
                'name'     => 'password',
                'required' => true,
                'filters'  => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => ['encoding' => 'UTF-8', 'min' => 1],
                    ],
                ],
            ]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;
        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function setLastLogin(\DateTime $date): self
    {
        $this->lastLogin = $date;
        return $this;
    }

    public function getLastLogin(): \DateTime
    {
        return $this->lastLogin;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getSalt(): string
    {
        return $this->salt;
    }

    public function setSalt(string $salt): self
    {
        $this->salt = $salt;
        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getSetting(): Setting
    {
        return $this->setting;
    }

    public function setSetting(Setting $setting): self
    {
        $this->setting = $setting;
        return $this;
    }

    public function getRegistrationToken(): string
    {
        return $this->registrationToken;
    }
}

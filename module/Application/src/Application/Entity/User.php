<?php

declare(strict_types=1);

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterAwareInterface;
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

    public function exchangeArray(array $data = []): void
    {
        if (isset($data['name'])) {
            $this->setName($data['name']);
        }
        if (isset($data['surname'])) {
            $this->setSurname($data['surname']);
        }
        if (isset($data['email'])) {
            $this->setEmail($data['email']);
        }
        if (isset($data['password'])) {
            $this->setPassword($data['password']);
        }
        if (isset($data['salt'])) {
            $this->setSalt($data['salt']);
        }
        if (isset($data['status'])) {
            $this->setStatus((int) $data['status']);
        }
        if (isset($data['role'])) {
            $this->setRole($data['role']);
        }
        if (isset($data['registrationToken'])) {
            $this->registrationToken = $data['registrationToken'];
        }
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception('Not implemented');
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): void
    {
        $this->surname = $surname;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    public function setLastLogin(\DateTime $date): void
    {
        $this->lastLogin = $date;
    }

    public function getLastLogin(): \DateTime
    {
        return $this->lastLogin;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getSalt(): string
    {
        return $this->salt;
    }

    public function setSalt(string $salt): void
    {
        $this->salt = $salt;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        if (!in_array($status, [self::STATUS_NOT_CONFIRMED, self::STATUS_CONFIRMED])) {
            throw new \Exception("Invalid status: $status");
        }
        $this->status = $status;
    }

    public function getSetting(): Setting
    {
        return $this->setting;
    }

    public function setSetting(Setting $setting): void
    {
        $this->setting = $setting;
    }

    public function getRegistrationToken(): string
    {
        return $this->registrationToken;
    }
}

<?php

declare(strict_types=1);

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name = "user", uniqueConstraints = {@ORM\UniqueConstraint(name="email_idx", columns={"email"})})
 */
class User
{
    public const STATUS_NOT_CONFIRMED = 0;
    public const STATUS_CONFIRMED = 1;

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
     * @return array<string, mixed>
     */
    public function getArrayCopy(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'surname' => $this->surname,
            'password' => $this->password,
            'salt' => $this->salt,
            'status' => $this->status,
            'role' => $this->role,
            'registrationToken' => $this->registrationToken,
            'lastLogin' => $this->lastLogin,
            'setting' => $this->setting,
        ];
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
            throw new \InvalidArgumentException("Invalid status: $status");
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

    public function setRegistrationToken(string $registrationToken): void
    {
        $this->registrationToken = $registrationToken;
    }
}

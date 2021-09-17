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
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string")
     */
    private string $email;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private string $surname;

    /**
     * @ORM\Column(type="string", nullable=false, length=32, options={"fixed" = true})
     */
    private string $password;

    /**
     * @ORM\Column(type="string", nullable=false, length=4, options={"fixed" = true})
     */
    private string $salt;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"unsigned"=true})
     */
    private int $status = 0;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private string $role;

    /**
     * @ORM\Column(type="string", nullable=false, length=8, options={"fixed" = true})
     */
    private string $registrationToken;

    /**
     * @ORM\Column(name="lastLogin", nullable=true, type="datetime", nullable=true)
     */
    private \DateTime $lastLogin;

    /**
     * One user has One setting.
     * @ORM\OneToOne(targetEntity="Setting", mappedBy="user")
     */
    private Setting $setting;

    public function __construct(
        string $email,
        string $name,
        string $surname,
        string $password,
        string $salt,
        int $status,
        string $role,
        string $registrationToken
    ) {
        $this->email = $email;
        $this->name = $name;
        $this->surname = $surname;
        $this->password = $password;
        $this->salt = $salt;
        $this->status = $status;
        $this->role = $role;
        $this->registrationToken = $registrationToken;
    }

    public function getId(): ?int
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

<?php

declare(strict_types=1);

namespace Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Application\Repository\AccountRepository")
 * @ORM\Table(name="account")
 */
class Account
{
    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_HIGHLIGHT = 'highlight';

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true});
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id;

    /**
     * Many accounts have one user. This is the owning side.
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id", nullable=false)
     */
    private User $user;

    /**
     * @ORM\Column(type="string", length=255, unique=false, nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('closed', 'open', 'highlight') NOT NULL DEFAULT 'open'")
     */
    private string $status;

    /**
     * @ORM\OneToMany(targetEntity="Movement", mappedBy="account")
     */
    private Collection $movements;

    public function __construct(User $user, string $name, string $status = self::STATUS_OPEN)
    {
        $this->user = $user;
        $this->name = $name;
        $this->status = $status;
        $this->movements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getMovements(): Collection
    {
        return $this->movements;
    }
}

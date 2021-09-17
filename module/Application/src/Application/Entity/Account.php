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
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $recap;

    /**
     * @ORM\Column(name="closed", type="boolean", nullable=false, options={"default": false})
     */
    private bool $closed;

    /**
     * @ORM\OneToMany(targetEntity="Movement", mappedBy="account")
     */
    private Collection $movements;

    public function __construct(User $user, string $name, int $recap = 0, bool $closed = false)
    {
        $this->user = $user;
        $this->name = $name;
        $this->recap = $recap;
        $this->closed = $closed;
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

    public function isClosed(): bool
    {
        return $this->closed;
    }

    public function setClosed(bool $closed): void
    {
        $this->closed = $closed;
    }

    public function getRecap(): int
    {
        return $this->recap;
    }

    public function setRecap(int $recap): void
    {
        $this->recap = $recap;
    }
}

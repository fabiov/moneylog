<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Application\Repository\ProvisionRepository")
 * @ORM\Table(name="provision")
 */
class Provision
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true});
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id;

    /**
     * Many provisions have one user. This is the owning side.
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id", nullable=false)
     */
    private User $user;

    /**
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private \DateTime $date;

    /**
     * @ORM\Column(name="amount", type="decimal", precision=8, scale=2, nullable=false)
     */
    private float $amount;

    /**
     * @ORM\Column(name="description", type="string", nullable=false)
     */
    private string $description;

    public function __construct(User $user, \DateTime $date, float $amount, string $description)
    {
        $this->user = $user;
        $this->date = $date;
        $this->amount = $amount;
        $this->description = $description;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}

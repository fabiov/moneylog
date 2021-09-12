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
     * @var int
     */
    private $id;

    /**
     * Many provisions have one user. This is the owning side.
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id", nullable=false)
     * @var User
     */
    private $user;

    /**
     * @ORM\Column(name="date", type="date", nullable=false)
     * @var \DateTime
     */
    private $date;

    /**
     * @ORM\Column(name="amount", type="decimal", precision=8, scale=2, nullable=false)
     * @var float
     */
    private $amount;

    /**
     * @ORM\Column(name="description", type="string", nullable=false)
     * @var string
     */
    private $description;

    public function getId(): int
    {
        return $this->id;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
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

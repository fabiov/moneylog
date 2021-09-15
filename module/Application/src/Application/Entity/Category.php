<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Application\Repository\CategoryRepository")
 * @ORM\Table(name="category")
 */
class Category
{
    public const STATUS_INACTIVE = 0;
    public const STATUS_ACTIVE = 1;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true});
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id;

    /**
     * Many categories have one user. This is the owning side.
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id", nullable=false)
     */
    private User $user;

    /**
     * @ORM\Column(name="description", type="string", nullable=false)
     */
    private string $description;

    /**
     * @ORM\Column(name="status", type="smallint", nullable=false)
     */
    private int $status = 1;

    public function __construct(User $user, string $description, int $status)
    {
        $this->user = $user;
        $this->description = $description;
        $this->status = $status;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        if (!in_array($status, [self::STATUS_INACTIVE, self::STATUS_ACTIVE])) {
            throw new \RuntimeException("Invalid status value: $status");
        }
        $this->status = $status;
    }
}

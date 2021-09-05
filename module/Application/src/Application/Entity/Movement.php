<?php

declare(strict_types=1);

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Application\Repository\MovementRepository")
 * @ORM\Table(name="movement")
 */
class Movement
{
    public const IN = 1;
    public const OUT = -1;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true});
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id;

    /**
     * Many movements have one account. This is the owning side.
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="movements")
     * @ORM\JoinColumn(name="accountId", referencedColumnName="id", nullable=false)
     */
    private Account $account;

    /**
     * Many movements have one category. This is the owning side.
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="movements")
     * @ORM\JoinColumn(name="categoryId", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private ?Category $category;

    /**
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private \DateTime $date;

    /**
     * @ORM\Column(name="amount", type="float", nullable=false)
     */
    private float $amount;

    /**
     * @ORM\Column(name="description", type="string", nullable=false)
     */
    private string $description;

    public function __construct(
        Account $account,
        float $amount,
        \DateTime $date,
        string $description,
        ?Category $category = null
    ) {
        $this->account = $account;
        $this->amount = $amount;
        $this->date = $date;
        $this->description = $description;
        $this->category = $category;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }
    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): void
    {
        $this->category = $category;
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

    /**
     * @return array<string, mixed>
     */
    public function getArrayCopy(): array
    {
        return [
            'id' => $this->id,
            'account' => $this->amount,
            'category' => $this->category,
            'date' => $this->date,
            'amount' => $this->amount,
            'description' => $this->description,
        ];
    }
}

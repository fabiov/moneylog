<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;
use MoneyLog\Form\Filter\MovementFilter;

/**
 *
 * @ORM\Entity(repositoryClass="Application\Repository\MovementRepository")
 * @ORM\Table(name="movement")
 */
class Movement implements InputFilterAwareInterface
{
    public const IN = 1;
    public const OUT = -1;

    /**
     * @var ?InputFilterInterface
     */
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true});
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * Many movements have one account. This is the owning side.
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="movements")
     * @ORM\JoinColumn(name="accountId", referencedColumnName="id", nullable=false)
     * @var Account
     */
    private $account;

    /**
     * Many movements have one category. This is the owning side.
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="movements")
     * @ORM\JoinColumn(name="categoryId", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * @var ?Category
     */
    private $category;

    /**
     * @ORM\Column(name="date", type="date", nullable=false)
     * @var \DateTime
     */
    private $date;

    /**
     * @ORM\Column(name="amount", type="float", nullable=false)
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

    public function getArrayCopy(): array
    {
        return get_object_vars($this);
    }

    public function exchangeArray(array $data): void
    {
        if (isset($data['accountId'])) {
            $this->account = $data['accountId'];
        }
        if (isset($data['category'])) {
            $this->category = $data['category'];
        }
        if (isset($data['date'])) {
            $this->date = new \DateTime($data['date']);
        }
        if (isset($data['amount'])) {
            $this->setAmount($this->amount = $data['amount']);
        }
        if (isset($data['description'])) {
            $this->setDescription($data['description']);
        }
    }

    public function setInputFilter(InputFilterInterface $inputFilter): void
    {
        throw new \Exception('Not used');
    }

    public function getInputFilter(): InputFilterInterface
    {
        if (!$this->inputFilter) {
            $this->inputFilter = new MovementFilter();
        }
        return $this->inputFilter;
    }
}

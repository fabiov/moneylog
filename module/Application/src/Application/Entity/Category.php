<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity(repositoryClass="Application\Repository\CategoryRepository")
 * @ORM\Table(name="category")
 */
class Category implements InputFilterAwareInterface
{
    public const STATUS_INACTIVE = 0;
    public const STATUS_ACTIVE = 1;

    /**
     * @var ?InputFilterInterface
     */
    private $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true});
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * Many categories have one user. This is the owning side.
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id", nullable=false)
     * @var User
     */
    private $user;

    /**
     * @ORM\Column(name="description", type="string", nullable=false)
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(name="status", type="smallint", nullable=false)
     * @var int
     */
    private $status = 1;

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
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

    public function getArrayCopy(): array
    {
        return get_object_vars($this);
    }

    public function exchangeArray(array $data = []): void
    {
        if (isset($data['user'])) {
            $this->user = $data['user'];
        }
        if (isset($data['description'])) {
            $this->setDescription($data['description']);
        }
        if (isset($data['status'])) {
            $this->setStatus((int) $data['status']);
        }
    }

    /**
     * @param InputFilterInterface $inputFilter
     * @return $this
     */
    public function setInputFilter(InputFilterInterface $inputFilter): Category
    {
        $this->inputFilter = $inputFilter;
        return $this;
    }

    public function getInputFilter(): InputFilterInterface
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name' => 'description',
                'required' => true,
                'filters'  => [['name' => 'StringTrim']],
            ]);
            $inputFilter->add([
                'name'     => 'status',
                'filters'  => [['name' => 'Int']],
            ]);

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}

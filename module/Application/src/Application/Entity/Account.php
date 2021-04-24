<?php

declare(strict_types=1);

namespace Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\InputFilter\InputFilter;

/**
 * Setting.
 *
 * @ORM\Entity(repositoryClass="Application\Repository\AccountRepository")
 * @ORM\Table(name="account")
 */
class Account implements InputFilterAwareInterface
{

    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true});
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * Many accounts have one user. This is the owning side.
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     * @var User
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, unique=false, nullable=false)
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $recap = 0;

    /**
     * @ORM\Column(name="closed", type="boolean", nullable=false, options={"default": false})
     */
    protected $closed = false;

    /**
     * @ORM\OneToMany(targetEntity="Movement", mappedBy="account")
     */
    protected $movements;

    public function __construct()
    {
        $this->movements = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function isClosed(): bool
    {
        return $this->closed;
    }

    public function getArrayCopy(): array
    {
        return get_object_vars($this);
    }

    /**
     * Populate from an array.
     *
     * @param array $data
     * @return $this
     */
    public function exchangeArray(array $data = []): Account
    {
        if (isset($data['user'])) {
            $this->user = $data['user'];
        }

        if (isset($data['name'])) {
            $this->name = $data['name'];
        }

        if (isset($data['recap'])) {
            $this->recap = $data['recap'];
        }

        if (isset($data['closed'])) {
            $this->closed = (bool) $data['closed'];
        }

        return $this;
    }

    /**
     * Not used
     *
     * @param  InputFilterInterface $inputFilter
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $this->inputFilter = new InputFilter();
        }
        return $this->inputFilter;
    }

}

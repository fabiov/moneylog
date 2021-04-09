<?php

declare(strict_types=1);

namespace Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilter;

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
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @var int
     */
    private $userId;

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

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function isClosed(): bool
    {
        return $this->closed;
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function getArrayCopy()
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
        if (isset($data['userId'])) {
            $this->userId =  $data['userId'];
        }

        if (isset($data['name'])) {
            $this->name = $data['name'];
        }

        if (isset($data['recap'])) {
            $this->recap =  $data['recap'];
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

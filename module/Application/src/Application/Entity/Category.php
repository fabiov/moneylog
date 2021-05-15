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
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     * @var User
     */
    private $user;

    /**
     * @ORM\Column(name="descrizione", type="string")
     * @var string
     */
    private $descrizione;

    /**
     * @ORM\Column(name="status", type="integer")
     * @var int
     */
    private $status = 1;

    public function getId(): int
    {
        return $this->id;
    }

    public function getDescrizione(): string
    {
        return $this->descrizione;
    }

    public function setDescrizione(string $descrizione): void
    {
        $this->descrizione = $descrizione;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Convert the object to an array.
     */
    public function getArrayCopy(): array
    {
        return get_object_vars($this);
    }

    /**
     * Populate from an array.
     *
     * @param array $data
     */
    public function exchangeArray(array $data = []): void
    {
        if (isset($data['user'])) {
            $this->user = $data['user'];
        }
        $this->descrizione = $data['descrizione'] ?? null;
        $this->status      = empty($data['status']) ? 0 : 1;
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
                'name'     => 'descrizione',
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

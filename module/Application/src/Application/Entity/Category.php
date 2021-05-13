<?php

namespace Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true});
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(name="userId", type="integer", options={"unsigned"=true})
     * @var int
     */
    protected $userId;

    /**
     * @ORM\Column(name="descrizione", type="string")
     * @var string
     */
    protected $descrizione;

    /**
     * @ORM\Column(name="status", type="integer")
     * @var int
     */
    protected $status = 1;

    /**
     * @ORM\Column(name="created", type="datetime", nullable=true, options={"default": "CURRENT_TIMESTAMP"})
     * @var \DateTime
     */
    protected $created;

    /**
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $updated;

    /**
     * One category has many movements. This is the inverse side.
     * @ORM\OneToMany(targetEntity="Movement", mappedBy="category")
     * @var ArrayCollection<int, Movement>
     */
    private $movements;

    public function __construct()
    {
        $this->movements = new ArrayCollection();
    }

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
        if (isset($data['userId'])) {
            $this->userId = $data['userId'];
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

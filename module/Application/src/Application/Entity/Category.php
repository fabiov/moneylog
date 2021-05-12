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
 * @property int $id
 * @property int $status
 * @property int $userId
 * @property string $created
 * @property string $descrizione
 * @property string $updated
 */
class Category implements InputFilterAwareInterface
{
    public const STATUS_INACTIVE = 0;
    public const STATUS_ACTIVE = 1;

    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true});
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="userId", type="integer", options={"unsigned"=true})
     */
    protected $userId;

    /**
     * @ORM\Column(name="descrizione", type="string")
     */
    protected $descrizione;

    /**
     * @ORM\Column(name="status", type="integer")
     */
    protected $status = 1;

    /**
     * @ORM\Column(name="created", type="datetime", nullable=true, options={"default": "CURRENT_TIMESTAMP"})
     */
    protected $created;

    /**
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    protected $updated;

    /**
     * One category has many movements. This is the inverse side.
     * @ORM\OneToMany(targetEntity="Movement", mappedBy="category")
     */
    private $movements;

    public function __construct()
    {
        $this->movements = new ArrayCollection();
    }

    /**
     * Magic getter to expose protected properties.
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->$property;
    }

    /**
     * Magic setter to save protected properties.
     *
     * @param string $property
     * @param mixed $value
     */
    public function __set(string $property, $value)
    {
        $this->$property = $value;
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
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        $this->inputFilter = $inputFilter;
        return $this;
    }

    /**
     * @return InputFilter
     */
    public function getInputFilter()
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

    /**
     * @return string
     */
    public function getDescrizione()
    {
        return $this->descrizione;
    }

    /**
     * @param string $descrizione
     */
    public function setDescrizione($descrizione)
    {
        $this->descrizione = $descrizione;
    }
}

<?php
namespace Application\Entity;

use MoneyLog\Form\Filter\MovementFilter;
use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilter;

/**
 *
 * @ORM\Entity(repositoryClass="Application\Repository\MovementRepository")
 * @ORM\Table(name="movement")
 * @property int $id
 * @property int $accountId
 * @property DateTime $date
 * @property float $amount
 * @property string $description
 *
 * Relations:
 * @property \Application\Entity\Account $account
 * @property \Application\Entity\Category $category
 */
class Movement implements InputFilterAwareInterface
{
    const IN = 1;
    const OUT = -1;

    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true});
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="accountId", type="integer", options={"unsigned"=true})
     */
    protected $accountId;

    /**
     * @ORM\Column(name="date", type="date")
     */
    protected $date;

    /**
     * @ORM\Column(name="amount", type="float")
     */
    protected $amount;

    /**
     * @ORM\Column(name="description", type="string")
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="movements")
     * @ORM\JoinColumn(name="accountId", referencedColumnName="id")
     */
    protected $account;

    /**
     * @ORM\OneToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="categoryId", referencedColumnName="id")
     */
    protected $category;

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
    public function __set($property, $value)
    {
        $this->$property = $value;
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
     * Convert the object to an array.
     *
     * @return array
     */
    public function exchangeArray($data)
    {
        if (isset($data['id'])) {
            $this->id = $data['id'];
        }
        if (isset($data['accountId'])) {
            $this->accountId = $data['accountId'];
        }
        if (array_key_exists('category', $data)) {
            $this->category = $data['category'];
        }
        $this->date        = isset($data['date'])        ? new \DateTime($data['date']) : null;
        $this->amount      = isset($data['amount'])      ? $data['amount']              : null;
        $this->description = isset($data['description']) ? $data['description']         : null;
    }

    /**
     * Set input filter
     *
     * @param  InputFilterInterface $inputFilter
     * @return InputFilterAwareInterface
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
            $this->inputFilter = new MovementFilter();
        }
        return $this->inputFilter;
    }
}
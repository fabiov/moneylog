<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Laminas\Filter\ToInt;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="setting")
 */
class Setting implements InputFilterAwareInterface
{
    /**
     * @var ?InputFilterInterface
     */
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     * @var User
     */
    protected $user;

    /**
     * @ORM\Column(name="payDay", type="smallint", nullable=false, options={"unsigned"=true, "default"=1})
     * @var int
     */
    protected $payDay = 1;

    /**
     * @ORM\Column(name="monthsRetrospective", type="smallint", nullable=false, options={"unsigned"=true, "default"=12})
     * @var int
     */
    protected $monthsRetrospective = 12;

    /**
     * @ORM\Column(name="`stored`", type="boolean", nullable=false, options={"default"=false})
     * @var boolean
     */
    protected $stored = false;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getPayDay(): int
    {
        return $this->payDay;
    }

    public function setPayDay(int $payDay): void
    {
        if ($payDay < 1 || $payDay > 28) {
            throw new \RuntimeException("Invalid payDay value: $payDay");
        }
        $this->payDay = $payDay;
    }

    public function getMonthsRetrospective(): int
    {
        return $this->monthsRetrospective;
    }

    public function setMonthsRetrospective(int $monthsRetrospective): void
    {
        $this->monthsRetrospective = $monthsRetrospective;
    }

    public function hasStored(): bool
    {
        return $this->stored;
    }

    public function setStored(bool $stored): void
    {
        $this->stored = $stored;
    }

    public function getArrayCopy(): array
    {
        return get_object_vars($this);
    }

    /**
     * Populate from an array.
     *
     * @param array $data
     * @return Setting
     */
    public function exchangeArray(array $data): self
    {
        if (isset($data['payDay'])) {
            $this->setPayDay((int) $data['payDay']);
        }
        if (isset($data['monthsRetrospective'])) {
            $this->setMonthsRetrospective((int) $data['monthsRetrospective']);
        }
        if (isset($data['stored'])) {
            $this->setStored((bool) $data['stored']);
        }
        return $this;
    }

    /**
     * Set input filter
     * @param \Laminas\InputFilter\InputFilterInterface $inputFilter
     * @return $this
     */
    public function setInputFilter(InputFilterInterface $inputFilter): self
    {
        $this->inputFilter = $inputFilter;
        return $this;
    }

    public function getInputFilter(): InputFilterInterface
    {
        if (!$this->inputFilter) {
            $this->inputFilter = new InputFilter();
            $this->inputFilter->add([
                'filters'  => [['name' => ToInt::class]],
                'name'     => 'payDay',
                'required' => true,
            ]);
            $this->inputFilter->add([
                'filters'  => [['name' => ToInt::class]],
                'name'     => 'monthsRetrospective',
                'required' => true,
            ]);
            $this->inputFilter->add([
                'filters'  => [['name' => ToInt::class]],
                'name'     => 'stored',
                'required' => true,
            ]);
        }
        return $this->inputFilter;
    }
}

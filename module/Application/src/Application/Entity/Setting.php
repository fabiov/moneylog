<?php

declare(strict_types=1);

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
     * @ORM\Column(name="paypay", type="smallint", nullable=false, options={"unsigned"=true, "default"=1})
     * @var int
     */
    protected $payday = 1;

    /**
     * @ORM\Column(name="months", type="smallint", nullable=false, options={"unsigned"=true, "default"=12})
     * @var int
     */
    protected $months = 12;

    /**
     * @ORM\Column(name="provisioning", type="boolean", nullable=false, options={"default"=false})
     * @var boolean
     */
    protected $provisioning = false;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getPayday(): int
    {
        return $this->payday;
    }

    public function setPayday(int $payday): void
    {
        if ($payday < 1 || $payday > 28) {
            throw new \InvalidArgumentException("Invalid payDay value: $payday");
        }
        $this->payday = $payday;
    }

    public function getMonths(): int
    {
        return $this->months;
    }

    public function setMonths(int $months): void
    {
        $this->months = $months;
    }

    public function hasProvisioning(): bool
    {
        return $this->provisioning;
    }

    public function setProvisioning(bool $provisioning): void
    {
        $this->provisioning = $provisioning;
    }

    public function getArrayCopy(): array
    {
        return [
            'user' => $this->user,
            'payday' => $this->payday,
            'months' => $this->months,
            'provisioning' => $this->provisioning,
        ];
    }

    public function exchangeArray(array $data): void
    {
        if (isset($data['payday'])) {
            $this->setPayday((int) $data['payday']);
        }
        if (isset($data['months'])) {
            $this->setMonths((int) $data['months']);
        }
        if (isset($data['provisioning'])) {
            $this->setProvisioning((bool) $data['provisioning']);
        }
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception('Not used');
    }

    public function getInputFilter(): InputFilterInterface
    {
        if (!$this->inputFilter) {
            $this->inputFilter = new InputFilter();
            $this->inputFilter->add([
                'filters' => [['name' => ToInt::class]],
                'name' => 'payday',
                'required' => true,
            ]);
            $this->inputFilter->add([
                'filters' => [['name' => ToInt::class]],
                'name' => 'months',
                'required' => true,
            ]);
            $this->inputFilter->add([
                'filters' => [['name' => ToInt::class]],
                'name' => 'provisioning',
                'required' => true,
            ]);
        }
        return $this->inputFilter;
    }
}

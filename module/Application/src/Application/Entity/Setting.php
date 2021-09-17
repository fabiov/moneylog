<?php

declare(strict_types=1);

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="setting")
 */
class Setting
{
    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    protected User $user;

    /**
     * @ORM\Column(name="paypay", type="smallint", nullable=false, options={"unsigned"=true, "default"=1})
     */
    protected int $payday = 1;

    /**
     * @ORM\Column(name="months", type="smallint", nullable=false, options={"unsigned"=true, "default"=12})
     */
    protected int $months;

    /**
     * @ORM\Column(name="provisioning", type="boolean", nullable=false, options={"default"=false})
     */
    protected bool $provisioning;

    public function __construct(User $user, int $payday = 1, int $months = 12, bool $provisioning = false)
    {
        $this->user = $user;
        $this->payday = $payday;
        $this->months = $months;
        $this->provisioning = $provisioning;
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
}

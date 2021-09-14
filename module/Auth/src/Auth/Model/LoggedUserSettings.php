<?php

declare(strict_types=1);

namespace Auth\Model;

class LoggedUserSettings
{
    private int $payday;
    private int $months;
    private bool $provisioning;

    public function __construct(int $payday, int $months, bool $provisioning)
    {
        $this->payday = $payday;
        $this->months = $months;
        $this->provisioning = $provisioning;
    }

    public function getPayday(): int
    {
        return $this->payday;
    }

    public function getMonths(): int
    {
        return $this->months;
    }

    public function hasProvisioning(): bool
    {
        return $this->provisioning;
    }
}

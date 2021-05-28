<?php

namespace Application\ViewHelper;

use Laminas\View\Helper\AbstractHelper;

class UserData extends AbstractHelper
{
    /**
     * @var ?\stdClass
     */
    private $identity;

    public function __invoke(): self
    {
        if (!$this->identity) {
            $this->identity = $this->view->identity();
        }
        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->identity ? $this->identity->name . ' ' . $this->identity->surname : null;
    }

    public function hasStored(): ?bool
    {
        return $this->identity ? (bool) $this->identity->setting->hasProvisioning() : null;
    }
}

<?php

namespace Application\ViewHelper;

use Auth\Model\LoggedUser;
use Laminas\View\Helper\AbstractHelper;

class UserData extends AbstractHelper
{
    private ?LoggedUser $identity;

    public function __construct()
    {
        $this->identity = null;
    }

    public function __invoke(): self
    {
        if (!$this->identity) {
            $this->identity = $this->view->identity();
        }
        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->identity ? $this->identity->getName() . ' ' . $this->identity->getSurname() : null;
    }

    public function hasStored(): ?bool
    {
        return $this->identity ? $this->identity->getSettings()->hasProvisioning() : null;
    }
}

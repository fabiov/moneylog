<?php
namespace Application\ViewHelper;

use Zend\View\Helper\AbstractHelper;

class UserData extends AbstractHelper
{
    /**
     * @var \Auth\Service\UserData
     */
    private $data;

    public function __invoke(): self
    {
        if (!$this->data) {
            $this->data = new \Auth\Service\UserData();
        }
        return $this;
    }

    public function getFullName(): string
    {
        return $this->data->getName() . ' ' . $this->data->getSurname();
    }

    public function hasStored(): bool
    {
        return (bool) $this->data->getSettings()['stored'];
    }
}

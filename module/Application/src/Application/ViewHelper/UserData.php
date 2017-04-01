<?php
namespace Application\ViewHelper;

use Zend\View\Helper\AbstractHelper;

class UserData extends AbstractHelper
{
    /**
     * @var \Auth\Service\UserData
     */
    private $data;

    /**
     * @return $this
     */
    public function __invoke()
    {

        if (!$this->data) {
            $this->data = new \Auth\Service\UserData('user_data');
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->data->getName() . ' ' . $this->data->getSurname();
    }

    /**
     * @return bool
     */
    public function hasStored()
    {
        return (bool) $this->data->getSettings()['stored'];
    }
}
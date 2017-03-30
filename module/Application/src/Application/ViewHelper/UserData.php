<?php
namespace Application\ViewHelper;

use Zend\Session\Container;
use Zend\View\Helper\AbstractHelper;

class UserData extends AbstractHelper
{
    private $data;

    /**
     * @return $this
     */
    public function __invoke()
    {

        if (!$this->data) {
            $this->data = new Container('user_data');
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->data->name . ' ' . $this->data->surname;
    }

    /**
     * @return bool
     */
    public function hasStored()
    {
        return (bool) $this->data->stored;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: fabio
 * Date: 31/03/17
 * Time: 21.22
 */

namespace Auth\Service;

use Application\Entity\Setting;
use Zend\Session\Container;

/**
 * Class UserData
 * @package Auth\Service
 */
class UserData
{
    /**
     * @var Container
     */
    private $data;

    /**
     * UserData constructor.
     */
    public function __construct()
    {
        $this->data = new Container('user_data');
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->data->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->data->name;
    }

    /**
    * @param string $surname
    * @return $this
    */
    public function setSurname($surname)
    {
        $this->data->surname = $surname;
        return $this;
    }

    /**
     * @return string
     */
    public function getSurname()
    {
        return $this->data->surname;
    }

    /**
     * @param Setting $settings
     * @return $this
     */
    public function setSettings(Setting $settings)
    {
        $this->data->settings = [
            'payDay'              => $settings->payDay,
            'monthsRetrospective' => $settings->monthsRetrospective,
            'stored'              => $settings->stored,
        ];
        return $this;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->data->settings;
    }
}
<?php
namespace Auth\Service;

use Application\Entity\Setting;
use Laminas\Session\Container;

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

    public function setName(string $name): self
    {
        $this->data->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->data->name;
    }

    public function setSurname(string $surname): self
    {
        $this->data->surname = $surname;
        return $this;
    }

    public function getSurname(): string
    {
        return $this->data->surname;
    }

    public function setSettings(Setting $settings): self
    {
        $this->data->settings = [
            'payDay'              => $settings->payDay,
            'monthsRetrospective' => $settings->monthsRetrospective,
            'stored'              => $settings->stored,
        ];
        return $this;
    }

    public function getSettings(): array
    {
        return $this->data->settings;
    }
}

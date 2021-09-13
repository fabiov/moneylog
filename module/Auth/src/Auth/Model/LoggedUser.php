<?php

declare(strict_types=1);

namespace Auth\Model;

class LoggedUser
{
    private int $id;
    private string $name;
    private string $surname;
    private string $email;
    private string $role;
    private LoggedUserSettings $settings;

    public function __construct(
        int $id,
        string $name,
        string $surname,
        string $email,
        string $role,
        LoggedUserSettings $settings
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->surname = $surname;
        $this->email = $email;
        $this->role = $role;
        $this->settings = $settings;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getSettings(): LoggedUserSettings
    {
        return $this->settings;
    }
}

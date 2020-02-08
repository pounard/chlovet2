<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

final class User implements UserInterface
{
    const ROLE_CHEF = 'ROLE_CHEF';

    private string $username;
    private ?string $password = null;
    private ?string $salt = null;

    public function __construct(string $username, ?string $password = null, ?string $salt = null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->salt = $salt;
    }

    public function getRoles(): array
    {
        return [self::ROLE_CHEF];
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function eraseCredentials(): void
    {
        $this->password = null;
        $this->salt = null;
    }
}

<?php

declare(strict_types=1);

namespace App\Security;

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class FormClientUser implements UserInterface
{
    private string $emailAddress;
    private UuidInterface $clientId;

    public function __construct(string $emailAddress, UuidInterface $clientId)
    {
        $this->emailAddress = $emailAddress;
        $this->clientId = $clientId;
    }

    public function getPassword()
    {
        return null;
    }

    public function eraseCredentials()
    {
    }

    public function getSalt()
    {
        return null;
    }

    public function getRoles()
    {
        return ['ROLE_CLIENT'];
    }

    public function getUsername()
    {
        return $this->emailAddress;
    }

    public function getClientId(): UuidInterface
    {
        return $this->clientId;
    }
}

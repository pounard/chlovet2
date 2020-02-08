<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * We need one, this the most basic, stupid one.
 */
final class ChefUserProvider implements UserProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function supportsClass(string $class)
    {
        return User::class === $class;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        return new User($user->getUsername(), $user->getPassword(), $user->getSalt());
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername(string $username)
    {
        return new User($username);
    }
}

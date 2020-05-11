<?php

declare(strict_types=1);

namespace App\Security;

use Goat\Runner\Runner;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Provide users from their token.
 */
final class FormClientUserProvider implements UserProviderInterface, FormClientTokenRepository
{
    private Runner $runner;

    public function __construct(Runner $runner)
    {
        $this->runner = $runner;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass(string $class)
    {
        return FormClientUser::class === $class;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        return new FormClientUser($user->getUsername());
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername(string $username)
    {
        $exists = $this
            ->runner
            ->execute(
                <<<SQL
                SELECT 1 FROM "client_login" WHERE "email" = ?
                SQL,
                [$username]
            )
            ->fetchField()
        ;

        if (!$exists) {
            throw new UsernameNotFoundException();
        }

        return new FormClientUser($username);
    }

    /**
     * {@inheritdoc}
     */
    public function findEmailAddressForToken(string $token): ?string
    {
        return $this
            ->runner
            ->execute(
                <<<SQL
                SELECT "email" FROM "client_login" WHERE "token" = ?
                SQL,
                [$token]
            )
            ->fetchField()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findTargetForToken(string $token): ?string
    {
        return $this
            ->runner
            ->execute(
                <<<SQL
                SELECT "target" FROM "client_login" WHERE "token" = ?
                SQL,
                [$token]
            )
            ->fetchField()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $emailAddress, ?string $target = null): string
    {
        $exists = false;
        do {
            $token = \bin2hex(\random_bytes(24));

            $exists = $this
                ->runner
                ->execute(
                    <<<SQL
                    SELECT 1 FROM "client_login" WHERE "token" = ?
                    SQL,
                    [$token]
                )
                ->fetchField()
            ;
        } while ($exists);

        $this
            ->runner
            ->getQueryBuilder()
            ->insert('client_login')
            ->values([
                'email' => $emailAddress,
                'target' => $target ?? 'default',
                'token' => $token,
            ])
            ->perform()
        ;

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function touch(string $token): bool
    {
        return 0 < $this
            ->runner
            ->perform(
                <<<SQL
                UPDATE "client_login"
                SET
                    "login_count" = "login_count" + 1,
                    "login_first" = COALESCE("login_first", current_timestamp),
                    "login_last" = current_timestamp
                WHERE
                    "token" = ?
                    AND "valid_until" > current_timestamp
                SQL,
                [$token]
            )
        ;
    }
}

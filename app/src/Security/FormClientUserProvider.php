<?php

declare(strict_types=1);

namespace App\Security;

use Goat\Runner\Runner;
use Ramsey\Uuid\Uuid;
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
        \assert($user instanceof FormClientUser);

        return new FormClientUser($user->getUsername(), $user->getClientId());
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername(string $username)
    {
        return $this
            ->runner
            ->execute(
                <<<SQL
                SELECT id FROM client WHERE email = ?
                SQL,
                [$username]
            )
            ->setHydrator(fn ($row) => new FormClientUser($username, $row['id'] ?? Uuid::uuid4()))
            ->fetch() ?? $this->usernameNotFoundError()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByToken(string $token): FormClientUser
    {
        return $this
            ->runner
            ->execute(
                <<<SQL
                SELECT
                    c.id,
                    l.email
                FROM client_login l
                LEFT JOIN client c
                    ON l.email = c.email
                WHERE
                    l.token = ?
                SQL,
                [$token]
            )
            ->setHydrator(fn ($row) => new FormClientUser($row['email'], $row['id'] ?? Uuid::uuid4()))
            ->fetch() ?? $this->usernameNotFoundError()
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
                SELECT type FROM client_login WHERE token = ?
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
                    SELECT 1 FROM client_login WHERE token = ?
                    SQL,
                    [$token]
                )
                ->fetchField()
            ;
        } while ($exists);

        $builder = $this->runner->getQueryBuilder();

        $builder
            ->merge('client')
            ->onConflictIgnore()
            ->setKey(['email'])
            ->values([
                'email' => $emailAddress,
                'id' => Uuid::uuid4(),
            ])
        ;

        $builder
            ->insert('client_login')
            ->values([
                'email' => $emailAddress,
                'type' => $target ?? 'default',
                'token' => $token,
            ])
            ->perform()
        ;

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function touch(string $token, FormClientUser $user): bool
    {
        $row = $this
            ->runner
            ->execute(
                <<<SQL
                UPDATE client_login
                SET
                    login_count = login_count + 1,
                    login_first = COALESCE(login_first, current_timestamp),
                    login_last = current_timestamp
                WHERE
                    token = ?
                    AND valid_until > current_timestamp
                RETURNING
                    email,
                    (
                        SELECT id
                        FROM client
                        WHERE
                            client.email = client_login.email
                    ) AS id
                SQL,
                [$token]
            )
            ->fetch()
        ;

        // Older "client_login" entries might miss the "client" row.
        if ($row) {
            if (!$row['id']) {
                $this
                    ->runner
                    ->getQueryBuilder()
                    ->merge('client')
                    ->onConflictIgnore()
                    ->setKey(['email'])
                    ->values([
                        'email' => $row['email'],
                        'id' => $user->getClientId(),
                    ])
                    ->perform()
                ;
            }

            return true;
        }

        return false;
    }

    private function usernameNotFoundError(): FormClientUser
    {
        throw new UsernameNotFoundException();
    }
}

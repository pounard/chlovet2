<?php

declare(strict_types=1);

namespace App\Security;

/**
 * Provide users from their token.
 */
interface FormClientTokenRepository
{
    /**
     * Find email address related to token.
     */
    public function findEmailAddressForToken(string $token): ?string;

    /**
     * Find target related to token.
     */
    public function findTargetForToken(string $token): ?string;

    /**
     * User has logged in.
     */
    public function touch(string $token): bool;

    /**
     * Create one time login for client.
     */
    public function create(string $emailAddress, ?string $target = null): string;
}

<?php

namespace App\Models\Concerns;

/**
 * Password sentinels. Neither is a bcrypt hash, so a user carrying one can
 * never authenticate with a password and must recover their account by
 * verifying a one-time code sent to their email address.
 */
trait Recoverable
{
    /**
     * Post-incident remediation: the member must choose a new password.
     * Mirrors the value written by members.agepac.org's invalidation.
     */
    public const string PASSWORD_RESET_SENTINEL = 'RESET_REQUIRED';

    /**
     * Imported from the old forum without a password: the member must choose
     * one and complete the profile fields the import did not carry over.
     */
    public const string MIGRATION_SENTINEL = 'MIGRATION_REQUIRED';

    public function requiresPasswordReset(): bool
    {
        return $this->password === self::PASSWORD_RESET_SENTINEL;
    }

    public function requiresMigration(): bool
    {
        return $this->password === self::MIGRATION_SENTINEL;
    }

    public function requiresRecovery(): bool
    {
        return $this->requiresPasswordReset() || $this->requiresMigration();
    }
}

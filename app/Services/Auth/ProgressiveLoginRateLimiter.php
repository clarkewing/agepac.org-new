<?php

namespace App\Services\Auth;

use Illuminate\Cache\RateLimiter;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Http\Request;
use Laravel\Fortify\LoginRateLimiter;

/**
 * Locks an email and IP pair out for increasingly long periods: a minute
 * after the fifth failed attempt, then longer for each further failure, the
 * way a phone reacts to wrong passcodes. Attempts made during a lockout are
 * refused before they count, and a successful login clears the run.
 */
class ProgressiveLoginRateLimiter extends LoginRateLimiter
{
    /**
     * Failed attempts allowed before the first lockout.
     */
    public const int FREE_ATTEMPTS = 5;

    /**
     * Lockout lengths in seconds from the fifth failure onwards; the last one
     * repeats for every failure after it.
     *
     * @var array<int, int>
     */
    public const array LOCKOUT_SCHEDULE = [60, 300, 900, 3600];

    /**
     * How long a run of failures is remembered, in seconds.
     */
    protected const int FAILURES_TTL = 86400;

    public function __construct(RateLimiter $limiter, protected Cache $cache)
    {
        parent::__construct($limiter);
    }

    public function attempts(Request $request): int
    {
        return (int) $this->cache->get($this->failuresKey($request), 0);
    }

    public function tooManyAttempts(Request $request): bool
    {
        return $this->availableIn($request) > 0;
    }

    public function increment(Request $request): void
    {
        $failures = $this->attempts($request) + 1;

        $this->cache->put($this->failuresKey($request), $failures, self::FAILURES_TTL);

        if ($failures < self::FREE_ATTEMPTS) {
            return;
        }

        $step = min($failures - self::FREE_ATTEMPTS, count(self::LOCKOUT_SCHEDULE) - 1);
        $seconds = self::LOCKOUT_SCHEDULE[$step];

        $this->cache->put($this->lockoutKey($request), now()->addSeconds($seconds)->getTimestamp(), $seconds);
    }

    public function availableIn(Request $request): int
    {
        $availableAt = $this->availableAt($request);

        return $availableAt === null ? 0 : max(0, $availableAt - now()->getTimestamp());
    }

    /**
     * The timestamp at which the current lockout ends.
     */
    public function availableAt(Request $request): ?int
    {
        $lockedUntil = $this->cache->get($this->lockoutKey($request));

        return $lockedUntil === null ? null : (int) $lockedUntil;
    }

    public function clear(Request $request): void
    {
        $this->cache->forget($this->failuresKey($request));
        $this->cache->forget($this->lockoutKey($request));
    }

    protected function failuresKey(Request $request): string
    {
        return 'login-failures:'.$this->throttleKey($request);
    }

    protected function lockoutKey(Request $request): string
    {
        return 'login-lockout:'.$this->throttleKey($request);
    }
}

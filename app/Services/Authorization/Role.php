<?php

namespace App\Services\Authorization;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class Role implements Arrayable, JsonSerializable
{
    /**
     * All the defined roles, keyed by role key.
     *
     * @var array<string, static>
     */
    protected static array $roles = [];

    /**
     * @param  list<string>  $permissions
     */
    public function __construct(
        public string $key,
        public array $permissions,
    ) {}

    /**
     * Define a role with the given permissions.
     *
     * @param  list<string>  $permissions
     */
    public static function define(string $key, array $permissions): static
    {
        return static::$roles[$key] = new static($key, $permissions);
    }

    public static function find(?string $key): ?static
    {
        return $key !== null ? (static::$roles[$key] ?? null) : null;
    }

    /**
     * @return array<string, static>
     */
    public static function all(): array
    {
        return static::$roles;
    }

    /**
     * The name and description live in the roles lang files, keyed by role
     * key, and are resolved at read time.
     */
    public string $name {
        get => __("roles.{$this->key}.name");
    }

    public string $description {
        get => __("roles.{$this->key}.description");
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions, true);
    }

    /**
     * @return array{key: string, name: string, description: string, permissions: list<string>}
     */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'name' => $this->name,
            'description' => $this->description,
            'permissions' => $this->permissions,
        ];
    }

    /**
     * @return array{key: string, name: string, description: string, permissions: list<string>}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}

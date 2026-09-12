<?php

namespace Database\Factories;

use App\Enums\NavItemType;
use App\Models\NavItem;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NavItem>
 */
class NavItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => NavItemType::PAGE,
            'label' => null,
            'page_id' => Page::factory(),
            'order' => fake()->unique()->numberBetween(1, 1000),
        ];
    }

    public function group(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => NavItemType::GROUP,
            'label' => ['fr' => fake()->words(2, true)],
            'page_id' => null,
        ]);
    }

    public function url(?string $url = null): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => NavItemType::URL,
            'label' => ['fr' => fake()->words(2, true)],
            'page_id' => null,
            'url' => $url ?? fake()->url(),
        ]);
    }

    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'disabled' => true,
        ]);
    }
}

<?php

namespace Database\Factories;

use App\Enums\PageFormat;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $title = fake()->unique()->sentence(3),
            'path' => Str::slug($title),
            'body' => "## Heading\n\nSome **markdown** content.",
            'format' => PageFormat::MARKDOWN,
            'restricted' => true,
            'published_at' => now(),
        ];
    }

    public function html(): static
    {
        return $this->state(fn (array $attributes) => [
            'body' => '<h2>Heading</h2><p>Some <strong>HTML</strong> content.</p>',
            'format' => PageFormat::HTML,
        ]);
    }

    public function unrestricted(): static
    {
        return $this->state(fn (array $attributes) => [
            'restricted' => false,
        ]);
    }

    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => null,
        ]);
    }
}

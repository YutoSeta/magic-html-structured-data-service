<?php

namespace Database\Factories;

use App\Models\StructuredDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StructuredDocument>
 */
final class StructuredDocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => fake()->slug(2),
            'document_key' => fake()->unique()->slug(2),
            'name' => fake()->sentence(2),
            'kind' => 'generic',
            'schema' => [],
            'value' => ['title' => fake()->sentence()],
            'metadata' => [],
            'version' => 1,
        ];
    }
}

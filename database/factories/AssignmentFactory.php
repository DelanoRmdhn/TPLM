<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\Classroom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assignment>
 */
class AssignmentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'classroom_id' => Classroom::factory(),
            'topic_id' => null,
            'author_id' => fn (array $attrs) => Classroom::find($attrs['classroom_id'])->teacher_id,
            'title' => 'Latihan Pecahan',
            'instructions' => 'Kerjakan soal di buku halaman 12.',
            'due_at' => now()->addDays(3),
            'max_score' => 100,
        ];
    }

    public function pastDue(): static
    {
        return $this->state(fn () => ['due_at' => now()->subHour()]);
    }
}

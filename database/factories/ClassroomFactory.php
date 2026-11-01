<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Classroom>
 */
class ClassroomFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'teacher_id' => User::factory()->state(['role' => Role::Guru]),
            'name' => 'Matematika 4-A',
            'subject' => 'Matematika',
            'rombel_id' => null,
            'academic_year' => '2026/2027',
        ];
    }
}

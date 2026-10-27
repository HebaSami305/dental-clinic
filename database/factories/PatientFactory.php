<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    public function definition(): array
    {
        $gender = fake()->randomElement(['male', 'female']);

        return [
            'name' => fake('ar_SA')->name($gender),
            'phone' => '059' . fake()->numerify('#######'),
            'gender' => $gender,
            'birth_date' => fake()->dateTimeBetween('-70 years', '-5 years'),
            'address' => fake()->randomElement(['نابلس', 'رام الله', 'الخليل', 'جنين', 'طولكرم']),
            'allergies' => fake()->optional(0.2)->randomElement(['البنسلين', 'اللاتكس', 'مخدر موضعي']),
            'medical_history' => fake()->optional(0.3)->randomElement(['سكري', 'ضغط', 'لا يوجد']),
        ];
    }
}
